@extends('layouts.app')

@section('title', 'Booking Form - Digital Child Health Record System')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #ffeef4 0%, #fff5f8 50%, #ffeef4 100%);
        min-height: 100vh;
    }

    .content-wrapper {
        padding: 40px 20px;
        max-width: 1000px;
        margin: 0 auto;
    }

    .booking-title {
        font-size: 36px;
        font-weight: 800;
        color: #1a1a1a;
        text-align: center;
        margin-bottom: 40px;
        text-transform: uppercase;
        letter-spacing: 1px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    .booking-form-container {
        background: linear-gradient(135deg, #fff5f7 0%, #ffeef0 50%, #ffe5e8 100%);
        border-radius: 24px;
        padding: 50px;
        box-shadow: 0 20px 60px rgba(255, 111, 145, 0.15);
        margin-bottom: 30px;
    }

    .form-group {
        margin-bottom: 30px;
    }

    .form-label {
        display: block;
        font-weight: 700;
        font-size: 13px;
        letter-spacing: 0.8px;
        color: #1a1a1a;
        margin-bottom: 12px;
        text-transform: uppercase;
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

    .form-input:read-only {
        background: #f5f5f5;
        cursor: not-allowed;
        border-color: #e0e0e0;
    }

    .dropdown-wrapper {
        position: relative;
    }

    .form-select {
        width: 100%;
        padding: 16px 50px 16px 20px;
        border-radius: 30px;
        border: none;
        background: linear-gradient(135deg, #ffe0e9 0%, #fff0f5 50%, #ffe0e9 100%);
        font-size: 15px;
        color: #1a1a1a;
        outline: none;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23ff6f91' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 20px center;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(255, 111, 145, 0.1);
    }

    .form-select:hover {
        box-shadow: 0 4px 12px rgba(255, 111, 145, 0.2);
        transform: translateY(-1px);
    }

    .form-select:focus {
        border-color: #ff6f91;
        box-shadow: 0 0 0 4px rgba(255, 111, 145, 0.15), 0 4px 12px rgba(255, 111, 145, 0.2);
        background: linear-gradient(135deg, #ffe0e9 0%, #fff0f5 50%, #ffe0e9 100%);
    }

    .date-time-section {
        margin-top: 40px;
    }

    .section-label {
        display: block;
        font-weight: 700;
        font-size: 13px;
        letter-spacing: 0.8px;
        color: #1a1a1a;
        margin-bottom: 12px;
        text-transform: uppercase;
    }

    .date-input-wrapper {
        position: relative;
        margin-bottom: 20px;
        height: 56px;
        cursor: pointer;
        display: block;
    }

    .date-input {
        position: absolute;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
        z-index: 3;
        pointer-events: auto;
        top: 0;
        left: 0;
        background: transparent;
        border: none;
    }

    /* Completely hide default date input styling */
    .date-input::-webkit-calendar-picker-indicator {
        opacity: 0;
        position: absolute;
        width: 100%;
        height: 100%;
        cursor: pointer;
        z-index: 5;
        left: 0;
        right: 0;
    }

    /* Hide any default browser styling on date input */
    .date-input::-webkit-datetime-edit,
    .date-input::-webkit-datetime-edit-fields-wrapper,
    .date-input::-webkit-datetime-edit-text,
    .date-input::-webkit-datetime-edit-month-field,
    .date-input::-webkit-datetime-edit-day-field,
    .date-input::-webkit-datetime-edit-year-field {
        display: none !important;
        opacity: 0 !important;
        visibility: hidden !important;
    }

    .date-display {
        width: 100%;
        padding: 16px 50px 16px 20px;
        border-radius: 30px;
        border: none !important;
        background: linear-gradient(135deg, #ffe0e9 0%, #fff0f5 50%, #ffe0e9 100%) !important;
        font-size: 15px;
        color: #1a1a1a !important;
        outline: none;
        cursor: pointer;
        pointer-events: none;
        z-index: 2;
        position: relative;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(255, 111, 145, 0.1);
        font-weight: 500;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        appearance: none !important;
    }

    .date-display:hover {
        box-shadow: 0 4px 12px rgba(255, 111, 145, 0.2);
        transform: translateY(-1px);
    }

    .date-display:focus {
        border-color: #ff6f91;
        box-shadow: 0 0 0 4px rgba(255, 111, 145, 0.15), 0 4px 12px rgba(255, 111, 145, 0.2);
        background: linear-gradient(135deg, #ffe0e9 0%, #fff0f5 50%, #ffe0e9 100%);
    }

    .date-display::placeholder {
        color: #999;
        opacity: 0.7;
    }

    /* Ensure the date display always maintains its styled appearance - regardless of value */
    .date-display[readonly],
    .date-display {
        background: linear-gradient(135deg, #ffe0e9 0%, #fff0f5 50%, #ffe0e9 100%) !important;
        border-radius: 30px !important;
        border: none !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        appearance: none !important;
        box-shadow: 0 2px 8px rgba(255, 111, 145, 0.1) !important;
    }

    /* When date has value, make text bolder but keep styling */
    .date-display:not(:placeholder-shown),
    .date-display[value]:not([value=""]) {
        font-weight: 600 !important;
        color: #1a1a1a !important;
        background: linear-gradient(135deg, #ffe0e9 0%, #fff0f5 50%, #ffe0e9 100%) !important;
        border-radius: 30px !important;
        border: none !important;
    }

    .calendar-icon {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        width: 24px;
        height: 24px;
        color: #ff6f91;
        cursor: pointer;
        pointer-events: none;
        z-index: 10;
        transition: all 0.3s ease;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    .date-input-wrapper:hover .calendar-icon {
        color: #ff4d79;
        transform: translateY(-50%) scale(1.15);
    }

    .error-message {
        color: #d32f2f;
        font-size: 12px;
        margin-top: 8px;
        display: block;
    }

    .time-slots-section {
        margin-top: 30px;
    }

    .time-slots-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 15px;
    }

    .time-slot-btn {
        padding: 14px 20px;
        border: 2px solid #ffe5e8;
        border-radius: 12px;
        background: white;
        font-size: 15px;
        font-weight: 600;
        color: #1a1a1a;
        cursor: pointer;
        transition: all 0.3s ease;
        outline: none;
        text-align: center;
    }

    .time-slot-btn:hover {
        border-color: #ff6f91;
        background: linear-gradient(135deg, #fff5f7 0%, #ffeef0 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 111, 145, 0.2);
    }

    .time-slot-btn.active {
        border-color: #ff6f91;
        background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
        color: white;
        font-weight: 700;
        box-shadow: 0 4px 16px rgba(255, 111, 145, 0.3);
    }

    .form-actions {
        margin-top: 50px;
        text-align: center;
    }

    .submit-btn {
        padding: 18px 50px;
        background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
        color: white;
        border: none;
        border-radius: 30px;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: 1px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        box-shadow: 0 4px 16px rgba(255, 111, 145, 0.3);
    }

    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 24px rgba(255, 111, 145, 0.4);
        background: linear-gradient(135deg, #ff4d79 0%, #ff6f91 100%);
    }

    .submit-btn:disabled {
        background: #ccc;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    #scheduleInfo {
        margin-top: 15px;
        font-size: 13px;
        color: #666;
        padding: 12px;
        background: rgba(255, 111, 145, 0.1);
        border-radius: 8px;
        border-left: 3px solid #ff6f91;
    }

    #doctorScheduleSection {
        margin-top: 30px;
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    #scheduleImageContainer img {
        max-width: 100%;
        height: auto;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    #scheduleImageContainer img:hover {
        transform: scale(1.02);
    }

    /* Modal Styles */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 10000;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(4px);
    }

    .modal-overlay.show {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 20px;
        padding: 40px;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        animation: modalSlideIn 0.3s ease-out;
        position: relative;
        z-index: 10001;
    }

    @keyframes modalSlideIn {
        from {
            transform: translateY(-50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #ffe0e8;
    }

    .modal-title {
        font-size: 24px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-icon {
        width: 24px;
        height: 24px;
        color: #ff6f91;
    }

    .modal-body {
        margin-bottom: 30px;
    }

    .modal-info-row {
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        padding: 12px;
        background: linear-gradient(135deg, #fff5f7 0%, #ffeef0 100%);
        border-radius: 10px;
    }

    .info-label {
        font-weight: 700;
        font-size: 13px;
        letter-spacing: 0.8px;
        color: #1a1a1a;
        min-width: 100px;
        margin-right: 15px;
        text-transform: uppercase;
    }

    .info-value {
        font-size: 15px;
        color: #333;
        flex: 1;
        font-weight: 600;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        padding-top: 20px;
        border-top: 2px solid #ffe0e8;
    }

    .cancel-btn {
        padding: 14px 30px;
        background: #f5f5f5;
        color: #333;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .cancel-btn:hover {
        background: #e0e0e0;
        transform: translateY(-2px);
    }

    .submit-modal-btn {
        padding: 14px 30px;
        background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 12px rgba(255, 111, 145, 0.3);
    }

    .submit-modal-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 111, 145, 0.4);
        background: linear-gradient(135deg, #ff4d79 0%, #ff6f91 100%);
    }

    .error-message-alert {
        background: linear-gradient(135deg, #f44336 0%, #e53935 100%);
        color: white;
        padding: 20px 24px;
        border-radius: 12px;
        margin: 0 20px 30px 20px;
        font-size: 15px;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(244, 67, 54, 0.3);
    }

    @media (max-width: 768px) {
        .booking-form-container {
            padding: 30px 20px;
        }

        .time-slots-container {
            grid-template-columns: repeat(2, 1fr);
        }

        .modal-content {
            padding: 30px 20px;
        }
    }
</style>

<div class="content-wrapper">
    <h1 class="booking-title">📅 Booking Form</h1>
    
    @if($errors->any())
        <div class="error-message-alert">
            <strong>❌ Validation Errors:</strong>
            <ul style="margin: 10px 0 0 20px; padding: 0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form id="bookingForm" action="{{ route('booking.store') }}" method="POST">
        @csrf
        @php
            $finalChildId = '';
            if (isset($child) && is_object($child) && !empty($child->ChildID)) {
                $finalChildId = $child->ChildID;
            } elseif (isset($childId) && !empty($childId)) {
                $finalChildId = $childId;
            }
        @endphp
        <input type="hidden" name="child_id" id="childId" value="{{ $finalChildId }}" required>
        @if(empty($finalChildId))
            <div style="color: #d32f2f; padding: 16px; background: #ffebee; border: 2px solid #f44336; border-radius: 12px; margin: 0 20px 30px 20px; font-weight: 600;">
                ⚠️ WARNING: Child ID is missing! Please go back and select a child again.
                <br><a href="{{ route('list.child') }}" style="color: #ff6f91; text-decoration: underline; margin-top: 8px; display: inline-block;">← Go back to child list</a>
            </div>
        @endif
        
    <div class="booking-form-container">
        <div class="form-group">
            <label class="form-label">Name</label>
            <input type="text" class="form-input" id="childName" value="{{ $childName ?? '' }}" readonly>
        </div>
        
        <div class="form-group">
            <label class="form-label">Doctor <span style="color: #ff6f91;">*</span></label>
            <div class="dropdown-wrapper">
                <select class="form-select" id="doctorSelect" name="doctor_id" required oninvalid="this.setCustomValidity('Please select a doctor from the list')" oninput="this.setCustomValidity('')">
                    <option value="">-- Select Doctor --</option>
                    @forelse($doctors ?? [] as $doctor)
                        @php
                            $doctorId = isset($doctor->DoctorID) ? (string)$doctor->DoctorID : '';
                        @endphp
                        @if(!empty($doctorId) && $doctorId !== '0')
                            <option value="{{ $doctorId }}" data-doctor-id="{{ $doctorId }}">{{ $doctor->FullName }}</option>
                        @endif
                    @empty
                        <option value="" disabled>No doctors available</option>
                    @endforelse
                </select>
            </div>
            <div class="error-message" id="doctorError" style="display: none;"></div>
        </div>
        
        <div class="date-time-section">
            <label class="section-label">Select Date <span style="color: #ff6f91;">*</span></label>
            <div class="date-input-wrapper" onclick="openDatePicker()">
                <input type="date" class="date-input" id="dateInput" name="date" required onchange="updateDateDisplay(this.value)" oninvalid="this.setCustomValidity('Please select a date')" oninput="this.setCustomValidity('')" style="opacity: 0; position: absolute; z-index: 3; width: 100%; height: 100%; top: 0; left: 0;">
                <input type="text" class="date-display" id="dateDisplay" value="" readonly placeholder="Select a date" style="background: linear-gradient(135deg, #ffe0e9 0%, #fff0f5 50%, #ffe0e9 100%) !important; border-radius: 30px !important; border: none !important;">
                <svg class="calendar-icon" fill="#ff6f91" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="display: block !important; visibility: visible !important; opacity: 1 !important; z-index: 10 !important;">
                    <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/>
                </svg>
            </div>
            <div class="error-message" id="dateError" style="display: none;"></div>
            <div id="scheduleInfo" style="display: none;">
                <span id="scheduleInfoText"></span>
            </div>
            
            <div id="doctorScheduleSection" style="margin-top: 20px; display: none; position: relative; z-index: 1;">
                <div id="scheduleImageContainer" style="text-align: center;">
                </div>
            </div>
        </div>
        
        <div class="time-slots-section">
            <label class="section-label">Select Time <span style="color: #ff6f91;">*</span></label>
            <div class="time-slots-container">
                <button type="button" class="time-slot-btn" onclick="selectTimeSlot(this, '8:00 am')">8:00 AM</button>
                <button type="button" class="time-slot-btn" onclick="selectTimeSlot(this, '9:00 am')">9:00 AM</button>
                <button type="button" class="time-slot-btn" onclick="selectTimeSlot(this, '10:00 am')">10:00 AM</button>
                <button type="button" class="time-slot-btn" onclick="selectTimeSlot(this, '11:00 am')">11:00 AM</button>
                <button type="button" class="time-slot-btn" onclick="selectTimeSlot(this, '12:00 pm')">12:00 PM</button>
                <button type="button" class="time-slot-btn" onclick="selectTimeSlot(this, '1:00 pm')">1:00 PM</button>
                <button type="button" class="time-slot-btn" onclick="selectTimeSlot(this, '2:00 pm')">2:00 PM</button>
                <button type="button" class="time-slot-btn" onclick="selectTimeSlot(this, '3:00 pm')">3:00 PM</button>
                <button type="button" class="time-slot-btn" onclick="selectTimeSlot(this, '4:00 pm')">4:00 PM</button>
            </div>
            <div class="error-message" id="timeError" style="display: none;"></div>
        </div>
        
        <div class="form-actions">
            <button type="button" class="submit-btn" id="bookAppointmentBtn" onclick="showConfirmModal()" style="pointer-events: auto !important; cursor: pointer !important; z-index: 1000 !important; position: relative;">Book Appointment</button>
        </div>
    </div>
    </form>
</div>

<!-- Schedule Details Confirmation Modal -->
<div class="modal-overlay" id="confirmModal" onclick="if(event.target === this) closeModal(event);">
    <div class="modal-content" onclick="event.stopPropagation(); return false;">
        <div class="modal-header">
            <h2 class="modal-title">Schedule Details</h2>
            <svg class="info-icon" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
            </svg>
        </div>
        <div class="modal-body">
            <div class="modal-info-row">
                <span class="info-label">Name:</span>
                <span class="info-value" id="modalChildName">ALISYA SYUHADA BINTI ISMAIL</span>
            </div>
            <div class="modal-info-row">
                <span class="info-label">Date:</span>
                <span class="info-value" id="modalDate">4 JANUARY 2022</span>
            </div>
            <div class="modal-info-row">
                <span class="info-label">Time:</span>
                <span class="info-value" id="modalTime">11:30 AM</span>
            </div>
        </div>
        <div class="modal-footer">
            <button class="cancel-btn" onclick="closeModal()">Cancel</button>
            <button type="button" class="submit-modal-btn" id="submitBookingBtn" onclick="event.stopPropagation(); event.preventDefault(); submitBooking(event); return false;" style="pointer-events: auto !important; cursor: pointer !important; position: relative; z-index: 10002;">Submit</button>
        </div>
    </div>
</div>

<script>
    // Store available schedule dates
    let availableScheduleDates = [];
    let selectedDoctorId = null;

    // Initialize: enable date input immediately (all dates available)
    document.addEventListener('DOMContentLoaded', function() {
        const childIdInput = document.getElementById('childId');
        if (childIdInput && (!childIdInput.value || childIdInput.value.trim() === '')) {
            const urlMatch = window.location.pathname.match(/\/booking\/form\/([^\/]+)/);
            if (urlMatch && urlMatch[1]) {
                const childIdFromUrl = urlMatch[1];
                childIdInput.value = childIdFromUrl;
                console.log('✅ Child ID extracted from URL:', childIdFromUrl);
            }
        }
        
        const dateInput = document.getElementById('dateInput');
        const dateDisplay = document.getElementById('dateDisplay');
        const bookBtn = document.getElementById('bookAppointmentBtn');
        const doctorSelect = document.getElementById('doctorSelect');
        
        if (dateInput) {
            dateInput.disabled = false;
            dateInput.min = '';
            dateInput.max = '';
        }
        if (dateDisplay) {
            dateDisplay.placeholder = 'Select a date';
        }
        
        if (bookBtn) {
            bookBtn.style.pointerEvents = 'auto';
            bookBtn.style.cursor = 'pointer';
            bookBtn.disabled = false;
        }
        
        @if(!$errors->any() && !session('error'))
        if (doctorSelect) {
            localStorage.removeItem('selectedDoctorId');
            doctorSelect.value = '';
        }
        @endif
    });

    // Listen for doctor selection change
    const doctorSelect = document.getElementById('doctorSelect');
    if (doctorSelect) {
        doctorSelect.addEventListener('change', function() {
            let doctorId = this.value;
            const selectedOption = this.options[this.selectedIndex];
            
            if (!doctorId || doctorId === '' || doctorId === '0') {
                const dataDoctorId = selectedOption.getAttribute('data-doctor-id');
                if (dataDoctorId && dataDoctorId !== '' && dataDoctorId !== '0') {
                    doctorId = dataDoctorId;
                    this.value = dataDoctorId;
                } else {
                    availableScheduleDates = [];
                    resetDateInput();
                    const dateInput = document.getElementById('dateInput');
                    const dateDisplay = document.getElementById('dateDisplay');
                    dateInput.disabled = true;
                    dateDisplay.placeholder = 'Please select a doctor first';
                    
                    const scheduleSection = document.getElementById('doctorScheduleSection');
                    const scheduleImageContainer = document.getElementById('scheduleImageContainer');
                    if (scheduleSection) scheduleSection.style.display = 'none';
                    if (scheduleImageContainer) scheduleImageContainer.innerHTML = '';
                    return;
                }
            }
            
            selectedDoctorId = doctorId;
            if (doctorId && doctorId !== '' && doctorId !== '0') {
                localStorage.setItem('selectedDoctorId', doctorId);
            }
            
            const dateInput = document.getElementById('dateInput');
            const dateDisplay = document.getElementById('dateDisplay');
            
            if (!doctorId || doctorId === '' || doctorId === '0') {
                availableScheduleDates = [];
                resetDateInput();
                dateInput.disabled = true;
                dateDisplay.placeholder = 'Please select a doctor first';
                
                const scheduleSection = document.getElementById('doctorScheduleSection');
                const scheduleImageContainer = document.getElementById('scheduleImageContainer');
                if (scheduleSection) scheduleSection.style.display = 'none';
                if (scheduleImageContainer) scheduleImageContainer.innerHTML = '';
                return;
            }
            
            const scheduleInfoDiv = document.getElementById('scheduleInfo');
            scheduleInfoDiv.style.display = 'block';
            scheduleInfoDiv.style.color = '#666';
            document.getElementById('scheduleInfoText').textContent = '⏳ Loading schedules...';
            
            dateInput.disabled = true;
            dateDisplay.placeholder = 'Loading schedules...';
            
            fetch(`/schedule/doctor/${encodeURIComponent(doctorId)}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                return response.json();
            })
            .then(data => {
                if (data.success && data.schedules && data.schedules.length > 0) {
                    availableScheduleDates = data.schedules.map(s => {
                        const date = new Date(s.UploadDate);
                        const year = date.getFullYear();
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const day = String(date.getDate()).padStart(2, '0');
                        return `${year}-${month}-${day}`;
                    });
                    
                    dateInput.disabled = false;
                    dateInput.removeAttribute('readonly');
                    dateInput.min = '';
                    dateInput.max = '';
                    dateInput.style.pointerEvents = 'auto';
                    dateInput.style.cursor = 'pointer';
                    dateDisplay.placeholder = 'Select a date';
                    dateDisplay.style.cursor = 'pointer';
                    dateDisplay.style.pointerEvents = 'auto';
                    
                    updateDateInputMinMax();
                    showScheduleInfo(data.schedules);
                    
                    setTimeout(() => {
                        dateInput.disabled = false;
                    }, 100);
                } else {
                    availableScheduleDates = [];
                    resetDateInput();
                    dateInput.disabled = true;
                    
                    const scheduleSection = document.getElementById('doctorScheduleSection');
                    const scheduleImageContainer = document.getElementById('scheduleImageContainer');
                    if (scheduleSection) scheduleSection.style.display = 'none';
                    if (scheduleImageContainer) scheduleImageContainer.innerHTML = '';
                    
                    scheduleInfoDiv.style.display = 'block';
                    scheduleInfoDiv.style.color = '#666';
                    const errorMsg = data.message || 'No schedules available for this doctor.';
                    document.getElementById('scheduleInfoText').textContent = '⚠️ ' + errorMsg;
                }
            })
            .catch(error => {
                console.error('❌ Error fetching schedules:', error);
                availableScheduleDates = [];
                resetDateInput();
                dateInput.disabled = true;
                
                const scheduleSection = document.getElementById('doctorScheduleSection');
                const scheduleImageContainer = document.getElementById('scheduleImageContainer');
                if (scheduleSection) scheduleSection.style.display = 'none';
                if (scheduleImageContainer) scheduleImageContainer.innerHTML = '';
                
                const scheduleInfoDiv = document.getElementById('scheduleInfo');
                const scheduleInfoText = document.getElementById('scheduleInfoText');
                if (scheduleInfoDiv) scheduleInfoDiv.style.display = 'block';
                if (scheduleInfoText) {
                    scheduleInfoText.textContent = '❌ Error loading schedules: ' + error.message;
                }
            });
        });
    }

    function updateDateInputMinMax() {
        const dateInput = document.getElementById('dateInput');
        dateInput.min = '';
        dateInput.max = '';
    }

    function resetDateInput() {
        const dateInput = document.getElementById('dateInput');
        const dateDisplay = document.getElementById('dateDisplay');
        dateInput.value = '';
        dateInput.min = '';
        dateInput.max = '';
        dateDisplay.value = '';
        dateDisplay.placeholder = 'Please select a doctor first';
    }

    function showScheduleInfo(schedules) {
        const scheduleInfoDiv = document.getElementById('scheduleInfo');
        const scheduleSection = document.getElementById('doctorScheduleSection');
        const scheduleImageContainer = document.getElementById('scheduleImageContainer');
        
        if (!scheduleSection || !scheduleImageContainer) return;
        
        if (schedules && schedules.length > 0) {
            if (scheduleInfoDiv) scheduleInfoDiv.style.display = 'none';
            
            scheduleSection.style.display = 'block';
            scheduleSection.style.visibility = 'visible';
            scheduleSection.style.opacity = '1';
            scheduleSection.style.position = 'relative';
            scheduleSection.style.zIndex = '1';
            
            // Only show the latest schedule (first one in the array, as they're sorted by created_at desc)
            const latestSchedule = schedules[0];
            const scheduleDate = new Date(latestSchedule.UploadDate);
            const formattedDate = scheduleDate.toLocaleDateString('en-US', { 
                weekday: 'long',
                month: 'long', 
                day: 'numeric', 
                year: 'numeric' 
            });
            
            const imagePath = `/storage/schedules/${latestSchedule.FileName}`;
            const scheduleHTML = `
                <div>
                    <img src="${imagePath}" 
                         alt="Doctor Schedule for ${formattedDate}" 
                         style="max-width: 100%; height: auto; border-radius: 12px; cursor: pointer; display: block; margin: 0 auto;"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"
                         onclick="window.open('${imagePath}', '_blank')"
                         title="Click to view full size">
                    <div style="display: none; color: #666; padding: 10px; text-align: center; background-color: #f0f0f0; border-radius: 8px; margin-top: 10px;">
                        ⚠️ Schedule image not found
                    </div>
                </div>
            `;
            
            scheduleImageContainer.innerHTML = scheduleHTML;
        } else {
            if (scheduleInfoDiv) scheduleInfoDiv.style.display = 'none';
            if (scheduleSection) scheduleSection.style.display = 'none';
            if (scheduleImageContainer) scheduleImageContainer.innerHTML = '';
        }
    }
    
    function selectTimeSlot(btn, time) {
        document.querySelectorAll('.time-slot-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    }
    
    function updateDateDisplay(dateValue) {
        const dateDisplay = document.getElementById('dateDisplay');
        if (!dateDisplay) return;
        
        if (!dateValue) {
            dateDisplay.value = '';
            dateDisplay.style.background = 'linear-gradient(135deg, #ffe0e9 0%, #fff0f5 50%, #ffe0e9 100%)';
            dateDisplay.style.color = '#1a1a1a';
            dateDisplay.style.fontWeight = '500';
            return;
        }
        
        const date = new Date(dateValue);
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        const dayName = days[date.getDay()];
        const day = date.getDate();
        const month = months[date.getMonth()];
        
        const formattedDate = `${dayName}, ${day} ${month}`;
        dateDisplay.value = formattedDate;
        
        // Ensure styled appearance is maintained after selection - force override any default styles
        dateDisplay.style.background = 'linear-gradient(135deg, #ffe0e9 0%, #fff0f5 50%, #ffe0e9 100%)';
        dateDisplay.style.color = '#1a1a1a';
        dateDisplay.style.fontWeight = '600';
        dateDisplay.style.borderRadius = '30px';
        dateDisplay.style.border = 'none';
        dateDisplay.style.boxShadow = '0 2px 8px rgba(255, 111, 145, 0.1)';
        dateDisplay.style.webkitAppearance = 'none';
        dateDisplay.style.mozAppearance = 'none';
        dateDisplay.style.appearance = 'none';
        dateDisplay.style.outline = 'none';
        
        // Also ensure the hidden date input doesn't show through
        const dateInput = document.getElementById('dateInput');
        if (dateInput) {
            dateInput.style.opacity = '0';
            dateInput.style.position = 'absolute';
            dateInput.style.zIndex = '3';
        }
    }
    
    function openDatePicker() {
        const dateInput = document.getElementById('dateInput');
        if (!dateInput) return false;
        
        if (dateInput.disabled) {
            alert('Please select a doctor first to enable date selection.');
            return false;
        }
        
        dateInput.style.opacity = '1';
        dateInput.style.pointerEvents = 'auto';
        dateInput.style.zIndex = '10';
        
        if (dateInput.showPicker && typeof dateInput.showPicker === 'function') {
            try {
                dateInput.showPicker();
                return true;
            } catch (error) {
                console.warn('showPicker() failed:', error);
            }
        }
        
        try {
            dateInput.focus();
            setTimeout(() => {
                dateInput.click();
            }, 10);
            return true;
        } catch (error) {
            console.warn('focus/click failed:', error);
        }
        
        setTimeout(() => {
            dateInput.style.opacity = '0';
        }, 100);
        
        return false;
    }
    
    function showConfirmModal() {
        const doctorError = document.getElementById('doctorError');
        const dateError = document.getElementById('dateError');
        const timeError = document.getElementById('timeError');
        
        if (doctorError) doctorError.style.display = 'none';
        if (dateError) dateError.style.display = 'none';
        if (timeError) timeError.style.display = 'none';
        
        const childName = document.getElementById('childName').value;
        const doctorId = document.getElementById('doctorSelect').value;
        const date = document.getElementById('dateInput').value;
        const dateDisplay = document.getElementById('dateDisplay').value;
        const timeSlot = document.querySelector('.time-slot-btn.active')?.textContent.trim();
        
        let hasError = false;
        
        if (!doctorId) {
            document.getElementById('doctorError').textContent = 'Please select a doctor from the list';
            document.getElementById('doctorError').style.display = 'block';
            document.getElementById('doctorSelect').focus();
            hasError = true;
        }
        
        if (!date) {
            document.getElementById('dateError').textContent = 'Please select a date';
            document.getElementById('dateError').style.display = 'block';
            if (!hasError) document.getElementById('dateInput').focus();
            hasError = true;
        }
        
        if (!timeSlot) {
            document.getElementById('timeError').textContent = 'Please select a time slot';
            document.getElementById('timeError').style.display = 'block';
            hasError = true;
        }
        
        if (hasError) return;
        
        const dateInput = document.getElementById('dateInput').value;
        let formattedDate = dateDisplay.toUpperCase();
        if (dateInput) {
            const date = new Date(dateInput);
            const months = ['JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE', 
                          'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER'];
            formattedDate = `${date.getDate()} ${months[date.getMonth()]} ${date.getFullYear()}`;
        }
        
        document.getElementById('modalChildName').textContent = childName;
        document.getElementById('modalDate').textContent = formattedDate;
        document.getElementById('modalTime').textContent = timeSlot.toUpperCase();
        
        const modal = document.getElementById('confirmModal');
        if (modal) {
            modal.classList.add('show');
            setTimeout(function() {
                const submitBtn = document.getElementById('submitBookingBtn');
                if (submitBtn) {
                    submitBtn.style.pointerEvents = 'auto';
                    submitBtn.style.cursor = 'pointer';
                    submitBtn.style.zIndex = '10003';
                    submitBtn.style.position = 'relative';
                    submitBtn.disabled = false;
                }
            }, 100);
        }
    }
    
    function closeModal(event) {
        if (event) {
            if (event.target.closest('.modal-content') || 
                event.target.closest('button') || 
                event.target.closest('.submit-modal-btn') ||
                event.target.closest('.cancel-btn') ||
                event.target.tagName === 'BUTTON') {
                return;
            }
            if (event.target.id === 'confirmModal') {
                const modal = document.getElementById('confirmModal');
                if (modal) modal.classList.remove('show');
            }
        } else {
            const modal = document.getElementById('confirmModal');
            if (modal) modal.classList.remove('show');
        }
    }
    
    function submitBooking(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();
        }
        
        const childIdEl = document.getElementById('childId');
        const childNameEl = document.getElementById('childName');
        const doctorSelectEl = document.getElementById('doctorSelect');
        const dateInputEl = document.getElementById('dateInput');
        const timeSlot = document.querySelector('.time-slot-btn.active')?.textContent.trim();
        
        if (!childIdEl || !childNameEl || !doctorSelectEl || !dateInputEl) {
            alert('Error: Form fields not found. Please refresh the page.');
            return;
        }
        
        let childId = childIdEl.value;
        if (!childId || childId.trim() === '') {
            childId = '{{ $child->ChildID ?? $childId ?? "" }}';
            if (childId && childId.trim() !== '') {
                childIdEl.value = childId;
            }
        }
        
        const childName = childNameEl.value;
        const doctorId = doctorSelectEl.value;
        const date = dateInputEl.value;
        
        if (!childId || !doctorId || !date || !timeSlot) {
            alert('Error: Please fill in all required fields.');
            return;
        }
        
        const nurseId = '{{ $currentNurseId ?? "" }}';
        if (!nurseId) {
            alert('❌ ERROR!\n\nNurse ID not found. Please contact administrator.');
            return;
        }
        
        const form = document.getElementById('bookingForm');
        if (!form) {
            alert('Error: Form not found. Please refresh the page.');
            return;
        }
        
        let nurseInput = form.querySelector('input[name="nurse_id"]');
        if (!nurseInput) {
            nurseInput = document.createElement('input');
            nurseInput.type = 'hidden';
            nurseInput.name = 'nurse_id';
            form.appendChild(nurseInput);
        }
        nurseInput.value = nurseId;
        
        let timeInput = form.querySelector('input[name="time"]');
        if (!timeInput) {
            timeInput = document.createElement('input');
            timeInput.type = 'hidden';
            timeInput.name = 'time';
            form.appendChild(timeInput);
        }
        timeInput.value = timeSlot;
        
        const modal = document.getElementById('confirmModal');
        if (modal) modal.classList.remove('show');
        
        form.submit();
    }
</script>
@endsection
