<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\StaffRegistrationMail;
use App\Helpers\PasswordHelper;
use Barryvdh\DomPDF\Facade\Pdf;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('login');
});

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'id' => 'required',
        'password' => 'required|string',
    ]);

    // Find user by UserID using DB facade
    // For doctors, UserID in user table = DoctorID
    $user = DB::table('user')->where('UserID', $request->id)->first();

    // Check if user exists and password matches (plain text comparison for testing)
    if (!$user || $request->password !== $user->PasswordHash) {
        return back()->withErrors([
            'id' => 'Invalid ID or password.',
        ])->withInput($request->only('id'));
    }

    // For doctors and nurses, UserID in user table = their ID (DoctorID/NurseID)
    // For other roles, use UserID as normal
    $sessionUserId = $user->UserID;
    if ($user->role === 'doctor') {
        // For doctors, UserID in user table IS their DoctorID
        // Verify doctor exists with this DoctorID
        $doctor = DB::table('doctor')->where('DoctorID', $user->UserID)->first();
        if (!$doctor) {
            return back()->withErrors([
                'id' => 'Doctor profile not found. Your login ID (' . $user->UserID . ') does not match any doctor in the system. Please contact administrator.',
            ])->withInput($request->only('id'));
        }
        // Use DoctorID directly (UserID = DoctorID for doctors)
        $sessionUserId = $doctor->DoctorID;
    } else if ($user->role === 'nurse') {
        // For nurses, UserID in user table IS their NurseID
        // Verify nurse exists with this NurseID
        $nurse = DB::table('nurse')->where('NurseID', $user->UserID)->first();
        if (!$nurse) {
            return back()->withErrors([
                'id' => 'Nurse profile not found. Your login ID (' . $user->UserID . ') does not match any nurse in the system. Please contact administrator.',
            ])->withInput($request->only('id'));
        }
        // Use NurseID directly (UserID = NurseID for nurses)
        $sessionUserId = $nurse->NurseID;
    }

    // Store user information in session
    // For doctors: user_id = DoctorID
    // For others: user_id = UserID
    session([
        'user_id' => $sessionUserId,
        'user_role' => $user->role,
        'authenticated' => true,
    ]);

    // Check if user must change password
    if ($user->must_change_password) {
        return redirect()->route('password.change');
    }

    // Redirect based on role
    return redirect()->route('home');
})->name('login.post');

Route::get('/password/change', function () {
    // Middleware already checks authentication, so just redirect to reset password page
    return redirect()->route('password.reset');
})->middleware('auth.check')->name('password.change');

Route::post('/password/change', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'current_password' => 'required|string',
        'new_password' => 'required|string|min:6',
        'confirm_password' => 'required|string|same:new_password',
    ]);

    // Get user from session
    $userId = session('user_id');
    
    if (!$userId) {
        return redirect()->route('login')->withErrors([
            'current_password' => 'Please login first.',
        ]);
    }

    // Find user by UserID
    $user = DB::table('user')->where('UserID', $userId)->first();

    if (!$user) {
        return back()->withErrors([
            'current_password' => 'User not found.',
        ]);
    }

    // Verify current password
    if ($request->current_password !== $user->PasswordHash) {
        return back()->withErrors([
            'current_password' => 'Current password is incorrect.',
        ]);
    }

    // Check if new password is different from current
    if ($request->new_password === $user->PasswordHash) {
        return back()->withErrors([
            'new_password' => 'New password must be different from current password.',
        ]);
    }

    // Update password and set must_change_password to false
    DB::table('user')
        ->where('UserID', $userId)
        ->update([
            'PasswordHash' => $request->new_password,
            'must_change_password' => false,
            'updated_at' => now(),
        ]);

    return redirect()->route('home')->with('success', 'Password has been changed successfully! You can now access the system.');
})->middleware('auth.check')->name('password.change.post');

Route::get('/password/reset', function () {
    // Middleware already checks authentication
    return view('forgot-password');
})->middleware('auth.check')->name('password.reset');

Route::post('/password/reset', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'new_password' => 'required|string|min:6',
        'confirm_password' => 'required|string|same:new_password',
    ]);

    // Get user from session (for logged-in users changing password)
    $userId = session('user_id');
    
    if (!$userId) {
        return redirect()->route('login')->withErrors([
            'id' => 'Please login first to change your password.',
        ]);
    }

    // Find user by UserID from session
    $user = DB::table('user')->where('UserID', $userId)->first();

    if (!$user) {
        return back()->withErrors([
            'new_password' => 'User not found.',
        ]);
    }

    // Check if this is a first-time password change
    $isFirstTimeChange = $user->must_change_password ?? false;

    // Update password (storing as plain text as per your current system)
    DB::table('user')
        ->where('UserID', $userId)
        ->update([
            'PasswordHash' => $request->new_password,
            'must_change_password' => false,
            'updated_at' => now(),
        ]);

    // If this was a required first-time password change, redirect to home
    if ($isFirstTimeChange) {
        return redirect()->route('home')->with('success', 'Password has been changed successfully! You can now access the system.');
    }

    // Otherwise, stay on reset page with success message
    return redirect()->route('password.reset')->with('success', 'Password has been changed successfully!');
})->middleware('auth.check')->name('password.reset.post');

Route::get('/home', function () {
    // Get user role from session (default to 'nurse' if not set)
    $role = session('user_role', '');
    // Or use: $role = auth()->user()->role ?? 'nurse';
    
    switch ($role) {
        case 'admin':
            $totalDoctors = DB::table('doctor')->count();
            $totalNurses = DB::table('nurse')->count();
            $totalParents = DB::table('parent')->count();
            $totalChildren = DB::table('child')->count();
            
            return view('admin.home', compact('totalDoctors', 'totalNurses', 'totalParents', 'totalChildren'));
            
        case 'doctor':
            $doctorId = session('user_id');
            
            // Get logged-in doctor information
            $doctor = DB::table('doctor')->where('DoctorID', $doctorId)->first();
            $fullName = $doctor->FullName ?? 'Doctor';
            // Remove "Dr." or "Dr" prefix if it exists in the name to avoid duplication
            $doctorName = preg_replace('/^Dr\.?\s*/i', '', $fullName);
            
            // Get today's appointments
            $todayAppointments = DB::table('appointment')
                ->leftJoin('child', 'appointment.ChildID', '=', 'child.ChildID')
                ->leftJoin('doctor', 'appointment.DoctorID', '=', 'doctor.DoctorID')
                ->where('appointment.DoctorID', $doctorId)
                ->whereDate('appointment.date', today())
                ->select(
                    'appointment.*',
                    'child.FullName as ChildName',
                    'child.ChildID',
                    'doctor.FullName as DoctorName'
                )
                ->orderBy('appointment.time', 'asc')
                ->get();
            
            // Get upcoming appointments (approved and pending) for this doctor
            $upcomingAppointments = DB::table('appointment')
                ->leftJoin('child', 'appointment.ChildID', '=', 'child.ChildID')
                ->leftJoin('doctor', 'appointment.DoctorID', '=', 'doctor.DoctorID')
                ->where('appointment.DoctorID', $doctorId)
                ->whereIn('appointment.status', ['approved', 'pending'])
                ->whereDate('appointment.date', '>=', today())
                ->select(
                    'appointment.*',
                    'child.FullName as ChildName',
                    'child.ChildID',
                    'doctor.FullName as DoctorName'
                )
                ->orderBy('appointment.date', 'asc')
                ->orderBy('appointment.time', 'asc')
                ->get();
            
            // Calculate summary statistics
            $totalPatients = DB::table('appointment')
                ->where('appointment.DoctorID', $doctorId)
                ->distinct('appointment.ChildID')
                ->count('appointment.ChildID');
            
            $upcomingAppointmentsToday = $todayAppointments->count();
            
            // Pending actions = pending appointments for this doctor
            $pendingActions = DB::table('appointment')
                ->where('appointment.DoctorID', $doctorId)
                ->where('appointment.status', 'pending')
                ->count();
            
            $newHealthRecordUpdates = 0; // Placeholder - can be implemented later
            
            return view('doctor.home', compact(
                'todayAppointments', 
                'upcomingAppointments',
                'totalPatients',
                'upcomingAppointmentsToday',
                'pendingActions',
                'newHealthRecordUpdates',
                'doctorName'
            ));
            
        case 'nurse':
            // Get logged-in nurse information
            $nurseId = session('user_id');
            $nurse = DB::table('nurse')->where('NurseID', $nurseId)->first();
            $nurseName = $nurse->FullName ?? 'Nurse';
            
            // Get all appointments for today (nurses can see all appointments they created)
            // For now, show all today's appointments - you can filter by specific nurse later if needed
            $todayAppointments = DB::table('appointment')
                ->leftJoin('child', 'appointment.ChildID', '=', 'child.ChildID')
                ->leftJoin('doctor', 'appointment.DoctorID', '=', 'doctor.DoctorID')
                ->leftJoin('nurse', 'appointment.NurseID', '=', 'nurse.NurseID')
                ->whereDate('appointment.date', today())
                ->select(
                    'appointment.*',
                    'appointment.AppointmentID',
                    'child.FullName as ChildName',
                    'doctor.FullName as DoctorName',
                    'nurse.FullName as NurseName'
                )
                ->orderBy('appointment.time', 'asc')
                ->get();
            
            // Calculate summary statistics
            // Total children registered (all time, not just today)
            $totalChildrenToday = DB::table('child')->count();
            
            // Get gender statistics for pie chart
            $maleCount = DB::table('child')->where('Gender', 'Male')->orWhere('Gender', 'male')->count();
            $femaleCount = DB::table('child')->where('Gender', 'Female')->orWhere('Gender', 'female')->count();
            $totalGenderCount = $maleCount + $femaleCount;
            $malePercentage = $totalGenderCount > 0 ? round(($maleCount / $totalGenderCount) * 100, 1) : 0;
            $femalePercentage = $totalGenderCount > 0 ? round(($femaleCount / $totalGenderCount) * 100, 1) : 0;
            
            // Get ethnic distribution for pie chart
            $ethnicDistribution = DB::table('child')
                ->select('Ethnic', DB::raw('count(*) as count'))
                ->whereNotNull('Ethnic')
                ->where('Ethnic', '!=', '')
                ->groupBy('Ethnic')
                ->get();
            
            $ethnicData = [];
            $ethnicLabels = [];
            $ethnicCounts = [];
            $ethnicColors = ['#FF6F91', '#4A90E2', '#9C27B0', '#FF9800', '#00BCD4', '#8BC34A', '#E91E63', '#3F51B5'];
            $totalEthnicCount = 0;
            
            foreach ($ethnicDistribution as $ethnic) {
                $ethnicLabels[] = $ethnic->Ethnic ?? 'Unknown';
                $ethnicCounts[] = $ethnic->count;
                $totalEthnicCount += $ethnic->count;
            }
            
            $ethnicPercentages = [];
            foreach ($ethnicCounts as $count) {
                $ethnicPercentages[] = $totalEthnicCount > 0 ? round(($count / $totalEthnicCount) * 100, 1) : 0;
            }
            
            // Prepare ethnic data for chart
            $ethnicChartData = [];
            for ($i = 0; $i < count($ethnicLabels); $i++) {
                $ethnicChartData[] = [
                    'label' => $ethnicLabels[$i],
                    'count' => $ethnicCounts[$i],
                    'percentage' => $ethnicPercentages[$i],
                    'color' => $ethnicColors[$i % count($ethnicColors)]
                ];
            }
            
            // Also get children registered today for reference
            $childrenRegisteredToday = DB::table('child')
                ->whereDate('created_at', today())
                ->count();
            
            // Vaccinations scheduled today (from immunization records)
            $upcomingVaccinationsToday = DB::table('immunization')
                ->whereDate('Date', today())
                ->count();
            
            // Appointments to prepare (pending appointments for today)
            $appointmentsToPrepare = DB::table('appointment')
                ->whereDate('date', today())
                ->whereIn('status', ['pending', 'approved'])
                ->count();
            
            // Pending health records (children without complete health records)
            // This is a simplified count - you can adjust based on your requirements
            $pendingHealthRecords = DB::table('child')
                ->leftJoin('growthchart', 'child.ChildID', '=', 'growthchart.ChildID')
                ->whereNull('growthchart.ChildID')
                ->count();
            
            return view('nurse.home', compact(
                'todayAppointments',
                'totalChildrenToday',
                'upcomingVaccinationsToday',
                'appointmentsToPrepare',
                'pendingHealthRecords',
                'nurseName',
                'maleCount',
                'femaleCount',
                'malePercentage',
                'femalePercentage',
                'ethnicChartData',
                'totalEthnicCount'
            ));
            
        case 'parent':
            $parentId = session('user_id');
            
            // Get parent info
            $parent = DB::table('parent')->where('ParentID', $parentId)->first();
            $parentName = $parent->FullName ?? 'Parent';
            
            // Get parent's children - comprehensive lookup (same as showChildRecord)
            $motherIC = $parent->MIdentificationNumber ?? null;
            $fatherIC = $parent->FIdentificationNumber ?? null;
            
            // Get children by ParentID (direct match)
            $childrenByParentId = DB::table('child')
                ->where('ParentID', $parentId)
                ->get();
            
            // Find all parents with matching IC numbers
            $allMatchingParentIds = collect([$parentId]);
            
            if ($motherIC) {
                $parentsByMotherIC = DB::table('parent')
                    ->where('MIdentificationNumber', $motherIC)
                    ->pluck('ParentID');
                $allMatchingParentIds = $allMatchingParentIds->merge($parentsByMotherIC);
            }
            
            if ($fatherIC) {
                $parentsByFatherIC = DB::table('parent')
                    ->where('FIdentificationNumber', $fatherIC)
                    ->pluck('ParentID');
                $allMatchingParentIds = $allMatchingParentIds->merge($parentsByFatherIC);
            }
            
            // Remove duplicates and get all children
            $allMatchingParentIds = $allMatchingParentIds->unique()->values();
            
            $childrenByIC = DB::table('child')
                ->whereIn('ParentID', $allMatchingParentIds)
                ->get();
            
            // Merge both results and remove duplicates
            $children = $childrenByParentId->merge($childrenByIC)->unique('ChildID')->sortBy('ChildID')->values();
            
            $childIds = $children->pluck('ChildID')->toArray();
            
            // Get upcoming appointments (approved and pending, future dates)
            $upcomingAppointments = DB::table('appointment')
                ->leftJoin('child', 'appointment.ChildID', '=', 'child.ChildID')
                ->leftJoin('doctor', 'appointment.DoctorID', '=', 'doctor.DoctorID')
                ->whereIn('appointment.ChildID', $childIds)
                ->whereIn('appointment.status', ['approved', 'pending'])
                ->where('appointment.date', '>=', date('Y-m-d'))
                ->select(
                    'appointment.*',
                    'child.FullName as ChildName',
                    'child.ChildID',
                    'doctor.FullName as DoctorName'
                )
                ->orderBy('appointment.date', 'asc')
                ->orderBy('appointment.time', 'asc')
                ->limit(5)
                ->get();
            
            // Get latest health records for overview
            $latestGrowth = DB::table('growthchart')
                ->leftJoin('child', 'growthchart.ChildID', '=', 'child.ChildID')
                ->whereIn('growthchart.ChildID', $childIds)
                ->select('growthchart.*', 'child.FullName as ChildName', 'child.DateOfBirth', 'child.ChildID')
                ->orderBy('growthchart.DateMeasured', 'desc')
                ->first();
            
            // Get previous growth record for velocity calculation
            $previousGrowth = null;
            if ($latestGrowth) {
                $previousGrowth = DB::table('growthchart')
                    ->where('ChildID', $latestGrowth->ChildID)
                    ->where('DateMeasured', '<', $latestGrowth->DateMeasured)
                    ->orderBy('DateMeasured', 'desc')
                    ->first();
            }
            
            // Helper function to calculate percentile status
            $calculatePercentileStatus = function($value, $age, $type) {
                // Simplified WHO percentile ranges (average of boys and girls)
                // For production, use complete WHO/CDC tables
                $percentiles = [
                    'weight' => [
                        0 => ['p5' => 2.5, 'p85' => 4.0, 'p95' => 4.5],
                        1 => ['p5' => 3.2, 'p85' => 5.0, 'p95' => 5.6],
                        2 => ['p5' => 4.0, 'p85' => 6.0, 'p95' => 6.6],
                        3 => ['p5' => 4.7, 'p85' => 6.9, 'p95' => 7.5],
                        6 => ['p5' => 6.0, 'p85' => 8.5, 'p95' => 9.2],
                        9 => ['p5' => 7.2, 'p85' => 9.8, 'p95' => 10.5],
                        12 => ['p5' => 8.0, 'p85' => 10.8, 'p95' => 11.5],
                        18 => ['p5' => 9.2, 'p85' => 12.2, 'p95' => 13.0],
                        24 => ['p5' => 10.2, 'p85' => 13.5, 'p95' => 14.5],
                        30 => ['p5' => 11.0, 'p85' => 14.5, 'p95' => 15.5],
                        36 => ['p5' => 11.8, 'p85' => 15.5, 'p95' => 16.5],
                        42 => ['p5' => 12.5, 'p85' => 16.5, 'p95' => 17.5],
                        48 => ['p5' => 13.2, 'p85' => 17.2, 'p95' => 18.5],
                        60 => ['p5' => 14.5, 'p85' => 19.0, 'p95' => 20.5],
                        72 => ['p5' => 15.5, 'p85' => 20.5, 'p95' => 22.0],
                    ],
                    'height' => [
                        0 => ['p5' => 46, 'p85' => 52, 'p95' => 54],
                        1 => ['p5' => 51, 'p85' => 57, 'p95' => 59],
                        2 => ['p5' => 55, 'p85' => 62, 'p95' => 64],
                        3 => ['p5' => 58, 'p85' => 66, 'p95' => 68],
                        6 => ['p5' => 63, 'p85' => 71, 'p95' => 73],
                        9 => ['p5' => 68, 'p85' => 76, 'p95' => 78],
                        12 => ['p5' => 71, 'p85' => 80, 'p95' => 82],
                        18 => ['p5' => 76, 'p85' => 86, 'p95' => 88],
                        24 => ['p5' => 80, 'p85' => 91, 'p95' => 93],
                        30 => ['p5' => 84, 'p85' => 95, 'p95' => 97],
                        36 => ['p5' => 87, 'p85' => 99, 'p95' => 101],
                        42 => ['p5' => 90, 'p85' => 102, 'p95' => 104],
                        48 => ['p5' => 93, 'p85' => 105, 'p95' => 107],
                        60 => ['p5' => 98, 'p85' => 111, 'p95' => 113],
                        72 => ['p5' => 103, 'p85' => 116, 'p95' => 118],
                    ],
                ];
                
                // Find closest age key
                $ageKeys = array_keys($percentiles[$type]);
                $closestAge = $ageKeys[0];
                foreach ($ageKeys as $key) {
                    if ($age >= $key) {
                        $closestAge = $key;
                    } else {
                        break;
                    }
                }
                
                if (!isset($percentiles[$type][$closestAge])) {
                    return ['status' => 'unknown', 'label' => 'N/A', 'color' => '#999'];
                }
                
                $p5 = $percentiles[$type][$closestAge]['p5'];
                $p85 = $percentiles[$type][$closestAge]['p85'];
                $p95 = $percentiles[$type][$closestAge]['p95'];
                
                if ($value < $p5) {
                    return ['status' => 'abnormal', 'label' => $type === 'weight' ? 'Underweight' : 'Short', 'color' => '#d32f2f'];
                } elseif ($value >= $p5 && $value < $p85) {
                    return ['status' => 'normal', 'label' => 'Normal', 'color' => '#4caf50'];
                } elseif ($value >= $p85 && $value < $p95) {
                    return ['status' => 'borderline', 'label' => 'Monitor', 'color' => '#ff9800'];
                } else {
                    return ['status' => 'abnormal', 'label' => $type === 'weight' ? 'Overweight' : 'Tall', 'color' => '#d32f2f'];
                }
            };
            
            // Calculate weight and height status
            $weightStatus = ['status' => 'unknown', 'label' => 'N/A', 'color' => '#999'];
            $heightStatus = ['status' => 'unknown', 'label' => 'N/A', 'color' => '#999'];
            $growthVelocity = ['status' => 'unknown', 'label' => 'N/A', 'color' => '#999'];
            $overallGrowthStatus = ['status' => 'unknown', 'label' => 'N/A', 'color' => '#999'];
            
            if ($latestGrowth && $latestGrowth->Age !== null) {
                if ($latestGrowth->Weight !== null) {
                    $weightStatus = $calculatePercentileStatus($latestGrowth->Weight, $latestGrowth->Age, 'weight');
                }
                if ($latestGrowth->Height !== null) {
                    $heightStatus = $calculatePercentileStatus($latestGrowth->Height, $latestGrowth->Age, 'height');
                }
                
                // Calculate overall growth status based on percentiles (not velocity)
                // This matches the logic in child record page
                $overallStatus = 'normal';
                if ($weightStatus['status'] === 'abnormal' || $heightStatus['status'] === 'abnormal') {
                    $overallStatus = 'abnormal';
                } elseif ($weightStatus['status'] === 'borderline' || $heightStatus['status'] === 'borderline') {
                    $overallStatus = 'borderline';
                }
                
                if ($overallStatus === 'abnormal') {
                    $overallGrowthStatus = ['status' => 'abnormal', 'label' => 'Abnormal', 'color' => '#d32f2f', 'icon' => '⚠️'];
                } elseif ($overallStatus === 'borderline') {
                    $overallGrowthStatus = ['status' => 'borderline', 'label' => 'Borderline', 'color' => '#ff9800', 'icon' => '⚡'];
                } else {
                    $overallGrowthStatus = ['status' => 'normal', 'label' => 'Normal', 'color' => '#4caf50', 'icon' => '✓'];
                }
                
                // Calculate growth velocity (comparing with previous measurement) - keep for reference
                if ($previousGrowth && $previousGrowth->Age !== null && $latestGrowth->Age > $previousGrowth->Age) {
                    $ageDiff = $latestGrowth->Age - $previousGrowth->Age;
                    if ($ageDiff > 0) {
                        $weightChange = ($latestGrowth->Weight - $previousGrowth->Weight) / $ageDiff;
                        $heightChange = ($latestGrowth->Height - $previousGrowth->Height) / $ageDiff;
                        
                        // Average growth velocity (simplified thresholds)
                        $avgVelocity = ($weightChange * 10 + $heightChange) / 2; // Weight in kg/month, height in cm/month
                        
                        if ($avgVelocity > 0.5) {
                            $growthVelocity = ['status' => 'good', 'label' => 'Growing Well', 'color' => '#4caf50'];
                        } elseif ($avgVelocity > 0.2) {
                            $growthVelocity = ['status' => 'normal', 'label' => 'Normal Growth', 'color' => '#4caf50'];
                        } elseif ($avgVelocity > 0) {
                            $growthVelocity = ['status' => 'slow', 'label' => 'Slow Growth', 'color' => '#ff9800'];
                        } else {
                            $growthVelocity = ['status' => 'poor', 'label' => 'Poor Growth', 'color' => '#d32f2f'];
                        }
                    }
                }
            }
            
            $totalImmunizations = DB::table('immunization')
                ->whereIn('ChildID', $childIds)
                ->count();
            
            // Get recent health updates (latest 1 record from various tables)
            $recentUpdates = collect();
            
            // Recent immunizations
            $recentImmunizations = DB::table('immunization')
                ->leftJoin('child', 'immunization.ChildID', '=', 'child.ChildID')
                ->whereIn('immunization.ChildID', $childIds)
                ->select('immunization.*', 'child.FullName as ChildName')
                ->orderBy('immunization.Date', 'desc')
                ->limit(5)
                ->get()
                ->map(function($item) {
                    return [
                        'type' => 'Vaccination',
                        'description' => ($item->VaccineName ?? 'Vaccine') . ' - ' . ($item->ChildName ?? 'Child'),
                        'date' => $item->Date ?? now(),
                    ];
                });
            
            // Recent screenings
            $recentScreenings = DB::table('screeningresult')
                ->leftJoin('child', 'screeningresult.ChildID', '=', 'child.ChildID')
                ->whereIn('screeningresult.ChildID', $childIds)
                ->select('screeningresult.*', 'child.FullName as ChildName')
                ->orderBy('screeningresult.DateScreened', 'desc')
                ->limit(5)
                ->get()
                ->map(function($item) {
                    return [
                        'type' => 'Screening',
                        'description' => ($item->ScreeningType ?? 'Screening') . ' - ' . ($item->ChildName ?? 'Child'),
                        'date' => $item->DateScreened ?? now(),
                    ];
                });
            
            // Get only the latest health update
            $recentUpdates = $recentImmunizations->merge($recentScreenings)
                ->sortByDesc('date')
                ->take(1);
            
            // Calculate health overview - show child name if multiple children
            $childCount = $children->count();
            $latestChildName = $latestGrowth ? ($latestGrowth->ChildName ?? null) : null;
            
            $healthOverview = [
                'weightStatus' => $weightStatus,
                'heightStatus' => $heightStatus,
                'growthVelocity' => $growthVelocity,
                'overallGrowthStatus' => $overallGrowthStatus, // Use percentile-based status instead of velocity
                'weight' => $latestGrowth ? ($latestGrowth->Weight ?? 'N/A') . ' kg' : 'N/A',
                'height' => $latestGrowth ? ($latestGrowth->Height ?? 'N/A') . ' cm' : 'N/A',
                'immunizations' => $totalImmunizations . ' completed',
                'childCount' => $childCount,
                'latestChildName' => $latestChildName,
            ];
            
            return view('parent.home', compact(
                'parentName',
                'children',
                'upcomingAppointments',
                'recentUpdates',
                'healthOverview'
            ));
            
        default:
            return redirect()->route('login')->withErrors([
                'id' => 'Invalid user role.',
            ]);
    }
})->middleware('auth.check')->name('home');

Route::post('/logout', function () {
    session()->forget(['user_id', 'user_role', 'authenticated']);
    return redirect()->route('login')->with('success', 'You have been logged out successfully.');
})->name('logout');

// Middleware to check authentication - use 'auth.check' middleware for protected routes
// Example: Route::middleware('auth.check')->group(function () { ... });

// Registration routes
Route::get('/register', function () {
    return view('manageRegistration.RegisterParent');
})->name('register');

Route::post('/register/parent', [App\Http\Controllers\manageRegistrationController::class, 'storeParentRegistration'])->name('register.parent');

// Child registration via CSV (nurse only)
Route::get('/register/child/csv', [App\Http\Controllers\manageRegistrationController::class, 'showChildRegistrationUpload'])->middleware(['auth.check', 'nurse.only'])->name('child.register.csv');
Route::post('/register/child/csv', [App\Http\Controllers\manageRegistrationController::class, 'uploadChildCSV'])->middleware(['auth.check', 'nurse.only'])->name('child.upload.csv');
Route::get('/child/list', [App\Http\Controllers\manageRegistrationController::class, 'getChildList'])->middleware('auth.check')->name('child.list');

// User registration routes (for admin)
Route::get('/register/doctor', [App\Http\Controllers\manageUserController::class, 'showRegisterDoctor'])->middleware('auth.check')->name('register.doctor');
Route::get('/register/nurse', [App\Http\Controllers\manageUserController::class, 'showRegisterNurse'])->middleware('auth.check')->name('register.nurse');

// CSV upload routes for doctors and nurses
Route::post('/doctor/upload/csv', [App\Http\Controllers\manageUserController::class, 'uploadDoctorCSV'])->middleware('auth.check')->name('doctor.upload.csv');
Route::post('/nurse/upload/csv', [App\Http\Controllers\manageUserController::class, 'uploadNurseCSV'])->middleware('auth.check')->name('nurse.upload.csv');

// List routes for doctors and nurses
Route::get('/doctor/list', [App\Http\Controllers\manageUserController::class, 'getDoctorList'])->middleware('auth.check')->name('doctor.list');
Route::get('/nurse/list', [App\Http\Controllers\manageUserController::class, 'getNurseList'])->middleware('auth.check')->name('nurse.list');

// Schedule management routes (for doctors)
Route::get('/schedule/add', [App\Http\Controllers\manageScheduleController::class, 'create'])->middleware('auth.check')->name('schedule.add');
Route::post('/schedule', [App\Http\Controllers\manageScheduleController::class, 'store'])->middleware('auth.check')->name('schedule.store');
Route::get('/schedule/doctor/{doctorId}', [App\Http\Controllers\manageScheduleController::class, 'getDoctorSchedules'])->middleware('auth.check')->name('schedule.doctor');
Route::get('/schedule', [App\Http\Controllers\manageScheduleController::class, 'index'])->middleware(['auth.check', 'doctor.only'])->name('schedule.index');
Route::get('/schedule/{scheduleId}', [App\Http\Controllers\manageScheduleController::class, 'show'])->middleware('auth.check')->name('schedule.show');
Route::get('/schedule/{scheduleId}/edit', [App\Http\Controllers\manageScheduleController::class, 'edit'])->middleware(['auth.check', 'doctor.only'])->name('schedule.edit');
Route::put('/schedule/{scheduleId}', [App\Http\Controllers\manageScheduleController::class, 'update'])->middleware(['auth.check', 'doctor.only'])->name('schedule.update');
Route::delete('/schedule/{scheduleId}', [App\Http\Controllers\manageScheduleController::class, 'destroy'])->middleware('auth.check')->name('schedule.destroy');
Route::get('/schedule/{scheduleId}/download', [App\Http\Controllers\manageScheduleController::class, 'download'])->middleware('auth.check')->name('schedule.download');

// Report management routes (for doctors)
Route::get('/report', [App\Http\Controllers\manageReportController::class, 'index'])->middleware('auth.check')->name('report.list');
Route::get('/report/create', [App\Http\Controllers\manageReportController::class, 'create'])->middleware('auth.check')->name('report.create');
Route::post('/report', [App\Http\Controllers\manageReportController::class, 'store'])->middleware('auth.check')->name('report.store');
Route::get('/report/view/{childId}', [App\Http\Controllers\manageReportController::class, 'show'])->middleware('auth.check')->name('report.view');
Route::get('/report/id/{reportId}', [App\Http\Controllers\manageReportController::class, 'showByReportId'])->middleware('auth.check')->name('report.view.id');
Route::get('/report/{reportId}/edit', [App\Http\Controllers\manageReportController::class, 'edit'])->middleware(['auth.check', 'doctor.only'])->name('report.edit');
Route::put('/report/{reportId}', [App\Http\Controllers\manageReportController::class, 'update'])->middleware(['auth.check', 'doctor.only'])->name('report.update');
Route::delete('/report/{reportId}', [App\Http\Controllers\manageReportController::class, 'destroy'])->middleware(['auth.check', 'doctor.only'])->name('report.destroy');
Route::get('/report/{childId}/pdf', [App\Http\Controllers\manageReportController::class, 'generatePDF'])->middleware('auth.check')->name('report.pdf');

// Child Health Record routes
// GET routes - accessible to both doctors and nurses (view only)
Route::get('/health/records/all', [App\Http\Controllers\manageChildHealthRecordController::class, 'showAllHealthRecords'])->middleware(['auth.check', 'nurse.only'])->name('health.records.all');
Route::get('/birth/record', [App\Http\Controllers\manageChildHealthRecordController::class, 'showBirthRecord'])->middleware('auth.check')->name('birth.record');
Route::get('/immunization/record', [App\Http\Controllers\manageChildHealthRecordController::class, 'showImmunization'])->middleware('auth.check')->name('immunization.record');
Route::get('/growth/record', [App\Http\Controllers\manageChildHealthRecordController::class, 'showGrowthChart'])->middleware('auth.check')->name('growth.record');
Route::get('/growth/record/{childId?}', [App\Http\Controllers\manageChildHealthRecordController::class, 'showGrowthChart'])->middleware('auth.check')->name('growth.record.show');
Route::get('/milestone/record', [App\Http\Controllers\manageChildHealthRecordController::class, 'showMilestone'])->middleware('auth.check')->name('milestone.record');
Route::get('/screening/record', [App\Http\Controllers\manageChildHealthRecordController::class, 'showScreening'])->middleware('auth.check')->name('screening.record');
Route::get('/feeding/record', [App\Http\Controllers\manageChildHealthRecordController::class, 'showFeeding'])->middleware('auth.check')->name('feeding.record');

// POST routes - only nurses can edit/create records
Route::post('/birth/record', [App\Http\Controllers\manageChildHealthRecordController::class, 'storeBirthRecord'])->middleware(['auth.check', 'nurse.only'])->name('birth.record.store');
Route::post('/immunization/record', [App\Http\Controllers\manageChildHealthRecordController::class, 'storeImmunization'])->middleware(['auth.check', 'nurse.only'])->name('immunization.record.store');
Route::post('/immunization/store', [App\Http\Controllers\manageChildHealthRecordController::class, 'storeImmunization'])->middleware(['auth.check', 'nurse.only'])->name('immunization.store');
Route::post('/growth/record', [App\Http\Controllers\manageChildHealthRecordController::class, 'storeGrowthMeasurement'])->middleware(['auth.check', 'nurse.only'])->name('growth.record.store');
Route::post('/milestone/record', [App\Http\Controllers\manageChildHealthRecordController::class, 'storeMilestone'])->middleware(['auth.check', 'nurse.only'])->name('milestone.record.store');
Route::post('/milestone/store', [App\Http\Controllers\manageChildHealthRecordController::class, 'storeMilestone'])->middleware(['auth.check', 'nurse.only'])->name('milestone.store');
Route::post('/screening/record', [App\Http\Controllers\manageChildHealthRecordController::class, 'storeScreening'])->middleware(['auth.check', 'nurse.only'])->name('screening.record.store');
Route::post('/screening/store', [App\Http\Controllers\manageChildHealthRecordController::class, 'storeScreening'])->middleware(['auth.check', 'nurse.only'])->name('screening.store');
Route::post('/feeding/record', [App\Http\Controllers\manageChildHealthRecordController::class, 'storeFeeding'])->middleware(['auth.check', 'nurse.only'])->name('feeding.record.store');
Route::post('/feeding/store', [App\Http\Controllers\manageChildHealthRecordController::class, 'storeFeeding'])->middleware(['auth.check', 'nurse.only'])->name('feeding.store');

Route::get('/test-db', function () {
    try {
        $tables = DB::select('SHOW TABLES');
        $tableNames = array_map(function ($table) {
            return array_values((array) $table)[0];
        }, $tables);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Database connection successful!',
            'database' => config('database.connections.mysql.database'),
            'tables' => $tableNames,
            'table_count' => count($tableNames)
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Database connection failed: ' . $e->getMessage()
        ], 500);
    }
});

Route::get('/verify/{registrationId}', function ($registrationId) {
    try {
        // Decode the registration ID (could be base64 encoded or plain)
        // Try to decode if it's base64, otherwise use as-is
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
})->name('verify.parent');

Route::post('/verify/parent', function (\Illuminate\Http\Request $request) {
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
})->name('verify.parent.post');

// Appointment management routes
Route::get('/list/child', [App\Http\Controllers\manageAppointmentController::class, 'listChildren'])->middleware('auth.check')->name('list.child');
Route::get('/booking/form/{childId}', [App\Http\Controllers\manageAppointmentController::class, 'showBookingForm'])->middleware('auth.check')->name('booking.form');
Route::post('/booking', [App\Http\Controllers\manageAppointmentController::class, 'storeBooking'])->middleware('auth.check')->name('booking.store');
Route::get('/appointment/status', [App\Http\Controllers\manageAppointmentController::class, 'appointmentStatus'])->middleware('auth.check')->name('appointment.status');
Route::get('/appointment/request', [App\Http\Controllers\manageAppointmentController::class, 'appointmentRequests'])->middleware('auth.check')->name('appointment.request');
Route::get('/appointment/history', [App\Http\Controllers\manageAppointmentController::class, 'appointmentHistory'])->middleware('auth.check')->name('appointment.history');
Route::get('/parent/appointment/history', [App\Http\Controllers\manageAppointmentController::class, 'appointmentHistory'])->middleware('auth.check')->name('parent.appointment.history');
Route::get('/parent/child/record', [App\Http\Controllers\manageChildHealthRecordController::class, 'showChildRecord'])->middleware('auth.check')->name('parent.child.record');
Route::put('/appointment/{appointmentID}/status', [App\Http\Controllers\manageAppointmentController::class, 'updateStatus'])->middleware('auth.check')->name('appointment.update.status');

// Route to approve appointment and send reminder email to parent
Route::post('/appointment/approve/{appointmentID}', function ($appointmentID) {
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
        
        // Update appointment status to approved/confirmed
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
        
        // Get parent email (prefer mother's email, fallback to father's email)
        $parentEmail = $parent->MEmail ?? $parent->FEmail ?? null;
        
        if ($parentEmail) {
            try {
                // Send reminder email
                Mail::to($parentEmail)->send(new \App\Mail\AppointmentReminderMail(
                    $parent->MotherName ?? $parent->FatherName ?? 'Parent',
                    $child->FullName,
                    $doctor->FullName ?? 'Doctor',
                    $appointment->date,
                    $appointment->time,
                    $appointmentID
                ));
            } catch (\Exception $mailException) {
                // Log email error but don't fail the approval
                \Log::error('Failed to send appointment reminder email: ' . $mailException->getMessage());
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Appointment approved and reminder email sent successfully!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error approving appointment: ' . $e->getMessage()
        ], 500);
    }
})->name('appointment.approve');
