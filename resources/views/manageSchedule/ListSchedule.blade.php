@extends('layouts.app')

@section('title', 'My Schedules')

@section('content')
    <style>
        body {
            background: linear-gradient(135deg, #ffeef4 0%, #fff5f8 50%, #ffeef4 100%);
            min-height: 100vh;
        }

        .page-wrapper {
            padding: 40px 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        .page-title {
            font-size: 24px;
            font-weight: 800;
            color: #1a1a1a;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-align: center;
            margin-bottom: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .section-card {
            background: linear-gradient(135deg, #fff5f7 0%, #ffeef0 50%, #ffe5e8 100%);
            border-radius: 16px;
            padding: 25px 30px;
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            flex: 1;
        }

        .form-label {
            display: block;
            font-size: 10px;
            font-weight: 600;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 14px 20px;
            border: none;
            border-radius: 30px;
            background: linear-gradient(135deg, #ffe0e9 0%, #fff0f5 50%, #ffe0e9 100%);
            font-size: 14px;
            color: #333;
            box-shadow: 0 2px 8px rgba(255, 111, 145, 0.1);
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 111, 145, 0.2);
        }

        .form-input::placeholder {
            color: #999;
        }

        .file-input {
            width: 100%;
            padding: 12px 20px;
            border: none;
            border-radius: 30px;
            background: linear-gradient(135deg, #ffe0e9 0%, #fff0f5 50%, #ffe0e9 100%);
            font-size: 13px;
            color: #666;
            box-shadow: 0 2px 8px rgba(255, 111, 145, 0.1);
            transition: all 0.3s ease;
        }

        .file-input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 111, 145, 0.2);
        }

        .file-input::file-selector-button {
            padding: 8px 16px;
            border: none;
            border-radius: 20px;
            background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
            color: white;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            margin-right: 10px;
            transition: all 0.3s ease;
        }

        .file-input::file-selector-button:hover {
            background: linear-gradient(135deg, #ff5580 0%, #ff8fa8 100%);
        }

        .upload-btn {
            width: 100%;
            padding: 12px 20px;
            background: linear-gradient(135deg, #ffb6c1 0%, #ffc0cb 100%);
            color: #333;
            border: none;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .upload-btn:hover {
            background: linear-gradient(135deg, #ff9eb3 0%, #ffb6c1 100%);
            transform: translateY(-1px);
        }

        .schedule-item {
            background: #fff;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 12px;
            display: flex;
            gap: 15px;
            align-items: flex-start;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
        }

        .schedule-image {
            width: 120px;
            height: 80px;
            border-radius: 6px;
            overflow: hidden;
            flex-shrink: 0;
            background: #f9f9f9;
        }

        .schedule-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .schedule-info {
            flex: 1;
        }

        .schedule-id {
            font-size: 10px;
            font-weight: 600;
            color: #ff6f91;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 4px;
        }

        .schedule-date {
            font-size: 12px;
            color: #888;
            margin-bottom: 10px;
        }

        .view-schedule-btn {
            display: inline-block;
            padding: 6px 16px;
            background: linear-gradient(135deg, #ffb6c1 0%, #ffc0cb 100%);
            color: #333;
            border-radius: 15px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .view-schedule-btn:hover {
            background: linear-gradient(135deg, #ff9eb3 0%, #ffb6c1 100%);
        }

        .empty-state {
            text-align: center;
            padding: 30px 20px;
            color: #999;
            font-size: 13px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        @media (max-width: 600px) {
            .form-row {
                flex-direction: column;
            }

            .schedule-item {
                flex-direction: column;
            }

            .schedule-image {
                width: 100%;
                height: 120px;
            }
        }
    </style>

    <div class="page-wrapper">
        <h1 class="page-title">📅 My Schedules</h1>

        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert-error">{{ session('error') }}</div>
        @endif

        <!-- Add New Schedule Section -->
        <div class="section-card">
            <div class="section-title">+ ADD NEW SCHEDULE</div>
            <form action="{{ route('schedule.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Schedule Date</label>
                        <input type="date" name="upload_date" class="form-input" placeholder="dd/mm/yyyy" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Schedule File</label>
                        <input type="file" name="schedule_file" class="file-input" accept="image/*,.pdf" required>
                    </div>
                </div>
                <button type="submit" class="upload-btn">Upload Schedule</button>
            </form>
        </div>

        <!-- Uploaded Schedules Section -->
        <div class="section-card">
            <div class="section-title">📋 UPLOADED SCHEDULES</div>

            @if(isset($schedules) && $schedules->count() > 0)
                @php
                    $schedule = $schedules->first();
                @endphp
                <div class="schedule-item">
                    @if($schedule->FileName)
                        @php
                            $extension = pathinfo($schedule->FileName, PATHINFO_EXTENSION);
                            $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        @endphp
                        <div class="schedule-image">
                            @if($isImage)
                                <img src="{{ asset('storage/schedules/' . $schedule->FileName) }}" alt="Schedule">
                            @else
                                <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #ccc; font-size: 24px;">
                                    📄
                                </div>
                            @endif
                        </div>
                    @endif
                    <div class="schedule-info">
                        <div class="schedule-id">{{ $schedule->ScheduleID ?? 'N/A' }}</div>
                        <div class="schedule-date">{{ $schedule->UploadDate ? \Carbon\Carbon::parse($schedule->UploadDate)->format('F d, Y') : 'N/A' }}</div>
                        <a href="{{ route('schedule.edit', $schedule->ScheduleID) }}" class="view-schedule-btn">Update Schedule</a>
                    </div>
                </div>
            @else
                <div class="empty-state">
                    No schedules uploaded yet
                </div>
            @endif
        </div>
    </div>
@endsection
