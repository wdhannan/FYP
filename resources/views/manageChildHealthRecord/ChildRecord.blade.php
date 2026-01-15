@extends('layouts.app')

@section('title', 'Child Record')

@section('content')
    @php
        $childRecords = $childRecords ?? [];
        $childInfo = $childInfo ?? [];
        
        // WHO Growth Percentiles Data (simplified - same as in Growthchart.blade.php)
        $whoPercentiles = [
            'weight' => [
                0 => ['p5' => 2.5, 'p50' => 3.3, 'p85' => 4.0, 'p95' => 4.5],
                1 => ['p5' => 3.2, 'p50' => 4.2, 'p85' => 5.0, 'p95' => 5.6],
                2 => ['p5' => 4.0, 'p50' => 5.0, 'p85' => 6.0, 'p95' => 6.6],
                3 => ['p5' => 4.7, 'p50' => 5.8, 'p85' => 6.9, 'p95' => 7.5],
                6 => ['p5' => 6.0, 'p50' => 7.3, 'p85' => 8.5, 'p95' => 9.2],
                9 => ['p5' => 7.2, 'p50' => 8.6, 'p85' => 9.8, 'p95' => 10.5],
                12 => ['p5' => 8.0, 'p50' => 9.5, 'p85' => 10.8, 'p95' => 11.5],
                18 => ['p5' => 9.2, 'p50' => 10.8, 'p85' => 12.2, 'p95' => 13.0],
                24 => ['p5' => 10.2, 'p50' => 12.0, 'p85' => 13.5, 'p95' => 14.5],
                30 => ['p5' => 11.0, 'p50' => 13.0, 'p85' => 14.5, 'p95' => 15.5],
                36 => ['p5' => 11.8, 'p50' => 13.8, 'p85' => 15.5, 'p95' => 16.5],
                42 => ['p5' => 12.5, 'p50' => 14.5, 'p85' => 16.5, 'p95' => 17.5],
                48 => ['p5' => 13.2, 'p50' => 15.2, 'p85' => 17.2, 'p95' => 18.5],
                54 => ['p5' => 13.8, 'p50' => 15.8, 'p85' => 18.0, 'p95' => 19.5],
                60 => ['p5' => 14.5, 'p50' => 16.5, 'p85' => 18.8, 'p95' => 20.5],
                66 => ['p5' => 15.2, 'p50' => 17.2, 'p85' => 19.5, 'p95' => 21.5],
                72 => ['p5' => 15.8, 'p50' => 18.0, 'p85' => 20.5, 'p95' => 22.5]
            ],
            'height' => [
                0 => ['p5' => 47.0, 'p50' => 50.0, 'p85' => 53.0, 'p95' => 55.0],
                1 => ['p5' => 51.0, 'p50' => 54.0, 'p85' => 57.0, 'p95' => 59.0],
                2 => ['p5' => 54.0, 'p50' => 57.5, 'p85' => 61.0, 'p95' => 63.0],
                3 => ['p5' => 56.5, 'p50' => 60.5, 'p85' => 64.0, 'p95' => 66.5],
                6 => ['p5' => 62.0, 'p50' => 66.0, 'p85' => 70.0, 'p95' => 72.5],
                9 => ['p5' => 67.0, 'p50' => 71.0, 'p85' => 75.0, 'p95' => 77.5],
                12 => ['p5' => 71.0, 'p50' => 75.0, 'p85' => 79.0, 'p95' => 82.0],
                18 => ['p5' => 77.0, 'p50' => 81.5, 'p85' => 86.0, 'p95' => 89.0],
                24 => ['p5' => 82.0, 'p50' => 87.0, 'p85' => 92.0, 'p95' => 95.5],
                30 => ['p5' => 86.0, 'p50' => 91.5, 'p85' => 97.0, 'p95' => 100.5],
                36 => ['p5' => 90.0, 'p50' => 95.5, 'p85' => 101.0, 'p95' => 105.0],
                42 => ['p5' => 93.5, 'p50' => 99.0, 'p85' => 105.0, 'p95' => 109.0],
                48 => ['p5' => 96.5, 'p50' => 102.5, 'p85' => 108.5, 'p95' => 112.5],
                54 => ['p5' => 99.5, 'p50' => 105.5, 'p85' => 111.5, 'p95' => 116.0],
                60 => ['p5' => 102.0, 'p50' => 108.5, 'p85' => 114.5, 'p95' => 119.0],
                66 => ['p5' => 104.5, 'p50' => 111.0, 'p85' => 117.5, 'p95' => 122.0],
                72 => ['p5' => 107.0, 'p50' => 113.5, 'p85' => 120.0, 'p95' => 124.5]
            ],
            'head' => [
                0 => ['p5' => 32.5, 'p50' => 35.0, 'p85' => 37.0, 'p95' => 38.0],
                1 => ['p5' => 35.0, 'p50' => 37.5, 'p85' => 39.5, 'p95' => 40.5],
                2 => ['p5' => 36.5, 'p50' => 39.0, 'p85' => 41.0, 'p95' => 42.0],
                3 => ['p5' => 38.0, 'p50' => 40.5, 'p85' => 42.5, 'p95' => 43.5],
                6 => ['p5' => 40.5, 'p50' => 43.0, 'p85' => 45.0, 'p95' => 46.0],
                9 => ['p5' => 42.0, 'p50' => 44.5, 'p85' => 46.5, 'p95' => 47.5],
                12 => ['p5' => 43.0, 'p50' => 45.5, 'p85' => 47.5, 'p95' => 48.5],
                18 => ['p5' => 44.5, 'p50' => 47.0, 'p85' => 49.0, 'p95' => 50.0],
                24 => ['p5' => 45.5, 'p50' => 48.0, 'p85' => 50.0, 'p95' => 51.0],
                30 => ['p5' => 46.5, 'p50' => 49.0, 'p85' => 51.0, 'p95' => 52.0],
                36 => ['p5' => 47.5, 'p50' => 50.0, 'p85' => 52.0, 'p95' => 53.0],
                42 => ['p5' => 48.0, 'p50' => 50.5, 'p85' => 52.5, 'p95' => 53.5],
                48 => ['p5' => 48.5, 'p50' => 51.0, 'p85' => 53.0, 'p95' => 54.0],
                54 => ['p5' => 49.0, 'p50' => 51.5, 'p85' => 53.5, 'p95' => 54.5],
                60 => ['p5' => 49.5, 'p50' => 52.0, 'p85' => 54.0, 'p95' => 55.0],
                66 => ['p5' => 50.0, 'p50' => 52.5, 'p85' => 54.5, 'p95' => 55.5],
                72 => ['p5' => 50.5, 'p50' => 53.0, 'p85' => 55.0, 'p95' => 56.0]
            ]
        ];
        
        // Function to get percentile value for a given age (with interpolation)
        function getPercentileValue($metric, $age, $percentile, $whoPercentiles) {
            if (!isset($whoPercentiles[$metric])) return null;
            $data = $whoPercentiles[$metric];
            $ages = array_keys($data);
            sort($ages);
            
            if ($age <= $ages[0]) return $data[$ages[0]][$percentile] ?? null;
            if ($age >= $ages[count($ages) - 1]) return $data[$ages[count($ages) - 1]][$percentile] ?? null;
            
            // Find surrounding ages
            $lowerAge = $ages[0];
            $upperAge = $ages[count($ages) - 1];
            for ($i = 0; $i < count($ages) - 1; $i++) {
                if ($age >= $ages[$i] && $age <= $ages[$i + 1]) {
                    $lowerAge = $ages[$i];
                    $upperAge = $ages[$i + 1];
                    break;
                }
            }
            
            // Linear interpolation
            $lowerValue = $data[$lowerAge][$percentile] ?? null;
            $upperValue = $data[$upperAge][$percentile] ?? null;
            if ($lowerValue === null || $upperValue === null) return null;
            
            $ratio = ($age - $lowerAge) / ($upperAge - $lowerAge);
            return $lowerValue + ($upperValue - $lowerValue) * $ratio;
        }
        
        // Function to calculate growth status
        function calculateGrowthStatus($age, $weight, $height, $headCircumference, $whoPercentiles) {
            $statuses = [];
            $overallStatus = 'normal';
            
            // Check weight
            if ($weight !== null && $age !== null) {
                $p5 = getPercentileValue('weight', $age, 'p5', $whoPercentiles);
                $p85 = getPercentileValue('weight', $age, 'p85', $whoPercentiles);
                $p95 = getPercentileValue('weight', $age, 'p95', $whoPercentiles);
                
                if ($p5 !== null && $p85 !== null && $p95 !== null) {
                    if ($weight < $p5) {
                        $statuses['weight'] = ['status' => 'abnormal', 'label' => 'Underweight', 'color' => '#d32f2f'];
                        $overallStatus = 'abnormal';
                    } elseif ($weight >= $p5 && $weight < $p85) {
                        $statuses['weight'] = ['status' => 'normal', 'label' => 'Normal', 'color' => '#4caf50'];
                    } elseif ($weight >= $p85 && $weight < $p95) {
                        $statuses['weight'] = ['status' => 'borderline', 'label' => 'Borderline', 'color' => '#ff9800'];
                        if ($overallStatus === 'normal') $overallStatus = 'borderline';
                    } elseif ($weight >= $p95) {
                        $statuses['weight'] = ['status' => 'abnormal', 'label' => 'Overweight', 'color' => '#d32f2f'];
                        $overallStatus = 'abnormal';
                    }
                }
            }
            
            // Check height
            if ($height !== null && $age !== null) {
                $p5 = getPercentileValue('height', $age, 'p5', $whoPercentiles);
                $p85 = getPercentileValue('height', $age, 'p85', $whoPercentiles);
                $p95 = getPercentileValue('height', $age, 'p95', $whoPercentiles);
                
                if ($p5 !== null && $p85 !== null && $p95 !== null) {
                    if ($height < $p5) {
                        $statuses['height'] = ['status' => 'abnormal', 'label' => 'Short', 'color' => '#d32f2f'];
                        $overallStatus = 'abnormal';
                    } elseif ($height >= $p5 && $height < $p85) {
                        $statuses['height'] = ['status' => 'normal', 'label' => 'Normal', 'color' => '#4caf50'];
                    } elseif ($height >= $p85 && $height < $p95) {
                        $statuses['height'] = ['status' => 'borderline', 'label' => 'Borderline', 'color' => '#ff9800'];
                        if ($overallStatus === 'normal') $overallStatus = 'borderline';
                    } elseif ($height >= $p95) {
                        $statuses['height'] = ['status' => 'abnormal', 'label' => 'Tall', 'color' => '#d32f2f'];
                        $overallStatus = 'abnormal';
                    }
                }
            }
            
            // Determine overall label and color
            if ($overallStatus === 'abnormal') {
                return ['status' => 'abnormal', 'label' => 'Abnormal', 'color' => '#d32f2f', 'icon' => '⚠️'];
            } elseif ($overallStatus === 'borderline') {
                return ['status' => 'borderline', 'label' => 'Borderline', 'color' => '#ff9800', 'icon' => '⚡'];
            } else {
                return ['status' => 'normal', 'label' => 'Normal', 'color' => '#4caf50', 'icon' => '✓'];
            }
        }
    @endphp

    <style>
        body {
            background: linear-gradient(135deg, #ffeef4 0%, #fff5f8 50%, #ffeef4 100%);
            min-height: 100vh;
        }

        .child-record-wrapper {
            padding: 40px 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .child-info-card {
            background: linear-gradient(135deg, #ffffff 0%, #fff8fa 100%);
            border: 2px solid #ffe0e8;
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 40px;
            box-shadow: 0 4px 15px rgba(255, 158, 179, 0.1);
            position: relative;
            overflow: hidden;
        }

        .child-info-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
        }

        .child-info-title {
            font-size: 32px;
            font-weight: 800;
            color: #1a1a1a;
            margin-bottom: 24px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .child-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 24px;
        }

        .child-info-item {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .child-info-label {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            color: #ff6f91;
            letter-spacing: 0.5px;
        }

        .child-info-value {
            font-size: 18px;
            font-weight: 600;
            color: #1a1a1a;
        }

        .record-section {
            background: linear-gradient(135deg, #ffffff 0%, #fff8fa 100%);
            border: 2px solid #ffe0e8;
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 32px;
            box-shadow: 0 4px 15px rgba(255, 158, 179, 0.1);
            position: relative;
            overflow: hidden;
        }

        .record-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
        }

        .record-section-title {
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

        .record-section {
            overflow-x: auto;
        }

        .record-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            min-width: 100%;
        }

        .record-table thead {
            background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
        }

        .record-table th {
            padding: 16px;
            text-align: left;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            color: #fff;
            letter-spacing: 0.5px;
        }

        .record-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #ffe0e8;
            color: #1a1a1a;
            font-size: 14px;
        }

        .record-table tbody tr {
            transition: background-color 0.2s ease;
        }

        .record-table tbody tr:hover {
            background-color: #fff8fa;
        }

        .record-table tbody tr:last-child td {
            border-bottom: none;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
            font-size: 16px;
            font-weight: 500;
        }

        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        @media (max-width: 768px) {
            .child-info-grid {
                grid-template-columns: 1fr;
            }

            .record-table {
                font-size: 12px;
            }

            .record-table th,
            .record-table td {
                padding: 10px 8px;
            }
        }
    </style>

    <div class="child-record-wrapper">
        <!-- Birth Record -->
        <div class="record-section">
            <h3 class="record-section-title">📋 Birth Record</h3>
            @if(isset($childRecords['birth_record']) && $childRecords['birth_record'])
                @php
                    $birth = $childRecords['birth_record'];
                @endphp
                <table class="record-table">
                    <thead>
                        <tr>
                            <th>Date of Birth</th>
                            <th>Time of Birth</th>
                            <th>Birth Weight</th>
                            <th>Birth Length</th>
                            <th>Head Circumference</th>
                            <th>Gestational Age</th>
                            <th>Birth Place</th>
                            <th>Birth Type</th>
                            <th>Baby Count</th>
                            <th>Apgar Score</th>
                            <th>Blood Group</th>
                            <th>Vitamin K Given</th>
                            <th>Complications</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $childInfo['DateOfBirth'] ?? 'N/A' }}</td>
                            <td>{{ $birth->TimeOfBirth ? \Carbon\Carbon::parse($birth->TimeOfBirth)->format('g:i A') : 'N/A' }}</td>
                            <td>{{ $birth->BirthWeight ? $birth->BirthWeight . ' kg' : 'N/A' }}</td>
                            <td>{{ $birth->BirthLength ? $birth->BirthLength . ' cm' : 'N/A' }}</td>
                            <td>{{ $birth->BirthCircumference ? $birth->BirthCircumference . ' cm' : 'N/A' }}</td>
                            <td>{{ $birth->GestationalAgeWeeks ? $birth->GestationalAgeWeeks . ' weeks' : 'N/A' }}</td>
                            <td>{{ $birth->BirthPlace ?? 'N/A' }}</td>
                            <td>{{ $birth->BirthType ?? 'N/A' }}</td>
                            <td>{{ $birth->BabyCount ?? 'N/A' }}</td>
                            <td>{{ $birth->ApgarScore ?? 'N/A' }}</td>
                            <td>{{ $birth->BloodGroup ?? 'N/A' }}</td>
                            <td>{{ $birth->VitaminKGiven ?? 'N/A' }}</td>
                            <td>{{ $birth->Complications ?? 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">📋</div>
                    <div>No birth record available</div>
                </div>
            @endif
        </div>

        <!-- Immunization Records -->
        <div class="record-section">
            <h3 class="record-section-title">💉 Immunization Records</h3>
            @if(isset($childRecords['immunization']) && $childRecords['immunization']->count() > 0)
                <table class="record-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Vaccine Name</th>
                            <th>Age</th>
                            <th>Dose</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($childRecords['immunization'] as $immunization)
                            @php
                                $age = $immunization->Age ?? 0;
                                $years = floor($age / 12);
                                $months = $age % 12;
                                $ageDisplay = '';
                                if ($years === 0) {
                                    $ageDisplay = $months === 1 ? "1 month" : $months . " months";
                                } else if ($months === 0) {
                                    $ageDisplay = $years === 1 ? "1 year" : $years . " years";
                                } else {
                                    $yearText = $years === 1 ? "1 year" : $years . " years";
                                    $monthText = $months === 1 ? "1 month" : $months . " months";
                                    $ageDisplay = $yearText . " " . $monthText;
                                }
                            @endphp
                            <tr>
                                <td>{{ $immunization->Date ? \Carbon\Carbon::parse($immunization->Date)->format('F j, Y') : 'N/A' }}</td>
                                <td>{{ $immunization->VaccineName ?? 'N/A' }}</td>
                                <td>{{ $ageDisplay }}</td>
                                <td>{{ $immunization->DoseNumber ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">💉</div>
                    <div>No immunization records available</div>
                </div>
            @endif
        </div>

        <!-- Growth Chart Records -->
        <div class="record-section">
            <h3 class="record-section-title">📊 Growth Chart Records</h3>
            @if(isset($childRecords['growth_chart']) && $childRecords['growth_chart']->count() > 0)
                <table class="record-table">
                    <thead>
                        <tr>
                            <th>Date Measured</th>
                            <th>Age</th>
                            <th>Weight (kg)</th>
                            <th>Height (cm)</th>
                            <th>Head Circumference (cm)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($childRecords['growth_chart'] as $growth)
                            @php
                                $age = $growth->Age ?? 0;
                                $years = floor($age / 12);
                                $months = $age % 12;
                                $ageDisplay = '';
                                if ($years === 0) {
                                    $ageDisplay = $months === 1 ? "1 month" : $months . " months";
                                } else if ($months === 0) {
                                    $ageDisplay = $years === 1 ? "1 year" : $years . " years";
                                } else {
                                    $yearText = $years === 1 ? "1 year" : $years . " years";
                                    $monthText = $months === 1 ? "1 month" : $months . " months";
                                    $ageDisplay = $yearText . " " . $monthText;
                                }
                                
                                // Calculate growth status
                                $weight = $growth->Weight ?? null;
                                $height = $growth->Height ?? null;
                                $headCircumference = $growth->HeadCircumference ?? null;
                                $growthStatus = calculateGrowthStatus($age, $weight, $height, $headCircumference, $whoPercentiles);
                            @endphp
                            <tr>
                                <td>{{ $growth->DateMeasured ? \Carbon\Carbon::parse($growth->DateMeasured)->format('F j, Y') : 'N/A' }}</td>
                                <td>{{ $ageDisplay }}</td>
                                <td>{{ $weight ?? 'N/A' }}</td>
                                <td>{{ $height ?? 'N/A' }}</td>
                                <td>{{ $headCircumference ?? 'N/A' }}</td>
                                <td>
                                    @if($growthStatus)
                                        <span class="growth-status-badge" style="
                                            display: inline-block;
                                            padding: 6px 12px;
                                            border-radius: 20px;
                                            font-size: 12px;
                                            font-weight: 600;
                                            background-color: {{ $growthStatus['color'] }};
                                            color: white;
                                            text-transform: uppercase;
                                            letter-spacing: 0.5px;
                                        ">
                                            {{ $growthStatus['icon'] }} {{ $growthStatus['label'] }}
                                        </span>
                                    @else
                                        <span style="color: #999;">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">📊</div>
                    <div>No growth chart records available</div>
                </div>
            @endif
        </div>

        <!-- Screening Records -->
        <div class="record-section">
            <h3 class="record-section-title">🔍 Screening Records</h3>
            @if(isset($childRecords['screening']) && $childRecords['screening']->count() > 0)
                <table class="record-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Screening Type</th>
                            <th>Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($childRecords['screening'] as $screening)
                            <tr>
                                <td>{{ $screening->DateScreened ? \Carbon\Carbon::parse($screening->DateScreened)->format('F j, Y') : 'N/A' }}</td>
                                <td>{{ $screening->ScreeningType ?? 'N/A' }}</td>
                                <td>{{ $screening->Result ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">🔍</div>
                    <div>No screening records available</div>
                </div>
            @endif
        </div>

        <!-- Milestone Records -->
        <div class="record-section">
            <h3 class="record-section-title">🎯 Development Milestones</h3>
            @if(isset($childRecords['milestone']) && $childRecords['milestone']->count() > 0)
                <table class="record-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Milestone Type</th>
                            <th>Description</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($childRecords['milestone'] as $milestone)
                            <tr>
                                <td>{{ $milestone->created_at ? \Carbon\Carbon::parse($milestone->created_at)->format('F j, Y') : 'N/A' }}</td>
                                <td>{{ $milestone->MilestoneType ?? 'N/A' }}</td>
                                <td>{{ $milestone->Notes ?? 'N/A' }}</td>
                                <td>Recorded</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">🎯</div>
                    <div>No milestone records available</div>
                </div>
            @endif
        </div>

        <!-- Feeding Records -->
        <div class="record-section">
            <h3 class="record-section-title">🍼 Feeding Records</h3>
            @if(isset($childRecords['feeding']) && $childRecords['feeding']->count() > 0)
                <table class="record-table">
                    <thead>
                        <tr>
                            <th>Date Logged</th>
                            <th>Feeding Type</th>
                            <th>Frequency Per Day</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($childRecords['feeding'] as $feeding)
                            <tr>
                                <td>{{ $feeding->DateLogged ? \Carbon\Carbon::parse($feeding->DateLogged)->format('F j, Y') : 'N/A' }}</td>
                                <td>{{ $feeding->FeedingType ?? 'N/A' }}</td>
                                <td>{{ $feeding->FrequencyPerDay ?? 'N/A' }} times/day</td>
                                <td>{{ $feeding->Remarks ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">🍼</div>
                    <div>No feeding records available</div>
                </div>
            @endif
        </div>
    </div>
@endsection
