@extends('layouts.app')

@section('title', 'Growth Chart')

@section('content')
    @php
        // Use the data passed from controller directly
        $weightRecords = $weightRecords ?? [];
        $heightRecords = $heightRecords ?? [];
        $headRecords = $headRecords ?? [];
        
        // Ensure they are arrays
        if (!is_array($weightRecords)) {
            $weightRecords = [];
        }
        if (!is_array($heightRecords)) {
            $heightRecords = [];
        }
        if (!is_array($headRecords)) {
            $headRecords = [];
        }

        $tableRecords = $weightRecords;

        $userRole = session('user_role', '');
        $isNurse = $userRole === 'nurse';
        $selectedChildId = $selectedChildId ?? $childId ?? null;
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

        .growth-wrapper {
            background: linear-gradient(135deg, #fff5f7 0%, #ffeef0 50%, #ffe5e8 100%);
            border-radius: 24px;
            padding: 40px;
            margin: 0 auto;
            max-width: 1200px;
            box-shadow: 0 20px 60px rgba(255, 111, 145, 0.15), 0 0 0 1px rgba(255, 182, 193, 0.1);
        }

        .child-selector-wrapper {
            background: white;
            border-radius: 16px;
            padding: 20px 24px;
            margin-bottom: 28px;
            box-shadow: 0 4px 20px rgba(255, 111, 145, 0.1);
            border: 1px solid rgba(255, 182, 193, 0.3);
            transition: all 0.3s ease;
        }

        .child-selector-wrapper:hover {
            box-shadow: 0 6px 30px rgba(255, 111, 145, 0.15);
            transform: translateY(-2px);
        }

        .child-selector-wrapper label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #1a1a1a;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .child-selector-wrapper label::before {
            content: "👶";
            font-size: 16px;
        }

        .child-selector-wrapper select {
            width: 100%;
            padding: 14px 16px;
            border-radius: 12px;
            border: 2px solid #ffe5e8;
            background: linear-gradient(to bottom, #fff, #fff8f8);
            font-size: 15px;
            color: #1a1a1a;
            transition: all 0.3s ease;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%231a1a1a' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            padding-right: 45px;
        }

        .child-selector-wrapper select:focus {
            outline: none;
            border-color: #ff6f91;
            box-shadow: 0 0 0 4px rgba(255, 111, 145, 0.1);
            background: white;
        }

        .growth-grid {
            display: grid;
            grid-template-columns: 1.5fr 0.75fr;
            gap: 32px;
        }

        .growth-grid-doctor {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0;
        }

        .chart-with-analysis {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
            margin-bottom: 24px;
            align-items: start;
        }

        .chart-with-analysis .ai-analysis-section {
            height: 350px;
            display: flex;
            flex-direction: column;
            margin: 0;
            min-width: 0;
        }

        .chart-with-analysis .ai-analysis-section #growthAlerts {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            min-height: 0;
        }

        @media (max-width: 1024px) {
            .chart-with-analysis {
                grid-template-columns: 1fr;
            }
        }

        .panel {
            background: white;
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 8px 32px rgba(255, 111, 145, 0.12), 0 0 0 1px rgba(255, 182, 193, 0.2);
            transition: all 0.3s ease;
        }

        .panel:hover {
            box-shadow: 0 12px 40px rgba(255, 111, 145, 0.18), 0 0 0 1px rgba(255, 182, 193, 0.3);
        }

        .chart-container {
            width: 100%;
            height: 350px;
            background: linear-gradient(135deg, #fff 0%, #fff8f8 100%);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 24px;
            box-shadow: inset 0 2px 8px rgba(255, 182, 193, 0.1);
        }

        .ai-analysis-section {
            background: linear-gradient(135deg, #fff8f8 0%, #fff 100%);
            border-radius: 16px;
            padding: 24px;
            margin: 24px 0;
            border: 2px solid rgba(255, 182, 193, 0.3);
            box-shadow: 0 4px 16px rgba(255, 111, 145, 0.08);
        }

        .ai-analysis-section h3 {
            text-transform: uppercase;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 16px;
            color: #1a1a1a;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .ai-analysis-section h3::before {
            content: "🤖";
            font-size: 20px;
        }

        .growth-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 24px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(255, 111, 145, 0.08);
        }

        .growth-table thead {
            background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
        }

        .growth-table th {
            color: white;
            text-transform: uppercase;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.8px;
            padding: 16px 12px;
            text-align: center;
            border: none;
        }

        .growth-table tbody tr {
            transition: all 0.2s ease;
        }

        .growth-table tbody tr:nth-child(even) {
            background-color: #fff8f8;
        }

        .growth-table tbody tr:nth-child(odd) {
            background-color: white;
        }

        .growth-table tbody tr:hover {
            background-color: #ffeef0;
            transform: scale(1.01);
            box-shadow: 0 2px 8px rgba(255, 111, 145, 0.1);
        }

        .growth-table td {
            padding: 14px 12px;
            text-align: center;
            font-size: 14px;
            color: #1a1a1a;
            border: none;
            border-bottom: 1px solid rgba(255, 182, 193, 0.2);
        }

        .form-field {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 18px;
        }

        .form-field label {
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #1a1a1a;
        }

        .form-input {
            border: 2px solid #ffe5e8;
            border-radius: 12px;
            padding: 12px 16px;
            background: linear-gradient(to bottom, #fff, #fff8f8);
            font-size: 14px;
            color: #1a1a1a;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #ff6f91;
            box-shadow: 0 0 0 4px rgba(255, 111, 145, 0.1);
            background: white;
        }

        .submit-btn {
            width: 100%;
            border: none;
            padding: 16px;
            border-radius: 12px;
            background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
            color: white;
            text-transform: uppercase;
            font-weight: 700;
            font-size: 14px;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 16px rgba(255, 111, 145, 0.3);
            margin-top: 8px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(255, 111, 145, 0.4);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .form-section-title {
            text-transform: uppercase;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 24px;
            color: #1a1a1a;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 12px;
            border-bottom: 2px solid rgba(255, 182, 193, 0.3);
        }

        .form-section-title::before {
            content: "➕";
            font-size: 18px;
        }

        @media (max-width: 992px) {
            .growth-grid {
                grid-template-columns: 1fr;
            }
            
            .growth-wrapper {
                padding: 24px;
            }
        }
    </style>

    <div class="page-wrapper">
        <div class="page-header">
            <h1 class="page-title">📊 Growth Chart</h1>
        </div>

    <div class="growth-wrapper">
        <!-- Validation Errors (inline) -->
        @if($errors->any())
            <div style="background-color: #f8d7da; color: #721c24; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #dc3545;">
                <strong>❌ Validation Errors:</strong>
                <ul style="margin: 8px 0 0 20px; padding: 0;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Child selector for both nurses and doctors --}}
        <div class="child-selector-wrapper">
            <label>Select Child</label>
            <select id="childSelector" name="child_id" onchange="
                const selectedValue = this.value || '';
                if (selectedValue) {
                    localStorage.setItem('growthChartSelectedChild', selectedValue);
                    window.location.href='{{ route('growth.record') }}?child_id=' + selectedValue;
                } else {
                    localStorage.removeItem('growthChartSelectedChild');
                    window.location.href='{{ route('growth.record') }}';
                }
            ">
                <option value="">-- Select Child --</option>
                @foreach($children as $child)
                    @php
                        $cid = $child->ChildID ?? ($child['ChildID'] ?? '');
                        $cname = $child->FullName ?? ($child['FullName'] ?? $cid);
                        // Only select if explicitly in URL parameter (for nurses, don't auto-select on first visit)
                        $currentSelected = request('child_id') === $cid ? $cid : null;
                    @endphp
                    @if(!empty($cid))
                        <option value="{{ $cid }}" {{ $currentSelected === $cid ? 'selected' : '' }}>
                            {{ $cid }} - {{ $cname }}
                        </option>
                    @endif
                @endforeach
            </select>
        </div>

        <div class="growth-grid {{ !$isNurse ? 'growth-grid-doctor' : '' }}">
            <div class="panel">
                @if(!$isNurse)
                <div class="chart-with-analysis">
                    <div class="chart-container" id="chartContainer">
                        <canvas id="growthChart"></canvas>
                </div>

                    <div class="ai-analysis-section">
                        <h3>AI Growth Analysis</h3>
                        <div id="growthAlerts">
                            <div style="padding: 20px; background: linear-gradient(135deg, #fff 0%, #fff8f8 100%); border-radius: 12px; color: #1a1a1a; font-size: 14px; text-align: center; border: 2px dashed rgba(255, 182, 193, 0.3);">
                                <span style="display: inline-block; margin-right: 8px;">⏳</span>
                                Analyzing growth data...
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="chart-container" id="chartContainer">
                    <canvas id="growthChart"></canvas>
                </div>

                <div class="ai-analysis-section">
                    <h3>AI Growth Analysis</h3>
                    <div id="growthAlerts">
                        <div style="padding: 20px; background: linear-gradient(135deg, #fff 0%, #fff8f8 100%); border-radius: 12px; color: #1a1a1a; font-size: 14px; text-align: center; border: 2px dashed rgba(255, 182, 193, 0.3);">
                            <span style="display: inline-block; margin-right: 8px;">⏳</span>
                            Analyzing growth data...
                        </div>
                    </div>
                </div>
                @endif

                <table class="growth-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Age (month)</th>
                            <th>Weight (kg)</th>
                            <th>Height (cm)</th>
                            <th>Head Circumference (cm)</th>
                        </tr>
                    </thead>
                    <tbody id="growthTableBody">
                        @php
                            $hasSelectedChild = !empty($selectedChildId ?? $childId);
                        @endphp
                        @if($hasSelectedChild && count($tableRecords) > 0)
                        @foreach ($tableRecords as $record)
                                @php
                                    // Format age like dropdown (e.g., "5 years 9 months")
                                    $ageMonths = isset($record['age']) ? (int)$record['age'] : 0;
                                    $years = floor($ageMonths / 12);
                                    $months = $ageMonths % 12;
                                    if ($years == 0) {
                                        $ageDisplay = $months == 1 ? "1 month" : "$months months";
                                    } elseif ($months == 0) {
                                        $ageDisplay = $years == 1 ? "1 year" : "$years years";
                                    } else {
                                        $yearText = $years == 1 ? "1 year" : "$years years";
                                        $monthText = $months == 1 ? "1 month" : "$months months";
                                        $ageDisplay = "$yearText $monthText";
                                    }
                                    
                                    // Format head circumference
                                    $headValue = isset($record['head']) && $record['head'] !== null && $record['head'] !== '' ? $record['head'] : null;
                                @endphp
                            <tr>
                                <td>{{ $record['date'] }}</td>
                                    <td>{{ $ageDisplay }}</td>
                                <td>{{ number_format($record['weight'], 1) }}</td>
                                <td>{{ number_format($record['height'], 1) }}</td>
                                    <td>{{ $headValue !== null ? number_format($headValue, 1) : '-' }}</td>
                            </tr>
                        @endforeach
                        @else
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 40px; color: #1a1a1a; font-style: italic;">
                                    @if(!$hasSelectedChild)
                                        Please select a child to view growth data.
                                    @else
                                        No growth data available
                                    @endif
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

                @if($isNurse)
            <div class="panel">
                <h3 class="form-section-title">
                    Add New Measurement
                </h3>
                <form id="measurementForm" method="POST" action="{{ route('growth.record.store') }}">
                    @csrf
                    <input type="hidden" name="child_id" id="formChildId" value="{{ $selectedChildId ?? $childId ?? '' }}" required>
                    <div class="form-field">
                        <label>Date Measured</label>
                            <input type="date" class="form-input" name="date_measured" required>
                    </div>
                    <div class="form-field">
                        <label>Age (month)</label>
                            <select class="form-input" name="age" required style="padding: 8px; border-radius: 4px; border: 1px solid #d8c4c4;">
                                <option value="">Select Age</option>
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
                    </div>
                    <div class="form-field">
                        <label>Weight (kg)</label>
                        <input type="number" class="form-input" name="weight" min="0" step="0.1" required>
                    </div>
                    <div class="form-field">
                        <label>Height (cm)</label>
                        <input type="number" class="form-input" name="height" min="0" step="0.1" required>
                    </div>
                    <div class="form-field">
                        <label>Head Circumference (cm)</label>
                            <input type="number" class="form-input" name="head_circumference" min="0" step="0.1">
                    </div>
                    <button type="submit" class="submit-btn">MEASURE</button>
                </form>
                </div>
                @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // WHO Growth Percentiles Data (for boys and girls, 0-72 months)
        // Format: [age in months] = { weight: {p5, p50, p85, p95}, height: {p5, p50, p85, p95}, head: {p5, p50, p85, p95} }
        // Note: This is simplified data. For production, use complete WHO/CDC tables
        const whoPercentiles = {
            // Weight (kg) percentiles by age (months) - Average of boys and girls
            weight: {
                0: {p5: 2.5, p50: 3.3, p85: 4.0, p95: 4.5},
                1: {p5: 3.2, p50: 4.2, p85: 5.0, p95: 5.6},
                2: {p5: 4.0, p50: 5.0, p85: 6.0, p95: 6.6},
                3: {p5: 4.7, p50: 5.8, p85: 6.9, p95: 7.5},
                6: {p5: 6.0, p50: 7.3, p85: 8.5, p95: 9.2},
                9: {p5: 7.2, p50: 8.6, p85: 9.8, p95: 10.5},
                12: {p5: 8.0, p50: 9.5, p85: 10.8, p95: 11.5},
                18: {p5: 9.2, p50: 10.8, p85: 12.2, p95: 13.0},
                24: {p5: 10.2, p50: 12.0, p85: 13.5, p95: 14.5},
                30: {p5: 11.0, p50: 13.0, p85: 14.5, p95: 15.5},
                36: {p5: 11.8, p50: 13.8, p85: 15.5, p95: 16.5},
                42: {p5: 12.5, p50: 14.5, p85: 16.5, p95: 17.5},
                48: {p5: 13.2, p50: 15.2, p85: 17.2, p95: 18.5},
                54: {p5: 13.8, p50: 15.8, p85: 18.0, p95: 19.5},
                60: {p5: 14.5, p50: 16.5, p85: 18.8, p95: 20.5},
                66: {p5: 15.2, p50: 17.2, p85: 19.5, p95: 21.5},
                72: {p5: 15.8, p50: 18.0, p85: 20.5, p95: 22.5}
            },
            // Height (cm) percentiles by age (months)
            height: {
                0: {p5: 47.0, p50: 50.0, p85: 53.0, p95: 55.0},
                1: {p5: 51.0, p50: 54.0, p85: 57.0, p95: 59.0},
                2: {p5: 54.0, p50: 57.5, p85: 61.0, p95: 63.0},
                3: {p5: 56.5, p50: 60.5, p85: 64.0, p95: 66.5},
                6: {p5: 62.0, p50: 66.0, p85: 70.0, p95: 72.5},
                9: {p5: 67.0, p50: 71.0, p85: 75.0, p95: 77.5},
                12: {p5: 71.0, p50: 75.0, p85: 79.0, p95: 82.0},
                18: {p5: 77.0, p50: 81.5, p85: 86.0, p95: 89.0},
                24: {p5: 82.0, p50: 87.0, p85: 92.0, p95: 95.5},
                30: {p5: 86.0, p50: 91.5, p85: 97.0, p95: 100.5},
                36: {p5: 90.0, p50: 95.5, p85: 101.0, p95: 105.0},
                42: {p5: 93.5, p50: 99.0, p85: 105.0, p95: 109.0},
                48: {p5: 96.5, p50: 102.5, p85: 108.5, p95: 112.5},
                54: {p5: 99.5, p50: 105.5, p85: 111.5, p95: 116.0},
                60: {p5: 102.0, p50: 108.5, p85: 114.5, p95: 119.0},
                66: {p5: 104.5, p50: 111.0, p85: 117.5, p95: 122.0},
                72: {p5: 107.0, p50: 113.5, p85: 120.0, p95: 124.5}
            },
            // Head Circumference (cm) percentiles by age (months)
            head: {
                0: {p5: 32.5, p50: 35.0, p85: 37.0, p95: 38.0},
                1: {p5: 35.0, p50: 37.5, p85: 39.5, p95: 40.5},
                2: {p5: 36.5, p50: 39.0, p85: 41.0, p95: 42.0},
                3: {p5: 38.0, p50: 40.5, p85: 42.5, p95: 43.5},
                6: {p5: 40.5, p50: 43.0, p85: 45.0, p95: 46.0},
                9: {p5: 42.0, p50: 44.5, p85: 46.5, p95: 47.5},
                12: {p5: 43.0, p50: 45.5, p85: 47.5, p95: 48.5},
                18: {p5: 44.5, p50: 47.0, p85: 49.0, p95: 50.0},
                24: {p5: 45.5, p50: 48.0, p85: 50.0, p95: 51.0},
                30: {p5: 46.5, p50: 49.0, p85: 51.0, p95: 52.0},
                36: {p5: 47.5, p50: 50.0, p85: 52.0, p95: 53.0},
                42: {p5: 48.0, p50: 50.5, p85: 52.5, p95: 53.5},
                48: {p5: 48.5, p50: 51.0, p85: 53.0, p95: 54.0},
                54: {p5: 49.0, p50: 51.5, p85: 53.5, p95: 54.5},
                60: {p5: 49.5, p50: 52.0, p85: 54.0, p95: 55.0},
                66: {p5: 50.0, p50: 52.5, p85: 54.5, p95: 55.5},
                72: {p5: 50.5, p50: 53.0, p85: 55.0, p95: 56.0}
            }
        };

        // Global datastore used by AI analysis helpers
        let dataStore = {
            weight: [],
            height: [],
            head: [],
        };

        // Store all data (unfiltered)
        const allDataStore = {
            weight: @json($weightRecords),
            height: @json($heightRecords),
            head: @json($headRecords),
        };

        // Function to filter data by child ID
        function filterDataByChild(childId) {
            if (!childId || childId === '') {
                return {
                    weight: [],
                    height: [],
                    head: [],
                };
            }
            
            return {
                weight: allDataStore.weight.filter(entry => entry.childId === childId),
                height: allDataStore.height.filter(entry => entry.childId === childId),
                head: allDataStore.head.filter(entry => entry.childId === childId),
            };
        }

        // Function to filter and update display
        function filterByChild(childId) {
            // Update dataStore with filtered data
            dataStore = filterDataByChild(childId);
            window.dataStore = dataStore;
            
            // Store selected child in localStorage for persistence
            if (childId) {
                localStorage.setItem('growthChartSelectedChild', childId);
            } else {
                localStorage.removeItem('growthChartSelectedChild');
            }
            
            // Get chart instance from global scope
            const chartInstance = window.growthChartVar;
            const chartContainer = document.getElementById('chartContainer');
            
            // Always show chart with percentile lines, even if no child data
            // Restore canvas if it was replaced
            if (chartContainer && !chartContainer.querySelector('canvas')) {
                chartContainer.innerHTML = '<canvas id="growthChart"></canvas>';
            }
            
            // Refresh chart if it exists, otherwise reload page to initialize
            if (chartInstance) {
                refreshChart();
            } else {
                // Reload page to properly initialize chart with filtered data
                const url = new URL(window.location);
                if (childId) {
                    url.searchParams.set('child_id', childId);
                } else {
                    url.searchParams.delete('child_id');
                }
                window.location.href = url.toString();
            }
            
            renderTable();
            displayGrowthAlerts();
        }
        
        // Helper function to prepare chart datasets
        function prepareChartDatasets(allAges) {
            // Always use full age range for percentile lines
            const fullAgeRange = Array.from({length: 73}, (_, i) => i);
            const hasData = dataStore.weight.length > 0 || dataStore.height.length > 0 || dataStore.head.length > 0;
            const chartAges = hasData ? allAges : fullAgeRange;
            
            return [
                // Weight percentile reference lines
                {
                    label: 'Weight - 5th Percentile (Normal Range)',
                    data: getPercentileData('weight', 'p5', true),
                    borderColor: '#4caf50',
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    borderWidth: 1,
                    borderDash: [5, 5],
                    pointRadius: 0,
                    fill: false,
                    yAxisID: 'y',
                    order: 10
                },
                {
                    label: 'Weight - 85th Percentile (Normal Range)',
                    data: getPercentileData('weight', 'p85', true),
                    borderColor: '#4caf50',
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    borderWidth: 1,
                    borderDash: [5, 5],
                    pointRadius: 0,
                    fill: '+1',
                    yAxisID: 'y',
                    order: 9
                },
                {
                    label: 'Weight - 95th Percentile (Borderline)',
                    data: getPercentileData('weight', 'p95', true),
                    borderColor: '#ff9800',
                    backgroundColor: 'rgba(255, 152, 0, 0.1)',
                    borderWidth: 1,
                    borderDash: [3, 3],
                    pointRadius: 0,
                    fill: false,
                    yAxisID: 'y',
                    order: 8
                },
                // Actual Weight data (only if we have data)
                ...(hasData ? [{
                    label: 'Weight (kg) - Child',
                    data: allAges.map(age => getDataForAge('weight', age)),
                    borderColor: '#e91e63',
                    backgroundColor: 'rgba(233, 30, 99, 0.15)',
                    pointBorderColor: '#e91e63',
                    borderWidth: 3,
                    tension: 0.3,
                    fill: false,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    yAxisID: 'y',
                    order: 1
                }] : []),
                // Height percentile reference lines
                {
                    label: 'Height - 5th Percentile (Normal Range)',
                    data: getPercentileData('height', 'p5', true),
                    borderColor: '#4caf50',
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    borderWidth: 1,
                    borderDash: [5, 5],
                    pointRadius: 0,
                    fill: false,
                    yAxisID: 'y1',
                    order: 10
                },
                {
                    label: 'Height - 85th Percentile (Normal Range)',
                    data: getPercentileData('height', 'p85', true),
                    borderColor: '#4caf50',
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    borderWidth: 1,
                    borderDash: [5, 5],
                    pointRadius: 0,
                    fill: '+1',
                    yAxisID: 'y1',
                    order: 9
                },
                {
                    label: 'Height - 95th Percentile (Borderline)',
                    data: getPercentileData('height', 'p95', true),
                    borderColor: '#ff9800',
                    backgroundColor: 'rgba(255, 152, 0, 0.1)',
                    borderWidth: 1,
                    borderDash: [3, 3],
                    pointRadius: 0,
                    fill: false,
                    yAxisID: 'y1',
                    order: 8
                },
                // Actual Height data (only if we have data)
                ...(hasData ? [{
                    label: 'Height (cm) - Child',
                    data: allAges.map(age => getDataForAge('height', age)),
                    borderColor: '#2196f3',
                    backgroundColor: 'rgba(33, 150, 243, 0.15)',
                    pointBorderColor: '#2196f3',
                    borderWidth: 3,
                    tension: 0.3,
                    fill: false,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    yAxisID: 'y1',
                    order: 1
                }] : []),
                // Head Circumference percentile reference lines
                {
                    label: 'Head - 5th Percentile (Normal Range)',
                    data: getPercentileData('head', 'p5'),
                    borderColor: '#4caf50',
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    borderWidth: 1,
                    borderDash: [5, 5],
                    pointRadius: 0,
                    fill: false,
                    yAxisID: 'y',
                    order: 10
                },
                {
                    label: 'Head - 85th Percentile (Normal Range)',
                    data: getPercentileData('head', 'p85', true),
                    borderColor: '#4caf50',
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    borderWidth: 1,
                    borderDash: [5, 5],
                    pointRadius: 0,
                    fill: '+1',
                    yAxisID: 'y',
                    order: 9
                },
                {
                    label: 'Head - 95th Percentile (Borderline)',
                    data: getPercentileData('head', 'p95', true),
                    borderColor: '#ff9800',
                    backgroundColor: 'rgba(255, 152, 0, 0.1)',
                    borderWidth: 1,
                    borderDash: [3, 3],
                    pointRadius: 0,
                    fill: false,
                    yAxisID: 'y',
                    order: 8
                },
                // Actual Head Circumference data (only if we have data)
                ...(hasData ? [{
                    label: 'Head Circumference (cm) - Child',
                    data: allAges.map(age => getDataForAge('head', age)),
                    borderColor: '#9c27b0',
                    backgroundColor: 'rgba(156, 39, 176, 0.15)',
                    pointBorderColor: '#9c27b0',
                    borderWidth: 3,
                    tension: 0.3,
                    fill: false,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    yAxisID: 'y',
                    order: 1
                }] : [])
            ];
        }
        
        // Helper function to get chart options
        function getChartOptions() {
            return {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Age (months)'
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Weight (kg) / Head Circumference (cm)'
                        },
                        beginAtZero: false
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Height (cm)'
                        },
                        beginAtZero: false,
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            boxWidth: 12,
                            filter: function(item, chart) {
                                return item.text.includes('Child') || 
                                       item.text.includes('5th Percentile') || 
                                       item.text.includes('85th Percentile') || 
                                       item.text.includes('95th Percentile');
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y.toFixed(1);
                                    if (label.includes('Weight')) label += ' kg';
                                    if (label.includes('Height')) label += ' cm';
                                    if (label.includes('Head')) label += ' cm';
                                }
                                return label;
                            }
                        }
                    }
                }
            };
        }

        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing chart...');
            
            // Get initially selected child (if any)
            const childSelector = document.getElementById('childSelector');
            
            // Check URL parameter first, then localStorage, then dropdown value
            const urlParams = new URLSearchParams(window.location.search);
            const urlChildId = urlParams.get('child_id');
            const storedChildId = localStorage.getItem('growthChartSelectedChild');
            const dropdownValue = childSelector ? childSelector.value : '';
            
            // For nurses: Only use URL parameter, don't auto-select from localStorage on first visit
            // This ensures nurses see "Select Child" by default on first visit
            let initialChildId = urlChildId; // Only use URL parameter for nurses
            
            // If URL has child_id, use it and update localStorage
            if (urlChildId) {
                if (childSelector && childSelector.value !== urlChildId) {
                    childSelector.value = urlChildId;
                }
                localStorage.setItem('growthChartSelectedChild', urlChildId);
            } else {
                // No URL parameter - clear localStorage and ensure dropdown shows default
                localStorage.removeItem('growthChartSelectedChild');
                if (childSelector && childSelector.value) {
                    childSelector.value = '';
                }
            }
            
            // Initialize dataStore with filtered data
            dataStore = filterDataByChild(initialChildId);
            window.dataStore = dataStore;
            
            console.log('Data loaded:', dataStore);

            const chartCanvas = document.getElementById('growthChart');
            if (!chartCanvas) {
                console.error('Chart canvas not found');
                return;
            }

            const ctx = chartCanvas.getContext('2d');
        const tableBody = document.getElementById('growthTableBody');
            let growthChart = null;
            
            // Make growthChart accessible globally for filterByChild function
            window.growthChartVar = null;

        const valueKey = metric => {
            if (metric === 'height') return 'height';
            if (metric === 'head') return 'head';
            return 'weight';
        };

        const labelMap = {
            weight: 'Weight (kg)',
            height: 'Height (cm)',
            head: 'Head Circumference (cm)'
        };

        // Combine all data and get unique ages
        const getAllAges = () => {
            const allAges = new Set();
            dataStore.weight.forEach(entry => allAges.add(entry.age));
            dataStore.height.forEach(entry => allAges.add(entry.age));
            dataStore.head.forEach(entry => allAges.add(entry.age));
            return Array.from(allAges).sort((a, b) => a - b);
        };

        const getDataForAge = (metric, age) => {
            const entry = dataStore[metric].find(e => e.age === age);
            if (!entry) return null;
            if (metric === 'weight') return entry.weight;
            if (metric === 'height') return entry.height;
            if (metric === 'head') return entry.head;
            return null;
        };

        // Check if we have data before initializing chart
        const hasData = dataStore.weight.length > 0 || dataStore.height.length > 0 || dataStore.head.length > 0;
        
        // Always use full age range (0-72 months) for both labels and percentile lines to ensure consistency
        const fullAgeRange = Array.from({length: 73}, (_, i) => i); // 0 to 72 months
        // Use full age range for chart labels to match percentile data
        const allAges = fullAgeRange; // Always use full range for consistent percentile display
        const chartAges = hasData ? getAllAges() : fullAgeRange; // Use actual ages only for child data points

        // Interpolate percentile values for ages not in the table
        function getPercentileValue(metric, age, percentile) {
            const data = whoPercentiles[metric];
            if (!data) return null;
            
            // Find closest age keys
            const ages = Object.keys(data).map(Number).sort((a, b) => a - b);
            if (age <= ages[0]) return data[ages[0]][percentile];
            if (age >= ages[ages.length - 1]) return data[ages[ages.length - 1]][percentile];
            
            // Find surrounding ages
            let lowerAge = ages[0];
            let upperAge = ages[ages.length - 1];
            for (let i = 0; i < ages.length - 1; i++) {
                if (age >= ages[i] && age <= ages[i + 1]) {
                    lowerAge = ages[i];
                    upperAge = ages[i + 1];
                    break;
                }
            }
            
            // Linear interpolation
            const lowerValue = data[lowerAge][percentile];
            const upperValue = data[upperAge][percentile];
            const ratio = (age - lowerAge) / (upperAge - lowerAge);
            return lowerValue + (upperValue - lowerValue) * ratio;
        }

        // Generate percentile reference lines for the chart
        // Always use full age range (0-72 months) for percentile lines to ensure consistency
        function getPercentileData(metric, percentile, useFullRange = false) {
            // Always use full age range (0-72 months) for percentile lines to match labels
            const ageRange = Array.from({length: 73}, (_, i) => i); // 0 to 72 months
            return ageRange.map(age => {
                const value = getPercentileValue(metric, age, percentile);
                return value !== null && value !== undefined ? value : null;
            });
        }

        // Prepare datasets with percentile reference lines
        // Always show percentile lines using full age range for consistency
        const datasets = [
            // Weight percentile reference lines
            {
                label: 'Weight - 5th Percentile (Normal Range)',
                data: getPercentileData('weight', 'p5', true),
                borderColor: '#4caf50',
                backgroundColor: 'rgba(76, 175, 80, 0.1)',
                borderWidth: 1,
                borderDash: [5, 5],
                pointRadius: 0,
                fill: false,
                yAxisID: 'y',
                order: 10
            },
            {
                label: 'Weight - 85th Percentile (Normal Range)',
                data: getPercentileData('weight', 'p85', true),
                borderColor: '#4caf50',
                backgroundColor: 'rgba(76, 175, 80, 0.1)',
                borderWidth: 1,
                borderDash: [5, 5],
                pointRadius: 0,
                fill: '+1',
                yAxisID: 'y',
                order: 9
            },
            {
                label: 'Weight - 95th Percentile (Borderline)',
                data: getPercentileData('weight', 'p95', true),
                borderColor: '#ff9800',
                backgroundColor: 'rgba(255, 152, 0, 0.1)',
                borderWidth: 1,
                borderDash: [3, 3],
                pointRadius: 0,
                fill: false,
                yAxisID: 'y',
                order: 8
            },
                // Actual Weight data (only if we have data)
                ...(hasData ? [{
                    label: 'Weight (kg) - Child',
                    data: allAges.map(age => getDataForAge('weight', age)),
                    borderColor: '#e91e63',
                    backgroundColor: 'rgba(233, 30, 99, 0.15)',
                    pointBorderColor: '#e91e63',
                    borderWidth: 3,
                    tension: 0.3,
                    fill: false,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    yAxisID: 'y',
                    order: 1
                }] : []),
            // Height percentile reference lines
            {
                label: 'Height - 5th Percentile (Normal Range)',
                data: getPercentileData('height', 'p5', true),
                borderColor: '#4caf50',
                backgroundColor: 'rgba(76, 175, 80, 0.1)',
                borderWidth: 1,
                borderDash: [5, 5],
                pointRadius: 0,
                fill: false,
                yAxisID: 'y1',
                order: 10
            },
            {
                label: 'Height - 85th Percentile (Normal Range)',
                data: getPercentileData('height', 'p85', true),
                borderColor: '#4caf50',
                backgroundColor: 'rgba(76, 175, 80, 0.1)',
                borderWidth: 1,
                borderDash: [5, 5],
                pointRadius: 0,
                fill: '+1',
                yAxisID: 'y1',
                order: 9
            },
            {
                label: 'Height - 95th Percentile (Borderline)',
                data: getPercentileData('height', 'p95', true),
                borderColor: '#ff9800',
                backgroundColor: 'rgba(255, 152, 0, 0.1)',
                borderWidth: 1,
                borderDash: [3, 3],
                pointRadius: 0,
                fill: false,
                yAxisID: 'y1',
                order: 8
            },
                // Actual Height data (only if we have data)
                ...(hasData ? [{
                    label: 'Height (cm) - Child',
                    data: allAges.map(age => getDataForAge('height', age)),
                    borderColor: '#2196f3',
                    backgroundColor: 'rgba(33, 150, 243, 0.15)',
                    pointBorderColor: '#2196f3',
                    borderWidth: 3,
                    tension: 0.3,
                    fill: false,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    yAxisID: 'y1',
                    order: 1
                }] : []),
            // Head Circumference percentile reference lines
            {
                label: 'Head - 5th Percentile (Normal Range)',
                data: getPercentileData('head', 'p5', true),
                borderColor: '#4caf50',
                backgroundColor: 'rgba(76, 175, 80, 0.1)',
                borderWidth: 1,
                borderDash: [5, 5],
                pointRadius: 0,
                fill: false,
                yAxisID: 'y',
                order: 10
            },
            {
                label: 'Head - 85th Percentile (Normal Range)',
                data: getPercentileData('head', 'p85', true),
                borderColor: '#4caf50',
                backgroundColor: 'rgba(76, 175, 80, 0.1)',
                borderWidth: 1,
                borderDash: [5, 5],
                pointRadius: 0,
                fill: '+1',
                yAxisID: 'y',
                order: 9
            },
            {
                label: 'Head - 95th Percentile (Borderline)',
                data: getPercentileData('head', 'p95', true),
                borderColor: '#ff9800',
                backgroundColor: 'rgba(255, 152, 0, 0.1)',
                borderWidth: 1,
                borderDash: [3, 3],
                pointRadius: 0,
                fill: false,
                yAxisID: 'y',
                order: 8
            },
                // Actual Head Circumference data (only if we have data)
                ...(hasData ? [{
                    label: 'Head Circumference (cm) - Child',
                    data: allAges.map(age => getDataForAge('head', age)),
                    borderColor: '#9c27b0',
                    backgroundColor: 'rgba(156, 39, 176, 0.15)',
                    pointBorderColor: '#9c27b0',
                    borderWidth: 3,
                    tension: 0.3,
                    fill: false,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    yAxisID: 'y',
                    order: 1
                }] : [])
        ];

        // Destroy existing chart if it exists
        if (growthChart) {
            growthChart.destroy();
            growthChart = null;
        }

        console.log('Creating chart with', datasets.length, 'datasets');
        console.log('Chart labels (ages):', allAges);

        try {
            growthChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: allAges,
                    datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Age (months)'
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Weight (kg) / Head Circumference (cm)'
                        },
                        beginAtZero: false
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Height (cm)'
                        },
                        beginAtZero: false,
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            boxWidth: 12,
                            filter: function(item, chart) {
                                // Show only child data and key percentile lines in legend
                                return item.text.includes('Child') || 
                                       item.text.includes('5th Percentile') || 
                                       item.text.includes('85th Percentile') || 
                                       item.text.includes('95th Percentile');
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y.toFixed(1);
                                    if (label.includes('Weight')) label += ' kg';
                                    if (label.includes('Height')) label += ' cm';
                                    if (label.includes('Head')) label += ' cm';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });

            console.log('Chart created successfully');
        } catch (error) {
            console.error('Error creating chart:', error);
            alert('Error creating chart: ' + error.message);
        }

        // Display growth alerts on page load
        displayGrowthAlerts();

        function renderTable() {
            // Check if we have any data
            const hasData = dataStore.weight.length > 0 || dataStore.height.length > 0 || dataStore.head.length > 0;
            
            if (!hasData) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: #1a1a1a; font-style: italic;">
                            No growth data available
                        </td>
                    </tr>
                `;
                return;
            }
            
            // Combine all records and sort by age
            const allRecords = [];
            const recordMap = new Map();
            
            // Combine all data
            [...dataStore.weight, ...dataStore.height, ...dataStore.head].forEach(entry => {
                const key = `${entry.date}-${entry.age}`;
                if (!recordMap.has(key)) {
                    recordMap.set(key, {
                        date: entry.date,
                        age: entry.age,
                        weight: null,
                        height: null,
                        head: null
                    });
                }
                const record = recordMap.get(key);
                if (entry.weight !== undefined) record.weight = entry.weight;
                if (entry.height !== undefined) record.height = entry.height;
                if (entry.head !== undefined) record.head = entry.head;
            });
            
            const sortedRecords = Array.from(recordMap.values()).sort((a, b) => a.age - b.age);
            
            const rows = sortedRecords
                .map(entry => {
                    // Format age like dropdown (e.g., "5 years 9 months")
                    const ageMonths = Number(entry.age);
                    const years = Math.floor(ageMonths / 12);
                    const months = ageMonths % 12;
                    let ageDisplay = '';
                    if (years === 0) {
                        ageDisplay = months === 1 ? "1 month" : months + " months";
                    } else if (months === 0) {
                        ageDisplay = years === 1 ? "1 year" : years + " years";
                    } else {
                        const yearText = years === 1 ? "1 year" : years + " years";
                        const monthText = months === 1 ? "1 month" : months + " months";
                        ageDisplay = yearText + " " + monthText;
                    }
                    
                    return `
                    <tr>
                        <td>${entry.date}</td>
                        <td>${ageDisplay}</td>
                        <td>${entry.weight !== null ? entry.weight.toFixed(1) : '-'}</td>
                        <td>${entry.height !== null ? entry.height.toFixed(1) : '-'}</td>
                        <td>${entry.head !== null ? entry.head.toFixed(1) : '-'}</td>
                    </tr>
                `;
                })
                .join('');
            tableBody.innerHTML = rows;
        }

        function refreshChart() {
            const chartInstance = window.growthChartVar || growthChart;
            if (!chartInstance) return;
            
            // Always use full age range (0-72 months) for labels to match percentile data
            const fullAgeRange = Array.from({length: 73}, (_, i) => i); // 0 to 72 months
            
            // Update labels to full age range
            chartInstance.data.labels = fullAgeRange;
            
            // Update all datasets
            chartInstance.data.datasets.forEach((dataset, index) => {
                if (dataset.label.includes('Weight - 5th')) {
                    dataset.data = getPercentileData('weight', 'p5', true);
                } else if (dataset.label.includes('Weight - 85th')) {
                    dataset.data = getPercentileData('weight', 'p85', true);
                } else if (dataset.label.includes('Weight - 95th')) {
                    dataset.data = getPercentileData('weight', 'p95', true);
                } else if (dataset.label.includes('Weight (kg) - Child')) {
                    dataset.data = fullAgeRange.map(age => getDataForAge('weight', age));
                } else if (dataset.label.includes('Height - 5th')) {
                    dataset.data = getPercentileData('height', 'p5', true);
                } else if (dataset.label.includes('Height - 85th')) {
                    dataset.data = getPercentileData('height', 'p85', true);
                } else if (dataset.label.includes('Height - 95th')) {
                    dataset.data = getPercentileData('height', 'p95', true);
                } else if (dataset.label.includes('Height (cm) - Child')) {
                    dataset.data = fullAgeRange.map(age => getDataForAge('height', age));
                } else if (dataset.label.includes('Head - 5th')) {
                    dataset.data = getPercentileData('head', 'p5', true);
                } else if (dataset.label.includes('Head - 85th')) {
                    dataset.data = getPercentileData('head', 'p85', true);
                } else if (dataset.label.includes('Head - 95th')) {
                    dataset.data = getPercentileData('head', 'p95', true);
                } else if (dataset.label.includes('Head Circumference (cm) - Child')) {
                    dataset.data = fullAgeRange.map(age => getDataForAge('head', age));
                }
            });
            
            chartInstance.update();
            displayGrowthAlerts();
            renderTable();
        }

        renderTable();
        }); // End of DOMContentLoaded

        // Interpolate percentile values for ages not in the table
        function getPercentileValue(metric, age, percentile) {
            const data = whoPercentiles[metric];
            if (!data) return null;
            
            // Find closest age keys
            const ages = Object.keys(data).map(Number).sort((a, b) => a - b);
            if (age <= ages[0]) return data[ages[0]][percentile];
            if (age >= ages[ages.length - 1]) return data[ages[ages.length - 1]][percentile];
            
            // Find surrounding ages
            let lowerAge = ages[0];
            let upperAge = ages[ages.length - 1];
            for (let i = 0; i < ages.length - 1; i++) {
                if (age >= ages[i] && age <= ages[i + 1]) {
                    lowerAge = ages[i];
                    upperAge = ages[i + 1];
                    break;
                }
            }
            
            // Linear interpolation
            const lowerValue = data[lowerAge][percentile];
            const upperValue = data[upperAge][percentile];
            const ratio = (age - lowerAge) / (upperAge - lowerAge);
            return lowerValue + (upperValue - lowerValue) * ratio;
        }

        // Calculate child's percentile for a given measurement
        function calculatePercentile(metric, age, value) {
            const p5 = getPercentileValue(metric, age, 'p5');
            const p50 = getPercentileValue(metric, age, 'p50');
            const p85 = getPercentileValue(metric, age, 'p85');
            const p95 = getPercentileValue(metric, age, 'p95');
            
            if (!p5 || !p50 || !p85 || !p95 || value === null || value === undefined) return null;
            
            if (value < p5) return {percentile: '<5', status: 'abnormal', color: 'red'};
            if (value >= p5 && value < p85) return {percentile: '5-85', status: 'normal', color: 'green'};
            if (value >= p85 && value < p95) return {percentile: '85-95', status: 'borderline', color: 'yellow'};
            if (value >= p95) return {percentile: '>95', status: 'abnormal', color: 'red'};
            
            return null;
        }

        // Helper: sort data by age for a given metric (uses global dataStore)
        function sortedData(metric) {
            if (!dataStore || !dataStore[metric]) return [];
            return [...dataStore[metric]].sort((a, b) => a.age - b.age);
        }

        // Get latest percentile info for summary (per metric)
        function getLatestPercentiles() {
            const latestWeight = sortedData('weight').slice(-1)[0];
            const latestHeight = sortedData('height').slice(-1)[0];
            const latestHead = sortedData('head').slice(-1)[0];
            
            return {
                weight: latestWeight
                    ? {
                        value: latestWeight.weight,
                        age: latestWeight.age,
                        percentileInfo: calculatePercentile('weight', latestWeight.age, latestWeight.weight),
                    }
                    : null,
                height: latestHeight
                    ? {
                        value: latestHeight.height,
                        age: latestHeight.age,
                        percentileInfo: calculatePercentile('height', latestHeight.age, latestHeight.height),
                    }
                    : null,
                head: latestHead
                    ? {
                        value: latestHead.head,
                        age: latestHead.age,
                        percentileInfo: calculatePercentile('head', latestHead.age, latestHead.head),
                    }
                    : null,
            };
        }

        // Detect abnormal growth patterns
        function detectAbnormalGrowth() {
            const alerts = [];
            const latestWeight = sortedData('weight').slice(-1)[0];
            const latestHeight = sortedData('height').slice(-1)[0];
            const latestHead = sortedData('head').slice(-1)[0];
            
            if (latestWeight) {
                const weightPercentile = calculatePercentile('weight', latestWeight.age, latestWeight.weight);
                if (weightPercentile && weightPercentile.status === 'abnormal') {
                    const isHigh = weightPercentile.percentile === '>95';
                    const message = isHigh 
                        ? `⚠️ Weight is above normal range. Consult healthcare provider.`
                        : `⚠️ Weight is below normal range. Consult healthcare provider.`;
                    alerts.push({
                        type: 'abnormal',
                        metric: 'Weight',
                        value: latestWeight.weight,
                        age: latestWeight.age,
                        percentile: weightPercentile.percentile,
                        message: message
                    });
                } else if (weightPercentile && weightPercentile.status === 'borderline') {
                    alerts.push({
                        type: 'borderline',
                        metric: 'Weight',
                        value: latestWeight.weight,
                        age: latestWeight.age,
                        percentile: weightPercentile.percentile,
                        message: `⚠️ Weight is at upper normal range. Monitor closely.`
                    });
                }
            }
            
            if (latestHeight) {
                const heightPercentile = calculatePercentile('height', latestHeight.age, latestHeight.height);
                if (heightPercentile && heightPercentile.status === 'abnormal') {
                    const isHigh = heightPercentile.percentile === '>95';
                    const message = isHigh 
                        ? `⚠️ Height is above normal range. Consult healthcare provider.`
                        : `⚠️ Height is below normal range. Consult healthcare provider.`;
                    alerts.push({
                        type: 'abnormal',
                        metric: 'Height',
                        value: latestHeight.height,
                        age: latestHeight.age,
                        percentile: heightPercentile.percentile,
                        message: message
                    });
                } else if (heightPercentile && heightPercentile.status === 'borderline') {
                    alerts.push({
                        type: 'borderline',
                        metric: 'Height',
                        value: latestHeight.height,
                        age: latestHeight.age,
                        percentile: heightPercentile.percentile,
                        message: `⚠️ Height is at upper normal range. Monitor closely.`
                    });
                }
            }
            
            if (latestHead) {
                const headPercentile = calculatePercentile('head', latestHead.age, latestHead.head);
                if (headPercentile && headPercentile.status === 'abnormal') {
                    const isHigh = headPercentile.percentile === '>95';
                    const message = isHigh 
                        ? `⚠️ Head circumference is above normal range. Consult healthcare provider.`
                        : `⚠️ Head circumference is below normal range. Consult healthcare provider.`;
                    alerts.push({
                        type: 'abnormal',
                        metric: 'Head Circumference',
                        value: latestHead.head,
                        age: latestHead.age,
                        percentile: headPercentile.percentile,
                        message: message
                    });
                } else if (headPercentile && headPercentile.status === 'borderline') {
                    alerts.push({
                        type: 'borderline',
                        metric: 'Head Circumference',
                        value: latestHead.head,
                        age: latestHead.age,
                        percentile: headPercentile.percentile,
                        message: `⚠️ Head circumference is at upper normal range. Monitor closely.`
                    });
                }
            }
            
            // Detect sudden growth changes (trend analysis)
            const weightData = sortedData('weight');
            const heightData = sortedData('height');
            
            if (weightData.length >= 2) {
                const recent = weightData.slice(-2);
                const change = recent[1].weight - recent[0].weight;
                const timeDiff = recent[1].age - recent[0].age;
                const monthlyChange = timeDiff > 0 ? change / timeDiff : 0;
                
                // Flag if weight decreases significantly or increases too rapidly
                if (monthlyChange < -0.5) {
                    alerts.push({
                        type: 'trend',
                        metric: 'Weight',
                        message: `📉 Weight declining rapidly. Recommend checkup.`
                    });
                } else if (monthlyChange > 1.5) {
                    alerts.push({
                        type: 'trend',
                        metric: 'Weight',
                        message: `📈 Weight increasing rapidly. Recommend checkup.`
                    });
                }
            }
            
            if (heightData.length >= 2) {
                const recent = heightData.slice(-2);
                const change = recent[1].height - recent[0].height;
                const timeDiff = recent[1].age - recent[0].age;
                const monthlyChange = timeDiff > 0 ? change / timeDiff : 0;
                
                // Flag if height growth is stagnant or too rapid
                if (monthlyChange < 0.1 && timeDiff >= 3) {
                alerts.push({
                        type: 'trend',
                        metric: 'Height',
                        message: `📉 Height growth stagnant. Recommend checkup.`
                });
                } else if (monthlyChange > 2.0) {
                alerts.push({
                        type: 'trend',
                        metric: 'Height',
                        message: `📈 Height growing rapidly. Recommend checkup.`
                    });
                }
            }
            
            return alerts;
        }

        // Display growth alerts - Simplified version
        function displayGrowthAlerts() {
            const alertContainer = document.getElementById('growthAlerts');
            if (!alertContainer) return;
            
            // Check if we have data
            const hasData = dataStore.weight.length > 0 || dataStore.height.length > 0 || dataStore.head.length > 0;
            
            if (!hasData) {
                alertContainer.innerHTML = `
                    <div style="padding: 16px; background-color: #f5f5f5; border-radius: 8px; color: #1a1a1a; font-size: 14px; text-align: center;">
                        <strong>📊 No Data Available</strong><br>
                        <span style="font-size: 13px; margin-top: 8px; display: block;">Add a measurement to see growth analysis</span>
                    </div>
                `;
                return;
            }
            
            const alerts = detectAbnormalGrowth();
            const latest = getLatestPercentiles();

            // Determine overall status
            const overallAbnormal = alerts.some(a => a.type === 'abnormal');
            const overallBorderline = alerts.some(a => a.type === 'borderline');

            let statusIcon = '✅';
            let statusText = 'NORMAL';
            let statusBg = '#e8f5e9';
            let statusBorder = '#4caf50';
            let statusColor = '#2e7d32';

            if (overallAbnormal) {
                statusIcon = '⚠️';
                statusText = 'NEEDS ATTENTION';
                statusBg = '#ffe0e0';
                statusBorder = '#ff4444';
                statusColor = '#cc0000';
            } else if (overallBorderline) {
                statusIcon = '⚠️';
                statusText = 'MONITOR';
                statusBg = '#fff4e0';
                statusBorder = '#ff8800';
                statusColor = '#cc6600';
            }

            // Build simplified summary
            let html = `
                <div style="padding: 16px; margin-bottom: 16px; background-color: ${statusBg}; border-radius: 12px; border-left: 5px solid ${statusBorder};">
                    <div style="font-size: 16px; font-weight: 600; color: ${statusColor}; margin-bottom: 12px;">
                        ${statusIcon} ${statusText}
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
            `;

                // Simple metric status with colors matching chart
            if (latest.weight && latest.weight.percentileInfo) {
                const p = latest.weight.percentileInfo;
                let status = 'Good';
                let icon = '✓';
                let color = '#4caf50';
                if (p.status === 'abnormal') {
                    status = p.percentile === '<5' ? 'Low' : 'High';
                    icon = '⚠️';
                    color = '#d32f2f';
                } else if (p.status === 'borderline') {
                    status = 'Monitor';
                    icon = '⚠️';
                    color = '#ff9800';
                }
                html += `
                    <div style="padding: 10px; background: #fff; border-radius: 8px; border: 2px solid ${color}; text-align: center;">
                        <div style="font-size: 11px; color: #666; margin-bottom: 4px;">Weight</div>
                        <div style="font-size: 14px; font-weight: 600; color: ${color};">
                            ${icon} ${status}
                        </div>
                    </div>
                `;
            }

            if (latest.height && latest.height.percentileInfo) {
                const p = latest.height.percentileInfo;
                let status = 'Good';
                let icon = '✓';
                let color = '#4caf50';
                if (p.status === 'abnormal') {
                    status = p.percentile === '<5' ? 'Low' : 'High';
                    icon = '⚠️';
                    color = '#d32f2f';
                } else if (p.status === 'borderline') {
                    status = 'Monitor';
                    icon = '⚠️';
                    color = '#ff9800';
                }
                html += `
                    <div style="padding: 10px; background: #fff; border-radius: 8px; border: 2px solid ${color}; text-align: center;">
                        <div style="font-size: 11px; color: #666; margin-bottom: 4px;">Height</div>
                        <div style="font-size: 14px; font-weight: 600; color: ${color};">
                            ${icon} ${status}
                        </div>
                    </div>
                `;
            }

            if (latest.head && latest.head.percentileInfo) {
                const p = latest.head.percentileInfo;
                let status = 'Good';
                let icon = '✓';
                let color = '#4caf50';
                if (p.status === 'abnormal') {
                    status = p.percentile === '<5' ? 'Small' : 'Large';
                    icon = '⚠️';
                    color = '#d32f2f';
                } else if (p.status === 'borderline') {
                    status = 'Monitor';
                    icon = '⚠️';
                    color = '#ff9800';
                }
                html += `
                    <div style="padding: 10px; background: #fff; border-radius: 8px; border: 2px solid ${color}; text-align: center;">
                        <div style="font-size: 11px; color: #666; margin-bottom: 4px;">Head</div>
                        <div style="font-size: 14px; font-weight: 600; color: ${color};">
                            ${icon} ${status}
                        </div>
                    </div>
                `;
            }

            html += `</div></div>`;

            // Show only critical alerts (abnormal only)
            if (alerts.length > 0) {
                const criticalAlerts = alerts.filter(a => a.type === 'abnormal');
                
                if (criticalAlerts.length > 0) {
                    html += `
                        <div style="margin-top: 16px; padding: 14px; background: #fff3cd; border-radius: 8px; border-left: 4px solid #ffc107;">
                            <div style="font-size: 14px; font-weight: 600; margin-bottom: 8px; color: #856404;">⚠️ Action Required</div>
                    `;
                    
                    criticalAlerts.forEach(alert => {
                        html += `
                            <div style="padding: 8px 0; font-size: 13px; color: #856404; border-bottom: 1px solid rgba(255, 193, 7, 0.3);">
                                <strong>${alert.metric}:</strong> ${alert.message.replace(/^⚠️\s*/, '')}
                            </div>
                        `;
                    });
                    
                    html += `</div>`;
                }
            }
            
            alertContainer.innerHTML = html;
        }

        // Sync top child selector with form hidden input
        document.addEventListener('DOMContentLoaded', function() {
            const topSelector = document.getElementById('childSelector');
            const formChildId = document.getElementById('formChildId');
            const measurementForm = document.getElementById('measurementForm');

            if (topSelector && formChildId) {
                // Update form hidden input when top selector changes
                topSelector.addEventListener('change', function() {
                    formChildId.value = this.value;
                });

                // Validate form submission - ensure child is selected
                if (measurementForm) {
                    measurementForm.addEventListener('submit', function(e) {
                        const selectedChildId = formChildId.value || topSelector.value;
                        if (!selectedChildId || selectedChildId === '') {
                            e.preventDefault();
                            alert('Please select a child from the dropdown above before adding a new measurement.');
                            topSelector.focus();
                            return false;
                        }
                        // Ensure the hidden input has the correct value before submission
                        formChildId.value = selectedChildId;
                    });
                }
            }
        });
    </script>
        </div>
    </div>
@endsection

