@extends('layouts.app')

@section('title', 'Child Screening Record')

@section('content')
    @php
        $screenings = $screenings ?? collect([]);
        $children = $children ?? collect([]);
        
        // Screening Types list
        $screeningTypes = [
            'Vision Test',
            'Hearing Test',
            'Head Circumference Measurement',
            'Newborn Screening (PKU, Hypothyroidism, etc.)',
            'TB Screening (Tuberculosis)',
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

        .screening-wrapper {
            background-color: #fde8e8;
            border-radius: 16px;
            padding: 30px;
            margin: 0 auto;
            max-width: 900px;
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

        .form-select {
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

        .screening-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 0;
        }

        .screening-table thead {
            background: linear-gradient(135deg, #ffe0e9 0%, #ffb6c1 100%);
            position: relative;
        }

        .screening-table thead::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #ff6f91, #ff9eb3);
        }

        .screening-table th {
            padding: 18px 20px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-size: 13px;
            font-weight: 700;
            color: #1c1c1c;
            text-align: left;
            border: none;
        }

        .screening-table th:first-child {
            border-top-left-radius: 16px;
        }

        .screening-table th:last-child {
            border-top-right-radius: 16px;
        }

        .screening-table tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid #f5f5f5;
        }

        .screening-table tbody tr:last-child {
            border-bottom: none;
        }

        .screening-table tbody tr:hover {
            background: linear-gradient(90deg, #fff5f5 0%, #ffe0e9 100%);
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(255, 111, 145, 0.1);
        }

        .screening-table td {
            padding: 16px 20px;
            border: none;
            text-transform: none;
            letter-spacing: 0;
            font-weight: 500;
            font-size: 14px;
            color: #333;
        }

        .screening-table tbody tr:nth-child(even) {
            background-color: #fff9fb;
        }

        .screening-table tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        .screening-table tbody tr:nth-child(even):hover,
        .screening-table tbody tr:nth-child(odd):hover {
            background: linear-gradient(90deg, #fff5f5 0%, #ffe0e9 100%);
        }

        .screening-table td select,
        .screening-table td input {
            width: 100%;
            border: none;
            background: transparent;
            font-size: 14px;
            font-weight: 500;
            color: #333;
        }

        .screening-save {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .screening-save button {
            background-color: #d9d1cc;
            border: none;
            padding: 12px 34px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .screening-save button:hover {
            background-color: #c3bcb8;
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
            <h1 class="page-title">🔍 Screening Record</h1>
        </div>

        <div class="screening-wrapper">

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

        @if($isNurse)
        <form id="screeningForm" method="POST" action="{{ route('screening.store') }}">
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
                <table class="screening-table">
                    <thead>
                        <tr>
                            <th>SCREENING TYPE</th>
                            <th>RESULT</th>
                            <th>DATE SCREENED</th>
                        </tr>
                    </thead>
                    <tbody id="screeningTableBody">
                    <tr>
                        <td>
                            <select name="screening_type[]" class="screening-select" required disabled>
                                <option value="">-- Select Child First --</option>
                            </select>
                        </td>
                        <td><input type="text" name="result[]" placeholder="Enter result" required disabled></td>
                        <td><input type="date" name="date_screened[]" required disabled></td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <button type="button" class="add-row-button" onclick="addRow()" id="addRowBtn" disabled>+ Add Row</button>

            <div class="screening-save">
                <button type="submit">Save</button>
            </div>
        </form>
        @else
        @php
            $selectedChildId = $selectedChildId ?? request('child_id');
        @endphp
        <div style="padding: 20px; text-align: center; color: #666;">
            {{-- Child selector for doctors to view a single child's records --}}
            <form method="GET" action="{{ route('screening.record') }}" style="margin: 15px auto 20px; max-width: 400px; text-align: left;">
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

            @if(($selectedChildId ?? '') !== '' && count($screenings) > 0)
                <div class="table-wrapper-modern" style="margin-top: 20px;">
                    <table class="screening-table">
                        <thead>
                            <tr>
                                <th>CHILD NAME</th>
                                <th>SCREENING TYPE</th>
                                <th>RESULT</th>
                                <th>DATE SCREENED</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($screenings as $screening)
                            <tr>
                                <td>{{ $screening->ChildName ?? 'N/A' }}</td>
                                <td>{{ $screening->ScreeningType ?? 'N/A' }}</td>
                                <td>{{ $screening->Result ?? 'N/A' }}</td>
                                <td>{{ $screening->DateScreened ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @elseif(($selectedChildId ?? '') !== '')
                <p>No screening records found for the selected child.</p>
            @endif
        </div>
        @endif
    </div>

    <script>
        const screeningTypes = @json($screeningTypes);
        const childSelect = document.querySelector('[name="child_id"]');
        const addRowBtn = document.getElementById('addRowBtn');
        
        function toggleTableInputs() {
            const childSelected = childSelect.value !== '';
            const tableInputs = document.querySelectorAll('#screeningTableBody input, #screeningTableBody select');
            
            tableInputs.forEach(input => {
                input.disabled = !childSelected;
            });
            
            addRowBtn.disabled = !childSelected;
            
            // Update first row select options
            if (childSelected) {
                const firstSelect = document.querySelector('#screeningTableBody select[name="screening_type[]"]');
                if (firstSelect) {
                    firstSelect.innerHTML = '<option value="">-- Select Screening Type --</option>' + 
                        screeningTypes.map(type => `<option value="${type}">${type}</option>`).join('');
                }
            } else {
                const firstSelect = document.querySelector('#screeningTableBody select[name="screening_type[]"]');
                if (firstSelect) {
                    firstSelect.innerHTML = '<option value="">-- Select Child First --</option>';
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
            
            const tbody = document.getElementById('screeningTableBody');
            const row = tbody.insertRow();
            
            let html = `
                <td>
                    <select name="screening_type[]" class="screening-select" required>
                        <option value="">-- Select Screening Type --</option>
            `;
            
            screeningTypes.forEach(type => {
                html += `<option value="${type}">${type}</option>`;
            });
            
            html += `
                    </select>
                </td>
                <td><input type="text" name="result[]" placeholder="Enter result" required></td>
                <td><input type="date" name="date_screened[]" required></td>
            `;
            
            row.innerHTML = html;
        }
    </script>
        </div>
    </div>
@endsection
