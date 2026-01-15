@extends('layouts.app')

@section('title', 'Appointment Request - Digital Child Health Record System')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #ffeef4 0%, #fff5f8 50%, #ffeef4 100%);
        min-height: 100vh;
    }

    .page-wrapper {
        padding: 40px 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .page-header {
        margin-bottom: 40px;
        text-align: center;
    }

    .page-title {
        font-size: 32px;
        font-weight: 800;
        color: #1a1a1a;
        text-transform: uppercase;
        letter-spacing: 1px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    .appointments-request-list {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    
    .appointment-request-card {
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

    .appointment-request-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 100%;
        background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
    }

    .appointment-request-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(255, 158, 179, 0.2);
        border-color: #ff9eb3;
    }
    
    .request-info {
        flex: 1;
        display: grid;
        grid-template-columns: 1fr 1fr 1fr 1fr 1fr;
        gap: 20px;
        align-items: start;
        padding-left: 8px;
    }
    
    .request-row {
        display: flex;
        flex-direction: column;
        gap: 10px;
        min-height: 60px;
    }
    
    .request-label {
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

    .request-label::before {
        content: '▸';
        color: #1a1a1a;
        font-size: 14px;
        flex-shrink: 0;
    }
    
    .request-value {
        font-size: 16px;
        color: #1a1a1a;
        font-weight: 600;
        padding-left: 0;
        line-height: 1.4;
        word-wrap: break-word;
    }
    
    .request-actions {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-left: 20px;
        align-self: flex-start;
        min-width: 140px;
    }
    
    .approve-btn {
        padding: 12px 24px;
        background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
        white-space: nowrap;
        width: 100%;
    }
    
    .approve-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
        background: linear-gradient(135deg, #45a049 0%, #3d8b40 100%);
    }
    
    .approve-btn:active {
        transform: translateY(0);
    }
    
    .reject-btn {
        padding: 12px 24px;
        background: linear-gradient(135deg, #f44336 0%, #e53935 100%);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(244, 67, 54, 0.3);
        white-space: nowrap;
        width: 100%;
    }
    
    .reject-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(244, 67, 54, 0.4);
        background: linear-gradient(135deg, #e53935 0%, #d32f2f 100%);
    }
    
    .reject-btn:active {
        transform: translateY(0);
    }
    
    .empty-state {
        text-align: center;
        padding: 80px 20px;
        background: linear-gradient(135deg, #fff5f7 0%, #ffeef0 50%, #ffe5e8 100%);
        border-radius: 20px;
        box-shadow: 0 8px 24px rgba(255, 111, 145, 0.15);
    }
    
    .empty-state-icon {
        font-size: 64px;
        margin-bottom: 20px;
        opacity: 0.5;
    }
    
    .empty-state-text {
        color: #999;
        font-size: 16px;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .appointment-request-card {
            flex-direction: column;
            gap: 20px;
        }

        .request-info {
            grid-template-columns: 1fr;
            padding-left: 0;
        }

        .request-actions {
            margin-left: 0;
            width: 100%;
            flex-direction: row;
        }

        .approve-btn,
        .reject-btn {
            flex: 1;
        }
    }
</style>

<div class="page-wrapper">
    <div class="page-header">
        <h1 class="page-title">📋 Appointment Requests</h1>
    </div>
    
    <div class="appointments-request-list">
        @php
            $appointmentRequests = $appointmentRequests ?? [];
        @endphp
        
        @if(count($appointmentRequests) > 0)
            @foreach($appointmentRequests as $request)
                @php
                    $appointmentId = $request->AppointmentID ?? 'N/A';
                    $childName = $request->ChildName ?? 'N/A';
                    $doctorName = $request->DoctorName ?? 'N/A';
                    $date = isset($request->date) ? \Carbon\Carbon::parse($request->date)->format('F j, Y') : 'N/A';
                    $time = 'N/A';
                    if (isset($request->time)) {
                        $timeValue = $request->time;
                        if (preg_match('/\d{1,2}:\d{2}\s*(AM|PM)/i', $timeValue)) {
                            $time = strtoupper($timeValue);
                        } else {
                            try {
                                $time = \Carbon\Carbon::parse($timeValue)->format('h:i A');
                            } catch (\Exception $e) {
                                $time = strtoupper($timeValue);
                            }
                        }
                    }
                @endphp
                <div class="appointment-request-card">
                    <div class="request-info">
                        @if($appointmentId !== 'N/A')
                            <div class="request-row">
                                <span class="request-label">🆔 APPOINTMENT ID</span>
                                <span class="request-value">{{ $appointmentId }}</span>
                            </div>
                        @endif
                        @if($childName !== 'N/A')
                            <div class="request-row">
                                <span class="request-label">👶 CHILD NAME</span>
                                <span class="request-value">{{ $childName }}</span>
                            </div>
                        @endif
                        @if($doctorName !== 'N/A')
                            <div class="request-row">
                                <span class="request-label">👨‍⚕️ DOCTOR NAME</span>
                                <span class="request-value">{{ $doctorName }}</span>
                            </div>
                        @endif
                        <div class="request-row">
                            <span class="request-label">🕒 TIME</span>
                            <span class="request-value">{{ $time }}</span>
                        </div>
                        <div class="request-row">
                            <span class="request-label">📅 DATE</span>
                            <span class="request-value">{{ $date }}</span>
                        </div>
                    </div>
                    <div class="request-actions">
                        <form method="POST" action="{{ route('appointment.update.status', $request->AppointmentID) }}" style="display: inline;">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" class="approve-btn">Approve</button>
                        </form>
                        <form method="POST" action="{{ route('appointment.update.status', $request->AppointmentID) }}" style="display: inline;">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="reject-btn">Reject</button>
                        </form>
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <div class="empty-state-icon">📋</div>
                <div class="empty-state-text">No appointment requests available at this time.</div>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const approveForms = document.querySelectorAll('form[action*="appointment.update.status"] input[value="approved"]');
        approveForms.forEach(input => {
            const form = input.closest('form');
            form.addEventListener('submit', function(e) {
                if (!confirm('Are you sure you want to APPROVE this appointment?')) {
                    e.preventDefault();
                }
            });
        });
        
        const rejectForms = document.querySelectorAll('form[action*="appointment.update.status"] input[value="rejected"]');
        rejectForms.forEach(input => {
            const form = input.closest('form');
            form.addEventListener('submit', function(e) {
                if (!confirm('Are you sure you want to REJECT this appointment?')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endsection
