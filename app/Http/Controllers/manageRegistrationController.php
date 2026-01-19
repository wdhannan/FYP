<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\ParentModel;
use App\Models\Child;
use App\Helpers\PasswordHelper;
use App\Mail\ParentRegistrationMail;
use Carbon\Carbon;

class manageRegistrationController extends Controller
{
    /**
     * Display parent registration form
     */
    public function showParentRegistration($registrationId = null)
    {
        return view('manageRegistration.ParentRegistration', [
            'registrationId' => $registrationId
        ]);
    }

    /**
     * Store parent and child registration data
     */
    public function storeParentRegistration(Request $request)
    {
        $request->validate([
            'mother_name' => 'required|string|max:255',
            'mother_phone' => 'nullable|string|max:20',
            'mother_email' => 'nullable|email|max:255',
            'mother_ic' => 'nullable|string|max:50',
            'father_name' => 'nullable|string|max:255',
            'father_phone' => 'nullable|string|max:20',
            'father_email' => 'nullable|email|max:255',
            'father_ic' => 'nullable|string|max:50',
            'child_full_name' => 'required|string|max:255',
            'child_date_of_birth' => 'required|date',
            'child_gender' => 'required|string|in:Male,Female',
            'child_mykid' => 'nullable|string|max:50',
            'child_ethnic' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Check if parent already exists by IC number
            $existingParent = null;
            
            // First, check by Mother's IC if provided
            if (!empty($request->mother_ic)) {
                $existingParent = DB::table('parent')
                    ->where('MIdentificationNumber', $request->mother_ic)
                    ->first();
            }
            
            // If not found, check by Father's IC if provided
            if (!$existingParent && !empty($request->father_ic)) {
                $existingParent = DB::table('parent')
                    ->where('FIdentificationNumber', $request->father_ic)
                    ->first();
            }
            
            $isNewParent = false;
            if ($existingParent) {
                // Use existing ParentID
                $parentID = $existingParent->ParentID;
            } else {
                // Generate new ParentID
                $maxParentID = DB::table('parent')
                    ->whereRaw("ParentID REGEXP '^P[0-9]+$'")
                    ->selectRaw("CAST(SUBSTRING(ParentID, 2) AS UNSIGNED) as num")
                    ->orderBy('num', 'desc')
                    ->value('num');
                
                $nextParentNumber = ($maxParentID ?? 0) + 1;
                $parentID = 'P' . str_pad($nextParentNumber, 3, '0', STR_PAD_LEFT);

                // Create new parent record
                ParentModel::create([
                    'ParentID' => $parentID,
                    'MotherName' => $request->mother_name,
                    'MphoneNumber' => $request->mother_phone,
                    'MEmail' => $request->mother_email,
                    'MIdentificationNumber' => $request->mother_ic,
                    'FatherName' => $request->father_name,
                    'FPhoneNumber' => $request->father_phone,
                    'FEmail' => $request->father_email,
                    'FIdentificationNumber' => $request->father_ic,
                ]);
                $isNewParent = true;
            }

            // Generate ChildID
            $maxChildID = DB::table('child')
                ->whereRaw("ChildID REGEXP '^C[0-9]+$'")
                ->selectRaw("CAST(SUBSTRING(ChildID, 2) AS UNSIGNED) as num")
                ->orderBy('num', 'desc')
                ->value('num');
            
            $nextChildNumber = ($maxChildID ?? 0) + 1;
            $childID = 'C' . str_pad($nextChildNumber, 3, '0', STR_PAD_LEFT);

            // Create child record
            Child::create([
                'ChildID' => $childID,
                'FullName' => $request->child_full_name,
                'DateOfBirth' => $request->child_date_of_birth,
                'Gender' => $request->child_gender,
                'MyKidNumber' => $request->child_mykid,
                'Ethnic' => $request->child_ethnic,
                'ParentID' => $parentID,
            ]);

            // Create user account for parent if email is provided and this is a new parent
            $motherEmail = !empty($request->mother_email) ? $request->mother_email : null;
            $fatherEmail = !empty($request->father_email) ? $request->father_email : null;
            
            if (($motherEmail || $fatherEmail) && $isNewParent) {
                // Check if user account already exists for this parent
                $existingUser = DB::table('user')->where('UserID', $parentID)->first();
                
                if (!$existingUser) {
                    // Generate temporary password
                    $temporaryPassword = PasswordHelper::generateTemporaryPassword(8);
                    
                    // Create user account for parent using ParentID as UserID
                    DB::table('user')->insert([
                        'UserID' => $parentID,
                        'PasswordHash' => $temporaryPassword, // Store plain text (as per current system)
                        'role' => 'parent',
                        'must_change_password' => true, // Force password change on first login
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    // Send email to mother if email provided
                    if ($motherEmail) {
                        try {
                            $motherName = !empty($request->mother_name) ? $request->mother_name : 'Parent';
                            Mail::to($motherEmail)->send(new ParentRegistrationMail(
                                $motherName,
                                $parentID,
                                $motherEmail,
                                $temporaryPassword,
                                $request->child_full_name
                            ));
                            \Log::info('✅ Parent registration email sent to mother', [
                                'ParentID' => $parentID,
                                'Email' => $motherEmail
                            ]);
                        } catch (\Exception $emailError) {
                            \Log::error('Failed to send parent registration email to mother: ' . $emailError->getMessage());
                        }
                    }
                    
                    // Send email to father if email provided
                    if ($fatherEmail) {
                        try {
                            $fatherName = !empty($request->father_name) ? $request->father_name : 'Parent';
                            Mail::to($fatherEmail)->send(new ParentRegistrationMail(
                                $fatherName,
                                $parentID,
                                $fatherEmail,
                                $temporaryPassword,
                                $request->child_full_name
                            ));
                            \Log::info('✅ Parent registration email sent to father', [
                                'ParentID' => $parentID,
                                'Email' => $fatherEmail
                            ]);
                        } catch (\Exception $emailError) {
                            \Log::error('Failed to send parent registration email to father: ' . $emailError->getMessage());
                        }
                    }
                }
            }

            DB::commit();

            // Redirect to verification page
            return redirect()->route('verify.parent', ['registrationId' => base64_encode($childID)])
                ->with('success', 'Registration submitted successfully! Waiting for nurse verification.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error submitting registration: ' . $e->getMessage());
        }
    }

    /**
     * Display verification page for nurse to verify parent/child registration
     */
    public function showVerification($registrationId)
    {
        try {
            // Decode the registration ID (could be base64 encoded or plain)
            $decodedId = base64_decode($registrationId, true);
            $actualId = ($decodedId !== false) ? $decodedId : $registrationId;
            
            // Fetch parent and child data based on registration ID
            // Option 1: If registration ID is ChildID
            $childData = DB::table('child')->where('ChildID', $actualId)->first();
            
            if ($childData) {
                $parentData = DB::table('parent')->where('ParentID', $childData->ParentID)->first();
                
                return view('manageRegistration.VerifyReport', [
                    'parentData' => $parentData,
                    'childData' => $childData,
                    'registrationId' => $registrationId
                ]);
            }
            
            // Option 2: If registration ID is ParentID
            $parentData = DB::table('parent')->where('ParentID', $actualId)->first();
            if ($parentData) {
                $childData = DB::table('child')->where('ParentID', $parentData->ParentID)->first();
                
                if ($childData) {
                    return view('manageRegistration.VerifyReport', [
                        'parentData' => $parentData,
                        'childData' => $childData,
                        'registrationId' => $registrationId
                    ]);
                }
            }
            
            return view('manageRegistration.VerifyReport', [
                'parentData' => null,
                'childData' => null,
                'registrationId' => $registrationId
            ])->with('error', 'Registration data not found.');
            
        } catch (\Exception $e) {
            return view('manageRegistration.VerifyReport', [
                'parentData' => null,
                'childData' => null,
                'registrationId' => $registrationId
            ])->with('error', 'Error loading registration data: ' . $e->getMessage());
        }
    }

    /**
     * Process verification action (confirm or reject)
     */
    public function processVerification(Request $request)
    {
        $request->validate([
            'action' => 'required|in:confirm,reject',
            'child_id' => 'required',
            'parent_id' => 'required',
        ]);
        
        $action = $request->action;
        $childId = $request->child_id;
        $parentId = $request->parent_id;
        
        try {
            if ($action === 'confirm') {
                // Mark registration as verified/approved
                // You can add a status field to parent/child table if needed
                // For now, just redirect with success message
                return redirect()->route('verify.parent', ['registrationId' => base64_encode($childId)])
                    ->with('success', 'Parent data has been verified and confirmed successfully!');
            } else {
                // Mark as rejected - send back for edit
                return redirect()->route('verify.parent', ['registrationId' => base64_encode($childId)])
                    ->with('error', 'Registration has been rejected. Please contact the parent to update their information.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error processing verification: ' . $e->getMessage());
        }
    }

    /**
     * Display child registration CSV upload page for nurses
     */
    public function showChildRegistrationUpload()
    {
        // Load all registered children from database
        $children = DB::table('child')
            ->leftJoin('parent', 'child.ParentID', '=', 'parent.ParentID')
            ->select(
                'child.ChildID',
                'child.FullName',
                'parent.MotherName',
                'parent.FatherName'
            )
            ->orderBy('child.created_at', 'desc')
            ->get()
            ->map(function($child) {
                return [
                    'FullName' => $child->FullName ?? '',
                    'MotherName' => $child->MotherName ?? '',
                    'FatherName' => $child->FatherName ?? ''
                ];
            })
            ->toArray();
        
        return view('manageRegistration.RegisterChildCSV', compact('children'));
    }
    
    /**
     * Get list of all registered children
     */
    public function getChildList()
    {
        try {
            $children = DB::table('child')
                ->leftJoin('parent', 'child.ParentID', '=', 'parent.ParentID')
                ->select(
                    'child.ChildID',
                    'child.FullName',
                    'parent.MotherName',
                    'parent.FatherName'
                )
                ->orderBy('child.created_at', 'desc')
                ->get()
                ->map(function($child) {
                    return [
                        'FullName' => $child->FullName ?? '',
                        'MotherName' => $child->MotherName ?? '',
                        'FatherName' => $child->FatherName ?? ''
                    ];
                })
                ->toArray();
            
            return response()->json([
                'success' => true,
                'children' => $children
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading children: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload CSV file to register multiple children with their parents
     * CSV Format: ChildFullName, DateOfBirth, Gender, MyKidNumber, Ethnic, 
     *              MotherName, MPhoneNumber, MEmail, MIdentificationNumber,
     *              FatherName, FPhoneNumber, FEmail, FIdentificationNumber
     */
    public function uploadChildCSV(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        try {
            $file = $request->file('csv_file');
            $path = $file->getRealPath();
            
            $children = [];
            $successCount = 0;
            $errors = [];
            $validRows = [];

            if (($handle = fopen($path, 'r')) !== false) {
                // Read and validate header row
                $header = fgetcsv($handle);
                
                if ($header === false || empty($header)) {
                    fclose($handle);
                    return response()->json([
                        'success' => false,
                        'message' => 'Error: CSV file is empty or invalid. Please check your file.'
                    ], 400);
                }
                
                // Check for duplicate column names in header
                $headerTrimmed = array_map('trim', $header);
                $headerCounts = array_count_values($headerTrimmed);
                $duplicates = [];
                foreach ($headerCounts as $columnName => $count) {
                    if ($count > 1 && !empty($columnName)) {
                        $duplicates[] = $columnName;
                    }
                }
                
                if (!empty($duplicates)) {
                    fclose($handle);
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid CSV: Duplicate column name(s) found: ' . implode(', ', $duplicates) . '. Please remove duplicate columns and try again.'
                    ], 400);
                }
                
                // Create column mapping from header
                $headerTrimmed = array_map('trim', $header);
                $columnMap = [];
                $columnNames = [
                    'childfullname' => 'childFullName',
                    'child\'s full name' => 'childFullName',
                    'child\'s full name (e.g: aminah binti abu)' => 'childFullName',
                    'childs full name' => 'childFullName',
                    'fullname' => 'childFullName',
                    'child name' => 'childFullName',
                    'name' => 'childFullName',
                    'dateofbirth' => 'dateOfBirth',
                    'date of birth' => 'dateOfBirth',
                    'dob' => 'dateOfBirth',
                    'birthdate' => 'dateOfBirth',
                    'birth date' => 'dateOfBirth',
                    'timestamp' => 'dateOfBirth', // Handle timestamp column if present
                    'gender' => 'gender',
                    'sex' => 'gender',
                    'mykidnumber' => 'myKidNumber',
                    'mykid number' => 'myKidNumber',
                    'mykid number (e.g: xxxxxx-xx-xxxx)' => 'myKidNumber',
                    'mykid' => 'myKidNumber',
                    'my kid' => 'myKidNumber',
                    'ethnic' => 'ethnic',
                    'ethnicity' => 'ethnic',
                    'mothername' => 'motherName',
                    'mother\'s full name' => 'motherName',
                    'mother\'s full name (e.g: aminah binti abu)' => 'motherName',
                    'mothers full name' => 'motherName',
                    'mother name' => 'motherName',
                    'mname' => 'motherName',
                    'mother' => 'motherName',
                    'mphonenumber' => 'mPhoneNumber',
                    'mother\'s phone number' => 'mPhoneNumber',
                    'mother\'s phone number (e.g: xxx-xxxxxxx)' => 'mPhoneNumber',
                    'mothers phone number' => 'mPhoneNumber',
                    'mphone' => 'mPhoneNumber',
                    'mother phone' => 'mPhoneNumber',
                    'mother phone number' => 'mPhoneNumber',
                    'memail' => 'mEmail',
                    'mother\'s email' => 'mEmail',
                    'mothers email' => 'mEmail',
                    'mother email' => 'mEmail',
                    'mother email address' => 'mEmail',
                    'midentificationnumber' => 'mIdentificationNumber',
                    'mother\'s identification number (ic)' => 'mIdentificationNumber',
                    'mother\'s identification number (e.g: xxxxxx-xx-xxxx)' => 'mIdentificationNumber',
                    'mothers identification number (ic)' => 'mIdentificationNumber',
                    'mother\'s identification number' => 'mIdentificationNumber',
                    'mothers identification number' => 'mIdentificationNumber',
                    'mic' => 'mIdentificationNumber',
                    'mother ic' => 'mIdentificationNumber',
                    'mother identification number' => 'mIdentificationNumber',
                    'fathername' => 'fatherName',
                    'father\'s full name' => 'fatherName',
                    'father\'s full name (e.g: ali bin bakar)' => 'fatherName',
                    'fathers full name' => 'fatherName',
                    'father name' => 'fatherName',
                    'fname' => 'fatherName',
                    'father' => 'fatherName',
                    'fphonenumber' => 'fPhoneNumber',
                    'father\'s mobile phone' => 'fPhoneNumber',
                    'fathers mobile phone' => 'fPhoneNumber',
                    'father\'s phone number' => 'fPhoneNumber',
                    'father\'s phone number (e.g: xxx-xxxxxxx)' => 'fPhoneNumber',
                    'fathers phone number' => 'fPhoneNumber',
                    'fphone' => 'fPhoneNumber',
                    'father phone' => 'fPhoneNumber',
                    'father phone number' => 'fPhoneNumber',
                    'femail' => 'fEmail',
                    'father\'s email' => 'fEmail',
                    'fathers email' => 'fEmail',
                    'father email' => 'fEmail',
                    'father email address' => 'fEmail',
                    'fidentificationnumber' => 'fIdentificationNumber',
                    'father\'s identification number (ic)' => 'fIdentificationNumber',
                    'father\'s identification number (ic)' => 'fIdentificationNumber',
                    'fathers identification number (ic)' => 'fIdentificationNumber',
                    'father\'s identification number' => 'fIdentificationNumber',
                    'fathers identification number' => 'fIdentificationNumber',
                    'fic' => 'fIdentificationNumber',
                    'father ic' => 'fIdentificationNumber',
                    'father identification number' => 'fIdentificationNumber',
                ];
                
                // Map header columns to field names
                foreach ($headerTrimmed as $index => $columnName) {
                    $columnNameLower = strtolower(trim($columnName));
                    
                    // Remove parentheses and example text (e.g: "Child's Full Name (e.g: AMINAH BINTI ABU)" -> "child's full name")
                    $columnNameClean = preg_replace('/\s*\([^)]*\)\s*/', '', $columnNameLower);
                    
                    // Try exact match first
                    if (isset($columnNames[$columnNameLower])) {
                        $columnMap[$columnNames[$columnNameLower]] = $index;
                    }
                    // Try cleaned match (without parentheses)
                    elseif (isset($columnNames[$columnNameClean])) {
                        $columnMap[$columnNames[$columnNameClean]] = $index;
                    }
                    // Try partial match (remove common prefixes/suffixes)
                    else {
                        // Remove common prefixes like "child's", "mother's", etc.
                        $columnNamePartial = preg_replace('/^(child\'s|mother\'s|father\'s|childs|mothers|fathers)\s*/i', '', $columnNameClean);
                        if (isset($columnNames[$columnNamePartial])) {
                            $columnMap[$columnNames[$columnNamePartial]] = $index;
                        }
                    }
                }
                
                // Check for required columns
                $requiredColumns = ['childFullName', 'dateOfBirth', 'gender', 'motherName'];
                $missingColumns = [];
                foreach ($requiredColumns as $required) {
                    if (!isset($columnMap[$required])) {
                        $missingColumns[] = $required;
                    }
                }
                
                if (!empty($missingColumns)) {
                    fclose($handle);
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid CSV: Missing required columns: ' . implode(', ', $missingColumns) . '. Please check your CSV header row. Available columns: ' . implode(', ', $headerTrimmed)
                    ], 400);
                }
                
                // Step 1: Read all CSV rows into memory and validate
                $csvRows = [];
                $rowNumber = 1;
                
                while (($data = fgetcsv($handle)) !== false) {
                    $rowNumber++;
                    
                    // Skip empty rows
                    if (empty(array_filter($data, function($value) { return trim($value) !== ''; }))) {
                        continue;
                    }
                    
                    // Parse CSV data using column mapping
                    $getColumnValue = function($fieldName) use ($data, $columnMap) {
                        $index = $columnMap[$fieldName] ?? null;
                        return $index !== null ? trim($data[$index] ?? '') : '';
                    };
                    
                    $childFullName = $getColumnValue('childFullName');
                    $dateOfBirth = $getColumnValue('dateOfBirth');
                    $gender = $getColumnValue('gender');
                    $myKidNumber = $getColumnValue('myKidNumber');
                    $ethnic = $getColumnValue('ethnic');
                    $motherName = $getColumnValue('motherName');
                    $mPhoneNumber = $getColumnValue('mPhoneNumber');
                    $mEmail = $getColumnValue('mEmail');
                    $mIdentificationNumber = $getColumnValue('mIdentificationNumber');
                    $fatherName = $getColumnValue('fatherName');
                    $fPhoneNumber = $getColumnValue('fPhoneNumber');
                    $fEmail = $getColumnValue('fEmail');
                    $fIdentificationNumber = $getColumnValue('fIdentificationNumber');
                    
                    // Validate required fields
                    if (empty($childFullName) || empty($dateOfBirth) || empty($gender) || empty($motherName)) {
                        $missing = [];
                        if (empty($childFullName)) $missing[] = 'ChildFullName';
                        if (empty($dateOfBirth)) $missing[] = 'DateOfBirth';
                        if (empty($gender)) $missing[] = 'Gender';
                        if (empty($motherName)) $missing[] = 'MotherName';
                        $errors[] = "Row {$rowNumber}: Missing required fields: " . implode(', ', $missing);
                        continue;
                    }
                    
                    // Validate gender
                    $genderNormalized = ucfirst(strtolower(trim($gender)));
                    if (!in_array($genderNormalized, ['Male', 'Female'])) {
                        $errors[] = "Row {$rowNumber}: Invalid gender '{$gender}'. Must be Male or Female";
                        continue;
                    }
                    
                    // Validate date format
                    $dateOfBirthObj = null;
                    $dateFormats = ['Y-m-d', 'd/m/Y', 'Y/m/d', 'd-m-Y', 'm/d/Y'];
                    $dateValid = false;
                    
                    foreach ($dateFormats as $format) {
                        try {
                            $dateOfBirthObj = Carbon::createFromFormat($format, $dateOfBirth);
                            $dateValid = true;
                            break;
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                    
                    if (!$dateValid) {
                        $errors[] = "Row {$rowNumber}: Invalid date format '{$dateOfBirth}' for child '{$childFullName}'. Use YYYY-MM-DD, DD/MM/YYYY, or similar formats";
                        continue;
                    }
                    
                    $csvRows[] = [
                        'row' => $rowNumber,
                        'childFullName' => $childFullName,
                        'dateOfBirth' => $dateOfBirthObj->format('Y-m-d'),
                        'gender' => $genderNormalized,
                        'myKidNumber' => $myKidNumber,
                        'ethnic' => $ethnic,
                        'motherName' => $motherName,
                        'mPhoneNumber' => $mPhoneNumber,
                        'mEmail' => $mEmail,
                        'mIdentificationNumber' => $mIdentificationNumber,
                        'fatherName' => $fatherName,
                        'fPhoneNumber' => $fPhoneNumber,
                        'fEmail' => $fEmail,
                        'fIdentificationNumber' => $fIdentificationNumber,
                    ];
                }
                fclose($handle);
                
                if (empty($csvRows)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No valid rows found in CSV file.'
                    ], 400);
                }
                
                // Step 2: Batch check existing records
                $motherICs = array_filter(array_column($csvRows, 'mIdentificationNumber'));
                $fatherICs = array_filter(array_column($csvRows, 'fIdentificationNumber'));
                $myKidNumbers = array_filter(array_column($csvRows, 'myKidNumber'));
                
                $existingParents = DB::table('parent')
                    ->whereIn('MIdentificationNumber', $motherICs)
                    ->orWhereIn('FIdentificationNumber', $fatherICs)
                    ->get()
                    ->keyBy(function($item) {
                        return ($item->MIdentificationNumber ?? '') . '|' . ($item->FIdentificationNumber ?? '');
                    });
                
                $existingChildren = DB::table('child')
                    ->whereIn('MyKidNumber', $myKidNumbers)
                    ->pluck('MyKidNumber', 'MyKidNumber')
                    ->toArray();
                
                // Step 3: Pre-calculate ParentIDs and ChildIDs
                $maxParentID = DB::table('parent')
                    ->whereRaw("ParentID REGEXP '^P[0-9]+$'")
                    ->selectRaw("CAST(SUBSTRING(ParentID, 2) AS UNSIGNED) as num")
                    ->orderBy('num', 'desc')
                    ->value('num') ?? 0;
                
                $maxChildID = DB::table('child')
                    ->whereRaw("ChildID REGEXP '^C[0-9]+$'")
                    ->selectRaw("CAST(SUBSTRING(ChildID, 2) AS UNSIGNED) as num")
                    ->orderBy('num', 'desc')
                    ->value('num') ?? 0;
                
                // Step 4: Process rows and group by parent
                $parentMap = []; // Maps IC to ParentID
                $parentInfoMap = []; // Maps ParentID to parent info (for response)
                $parentsToInsert = [];
                $childrenToInsert = [];
                $usersToInsert = [];
                $emailsToQueue = [];
                $nextParentNum = $maxParentID;
                $nextChildNum = $maxChildID;
                $now = now();
                
                foreach ($csvRows as $row) {
                    $rowNumber = $row['row'];
                    $myKidNumber = $row['myKidNumber'];
                    
                    // Check if child already exists
                    if (!empty($myKidNumber) && isset($existingChildren[$myKidNumber])) {
                        $errors[] = "Row {$rowNumber}: Child with MyKidNumber '{$myKidNumber}' already exists";
                        continue;
                    }
                    
                    // Find or create parent
                    $parentKey = ($row['mIdentificationNumber'] ?? '') . '|' . ($row['fIdentificationNumber'] ?? '');
                    $parentID = null;
                    $isNewParent = false;
                    
                    // Check existing parents
                    foreach ($existingParents as $existingParent) {
                        $match = false;
                        if (!empty($row['mIdentificationNumber']) && $existingParent->MIdentificationNumber === $row['mIdentificationNumber']) {
                            $match = true;
                        }
                        if (!empty($row['fIdentificationNumber']) && $existingParent->FIdentificationNumber === $row['fIdentificationNumber']) {
                            $match = true;
                        }
                        if ($match) {
                            $parentID = $existingParent->ParentID;
                            // Store parent info for response
                            $parentInfoMap[$parentID] = [
                                'MotherName' => $existingParent->MotherName ?? '',
                                'FatherName' => $existingParent->FatherName ?? ''
                            ];
                            
                            // Check if existing parent has user account, if not create one
                            $mEmail = !empty($row['mEmail']) ? trim($row['mEmail']) : (!empty($existingParent->MEmail) ? trim($existingParent->MEmail) : null);
                            $fEmail = !empty($row['fEmail']) ? trim($row['fEmail']) : (!empty($existingParent->FEmail) ? trim($existingParent->FEmail) : null);
                            
                            \Log::info('Checking existing parent for user account', [
                                'ParentID' => $parentID,
                                'MotherEmail' => $mEmail,
                                'FatherEmail' => $fEmail
                            ]);
                            
                            if ($mEmail || $fEmail) {
                                $existingUser = DB::table('user')->where('UserID', $parentID)->first();
                                if (!$existingUser) {
                                    // Existing parent but no user account - create one and send email
                                    $temporaryPassword = PasswordHelper::generateTemporaryPassword(8);
                                    $usersToInsert[] = [
                                        'UserID' => $parentID,
                                        'PasswordHash' => $temporaryPassword,
                                        'role' => 'parent',
                                        'must_change_password' => true,
                                        'created_at' => $now,
                                        'updated_at' => $now,
                                    ];
                                    
                                    // Send email to mother if email provided
                                    if ($mEmail && filter_var($mEmail, FILTER_VALIDATE_EMAIL)) {
                                        $emailsToQueue[] = [
                                            'email' => $mEmail,
                                            'parentName' => !empty($existingParent->MotherName) ? $existingParent->MotherName : 'Parent',
                                            'parentID' => $parentID,
                                            'password' => $temporaryPassword,
                                            'childName' => $row['childFullName'],
                                        ];
                                        \Log::info('Added mother email to queue for existing parent', [
                                            'ParentID' => $parentID,
                                            'Email' => $mEmail
                                        ]);
                                    }
                                    
                                    // Send email to father if email provided
                                    if ($fEmail && filter_var($fEmail, FILTER_VALIDATE_EMAIL)) {
                                        $emailsToQueue[] = [
                                            'email' => $fEmail,
                                            'parentName' => !empty($existingParent->FatherName) ? $existingParent->FatherName : 'Parent',
                                            'parentID' => $parentID,
                                            'password' => $temporaryPassword,
                                            'childName' => $row['childFullName'],
                                        ];
                                        \Log::info('Added father email to queue for existing parent', [
                                            'ParentID' => $parentID,
                                            'Email' => $fEmail
                                        ]);
                                    }
                                    
                                    \Log::info('Creating user account for existing parent without account', [
                                        'ParentID' => $parentID,
                                        'MotherEmail' => $mEmail,
                                        'FatherEmail' => $fEmail,
                                        'EmailsQueued' => count($emailsToQueue)
                                    ]);
                                } else {
                                    \Log::info('Existing parent already has user account', [
                                        'ParentID' => $parentID
                                    ]);
                                }
                            } else {
                                \Log::warning('Existing parent has no email addresses', [
                                    'ParentID' => $parentID
                                ]);
                            }
                            break;
                        }
                    }
                    
                    // Create new parent if not found
                    if (!$parentID) {
                        if (!isset($parentMap[$parentKey])) {
                            $nextParentNum++;
                            $parentID = 'P' . str_pad($nextParentNum, 3, '0', STR_PAD_LEFT);
                            $parentMap[$parentKey] = $parentID;
                            
                            $parentsToInsert[] = [
                                'ParentID' => $parentID,
                                'MotherName' => $row['motherName'],
                                'MphoneNumber' => $row['mPhoneNumber'],
                                'MEmail' => $row['mEmail'],
                                'MIdentificationNumber' => $row['mIdentificationNumber'],
                                'FatherName' => $row['fatherName'],
                                'FPhoneNumber' => $row['fPhoneNumber'],
                                'FEmail' => $row['fEmail'],
                                'FIdentificationNumber' => $row['fIdentificationNumber'],
                            ];
                            
                            $isNewParent = true;
                            
                            // Prepare user account if email provided
                            $mEmail = !empty($row['mEmail']) ? trim($row['mEmail']) : null;
                            $fEmail = !empty($row['fEmail']) ? trim($row['fEmail']) : null;
                            
                            \Log::info('Checking parent emails for new parent', [
                                'ParentID' => $parentID,
                                'MotherEmail' => $mEmail,
                                'FatherEmail' => $fEmail,
                                'MotherName' => $row['motherName'] ?? '',
                                'FatherName' => $row['fatherName'] ?? ''
                            ]);
                            
                            if ($mEmail || $fEmail) {
                                $temporaryPassword = PasswordHelper::generateTemporaryPassword(8);
                                $usersToInsert[] = [
                                    'UserID' => $parentID,
                                    'PasswordHash' => $temporaryPassword,
                                    'role' => 'parent',
                                    'must_change_password' => true,
                                    'created_at' => $now,
                                    'updated_at' => $now,
                                ];
                                
                                // Send email to mother if email provided
                                if ($mEmail && filter_var($mEmail, FILTER_VALIDATE_EMAIL)) {
                                    $emailsToQueue[] = [
                                        'email' => $mEmail,
                                        'parentName' => !empty($row['motherName']) ? $row['motherName'] : 'Parent',
                                        'parentID' => $parentID,
                                        'password' => $temporaryPassword,
                                        'childName' => $row['childFullName'],
                                    ];
                                    \Log::info('Added mother email to queue', [
                                        'ParentID' => $parentID,
                                        'Email' => $mEmail
                                    ]);
                                } else {
                                    \Log::warning('Mother email is invalid or empty', [
                                        'ParentID' => $parentID,
                                        'Email' => $mEmail
                                    ]);
                                }
                                
                                // Send email to father if email provided
                                if ($fEmail && filter_var($fEmail, FILTER_VALIDATE_EMAIL)) {
                                    $emailsToQueue[] = [
                                        'email' => $fEmail,
                                        'parentName' => !empty($row['fatherName']) ? $row['fatherName'] : 'Parent',
                                        'parentID' => $parentID,
                                        'password' => $temporaryPassword,
                                        'childName' => $row['childFullName'],
                                    ];
                                    \Log::info('Added father email to queue', [
                                        'ParentID' => $parentID,
                                        'Email' => $fEmail
                                    ]);
                                } else {
                                    \Log::warning('Father email is invalid or empty', [
                                        'ParentID' => $parentID,
                                        'Email' => $fEmail
                                    ]);
                                }
                            } else {
                                \Log::warning('No email addresses provided for new parent', [
                                    'ParentID' => $parentID,
                                    'MotherEmail' => $mEmail,
                                    'FatherEmail' => $fEmail
                                ]);
                            }
                        } else {
                            $parentID = $parentMap[$parentKey];
                        }
                    }
                    
                    // Generate ChildID
                    $nextChildNum++;
                    $childID = 'C' . str_pad($nextChildNum, 3, '0', STR_PAD_LEFT);
                    
                    $childrenToInsert[] = [
                        'ChildID' => $childID,
                        'FullName' => $row['childFullName'],
                        'DateOfBirth' => $row['dateOfBirth'],
                        'Gender' => $row['gender'],
                        'MyKidNumber' => $myKidNumber ?: null,
                        'Ethnic' => $row['ethnic'] ?: null,
                        'ParentID' => $parentID,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                
                // Step 5: Bulk insert in single transaction
                if (!empty($childrenToInsert)) {
                    DB::beginTransaction();
                    try {
                        if (!empty($parentsToInsert)) {
                            DB::table('parent')->insert($parentsToInsert);
                        }
                        
                        if (!empty($usersToInsert)) {
                            DB::table('user')->insert($usersToInsert);
                        }
                        
                        DB::table('child')->insert($childrenToInsert);
                        
                        DB::commit();
                        
                        // Step 6: Send emails to parents with login credentials
                        \Log::info('Preparing to send parent registration emails', [
                            'TotalEmails' => count($emailsToQueue),
                            'Emails' => array_map(function($e) { return $e['email']; }, $emailsToQueue)
                        ]);
                        
                        if (empty($emailsToQueue)) {
                            \Log::warning('No emails to send - emailsToQueue is empty', [
                                'ParentsInserted' => count($parentsToInsert),
                                'UsersInserted' => count($usersToInsert)
                            ]);
                        }
                        
                        foreach ($emailsToQueue as $emailData) {
                            try {
                                \Log::info('Attempting to send parent registration email', [
                                    'ParentID' => $emailData['parentID'],
                                    'Email' => $emailData['email'],
                                    'ParentName' => $emailData['parentName'],
                                    'ChildName' => $emailData['childName'],
                                    'QueueDriver' => config('queue.default')
                                ]);
                                
                                // Always send immediately (don't queue) to ensure emails are sent
                                Mail::to($emailData['email'])->send(new ParentRegistrationMail(
                                    $emailData['parentName'],
                                    $emailData['parentID'],
                                    $emailData['email'],
                                    $emailData['password'],
                                    $emailData['childName']
                                ));
                                
                                \Log::info('✅ Parent registration email sent successfully', [
                                    'ParentID' => $emailData['parentID'],
                                    'Email' => $emailData['email'],
                                    'ParentName' => $emailData['parentName'],
                                    'ChildName' => $emailData['childName']
                                ]);
                            } catch (\Exception $e) {
                                \Log::error("❌ Failed to send parent registration email", [
                                    'ParentID' => $emailData['parentID'],
                                    'Email' => $emailData['email'],
                                    'ParentName' => $emailData['parentName'],
                                    'Error' => $e->getMessage(),
                                    'ErrorCode' => $e->getCode(),
                                    'Trace' => $e->getTraceAsString()
                                ]);
                            }
                        }
                        
                        \Log::info('Completed sending parent registration emails', [
                            'TotalAttempted' => count($emailsToQueue),
                            'SuccessCount' => count($emailsToQueue) // Will be updated if we track failures
                        ]);
                        
                        $successCount = count($childrenToInsert);
                        
                        // Create a map of ParentID to parent info for response
                        $parentInfoMap = [];
                        foreach ($parentsToInsert as $parent) {
                            $parentInfoMap[$parent['ParentID']] = [
                                'MotherName' => $parent['MotherName'],
                                'FatherName' => $parent['FatherName'] ?? null,
                            ];
                        }
                        // Also add existing parents
                        foreach ($existingParents as $parent) {
                            $parentInfoMap[$parent->ParentID] = [
                                'MotherName' => $parent->MotherName,
                                'FatherName' => $parent->FatherName ?? null,
                            ];
                        }
                        
                        $children = array_map(function($item) use ($parentInfoMap) {
                            $parentInfo = $parentInfoMap[$item['ParentID']] ?? ['MotherName' => '', 'FatherName' => ''];
                            return [
                                'ChildID' => $item['ChildID'],
                                'FullName' => $item['FullName'],
                                'DateOfBirth' => $item['DateOfBirth'],
                                'Gender' => $item['Gender'],
                                'ParentID' => $item['ParentID'],
                                'MotherName' => $parentInfo['MotherName'] ?? '',
                                'FatherName' => $parentInfo['FatherName'] ?? '',
                            ];
                        }, $childrenToInsert);
                        
                    } catch (\Exception $e) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Error inserting data: ' . $e->getMessage()
                        ], 500);
                    }
                }
            }

            // Log errors for debugging but don't show them to user
            if (count($errors) > 0) {
                \Log::info('Child CSV Upload - Some rows had errors: ' . count($errors), ['errors' => $errors]);
            }

            if ($successCount > 0) {
                $message = "✅ Successfully registered {$successCount} child(ren)! Registration emails are being sent.";
            } else {
                $message = "No children were registered.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'count' => $successCount,
                'children' => $children ?? []
            ]);
        } catch (\Exception $e) {
            \Log::error('CSV Upload Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Always return JSON, even on error
            return response()->json([
                'success' => false,
                'message' => 'Error processing CSV: ' . $e->getMessage() . '. Please check your file format and try again.',
                'error' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }
}

