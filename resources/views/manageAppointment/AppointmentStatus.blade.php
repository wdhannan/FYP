@extends('layouts.app')

@section('title', 'Appointment Status - Digital Child Health Record System')

@section('content')
    @php
        $appointments = $appointments ?? [];
    @endphp

    <style>
        body {
            background: linear-gradient(135deg, #ffeef4 0%, #fff5f8 50%, #ffeef4 100%);
            min-height: 100vh;
        }

        .appointment-status-wrapper {
            padding: 40px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .appointment-status-header {
            margin-bottom: 40px;
        }

        .appointment-status-title {
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
            grid-template-columns: 1fr 1fr 1fr 1fr 1fr;
            gap: 20px;
            align-items: start;
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
            white-space: nowrap;
        }

        .cancel-appointment-btn {
            padding: 10px 24px;
            background: linear-gradient(135deg, #f44336 0%, #e53935 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(244, 67, 54, 0.3);
            white-space: nowrap;
        }

        .cancel-appointment-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(244, 67, 54, 0.4);
            background: linear-gradient(135deg, #e53935 0%, #d32f2f 100%);
        }

        .cancel-appointment-btn:active {
            transform: translateY(0);
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

        .status-pending {
            background: linear-gradient(135deg, #FFEB3B 0%, #FFF59D 100%);
            color: #000;
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

        .success-message {
            background: linear-gradient(135deg, #4CAF50 0%, #66BB6A 100%);
            color: white;
            padding: 16px 24px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 15px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(76, 175, 80, 0.2);
        }

        .error-message {
            background: linear-gradient(135deg, #EF5350 0%, #E57373 100%);
            color: white;
            padding: 16px 24px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 15px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(239, 83, 80, 0.2);
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

            .cancel-appointment-btn {
                width: 100%;
                max-width: 200px;
            }
        }
    </style>

    <div class="appointment-status-wrapper">
        <div class="appointment-status-header">
            <h2 class="appointment-status-title">📋 Appointment Status</h2>
        </div>

        @if(count($appointments) > 0)
            <div class="appointment-list">
                @foreach($appointments as $appointment)
                    @php
                        $childName = $appointment->ChildName ?? ($appointment['ChildName'] ?? 'N/A');
                        $doctorName = $appointment->DoctorName ?? ($appointment['DoctorName'] ?? 'N/A');
                        $date = isset($appointment->date) || isset($appointment['date']) 
                            ? \Carbon\Carbon::parse($appointment->date ?? $appointment['date'])->format('F j, Y') 
                            : 'N/A';
                        $time = $appointment->time ?? ($appointment['time'] ?? 'N/A');
                        $appointmentId = $appointment->AppointmentID ?? ($appointment['AppointmentID'] ?? 'N/A');
                        $status = strtolower($appointment->status ?? ($appointment['status'] ?? 'pending'));
                        
                        // Map status for display
                        $displayStatus = $status;
                        if ($status === 'approved') {
                            $displayStatus = 'approved';
                        } elseif ($status === 'rejected' || $status === 'reject') {
                            $displayStatus = 'rejected';
                        } elseif ($status === 'cancelled') {
                            $displayStatus = 'cancelled';
                        } elseif ($status === 'confirmed') {
                            $displayStatus = 'confirmed';
                        }
                    @endphp
                    <div class="appointment-card">
                        <div class="appointment-details">
                            @if($appointmentId !== 'N/A')
                                <div class="appointment-field">
                                    <span class="appointment-label">🆔 APPOINTMENT ID</span>
                                    <span class="appointment-value">{{ $appointmentId }}</span>
                                </div>
                            @endif
                            @if($childName !== 'N/A')
                                <div class="appointment-field">
                                    <span class="appointment-label">👶 CHILD NAME</span>
                                    <span class="appointment-value">{{ $childName }}</span>
                                </div>
                            @endif
                            @if($doctorName !== 'N/A')
                                <div class="appointment-field">
                                    <span class="appointment-label">👨‍⚕️ DOCTOR NAME</span>
                                    <span class="appointment-value">{{ $doctorName }}</span>
                                </div>
                            @endif
                            <div class="appointment-field">
                                <span class="appointment-label">🕒 TIME</span>
                                <span class="appointment-value">{{ $time }}</span>
                            </div>
                            <div class="appointment-field">
                                <span class="appointment-label">📅 DATE</span>
                                <span class="appointment-value">{{ $date }}</span>
                            </div>
                        </div>
                        <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 12px;">
                            <div class="appointment-status status-{{ $displayStatus }}">
                                {{ strtoupper($appointment->status ?? ($appointment['status'] ?? 'PENDING')) }}
                            </div>
                            @if(strtolower($status) === 'pending')
                                <form method="POST" action="{{ route('appointment.update.status', $appointmentId) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to cancel this appointment?');">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="cancelled">
                                    <button type="submit" class="cancel-appointment-btn">Cancel</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">📋</div>
                <div class="empty-state-text">No appointments available at this time.</div>
            </div>
        @endif
    </div>
@endsection
