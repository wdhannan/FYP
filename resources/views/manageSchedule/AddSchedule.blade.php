@extends('layouts.app')

@section('title', 'Add Schedule')

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
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .schedule-form-wrapper {
            background: linear-gradient(135deg, #fff5f7 0%, #ffeef0 50%, #ffe5e8 100%);
            border-radius: 24px;
            padding: 50px;
            margin: 0 auto;
            max-width: 700px;
            box-shadow: 0 20px 60px rgba(255, 111, 145, 0.15), 0 0 0 1px rgba(255, 182, 193, 0.1);
        }

        .form-section {
            margin-bottom: 32px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.8px;
            color: #1a1a1a;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .form-input {
            width: 100%;
            padding: 16px 20px;
            border-radius: 12px;
            border: 2px solid #ffe5e8;
            background: linear-gradient(to bottom, #fff, #fff8f8);
            font-size: 15px;
            font-family: inherit;
            color: #1a1a1a;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #ff6f91;
            box-shadow: 0 0 0 4px rgba(255, 111, 145, 0.1);
            background: white;
        }

        .file-upload-section {
            position: relative;
        }

        .file-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .file-input {
            padding: 12px 18px;
            border: 2px solid #ffc0cb;
            border-radius: 8px;
            font-size: 15px;
            color: #333;
            background-color: #fff;
            flex-grow: 1;
            max-width: 400px;
            box-shadow: 0 2px 8px rgba(255, 111, 145, 0.1);
            transition: all 0.3s ease;
        }

        .file-input:focus {
            border-color: #ff6f91;
            box-shadow: 0 0 0 3px rgba(255, 111, 145, 0.3);
            outline: none;
        }

        .upload-icon-btn {
            background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
            border: none;
            cursor: pointer;
            padding: 12px 18px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(255, 111, 145, 0.2);
        }

        .upload-icon-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 111, 145, 0.3);
            background: linear-gradient(135deg, #ff4d79 0%, #ff6f91 100%);
        }

        .upload-icon {
            width: 24px;
            height: 24px;
            color: white;
        }

        .file-name-display {
            margin-top: 12px;
            padding: 12px 16px;
            background: rgba(255, 111, 145, 0.1);
            border-radius: 8px;
            font-size: 14px;
            color: #ff6f91;
            font-weight: 600;
            display: none;
        }

        .file-name-display.show {
            display: block;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 40px;
            padding-top: 32px;
            border-top: 2px solid rgba(255, 182, 193, 0.3);
        }

        .submit-btn {
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

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(255, 111, 145, 0.4);
        }

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        @media (max-width: 768px) {
            .schedule-form-wrapper {
                padding: 30px 20px;
            }

            .page-title {
                font-size: 24px;
            }

            .file-input-wrapper {
                flex-direction: column;
                align-items: stretch;
            }

            .file-input {
                max-width: 100%;
            }
        }
    </style>

    <div class="page-wrapper">
        <div class="page-header">
            <h1 class="page-title">📅 Add Schedule</h1>
        </div>

        <div class="schedule-form-wrapper">
            <form id="scheduleForm" action="{{ route('schedule.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="form-section">
                    <label class="form-label" for="upload_date">Schedule Date</label>
                    <input type="date" id="upload_date" name="upload_date" class="form-input" required>
                </div>

                <div class="form-section">
                    <label class="form-label">Schedule File</label>
                    <div class="file-upload-section">
                        <div class="file-input-wrapper">
                            <input type="text" class="file-input" id="fileDisplay" placeholder="No file chosen" readonly>
                            <label for="schedule_file" class="upload-icon-btn">
                                <svg class="upload-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                            </label>
                            <input type="file" id="schedule_file" name="schedule_file" accept=".png,.jpg,.jpeg,image/png,image/jpeg" style="display:none" required>
                        </div>
                        <div class="file-name-display" id="fileNameDisplay"></div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="submit-btn" id="submitBtn">Upload Schedule</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const fileInput = document.getElementById('schedule_file');
        const fileDisplay = document.getElementById('fileDisplay');
        const fileNameDisplay = document.getElementById('fileNameDisplay');
        const submitBtn = document.getElementById('submitBtn');

        fileInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                fileDisplay.value = file.name;
                fileNameDisplay.textContent = `Selected: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
                fileNameDisplay.classList.add('show');
            } else {
                fileDisplay.value = '';
                fileNameDisplay.classList.remove('show');
            }
        });

        document.getElementById('scheduleForm').addEventListener('submit', function(e) {
            const uploadDate = document.getElementById('upload_date').value;
            const scheduleFile = fileInput.files[0];
            
            if (!uploadDate) {
                e.preventDefault();
                return;
            }
            
            if (!scheduleFile) {
                e.preventDefault();
                return;
            }
            
            // Check file type
            const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
            const allowedExtensions = /\.(png|jpg|jpeg)$/i;
            if (!allowedTypes.includes(scheduleFile.type) && !allowedExtensions.test(scheduleFile.name)) {
                e.preventDefault();
                return;
            }
            
            // Check file size (10MB max)
            if (scheduleFile.size > 10 * 1024 * 1024) {
                e.preventDefault();
                return;
            }
            
            // Disable button to prevent double submission
            submitBtn.disabled = true;
            submitBtn.textContent = 'Uploading...';
        });
    </script>
@endsection
