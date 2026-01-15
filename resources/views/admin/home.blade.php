@extends('layouts.app')

@section('title', 'Home - Digital Child Health Record System')

@section('content')
<style>
    .admin-dashboard {
        padding: 40px;
        max-width: 1400px;
        margin: 0 auto;
    }

    .welcome-text {
        font-size: 32px;
        font-weight: bold;
        color: black;
        text-transform: uppercase;
        line-height: 1.8;
        letter-spacing: 1px;
        text-align: center;
        margin-bottom: 50px;
    }

    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-top: 30px;
    }

    .stat-card {
        background-color: #fff5f5;
        border-radius: 12px;
        padding: 30px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        box-shadow: 0 2px 8px rgba(255, 111, 145, 0.1);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid #ffe0e9;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 111, 145, 0.2);
    }

    .stat-info {
        flex: 1;
    }

    .stat-label {
        font-size: 14px;
        color: #999;
        font-weight: 400;
        margin-bottom: 4px;
    }

    .stat-label-main {
        font-size: 14px;
        color: #999;
        font-weight: 400;
        margin-bottom: 12px;
    }

    .stat-value {
        font-size: 42px;
        font-weight: 700;
        color: #333;
        line-height: 1;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(255, 111, 145, 0.3);
    }

    .stat-icon svg {
        width: 32px;
        height: 32px;
        color: white;
    }

    @media (max-width: 768px) {
        .admin-dashboard {
            padding: 20px;
        }

        .stats-container {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .stat-value {
            font-size: 36px;
        }
    }
</style>

<div class="admin-dashboard">
    <div class="welcome-text">
        WELCOME ADMIN<br>
        TO THE DIGITAL CHILD HEALTH SYSTEM
    </div>

    <div class="stats-container">
        <!-- Total Doctors Card -->
        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">Total</div>
                <div class="stat-label-main">Doctor</div>
                <div class="stat-value">{{ $totalDoctors ?? 0 }}</div>
            </div>
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
        </div>

        <!-- Total Nurses Card -->
        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-label">Total</div>
                <div class="stat-label-main">Nurse</div>
                <div class="stat-value">{{ $totalNurses ?? 0 }}</div>
            </div>
            <div class="stat-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
        </div>
    </div>
</div>
@endsection
