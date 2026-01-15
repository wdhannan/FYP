@extends('layouts.app')

@section('title', 'Nurse Dashboard - Digital Child Health Record System')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #ffeef4 0%, #fff5f8 50%, #ffeef4 100%);
        min-height: 100vh;
    }

    .nurse-dashboard {
        padding: 40px 20px;
        max-width: 1400px;
        margin: 0 auto;
    }

    .dashboard-header {
        margin-bottom: 40px;
    }

    .dashboard-title {
        font-size: 28px;
        font-weight: 800;
        color: #1a1a1a;
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 1px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .dashboard-subtitle {
        color: #666;
        font-size: 16px;
        font-weight: 500;
    }

    /* Summary Cards */
    .summary-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 24px;
        margin-bottom: 40px;
    }

    .dashboard-main-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 40px;
    }

    .charts-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 40px;
    }

    @media (max-width: 1024px) {
        .dashboard-main-grid {
            grid-template-columns: 1fr;
        }
        
        .charts-container {
            grid-template-columns: 1fr;
        }
    }

    .summary-card {
        background: linear-gradient(135deg, #ffffff 0%, #fff8fa 100%);
        border: 2px solid #ffe0e8;
        border-radius: 16px;
        padding: 28px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(255, 158, 179, 0.1);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        cursor: pointer;
    }

    .chart-card {
        background: linear-gradient(135deg, #ffffff 0%, #fff8fa 100%);
        border: 2px solid #ffe0e8;
        border-radius: 16px;
        padding: 28px;
        box-shadow: 0 4px 15px rgba(255, 158, 179, 0.1);
        position: relative;
        overflow: hidden;
    }

    .chart-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 100%;
        background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
    }

    .chart-title {
        font-size: 14px;
        color: #1a1a1a;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 20px;
        text-align: center;
    }

    .chart-container {
        position: relative;
        height: 200px;
        margin-bottom: 20px;
    }

    .chart-legend {
        display: flex;
        justify-content: center;
        gap: 24px;
        flex-wrap: wrap;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .legend-color {
        width: 16px;
        height: 16px;
        border-radius: 4px;
    }

    .legend-text {
        font-size: 13px;
        color: #1a1a1a;
        font-weight: 600;
    }

    .summary-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 100%;
        background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
    }

    .summary-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(255, 158, 179, 0.2);
        border-color: #ff9eb3;
    }

    .summary-card-icon {
        font-size: 48px;
        margin-bottom: 16px;
        display: block;
    }

    .summary-number {
        font-size: 42px;
        font-weight: 800;
        background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 8px;
    }

    .summary-label {
        font-size: 14px;
        color: #1a1a1a;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Quick Actions */
    .quick-actions {
        background: linear-gradient(135deg, #ffffff 0%, #fff8fa 100%);
        border: 2px solid #ffe0e8;
        border-radius: 16px;
        padding: 32px;
        margin-bottom: 40px;
        box-shadow: 0 4px 15px rgba(255, 158, 179, 0.1);
        position: relative;
        overflow: hidden;
    }

    .quick-actions::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 100%;
        background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
    }

    .quick-actions-title {
        font-size: 24px;
        font-weight: 800;
        color: #1a1a1a;
        margin-bottom: 24px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .quick-actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }

    .quick-action-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 20px;
        background: linear-gradient(135deg, #fff5f5 0%, #ffe0e9 100%);
        border: 2px solid #ffe0e8;
        border-radius: 12px;
        text-decoration: none;
        color: #1a1a1a;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .quick-action-btn:hover {
        background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
        color: white;
        transform: translateX(4px);
        border-color: #ff6f91;
    }

    .quick-action-icon {
        font-size: 24px;
    }

    /* Appointments Section */
    .appointments-section {
        background: linear-gradient(135deg, #ffffff 0%, #fff8fa 100%);
        border: 2px solid #ffe0e8;
        border-radius: 16px;
        padding: 32px;
        box-shadow: 0 4px 15px rgba(255, 158, 179, 0.1);
        position: relative;
        overflow: hidden;
    }

    .appointments-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 100%;
        background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
    }

    .section-title {
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

    .appointments-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .appointments-table thead {
        background: linear-gradient(135deg, #ffe0e9 0%, #ffb6c1 100%);
    }

    .appointments-table th {
        text-align: left;
        padding: 16px;
        font-size: 13px;
        font-weight: 700;
        color: #1a1a1a;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 3px solid #ff6f91;
    }

    .appointments-table td {
        padding: 16px;
        font-size: 14px;
        color: #1a1a1a;
        font-weight: 500;
        border-bottom: 1px solid #ffe0e8;
    }

    .appointments-table tbody tr {
        transition: all 0.3s ease;
    }

    .appointments-table tbody tr:hover {
        background: linear-gradient(90deg, #fff5f5 0%, #ffe0e9 100%);
        transform: translateX(4px);
    }

    .appointments-table tbody tr:nth-child(even) {
        background-color: #fff9fb;
    }

    .status-badge {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        display: inline-block;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-pending {
        background: linear-gradient(135deg, #FFEB3B 0%, #FFF59D 100%);
        color: #000;
    }

    .status-confirmed {
        background: linear-gradient(135deg, #81C784 0%, #A5D6A7 100%);
        color: #000;
    }

    .status-completed {
        background: linear-gradient(135deg, #4CAF50 0%, #66BB6A 100%);
        color: #fff;
    }

    .status-approved {
        background: linear-gradient(135deg, #4CAF50 0%, #66BB6A 100%);
        color: #fff;
    }
    
    .status-rejected {
        background: linear-gradient(135deg, #EF5350 0%, #E57373 100%);
        color: #fff;
    }
    
    .status-cancelled {
        background: linear-gradient(135deg, #EF5350 0%, #E57373 100%);
        color: #fff;
    }

    .btn-view {
        padding: 8px 18px;
        background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-view:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 111, 145, 0.3);
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #999;
        font-size: 16px;
        font-weight: 500;
    }

    .empty-state-icon {
        font-size: 64px;
        margin-bottom: 16px;
        opacity: 0.5;
    }

    @media (max-width: 768px) {
        .nurse-dashboard {
            padding: 20px;
        }

        .summary-cards {
            grid-template-columns: 1fr;
        }

        .quick-actions-grid {
            grid-template-columns: 1fr;
        }

        .appointments-table {
            font-size: 12px;
        }

        .appointments-table th,
        .appointments-table td {
            padding: 10px 8px;
        }
    }
</style>

<div class="nurse-dashboard">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Welcome, Nurse <strong>{{ $nurseName ?? 'Nurse' }}</strong>! 👩‍⚕️</h1>
    </div>

    <!-- Charts Section: Side by Side -->
    <div class="charts-container">
        <!-- Gender Distribution Chart -->
        <div class="chart-card">
            <div class="chart-title">👶 Children by Gender</div>
            <div class="chart-container">
                <canvas id="genderChart"></canvas>
            </div>
            <div class="chart-legend">
                <div class="legend-item">
                    <div class="legend-color" style="background: #4A90E2;"></div>
                    <span class="legend-text">Male: {{ $malePercentage ?? 0 }}% ({{ $maleCount ?? 0 }})</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #FF6F91;"></div>
                    <span class="legend-text">Female: {{ $femalePercentage ?? 0 }}% ({{ $femaleCount ?? 0 }})</span>
                </div>
            </div>
        </div>

        <!-- Ethnic Distribution Chart -->
        <div class="chart-card">
            <div class="chart-title">🌍 Children by Ethnicity</div>
            <div class="chart-container">
                <canvas id="ethnicChart"></canvas>
            </div>
            <div class="chart-legend" id="ethnicLegend">
                @if(isset($ethnicChartData) && count($ethnicChartData) > 0)
                    @foreach($ethnicChartData as $ethnic)
                        <div class="legend-item">
                            <div class="legend-color" style="background: {{ $ethnic['color'] }};"></div>
                            <span class="legend-text">{{ $ethnic['label'] }}: {{ $ethnic['percentage'] }}% ({{ $ethnic['count'] }})</span>
                        </div>
                    @endforeach
                @else
                    <div class="legend-item">
                        <span class="legend-text">No ethnic data available</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions Section: Below Charts -->
    <div class="quick-actions">
        <h2 class="quick-actions-title">⚡ Quick Actions</h2>
        <div class="quick-actions-grid">
            <a href="{{ route('child.register.csv') }}" class="quick-action-btn">
                <span class="quick-action-icon">➕</span>
                <span>Register New Child</span>
            </a>
            <a href="{{ route('list.child') }}" class="quick-action-btn">
                <span class="quick-action-icon">👶</span>
                <span>Child List</span>
            </a>
            <a href="{{ route('birth.record') }}" class="quick-action-btn">
                <span class="quick-action-icon">📋</span>
                <span>Birth Record</span>
            </a>
            <a href="{{ route('immunization.record') }}" class="quick-action-btn">
                <span class="quick-action-icon">💉</span>
                <span>Immunization</span>
            </a>
            <a href="{{ route('growth.record') }}" class="quick-action-btn">
                <span class="quick-action-icon">📊</span>
                <span>Growth Chart</span>
            </a>
            <a href="{{ route('screening.record') }}" class="quick-action-btn">
                <span class="quick-action-icon">🔍</span>
                <span>Screening</span>
            </a>
            <a href="{{ route('milestone.record') }}" class="quick-action-btn">
                <span class="quick-action-icon">🎯</span>
                <span>Milestones</span>
            </a>
            <a href="{{ route('feeding.record') }}" class="quick-action-btn">
                <span class="quick-action-icon">🍼</span>
                <span>Feeding</span>
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    function viewAppointment(appointmentId) {
        // Navigate to appointment view
        window.location.href = '/appointment/view/' + appointmentId;
    }

    // Ethnic Distribution Pie Chart
    document.addEventListener('DOMContentLoaded', function() {
        const ethnicCtx = document.getElementById('ethnicChart');
        if (ethnicCtx) {
            @if(isset($ethnicChartData) && count($ethnicChartData) > 0)
                const ethnicLabels = @json(array_column($ethnicChartData, 'label'));
                const ethnicCounts = @json(array_column($ethnicChartData, 'count'));
                const ethnicColors = @json(array_column($ethnicChartData, 'color'));
                
                const ethnicChart = new Chart(ethnicCtx, {
                    type: 'pie',
                    data: {
                        labels: ethnicLabels,
                        datasets: [{
                            data: ethnicCounts,
                            backgroundColor: ethnicColors,
                            borderColor: ethnicColors.map(color => {
                                // Darken color for border
                                return color;
                            }),
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                        label += context.parsed + ' (' + percentage + '%)';
                                        return label;
                                    }
                                }
                            }
                        }
                    }
                });
            @endif
        }

        // Gender Distribution Pie Chart
        const ctx = document.getElementById('genderChart');
        if (ctx) {
            const genderChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Male', 'Female'],
                    datasets: [{
                        data: [{{ $maleCount ?? 0 }}, {{ $femaleCount ?? 0 }}],
                        backgroundColor: [
                            '#4A90E2',
                            '#FF6F91'
                        ],
                        borderColor: [
                            '#357ABD',
                            '#E55A7A'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                    label += context.parsed + ' (' + percentage + '%)';
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection
