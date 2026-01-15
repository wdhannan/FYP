@extends('layouts.app')

@section('title', 'Appointment History')

@section('content')
    @php
        $appointments = $appointments ?? [];
    @endphp

    <style>
        body {
            background: linear-gradient(135deg, #ffeef4 0%, #fff5f8 50%, #ffeef4 100%);
            min-height: 100vh;
        }

        .appointment-history-wrapper {
            padding: 40px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .appointment-history-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .appointment-history-title {
            font-size: 36px;
            font-weight: 800;
            color: #1a1a1a;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .appointment-history-subtitle {
            color: #1a1a1a;
            font-size: 16px;
            font-weight: 500;
        }

        .appointment-list {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .appointment-card {
            background: linear-gradient(135deg, #ffffff 0%, #fff8fa 100%);
            border: 2px solid #ffe0e8;
            border-radius: 16px;
            padding: 28px 32px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 158, 179, 0.1);
            position: relative;
            overflow: hidden;
        }

        .appointment-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
        }

        .appointment-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(255, 158, 179, 0.2);
            border-color: #ff9eb3;
        }

        .appointment-details {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 20px;
            align-items: start;
        }
        
        .date-time-container {
            grid-column: span 2;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .appointment-field {
            display: flex;
            flex-direction: column;
            gap: 10px;
            min-height: 60px;
        }

        .appointment-label {
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            color: #1a1a1a;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 8px;
            height: 20px;
        }

        .appointment-label::before {
            content: '▸';
            color: #1a1a1a;
            font-size: 14px;
            flex-shrink: 0;
        }

        .appointment-value {
            font-size: 16px;
            color: #1a1a1a;
            font-weight: 600;
            padding-left: 0;
            line-height: 1.4;
            word-wrap: break-word;
        }

        .appointment-status {
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            padding: 10px 20px;
            border-radius: 20px;
            align-self: flex-start;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-left: 20px;
            white-space: nowrap;
        }

        .status-pending {
            background: linear-gradient(135deg, #FFEB3B 0%, #FFF59D 100%);
            color: #000;
        }

        .status-confirmed {
            background: linear-gradient(135deg, #81C784 0%, #A5D6A7 100%);
            color: #000;
        }

        .status-approved,
        .status-completed {
            background: linear-gradient(135deg, #4CAF50 0%, #66BB6A 100%);
            color: #fff;
        }

        .status-rejected,
        .status-cancelled {
            background: linear-gradient(135deg, #EF5350 0%, #E57373 100%);
            color: #fff;
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: linear-gradient(135deg, #ffffff 0%, #fff8fa 100%);
            border-radius: 16px;
            border: 2px dashed #ffe0e8;
        }

        .empty-state-icon {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-state-text {
            color: #1a1a1a;
            font-size: 18px;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .appointment-card {
                flex-direction: column;
                gap: 20px;
            }

            .appointment-status {
                align-self: flex-end;
            }

            .appointment-details {
                grid-template-columns: 1fr;
            }

            .appointment-status {
                margin-left: 0;
                align-self: flex-end;
            }
        }
    </style>

    <div class="appointment-history-wrapper">
        <div class="appointment-history-header">
            <h2 class="appointment-history-title">📅 Appointment History</h2>
        </div>

        @if(count($appointments) > 0)
            <div class="appointment-list">
                @foreach($appointments as $appointment)
                    @php
                        $doctorName = $appointment->DoctorName ?? 'N/A';
                        $childName = $appointment->ChildName ?? 'N/A';
                        $date = isset($appointment->date) ? \Carbon\Carbon::parse($appointment->date)->format('F j, Y') : 'N/A';
                        $time = $appointment->time ?? 'N/A';
                        $status = $appointment->status ?? 'N/A';
                        
                        // Map "approved" to "completed" for display
                        $displayStatus = strtolower($status) === 'approved' ? 'completed' : strtolower($status);
                    @endphp
                    <div class="appointment-card">
                        <div class="appointment-details">
                            <div class="appointment-field">
                                <span class="appointment-label">👶 CHILD NAME</span>
                                <span class="appointment-value">{{ $childName }}</span>
                            </div>
                            <div class="appointment-field">
                                <span class="appointment-label">👨‍⚕️ DOCTOR NAME</span>
                                <span class="appointment-value">{{ $doctorName }}</span>
                            </div>
                            <div class="date-time-container">
                                <div class="appointment-field">
                                    <span class="appointment-label">📅 DATE</span>
                                    <span class="appointment-value">{{ $date }}</span>
                                </div>
                                <div class="appointment-field">
                                    <span class="appointment-label">🕒 TIME</span>
                                    <span class="appointment-value">{{ $time }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="appointment-status status-{{ $displayStatus }}">
                            {{ strtoupper($displayStatus) }}
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">📋</div>
                <div class="empty-state-text">No appointment history available.</div>
            </div>
        @endif
    </div>
@endsection
