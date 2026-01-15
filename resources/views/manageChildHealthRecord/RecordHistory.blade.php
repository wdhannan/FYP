@extends('layouts.app')

@section('title', 'Record History - Digital Child Health Record System')

@section('content')
<div class="content-wrapper">
    <h1 class="history-page-title">CHILD HEALTH RECORD HISTORY</h1>
    
    <div class="record-history-container">
        @php
            $recordHistory = $recordHistory ?? collect([]);
            $children = $children ?? collect([]);
        @endphp

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="filter-controls">
                <div class="filter-group">
                    <label for="childFilter" class="filter-label">Filter by Child:</label>
                    <select id="childFilter" class="filter-select" onchange="filterRecords()">
                        <option value="">All Children</option>
                        @foreach($children as $child)
                            <option value="{{ $child->ChildID ?? $child['ChildID'] }}">{{ $child->FullName ?? $child['FullName'] ?? $child->ChildID ?? $child['ChildID'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label for="recordTypeFilter" class="filter-label">Filter by Record Type:</label>
                    <select id="recordTypeFilter" class="filter-select" onchange="filterRecords()">
                        <option value="">All Record Types</option>
                        <option value="birth">Birth Record</option>
                        <option value="immunization">Immunization</option>
                        <option value="growth">Growth Chart</option>
                        <option value="screening">Screening</option>
                        <option value="milestone">Development Milestone</option>
                        <option value="feeding">Feeding Record</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Records Display -->
        <div class="records-tabs">
            <div class="tab-buttons">
                <button class="tab-btn active" data-tab="all" onclick="switchTab('all')">All Records</button>
                <button class="tab-btn" data-tab="birth" onclick="switchTab('birth')">Birth Records</button>
                <button class="tab-btn" data-tab="immunization" onclick="switchTab('immunization')">Immunizations</button>
                <button class="tab-btn" data-tab="growth" onclick="switchTab('growth')">Growth Charts</button>
                <button class="tab-btn" data-tab="screening" onclick="switchTab('screening')">Screenings</button>
                <button class="tab-btn" data-tab="milestone" onclick="switchTab('milestone')">Milestones</button>
                <button class="tab-btn" data-tab="feeding" onclick="switchTab('feeding')">Feeding Records</button>
            </div>
        </div>

        <!-- Records List -->
        <div class="records-list" id="recordsList">
            @if(count($recordHistory) > 0)
                @foreach($recordHistory as $record)
                    <div class="record-item" data-record-type="{{ $record->type ?? 'unknown' }}" data-child-id="{{ $record->ChildID ?? '' }}">
                        <div class="record-header">
                            <div class="record-type-badge type-{{ $record->type ?? 'unknown' }}">
                                {{ strtoupper($record->type ?? 'UNKNOWN') }}
                            </div>
                            <div class="record-date">
                                {{ isset($record->date) ? \Carbon\Carbon::parse($record->date)->format('M j, Y') : 'N/A' }}
                            </div>
                        </div>
                        <div class="record-body">
                            @if(isset($record->ChildName))
                                <div class="record-field">
                                    <span class="field-label">Child:</span>
                                    <span class="field-value">{{ $record->ChildName }}</span>
                                </div>
                            @endif
                            <div class="record-details">
                                @if($record->type === 'birth')
                                    <div class="detail-item">Birth Weight: {{ $record->BirthWeight ?? 'N/A' }} kg</div>
                                    <div class="detail-item">Birth Length: {{ $record->BirthLength ?? 'N/A' }} cm</div>
                                @elseif($record->type === 'immunization')
                                    <div class="detail-item">Vaccine: {{ $record->VaccineName ?? 'N/A' }}</div>
                                    <div class="detail-item">Dose: {{ $record->DoseNumber ?? 'N/A' }}</div>
                                @elseif($record->type === 'growth')
                                    <div class="detail-item">Weight: {{ $record->Weight ?? 'N/A' }} kg</div>
                                    <div class="detail-item">Height: {{ $record->Height ?? 'N/A' }} cm</div>
                                @elseif($record->type === 'screening')
                                    <div class="detail-item">Type: {{ $record->ScreeningType ?? 'N/A' }}</div>
                                    <div class="detail-item">Result: {{ $record->Result ?? 'N/A' }}</div>
                                @elseif($record->type === 'milestone')
                                    <div class="detail-item">Type: {{ $record->MilestoneType ?? 'N/A' }}</div>
                                    <div class="detail-item">Notes: {{ $record->Notes ?? 'N/A' }}</div>
                                @elseif($record->type === 'feeding')
                                    <div class="detail-item">Type: {{ $record->FeedingType ?? 'N/A' }}</div>
                                    <div class="detail-item">Frequency: {{ $record->FrequencyPerDay ?? 'N/A' }} times/day</div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty-state">
                    <p>No health records available at this time.</p>
                    <p class="empty-subtitle">Health records will appear here once they are added to the system.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .history-page-title {
        font-size: 36px;
        font-weight: bold;
        color: #000;
        text-transform: uppercase;
        margin: 30px 0 40px 0;
        padding: 0 20px;
    }

    .record-history-container {
        padding: 0 20px 40px;
    }

    .filter-section {
        background-color: #f5f5f5;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 30px;
    }

    .filter-controls {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
        align-items: flex-end;
    }

    .filter-group {
        flex: 1;
        min-width: 200px;
    }

    .filter-label {
        display: block;
        font-weight: 600;
        font-size: 14px;
        color: #333;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .filter-select {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        background-color: white;
        cursor: pointer;
    }

    .records-tabs {
        margin-bottom: 30px;
    }

    .tab-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        border-bottom: 2px solid #e0e0e0;
    }

    .tab-btn {
        padding: 12px 24px;
        border: none;
        background-color: transparent;
        color: #666;
        font-weight: 600;
        font-size: 14px;
        text-transform: uppercase;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        transition: all 0.3s;
    }

    .tab-btn:hover {
        color: #000;
        background-color: #f5f5f5;
    }

    .tab-btn.active {
        color: #ff6f91;
        border-bottom-color: #ff6f91;
    }

    .records-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 20px;
    }

    .record-item {
        background-color: white;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 20px;
        transition: box-shadow 0.3s, transform 0.2s;
    }

    .record-item:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }

    .record-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #f0f0f0;
    }

    .record-type-badge {
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .type-birth { background-color: #e3f2fd; color: #1976d2; }
    .type-immunization { background-color: #f3e5f5; color: #7b1fa2; }
    .type-growth { background-color: #e8f5e9; color: #388e3c; }
    .type-screening { background-color: #fff3e0; color: #f57c00; }
    .type-milestone { background-color: #fce4ec; color: #c2185b; }
    .type-feeding { background-color: #e0f2f1; color: #00796b; }

    .record-date {
        font-size: 13px;
        color: #666;
        font-weight: 500;
    }

    .record-body {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .record-field {
        display: flex;
        gap: 10px;
    }

    .field-label {
        font-weight: 600;
        color: #666;
        font-size: 14px;
        min-width: 70px;
    }

    .field-value {
        color: #333;
        font-size: 14px;
    }

    .record-details {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-top: 5px;
    }

    .detail-item {
        font-size: 13px;
        color: #555;
        padding-left: 15px;
        position: relative;
    }

    .detail-item::before {
        content: "•";
        position: absolute;
        left: 0;
        color: #999;
    }

    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 60px 20px;
        color: #999;
        font-size: 16px;
        background-color: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 8px;
    }

    .empty-state p {
        margin: 0;
    }

    .empty-subtitle {
        font-size: 14px;
        margin-top: 10px;
        color: #bbb;
    }

    @media (max-width: 768px) {
        .records-list {
            grid-template-columns: 1fr;
        }

        .filter-controls {
            flex-direction: column;
        }

        .tab-buttons {
            overflow-x: auto;
        }
    }
</style>

<script>
    function switchTab(tabType) {
        // Update active tab button
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelector(`[data-tab="${tabType}"]`).classList.add('active');

        // Filter records
        filterRecords();
    }

    function filterRecords() {
        const childFilter = document.getElementById('childFilter').value;
        const typeFilter = document.getElementById('recordTypeFilter').value;
        const activeTab = document.querySelector('.tab-btn.active').dataset.tab;

        const recordItems = document.querySelectorAll('.record-item');
        
        recordItems.forEach(item => {
            let show = true;
            const recordType = item.dataset.recordType;
            const childId = item.dataset.childId;

            // Filter by tab
            if (activeTab !== 'all' && recordType !== activeTab) {
                show = false;
            }

            // Filter by child
            if (childFilter && childId !== childFilter) {
                show = false;
            }

            // Filter by record type dropdown
            if (typeFilter && recordType !== typeFilter) {
                show = false;
            }

            // Show or hide item
            item.style.display = show ? 'block' : 'none';
        });

        // Show empty state if no records visible
        const visibleRecords = Array.from(recordItems).filter(item => item.style.display !== 'none');
        let emptyState = document.querySelector('.empty-state');
        
        if (visibleRecords.length === 0) {
            if (!emptyState) {
                const recordsList = document.getElementById('recordsList');
                emptyState = document.createElement('div');
                emptyState.className = 'empty-state';
                emptyState.innerHTML = '<p>No records match your filters.</p>';
                recordsList.appendChild(emptyState);
            }
            emptyState.style.display = 'block';
        } else {
            if (emptyState) {
                emptyState.style.display = 'none';
            }
        }
    }
</script>
@endsection

