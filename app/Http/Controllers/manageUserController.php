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
            $validRows = [];
            
            // Step 1: Read all CSV rows into memory
            $csvRows = [];
            if (($handle = fopen($path, 'r')) !== false) {
                $header = fgetcsv($handle);
                $rowNumber = 1;
                while (($data = fgetcsv($handle)) !== false) {
                    $rowNumber++;
                    if (count($data) < 3) {
                        $errors[] = "Row {$rowNumber}: Insufficient columns. Expected at least 3 columns (DoctorID, FullName, Email)";
                        continue;
                    }
                    
                    $doctorID = trim($data[0] ?? '');
                    $fullName = trim($data[1] ?? '');
                    $email = trim($data[2] ?? '');
                    
                    if (empty($doctorID) || empty($fullName) || empty($email)) {
                        $missing = [];
                        if (empty($doctorID)) $missing[] = 'DoctorID';
                        if (empty($fullName)) $missing[] = 'FullName';
                        if (empty($email)) $missing[] = 'Email';
                        $errors[] = "Row {$rowNumber}: Missing required fields: " . implode(', ', $missing);
                        continue;
                    }
                    
                    $csvRows[] = [
                        'row' => $rowNumber,
                        'DoctorID' => $doctorID,
                        'FullName' => $fullName,
                        'Email' => $email
                    ];
                }
                fclose($handle);
            }
            
            if (empty($csvRows)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid rows found in CSV file.'
                ], 400);
            }
            
            // Step 2: Batch check all existing records in one query
            $doctorIDs = array_column($csvRows, 'DoctorID');
            $emails = array_column($csvRows, 'Email');
            
            $existingDoctors = DB::table('doctor')
                ->whereIn('DoctorID', $doctorIDs)
                ->orWhereIn('Email', $emails)
                ->get()
                ->keyBy(function($item) {
                    return $item->DoctorID . '|' . $item->Email;
                });
            
            $existingUsers = DB::table('user')
                ->whereIn('UserID', $doctorIDs)
                ->pluck('UserID')
                ->toArray();
            
            // Step 3: Validate and prepare bulk insert data
            $usersToInsert = [];
            $doctorsToInsert = [];
            $emailsToQueue = [];
            $now = now();
            
            foreach ($csvRows as $row) {
                $doctorID = $row['DoctorID'];
                $email = $row['Email'];
                $fullName = $row['FullName'];
                $rowNumber = $row['row'];
                
                // Check duplicates
                $doctorExists = $existingDoctors->contains(function($item) use ($doctorID, $email) {
                    return $item->DoctorID === $doctorID || $item->Email === $email;
                });
                
                if ($doctorExists) {
                    $errors[] = "Row {$rowNumber}: Doctor with ID '{$doctorID}' or email '{$email}' already exists.";
                    continue;
                }
                
                if (in_array($doctorID, $existingUsers)) {
                    $errors[] = "Row {$rowNumber}: UserID '{$doctorID}' already exists in user table.";
                    continue;
                }
                
                // Generate password
                $temporaryPassword = PasswordHelper::generateTemporaryPassword(8);
                
                // Prepare bulk insert data
                $usersToInsert[] = [
                    'UserID' => $doctorID,
                    'PasswordHash' => $temporaryPassword,
                    'role' => 'doctor',
                    'must_change_password' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                
                $doctorsToInsert[] = [
                    'DoctorID' => $doctorID,
                    'FullName' => $fullName,
                    'Email' => $email,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                
                $emailsToQueue[] = [
                    'email' => $email,
                    'fullName' => $fullName,
                    'doctorID' => $doctorID,
                    'password' => $temporaryPassword,
                ];
                
                $validRows[] = $rowNumber;
            }
            
            // Step 4: Bulk insert in transaction
            if (!empty($usersToInsert) && !empty($doctorsToInsert)) {
                DB::beginTransaction();
                try {
                    // Bulk insert users
                    DB::table('user')->insert($usersToInsert);
                    
                    // Bulk insert doctors
                    DB::table('doctor')->insert($doctorsToInsert);
                    
                    DB::commit();
                    
                    // Step 5: Queue emails asynchronously (don't wait for email sending)
                    foreach ($emailsToQueue as $emailData) {
                        try {
                            // Use queue if available, otherwise log for later sending
                            if (config('queue.default') !== 'sync') {
                                Mail::to($emailData['email'])->queue(new StaffRegistrationMail(
                                    $emailData['fullName'],
                                    $emailData['doctorID'],
                                    $emailData['email'],
                                    $emailData['password'],
                                    'doctor'
                                ));
                            } else {
                                // Send email (non-blocking in production with proper mail config)
                                // For best performance, configure queue driver in .env
                                Mail::to($emailData['email'])->send(new StaffRegistrationMail(
                                    $emailData['fullName'],
                                    $emailData['doctorID'],
                                    $emailData['email'],
                                    $emailData['password'],
                                    'doctor'
                                ));
                            }
                        } catch (\Exception $e) {
                            \Log::error("Failed to send email for doctor {$emailData['doctorID']}: " . $e->getMessage());
                        }
                    }
                    
                    $successCount = count($doctorsToInsert);
                    $doctors = array_map(function($item) {
                        return [
                            'DoctorID' => $item['DoctorID'],
                            'FullName' => $item['FullName'],
                            'Email' => $item['Email']
                        ];
                    }, $doctorsToInsert);
                    
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Error inserting data: ' . $e->getMessage()
                    ], 500);
                }
            }

            // Log errors for debugging but don't show them to user
            if (count($errors) > 0) {
                \Log::info('Doctor CSV Upload - Some rows had errors: ' . count($errors), ['errors' => $errors]);
            }

            if ($successCount > 0) {
                $message = "✅ Successfully registered {$successCount} doctor(s)! Registration emails are being sent.";
            } else {
                $message = "No doctors were registered.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'count' => $successCount,
                'doctors' => $doctors
            ]);
        } catch (\Exception $e) {
            \Log::error('Doctor CSV Upload Error: ' . $e->getMessage());
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
            $validRows = [];
            
            // Step 1: Read all CSV rows into memory
            $csvRows = [];
            if (($handle = fopen($path, 'r')) !== false) {
                $header = fgetcsv($handle);
                $rowNumber = 1;
                while (($data = fgetcsv($handle)) !== false) {
                    $rowNumber++;
                    if (count($data) < 3) {
                        $errors[] = "Row {$rowNumber}: Insufficient columns. Expected at least 3 columns (NurseID, FullName, Email)";
                        continue;
                    }
                    
                    $nurseID = trim($data[0] ?? '');
                    $fullName = trim($data[1] ?? '');
                    $email = trim($data[2] ?? '');
                    
                    if (empty($nurseID) || empty($fullName) || empty($email)) {
                        $missing = [];
                        if (empty($nurseID)) $missing[] = 'NurseID';
                        if (empty($fullName)) $missing[] = 'FullName';
                        if (empty($email)) $missing[] = 'Email';
                        $errors[] = "Row {$rowNumber}: Missing required fields: " . implode(', ', $missing);
                        continue;
                    }
                    
                    $csvRows[] = [
                        'row' => $rowNumber,
                        'NurseID' => $nurseID,
                        'FullName' => $fullName,
                        'Email' => $email
                    ];
                }
                fclose($handle);
            }
            
            if (empty($csvRows)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid rows found in CSV file.'
                ], 400);
            }
            
            // Step 2: Batch check all existing records in one query
            $nurseIDs = array_column($csvRows, 'NurseID');
            $emails = array_column($csvRows, 'Email');
            
            $existingNurses = DB::table('nurse')
                ->whereIn('NurseID', $nurseIDs)
                ->orWhereIn('Email', $emails)
                ->get()
                ->keyBy(function($item) {
                    return $item->NurseID . '|' . $item->Email;
                });
            
            $existingUsers = DB::table('user')
                ->whereIn('UserID', $nurseIDs)
                ->pluck('UserID')
                ->toArray();
            
            // Step 3: Validate and prepare bulk insert data
            $usersToInsert = [];
            $nursesToInsert = [];
            $emailsToQueue = [];
            $now = now();
            
            foreach ($csvRows as $row) {
                $nurseID = $row['NurseID'];
                $email = $row['Email'];
                $fullName = $row['FullName'];
                $rowNumber = $row['row'];
                
                // Check duplicates
                $nurseExists = $existingNurses->contains(function($item) use ($nurseID, $email) {
                    return $item->NurseID === $nurseID || $item->Email === $email;
                });
                
                if ($nurseExists) {
                    $errors[] = "Row {$rowNumber}: Nurse with ID '{$nurseID}' or email '{$email}' already exists.";
                    continue;
                }
                
                if (in_array($nurseID, $existingUsers)) {
                    $errors[] = "Row {$rowNumber}: UserID '{$nurseID}' already exists in user table.";
                    continue;
                }
                
                // Generate password
                $temporaryPassword = PasswordHelper::generateTemporaryPassword(8);
                
                // Prepare bulk insert data
                $usersToInsert[] = [
                    'UserID' => $nurseID,
                    'PasswordHash' => $temporaryPassword,
                    'role' => 'nurse',
                    'must_change_password' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                
                $nursesToInsert[] = [
                    'NurseID' => $nurseID,
                    'FullName' => $fullName,
                    'Email' => $email,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                
                $emailsToQueue[] = [
                    'email' => $email,
                    'fullName' => $fullName,
                    'nurseID' => $nurseID,
                    'password' => $temporaryPassword,
                ];
                
                $validRows[] = $rowNumber;
            }
            
            // Step 4: Bulk insert in transaction
            if (!empty($usersToInsert) && !empty($nursesToInsert)) {
                DB::beginTransaction();
                try {
                    // Bulk insert users
                    DB::table('user')->insert($usersToInsert);
                    
                    // Bulk insert nurses
                    DB::table('nurse')->insert($nursesToInsert);
                    
                    DB::commit();
                    
                    // Step 5: Queue emails asynchronously (don't wait for email sending)
                    foreach ($emailsToQueue as $emailData) {
                        try {
                            // Use queue if available, otherwise send (emails won't block response)
                            if (config('queue.default') !== 'sync') {
                                Mail::to($emailData['email'])->queue(new StaffRegistrationMail(
                                    $emailData['fullName'],
                                    $emailData['nurseID'],
                                    $emailData['email'],
                                    $emailData['password'],
                                    'nurse'
                                ));
                            } else {
                                // Send email (non-blocking in production with proper mail config)
                                Mail::to($emailData['email'])->send(new StaffRegistrationMail(
                                    $emailData['fullName'],
                                    $emailData['nurseID'],
                                    $emailData['email'],
                                    $emailData['password'],
                                    'nurse'
                                ));
                            }
                        } catch (\Exception $e) {
                            \Log::error("Failed to send email for nurse {$emailData['nurseID']}: " . $e->getMessage());
                        }
                    }
                    
                    $successCount = count($nursesToInsert);
                    $nurses = array_map(function($item) {
                        return [
                            'NurseID' => $item['NurseID'],
                            'FullName' => $item['FullName'],
                            'Email' => $item['Email']
                        ];
                    }, $nursesToInsert);
                    
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Error inserting data: ' . $e->getMessage()
                    ], 500);
                }
            }

            // Log errors for debugging but don't show them to user
            if (count($errors) > 0) {
                \Log::info('Nurse CSV Upload - Some rows had errors: ' . count($errors), ['errors' => $errors]);
            }

            if ($successCount > 0) {
                $message = "✅ Successfully registered {$successCount} nurse(s)! Registration emails are being sent.";
            } else {
                $message = "No nurses were registered.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'count' => $successCount,
                'nurses' => $nurses
            ]);
        } catch (\Exception $e) {
            \Log::error('Nurse CSV Upload Error: ' . $e->getMessage());
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

