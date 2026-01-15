@extends('layouts.app')

@section('title', 'Doctor Dashboard - Digital Child Health Record System')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #ffeef4 0%, #fff5f8 50%, #ffeef4 100%);
        min-height: 100vh;
    }

    .doctor-dashboard {
        padding: 20px;
        max-width: 1400px;
        margin: 0 auto;
    }

    .dashboard-header {
        margin-bottom: 20px;
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

    /* Summary Cards */
    .summary-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 24px;
        margin-bottom: 40px;
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
        text-decoration: none;
        color: inherit;
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
        text-decoration: none;
        display: inline-block;
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

    /* Quick Actions Styles */
    .quick-actions-grid {
        display: flex;
        flex-direction: column;
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
        box-shadow: 0 4px 12px rgba(255, 111, 145, 0.3);
    }

    .quick-action-icon {
        font-size: 24px;
    }

    /* Calendar Styles */
    .calendar-container {
        width: 100%;
    }

    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
        padding: 6px 10px;
        background: linear-gradient(135deg, #ffe0e9 0%, #ffb6c1 100%);
        border-radius: 6px;
    }

    .calendar-nav-btn {
        background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
        color: white;
        border: none;
        padding: 4px 10px;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 600;
        font-size: 11px;
        transition: all 0.3s ease;
    }

    .calendar-nav-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 111, 145, 0.3);
    }

    .calendar-month-year {
        font-size: 12px;
        font-weight: 700;
        color: #1a1a1a;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 2px;
    }

    .calendar-day-header {
        text-align: center;
        padding: 4px 2px;
        font-size: 9px;
        font-weight: 700;
        color: #1a1a1a;
        text-transform: uppercase;
        background: linear-gradient(135deg, #ffe0e9 0%, #ffb6c1 100%);
        border-radius: 3px;
    }

    .calendar-day {
        aspect-ratio: 1;
        padding: 3px;
        text-align: center;
        border-radius: 3px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        background: #fff;
        border: 1px solid #ffe0e8;
        min-height: 30px;
    }

    .calendar-day:hover {
        background: linear-gradient(135deg, #fff5f5 0%, #ffe0e9 100%);
        transform: scale(1.05);
    }

    .calendar-day.other-month {
        color: #ccc;
        background: #f9f9f9;
    }

    .calendar-day.today {
        background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
        color: white;
        font-weight: 700;
        border-color: #ff6f91;
    }

    .calendar-day.has-appointment {
        background: linear-gradient(135deg, #ffe0e9 0%, #ffb6c1 100%);
        border-color: #ff6f91;
        font-weight: 600;
    }

    .calendar-day.has-appointment::after {
        content: '•';
        position: absolute;
        bottom: 4px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 20px;
        color: #ff6f91;
    }

    .calendar-day-number {
        font-size: 10px;
        font-weight: 600;
    }

    .appointment-tooltip {
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: #1a1a1a;
        color: white;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 12px;
        white-space: nowrap;
        z-index: 1000;
        margin-bottom: 8px;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }

    .calendar-day:hover .appointment-tooltip {
        opacity: 1;
    }

    @media (max-width: 1024px) {
        .doctor-dashboard > div:nth-child(2) {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .doctor-dashboard {
            padding: 20px;
        }

        .summary-cards {
            grid-template-columns: 1fr;
        }

        .doctor-dashboard > div:nth-child(2) {
            grid-template-columns: 1fr;
        }

        .appointments-table {
            font-size: 12px;
        }

        .appointments-table th,
        .appointments-table td {
            padding: 10px 8px;
        }

        .calendar-day {
            padding: 4px;
        }

        .calendar-day-number {
            font-size: 12px;
        }
    }
</style>

<div class="doctor-dashboard">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Welcome, Dr. <strong>{{ $doctorName ?? 'Doctor' }}</strong>! 👨‍⚕️</h1>
    </div>

    <!-- Top Section: Left Column (Total Patients + Quick Actions) and Right Column (Calendar) -->
    <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 16px; margin-bottom: 16px; align-items: start;">
        <!-- Left Column: Total Patients and Quick Actions -->
        <div style="display: flex; flex-direction: column; gap: 16px;">
            <!-- Total Patients Card (Small) -->
            <div class="summary-card" style="padding: 16px;">
                <span class="summary-card-icon" style="font-size: 28px;">👥</span>
                <div class="summary-number" style="font-size: 24px;">{{ $totalPatients ?? 0 }}</div>
                <div class="summary-label" style="font-size: 12px;">Total Patients</div>
            </div>

            <!-- Quick Actions Section (Below Total Patients) -->
            <div class="appointments-section" style="padding: 12px;">
                <h2 class="section-title" style="font-size: 14px; margin-bottom: 10px;">⚡ Quick Actions</h2>
                <div class="quick-actions-grid">
                    <a href="{{ route('report.create') }}" class="quick-action-btn" style="padding: 10px 14px; font-size: 12px;">
                        <span class="quick-action-icon" style="font-size: 18px;">📝</span>
                        <span>Create New Report</span>
                    </a>
                    <a href="{{ route('schedule.add') }}" class="quick-action-btn" style="padding: 10px 14px; font-size: 12px;">
                        <span class="quick-action-icon" style="font-size: 18px;">📅</span>
                        <span>Add Schedule</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Right Column: Calendar Section (Smaller Size) -->
        <div class="appointments-section" style="padding: 10px; max-width: 500px;">
            <h2 class="section-title" style="font-size: 14px; margin-bottom: 8px;">📅 Appointments Calendar</h2>
            <div id="appointmentsCalendar" style="background: white; border-radius: 6px; padding: 6px; max-width: 100%;"></div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const appointments = @json($upcomingAppointments ?? []);
    
    // Group appointments by date
    const appointmentsByDate = {};
    appointments.forEach(apt => {
        const date = apt.date;
        if (!appointmentsByDate[date]) {
            appointmentsByDate[date] = [];
        }
        appointmentsByDate[date].push(apt);
    });

    let currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();

    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'];
    
    const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

    function renderCalendar() {
        const calendarEl = document.getElementById('appointmentsCalendar');
        if (!calendarEl) return;

        // Get first day of month and number of days
        const firstDay = new Date(currentYear, currentMonth, 1).getDay();
        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        const today = new Date();
        const isCurrentMonth = currentMonth === today.getMonth() && currentYear === today.getFullYear();

        let html = `
            <div class="calendar-container">
                <div class="calendar-header">
                    <button class="calendar-nav-btn" onclick="changeMonth(-1)">‹ Prev</button>
                    <div class="calendar-month-year">${monthNames[currentMonth]} ${currentYear}</div>
                    <button class="calendar-nav-btn" onclick="changeMonth(1)">Next ›</button>
                </div>
                <div class="calendar-grid">
        `;

        // Day headers
        dayNames.forEach(day => {
            html += `<div class="calendar-day-header">${day}</div>`;
        });

        // Empty cells for days before month starts
        for (let i = 0; i < firstDay; i++) {
            html += `<div class="calendar-day other-month"></div>`;
        }

        // Days of the month
        for (let day = 1; day <= daysInMonth; day++) {
            const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const isToday = isCurrentMonth && day === today.getDate();
            const hasAppointment = appointmentsByDate[dateStr] && appointmentsByDate[dateStr].length > 0;
            
            let dayClass = 'calendar-day';
            if (isToday) dayClass += ' today';
            if (hasAppointment) dayClass += ' has-appointment';

            let tooltipHtml = '';
            if (hasAppointment) {
                const apts = appointmentsByDate[dateStr];
                tooltipHtml = `<div class="appointment-tooltip">${apts.length} appointment(s)<br>`;
                apts.forEach(apt => {
                    const time = new Date('2000-01-01 ' + apt.time).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                    tooltipHtml += `${apt.ChildName} - ${time}<br>`;
                });
                tooltipHtml += '</div>';
            }

            html += `
                <div class="${dayClass}">
                    <div class="calendar-day-number">${day}</div>
                    ${tooltipHtml}
                </div>
            `;
        }

        // Fill remaining cells
        const totalCells = firstDay + daysInMonth;
        const remainingCells = 42 - totalCells; // 6 rows * 7 days
        for (let i = 0; i < remainingCells && totalCells + i < 42; i++) {
            html += `<div class="calendar-day other-month"></div>`;
        }

        html += `
                </div>
            </div>
        `;

        calendarEl.innerHTML = html;
    }

    window.changeMonth = function(direction) {
        currentMonth += direction;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        } else if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        renderCalendar();
    };

    // Initial render
    renderCalendar();
});
</script>
@endsection
