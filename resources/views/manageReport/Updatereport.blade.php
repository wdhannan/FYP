@extends('layouts.app')

@section('title', 'Update Report')

@section('content')

    <style>
        body {
            background: linear-gradient(135deg, #ffeef4 0%, #fff5f8 50%, #ffeef4 100%);
            min-height: 100vh;
        }

        .page-wrapper {
            padding: 40px 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .page-header {
            margin-bottom: 40px;
        }

        .page-title {
            font-size: 28px;
            font-weight: 800;
            color: #1a1a1a;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .new-report-wrapper {
            background: linear-gradient(135deg, #fff5f7 0%, #ffeef0 50%, #ffe5e8 100%);
            border-radius: 24px;
            padding: 40px;
            margin: 0 auto;
            max-width: 1100px;
            box-shadow: 0 20px 60px rgba(255, 111, 145, 0.15), 0 0 0 1px rgba(255, 182, 193, 0.1);
        }

        .report-form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
            margin-bottom: 24px;
        }

        .report-form-field {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .report-form-field.full-width {
            grid-column: 1 / -1;
        }

        .report-form-label {
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.8px;
            color: #1a1a1a;
            text-transform: uppercase;
        }

        .report-form-input,
        .report-form-select,
        .report-form-textarea {
            padding: 14px 16px;
            border-radius: 12px;
            border: 2px solid #ffe5e8;
            background: linear-gradient(to bottom, #fff, #fff8f8);
            font-size: 15px;
            font-family: inherit;
            color: #1a1a1a;
            transition: all 0.3s ease;
        }

        .report-form-input:focus,
        .report-form-select:focus,
        .report-form-textarea:focus {
            outline: none;
            border-color: #ff6f91;
            box-shadow: 0 0 0 4px rgba(255, 111, 145, 0.1);
            background: white;
        }

        .report-form-input[readonly] {
            background: #f5f5f5;
            cursor: not-allowed;
            border-color: #e0e0e0;
        }

        .report-form-textarea {
            min-height: 120px;
            resize: vertical;
            font-family: inherit;
        }

        .date-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .date-input-wrapper input {
            flex: 1;
        }

        .select-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .select-wrapper select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            padding-right: 45px;
            width: 100%;
            cursor: pointer;
        }

        .select-chevron {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            pointer-events: none;
            color: #1a1a1a;
            z-index: 1;
        }

        .report-form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 2px solid rgba(255, 182, 193, 0.3);
        }

        .save-report-btn {
            background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
            border: none;
            padding: 16px 40px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 15px;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            color: white;
            box-shadow: 0 4px 16px rgba(255, 111, 145, 0.3);
        }

        .save-report-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(255, 111, 145, 0.4);
        }

        .cancel-btn {
            background: linear-gradient(135deg, #999 0%, #777 100%);
            border: none;
            padding: 16px 40px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 15px;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            color: white;
            box-shadow: 0 4px 16px rgba(153, 153, 153, 0.3);
            text-decoration: none;
            display: inline-block;
        }

        .cancel-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(153, 153, 153, 0.4);
        }

        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .alert-success {
            background: linear-gradient(135deg, #4CAF50 0%, #66BB6A 100%);
            color: white;
            border-left: 4px solid #2E7D32;
        }

        .alert-error {
            background: linear-gradient(135deg, #F44336 0%, #EF5350 100%);
            color: white;
            border-left: 4px solid #C62828;
        }

        @media (max-width: 768px) {
            .report-form-grid {
                grid-template-columns: 1fr;
            }
            
            .new-report-wrapper {
                padding: 24px;
            }
            
            .page-title {
                font-size: 24px;
            }
        }
    </style>

    <div class="page-wrapper">
        <div class="page-header">
            <h1 class="page-title">✏️ Update Report</h1>
        </div>

        <div class="new-report-wrapper">

        @if($errors->any())
            <div class="alert alert-error">
                <strong>❌ Validation Errors:</strong>
                <ul style="margin: 8px 0 0 20px; padding: 0;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="updateReportForm" method="POST" action="{{ route('report.update', ['reportId' => $report->ReportID]) }}">
            @csrf
            @method('PUT')
            
            <div class="report-form-grid">
                <div class="report-form-field">
                    <label class="report-form-label">Report ID</label>
                    <input type="text" class="report-form-input" value="{{ isset($report) && isset($report->ReportID) ? $report->ReportID : 'N/A' }}" readonly style="background-color: #f5f5f5; cursor: not-allowed;">
                </div>

                <div class="report-form-field">
                    <label class="report-form-label">Child ID</label>
                    <input type="text" class="report-form-input" value="{{ isset($report) && isset($report->ChildID) ? $report->ChildID . ' - ' . ($child->FullName ?? 'N/A') : 'N/A' }}" readonly style="background-color: #f5f5f5; cursor: not-allowed;">
                    <input type="hidden" name="child_id" value="{{ $report->ChildID ?? '' }}">
                </div>

                <div class="report-form-field">
                    <label class="report-form-label">Report Date</label>
                    <div class="date-input-wrapper">
                        <input type="date" class="report-form-input" name="report_date" value="{{ old('report_date', $report->ReportDate ? \Carbon\Carbon::parse($report->ReportDate)->format('Y-m-d') : '') }}" required>
                    </div>
                </div>

                <div class="report-form-field full-width">
                    <label class="report-form-label">Diagnosis</label>
                    <textarea class="report-form-textarea" name="diagnosis" placeholder="Enter diagnosis">{{ old('diagnosis', $report->Diagnosis ?? '') }}</textarea>
                </div>

                <div class="report-form-field full-width">
                    <label class="report-form-label">Symptoms</label>
                    <textarea class="report-form-textarea" name="symptoms" placeholder="Enter symptoms">{{ old('symptoms', $report->Symptoms ?? '') }}</textarea>
                </div>

                <div class="report-form-field full-width">
                    <label class="report-form-label">Findings</label>
                    <textarea class="report-form-textarea" name="findings" placeholder="Enter findings">{{ old('findings', $report->Findings ?? '') }}</textarea>
                </div>

                <div class="report-form-field full-width">
                    <label class="report-form-label">Follow-up Advices</label>
                    <textarea class="report-form-textarea" name="follow_up_advices" placeholder="Enter follow-up advices">{{ old('follow_up_advices', $report->FollowUpAdvices ?? '') }}</textarea>
                </div>

                <div class="report-form-field full-width">
                    <label class="report-form-label">Notes</label>
                    <textarea class="report-form-textarea" name="notes" placeholder="Enter additional notes">{{ old('notes', $report->Notes ?? '') }}</textarea>
                </div>
            </div>

            <div class="report-form-actions">
                <a href="{{ route('report.list') }}" class="cancel-btn">Cancel</a>
                <button type="submit" class="save-report-btn" id="updateReportBtn">Update Report</button>
            </div>
        </form>
    </div>

    <script>
        // Prevent double submission
        document.getElementById('updateReportForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('updateReportBtn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Updating...';
        });
    </script>
        </div>
    </div>
@endsection
