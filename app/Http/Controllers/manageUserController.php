<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Doctor;
use App\Models\Nurse;
use App\Mail\StaffRegistrationMail;
use App\Helpers\PasswordHelper;

class manageUserController extends Controller
{
    /**
     * Display doctor registration form
     */
    public function showRegisterDoctor()
    {
        return view('manageUser.RegisterDoctor');
    }

    /**
     * Upload CSV file to register multiple doctors
     */
    public function uploadDoctorCSV(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        try {
            $file = $request->file('csv_file');
            $path = $file->getRealPath();
            
            $doctors = [];
            $successCount = 0;
            $errors = [];

            if (($handle = fopen($path, 'r')) !== false) {
                // Skip header row
                $header = fgetcsv($handle);
                
                $rowNumber = 1; // Track row number for better error messages
                while (($data = fgetcsv($handle)) !== false) {
                    $rowNumber++;
                    if (count($data) < 3) {
                        $errors[] = "Row {$rowNumber}: Insufficient columns. Expected at least 3 columns (DoctorID, FullName, Email)";
                        continue;
                    }
                    
                    $doctorID = trim($data[0] ?? '');
                    $fullName = trim($data[1] ?? '');
                    $email = trim($data[2] ?? '');
                    
                    // Check for empty required fields
                    if (empty($doctorID)) {
                        $errors[] = "Row {$rowNumber}: DoctorID is empty. Please provide a DoctorID in the first column.";
                        continue;
                    }
                    
                    if (empty($fullName)) {
                        $errors[] = "Row {$rowNumber}: FullName is empty for DoctorID '{$doctorID}'.";
                        continue;
                    }
                    
                    if (empty($email)) {
                        $errors[] = "Row {$rowNumber}: Email is empty for DoctorID '{$doctorID}'.";
                        continue;
                    }
                    
                    // Check if doctor already exists by DoctorID
                    $existing = Doctor::where('DoctorID', $doctorID)->first();
                    if ($existing) {
                        $errors[] = "Row {$rowNumber}: Doctor with ID '{$doctorID}' already exists in the system.";
                        continue;
                    }
                    
                    // Also check by email to prevent duplicates
                    $existingEmail = Doctor::where('Email', $email)->first();
                    if ($existingEmail) {
                        $errors[] = "Row {$rowNumber}: Doctor with email '{$email}' already exists (DoctorID: {$existingEmail->DoctorID}).";
                        continue;
                    }
                    
                    // Generate temporary password
                    $temporaryPassword = PasswordHelper::generateTemporaryPassword(8);
                    
                    // For doctors, use DoctorID as UserID in user table
                    // This allows doctors to login with their DoctorID
                    $newUserID = $doctorID; // Use DoctorID as UserID for doctors
                    
                    // Check if UserID (DoctorID) already exists in user table
                    $existingUser = DB::table('user')->where('UserID', $newUserID)->first();
                    if ($existingUser) {
                        $errors[] = "Row {$rowNumber}: UserID '{$newUserID}' already exists in user table. Please use a different DoctorID.";
                        continue;
                    }
                    
                    // Create user account for doctor using DoctorID as UserID
                    DB::table('user')->insert([
                        'UserID' => $newUserID, // Use DoctorID as UserID
                        'PasswordHash' => $temporaryPassword, // Store plain text (as per current system)
                        'role' => 'doctor',
                        'must_change_password' => true, // Force password change on first login
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    // Create new doctor
                    try {
                        $doctor = Doctor::create([
                            'DoctorID' => $doctorID,
                            'FullName' => $fullName,
                            'Email' => $email
                        ]);
                        
                        // Verify the doctor was created correctly
                        $savedDoctor = Doctor::where('DoctorID', $doctorID)->first();
                        if (!$savedDoctor) {
                            $errors[] = "Row {$rowNumber}: Doctor '{$fullName}' was not created successfully.";
                            // Delete the user account that was created
                            DB::table('user')->where('UserID', $newUserID)->delete();
                            continue;
                        }
                    } catch (\Exception $e) {
                        $errors[] = "Row {$rowNumber}: Failed to create doctor '{$fullName}': " . $e->getMessage();
                        // Delete the user account that was created if doctor creation failed
                        DB::table('user')->where('UserID', $newUserID)->delete();
                        continue;
                    }
                    
                    // Send email to doctor with login credentials
                    // For doctors, send DoctorID for login (not UserID)
                    try {
                        Mail::to($email)->send(new StaffRegistrationMail(
                            $fullName,
                            $doctorID, // Send DoctorID for login instead of UserID
                            $email,
                            $temporaryPassword,
                            'doctor'
                        ));
                    } catch (\Exception $e) {
                        $errors[] = "Row {$rowNumber}: Failed to send email to '{$email}': " . $e->getMessage();
                    }
                    
                    // Refresh to ensure all attributes are loaded
                    $doctor->refresh();
                    
                    // Ensure DoctorID is included in response
                    $doctors[] = [
                        'DoctorID' => $doctorID,
                        'FullName' => $doctor->FullName ?? $fullName,
                        'Email' => $doctor->Email ?? $email
                    ];
                    $successCount++;
                }
                fclose($handle);
            }

            if ($successCount > 0) {
                $message = "✅ Successfully registered {$successCount} doctor(s)! Registration emails with temporary passwords have been sent to all registered doctors.";
            } else {
                $message = "No doctors were registered.";
            }
            
            if (count($errors) > 0) {
                $message .= "\n\n⚠️ " . count($errors) . " error(s) occurred during registration.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'count' => $successCount,
                'doctors' => $doctors,
                'errors' => $errors,
                'errorCount' => count($errors)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing CSV: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get list of all registered doctors
     */
    public function getDoctorList()
    {
        try {
            $doctors = DB::table('doctor')
                ->select('DoctorID', 'FullName', 'Email')
                ->orderBy('DoctorID', 'asc')
                ->get()
                ->map(function($doctor) {
                    return [
                        'DoctorID' => $doctor->DoctorID ?? '',
                        'FullName' => $doctor->FullName ?? '',
                        'Email' => $doctor->Email ?? ''
                    ];
                })
                ->filter(function($doctor) {
                    // Filter out any doctors with empty DoctorID
                    return !empty($doctor['DoctorID']);
                })
                ->values()
                ->toArray();
            
            return response()->json([
                'success' => true,
                'doctors' => $doctors
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading doctors: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display nurse registration form
     */
    public function showRegisterNurse()
    {
        return view('manageUser.RegisterNurse');
    }

    /**
     * Upload CSV file to register multiple nurses
     */
    public function uploadNurseCSV(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        try {
            $file = $request->file('csv_file');
            $path = $file->getRealPath();
            
            $nurses = [];
            $successCount = 0;
            $errors = [];

            if (($handle = fopen($path, 'r')) !== false) {
                // Skip header row
                $header = fgetcsv($handle);
                
                $rowNumber = 1; // Track row number for better error messages
                while (($data = fgetcsv($handle)) !== false) {
                    $rowNumber++;
                    if (count($data) < 3) {
                        $errors[] = "Row {$rowNumber}: Insufficient columns. Expected at least 3 columns (NurseID, FullName, Email)";
                        continue;
                    }
                    
                    $nurseID = trim($data[0] ?? '');
                    $fullName = trim($data[1] ?? '');
                    $email = trim($data[2] ?? '');
                    
                    // Check for empty required fields
                    if (empty($nurseID)) {
                        $errors[] = "Row {$rowNumber}: NurseID is empty. Please provide a NurseID in the first column.";
                        continue;
                    }
                    
                    if (empty($fullName)) {
                        $errors[] = "Row {$rowNumber}: FullName is empty for NurseID '{$nurseID}'.";
                        continue;
                    }
                    
                    if (empty($email)) {
                        $errors[] = "Row {$rowNumber}: Email is empty for NurseID '{$nurseID}'.";
                        continue;
                    }
                    
                    // Check if nurse already exists by NurseID
                    $existing = Nurse::where('NurseID', $nurseID)->first();
                    if ($existing) {
                        $errors[] = "Row {$rowNumber}: Nurse with ID '{$nurseID}' already exists in the system (Email: {$existing->Email}).";
                        continue;
                    }
                    
                    // Also check by email to prevent duplicates (check FIRST before creating)
                    $existingEmail = Nurse::where('Email', $email)->first();
                    if ($existingEmail) {
                        $errors[] = "Row {$rowNumber}: Nurse with email '{$email}' already exists (NurseID: {$existingEmail->NurseID}, Name: {$existingEmail->FullName}). Duplicate registration prevented.";
                        continue;
                    }
                    
                    // Generate temporary password
                    $temporaryPassword = PasswordHelper::generateTemporaryPassword(8);
                    
                    // For nurses, use NurseID as UserID in user table
                    // This allows nurses to login with their NurseID
                    $newUserID = $nurseID; // Use NurseID as UserID for nurses
                    
                    // Check if UserID (NurseID) already exists in user table
                    $existingUser = DB::table('user')->where('UserID', $newUserID)->first();
                    if ($existingUser) {
                        $errors[] = "Row {$rowNumber}: UserID '{$newUserID}' already exists in user table. Please use a different NurseID.";
                        continue;
                    }
                    
                    // Create new nurse
                    try {
                        $nurse = Nurse::create([
                            'NurseID' => $nurseID,
                            'FullName' => $fullName,
                            'Email' => $email
                        ]);
                        
                        // Verify the nurse was created correctly
                        $savedNurse = Nurse::where('NurseID', $nurseID)->first();
                        if (!$savedNurse) {
                            $errors[] = "Row {$rowNumber}: Nurse '{$fullName}' was not created successfully.";
                            continue;
                        }
                    } catch (\Exception $e) {
                        $errors[] = "Row {$rowNumber}: Failed to create nurse '{$fullName}': " . $e->getMessage();
                        continue;
                    }
                    
                    // Create user account for nurse using NurseID as UserID
                    DB::table('user')->insert([
                        'UserID' => $newUserID, // Use NurseID as UserID
                        'PasswordHash' => $temporaryPassword, // Store plain text (as per current system)
                        'role' => 'nurse',
                        'must_change_password' => true, // Force password change on first login
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    // Send email to nurse with login credentials
                    // For nurses, send NurseID for login (not UserID)
                    try {
                        Mail::to($email)->send(new StaffRegistrationMail(
                            $fullName,
                            $nurseID, // Send NurseID for login instead of UserID
                            $email,
                            $temporaryPassword,
                            'nurse'
                        ));
                    } catch (\Exception $e) {
                        $errors[] = "Row {$rowNumber}: Failed to send email to '{$email}': " . $e->getMessage();
                    }
                    
                    // Refresh to ensure all attributes are loaded
                    $nurse->refresh();
                    
                    // Ensure NurseID is included in response
                    $nurses[] = [
                        'NurseID' => $nurseID,
                        'FullName' => $nurse->FullName ?? $fullName,
                        'Email' => $nurse->Email ?? $email
                    ];
                    $successCount++;
                }
                fclose($handle);
            }

            if ($successCount > 0) {
                $message = "✅ Successfully registered {$successCount} nurse(s)! Registration emails with temporary passwords have been sent to all registered nurses.";
            } else {
                $message = "No nurses were registered.";
            }
            
            if (count($errors) > 0) {
                $message .= "\n\n⚠️ " . count($errors) . " error(s) occurred during registration.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'count' => $successCount,
                'nurses' => $nurses,
                'errors' => $errors,
                'errorCount' => count($errors)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing CSV: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get list of all registered nurses
     */
    public function getNurseList()
    {
        try {
            $nurses = DB::table('nurse')
                ->select('NurseID', 'FullName', 'Email')
                ->orderBy('NurseID', 'asc')
                ->get()
                ->map(function($nurse) {
                    return [
                        'NurseID' => $nurse->NurseID ?? '',
                        'FullName' => $nurse->FullName ?? '',
                        'Email' => $nurse->Email ?? ''
                    ];
                })
                ->filter(function($nurse) {
                    // Filter out any nurses with empty NurseID
                    return !empty($nurse['NurseID']);
                })
                ->values()
                ->toArray();
            
            return response()->json([
                'success' => true,
                'nurses' => $nurses
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading nurses: ' . $e->getMessage()
            ], 500);
        }
    }
}

