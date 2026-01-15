@extends('layouts.app')

@section('title', 'View Report')

@section('content')
    @php
        // DB::table()->get() returns stdClass objects, so always use object notation
        $reportId = $report->ReportID ?? '';
        $childId = $report->ChildID ?? '';
        $reportDate = isset($report->ReportDate) ? \Carbon\Carbon::parse($report->ReportDate)->format('d/m/Y') : 'N/A';
        $diagnosis = $report->Diagnosis ?? '-';
        $symptoms = $report->Symptoms ?? '-';
        $findings = $report->Findings ?? '-';
        $followUp = $report->FollowUpAdvices ?? '-';
        $notes = $report->Notes ?? '-';
    @endphp

    <style>
        .report-view-wrapper {
            background-color: #fff5f5;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
            min-height: calc(100vh - 160px);
        }

        .report-view-title {
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            text-align: center;
            margin-bottom: 24px;
            color: #000;
        }

        .report-meta-table,
        .report-detail-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
        }

        .report-meta-table th {
            background-color: #f6bbc5;
            color: #000;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 1px;
            padding: 16px;
            text-align: left;
        }

        .report-meta-table td {
            padding: 16px;
            border-bottom: 1px solid #f1f1f1;
            font-weight: 600;
            font-size: 16px;
        }

        .report-detail-table td {
            padding: 16px;
            border-bottom: 1px solid #f1f1f1;
            font-size: 15px;
        }

        .report-detail-table td:first-child {
            width: 25%;
            font-weight: 600;
        }

        .report-detail-table tr:nth-child(odd) td {
            background-color: #fff9fb;
        }

        .report-actions {
            margin-top: 24px;
            display: flex;
            justify-content: flex-end;
        }

        .report-button {
            background-color: #555;
            color: white;
            border: none;
            padding: 12px 28px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .report-button:hover {
            background-color: #333;
        }

        .report-back-link {
            display: inline-block;
            margin-bottom: 16px;
            color: #ff6f91;
            text-decoration: none;
            font-weight: 600;
        }
    </style>

    <div class="report-view-wrapper">
      

        <h2 class="report-view-title">Report</h2>

        <table class="report-meta-table">
            <tr>
                <th>Report ID</th>
                <th>Child ID</th>
                <th>Report Date</th>
            </tr>
            <tr>
                <td>{{ $reportId }}</td>
                <td>{{ $childId }}</td>
                <td>{{ $reportDate }}</td>
            </tr>
        </table>

        <table class="report-detail-table" style="margin-top: 24px;">
            <tr>
                <td>Diagnosis</td>
                <td>{{ $diagnosis }}</td>
            </tr>
            <tr>
                <td>Symptoms</td>
                <td>{{ $symptoms }}</td>
            </tr>
            <tr>
                <td>Findings</td>
                <td>{{ $findings }}</td>
            </tr>
            <tr>
                <td>Follow-up Advices</td>
                <td>{{ $followUp }}</td>
            </tr>
            <tr>
                <td>Notes</td>
                <td>{{ $notes }}</td>
            </tr>
        </table>

        <div class="report-actions">
            <form method="GET" action="{{ route('report.pdf', ['childId' => $childId]) }}">
                <button type="submit" class="report-button">Generate PDF</button>
            </form>
        </div>
    </div>
@endsection

