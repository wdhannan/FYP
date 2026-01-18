<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Appointment;
use App\Models\Child;
use App\Models\Doctor;
use App\Models\Nurse;
use App\Models\ParentModel;
use App\Mail\AppointmentReminderMail;
use App\Mail\AppointmentRequestMail;

class manageAppointmentController extends Controller
{
    /**
     * Display list of children for appointment booking
     */
    public function listChildren()
    {
        try {
            $children = DB::table('child')
                ->leftJoin('parent', 'child.ParentID', '=', 'parent.ParentID')
                ->select(
                    'child.ChildID',
                    'child.FullName',
                    'child.DateOfBirth',
                    'child.Gender',
                    'parent.MotherName',
                    'parent.FatherName'
                )
                ->get();
            
            return view('manageAppointment.ListOfChild', compact('children'));
        } catch (\Exception $e) {
            return view('manageAppointment.ListOfChild', ['children' => collect([])])
                ->with('error', 'Error loading children: ' . $e->getMessage());
        }
    }

    /**
     * Display booking form for a specific child
     */
    public function showBookingForm($childId)
    {
        try {
            // Get child - use DB facade for reliability
            $child = DB::table('child')->where('ChildID', $childId)->first();
            
            // If child not found, redirect back with error
            if (!$child) {
                return redirect()->route('list.child')->with('error', 'Child not found with ID: ' . $childId);
            }
            
            // Convert to object for view compatibility
            $childObj = Child::where('ChildID', $childId)->first();
            if (!$childObj) {
                // Create a simple object if Eloquent fails
                $childObj = (object)[
                    'ChildID' => $child->ChildID,
                    'FullName' => $child->FullName ?? 'Unknown'
                ];
            }
            
            // Get doctors - use DB facade
            $doctors = DB::table('doctor')
                ->select('DoctorID', 'FullName')
                ->orderBy('FullName', 'asc')
                ->get();
            
            // Get nurses - use DB facade
            $nurses = DB::table('nurse')
                ->select('NurseID', 'FullName')
                ->orderBy('FullName', 'asc')
                ->get();
            
            // Get current logged-in nurse
            $currentNurseId = null;
            $userRole = session('user_role');
            if ($userRole === 'nurse') {
                $sessionUserId = session('user_id');
                if ($sessionUserId) {
                    $nurse = DB::table('nurse')->where('NurseID', $sessionUserId)->first();
                    if ($nurse) {
                        $currentNurseId = $nurse->NurseID;
                    } elseif ($nurses->count() > 0) {
                        $currentNurseId = $nurses->first()->NurseID;
                    }
                } elseif ($nurses->count() > 0) {
                    $currentNurseId = $nurses->first()->NurseID;
                }
            }
            
            return view('manageAppointment.BookingForm', [
                'child' => $childObj,
                'childName' => $child->FullName ?? 'Unknown',
                'childId' => $child->ChildID,
                'doctors' => $doctors,
                'nurses' => $nurses,
                'currentNurseId' => $currentNurseId
            ]);
        } catch (\Exception $e) {
            // Log error but don't show it to user - just redirect
            \Log::error('Error in showBookingForm: ' . $e->getMessage());
            return redirect()->route('list.child')->with('error', 'Unable to load booking form. Please try again.');
        }
    }

    /**
     * Store new appointment booking
     */
    public function storeBooking(Request $request)
    {
        // Log incoming request data for debugging
        \Log::info('Booking form submission received', [
            'child_id' => $request->child_id,
            'doctor_id' => $request->doctor_id,
            'nurse_id' => $request->nurse_id,
            'date' => $request->date,
            'time' => $request->time,
            'session_user_id' => session('user_id'),
            'session_user_role' => session('user_role')
        ]);
        
        // Trim whitespace from IDs
        $childId = trim($request->child_id ?? '');
        $doctorId = trim($request->doctor_id ?? '');
        $nurseId = trim($request->nurse_id ?? '');
        
        // If nurse_id is not provided or empty, try to get it from session
        if (empty($nurseId) && session('user_role') === 'nurse') {
            $nurseId = session('user_id');
            \Log::info('Nurse ID not provided, using from session', ['nurse_id' => $nurseId]);
        }
        
        // Merge trimmed values back into request
        $request->merge([
            'child_id' => $childId,
            'doctor_id' => $doctorId,
            'nurse_id' => $nurseId
        ]);
        
        $request->validate([
            'child_id' => 'required|exists:child,ChildID',
            'doctor_id' => 'required|exists:doctor,DoctorID',
            'nurse_id' => 'required|exists:nurse,NurseID',
            'date' => 'required|date',
            'time' => 'required',
            'status' => 'nullable|in:pending,confirmed,approved,cancelled,completed'
        ], [
            'child_id.required' => 'Child ID is required.',
            'child_id.exists' => 'Selected child does not exist. ChildID: ' . ($childId ?: 'empty'),
            'doctor_id.required' => 'Please select a doctor.',
            'doctor_id.exists' => 'Selected doctor does not exist. DoctorID: ' . ($doctorId ?: 'empty'),
            'nurse_id.required' => 'Nurse ID is required.',
            'nurse_id.exists' => 'Selected nurse does not exist. NurseID: ' . ($nurseId ?: 'empty'),
            'date.required' => 'Please select a date.',
            'date.date' => 'Please enter a valid date.',
            'time.required' => 'Please select a time slot.',
        ]);

        try {
            // Schedule validation removed - allow any date

            // Generate AppointmentID
            $maxAppointmentID = DB::table('appointment')
                ->whereRaw("AppointmentID REGEXP '^APP[0-9]+$'")
                ->selectRaw("CAST(SUBSTRING(AppointmentID, 4) AS UNSIGNED) as num")
                ->orderBy('num', 'desc')
                ->value('num');
            
            $nextNumber = ($maxAppointmentID ?? 0) + 1;
            $appointmentID = 'APP' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            // Get child and doctor details for email
            $child = Child::where('ChildID', $request->child_id)->first();
            $doctor = Doctor::where('DoctorID', $request->doctor_id)->first();
            $nurse = Nurse::where('NurseID', $request->nurse_id)->first();
            
            // Convert time format from "8:00 AM" to "08:00:00" for database
            $timeValue = $request->time;
            try {
                // Try to parse time string like "8:00 AM" or "8:00 am"
                if (preg_match('/(\d{1,2}):(\d{2})\s*(AM|PM|am|pm)/i', $timeValue, $matches)) {
                    $hour = (int)$matches[1];
                    $minute = (int)$matches[2];
                    $period = strtoupper($matches[3]);
                    
                    // Convert to 24-hour format
                    if ($period === 'PM' && $hour != 12) {
                        $hour += 12;
                    } elseif ($period === 'AM' && $hour == 12) {
                        $hour = 0;
                    }
                    
                    // Format as TIME: "HH:MM:SS"
                    $timeValue = sprintf('%02d:%02d:00', $hour, $minute);
                } else {
                    // If already in correct format, use as is
                    $timeValue = $request->time;
                }
            } catch (\Exception $e) {
                \Log::warning('Time format conversion failed, using original value', [
                    'time' => $request->time,
                    'error' => $e->getMessage()
                ]);
                $timeValue = $request->time;
            }
            
            // Create appointment
            Appointment::create([
                'AppointmentID' => $appointmentID,
                'ChildID' => $request->child_id,
                'DoctorID' => $request->doctor_id,
                'NurseID' => $request->nurse_id,
                'date' => $request->date,
                'time' => $timeValue,
                'status' => $request->status ?? 'pending'
            ]);
            
            \Log::info('✅ Appointment created successfully and saved to database', [
                'AppointmentID' => $appointmentID,
                'ChildID' => $request->child_id,
                'DoctorID' => $request->doctor_id,
                'NurseID' => $request->nurse_id,
                'date' => $request->date,
                'time' => $timeValue,
                'status' => $request->status ?? 'pending'
            ]);

            // Send notification email to doctor
            if ($doctor && $doctor->Email) {
                try {
                    Mail::to($doctor->Email)->send(new AppointmentRequestMail(
                        $doctor->FullName,
                        $child->FullName ?? 'Unknown',
                        $request->date,
                        $request->time,
                        $appointmentID,
                        $nurse->FullName ?? 'Unknown'
                    ));
                } catch (\Exception $e) {
                    \Log::error('Failed to send appointment request email to doctor: ' . $e->getMessage());
                    // Don't fail the appointment creation if email fails
                }
            }

            return redirect()->route('appointment.status')
                ->with('success', '✅ Appointment booked successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in storeBooking', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            return back()->withInput()
                ->withErrors($e->errors())
                ->with('error', 'Validation failed. Please check your input.');
        } catch (\Exception $e) {
            \Log::error('Error creating appointment', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all()
            ]);
            return back()->withInput()
                ->with('error', 'Error creating appointment: ' . $e->getMessage());
        }
    }

    /**
     * Display appointment status page
     */
    public function appointmentStatus()
    {
        try {
            $appointments = DB::table('appointment')
                ->leftJoin('child', 'appointment.ChildID', '=', 'child.ChildID')
                ->leftJoin('doctor', 'appointment.DoctorID', '=', 'doctor.DoctorID')
                ->leftJoin('nurse', 'appointment.NurseID', '=', 'nurse.NurseID')
                ->select(
                    'appointment.*',
                    'child.FullName as ChildName',
                    'doctor.FullName as DoctorName',
                    'nurse.FullName as NurseName'
                )
                ->orderBy('appointment.created_at', 'desc')
                ->orderBy('appointment.AppointmentID', 'desc')
                ->get();
            
            return view('manageAppointment.AppointmentStatus', compact('appointments'));
        } catch (\Exception $e) {
            return view('manageAppointment.AppointmentStatus', ['appointments' => collect([])])
                ->with('error', 'Error loading appointments: ' . $e->getMessage());
        }
    }

    /**
     * Display appointment requests (pending appointments)
     */
    public function appointmentRequests()
    {
        try {
            $appointmentRequests = DB::table('appointment')
                ->leftJoin('child', 'appointment.ChildID', '=', 'child.ChildID')
                ->leftJoin('doctor', 'appointment.DoctorID', '=', 'doctor.DoctorID')
                ->leftJoin('nurse', 'appointment.NurseID', '=', 'nurse.NurseID')
                ->leftJoin('parent', 'child.ParentID', '=', 'parent.ParentID')
                ->where('appointment.status', 'pending')
                ->select(
                    'appointment.*',
                    'child.FullName as ChildName',
                    'doctor.FullName as DoctorName',
                    'nurse.FullName as NurseName',
                    'parent.MotherName',
                    'parent.FatherName'
                )
                ->orderBy('appointment.created_at', 'desc')
                ->orderBy('appointment.date', 'desc')
                ->orderBy('appointment.time', 'desc')
                ->get();
            
            return view('manageAppointment.AppointmentRequest', compact('appointmentRequests'));
        } catch (\Exception $e) {
            return view('manageAppointment.AppointmentRequest', ['appointmentRequests' => collect([])])
                ->with('error', 'Error loading appointment requests: ' . $e->getMessage());
        }
    }

    /**
     * Display appointment history
     */
    public function appointmentHistory(Request $request)
    {
        try {
            $userRole = session('user_role');
            $userId = session('user_id');
            
            $query = DB::table('appointment')
                ->leftJoin('child', 'appointment.ChildID', '=', 'child.ChildID')
                ->leftJoin('doctor', 'appointment.DoctorID', '=', 'doctor.DoctorID')
                ->leftJoin('nurse', 'appointment.NurseID', '=', 'nurse.NurseID')
                ->select(
                    'appointment.*',
                    'child.FullName as ChildName',
                    'child.ParentID',
                    'child.ChildID',
                    'doctor.FullName as DoctorName',
                    'nurse.FullName as NurseName'
                );
            
            // Filter by parent if user is a parent
            if ($userRole === 'parent' && $userId) {
                $query->where('child.ParentID', $userId);
                
                // Filter by specific child if child_id is provided
                $selectedChildId = $request->input('child_id');
                
                // If no child_id is provided, default to first child (like Child Record logic)
                if (!$selectedChildId) {
                    $firstChild = DB::table('child')
                        ->where('ParentID', $userId)
                        ->orderBy('ChildID', 'asc')
                        ->first();
                    if ($firstChild) {
                        $selectedChildId = $firstChild->ChildID;
                    }
                }
                
                if ($selectedChildId) {
                    $query->where('appointment.ChildID', $selectedChildId);
                }
            }
            
            $appointments = $query->orderBy('appointment.date', 'desc')
                ->orderBy('appointment.time', 'desc')
                ->get();
            
            // Use unified view for all roles
            return view('manageAppointment.AppointmentHistory', compact('appointments'));
        } catch (\Exception $e) {
            return view('manageAppointment.AppointmentHistory', ['appointments' => collect([])])
                ->with('error', 'Error loading appointment history: ' . $e->getMessage());
        }
    }

    /**
     * Approve appointment and send reminder email to parent
     */
    public function approveAppointment($appointmentID)
    {
        try {
            // Get appointment details
            $appointment = DB::table('appointment')
                ->where('AppointmentID', $appointmentID)
                ->first();
            
            if (!$appointment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Appointment not found'
                ], 404);
            }
            
            // Check if already approved
            if (strtolower($appointment->status) === 'approved' || strtolower($appointment->status) === 'confirmed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Appointment is already approved.'
                ], 400);
            }
            
            // Update appointment status to approved
            DB::table('appointment')
                ->where('AppointmentID', $appointmentID)
                ->update([
                    'status' => 'approved',
                    'updated_at' => now()
                ]);
            
            // Get child information
            $child = DB::table('child')
                ->where('ChildID', $appointment->ChildID)
                ->first();
            
            if (!$child) {
                return response()->json([
                    'success' => false,
                    'message' => 'Child information not found'
                ], 404);
            }
            
            // Get parent information
            $parent = DB::table('parent')
                ->where('ParentID', $child->ParentID)
                ->first();
            
            if (!$parent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parent information not found'
                ], 404);
            }
            
            // Get doctor information
            $doctor = DB::table('doctor')
                ->where('DoctorID', $appointment->DoctorID)
                ->first();
            
            // Determine parent email (prefer mother's email, fallback to father's email)
            $parentEmail = $parent->MEmail ?? $parent->FEmail ?? null;
            $parentName = $parent->MotherName ?? $parent->FatherName ?? 'Parent';
            
            if (!$parentEmail) {
                return response()->json([
                    'success' => false,
                    'message' => 'Parent email not found. Cannot send reminder.'
                ], 400);
            }
            
            // Format date and time
            $appointmentDate = $appointment->date;
            $appointmentTime = $appointment->time;
            
            // Send reminder email to parent
            try {
                Mail::to($parentEmail)->send(new AppointmentReminderMail(
                    $parentName,
                    $child->FullName,
                    $doctor->FullName ?? 'Doctor',
                    $appointmentDate,
                    $appointmentTime,
                    $appointmentID
                ));
            } catch (\Exception $e) {
                // Log error but don't fail the approval
                \Log::error('Failed to send appointment reminder email: ' . $e->getMessage());
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Appointment approved successfully and reminder email sent to parent.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving appointment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update appointment status
     */
    public function updateStatus(Request $request, $appointmentID)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,approved,rejected,cancelled,completed'
        ]);

        try {
            $appointment = Appointment::where('AppointmentID', $appointmentID)->first();
            
            if (!$appointment) {
                return back()->with('error', 'Appointment not found.');
            }
            
            $oldStatus = $appointment->status;
            
            $appointment->update([
                'status' => $request->status
            ]);
            
            // If status changed to approved, send email to parent
            if (strtolower($request->status) === 'approved' && strtolower($oldStatus) !== 'approved') {
                // Get child information
                $child = DB::table('child')
                    ->where('ChildID', $appointment->ChildID)
                    ->first();
                
                if ($child) {
                    // Get parent information
                    $parent = DB::table('parent')
                        ->where('ParentID', $child->ParentID)
                        ->first();
                    
                    if ($parent) {
                        // Get doctor information
                        $doctor = DB::table('doctor')
                            ->where('DoctorID', $appointment->DoctorID)
                            ->first();
                        
                        // Determine parent email (prefer mother's email, fallback to father's email)
                        $parentEmail = $parent->MEmail ?? $parent->FEmail ?? null;
                        $parentName = $parent->MotherName ?? $parent->FatherName ?? 'Parent';
                        
                        // Send reminder email to parent if email exists
                        if ($parentEmail) {
                            try {
                                Mail::to($parentEmail)->send(new AppointmentReminderMail(
                                    $parentName,
                                    $child->FullName,
                                    $doctor->FullName ?? 'Doctor',
                                    $appointment->date,
                                    $appointment->time,
                                    $appointmentID
                                ));
                            } catch (\Exception $e) {
                                // Log error but don't fail the status update
                                \Log::error('Failed to send appointment reminder email: ' . $e->getMessage());
                            }
                        }
                    }
                }
            }
            
            $message = 'Appointment status updated successfully.';
            if (strtolower($request->status) === 'approved' && strtolower($oldStatus) !== 'approved') {
                $message .= ' Reminder email sent to parent.';
            }
            
            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating appointment status: ' . $e->getMessage());
        }
    }
}

