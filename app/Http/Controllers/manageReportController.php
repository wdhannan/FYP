<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Report;
use App\Models\Child;
use App\Models\Doctor;
use Barryvdh\DomPDF\Facade\Pdf;

class manageReportController extends Controller
{
    /**
     * Display list of all reports
     */
    public function index()
    {
        try {
            $reports = DB::table('report')
                ->leftJoin('child', 'report.ChildID', '=', 'child.ChildID')
                ->leftJoin('doctor', 'report.DoctorID', '=', 'doctor.DoctorID')
                ->select(
                    'report.*',
                    'child.FullName as ChildName',
                    'doctor.FullName as DoctorName'
                )
                ->orderBy('report.ReportDate', 'desc')
                ->get();
            
            return view('manageReport.ListOfReport', compact('reports'));
        } catch (\Exception $e) {
            return view('manageReport.ListOfReport', ['reports' => collect([])])
                ->with('error', 'Error loading reports: ' . $e->getMessage());
        }
    }

    /**
     * Display form to create new report
     */
    public function create()
    {
        try {
            // Get all registered children from database
            $children = DB::table('child')
                ->select('ChildID', 'FullName')
                ->orderBy('ChildID', 'asc')
                ->get();
            
            $doctors = Doctor::select('DoctorID', 'FullName')->get();

            // Generate preview ReportID for display
            try {
                $maxReportResult = DB::table('report')
                    ->whereRaw("ReportID REGEXP '^RPT[0-9]+$'")
                    ->selectRaw("CAST(SUBSTRING(ReportID, 4) AS UNSIGNED) as num")
                    ->orderBy('num', 'desc')
                    ->first();
                
                $maxReportID = $maxReportResult ? (int)$maxReportResult->num : 0;
                $nextNumber = $maxReportID + 1;
                $previewReportID = 'RPT' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            } catch (\Exception $e) {
                $previewReportID = 'RPT000001';
            }

            return view('manageReport.CreateNewReport', compact('children', 'doctors', 'previewReportID'));
        } catch (\Exception $e) {
            return view('manageReport.CreateNewReport', ['children' => collect([]), 'doctors' => collect([]), 'previewReportID' => 'RPT000001'])
                ->with('error', 'Error loading form: ' . $e->getMessage());
        }
    }

    /**
     * Store new report
     */
    public function store(Request $request)
    {
        $request->validate([
            'child_id' => 'required|exists:child,ChildID',
            'doctor_id' => 'required|exists:doctor,DoctorID',
            'report_date' => 'required|date',
            'diagnosis' => 'nullable|string',
            'symptoms' => 'nullable|string',
            'findings' => 'nullable|string',
            'follow_up_advices' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            // Always auto-generate ReportID
            $maxReportResult = DB::table('report')
                ->whereRaw("ReportID REGEXP '^RPT[0-9]+$'")
                ->selectRaw("CAST(SUBSTRING(ReportID, 4) AS UNSIGNED) as num")
                ->orderBy('num', 'desc')
                ->first();
            
            $maxReportID = $maxReportResult ? (int)$maxReportResult->num : 0;
            $nextNumber = $maxReportID + 1;
            $reportID = 'RPT' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            Report::create([
                'ReportID' => $reportID,
                'ChildID' => $request->child_id,
                'DoctorID' => $request->doctor_id,
                'ReportDate' => $request->report_date,
                'Diagnosis' => $request->diagnosis,
                'Symptoms' => $request->symptoms,
                'Findings' => $request->findings,
                'FollowUpAdvices' => $request->follow_up_advices,
                'Notes' => $request->notes,
            ]);

            return redirect()->route('report.list')
                ->with('success', 'Report created successfully!');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating report: ' . $e->getMessage());
        }
    }

    /**
     * Display specific report by ChildID
     */
    public function show($childId)
    {
        try {
            // Fetch the most recent report for the child, or fetch by report ID if provided
            $report = DB::table('report')
                ->leftJoin('child', 'report.ChildID', '=', 'child.ChildID')
                ->leftJoin('doctor', 'report.DoctorID', '=', 'doctor.DoctorID')
                ->where('report.ChildID', $childId)
                ->select(
                    'report.*',
                    'child.FullName as ChildName',
                    'child.DateOfBirth',
                    'child.Gender',
                    'doctor.FullName as DoctorName'
                )
                ->orderBy('report.ReportDate', 'desc')
                ->first();

            if (!$report) {
                abort(404, 'Report not found');
            }

            return view('manageReport.ViewReport', compact('report'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading report: ' . $e->getMessage());
        }
    }

    /**
     * Display specific report by ReportID
     */
    public function showByReportId($reportId)
    {
        try {
            // Fetch report by ReportID
            $report = DB::table('report')
                ->leftJoin('child', 'report.ChildID', '=', 'child.ChildID')
                ->leftJoin('doctor', 'report.DoctorID', '=', 'doctor.DoctorID')
                ->where('report.ReportID', $reportId)
                ->select(
                    'report.*',
                    'child.FullName as ChildName',
                    'child.DateOfBirth',
                    'child.Gender',
                    'doctor.FullName as DoctorName'
                )
                ->first();

            if (!$report) {
                abort(404, 'Report not found');
            }

            return view('manageReport.ViewReport', compact('report'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading report: ' . $e->getMessage());
        }
    }

    /**
     * Display form to update report
     */
    public function edit($reportId)
    {
        try {
            // Check if user is a doctor
            $userRole = session('user_role');
            if ($userRole !== 'doctor') {
                return back()->with('error', 'Access denied. Only doctors can edit reports.');
            }

            // Use DB facade to ensure we get the report correctly
            $report = DB::table('report')
                ->where('ReportID', $reportId)
                ->first();
            
            if (!$report) {
                // Try with string comparison in case of type mismatch
                $report = DB::table('report')
                    ->whereRaw('CAST(ReportID AS CHAR) = ?', [(string)$reportId])
                    ->first();
            }
            
            if (!$report) {
                \Log::error('Report not found in edit', ['reportId' => $reportId]);
                abort(404, 'Report not found');
            }

            // Get child information for display
            $child = DB::table('child')
                ->where('ChildID', $report->ChildID)
                ->select('ChildID', 'FullName')
                ->first();

            return view('manageReport.Updatereport', compact('report', 'child'));
        } catch (\Exception $e) {
            \Log::error('Error in report edit: ' . $e->getMessage());
            return back()->with('error', 'Error loading report: ' . $e->getMessage());
        }
    }

    /**
     * Update report
     */
    public function update(Request $request, $reportId)
    {
        // Check if user is a doctor
        $userRole = session('user_role');
        if ($userRole !== 'doctor') {
            return back()->with('error', 'Access denied. Only doctors can update reports.');
        }

        $request->validate([
            'report_date' => 'required|date',
            'diagnosis' => 'nullable|string',
            'symptoms' => 'nullable|string',
            'findings' => 'nullable|string',
            'follow_up_advices' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            // Use DB facade to ensure we get the report correctly
            $report = DB::table('report')
                ->where('ReportID', $reportId)
                ->first();
            
            if (!$report) {
                $report = DB::table('report')
                    ->whereRaw('CAST(ReportID AS CHAR) = ?', [(string)$reportId])
                    ->first();
            }
            
            if (!$report) {
                abort(404, 'Report not found');
            }

            // Ensure the logged-in doctor owns this report
            $sessionDoctorId = session('user_id');
            if ($report->DoctorID !== $sessionDoctorId) {
                return back()->with('error', 'You are not authorized to update this report.');
            }

            // Update report - keep ChildID and DoctorID unchanged
            DB::table('report')
                ->where('ReportID', $reportId)
                ->update([
                    'ReportDate' => $request->report_date,
                    'Diagnosis' => $request->diagnosis,
                    'Symptoms' => $request->symptoms,
                    'Findings' => $request->findings,
                    'FollowUpAdvices' => $request->follow_up_advices,
                    'Notes' => $request->notes,
                    'updated_at' => now(),
                ]);

            return redirect()->route('report.list')
                ->with('success', 'Report updated successfully!');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating report: ' . $e->getMessage());
        }
    }

    /**
     * Generate PDF for report
     */
    public function generatePDF($childId)
    {
        try {
            // Fetch the most recent report for the child
            $report = DB::table('report')
                ->leftJoin('child', 'report.ChildID', '=', 'child.ChildID')
                ->leftJoin('doctor', 'report.DoctorID', '=', 'doctor.DoctorID')
                ->leftJoin('parent', 'child.ParentID', '=', 'parent.ParentID')
                ->where('report.ChildID', $childId)
                ->select(
                    'report.*',
                    'child.FullName as ChildName',
                    'child.DateOfBirth',
                    'child.Gender',
                    'doctor.FullName as DoctorName',
                    'parent.MotherName',
                    'parent.FatherName'
                )
                ->orderBy('report.ReportDate', 'desc')
                ->first();

            if (!$report) {
                abort(404, 'Report not found');
            }

            $pdf = Pdf::loadView('manageReport.ReportPdf', [
                'report' => $report,
            ])->setPaper('a4');

            // Sanitize filename - remove invalid characters
            $childName = preg_replace('/[^a-zA-Z0-9_]/', '_', $report->ChildName ?? 'child');
            $reportId = preg_replace('/[^a-zA-Z0-9_]/', '_', $report->ReportID ?? 'unknown');
            $filename = sprintf('report_%s_%s.pdf', strtolower($childName), strtolower($reportId));

            return $pdf->download($filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Error generating PDF: ' . $e->getMessage());
        }
    }

    /**
     * Delete report
     */
    public function destroy(Request $request, $reportId)
    {
        try {
        // Check if user is a doctor
        $userRole = session('user_role');
        if ($userRole !== 'doctor') {
            return back()->with('error', 'Access denied. Only doctors can delete reports.');
        }

            $report = Report::where('ReportID', $reportId)->first();
            
            if (!$report) {
                abort(404, 'Report not found');
            }

            $report->delete();

            return redirect()->route('report.list')
                ->with('success', 'Report deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting report: ' . $e->getMessage());
        }
    }
}

