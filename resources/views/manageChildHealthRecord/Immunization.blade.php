@extends('layouts.app')

@section('title', 'Immunization Record')

@section('content')
    @php
        $vaccines = $vaccines ?? collect([]);
        $children = $children ?? collect([]);
        
        // Vaccine Names list
        $vaccineNames = [
            'Hepatitis B',
            'BCG (Bacillus Calmette–Guérin)',
            'Polio (IPV)',
            'DTP (Diphtheria, Tetanus, Pertussis)',
            'MMR (Measles, Mumps, Rubella)',
            'Hib (Haemophilus Influenzae Type B)',
            'Rotavirus',
            'Pneumococcal (PCV)',
            'Varicella (Chickenpox)',
            'Influenza (Flu)',
        ];
    @endphp

    <style>
        body {
            background: linear-gradient(135deg, #ffeef4 0%, #fff5f8 50%, #ffeef4 100%);
            min-height: 100vh;
        }

        .page-wrapper {
            padding: 40px 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .page-header {
            margin-bottom: 40px;
        }

        .page-title {
            font-size: 28px;
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

        .immunization-wrapper {
            background-color: #fde8e8;
            border-radius: 16px;
            padding: 40px 30px;
            margin: 0 auto;
            max-width: 1000px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.06);
        }

        .form-section {
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #333;
            margin-bottom: 6px;
        }

        .form-select,
        .form-input {
            width: 100%;
            padding: 12px 14px;
            border-radius: 6px;
            border: 1px solid #d8c4c4;
            background-color: white;
            font-size: 14px;
        }

        .table-wrapper-modern {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
            border: 1px solid #f0f0f0;
            margin-top: 20px;
        }

        .immunization-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 0;
        }

        .immunization-table thead {
            background: linear-gradient(135deg, #ffe0e9 0%, #ffb6c1 100%);
            position: relative;
        }

        .immunization-table thead::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #ff6f91, #ff9eb3);
        }

        .immunization-table th {
            padding: 18px 20px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-size: 13px;
            font-weight: 700;
            color: #1c1c1c;
            text-align: left;
            border: none;
        }

        .immunization-table th:first-child {
            border-top-left-radius: 16px;
        }

        .immunization-table th:last-child {
            border-top-right-radius: 16px;
        }

        .immunization-table tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid #f5f5f5;
        }

        .immunization-table tbody tr:last-child {
            border-bottom: none;
        }

        .immunization-table tbody tr:hover {
            background: linear-gradient(90deg, #fff5f5 0%, #ffe0e9 100%);
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(255, 111, 145, 0.1);
        }

        .immunization-table td {
            padding: 16px 20px;
            border: none;
            font-size: 14px;
            font-weight: 500;
            color: #333;
        }

        .immunization-table tbody tr:nth-child(even) {
            background-color: #fff9fb;
        }

        .immunization-table tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        .immunization-table tbody tr:nth-child(even):hover,
        .immunization-table tbody tr:nth-child(odd):hover {
            background: linear-gradient(90deg, #fff5f5 0%, #ffe0e9 100%);
        }

        .immunization-select,
        .immunization-input {
            width: 100%;
            border: none;
            background-color: transparent;
            padding: 8px;
            font-size: 14px;
        }

        .immunization-select {
            cursor: pointer;
        }

        .immunization-input:focus,
        .immunization-select:focus {
            outline: none;
            background-color: rgba(255, 255, 255, 0.8);
        }

        .immunization-actions {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .immunization-button {
            background-color: #d9d1cc;
            border: none;
            padding: 12px 32px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .immunization-button:hover {
            background-color: #c7bfba;
        }

        .add-row-button {
            background-color: #f0f0f0;
            border: 1px solid #d0d0d0;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 13px;
            cursor: pointer;
            margin-top: 10px;
        }

        .remove-row-button {
            background-color: #ffebee;
            border: 1px solid #ffcdd2;
            color: #c62828;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
        }
    </style>

    <div class="page-wrapper">
        <div class="page-header">
            <h1 class="page-title">💉 Immunization Record</h1>
        </div>

        <div class="immunization-wrapper">

        @if($errors->any())
            <div style="background: #F44336; color: white; padding: 12px; border-radius: 4px; margin-bottom: 20px;">
                <strong>❌ Validation Errors:</strong>
                <ul style="margin: 10px 0 0 20px; padding: 0;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $userRole = session('user_role', '');
            $isNurse = $userRole === 'nurse';
        @endphp

        @php
            // Selected child for doctor view filtering
            $selectedChildId = $selectedChildId ?? request('child_id');
        @endphp

        @if($isNurse)
        <form id="immunizationForm" method="POST" action="{{ route('immunization.store') }}">
            @csrf
            
            <div class="form-section">
                <div class="form-group">
                    <label class="form-label">Select Child</label>
                    <select name="child_id" class="form-select" required>
                        <option value="">-- Select Child --</option>
                        @foreach($children as $child)
                            <option value="{{ $child->ChildID ?? $child['ChildID'] }}">
                                {{ $child->FullName ?? $child['FullName'] ?? $child->ChildID ?? $child['ChildID'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="table-wrapper-modern">
                <table class="immunization-table">
                    <thead>
                        <tr>
                            <th>VACCINE(S)</th>
                            <th>AGE</th>
                            <th>DOSE NUMBER</th>
                            <th>DATE</th>
                            <th>GIVEN BY</th>
                        </tr>
                    </thead>
                    <tbody id="immunizationTableBody">
                    <tr>
                        <td>
                            <select name="vaccine_name[]" class="immunization-select" required disabled>
                                <option value="">-- Select Child First --</option>
                            </select>
                        </td>
                        <td>
                            <select name="age[]" class="immunization-select" required disabled>
                                <option value="">-- Select Child First --</option>
                                @for($i = 0; $i <= 72; $i++)
                                    @php
                                        $years = floor($i / 12);
                                        $months = $i % 12;
                                        if ($years == 0) {
                                            $display = $months == 1 ? "1 month" : "$months months";
                                        } elseif ($months == 0) {
                                            $display = $years == 1 ? "1 year" : "$years years";
                                        } else {
                                            $yearText = $years == 1 ? "1 year" : "$years years";
                                            $monthText = $months == 1 ? "1 month" : "$months months";
                                            $display = "$yearText $monthText";
                                        }
                                    @endphp
                                    <option value="{{ $i }}">{{ $display }}</option>
                                @endfor
                            </select>
                        </td>
                        <td><input type="text" name="dose_number[]" class="immunization-input" placeholder="e.g. 1st, 2nd" required></td>
                        <td><input type="date" name="date[]" class="immunization-input" required></td>
                        <td><input type="text" name="given_by[]" class="immunization-input" placeholder="Doctor/Nurse name" required></td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <button type="button" class="add-row-button" onclick="addRow()" id="addRowBtn" disabled>+ Add Row</button>

            <div class="immunization-actions">
                <button type="submit" class="immunization-button">Save</button>
            </div>
        </form>
        @else
        <div style="padding: 20px; text-align: center; color: #666;">
            {{-- Child selector for doctors to view a single child's records --}}
            <form method="GET" action="{{ route('immunization.record') }}" style="margin: 15px auto 20px; max-width: 400px; text-align: left;">
                <label class="form-label" style="margin-bottom: 6px;">Select Child</label>
                <select name="child_id" class="form-select" onchange="this.form.submit()">
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

            @if(($selectedChildId ?? '') !== '' && count($vaccines) > 0)
                <div class="table-wrapper-modern" style="margin-top: 20px;">
                    <table class="immunization-table">
                        <thead>
                            <tr>
                                <th>CHILD NAME</th>
                                <th>VACCINE</th>
                                <th>AGE</th>
                            <th>DOSE NUMBER</th>
                            <th>DATE</th>
                            <th>GIVEN BY</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vaccines as $vaccine)
                            <tr>
                                <td>{{ $vaccine->ChildName ?? 'N/A' }}</td>
                                <td>{{ $vaccine->VaccineName ?? 'N/A' }}</td>
                                <td>{{ $vaccine->Age !== null ? $vaccine->Age . ' months' : 'N/A' }}</td>
                                <td>{{ $vaccine->DoseNumber ?? '-' }}</td>
                                <td>{{ $vaccine->Date ? \Carbon\Carbon::parse($vaccine->Date)->format('Y-m-d') : 'N/A' }}</td>
                                <td>{{ $vaccine->GivenBy ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    </table>
                </div>
            @elseif(($selectedChildId ?? '') !== '')
                <p>No immunization records found for the selected child.</p>
            @endif
        </div>
        @endif
    </div>

    <script>
        const vaccineNames = @json($vaccineNames);
        const childSelect = document.querySelector('[name="child_id"]');
        const addRowBtn = document.getElementById('addRowBtn');
        
        function toggleTableInputs() {
            const childSelected = childSelect.value !== '';
            const tableInputs = document.querySelectorAll('#immunizationTableBody input, #immunizationTableBody select');
            
            tableInputs.forEach(input => {
                input.disabled = !childSelected;
            });
            
            addRowBtn.disabled = !childSelected;
            
            // Update first row select options
            if (childSelected) {
                const firstVaccineSelect = document.querySelector('#immunizationTableBody select[name="vaccine_name[]"]');
                if (firstVaccineSelect) {
                    firstVaccineSelect.innerHTML = '<option value="">-- Select Vaccine --</option>' + 
                        vaccineNames.map(vaccine => `<option value="${vaccine}">${vaccine}</option>`).join('');
                }
                
                const firstAgeSelect = document.querySelector('#immunizationTableBody select[name="age[]"]');
                if (firstAgeSelect) {
                    let ageOptions = '<option value="">-- Select Age --</option>';
                    for (let i = 0; i <= 72; i++) {
                        const years = Math.floor(i / 12);
                        const months = i % 12;
                        let display = '';
                        
                        if (years === 0) {
                            display = months === 1 ? "1 month" : months + " months";
                        } else if (months === 0) {
                            display = years === 1 ? "1 year" : years + " years";
                        } else {
                            const yearText = years === 1 ? "1 year" : years + " years";
                            const monthText = months === 1 ? "1 month" : months + " months";
                            display = yearText + " " + monthText;
                        }
                        ageOptions += `<option value="${i}">${display}</option>`;
                    }
                    firstAgeSelect.innerHTML = ageOptions;
                }
            } else {
                const firstVaccineSelect = document.querySelector('#immunizationTableBody select[name="vaccine_name[]"]');
                if (firstVaccineSelect) {
                    firstVaccineSelect.innerHTML = '<option value="">-- Select Child First --</option>';
                }
                const firstAgeSelect = document.querySelector('#immunizationTableBody select[name="age[]"]');
                if (firstAgeSelect) {
                    firstAgeSelect.innerHTML = '<option value="">-- Select Child First --</option>';
                }
            }
        }
        
        childSelect.addEventListener('change', toggleTableInputs);
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleTableInputs();
        });
        
        function addRow() {
            if (!childSelect.value) {
                alert('Please select a child first!');
                return;
            }
            
            const tbody = document.getElementById('immunizationTableBody');
            const row = tbody.insertRow();
            
            let html = `
                <td>
                    <select name="vaccine_name[]" class="immunization-select" required>
                        <option value="">-- Select Vaccine --</option>
            `;
            
            vaccineNames.forEach(vaccine => {
                html += `<option value="${vaccine}">${vaccine}</option>`;
            });
            
            html += `
                    </select>
                </td>
                <td>
                    <select name="age[]" class="immunization-select" required>
                        <option value="">-- Select Age --</option>
            `;
            
            // Generate age options (0 to 72 months)
            for (let i = 0; i <= 72; i++) {
                const years = Math.floor(i / 12);
                const months = i % 12;
                let display = '';
                
                if (years === 0) {
                    display = months === 1 ? "1 month" : months + " months";
                } else if (months === 0) {
                    display = years === 1 ? "1 year" : years + " years";
                } else {
                    const yearText = years === 1 ? "1 year" : years + " years";
                    const monthText = months === 1 ? "1 month" : months + " months";
                    display = yearText + " " + monthText;
                }
                
                html += `<option value="${i}">${display}</option>`;
            }
            
            html += `
                    </select>
                </td>
                <td><input type="text" name="dose_number[]" class="immunization-input" placeholder="e.g. 1st, 2nd" required></td>
                <td><input type="date" name="date[]" class="immunization-input" required></td>
                <td><input type="text" name="given_by[]" class="immunization-input" placeholder="Doctor/Nurse name" required></td>
            `;
            
            row.innerHTML = html;
        }
    </script>
        </div>
    </div>
@endsection
