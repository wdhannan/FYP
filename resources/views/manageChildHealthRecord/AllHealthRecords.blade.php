@extends('layouts.app')

@section('title', 'Health Records Overview')

@section('content')
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
        text-align: center;
    }

    .page-title {
        font-size: 32px;
        font-weight: 800;
        color: #1a1a1a;
        text-transform: uppercase;
        letter-spacing: 1px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    .records-container {
        display: flex;
        flex-direction: column;
        gap: 30px;
    }

    .record-section {
        background: linear-gradient(135deg, #fff5f7 0%, #ffeef0 50%, #ffe5e8 100%);
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 8px 24px rgba(255, 111, 145, 0.15);
    }

    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 2px solid #ffe0e8;
    }

    .section-title {
        font-size: 20px;
        font-weight: 700;
        color: #1a1a1a;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .record-count {
        background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
        color: white;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 700;
    }

    .records-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 12px;
        overflow: hidden;
    }

    .records-table thead {
        background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
    }

    .records-table th {
        padding: 14px 16px;
        text-align: left;
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        color: white;
        letter-spacing: 0.5px;
    }

    .records-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #ffe0e8;
        color: #1a1a1a;
        font-size: 14px;
    }

    .records-table tbody tr {
        transition: background-color 0.2s ease;
    }

    .records-table tbody tr:hover {
        background-color: #fff8fa;
    }

    .records-table tbody tr:last-child td {
        border-bottom: none;
    }


    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #999;
        font-size: 15px;
        background: white;
        border-radius: 12px;
    }

    .filter-section {
        background: linear-gradient(135deg, #fff5f7 0%, #ffeef0 50%, #ffe5e8 100%);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 30px;
        box-shadow: 0 4px 12px rgba(255, 111, 145, 0.1);
    }

    .filter-form {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .filter-label {
        font-size: 14px;
        font-weight: 700;
        color: #1a1a1a;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .filter-select {
        padding: 12px 16px;
        border-radius: 10px;
        border: 2px solid #ffe0e8;
        background: white;
        font-size: 14px;
        font-weight: 600;
        color: #1a1a1a;
        min-width: 250px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .filter-select:focus {
        outline: none;
        border-color: #ff6f91;
        box-shadow: 0 0 0 4px rgba(255, 111, 145, 0.1);
    }

    .filter-btn {
        padding: 12px 24px;
        background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
        border: none;
        border-radius: 10px;
        color: white;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
    }

    .filter-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 111, 145, 0.3);
    }

    .clear-btn {
        padding: 12px 24px;
        background: linear-gradient(135deg, #999 0%, #777 100%);
        border: none;
        border-radius: 10px;
        color: white;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
    }

    .clear-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(153, 153, 153, 0.3);
    }

    @media (max-width: 768px) {
        .records-table {
            font-size: 12px;
        }

        .records-table th,
        .records-table td {
            padding: 10px;
        }

        .filter-form {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-select {
            min-width: 100%;
        }
    }
</style>

<div class="page-wrapper">
    <div class="page-header">
        <h1 class="page-title">Health Records Overview</h1>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <form method="GET" action="{{ route('health.records.all') }}" class="filter-form">
            <label class="filter-label">Filter by Child:</label>
            <select name="child_id" class="filter-select" onchange="this.form.submit()">
                <option value="">All Children</option>
                @if(isset($children) && $children->count() > 0)
                    @foreach ($children as $child)
                        @php
                            $childId = is_object($child) ? ($child->ChildID ?? '') : ($child['ChildID'] ?? '');
                            $childName = is_object($child) ? ($child->FullName ?? 'Unknown') : ($child['FullName'] ?? 'Unknown');
                            $selected = (request('child_id') == $childId) ? 'selected' : '';
                        @endphp
                        @if(!empty($childId) && $childId != '0' && $childId != 0)
                            <option value="{{ $childId }}" {{ $selected }}>{{ $childId }} - {{ $childName }}</option>
                        @endif
                    @endforeach
                @endif
            </select>
            @if(request('child_id'))
                <a href="{{ route('health.records.all') }}" class="clear-btn">Clear Filter</a>
            @endif
        </form>
    </div>

    <div class="records-container">
        @php
            $recordTypes = [
                'birth' => ['title' => '👶 Birth Records', 'route' => 'birth.record', 'dateField' => 'created_at'],
                'growth' => ['title' => '📊 Growth Chart Records', 'route' => 'growth.record', 'dateField' => 'DateMeasured'],
                'immunization' => ['title' => '💉 Immunization Records', 'route' => 'immunization.record', 'dateField' => 'Date'],
                'screening' => ['title' => '🔍 Screening Records', 'route' => 'screening.record', 'dateField' => 'DateScreened'],
                'milestone' => ['title' => '🎯 Development Milestones', 'route' => 'milestone.record', 'dateField' => 'created_at'],
                'feeding' => ['title' => '🍼 Feeding Records', 'route' => 'feeding.record', 'dateField' => 'DateLogged'],
            ];
        @endphp

        @foreach($recordTypes as $type => $config)
            <div class="record-section">
                <div class="section-header">
                    <h2 class="section-title">
                        {{ $config['title'] }}
                    </h2>
                    <span class="record-count">{{ $allRecords[$type]->count() }} Records</span>
                </div>

                @if($allRecords[$type]->count() > 0)
                    <div style="overflow-x: auto;">
                        <table class="records-table">
                            <thead>
                                <tr>
                                    <th>Child Name</th>
                                    <th>Date</th>
                                    @if($type === 'growth')
                                        <th>Weight (kg)</th>
                                        <th>Height (cm)</th>
                                        <th>Head Circumference (cm)</th>
                                        <th>AI Analysis</th>
                                    @elseif($type === 'immunization')
                                        <th>Vaccine</th>
                                        <th>Dose</th>
                                    @elseif($type === 'screening')
                                        <th>Type</th>
                                        <th>Result</th>
                                    @elseif($type === 'milestone')
                                        <th>Milestone</th>
                                        <th>Remarks</th>
                                    @elseif($type === 'feeding')
                                        <th>Type</th>
                                        <th>Amount</th>
                                    @elseif($type === 'birth')
                                        <th>Time of Birth</th>
                                        <th>Birth Weight (kg)</th>
                                        <th>Birth Length (cm)</th>
                                        <th>Head Circumference (cm)</th>
                                        <th>Gestational Age</th>
                                        <th>Birth Place</th>
                                        <th>Birth Type</th>
                                        <th>Baby Count</th>
                                        <th>Apgar Score</th>
                                        <th>Blood Group</th>
                                        <th>Vitamin K Given</th>
                                        <th>Complications</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allRecords[$type] as $record)
                                    <tr>
                                        <td><strong>{{ $record->ChildName ?? 'N/A' }}</strong></td>
                                        <td>
                                            @php
                                                $dateField = $config['dateField'];
                                                $dateValue = $record->$dateField ?? null;
                                                if ($dateValue) {
                                                    try {
                                                        echo \Carbon\Carbon::parse($dateValue)->format('M j, Y');
                                                    } catch (\Exception $e) {
                                                        echo $dateValue;
                                                    }
                                                } else {
                                                    echo 'N/A';
                                                }
                                            @endphp
                                        </td>
                                        @if($type === 'growth')
                                            <td>{{ $record->Weight ?? 'N/A' }}</td>
                                            <td>{{ $record->Height ?? 'N/A' }}</td>
                                            <td>{{ $record->HeadCircumference ?? 'N/A' }}</td>
                                            <td>
                                                @if(isset($record->aiAnalysis) && $record->aiAnalysis)
                                                    <span style="
                                                        display: inline-block;
                                                        padding: 6px 12px;
                                                        border-radius: 20px;
                                                        font-size: 12px;
                                                        font-weight: 600;
                                                        background-color: {{ $record->aiAnalysis['color'] }};
                                                        color: white;
                                                        text-transform: uppercase;
                                                        letter-spacing: 0.5px;
                                                    ">
                                                        {{ $record->aiAnalysis['icon'] }} {{ $record->aiAnalysis['label'] }}
                                                    </span>
                                                @else
                                                    <span style="color: #999;">N/A</span>
                                                @endif
                                            </td>
                                        @elseif($type === 'immunization')
                                            <td>{{ $record->VaccineName ?? 'N/A' }}</td>
                                            <td>{{ $record->DoseNumber ?? 'N/A' }}</td>
                                        @elseif($type === 'screening')
                                            <td>{{ $record->ScreeningType ?? 'N/A' }}</td>
                                            <td>{{ $record->Result ?? 'N/A' }}</td>
                                        @elseif($type === 'milestone')
                                            <td>{{ $record->MilestoneType ?? 'N/A' }}</td>
                                            <td>{{ $record->Notes ?? 'N/A' }}</td>
                                        @elseif($type === 'feeding')
                                            <td>{{ $record->FeedingType ?? 'N/A' }}</td>
                                            <td>{{ $record->FrequencyPerDay ? $record->FrequencyPerDay . ' times/day' : 'N/A' }}</td>
                                        @elseif($type === 'birth')
                                            <td>
                                                @if(isset($record->TimeOfBirth))
                                                    @php
                                                        try {
                                                            $time = \Carbon\Carbon::parse($record->TimeOfBirth)->format('h:i A');
                                                            echo $time;
                                                        } catch (\Exception $e) {
                                                            echo $record->TimeOfBirth ?? 'N/A';
                                                        }
                                                    @endphp
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ $record->BirthWeight ?? 'N/A' }}</td>
                                            <td>{{ $record->BirthLength ?? 'N/A' }}</td>
                                            <td>{{ $record->BirthCircumference ?? 'N/A' }}</td>
                                            <td>{{ ($record->GestationalAgeWeeks ?? null) !== null ? $record->GestationalAgeWeeks . ' weeks' : 'N/A' }}</td>
                                            <td>{{ $record->BirthPlace ?? 'N/A' }}</td>
                                            <td>{{ $record->BirthType ?? 'N/A' }}</td>
                                            <td>{{ $record->BabyCount ?? 'N/A' }}</td>
                                            <td>{{ $record->ApgarScore ?? 'N/A' }}</td>
                                            <td>{{ $record->BloodGroup ?? 'N/A' }}</td>
                                            <td>{{ $record->VitaminKGiven ?? 'N/A' }}</td>
                                            <td>{{ $record->Complications ?? 'N/A' }}</td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        No {{ strtolower($config['title']) }} found.
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endsection
