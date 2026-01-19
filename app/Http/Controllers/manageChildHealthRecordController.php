<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\GrowthChart;
use App\Models\Immunization;
use App\Models\ScreeningResult;
use App\Models\DevelopmentMilestone;
use App\Models\FeedingRecord;
use App\Models\BirthRecord;
use App\Models\Child;

class manageChildHealthRecordController extends Controller
{
    /**
     * Display all health records view for nurses
     */
    public function showAllHealthRecords(Request $request)
    {
        try {
            $userRole = session('user_role', '');
            
            if ($userRole !== 'nurse') {
                return redirect()->route('home')->with('error', 'Access denied.');
            }

            // Get all children
            $children = DB::table('child')->select('ChildID', 'FullName')->orderBy('ChildID', 'asc')->get();

            // Get selected child ID from request
            $selectedChildId = $request->input('child_id');

            // Build queries for all health records grouped by type
            $allRecords = [];

            // Growth Chart Records
            $growthQuery = DB::table('growthchart')
                ->leftJoin('child', 'growthchart.ChildID', '=', 'child.ChildID')
                ->select('growthchart.*', 'child.FullName as ChildName', 'child.ChildID');
            if ($selectedChildId) {
                $growthQuery->where('growthchart.ChildID', $selectedChildId);
            }
            $growthRecords = $growthQuery->orderBy('growthchart.created_at', 'desc')->limit(50)->get();
            
            // Calculate AI analysis for each growth record
            $whoPercentiles = $this->getWhoPercentiles();
            foreach ($growthRecords as $record) {
                $age = $record->Age ?? null;
                $weight = $record->Weight ?? null;
                $height = $record->Height ?? null;
                $headCircumference = $record->HeadCircumference ?? null;
                
                $record->aiAnalysis = $this->calculateGrowthStatus($age, $weight, $height, $headCircumference, $whoPercentiles);
            }
            $allRecords['growth'] = $growthRecords;

            // Immunization Records
            $immunizationQuery = DB::table('immunization')
                ->leftJoin('child', 'immunization.ChildID', '=', 'child.ChildID')
                ->select('immunization.*', 'child.FullName as ChildName', 'child.ChildID');
            if ($selectedChildId) {
                $immunizationQuery->where('immunization.ChildID', $selectedChildId);
            }
            $allRecords['immunization'] = $immunizationQuery->orderBy('immunization.created_at', 'desc')->limit(50)->get();

            // Screening Records
            $screeningQuery = DB::table('screeningresult')
                ->leftJoin('child', 'screeningresult.ChildID', '=', 'child.ChildID')
                ->select('screeningresult.*', 'child.FullName as ChildName', 'child.ChildID');
            if ($selectedChildId) {
                $screeningQuery->where('screeningresult.ChildID', $selectedChildId);
            }
            $allRecords['screening'] = $screeningQuery->orderBy('screeningresult.created_at', 'desc')->limit(50)->get();

            // Milestone Records
            $milestoneQuery = DB::table('developmentmilestone')
                ->leftJoin('child', 'developmentmilestone.ChildID', '=', 'child.ChildID')
                ->select('developmentmilestone.*', 'child.FullName as ChildName', 'child.ChildID');
            if ($selectedChildId) {
                $milestoneQuery->where('developmentmilestone.ChildID', $selectedChildId);
            }
            $allRecords['milestone'] = $milestoneQuery->orderBy('developmentmilestone.created_at', 'desc')->limit(50)->get();

            // Feeding Records
            $feedingQuery = DB::table('feedingrecord')
                ->leftJoin('child', 'feedingrecord.ChildID', '=', 'child.ChildID')
                ->select('feedingrecord.*', 'child.FullName as ChildName', 'child.ChildID');
            if ($selectedChildId) {
                $feedingQuery->where('feedingrecord.ChildID', $selectedChildId);
            }
            $allRecords['feeding'] = $feedingQuery->orderBy('feedingrecord.created_at', 'desc')->limit(50)->get();

            // Birth Records
            $birthQuery = DB::table('birthrecord')
                ->leftJoin('child', 'birthrecord.ChildID', '=', 'child.ChildID')
                ->select('birthrecord.*', 'child.FullName as ChildName', 'child.ChildID');
            if ($selectedChildId) {
                $birthQuery->where('birthrecord.ChildID', $selectedChildId);
            }
            $allRecords['birth'] = $birthQuery->orderBy('birthrecord.created_at', 'desc')->limit(50)->get();

            return view('manageChildHealthRecord.AllHealthRecords', compact('allRecords', 'children', 'selectedChildId'));
        } catch (\Exception $e) {
            \Log::error('Error in showAllHealthRecords: ' . $e->getMessage());
            return view('manageChildHealthRecord.AllHealthRecords', [
                'allRecords' => [
                    'growth' => collect([]),
                    'immunization' => collect([]),
                    'screening' => collect([]),
                    'milestone' => collect([]),
                    'feeding' => collect([]),
                    'birth' => collect([]),
                ],
                'children' => collect([]),
                'selectedChildId' => null
            ])->with('error', 'Error loading health records: ' . $e->getMessage());
        }
    }

    /**
     * Display growth chart page
     */
    public function showGrowthChart($childId = null)
    {
        try {
            $children = DB::table('child')->select('ChildID', 'FullName')->orderBy('ChildID', 'asc')->get();
            
            $userRole = session('user_role', '');
            $selectedChildId = request('child_id') ?? $childId; // Support both route param and query param
            
            // For both doctors and nurses: if no child is selected, don't load any data
            if (!$selectedChildId) {
                $emptyChartData = [
                    'weight' => [],
                    'height' => [],
                    'head' => [],
                ];
            return view('manageChildHealthRecord.Growthchart', [
                'weightRecords' => [],
                'heightRecords' => [],
                'headRecords' => [],
                'children' => $children,
                'childId' => null,
                'selectedChildId' => null,
            ]);
            }
            
            // Fetch growth chart data (filtered by selected child)
            $query = DB::table('growthchart')
                ->leftJoin('child', 'growthchart.ChildID', '=', 'child.ChildID')
                ->select('growthchart.*', 'child.FullName as ChildName', 'child.DateOfBirth')
                ->where('growthchart.ChildID', $selectedChildId);
            
            $growthRecords = $query->orderBy('growthchart.DateMeasured', 'asc')->get();
            
            // Format data for chart display
            $chartData = [
                'weight' => [],
                'height' => [],
                'head' => []
            ];
            
            foreach ($growthRecords as $record) {
                $dateMeasured = \Carbon\Carbon::parse($record->DateMeasured);
                
                // Use stored age if available, otherwise calculate from birth date
                $ageInMonths = null;
                if (isset($record->Age) && $record->Age !== null) {
                    // Use the stored age from the form
                    $ageInMonths = (int)$record->Age;
                } else {
                    // Fallback: Calculate age from birth date if age not stored
                    $birthDate = null;
                    if ($record->DateOfBirth) {
                        $birthDate = \Carbon\Carbon::parse($record->DateOfBirth);
                    }
                    
                    if ($birthDate) {
                        $ageInMonths = $birthDate->diffInMonths($dateMeasured);
                        if ($ageInMonths < 0 || $dateMeasured->lt($birthDate)) {
                            $ageInMonths = 0;
                        }
                    } else {
                        $ageInMonths = 0;
                    }
                }
                
                $entry = [
                    'date' => $dateMeasured->format('Y-m-d'),
                    'age' => $ageInMonths,
                    'weight' => $record->Weight,
                    'height' => $record->Height,
                    'head' => $record->HeadCircumference !== null ? (float)$record->HeadCircumference : null,
                    'childId' => $record->ChildID, // Add ChildID to each entry
                ];
                
                $chartData['weight'][] = $entry;
                $chartData['height'][] = $entry;
                $chartData['head'][] = $entry;
            }
            
            return view('manageChildHealthRecord.Growthchart', [
                'weightRecords' => $chartData['weight'],
                'heightRecords' => $chartData['height'],
                'headRecords' => $chartData['head'],
                'children' => $children,
                'childId' => $selectedChildId,
                'selectedChildId' => $selectedChildId,
            ]);
        } catch (\Exception $e) {
            $children = DB::table('child')->select('ChildID', 'FullName')->orderBy('ChildID', 'asc')->get();
            return view('manageChildHealthRecord.Growthchart', [
                'weightRecords' => [],
                'heightRecords' => [],
                'headRecords' => [],
                'children' => $children,
                'childId' => null,
                'selectedChildId' => null,
            ])->with('error', 'Error loading growth chart: ' . $e->getMessage());
        }
    }

    /**
     * Store new growth measurement
     */
    public function storeGrowthMeasurement(Request $request)
    {
        $request->validate([
            'child_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!DB::table('child')->where('ChildID', $value)->exists()) {
                        $fail('The selected child id is invalid.');
                    }
                },
            ],
            'date_measured' => 'required|date',
            'age' => 'required|integer|min:0|max:72',
            'weight' => 'required|numeric|min:0',
            'height' => 'required|numeric|min:0',
            'head_circumference' => 'nullable|numeric|min:0',
        ]);

        try {
            // Generate GrowthID
            $maxGrowthID = DB::table('growthchart')
                ->whereRaw("GrowthID REGEXP '^GROW[0-9]+$'")
                ->selectRaw("CAST(SUBSTRING(GrowthID, 5) AS UNSIGNED) as num")
                ->orderBy('num', 'desc')
                ->value('num');
            
            $nextNumber = ($maxGrowthID ?? 0) + 1;
            $growthID = 'GROW' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            GrowthChart::create([
                'GrowthID' => $growthID,
                'ChildID' => $request->child_id,
                'DateMeasured' => $request->date_measured,
                'Age' => $request->age,
                'Weight' => $request->weight,
                'Height' => $request->height,
                'HeadCircumference' => $request->head_circumference,
            ]);

            // Redirect back to growth chart with the same child selected
            $redirectUrl = route('growth.record');
            if ($request->child_id) {
                $redirectUrl .= '?child_id=' . $request->child_id;
            }
            return redirect($redirectUrl)->with('success', '✅ Growth measurement added successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error adding growth measurement: ' . $e->getMessage());
        }
    }

    /**
     * Display immunization record page
     */
    public function showImmunization()
    {
        try {
            $userRole = session('user_role', '');
            $selectedChildId = request('child_id');

            $query = DB::table('immunization')
                ->leftJoin('child', 'immunization.ChildID', '=', 'child.ChildID')
                ->select('immunization.*', 'child.FullName as ChildName');

            // For doctors and nurses, only show data for the selected child (and nothing if none selected)
            if ($userRole === 'doctor' || $userRole === 'nurse') {
                if (!empty($selectedChildId)) {
                    $query->where('immunization.ChildID', $selectedChildId);
                } else {
                    $vaccines = collect([]);
                    $children = DB::table('child')->select('ChildID', 'FullName')->orderBy('ChildID', 'asc')->get();
                    return view('manageChildHealthRecord.Immunization', compact('vaccines', 'children', 'selectedChildId'));
                }
            }

            $vaccines = $query->orderBy('immunization.Date', 'desc')->get();

            $children = DB::table('child')->select('ChildID', 'FullName')->orderBy('ChildID', 'asc')->get();

            return view('manageChildHealthRecord.Immunization', compact('vaccines', 'children', 'selectedChildId'));
        } catch (\Exception $e) {
            return view('manageChildHealthRecord.Immunization', ['vaccines' => collect([]), 'children' => collect([])])
                ->with('error', 'Error loading immunization records: ' . $e->getMessage());
        }
    }

    /**
     * Store new immunization record(s) - can handle multiple records
     */
    public function storeImmunization(Request $request)
    {
        $request->validate([
            'child_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!DB::table('child')->where('ChildID', $value)->exists()) {
                        $fail('The selected child id is invalid.');
                    }
                },
            ],
            'vaccine_name' => 'required|array',
            'vaccine_name.*' => 'required|string|max:255',
            'age' => 'required|array',
            'age.*' => 'required|integer|min:0',
            'date' => 'required|array',
            'date.*' => 'required|date',
            'dose_number' => 'nullable|array',
            'dose_number.*' => 'nullable|string|max:50',
            'given_by' => 'nullable|array',
            'given_by.*' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();
            
            $childId = trim($request->child_id);
            
            // Verify child exists
            if (!DB::table('child')->where('ChildID', $childId)->exists()) {
                return back()->withInput()->with('error', 'Invalid child ID selected. Please select a valid child.');
            }
            
            $vaccineNames = $request->vaccine_name;
            $ages = $request->age;
            $dates = $request->date;
            $doseNumbers = $request->dose_number ?? [];
            $givenBy = $request->given_by ?? [];
            
            $successCount = 0;
            
            // Get starting ImmunizationID number once
            $maxImmunizationID = DB::table('immunization')
                ->whereRaw("ImmunizationID REGEXP '^IMM[0-9]+$'")
                ->selectRaw("CAST(SUBSTRING(ImmunizationID, 4) AS UNSIGNED) as num")
                ->orderBy('num', 'desc')
                ->value('num');
            
            $baseNumber = ($maxImmunizationID ?? 0);
            
            foreach ($vaccineNames as $index => $vaccineName) {
                if (empty($vaccineName)) continue;
                
                // Generate ImmunizationID
                $nextNumber = $baseNumber + 1 + $successCount;
                $immunizationID = 'IMM' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
                
                // Use age from form input
                $age = isset($ages[$index]) ? (int)$ages[$index] : null;

                Immunization::create([
                    'ImmunizationID' => $immunizationID,
                    'ChildID' => $childId,
                    'Age' => $age,
                    'VaccineName' => $vaccineName,
                    'Date' => $dates[$index] ?? null,
                    'DoseNumber' => $doseNumbers[$index] ?? null,
                    'GivenBy' => $givenBy[$index] ?? null,
                ]);
                
                $successCount++;
            }
            
            DB::commit();
            
            $message = $successCount > 0 
                ? "✅ Successfully added {$successCount} immunization record(s)!"
                : "No records were added. Please fill in the form.";
            
            return redirect()->route('immunization.record')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error adding immunization record: ' . $e->getMessage());
        }
    }

    /**
     * Display screening record page
     */
    public function showScreening()
    {
        try {
            $userRole = session('user_role', '');
            $selectedChildId = request('child_id');

            $query = DB::table('screeningresult')
                ->leftJoin('child', 'screeningresult.ChildID', '=', 'child.ChildID')
                ->select('screeningresult.*', 'child.FullName as ChildName');

            // For doctors and nurses, only show data for the selected child (and nothing if none selected)
            if ($userRole === 'doctor' || $userRole === 'nurse') {
                if (!empty($selectedChildId)) {
                    $query->where('screeningresult.ChildID', $selectedChildId);
                } else {
                    $screenings = collect([]);
                    $children = DB::table('child')->select('ChildID', 'FullName')->orderBy('ChildID', 'asc')->get();
                    return view('manageChildHealthRecord.Screening', compact('screenings', 'children', 'selectedChildId'));
                }
            }

            $screenings = $query->orderBy('screeningresult.DateScreened', 'desc')->get();

            $children = DB::table('child')->select('ChildID', 'FullName')->orderBy('ChildID', 'asc')->get();

            return view('manageChildHealthRecord.Screening', compact('screenings', 'children', 'selectedChildId'));
        } catch (\Exception $e) {
            return view('manageChildHealthRecord.Screening', ['screenings' => collect([]), 'children' => collect([])])
                ->with('error', 'Error loading screening records: ' . $e->getMessage());
        }
    }

    /**
     * Store new screening record(s) - can handle multiple records
     */
    public function storeScreening(Request $request)
    {
        $request->validate([
            'child_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!DB::table('child')->where('ChildID', $value)->exists()) {
                        $fail('The selected child id is invalid.');
                    }
                },
            ],
            'screening_type' => 'required|array',
            'screening_type.*' => 'required|string|max:255',
            'result' => 'required|array',
            'result.*' => 'required|string|max:255',
            'date_screened' => 'required|array',
            'date_screened.*' => 'required|date',
        ]);

        try {
            DB::beginTransaction();
            
            $childId = trim($request->child_id);
            
            // Verify child exists
            if (!DB::table('child')->where('ChildID', $childId)->exists()) {
                return back()->withInput()->with('error', 'Invalid child ID selected. Please select a valid child.');
            }
            
            $screeningTypes = $request->screening_type;
            $results = $request->result;
            $datesScreened = $request->date_screened;
            
            $successCount = 0;
            
            // Get starting ScreeningID number once
            $maxScreeningID = DB::table('screeningresult')
                ->whereRaw("ScreeningID REGEXP '^SCR[0-9]+$'")
                ->selectRaw("CAST(SUBSTRING(ScreeningID, 4) AS UNSIGNED) as num")
                ->orderBy('num', 'desc')
                ->value('num');
            
            $baseNumber = ($maxScreeningID ?? 0);
            
            foreach ($screeningTypes as $index => $screeningType) {
                if (empty($screeningType)) continue;
                
                // Generate ScreeningID
                $nextNumber = $baseNumber + 1 + $successCount;
                $screeningID = 'SCR' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

                ScreeningResult::create([
                    'ScreeningID' => $screeningID,
                    'ChildID' => $childId,
                    'ScreeningType' => $screeningType,
                    'Result' => $results[$index] ?? null,
                    'DateScreened' => $datesScreened[$index] ?? null,
                ]);
                
                $successCount++;
            }
            
            DB::commit();
            
            $message = $successCount > 0 
                ? "✅ Successfully added {$successCount} screening record(s)!"
                : "No records were added. Please fill in the form.";
            
            return redirect()->route('screening.record')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error adding screening record: ' . $e->getMessage());
        }
    }

    /**
     * Display milestone record page
     */
    public function showMilestone()
    {
        try {
            $userRole = session('user_role', '');
            $selectedChildId = request('child_id');

            $query = DB::table('developmentmilestone')
                ->leftJoin('child', 'developmentmilestone.ChildID', '=', 'child.ChildID')
                ->select('developmentmilestone.*', 'child.FullName as ChildName');

            // For doctors and nurses, only show data for the selected child (and nothing if none selected)
            if ($userRole === 'doctor' || $userRole === 'nurse') {
                if (!empty($selectedChildId)) {
                    $query->where('developmentmilestone.ChildID', $selectedChildId);
                } else {
                    $milestones = collect([]);
                    $children = DB::table('child')->select('ChildID', 'FullName')->orderBy('ChildID', 'asc')->get();
                    return view('manageChildHealthRecord.Milestone', compact('milestones', 'children', 'selectedChildId'));
                }
            }

            $milestones = $query->orderBy('developmentmilestone.created_at', 'desc')->get();

            $children = DB::table('child')->select('ChildID', 'FullName')->orderBy('ChildID', 'asc')->get();

            return view('manageChildHealthRecord.Milestone', compact('milestones', 'children', 'selectedChildId'));
        } catch (\Exception $e) {
            return view('manageChildHealthRecord.Milestone', ['milestones' => collect([]), 'children' => collect([])])
                ->with('error', 'Error loading milestone records: ' . $e->getMessage());
        }
    }

    /**
     * Store new milestone record(s) - can handle multiple records
     */
    public function storeMilestone(Request $request)
    {
        $request->validate([
            'child_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!DB::table('child')->where('ChildID', $value)->exists()) {
                        $fail('The selected child id is invalid.');
                    }
                },
            ],
            'milestone_type' => 'required|array',
            'milestone_type.*' => 'required|string|max:255',
            'notes' => 'nullable|array',
            'notes.*' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();
            
            $childId = trim($request->child_id);
            
            // Verify child exists
            if (!DB::table('child')->where('ChildID', $childId)->exists()) {
                return back()->withInput()->with('error', 'Invalid child ID selected. Please select a valid child.');
            }
            
            $milestoneTypes = $request->milestone_type;
            $notes = $request->notes ?? [];
            
            $successCount = 0;
            
            // Get starting MilestoneID number once
            $maxMilestoneID = DB::table('developmentmilestone')
                ->whereRaw("MilestoneID REGEXP '^MIL[0-9]+$'")
                ->selectRaw("CAST(SUBSTRING(MilestoneID, 4) AS UNSIGNED) as num")
                ->orderBy('num', 'desc')
                ->value('num');
            
            $baseNumber = ($maxMilestoneID ?? 0);
            
            foreach ($milestoneTypes as $index => $milestoneType) {
                if (empty($milestoneType)) continue;
                
                // Generate MilestoneID
                $nextNumber = $baseNumber + 1 + $successCount;
                $milestoneID = 'MIL' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

                DevelopmentMilestone::create([
                    'MilestoneID' => $milestoneID,
                    'ChildID' => $childId,
                    'MilestoneType' => $milestoneType,
                    'Notes' => $notes[$index] ?? null,
                ]);
                
                $successCount++;
            }
            
            DB::commit();
            
            $message = $successCount > 0 
                ? "✅ Successfully added {$successCount} milestone record(s)!"
                : "No records were added. Please fill in the form.";
            
            return redirect()->route('milestone.record')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error adding milestone record: ' . $e->getMessage());
        }
    }

    /**
     * Display feeding record page
     */
    public function showFeeding()
    {
        try {
            $userRole = session('user_role', '');
            $selectedChildId = request('child_id');

            $query = DB::table('feedingrecord')
                ->leftJoin('child', 'feedingrecord.ChildID', '=', 'child.ChildID')
                ->select('feedingrecord.*', 'child.FullName as ChildName');

            // For doctors and nurses, only show data for the selected child (and nothing if none selected)
            if ($userRole === 'doctor' || $userRole === 'nurse') {
                if (!empty($selectedChildId)) {
                    $query->where('feedingrecord.ChildID', $selectedChildId);
                } else {
                    $feedings = collect([]);
                    $children = DB::table('child')->select('ChildID', 'FullName')->orderBy('ChildID', 'asc')->get();
                    return view('manageChildHealthRecord.Feeding', compact('feedings', 'children', 'selectedChildId'));
                }
            }

            $feedings = $query->orderBy('feedingrecord.DateLogged', 'desc')->get();

            $children = DB::table('child')->select('ChildID', 'FullName')->orderBy('ChildID', 'asc')->get();

            return view('manageChildHealthRecord.Feeding', compact('feedings', 'children', 'selectedChildId'));
        } catch (\Exception $e) {
            return view('manageChildHealthRecord.Feeding', ['feedings' => collect([]), 'children' => collect([])])
                ->with('error', 'Error loading feeding records: ' . $e->getMessage());
        }
    }

    /**
     * Store new feeding record(s) - can handle multiple records
     */
    public function storeFeeding(Request $request)
    {
        $request->validate([
            'child_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!DB::table('child')->where('ChildID', $value)->exists()) {
                        $fail('The selected child id is invalid.');
                    }
                },
            ],
            'feeding_type' => 'required|array',
            'feeding_type.*' => 'required|string|max:255',
            'frequency_per_day' => 'required|array',
            'frequency_per_day.*' => 'required|numeric|min:0.5',
            'date_logged' => 'required|array',
            'date_logged.*' => 'required|date',
            'remarks' => 'nullable|array',
            'remarks.*' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();
            
            $childId = trim($request->child_id);
            
            // Verify child exists
            if (!DB::table('child')->where('ChildID', $childId)->exists()) {
                return back()->withInput()->with('error', 'Invalid child ID selected. Please select a valid child.');
            }
            
            $feedingTypes = $request->feeding_type;
            $frequencies = $request->frequency_per_day;
            $datesLogged = $request->date_logged;
            $remarks = $request->remarks ?? [];
            
            $successCount = 0;
            
            // Get starting FeedingID number once
            $maxFeedingID = DB::table('feedingrecord')
                ->whereRaw("FeedingID REGEXP '^FED[0-9]+$'")
                ->selectRaw("CAST(SUBSTRING(FeedingID, 4) AS UNSIGNED) as num")
                ->orderBy('num', 'desc')
                ->value('num');
            
            $baseNumber = ($maxFeedingID ?? 0);
            
            foreach ($feedingTypes as $index => $feedingType) {
                if (empty($feedingType)) continue;
                
                // Generate FeedingID
                $nextNumber = $baseNumber + 1 + $successCount;
                $feedingID = 'FED' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

                FeedingRecord::create([
                    'FeedingID' => $feedingID,
                    'ChildID' => $childId,
                    'FeedingType' => $feedingType,
                    'FrequencyPerDay' => $frequencies[$index] ?? null,
                    'DateLogged' => $datesLogged[$index] ?? null,
                    'Remarks' => $remarks[$index] ?? null,
                ]);
                
                $successCount++;
            }
            
            DB::commit();
            
            $message = $successCount > 0 
                ? "✅ Successfully added {$successCount} feeding record(s)!"
                : "No records were added. Please fill in the form.";
            
            return redirect()->route('feeding.record')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error adding feeding record: ' . $e->getMessage());
        }
    }

    /**
     * Display birth record page
     */
    public function showBirthRecord()
    {
        try {
            // Get all registered children from database using DB facade (returns stdClass objects)
            $children = DB::table('child')
                ->select('ChildID', 'FullName')
                ->orderBy('ChildID', 'asc')
                ->get();
            
            $userRole = session('user_role', '');
            $selectedChildId = request('child_id');

            $query = DB::table('birthrecord')
                ->leftJoin('child', 'birthrecord.ChildID', '=', 'child.ChildID')
                ->select('birthrecord.*', 'child.FullName as ChildName');

            // For doctors and nurses, only show data for the selected child (and nothing if none selected)
            if ($userRole === 'doctor' || $userRole === 'nurse') {
                if (!empty($selectedChildId)) {
                    $query->where('birthrecord.ChildID', $selectedChildId);
                } else {
                    $birthRecords = collect([]);
                    return view('manageChildHealthRecord.BirthRecord', compact('birthRecords', 'children', 'selectedChildId'));
                }
            }

            $birthRecords = $query->orderBy('birthrecord.created_at', 'desc')->get();

            return view('manageChildHealthRecord.BirthRecord', compact('birthRecords', 'children', 'selectedChildId'));
        } catch (\Exception $e) {
            return view('manageChildHealthRecord.BirthRecord', ['birthRecords' => collect([]), 'children' => collect([])])
                ->with('error', 'Error loading birth records: ' . $e->getMessage());
        }
    }

    /**
     * Store new birth record
     */
    public function storeBirthRecord(Request $request)
    {
        $request->validate([
            'child_id' => 'required|exists:child,ChildID',
            'time_of_birth' => 'nullable|date_format:H:i:s',
            'gestational_age_weeks' => 'nullable|integer|min:0|max:45',
            'birth_place' => 'nullable|string|max:255',
            'birth_type' => 'nullable|string|max:255',
            'complications' => 'nullable|string',
            'baby_count' => 'nullable|integer|min:1',
            'birth_weight' => 'nullable|numeric|min:0',
            'birth_length' => 'nullable|numeric|min:0',
            'birth_circumference' => 'nullable|numeric|min:0',
            'vitamin_k' => 'nullable|string|in:yes,no',
            'apgar_score' => 'nullable|string|max:50',
            'blood_group' => 'nullable|string|max:10',
        ]);

        try {
            // Generate BirthID
            $maxBirthID = DB::table('birthrecord')
                ->whereRaw("BirthID REGEXP '^BIR[0-9]+$'")
                ->selectRaw("CAST(SUBSTRING(BirthID, 4) AS UNSIGNED) as num")
                ->orderBy('num', 'desc')
                ->value('num');
            
            $nextNumber = ($maxBirthID ?? 0) + 1;
            $birthID = 'BIR' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            // Convert vitamin_k to boolean
            $vitaminKGiven = false;
            if ($request->vitamin_k === 'yes') {
                $vitaminKGiven = true;
            }

            BirthRecord::create([
                'BirthID' => $birthID,
                'ChildID' => $request->child_id,
                'TimeOfBirth' => $request->time_of_birth,
                'GestationalAgeWeeks' => $request->gestational_age_weeks,
                'BirthPlace' => $request->birth_place,
                'BirthType' => $request->birth_type,
                'Complications' => $request->complications,
                'BabyCount' => $request->baby_count,
                'BirthWeight' => $request->birth_weight,
                'BirthLength' => $request->birth_length,
                'BirthCircumference' => $request->birth_circumference,
                'VitaminKGiven' => $vitaminKGiven,
                'ApgarScore' => $request->apgar_score,
                'BloodGroup' => $request->blood_group,
            ]);

            return redirect()->route('birth.record')->with('success', '✅ Birth record added successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error adding birth record: ' . $e->getMessage());
        }
    }

    /**
     * Display child records for parent
     */
    public function showChildRecord(Request $request)
    {
        try {
            $sessionUserId = session('user_id');
            $userRole = session('user_role');
            
            \Log::info('showChildRecord called', [
                'sessionUserId' => $sessionUserId,
                'userRole' => $userRole
            ]);
            
            if ($userRole !== 'parent' || !$sessionUserId) {
                return redirect()->route('home')->with('error', 'Access denied.');
            }
            
            // For parents, UserID in user table should be the ParentID
            // The ParentID should be the same as UserID for parents
            $parentId = $sessionUserId;
            
            \Log::info('Looking for parent', ['parentId' => $parentId, 'type' => gettype($parentId)]);
            
            // Get parent record - try exact match first
            $parent = DB::table('parent')->where('ParentID', $parentId)->first();
            
            // If not found, try with string comparison (handle any type issues)
            if (!$parent) {
                $parent = DB::table('parent')->whereRaw('ParentID = ?', [$parentId])->first();
            }
            
            // Try one more time with explicit string casting
            if (!$parent) {
                $parent = DB::table('parent')->where('ParentID', (string)$parentId)->first();
            }
            
            if (!$parent) {
                // Last resort: try to find parent by checking all parents
                $allParents = DB::table('parent')->get();
                \Log::error('Parent not found', [
                    'sessionUserId' => $sessionUserId,
                    'parentId' => $parentId,
                    'parentIdType' => gettype($parentId),
                    'allParentIds' => $allParents->pluck('ParentID')->toArray()
                ]);
                
                return view('manageChildHealthRecord.ChildRecord', [
                    'childRecords' => [],
                    'children' => collect([]),
                    'selectedChildId' => null,
                    'childInfo' => null
                ])->with('error', 'Parent record not found. Session UserID: ' . $sessionUserId . ', ParentID: ' . $parentId);
            }
            
            \Log::info('Parent found', ['parentId' => $parent->ParentID, 'motherName' => $parent->MotherName]);
            
            $motherIC = trim($parent->MIdentificationNumber ?? '');
            $fatherIC = trim($parent->FIdentificationNumber ?? '');
            
            // Step 1: Get children by direct ParentID match
            // Try multiple query approaches to ensure we find the children
            $children = DB::table('child')
                ->where('ParentID', $parentId)
                ->get();
            
            \Log::info('First query attempt', [
                'parentId' => $parentId,
                'parentIdType' => gettype($parentId),
                'childrenFound' => $children->count(),
                'query' => 'where ParentID = ' . $parentId
            ]);
            
            // If no children found, try with string casting (in case of type mismatch)
            if ($children->isEmpty()) {
                $children = DB::table('child')
                    ->where('ParentID', (string)$parentId)
                    ->get();
                \Log::info('Second query attempt (string cast)', [
                    'childrenFound' => $children->count()
                ]);
            }
            
            // Try with raw query
            if ($children->isEmpty()) {
                $children = DB::table('child')
                    ->whereRaw('ParentID = ?', [$parentId])
                    ->get();
                \Log::info('Third query attempt (raw)', [
                    'childrenFound' => $children->count()
                ]);
            }
            
            // Try with explicit string in raw query
            if ($children->isEmpty()) {
                $children = DB::table('child')
                    ->whereRaw('ParentID = ?', [(string)$parentId])
                    ->get();
                \Log::info('Fourth query attempt (raw string)', [
                    'childrenFound' => $children->count()
                ]);
            }
            
            // Debug: Log the query result
            \Log::info('Direct ParentID query result', [
                'parentId' => $parentId,
                'parentIdType' => gettype($parentId),
                'childrenFound' => $children->count(),
                'children' => $children->pluck('ChildID', 'FullName')->toArray(),
                'allChildrenInDB' => DB::table('child')->pluck('ChildID', 'ParentID')->toArray()
            ]);
            
            // Step 2: If no children found, find all parents with matching IC numbers
            if ($children->isEmpty() && ($motherIC || $fatherIC)) {
                $matchingParentIds = collect([$parentId]);
                
                if ($motherIC) {
                    $parentsByMotherIC = DB::table('parent')
                        ->where('MIdentificationNumber', $motherIC)
                        ->pluck('ParentID');
                    $matchingParentIds = $matchingParentIds->merge($parentsByMotherIC);
                }
                
                if ($fatherIC) {
                    $parentsByFatherIC = DB::table('parent')
                        ->where('FIdentificationNumber', $fatherIC)
                        ->pluck('ParentID');
                    $matchingParentIds = $matchingParentIds->merge($parentsByFatherIC);
                }
                
                $matchingParentIds = $matchingParentIds->unique()->values();
                
                if ($matchingParentIds->isNotEmpty()) {
                    $children = DB::table('child')
                        ->whereIn('ParentID', $matchingParentIds->toArray())
                        ->get();
                }
            }
            
            // Step 3: If still no children, check all children and match by parent IC
            if ($children->isEmpty() && ($motherIC || $fatherIC)) {
                $allChildren = DB::table('child')
                    ->whereNotNull('ParentID')
                    ->get();
                
                $matchedChildren = collect();
                foreach ($allChildren as $child) {
                    $childParent = DB::table('parent')->where('ParentID', $child->ParentID)->first();
                    if ($childParent) {
                        $childMotherIC = trim($childParent->MIdentificationNumber ?? '');
                        $childFatherIC = trim($childParent->FIdentificationNumber ?? '');
                        
                        $matches = false;
                        if ($motherIC && $childMotherIC && $motherIC === $childMotherIC) {
                            $matches = true;
                        }
                        if ($fatherIC && $childFatherIC && $fatherIC === $childFatherIC) {
                            $matches = true;
                        }
                        
                        if ($matches) {
                            $matchedChildren->push($child);
                        }
                    }
                }
                
                $children = $matchedChildren;
            }
            
            // Final fallback: If still no children, try a very direct query
            // This handles any edge cases where the query might have failed
            if ($children->isEmpty()) {
                // Try direct string match
                $children = DB::table('child')
                    ->where('ParentID', (string)$parentId)
                    ->get();
                
                // If still empty, try with raw query
                if ($children->isEmpty()) {
                    $children = DB::table('child')
                        ->whereRaw('CAST(ParentID AS CHAR) = ?', [(string)$parentId])
                        ->get();
                }
                
                \Log::info('Fallback query attempt', [
                    'parentId' => $parentId,
                    'parentIdType' => gettype($parentId),
                    'childrenFound' => $children->count(),
                    'children' => $children->pluck('ChildID', 'FullName')->toArray()
                ]);
            }
            
            // Sort children by ChildID
            $children = $children->sortBy('ChildID')->values();
            
            // Final check: Get ALL children and filter in PHP to ensure we find them
            if ($children->isEmpty()) {
                $allChildren = DB::table('child')->get();
                \Log::info('All children in database', [
                    'totalChildren' => $allChildren->count(),
                    'allChildren' => $allChildren->map(function($c) {
                        return [
                            'ChildID' => $c->ChildID,
                            'FullName' => $c->FullName,
                            'ParentID' => $c->ParentID,
                            'ParentIDType' => gettype($c->ParentID)
                        ];
                    })->toArray()
                ]);
                
                // Filter by ParentID in PHP (handles any type issues)
                $children = $allChildren->filter(function($child) use ($parentId) {
                    $childParentId = $child->ParentID;
                    $matches = false;
                    
                    // Try multiple comparison methods
                    if ($childParentId == $parentId) {
                        $matches = true;
                    } elseif ((string)$childParentId === (string)$parentId) {
                        $matches = true;
                    } elseif (trim($childParentId) === trim($parentId)) {
                        $matches = true;
                    }
                    
                    return $matches;
                })->values();
                
                \Log::info('After PHP filtering', [
                    'parentId' => $parentId,
                    'parentIdType' => gettype($parentId),
                    'childrenFound' => $children->count(),
                    'children' => $children->map(function($c) {
                        return [
                            'ChildID' => $c->ChildID,
                            'FullName' => $c->FullName,
                            'ParentID' => $c->ParentID
                        ];
                    })->toArray()
                ]);
            }
            
            // Debug: Log what we found
            \Log::info('Child Record Lookup', [
                'parentId' => $parentId,
                'motherIC' => $motherIC,
                'fatherIC' => $fatherIC,
                'childrenFound' => $children->count(),
                'childIds' => $children->pluck('ChildID')->toArray()
            ]);
            
            if ($children->isEmpty()) {
                // Debug: Check if there are any children in the database at all
                $allChildrenCount = DB::table('child')->count();
                $childrenWithParentId = DB::table('child')->where('ParentID', $parentId)->count();
                
                $errorMessage = 'No child records found. ';
                $errorMessage .= 'ParentID: ' . $parentId . ', ';
                $errorMessage .= 'MotherIC: ' . ($motherIC ?: 'N/A') . ', ';
                $errorMessage .= 'FatherIC: ' . ($fatherIC ?: 'N/A') . '. ';
                $errorMessage .= 'Total children in DB: ' . $allChildrenCount . ', ';
                $errorMessage .= 'Children with this ParentID: ' . $childrenWithParentId;
                
                return view('manageChildHealthRecord.ChildRecord', [
                    'childRecords' => [],
                    'children' => collect([]),
                    'selectedChildId' => null,
                    'childInfo' => null
                ])->with('error', $errorMessage);
            }
            
            // Ensure we have children before proceeding
            if ($children->isEmpty()) {
                \Log::error('Children collection is empty after all queries', [
                    'parentId' => $parentId,
                    'childrenCollection' => $children->toArray()
                ]);
                
                return view('manageChildHealthRecord.ChildRecord', [
                    'childRecords' => [],
                    'children' => collect([]),
                    'selectedChildId' => null,
                    'childInfo' => null
                ])->with('error', 'No children found for this parent account.');
            }
            
            // Get selected child ID from request, or default to first child
            $firstChild = $children->first();
            if (!$firstChild) {
                \Log::error('Cannot get first child from collection', [
                    'childrenCount' => $children->count(),
                    'children' => $children->toArray()
                ]);
                
                return view('manageChildHealthRecord.ChildRecord', [
                    'childRecords' => [],
                    'children' => $children,
                    'selectedChildId' => null,
                    'childInfo' => null
                ])->with('error', 'Error: Cannot access child data.');
            }
            
            $selectedChildId = $request->input('child_id', $firstChild->ChildID);
            
            // Get the selected child
            $child = $children->firstWhere('ChildID', $selectedChildId);
            if (!$child) {
                $child = $firstChild;
                $selectedChildId = $child->ChildID;
            }
            
            $childId = $selectedChildId;
            
            \Log::info('Child selected for processing', [
                'childId' => $childId,
                'childName' => $child->FullName ?? 'N/A',
                'totalChildren' => $children->count()
            ]);
            
            // Debug: Log the child being processed
            \Log::info('Processing child records', [
                'childId' => $childId,
                'childName' => $child->FullName ?? 'N/A'
            ]);
            
            // Get all records for this child
            $childRecords = [];
            $childInfo = [
                'ChildID' => $child->ChildID,
                'FullName' => $child->FullName ?? 'N/A',
                'DateOfBirth' => $child->DateOfBirth ? \Carbon\Carbon::parse($child->DateOfBirth)->format('F j, Y') : 'N/A',
                'Gender' => $child->Gender ?? 'N/A',
            ];
            
            // Immunization - get all records
            $immunizations = DB::table('immunization')
                ->where('ChildID', $childId)
                ->orderBy('Date', 'desc')
                ->get();
            $childRecords['immunization'] = $immunizations;
            
            // Debug: Log record counts
            \Log::info('Child records retrieved', [
                'childId' => $childId,
                'immunizations' => $immunizations->count(),
            ]);
            
            // Birth Record
            $birthRecord = DB::table('birthrecord')
                ->where('ChildID', $childId)
                ->first();
            $childRecords['birth_record'] = $birthRecord;
            
            // Screening - get all records
            $screenings = DB::table('screeningresult')
                ->where('ChildID', $childId)
                ->orderBy('DateScreened', 'desc')
                ->get();
            $childRecords['screening'] = $screenings;
            
            // Growth Chart - get all records
            $growthRecords = DB::table('growthchart')
                ->where('ChildID', $childId)
                ->orderBy('DateMeasured', 'desc')
                ->get();
            $childRecords['growth_chart'] = $growthRecords;
            
            // Milestone - get all records
            $milestones = DB::table('developmentmilestone')
                ->where('ChildID', $childId)
                ->orderBy('created_at', 'desc')
                ->get();
            $childRecords['milestone'] = $milestones;
            
            // Feeding - get all records
            $feedings = DB::table('feedingrecord')
                ->where('ChildID', $childId)
                ->orderBy('DateLogged', 'desc')
                ->get();
            $childRecords['feeding'] = $feedings;
            
            // Debug: Log all record counts before returning
            \Log::info('Child records summary for parent view', [
                'parentId' => $parentId,
                'childId' => $childId,
                'childName' => $child->FullName ?? 'N/A',
                'immunizations' => $childRecords['immunization']->count(),
                'birth_record' => $childRecords['birth_record'] ? 1 : 0,
                'screenings' => $childRecords['screening']->count(),
                'growth_chart' => $childRecords['growth_chart']->count(),
                'milestones' => $childRecords['milestone']->count(),
                'feedings' => $childRecords['feeding']->count(),
            ]);
            
            // Final debug before returning - ensure children collection is valid
            \Log::info('Returning view with data', [
                'childrenCount' => $children->count(),
                'childrenType' => gettype($children),
                'childrenIsCollection' => $children instanceof \Illuminate\Support\Collection,
                'childInfoExists' => !empty($childInfo),
                'selectedChildId' => $selectedChildId,
                'immunizationsCount' => isset($childRecords['immunization']) ? $childRecords['immunization']->count() : 0,
                'growthChartCount' => isset($childRecords['growth_chart']) ? $childRecords['growth_chart']->count() : 0,
                'childrenData' => $children->map(function($c) {
                    return ['ChildID' => $c->ChildID ?? 'N/A', 'FullName' => $c->FullName ?? 'N/A'];
                })->toArray()
            ]);
            
            // Ensure children is a proper collection
            if (!($children instanceof \Illuminate\Support\Collection)) {
                $children = collect($children);
            }
            
            return view('manageChildHealthRecord.ChildRecord', [
                'childRecords' => $childRecords,
                'childInfo' => $childInfo,
                'children' => $children,
                'selectedChildId' => $selectedChildId
            ]);
        } catch (\Exception $e) {
            \Log::error('Exception in showChildRecord', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return view('manageChildHealthRecord.ChildRecord', [
                'childRecords' => [],
                'children' => collect([]),
                'selectedChildId' => null,
                'childInfo' => null
            ])->with('error', 'Error loading child records: ' . $e->getMessage());
        }
    }

    /**
     * Get WHO Growth Percentiles Data
     */
    private function getWhoPercentiles()
    {
        return [
            'weight' => [
                0 => ['p5' => 2.5, 'p50' => 3.3, 'p85' => 4.0, 'p95' => 4.5],
                1 => ['p5' => 3.2, 'p50' => 4.2, 'p85' => 5.0, 'p95' => 5.6],
                2 => ['p5' => 4.0, 'p50' => 5.0, 'p85' => 6.0, 'p95' => 6.6],
                3 => ['p5' => 4.7, 'p50' => 5.8, 'p85' => 6.9, 'p95' => 7.5],
                6 => ['p5' => 6.0, 'p50' => 7.3, 'p85' => 8.5, 'p95' => 9.2],
                9 => ['p5' => 7.2, 'p50' => 8.6, 'p85' => 9.8, 'p95' => 10.5],
                12 => ['p5' => 8.0, 'p50' => 9.5, 'p85' => 10.8, 'p95' => 11.5],
                18 => ['p5' => 9.2, 'p50' => 10.8, 'p85' => 12.2, 'p95' => 13.0],
                24 => ['p5' => 10.2, 'p50' => 12.0, 'p85' => 13.5, 'p95' => 14.5],
                30 => ['p5' => 11.0, 'p50' => 13.0, 'p85' => 14.5, 'p95' => 15.5],
                36 => ['p5' => 11.8, 'p50' => 13.8, 'p85' => 15.5, 'p95' => 16.5],
                42 => ['p5' => 12.5, 'p50' => 14.5, 'p85' => 16.5, 'p95' => 17.5],
                48 => ['p5' => 13.2, 'p50' => 15.2, 'p85' => 17.2, 'p95' => 18.5],
                54 => ['p5' => 13.8, 'p50' => 15.8, 'p85' => 18.0, 'p95' => 19.5],
                60 => ['p5' => 14.5, 'p50' => 16.5, 'p85' => 18.8, 'p95' => 20.5],
                66 => ['p5' => 15.2, 'p50' => 17.2, 'p85' => 19.5, 'p95' => 21.5],
                72 => ['p5' => 15.8, 'p50' => 18.0, 'p85' => 20.5, 'p95' => 22.5]
            ],
            'height' => [
                0 => ['p5' => 47.0, 'p50' => 50.0, 'p85' => 53.0, 'p95' => 55.0],
                1 => ['p5' => 51.0, 'p50' => 54.0, 'p85' => 57.0, 'p95' => 59.0],
                2 => ['p5' => 54.0, 'p50' => 57.5, 'p85' => 61.0, 'p95' => 63.0],
                3 => ['p5' => 56.5, 'p50' => 60.5, 'p85' => 64.0, 'p95' => 66.5],
                6 => ['p5' => 62.0, 'p50' => 66.0, 'p85' => 70.0, 'p95' => 72.5],
                9 => ['p5' => 67.0, 'p50' => 71.0, 'p85' => 75.0, 'p95' => 77.5],
                12 => ['p5' => 71.0, 'p50' => 75.0, 'p85' => 79.0, 'p95' => 82.0],
                18 => ['p5' => 77.0, 'p50' => 81.5, 'p85' => 86.0, 'p95' => 89.0],
                24 => ['p5' => 82.0, 'p50' => 87.0, 'p85' => 92.0, 'p95' => 95.5],
                30 => ['p5' => 86.0, 'p50' => 91.5, 'p85' => 97.0, 'p95' => 100.5],
                36 => ['p5' => 90.0, 'p50' => 95.5, 'p85' => 101.0, 'p95' => 105.0],
                42 => ['p5' => 93.5, 'p50' => 99.0, 'p85' => 105.0, 'p95' => 109.0],
                48 => ['p5' => 96.5, 'p50' => 102.5, 'p85' => 108.5, 'p95' => 112.5],
                54 => ['p5' => 99.5, 'p50' => 105.5, 'p85' => 111.5, 'p95' => 116.0],
                60 => ['p5' => 102.0, 'p50' => 108.5, 'p85' => 114.5, 'p95' => 119.0],
                66 => ['p5' => 104.5, 'p50' => 111.0, 'p85' => 117.5, 'p95' => 122.0],
                72 => ['p5' => 107.0, 'p50' => 113.5, 'p85' => 120.0, 'p95' => 124.5]
            ],
            'head' => [
                0 => ['p5' => 32.5, 'p50' => 35.0, 'p85' => 37.0, 'p95' => 38.0],
                1 => ['p5' => 35.0, 'p50' => 37.5, 'p85' => 39.5, 'p95' => 40.5],
                2 => ['p5' => 36.5, 'p50' => 39.0, 'p85' => 41.0, 'p95' => 42.0],
                3 => ['p5' => 38.0, 'p50' => 40.5, 'p85' => 42.5, 'p95' => 43.5],
                6 => ['p5' => 40.5, 'p50' => 43.0, 'p85' => 45.0, 'p95' => 46.0],
                9 => ['p5' => 42.0, 'p50' => 44.5, 'p85' => 46.5, 'p95' => 47.5],
                12 => ['p5' => 43.0, 'p50' => 45.5, 'p85' => 47.5, 'p95' => 48.5],
                18 => ['p5' => 44.5, 'p50' => 47.0, 'p85' => 49.0, 'p95' => 50.0],
                24 => ['p5' => 45.5, 'p50' => 48.0, 'p85' => 50.0, 'p95' => 51.0],
                30 => ['p5' => 46.5, 'p50' => 49.0, 'p85' => 51.0, 'p95' => 52.0],
                36 => ['p5' => 47.5, 'p50' => 50.0, 'p85' => 52.0, 'p95' => 53.0],
                42 => ['p5' => 48.0, 'p50' => 50.5, 'p85' => 52.5, 'p95' => 53.5],
                48 => ['p5' => 48.5, 'p50' => 51.0, 'p85' => 53.0, 'p95' => 54.0],
                54 => ['p5' => 49.0, 'p50' => 51.5, 'p85' => 53.5, 'p95' => 54.5],
                60 => ['p5' => 49.5, 'p50' => 52.0, 'p85' => 54.0, 'p95' => 55.0],
                66 => ['p5' => 50.0, 'p50' => 52.5, 'p85' => 54.5, 'p95' => 55.5],
                72 => ['p5' => 50.5, 'p50' => 53.0, 'p85' => 55.0, 'p95' => 56.0]
            ]
        ];
    }

    /**
     * Get percentile value for a given age (with interpolation)
     */
    private function getPercentileValue($metric, $age, $percentile, $whoPercentiles)
    {
        if (!isset($whoPercentiles[$metric])) return null;
        $data = $whoPercentiles[$metric];
        $ages = array_keys($data);
        sort($ages);
        
        if ($age <= $ages[0]) return $data[$ages[0]][$percentile] ?? null;
        if ($age >= $ages[count($ages) - 1]) return $data[$ages[count($ages) - 1]][$percentile] ?? null;
        
        // Find surrounding ages
        $lowerAge = $ages[0];
        $upperAge = $ages[count($ages) - 1];
        for ($i = 0; $i < count($ages) - 1; $i++) {
            if ($age >= $ages[$i] && $age <= $ages[$i + 1]) {
                $lowerAge = $ages[$i];
                $upperAge = $ages[$i + 1];
                break;
            }
        }
        
        // Linear interpolation
        $lowerValue = $data[$lowerAge][$percentile] ?? null;
        $upperValue = $data[$upperAge][$percentile] ?? null;
        if ($lowerValue === null || $upperValue === null) return null;
        
        $ratio = ($age - $lowerAge) / ($upperAge - $lowerAge);
        return $lowerValue + ($upperValue - $lowerValue) * $ratio;
    }

    /**
     * Calculate growth status based on WHO percentiles
     */
    private function calculateGrowthStatus($age, $weight, $height, $headCircumference, $whoPercentiles)
    {
        if ($age === null) {
            return ['status' => 'unknown', 'label' => 'N/A', 'color' => '#999', 'icon' => ''];
        }

        $overallStatus = 'normal';
        $statuses = [];

        // Check weight
        if ($weight !== null) {
            $p5 = $this->getPercentileValue('weight', $age, 'p5', $whoPercentiles);
            $p85 = $this->getPercentileValue('weight', $age, 'p85', $whoPercentiles);
            $p95 = $this->getPercentileValue('weight', $age, 'p95', $whoPercentiles);
            
            if ($p5 !== null && $p85 !== null && $p95 !== null) {
                if ($weight < $p5) {
                    $statuses['weight'] = ['status' => 'abnormal', 'label' => 'Underweight'];
                    $overallStatus = 'abnormal';
                } elseif ($weight >= $p5 && $weight < $p85) {
                    $statuses['weight'] = ['status' => 'normal', 'label' => 'Normal'];
                } elseif ($weight >= $p85 && $weight < $p95) {
                    $statuses['weight'] = ['status' => 'borderline', 'label' => 'Borderline'];
                    if ($overallStatus === 'normal') $overallStatus = 'borderline';
                } elseif ($weight >= $p95) {
                    $statuses['weight'] = ['status' => 'abnormal', 'label' => 'Overweight'];
                    $overallStatus = 'abnormal';
                }
            }
        }
        
        // Check height
        if ($height !== null) {
            $p5 = $this->getPercentileValue('height', $age, 'p5', $whoPercentiles);
            $p85 = $this->getPercentileValue('height', $age, 'p85', $whoPercentiles);
            $p95 = $this->getPercentileValue('height', $age, 'p95', $whoPercentiles);
            
            if ($p5 !== null && $p85 !== null && $p95 !== null) {
                if ($height < $p5) {
                    $statuses['height'] = ['status' => 'abnormal', 'label' => 'Short'];
                    $overallStatus = 'abnormal';
                } elseif ($height >= $p5 && $height < $p85) {
                    $statuses['height'] = ['status' => 'normal', 'label' => 'Normal'];
                } elseif ($height >= $p85 && $height < $p95) {
                    $statuses['height'] = ['status' => 'borderline', 'label' => 'Borderline'];
                    if ($overallStatus === 'normal') $overallStatus = 'borderline';
                } elseif ($height >= $p95) {
                    $statuses['height'] = ['status' => 'abnormal', 'label' => 'Tall'];
                    $overallStatus = 'abnormal';
                }
            }
        }

        // Determine overall status
        if ($overallStatus === 'abnormal') {
            return ['status' => 'abnormal', 'label' => 'Abnormal', 'color' => '#d32f2f', 'icon' => '⚠️'];
        } elseif ($overallStatus === 'borderline') {
            return ['status' => 'borderline', 'label' => 'Borderline', 'color' => '#ff9800', 'icon' => '⚡'];
        } else {
            return ['status' => 'normal', 'label' => 'Normal', 'color' => '#4caf50', 'icon' => '✓'];
        }
    }
}

