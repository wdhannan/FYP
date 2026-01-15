<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Schedule;
use App\Models\Doctor;

class manageScheduleController extends Controller
{
    /**
     * Display form to add new schedule
     */
    public function create()
    {
        try {
            // Check if user is logged in and is a doctor
            $userRole = session('user_role');
            if ($userRole !== 'doctor') {
                return redirect()->route('home')->with('error', 'Only doctors can add schedules.');
            }
            
            // Get DoctorID directly from session (for doctors, user_id = DoctorID)
            $doctorId = session('user_id');
            if (!$doctorId) {
                return view('manageSchedule.AddSchedule')
                    ->with('error', 'User not authenticated.');
            }
            
            // Verify doctor exists with this DoctorID
            $doctor = Doctor::where('DoctorID', $doctorId)->first();
            if (!$doctor) {
                // Debug: Show what DoctorID we're looking for
                $errorMsg = 'Doctor profile not found. ';
                $errorMsg .= 'Looking for DoctorID: ' . $doctorId . '. ';
                $errorMsg .= 'Please ensure your DoctorID in the database matches your login ID.';
                return view('manageSchedule.AddSchedule')
                    ->with('error', $errorMsg);
            }
            
            return view('manageSchedule.AddSchedule');
        } catch (\Exception $e) {
            return view('manageSchedule.AddSchedule')
                ->with('error', 'Error loading form: ' . $e->getMessage());
        }
    }

    /**
     * Store new schedule (file upload)
     */
    public function store(Request $request)
    {
        $request->validate([
            'upload_date' => 'required|date',
            'schedule_file' => 'required|file|mimes:png,jpg,jpeg|max:10240', // 10MB max, only image formats
        ]);

        try {
            // Check if user is logged in and is a doctor
            $userRole = session('user_role');
            if ($userRole !== 'doctor') {
                return redirect()->route('home')->with('error', 'Only doctors can add schedules.');
            }

            // Get DoctorID directly from session (for doctors, user_id = DoctorID)
            $doctorId = session('user_id');
            if (!$doctorId) {
                return back()->with('error', 'User not authenticated.');
            }
            
            // Validate that doctorId is not empty, null, or 0
            if (empty($doctorId) || $doctorId == '0' || $doctorId == 0 || is_null($doctorId)) {
                return back()->with('error', 'Invalid doctor ID. Please contact administrator.');
            }

            // Verify doctor exists with this DoctorID
            $doctor = Doctor::where('DoctorID', $doctorId)->first();
            if (!$doctor) {
                $errorMsg = 'Doctor profile not found. ';
                $errorMsg .= 'Your login ID (DoctorID): ' . $doctorId . ' does not exist in the doctor table. ';
                $errorMsg .= 'Please contact administrator to fix your doctor profile.';
                return back()->with('error', $errorMsg);
            }

            // Handle file upload
            if ($request->hasFile('schedule_file')) {
                $file = $request->file('schedule_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('schedules', $fileName, 'public');
                
                // Generate ScheduleID
                $maxScheduleID = DB::table('schedule')
                    ->whereRaw("ScheduleID REGEXP '^SCH[0-9]+$'")
                    ->selectRaw("CAST(SUBSTRING(ScheduleID, 4) AS UNSIGNED) as num")
                    ->orderBy('num', 'desc')
                    ->value('num');
                
                $nextNumber = ($maxScheduleID ?? 0) + 1;
                $scheduleID = 'SCH' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

                // Create schedule record
                Schedule::create([
                    'ScheduleID' => $scheduleID,
                    'DoctorID' => $doctorId,
                    'UploadDate' => $request->upload_date,
                    'FileName' => $fileName,
                ]);

                return redirect()->route('schedule.index')
                    ->with('success', 'Schedule uploaded successfully!');
            }

            return back()->with('error', 'No file uploaded.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error uploading schedule: ' . $e->getMessage());
        }
    }

    /**
     * Display list of schedules (for doctors, shows only their own schedules)
     */
    public function index()
    {
        try {
            $userRole = session('user_role');
            $doctorId = session('user_id');
            
            // For doctors, show only their own schedules
            if ($userRole === 'doctor' && $doctorId) {
                // Query schedules for this doctor - handle both string and exact match
                $schedules = DB::table('schedule')
                    ->where('DoctorID', $doctorId)
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                // If no results with direct match, try string comparison
                if ($schedules->isEmpty()) {
                    $schedules = DB::table('schedule')
                        ->whereRaw('CAST(DoctorID AS CHAR) = ?', [(string)$doctorId])
                        ->orderBy('created_at', 'desc')
                        ->get();
                }
                
            } else {
                // For other roles (admin), show all schedules
                $schedules = DB::table('schedule')
                    ->leftJoin('doctor', 'schedule.DoctorID', '=', 'doctor.DoctorID')
                    ->select(
                        'schedule.*',
                        'doctor.FullName as DoctorName',
                        'doctor.DoctorID as DoctorTableID'
                    )
                    ->orderBy('schedule.created_at', 'desc')
                    ->get();
            }
            
            return view('manageSchedule.ListSchedule', compact('schedules'));
        } catch (\Exception $e) {
            \Log::error('Error in schedule index: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return view('manageSchedule.ListSchedule', ['schedules' => collect([])])
                ->with('error', 'Error loading schedules: ' . $e->getMessage());
        }
    }

    /**
     * Display specific schedule
     */
    public function show($scheduleId)
    {
        try {
            $schedule = DB::table('schedule')
                ->leftJoin('doctor', 'schedule.DoctorID', '=', 'doctor.DoctorID')
                ->where('schedule.ScheduleID', $scheduleId)
                ->select(
                    'schedule.*',
                    'doctor.FullName as DoctorName'
                )
                ->first();

            if (!$schedule) {
                abort(404, 'Schedule not found');
            }

            return view('manageSchedule.ViewSchedule', compact('schedule'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading schedule: ' . $e->getMessage());
        }
    }

    /**
     * Display form to update schedule
     */
    public function edit($scheduleId)
    {
        try {
            // Check if user is a doctor
            $userRole = session('user_role');
            if ($userRole !== 'doctor') {
                return back()->with('error', 'Access denied. Only doctors can edit schedules.');
            }

            \Log::info('Editing schedule', ['scheduleId' => $scheduleId, 'type' => gettype($scheduleId)]);

            // Get doctor ID first
            $doctorId = session('user_id');
            
            // Use DB facade to ensure we get the schedule correctly - explicitly select all fields
            // Also filter by doctor to ensure they can only see their own
            $schedule = DB::table('schedule')
                ->select('ScheduleID', 'DoctorID', 'UploadDate', 'FileName', 'created_at', 'updated_at')
                ->where('ScheduleID', $scheduleId)
                ->where('DoctorID', $doctorId)
                ->first();
            
            if (!$schedule) {
                // Try with string comparison in case of type mismatch
                $schedule = DB::table('schedule')
                    ->select('ScheduleID', 'DoctorID', 'UploadDate', 'FileName', 'created_at', 'updated_at')
                    ->whereRaw('CAST(ScheduleID AS CHAR) = ?', [(string)$scheduleId])
                    ->whereRaw('CAST(DoctorID AS CHAR) = ?', [(string)$doctorId])
                    ->first();
            }
            
            // Debug: Log what we found
            if ($schedule) {
                \Log::info('Schedule retrieved', [
                    'ScheduleID' => $schedule->ScheduleID,
                    'ScheduleID_type' => gettype($schedule->ScheduleID),
                    'DoctorID' => $schedule->DoctorID,
                    'FileName' => $schedule->FileName
                ]);
            }
            
            if (!$schedule) {
                \Log::error('Schedule not found', [
                    'scheduleId' => $scheduleId,
                    'doctorId' => $doctorId,
                    'scheduleId_type' => gettype($scheduleId),
                    'doctorId_type' => gettype($doctorId)
                ]);
                // Debug: Check what schedules exist for this doctor
                $doctorSchedules = DB::table('schedule')
                    ->where('DoctorID', $doctorId)
                    ->orWhereRaw('CAST(DoctorID AS CHAR) = ?', [(string)$doctorId])
                    ->select('ScheduleID', 'DoctorID')
                    ->get();
                \Log::info('Schedules for doctor', [
                    'doctorId' => $doctorId,
                    'count' => $doctorSchedules->count(),
                    'schedules' => $doctorSchedules->toArray()
                ]);
                return back()->with('error', 'Schedule not found. Please check the schedule ID and try again.');
            }

            \Log::info('Schedule found', ['scheduleId' => $schedule->ScheduleID, 'doctorId' => $schedule->DoctorID]);

            // Already filtered by doctor ID in query above, so this check is redundant but kept for safety
            $scheduleDoctorId = $schedule->DoctorID;
            
            // Handle type comparison
            if ($scheduleDoctorId != $doctorId && (string)$scheduleDoctorId !== (string)$doctorId) {
                \Log::warning('Doctor trying to edit another doctor\'s schedule', [
                    'loggedInDoctorId' => $doctorId,
                    'scheduleDoctorId' => $scheduleDoctorId
                ]);
                return back()->with('error', 'Access denied. You can only edit your own schedules.');
            }

            return view('manageSchedule.UpdateSchedule', compact('schedule'));
        } catch (\Exception $e) {
            \Log::error('Error in schedule edit: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Error loading schedule: ' . $e->getMessage());
        }
    }

    /**
     * Update schedule
     */
    public function update(Request $request, $scheduleId)
    {
        // Check if user is a doctor
        $userRole = session('user_role');
        if ($userRole !== 'doctor') {
            return back()->with('error', 'Access denied. Only doctors can update schedules.');
        }

        $request->validate([
            'upload_date' => 'required|date',
            'schedule_file' => 'nullable|file|mimes:png,jpg,jpeg|max:10240',
        ]);

        try {
            $schedule = Schedule::where('ScheduleID', $scheduleId)->first();
            
            if (!$schedule) {
                abort(404, 'Schedule not found');
            }

            // Ensure doctor can only update their own schedules
            $doctorId = session('user_id');
            if ($schedule->DoctorID !== $doctorId) {
                return back()->with('error', 'Access denied. You can only update your own schedules.');
            }

            $updateData = [
                'UploadDate' => $request->upload_date,
            ];

            // Handle new file upload if provided
            if ($request->hasFile('schedule_file')) {
                // Delete old file
                if ($schedule->FileName && Storage::disk('public')->exists('schedules/' . $schedule->FileName)) {
                    Storage::disk('public')->delete('schedules/' . $schedule->FileName);
                }

                // Upload new file
                $file = $request->file('schedule_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('schedules', $fileName, 'public');
                
                $updateData['FileName'] = $fileName;
            }

            $schedule->update($updateData);

            return redirect()->route('schedule.index')
                ->with('success', 'Schedule updated successfully!');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating schedule: ' . $e->getMessage());
        }
    }

    /**
     * Delete schedule
     */
    public function destroy($scheduleId)
    {
        try {
            $schedule = Schedule::where('ScheduleID', $scheduleId)->first();
            
            if (!$schedule) {
                abort(404, 'Schedule not found');
            }

            // Delete file from storage
            if ($schedule->FileName && Storage::disk('public')->exists('schedules/' . $schedule->FileName)) {
                Storage::disk('public')->delete('schedules/' . $schedule->FileName);
            }

            $schedule->delete();

            return redirect()->route('schedule.index')
                ->with('success', 'Schedule deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting schedule: ' . $e->getMessage());
        }
    }

    /**
     * Download schedule file
     */
    public function download($scheduleId)
    {
        try {
            $schedule = Schedule::where('ScheduleID', $scheduleId)->first();
            
            if (!$schedule) {
                abort(404, 'Schedule not found');
            }

            $filePath = storage_path('app/public/schedules/' . $schedule->FileName);

            if (!file_exists($filePath)) {
                abort(404, 'Schedule file not found');
            }

            return response()->download($filePath, $schedule->FileName);
        } catch (\Exception $e) {
            return back()->with('error', 'Error downloading schedule: ' . $e->getMessage());
        }
    }

    /**
     * Get schedules for a specific doctor (AJAX endpoint)
     */
    public function getDoctorSchedules($doctorId)
    {
        try {
            // Debug: Log the doctorId being searched
            \Log::info('Fetching schedules for DoctorID: ' . $doctorId);
            
            // Verify doctor exists first
            $doctor = DB::table('doctor')->where('DoctorID', $doctorId)->first();
            if (!$doctor) {
                \Log::warning('Doctor not found with DoctorID: ' . $doctorId);
                return response()->json([
                    'success' => false,
                    'message' => 'Doctor not found with ID: ' . $doctorId,
                    'schedules' => [],
                    'count' => 0
                ]);
            }
            
            // Get all schedules for this doctor, ordered by most recently created first
            // This ensures the latest/updated schedule is shown first
            $schedules = DB::table('schedule')
                ->where('DoctorID', $doctorId)
                ->select('ScheduleID', 'UploadDate', 'FileName', 'created_at')
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Debug: Log what we found
            \Log::info('Found ' . $schedules->count() . ' schedules for DoctorID: ' . $doctorId . ' (Doctor: ' . $doctor->FullName . ')');
            if ($schedules->count() > 0) {
                \Log::info('Schedule IDs: ' . $schedules->pluck('ScheduleID')->implode(', '));
            } else {
                // Check what schedules exist in the database for debugging
                $allSchedules = DB::table('schedule')
                    ->select('ScheduleID', 'DoctorID', 'UploadDate')
                    ->get();
                \Log::info('Total schedules in database: ' . $allSchedules->count());
                \Log::info('All DoctorIDs with schedules: ' . $allSchedules->pluck('DoctorID')->unique()->implode(', '));
            }

            return response()->json([
                'success' => true,
                'schedules' => $schedules,
                'count' => $schedules->count(),
                'doctorName' => $doctor->FullName ?? 'Unknown',
                'doctorId' => $doctorId
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching schedules: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching schedules: ' . $e->getMessage(),
                'schedules' => [],
                'count' => 0
            ], 500);
        }
    }

    /**
     * Link doctor to user account
     */
    public function linkDoctor(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctor,DoctorID',
        ]);

        try {
            $userId = session('user_id');
            if (!$userId) {
                return back()->with('error', 'User not authenticated.');
            }

            $doctor = Doctor::where('DoctorID', $request->doctor_id)->first();
            if (!$doctor) {
                return back()->with('error', 'Doctor not found.');
            }

            // Check if doctor is already linked to another user
            if ($doctor->UserID && $doctor->UserID != $userId) {
                return back()->with('error', 'This doctor is already linked to another user account.');
            }

            // Link the doctor to current user
            $doctor->UserID = $userId;
            $doctor->save();

            return redirect()->route('schedule.add')
                ->with('success', 'Doctor account linked successfully! You can now add schedules.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error linking doctor: ' . $e->getMessage());
        }
    }
}

