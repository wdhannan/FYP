@extends('layouts.app')

@section('title', 'Birth Record')

@section('content')
    @php
        // Get registered children from database
        $children = $children ?? collect([]);
        $childOptions = [];
        if ($children && $children->count() > 0) {
            foreach ($children as $child) {
                // DB::table() returns stdClass objects, so use object notation
                $childId = isset($child->ChildID) ? $child->ChildID : '';
                $childName = isset($child->FullName) ? $child->FullName : 'Unknown';
                if (!empty($childId) && $childId != '0' && $childId != 0) {
                    $childOptions[$childId] = $childId . ' - ' . $childName;
                }
            }
        }
    @endphp

    <style>
        body {
            background: linear-gradient(135deg, #ffeef4 0%, #fff5f8 50%, #ffeef4 100%);
            min-height: 100vh;
        }

        .page-wrapper {
            padding: 40px 20px;
            max-width: 1100px;
            margin: 0 auto;
            background: linear-gradient(135deg, #fff5f7 0%, #ffeef0 50%, #ffe5e8 100%);
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(255, 111, 145, 0.15), 0 0 0 1px rgba(255, 182, 193, 0.1);
            position: relative;
            overflow: hidden;
        }

        .page-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
        }

        .page-title {
            font-size: 32px;
            font-weight: 800;
            color: #1a1a1a;
            margin-bottom: 40px;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 0 40px;
        }

        .birth-record-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
        }

        .birth-record-field {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .birth-record-field.full-width {
            grid-column: 1 / -1;
        }

        .birth-record-label {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.8px;
            color: #1a1a1a;
            text-transform: uppercase;
        }

        .birth-record-input,
        .birth-record-select,
        .birth-record-textarea {
            padding: 14px 16px;
            border-radius: 12px;
            border: 2px solid #ffe5e8;
            background: linear-gradient(to bottom, #fff, #fff8f8);
            font-size: 15px;
            font-family: inherit;
            color: #1a1a1a;
            transition: all 0.3s ease;
        }

        .birth-record-input:focus,
        .birth-record-select:focus,
        .birth-record-textarea:focus {
            outline: none;
            border-color: #ff6f91;
            box-shadow: 0 0 0 4px rgba(255, 111, 145, 0.1);
            background: white;
        }

        .birth-record-textarea {
            min-height: 100px;
            resize: vertical;
            font-family: inherit;
        }

        .inline-unit {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .inline-unit input {
            flex: 1;
        }

        .inline-unit span {
            background: linear-gradient(135deg, #ffe5e8 0%, #ffd6db 100%);
            border: 2px solid #ffe5e8;
            border-radius: 12px;
            padding: 14px 18px;
            font-size: 14px;
            font-weight: 700;
            min-width: 60px;
            text-align: center;
            color: #1a1a1a;
        }

        .time-selector {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .time-selector select {
            flex: 1;
        }

        .time-separator {
            font-size: 20px;
            font-weight: 700;
            color: #ff6f91;
        }

        .radio-group {
            display: flex;
            gap: 24px;
            align-items: center;
            padding: 12px 16px;
            background: linear-gradient(to bottom, #fff, #fff8f8);
            border: 2px solid #ffe5e8;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .radio-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 15px;
            font-weight: 600;
            color: #1a1a1a;
            cursor: pointer;
        }

        .radio-group input[type="radio"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #ff6f91;
        }

        .radio-group.validation-error {
            border-color: #d64545;
            box-shadow: 0 0 0 4px rgba(214, 69, 69, 0.1);
        }

        .validation-error {
            border-color: #d64545 !important;
            box-shadow: 0 0 0 4px rgba(214, 69, 69, 0.1) !important;
        }

        .birth-record-actions {
            margin-top: 32px;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            padding-top: 24px;
            border-top: 2px solid rgba(255, 182, 193, 0.3);
        }

        .birth-record-button {
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

        .birth-record-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(255, 111, 145, 0.4);
        }

        /* View Records Section */
        .view-records-wrapper {
            background: linear-gradient(135deg, #fff5f7 0%, #ffeef0 50%, #ffe5e8 100%);
            border-radius: 24px;
            padding: 40px;
            margin: 0 auto;
            max-width: 1400px;
            box-shadow: 0 20px 60px rgba(255, 111, 145, 0.15), 0 0 0 1px rgba(255, 182, 193, 0.1);
        }

        .child-selector-form {
            background: white;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(255, 111, 145, 0.1);
        }

        .records-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .records-table thead {
            background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
        }

        .records-table th {
            padding: 16px;
            text-align: left;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            color: white;
            letter-spacing: 0.5px;
        }

        .records-table td {
            padding: 16px;
            font-size: 14px;
            color: #1a1a1a;
            font-weight: 500;
            border-bottom: 1px solid #ffe0e8;
        }

        .records-table tbody tr {
            transition: all 0.3s ease;
        }

        .records-table tbody tr:hover {
            background: linear-gradient(90deg, #fff5f5 0%, #ffe0e9 100%);
        }

        .records-table tbody tr:last-child td {
            border-bottom: none;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
            font-size: 16px;
            font-weight: 500;
            background: white;
            border-radius: 12px;
        }

        .empty-state-icon {
            font-size: 64px;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        @media (max-width: 768px) {
            .birth-record-grid {
                grid-template-columns: 1fr;
            }
            
            .page-wrapper {
                padding: 24px;
            }
            
            .page-title {
                padding: 0 24px;
            }
            
            .page-title {
                font-size: 24px;
            }
        }
    </style>

    <div class="page-wrapper">
        <h1 class="page-title">📋 Birth Record</h1>

        @php
            $userRole = session('user_role', '');
            $isNurse = $userRole === 'nurse';
        @endphp

        @if($isNurse)
        <form id="birthRecordForm" method="POST" action="{{ route('birth.record.store') }}">
            @csrf
            <div class="birth-record-grid">
                <div class="birth-record-field">
                    <label class="birth-record-label">Child ID</label>
                    <select class="birth-record-select" name="child_id" data-required="true" data-label="Child ID">
                        <option value="">Select Child ID</option>
                        @foreach ($childOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="birth-record-field">
                    <label class="birth-record-label">Gestational Age (Weeks)</label>
                    <select class="birth-record-select" name="gestational_age_weeks" data-required="true" data-label="Gestational Age">
                        <option value="">Select Gestational Age</option>
                        @for($i = 0; $i <= 45; $i++)
                            <option value="{{ $i }}">{{ $i }} {{ $i == 1 ? 'week' : 'weeks' }}</option>
                        @endfor
                    </select>
                </div>

                <div class="birth-record-field">
                    <label class="birth-record-label">Time of Birth</label>
                    <div class="time-selector">
                        <select class="birth-record-select" name="birth_hour" data-required="true" data-label="Time of Birth">
                            <option value="">Hour</option>
                            @for($i = 0; $i <= 23; $i++)
                                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                            @endfor
                        </select>
                        <span class="time-separator">:</span>
                        <select class="birth-record-select" name="birth_minute" data-required="true" data-label="Time of Birth">
                            <option value="">Minute</option>
                            @for($i = 0; $i <= 59; $i++)
                                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                            @endfor
                        </select>
                        <input type="hidden" name="time_of_birth" id="time_of_birth_hidden">
                    </div>
                </div>

                <div class="birth-record-field">
                    <label class="birth-record-label">Birth Place</label>
                    <select class="birth-record-select" name="birth_place" data-required="true" data-label="Birth Place">
                        <option value="">Select Birth Place</option>
                        <option value="Hospital">Hospital</option>
                        <option value="Home">Home</option>
                        <option value="Clinic">Clinic</option>
                    </select>
                </div>

                <div class="birth-record-field">
                    <label class="birth-record-label">Birth Type</label>
                    <select class="birth-record-select" name="birth_type" data-required="true" data-label="Birth Type">
                        <option value="">Select Birth Type</option>
                        <option value="Normal">Normal</option>
                        <option value="Cesarean">Cesarean</option>
                    </select>
                </div>

                <div class="birth-record-field full-width">
                    <label class="birth-record-label">Complications</label>
                    <textarea class="birth-record-textarea" name="complications" placeholder="Describe any complications during birth"></textarea>
                </div>

                <div class="birth-record-field">
                    <label class="birth-record-label">Baby Counts</label>
                    <select class="birth-record-select" name="baby_count" data-required="true" data-label="Baby Counts">
                        <option value="">Select Baby Count</option>
                        <option value="1">1 (Single)</option>
                        <option value="2">2 (Twins)</option>
                        <option value="3">3 (Triplets)</option>
                        <option value="4">4 (Quadruplets)</option>
                        <option value="5">5 (Quintuples)</option>
                    </select>
                </div>

                <div class="birth-record-field">
                    <label class="birth-record-label">Birth Weight</label>
                    <div class="inline-unit">
                        <input type="number" class="birth-record-input" name="birth_weight" placeholder="0.0" step="0.1" min="0" data-required="true" data-label="Birth Weight">
                        <span>Kg</span>
                    </div>
                </div>

                <div class="birth-record-field">
                    <label class="birth-record-label">Baby Height</label>
                    <div class="inline-unit">
                        <input type="number" class="birth-record-input" name="birth_length" placeholder="0.0" step="0.1" min="0" data-required="true" data-label="Baby Height">
                        <span>Cm</span>
                    </div>
                </div>

                <div class="birth-record-field">
                    <label class="birth-record-label">Head Circumference</label>
                    <div class="inline-unit">
                        <input type="number" class="birth-record-input" name="birth_circumference" placeholder="0.0" step="0.1" min="0" data-required="true" data-label="Head Circumference">
                        <span>Cm</span>
                    </div>
                </div>

                <div class="birth-record-field">
                    <label class="birth-record-label">Blood Group</label>
                    <select class="birth-record-select" name="blood_group" data-required="true" data-label="Blood Group">
                        <option value="">Select Blood Group</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                </div>

                <div class="birth-record-field">
                    <label class="birth-record-label">Apgar Score (Min)</label>
                    <input type="number" class="birth-record-input" name="apgar_score" placeholder="e.g. 8" data-required="true" data-label="Apgar Score" min="0" max="10">
                </div>

                <div class="birth-record-field">
                    <label class="birth-record-label">IM Vitamin K</label>
                    <div class="radio-group">
                        <label><input type="radio" name="vitamin_k" value="yes" data-required="true" data-label="IM Vitamin K" checked> Yes</label>
                        <label><input type="radio" name="vitamin_k" value="no"> No</label>
                    </div>
                </div>
            </div>

            <div class="birth-record-actions">
                <button type="submit" class="birth-record-button">Save</button>
            </div>
        </form>
        @else
        @php
            $selectedChildId = $selectedChildId ?? request('child_id');
        @endphp
        <div style="padding: 20px; text-align: center; color: #666;">
            {{-- Child selector for doctors to view a single child's records --}}
            <form method="GET" action="{{ route('birth.record') }}" style="margin: 15px auto 20px; max-width: 400px; text-align: left;">
                <label class="birth-record-label" style="margin-bottom: 6px;">Select Child</label>
                <select name="child_id" class="birth-record-select" onchange="this.form.submit()" style="width: 100%;">
                    <option value="">-- Select Child --</option>
                    @foreach($children as $child)
                        @php
                            $cid = $child->ChildID ?? ($child['ChildID'] ?? '');
                            $cname = $child->FullName ?? ($child['FullName'] ?? $cid);
                        @endphp
                        @if(!empty($cid))
                            <option value="{{ $cid }}" {{ request('child_id') === $cid ? 'selected' : '' }}>
                                {{ $cid }} - {{ $cname }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </form>

            @if(($selectedChildId ?? '') !== '' && count($birthRecords) > 0)
                @foreach($birthRecords as $record)
                    <div class="birth-record-grid" style="background: white; border-radius: 16px; padding: 40px; margin: 20px auto; max-width: 1000px;">
                        <div class="birth-record-field">
                            <label class="birth-record-label">Child ID</label>
                            <input type="text" class="birth-record-input" value="{{ $record->ChildID ?? 'N/A' }}" readonly>
                        </div>

                        <div class="birth-record-field">
                            <label class="birth-record-label">Gestational Age (Weeks)</label>
                            <input type="text" class="birth-record-input" value="{{ ($record->GestationalAgeWeeks ?? null) !== null ? $record->GestationalAgeWeeks . ' weeks' : 'N/A' }}" readonly>
                        </div>

                        <div class="birth-record-field">
                            <label class="birth-record-label">Time of Birth</label>
                            <input type="text" class="birth-record-input" value="@if(isset($record->TimeOfBirth))@php try { echo \Carbon\Carbon::parse($record->TimeOfBirth)->format('h:i A'); } catch (\Exception $e) { echo $record->TimeOfBirth ?? 'N/A'; } @endphp @else N/A @endif" readonly>
                        </div>

                        <div class="birth-record-field">
                            <label class="birth-record-label">Birth Place</label>
                            <input type="text" class="birth-record-input" value="{{ $record->BirthPlace ?? 'N/A' }}" readonly>
                        </div>

                        <div class="birth-record-field">
                            <label class="birth-record-label">Birth Type</label>
                            <input type="text" class="birth-record-input" value="{{ $record->BirthType ?? 'N/A' }}" readonly>
                        </div>

                        <div class="birth-record-field">
                            <label class="birth-record-label">Baby Count</label>
                            <input type="text" class="birth-record-input" value="{{ $record->BabyCount ?? 'N/A' }}" readonly>
                        </div>

                        <div class="birth-record-field">
                            <label class="birth-record-label">Birth Weight</label>
                            <div class="inline-unit">
                                <input type="text" class="birth-record-input" value="{{ $record->BirthWeight ?? 'N/A' }}" readonly>
                                <span>Kg</span>
                            </div>
                        </div>

                        <div class="birth-record-field">
                            <label class="birth-record-label">Birth Length</label>
                            <div class="inline-unit">
                                <input type="text" class="birth-record-input" value="{{ $record->BirthLength ?? 'N/A' }}" readonly>
                                <span>Cm</span>
                            </div>
                        </div>

                        <div class="birth-record-field">
                            <label class="birth-record-label">Head Circumference</label>
                            <div class="inline-unit">
                                <input type="text" class="birth-record-input" value="{{ $record->BirthCircumference ?? 'N/A' }}" readonly>
                                <span>Cm</span>
                            </div>
                        </div>

                        <div class="birth-record-field">
                            <label class="birth-record-label">Blood Group</label>
                            <input type="text" class="birth-record-input" value="{{ $record->BloodGroup ?? 'N/A' }}" readonly>
                        </div>

                        <div class="birth-record-field">
                            <label class="birth-record-label">Apgar Score</label>
                            <input type="text" class="birth-record-input" value="{{ $record->ApgarScore ?? 'N/A' }}" readonly>
                        </div>

                        <div class="birth-record-field">
                            <label class="birth-record-label">IM Vitamin K</label>
                            <div class="radio-group" style="pointer-events: none;">
                                <label><input type="radio" name="vitamin_k_display_{{ $record->BirthRecordID ?? '' }}" value="yes" {{ ($record->VitaminKGiven ?? '') === 'yes' ? 'checked' : '' }} disabled> Yes</label>
                                <label><input type="radio" name="vitamin_k_display_{{ $record->BirthRecordID ?? '' }}" value="no" {{ ($record->VitaminKGiven ?? '') === 'no' ? 'checked' : '' }} disabled> No</label>
                            </div>
                        </div>

                        <div class="birth-record-field full-width">
                            <label class="birth-record-label">Complications</label>
                            <textarea class="birth-record-textarea" readonly>{{ $record->Complications ?? 'N/A' }}</textarea>
                        </div>
                    </div>
                @endforeach
            @elseif(($selectedChildId ?? '') !== '')
                <div style="text-align: center; padding: 40px 20px; color: #999; font-size: 16px; background: white; border-radius: 12px; margin-top: 20px; max-width: 1000px; margin-left: auto; margin-right: auto;">
                    No birth records found for the selected child.
                </div>
            @endif
        </div>
        @endif
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('birthRecordForm');
            if (!form) return;

            // Convert hour and minute to 24-hour format for hidden input
            function updateTimeOfBirth() {
                const hourSelect = form.querySelector('[name="birth_hour"]');
                const minuteSelect = form.querySelector('[name="birth_minute"]');
                const hiddenInput = document.getElementById('time_of_birth_hidden');
                
                if (hourSelect && minuteSelect && hiddenInput) {
                    const hour = hourSelect.value;
                    const minute = minuteSelect.value;
                    
                    if (hour && minute) {
                        const hour24 = String(hour).padStart(2, '0');
                        const time24 = hour24 + ':' + minute + ':00';
                        hiddenInput.value = time24;
                    } else {
                        hiddenInput.value = '';
                    }
                }
            }

            // Add change listeners to time fields
            const timeFields = ['birth_hour', 'birth_minute'];
            timeFields.forEach(fieldName => {
                const field = form.querySelector(`[name="${fieldName}"]`);
                if (field) {
                    field.addEventListener('change', updateTimeOfBirth);
                }
            });

            const requiredFields = form.querySelectorAll('[data-required="true"]');
            requiredFields.forEach(field => {
                const handler = () => {
                    if (field.type === 'radio') {
                        const wrapper = field.closest('.radio-group');
                        if (wrapper) wrapper.classList.remove('validation-error');
                    } else {
                        field.classList.remove('validation-error');
                    }
                };
                field.addEventListener('input', handler);
                field.addEventListener('change', handler);
            });
        });

        // Form submission handler - validate before submit
        const form = document.getElementById('birthRecordForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                // Update time_of_birth before validation
                const hourSelect = form.querySelector('[name="birth_hour"]');
                const minuteSelect = form.querySelector('[name="birth_minute"]');
                const hiddenInput = document.getElementById('time_of_birth_hidden');
                
                if (hourSelect && minuteSelect && hiddenInput) {
                    const hour = hourSelect.value;
                    const minute = minuteSelect.value;
                    
                    if (hour && minute) {
                        const hour24 = String(hour).padStart(2, '0');
                        const time24 = hour24 + ':' + minute + ':00';
                        hiddenInput.value = time24;
                    }
                }

                const requiredFields = form.querySelectorAll('[data-required="true"]');
                const missingLabels = [];

                requiredFields.forEach(field => {
                    if (field.type === 'radio') {
                        if (missingLabels.includes(field.dataset.label)) {
                            return;
                        }

                        const checked = form.querySelector(`input[name="${field.name}"]:checked`);
                        if (!checked) {
                            missingLabels.push(field.dataset.label || 'This option');
                            const wrapper = field.closest('.radio-group');
                            if (wrapper) wrapper.classList.add('validation-error');
                        }
                    } else {
                        if (!field.value || !field.value.toString().trim()) {
                            field.classList.add('validation-error');
                            missingLabels.push(field.dataset.label || 'This field');
                        }
                    }
                });

                if (missingLabels.length) {
                    e.preventDefault();
                    alert('Please fill in: ' + [...new Set(missingLabels)].join(', '));
                    return false;
                }

                // Form will submit normally if validation passes
            });
        }
    </script>
    </div>
@endsection

