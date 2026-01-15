@extends('layouts.app')

@section('title', 'Parent Dashboard - Digital Child Health Record System')

@section('content')
    @php
        $parentName = $parentName ?? 'Parent';
        $children = $children ?? collect([]);
        $upcomingAppointments = $upcomingAppointments ?? collect([]);
        $recentUpdates = $recentUpdates ?? collect([]);
        $healthOverview = $healthOverview ?? [
            'weight' => 'N/A',
            'height' => 'N/A',
            'immunizations' => '0 completed',
        ];
    @endphp

    <style>
        body {
            background: linear-gradient(135deg, #ffeef4 0%, #fff5f8 50%, #ffeef4 100%);
            min-height: 100vh;
        }

        .parent-dashboard {
            padding: 40px 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .dashboard-header {
            margin-bottom: 40px;
        }

        .dashboard-title {
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

        /* Health Overview Card */
        .health-overview-card {
            background: linear-gradient(135deg, #ffffff 0%, #fff8fa 100%);
            border: 2px solid #ffe0e8;
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 40px;
            box-shadow: 0 4px 15px rgba(255, 158, 179, 0.1);
            position: relative;
            overflow: hidden;
        }

        .health-overview-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
        }

        .health-overview-title {
            font-size: 24px;
            font-weight: 800;
            color: #1a1a1a;
            margin-bottom: 24px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .health-overview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .overview-item {
            text-align: center;
            padding: 20px;
            background: linear-gradient(135deg, #fff5f5 0%, #ffe0e9 100%);
            border-radius: 12px;
            border: 1px solid #ffe0e8;
            transition: all 0.3s ease;
        }

        .overview-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 111, 145, 0.15);
        }

        .overview-label {
            font-size: 13px;
            color: #666;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .overview-value {
            font-size: 24px;
            font-weight: 800;
            color: #1a1a1a;
        }

        /* Dashboard Content Grid */
        .dashboard-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }

        .dashboard-card {
            background: linear-gradient(135deg, #ffffff 0%, #fff8fa 100%);
            border: 2px solid #ffe0e8;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 4px 15px rgba(255, 158, 179, 0.1);
            position: relative;
            overflow: hidden;
        }

        .dashboard-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
        }

        .card-title {
            font-size: 24px;
            font-weight: 800;
            color: #1a1a1a;
            margin-bottom: 24px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .list-item {
            padding: 16px 20px;
            margin-bottom: 12px;
            background: linear-gradient(135deg, #fff5f5 0%, #ffe0e9 100%);
            border-radius: 12px;
            border-left: 4px solid #ff6f91;
            transition: all 0.3s ease;
        }

        .list-item:hover {
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(255, 111, 145, 0.15);
        }

        .list-item:last-child {
            margin-bottom: 0;
        }

        .item-title {
            font-size: 15px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 6px;
        }

        .item-details {
            font-size: 14px;
            color: #666;
            margin-bottom: 6px;
        }

        .item-date {
            font-size: 12px;
            color: #999;
            font-weight: 500;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #999;
            font-size: 16px;
            font-weight: 500;
        }

        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 12px;
            opacity: 0.5;
        }

        @media (max-width: 768px) {
            .parent-dashboard {
                padding: 20px;
            }

            .dashboard-content {
                grid-template-columns: 1fr;
            }

            .health-overview-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="parent-dashboard">
        <div class="dashboard-header">
            <h1 class="dashboard-title">Welcome, <strong>{{ $parentName }}</strong>! 👨‍👩‍👧‍👦</h1>
        </div>

        <!-- Health Overview -->
        <div class="health-overview-card">
            <h2 class="health-overview-title">📊 Health Overview</h2>
            @if(isset($healthOverview['childCount']) && $healthOverview['childCount'] > 1 && isset($healthOverview['latestChildName']))
                <div style="font-size: 12px; color: #666; margin-bottom: 16px; font-style: italic;">
                    Latest measurements from: <strong>{{ $healthOverview['latestChildName'] }}</strong>
                </div>
            @endif
            <div class="health-overview-grid">
                <div class="overview-item">
                    <div class="overview-label">Weight Status</div>
                    <div class="overview-value" style="color: {{ $healthOverview['weightStatus']['color'] ?? '#1a1a1a' }};">
                        {{ $healthOverview['weightStatus']['label'] ?? 'N/A' }}
                    </div>
                    @if(isset($healthOverview['weight']) && $healthOverview['weight'] !== 'N/A')
                        <div style="font-size: 18px; color: #1a1a1a; margin-top: 8px; font-weight: 700;">{{ $healthOverview['weight'] }}</div>
                    @endif
                </div>
                <div class="overview-item">
                    <div class="overview-label">Height Status</div>
                    <div class="overview-value" style="color: {{ $healthOverview['heightStatus']['color'] ?? '#1a1a1a' }};">
                        {{ $healthOverview['heightStatus']['label'] ?? 'N/A' }}
                    </div>
                    @if(isset($healthOverview['height']) && $healthOverview['height'] !== 'N/A')
                        <div style="font-size: 18px; color: #1a1a1a; margin-top: 8px; font-weight: 700;">{{ $healthOverview['height'] }}</div>
                    @endif
                </div>
                <div class="overview-item">
                    <div class="overview-label">Growth Status</div>
                    <div class="overview-value" style="color: {{ $healthOverview['overallGrowthStatus']['color'] ?? '#1a1a1a' }};">
                        {{ ($healthOverview['overallGrowthStatus']['icon'] ?? '') . ' ' . ($healthOverview['overallGrowthStatus']['label'] ?? 'N/A') }}
                    </div>
                    <div style="font-size: 16px; color: #1a1a1a; margin-top: 8px; font-weight: 600;">{{ $healthOverview['immunizations'] }}</div>
                    @if(isset($healthOverview['childCount']) && $healthOverview['childCount'] > 1)
                        <div style="font-size: 11px; color: #999; margin-top: 2px;">Across all children</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div class="dashboard-content">
            <!-- Upcoming Appointments -->
            <div class="dashboard-card">
                <h2 class="card-title">📅 Upcoming Appointments</h2>
                @if($upcomingAppointments->count() > 0)
                    @foreach($upcomingAppointments as $appointment)
                        <div class="list-item">
                            <div class="item-title">{{ $appointment->ChildName ?? 'Child' }}</div>
                            <div class="item-details">{{ $appointment->DoctorName ?? 'Doctor' }}</div>
                            <div class="item-date">
                                {{ \Carbon\Carbon::parse($appointment->date)->format('M d, Y') }} at 
                                {{ \Carbon\Carbon::parse($appointment->time)->format('h:i A') }}
                            </div>
                            @if(isset($appointment->status))
                                <div class="item-details" style="margin-top: 4px;">
                                    <span style="padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; text-transform: uppercase; 
                                        background: {{ strtolower($appointment->status) === 'approved' ? '#4CAF50' : '#FFEB3B' }};
                                        color: {{ strtolower($appointment->status) === 'approved' ? 'white' : '#000' }};
                                    ">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon">📭</div>
                        No upcoming appointments
                    </div>
                @endif
            </div>

            <!-- Recent Health Updates -->
            <div class="dashboard-card">
                <h2 class="card-title">🔄 Latest Health Update</h2>
                @if($recentUpdates->count() > 0)
                    @php
                        $latestUpdate = $recentUpdates->first();
                    @endphp
                    <div class="list-item">
                        <div class="item-title">{{ $latestUpdate['type'] ?? 'Update' }}</div>
                        <div class="item-details">{{ $latestUpdate['description'] ?? 'No description' }}</div>
                        <div class="item-date">
                            {{ \Carbon\Carbon::parse($latestUpdate['date'])->format('M d, Y') }}
                        </div>
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon">📋</div>
                        No recent updates
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection
