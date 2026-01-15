<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Report - {{ $report->ReportID ?? 'Report' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            padding: 0;
            margin: 0;
            background: #fff;
            color: #333;
        }
        .page {
            padding: 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #ff6f91;
        }
        .header h1 {
            font-size: 24px;
            font-weight: 700;
            color: #ff6f91;
            margin: 0 0 8px 0;
            letter-spacing: 3px;
        }
        .header p {
            font-size: 11px;
            color: #888;
            margin: 0;
        }
        .info-card {
            background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
            border-radius: 12px;
            padding: 3px;
            margin-bottom: 25px;
        }
        .info-card-inner {
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
        }
        .info-row {
            display: table;
            width: 100%;
        }
        .info-cell {
            display: table-cell;
            width: 25%;
            padding: 15px 20px;
            text-align: center;
            border-right: 1px solid #ffe0e8;
        }
        .info-cell:last-child {
            border-right: none;
        }
        .info-label {
            font-size: 10px;
            font-weight: 600;
            color: #ff6f91;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 14px;
            font-weight: 600;
            color: #1a1a1a;
        }
        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #1a1a1a;
            margin: 25px 0 15px;
            padding-left: 12px;
            border-left: 4px solid #ff6f91;
        }
        .detail-card {
            background: #fff8fa;
            border-radius: 10px;
            margin-bottom: 12px;
            overflow: hidden;
        }
        .detail-item {
            display: table;
            width: 100%;
            border-bottom: 1px solid #ffe0e8;
        }
        .detail-item:last-child {
            border-bottom: none;
        }
        .detail-label {
            display: table-cell;
            width: 28%;
            padding: 14px 18px;
            font-weight: 600;
            color: #1a1a1a;
            background: #fff0f3;
            vertical-align: top;
        }
        .detail-value {
            display: table-cell;
            width: 72%;
            padding: 14px 18px;
            color: #444;
            line-height: 1.5;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #ffe0e8;
            text-align: center;
        }
        .footer p {
            font-size: 10px;
            color: #aaa;
            margin: 3px 0;
        }
        .badge {
            display: inline-block;
            background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
            color: #fff;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- Header -->
        <div class="header">
            <h1>REPORT</h1>
            <p>Digital Child Health Record System</p>
        </div>

        <!-- Report Info Card -->
        <div class="info-card">
            <div class="info-card-inner">
                <div class="info-row">
                    <div class="info-cell">
                        <div class="info-label">Report ID</div>
                        <div class="info-value">{{ $report->ReportID ?? 'N/A' }}</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-label">Child ID</div>
                        <div class="info-value">{{ $report->ChildID ?? 'N/A' }}</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-label">Child Name</div>
                        <div class="info-value">{{ $report->ChildName ?? 'N/A' }}</div>
                    </div>
                    <div class="info-cell">
                        <div class="info-label">Report Date</div>
                        <div class="info-value">{{ $report->ReportDate ? \Carbon\Carbon::parse($report->ReportDate)->format('d M Y') : 'N/A' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medical Details -->
        <div class="section-title">Medical Details</div>
        <div class="detail-card">
            <div class="detail-item">
                <div class="detail-label">Diagnosis</div>
                <div class="detail-value">{{ $report->Diagnosis ?? 'N/A' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Symptoms</div>
                <div class="detail-value">{{ $report->Symptoms ?? 'N/A' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Findings</div>
                <div class="detail-value">{{ $report->Findings ?? 'N/A' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Follow-up Advices</div>
                <div class="detail-value">{{ $report->FollowUpAdvices ?? 'N/A' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Notes</div>
                <div class="detail-value">{{ $report->Notes ?? 'N/A' }}</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><span class="badge">Official Document</span></p>
            <p>Generated on {{ now()->format('d M Y, h:i A') }}</p>
        </div>
    </div>
</body>
</html>
