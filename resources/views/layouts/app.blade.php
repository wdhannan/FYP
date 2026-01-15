<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Digital Child Health Record System')</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background-color: white;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        
        .container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Header */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 100px;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px 0 90px;
            z-index: 1001;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .logo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            border: none;
        }
        
        .header-title {
            font-size: 28px;
            font-weight: bold;
            color: black;
        }
        
        .header-right {
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .user-icon {
            width: 32px;
            height: 32px;
            color: #1a1a1a;
            transition: transform 0.3s ease;
        }

        .header-right:hover .user-icon {
            transform: scale(1.1);
            color: #1a1a1a;
        }
        
        .chevron-down {
            width: 20px;
            height: 20px;
            transition: transform 0.3s ease;
            color: #1a1a1a;
        }

        .header-right:hover .chevron-down {
            color: #1a1a1a;
        }
        
        .chevron-down.rotated {
            transform: rotate(180deg);
        }

        .user-menu {
            padding: 8px 12px;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .user-menu:hover {
            background: linear-gradient(135deg, rgba(255, 158, 179, 0.1) 0%, rgba(255, 111, 145, 0.05) 100%);
        }
        
        .user-dropdown {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            background: linear-gradient(135deg, #ffffff 0%, #fff8fa 100%);
            border: 2px solid #ffe0e8;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(255, 111, 145, 0.2);
            min-width: 180px;
            display: none;
            z-index: 1003;
            overflow: hidden;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .user-dropdown.show {
            display: block;
        }
        
        .dropdown-menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            color: #1a1a1a;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 14px;
            position: relative;
        }

        .dropdown-menu-item span {
            color: #1a1a1a;
        }

        .dropdown-menu-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .dropdown-menu-item:hover {
            background: linear-gradient(90deg, rgba(255, 158, 179, 0.1) 0%, rgba(255, 111, 145, 0.05) 100%);
            padding-left: 24px;
        }

        .dropdown-menu-item:hover::before {
            opacity: 1;
        }
        
        .dropdown-menu-item.logout {
            border-top: 2px solid #ffe0e8;
            color: #1a1a1a;
            font-weight: 600;
        }

        .dropdown-menu-item.logout::before {
            background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
        }
        
        .dropdown-menu-item.logout:hover {
            background: linear-gradient(90deg, rgba(255, 158, 179, 0.1) 0%, rgba(255, 111, 145, 0.05) 100%);
            color: #1a1a1a;
        }

        .dropdown-menu-item.logout span {
            color: #1a1a1a;
        }

        .dropdown-menu-item-icon {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            color: #1a1a1a;
        }

        .dropdown-menu-item.logout .dropdown-menu-item-icon {
            color: #1a1a1a !important;
        }

        .dropdown-menu-item.logout svg {
            color: #1a1a1a !important;
        }

        .dropdown-menu-item.logout svg path {
            fill: #1a1a1a !important;
        }
        
        /* Sidebar Toggle Button */
        .sidebar-toggle {
            position: fixed;
            top: 25px;
            left: 25px;
            z-index: 1002;
            background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
            border: none;
            border-radius: 8px;
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(255, 111, 145, 0.3);
            transition: all 0.3s ease;
        }

        .sidebar-toggle:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 16px rgba(255, 111, 145, 0.4);
        }

        .sidebar-toggle-icon {
            width: 24px;
            height: 24px;
            color: white;
            transition: transform 0.3s ease;
        }

        .sidebar-toggle-icon.rotated {
            transform: rotate(180deg);
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #ffeef4 0%, #fff5f8 50%, #ffeef4 100%);
            padding-top: 100px;
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            transition: transform 0.3s ease;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            border-right: 2px solid #ffe0e8;
        }

        .sidebar.collapsed {
            transform: translateX(-100%);
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: #fff5f8;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: #ff9eb3;
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #ff6f91;
        }
        
        .nav-item {
            padding: 16px 24px;
            color: #1a1a1a;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
            font-weight: 500;
            position: relative;
        }
        
        .nav-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .nav-item:hover {
            background: linear-gradient(90deg, rgba(255, 158, 179, 0.1) 0%, rgba(255, 111, 145, 0.05) 100%);
            padding-left: 28px;
        }

        .nav-item:hover::before {
            opacity: 1;
        }
        
        .nav-icon {
            width: 22px;
            height: 22px;
            flex-shrink: 0;
            color: #ff6f91;
            transition: transform 0.3s ease;
        }

        .nav-item:hover .nav-icon {
            transform: scale(1.1);
            color: #ff5a7d;
        }
        
        .nav-text {
            flex: 1;
            font-size: 15px;
            font-weight: 500;
            white-space: nowrap;
            transition: opacity 0.3s ease;
        }

        .sidebar.collapsed .nav-text {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }
        
        .nav-chevron {
            width: 16px;
            height: 16px;
        }
        
        .dropdown-menu {
            display: none;
            background-color: #f5f5f5;
            padding: 10px 0;
        }
        
        .dropdown-menu.show {
            display: block;
        }
        
        .dropdown-item {
            display: block;
            padding: 12px 20px 12px 60px;
            color: #1a1a1a;
            text-decoration: none;
            background: linear-gradient(90deg, rgba(255, 158, 179, 0.08) 0%, rgba(255, 111, 145, 0.05) 100%);
            margin: 4px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
            border-left: 3px solid transparent;
        }
        
        .dropdown-item:hover {
            background: linear-gradient(90deg, rgba(255, 158, 179, 0.15) 0%, rgba(255, 111, 145, 0.1) 100%);
            border-left-color: #ff9eb3;
            padding-left: 64px;
        }

        .dropdown-item.active {
            background: linear-gradient(90deg, rgba(255, 158, 179, 0.2) 0%, rgba(255, 111, 145, 0.15) 100%);
            border-left-color: #ff6f91;
            font-weight: 600;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 280px;
            margin-top: 100px;
            padding: 40px;
            background-color: white;
            min-height: calc(100vh - 100px);
            width: calc(100% - 280px);
            transition: margin-left 0.3s ease, width 0.3s ease;
        }

        .main-content.sidebar-collapsed {
            margin-left: 0;
            width: 100%;
        }
        
        .main-content.centered {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .main-content:not(.centered) {
            display: block;
        }
        
        .content-wrapper {
            width: 100%;
        }
        
        .content-wrapper.centered {
            text-align: center;
        }
        
        /* Doctor Registration Page Styles */
        .page-title {
            font-size: 36px;
            font-weight: bold;
            color: black;
            text-transform: uppercase;
            margin-bottom: 30px;
        }
        
        .upload-section {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 30px;
            justify-content: flex-end;
            flex-wrap: wrap;
        }
        
        .file-input-wrapper {
            position: relative;
            display: inline-flex;
            align-items: center;
        }
        
        .file-input {
            padding: 12px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 15px;
            min-width: 220px;
            background-color: white;
            color: #333;
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .file-input:focus {
            outline: none;
            border-color: #ff6f91;
            box-shadow: 0 2px 12px rgba(255, 111, 145, 0.2);
        }
        
        .file-icon {
            width: 20px;
            height: 20px;
            margin-left: 10px;
            color: #666;
        }
        
        .hidden-file-input {
            display: none;
        }
        
        .upload-icon-btn {
            background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
            border: none;
            cursor: pointer;
            padding: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(255, 111, 145, 0.2);
            width: 48px;
            height: 48px;
        }
        
        .upload-icon-btn:hover {
            background: linear-gradient(135deg, #ff5a7f 0%, #ff8da8 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 111, 145, 0.3);
        }
        
        .upload-icon-btn:active {
            transform: translateY(0);
        }
        
        .upload-icon {
            width: 24px;
            height: 24px;
            color: white;
        }
        
        .upload-btn {
            padding: 12px 32px;
            background: linear-gradient(135deg, #ff6f91 0%, #ff9eb3 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(255, 111, 145, 0.2);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .upload-btn:hover {
            background: linear-gradient(135deg, #ff5a7f 0%, #ff8da8 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 111, 145, 0.3);
        }
        
        .upload-btn:active {
            transform: translateY(0);
        }
        
        @media (max-width: 768px) {
            .upload-section {
                justify-content: center;
                flex-direction: column;
                align-items: stretch;
            }
            
            .file-input {
                width: 100%;
                min-width: auto;
            }
            
            .upload-icon-btn,
            .upload-btn {
                width: 100%;
            }
        }
        
        .doctors-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .doctors-table thead {
            background-color: #555;
            color: white;
        }
        
        .doctors-table th {
            padding: 15px;
            text-align: left;
            font-weight: bold;
            font-size: 16px;
        }
        
        .doctors-table td {
            padding: 15px;
            font-size: 14px;
        }
        
        .doctors-table tbody tr:nth-child(even) {
            background-color: #f5f5f5;
        }
        
        .doctors-table tbody tr:nth-child(odd) {
            background-color: white;
        }
        
        .dropdown-item.active {
            background-color: #d0d0d0;
            font-weight: bold;
        }
        
        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        /* Nurse Home Page Styles */
        .info-box {
            width: 100%;
            height: 200px;
            background-color: #E0E0E0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 40px;
            border-radius: 4px;
        }
        
        .info-title {
            font-size: 36px;
            font-weight: bold;
            color: black;
            text-transform: uppercase;
        }
        
        .welcome-text {
            font-size: 32px;
            font-weight: bold;
            color: black;
            text-transform: uppercase;
            line-height: 1.8;
            letter-spacing: 1px;
            text-align: center;
            margin-top: 40px;
        }
        
        .nav-item.active {
            background: linear-gradient(90deg, rgba(255, 111, 145, 0.5) 0%, rgba(255, 158, 179, 0.4) 100%);
            border-left-color: #ff6f91;
            font-weight: 700;
        }

        .nav-item.active::before {
            opacity: 1;
            width: 5px;
        }
        
        .nav-item.active:hover {
            background: linear-gradient(90deg, rgba(255, 111, 145, 0.55) 0%, rgba(255, 158, 179, 0.45) 100%);
        }

        .nav-item.active .nav-icon {
            color: #ff5a7d;
            transform: scale(1.15);
        }

        .nav-item.active .nav-text {
            font-weight: 700;
            color: #1a1a1a;
        }
    </style>
</head>
<body>
    <!-- Sidebar Toggle Button -->
    <button class="sidebar-toggle" onclick="toggleSidebar()" id="sidebarToggle" title="Toggle Sidebar">
        <svg class="sidebar-toggle-icon" id="sidebarToggleIcon" fill="currentColor" viewBox="0 0 24 24">
            <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
        </svg>
    </button>

    <!-- Header -->
    <div class="header">
        <div class="header-left">
            @php
                $logoPath = asset('logo.jpg');
            @endphp
            <img src="{{ $logoPath }}" alt="Logo" class="logo" onerror="this.onerror=null; this.style.display='none';">
            <h1 class="header-title">Digital Child Health Record System</h1>
        </div>
        <div class="header-right" onclick="toggleUserDropdown(event)">
            <div class="user-menu">
                <svg class="user-icon" fill="#1a1a1a" viewBox="0 0 24 24" style="color: #1a1a1a;">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" fill="#1a1a1a"/>
                </svg>
                <svg class="chevron-down" id="userChevron" fill="#1a1a1a" viewBox="0 0 24 24" style="color: #1a1a1a;">
                    <path d="M7 10l5 5 5-5z" fill="#1a1a1a"/>
                </svg>
            </div>
            <div class="user-dropdown" id="userDropdown">
                <a href="#" class="dropdown-menu-item logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <svg class="dropdown-menu-item-icon" viewBox="0 0 24 24" style="color: #1a1a1a; fill: #1a1a1a;">
                        <path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.59L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z" fill="#1a1a1a"/>
                    </svg>
                    <span>Logout</span>
                </a>
            </div>
        </div>
        
        <!-- Hidden logout form -->
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
    
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        @php
            $userRole = session('user_role', 'nurse'); // Get user role from session or default to 'nurse'
            // Or use: $userRole = auth()->user()->role ?? 'nurse';
            
            // Fetch parent's children if user is a parent
            $parentChildren = collect([]);
            if ($userRole === 'parent') {
                $parentId = session('user_id', '');
                if ($parentId) {
                    $parentChildren = \Illuminate\Support\Facades\DB::table('child')
                        ->where('ParentID', $parentId)
                        ->orderBy('ChildID', 'asc')
                        ->get();
                }
            }
        @endphp
        
        @if($userRole === 'admin')
            <!-- Admin Navigation -->
            <a href="{{ route('home') }}" class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                <svg class="nav-icon" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                </svg>
                <span class="nav-text">Home</span>
            </a>
            <div class="nav-item" onclick="toggleRegisterUserDropdown()">
                <svg class="nav-icon" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                    <path d="M10 14l-2-2-1.41 1.41L10 16.83l5.41-5.42L14 10l-4 4z"/>
                </svg>
                <span class="nav-text">Register User</span>
                <svg class="nav-chevron" id="registerUserChevron" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M7 10l5 5 5-5z"/>
                </svg>
            </div>
            <div class="dropdown-menu show" id="registerUserDropdown">
                <a href="{{ route('register.doctor') }}" class="dropdown-item {{ request()->routeIs('register.doctor') ? 'active' : '' }}">Doctor</a>
                <a href="{{ route('register.nurse') }}" class="dropdown-item {{ request()->routeIs('register.nurse') ? 'active' : '' }}">Nurse</a>
            </div>
        @elseif($userRole === 'doctor')
            <!-- Doctor Navigation -->
            <a href="{{ route('home') }}" class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                <svg class="nav-icon" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                </svg>
                <span class="nav-text">Home</span>
            </a>
            <a href="{{ route('appointment.request') }}" class="nav-item {{ request()->routeIs('appointment.request') ? 'active' : '' }}">
                <svg class="nav-icon" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/>
                    <path d="M12 13h2v2h-2zm-2-2h2v2h-2zm4 0h2v2h-2zm-4 4h2v2h-2zm4 0h2v2h-2z"/>
                </svg>
                <span class="nav-text">Appointment Request</span>
            </a>
            <a href="{{ route('schedule.add') }}" class="nav-item {{ request()->routeIs('schedule.add') ? 'active' : '' }}">
                <svg class="nav-icon" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/>
                </svg>
                <span class="nav-text">Schedule</span>
            </a>
            <div class="nav-item {{ request()->routeIs('report.*') ? 'active' : '' }}" onclick="toggleReportDropdown()">
                <svg class="nav-icon" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                </svg>
                <span class="nav-text">Report</span>
                <svg class="nav-chevron" id="reportChevron" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M7 10l5 5 5-5z"/>
                </svg>
            </div>
            <div class="dropdown-menu {{ request()->routeIs('report.*') ? 'show' : '' }}" id="reportDropdown">
                <a href="{{ route('report.list') }}" class="dropdown-item {{ request()->routeIs('report.list') ? 'active' : '' }}">List Reports</a>
                <a href="{{ route('report.create') }}" class="dropdown-item {{ request()->routeIs('report.create') ? 'active' : '' }}">Create New Report</a>
            </div>
            <div class="nav-item" onclick="toggleHealthRecordDropdown()">
                <svg class="nav-icon" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                </svg>
                <span class="nav-text">Health Record</span>
                <svg class="nav-chevron" id="healthRecordChevron" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M7 10l5 5 5-5z"/>
                </svg>
            </div>
            <div class="dropdown-menu show" id="healthRecordDropdown">
                <a href="{{ route('birth.record') }}" class="dropdown-item {{ request()->routeIs('birth.record') ? 'active' : '' }}">Birth Record</a>
                <a href="{{ route('immunization.record') }}" class="dropdown-item {{ request()->routeIs('immunization.record') ? 'active' : '' }}">Immunization</a>
                <a href="{{ route('growth.record') }}" class="dropdown-item {{ request()->routeIs('growth.record') ? 'active' : '' }}">Growth Chart</a>
                <a href="{{ route('milestone.record') }}" class="dropdown-item {{ request()->routeIs('milestone.record') ? 'active' : '' }}">Milestone</a>
                <a href="{{ route('screening.record') }}" class="dropdown-item {{ request()->routeIs('screening.record') ? 'active' : '' }}">Screening</a>
                <a href="{{ route('feeding.record') }}" class="dropdown-item {{ request()->routeIs('feeding.record') ? 'active' : '' }}">Feeding</a>
            </div>
        @elseif($userRole === 'parent')
            <!-- Parent Navigation -->
            <a href="{{ route('home') }}" class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                <svg class="nav-icon" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                </svg>
                <span class="nav-text">Home</span>
            </a>
            @if($parentChildren->count() > 1)
                <!-- Appointment History Dropdown (Multiple Children) -->
                <div class="nav-item {{ request()->routeIs('parent.appointment.history') ? 'active' : '' }}" onclick="toggleAppointmentHistoryDropdown()">
                    <svg class="nav-icon" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/>
                    </svg>
                    <span class="nav-text">Appointment History</span>
                    <svg class="nav-chevron" id="appointmentHistoryChevron" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M7 10l5 5 5-5z"/>
                    </svg>
                </div>
                <div class="dropdown-menu {{ request()->routeIs('parent.appointment.history') ? 'show' : '' }}" id="appointmentHistoryDropdown">
                    @foreach($parentChildren as $child)
                        <a href="{{ route('parent.appointment.history') }}?child_id={{ $child->ChildID }}" 
                           class="dropdown-item {{ (request()->input('child_id') == $child->ChildID || (request()->routeIs('parent.appointment.history') && !request()->input('child_id') && $loop->first)) ? 'active' : '' }}">
                            {{ $child->ChildID }} - {{ $child->FullName }}
                        </a>
                    @endforeach
                </div>
            @else
                <!-- Single Child Appointment History Link (No Dropdown) -->
                <a href="{{ route('parent.appointment.history') }}" class="nav-item {{ request()->routeIs('parent.appointment.history') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/>
                    </svg>
                    <span class="nav-text">Appointment History</span>
                </a>
            @endif
            @if($parentChildren->count() > 1)
                <!-- Child Record Dropdown (Multiple Children) -->
                <div class="nav-item {{ request()->routeIs('parent.child.record') ? 'active' : '' }}" onclick="toggleChildRecordDropdown()">
                    <svg class="nav-icon" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                    </svg>
                    <span class="nav-text">Child Record</span>
                    <svg class="nav-chevron" id="childRecordChevron" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M7 10l5 5 5-5z"/>
                    </svg>
                </div>
                <div class="dropdown-menu {{ request()->routeIs('parent.child.record') ? 'show' : '' }}" id="childRecordDropdown">
                    @foreach($parentChildren as $child)
                        <a href="{{ route('parent.child.record') }}?child_id={{ $child->ChildID }}" 
                           class="dropdown-item {{ (request()->input('child_id') == $child->ChildID || (request()->routeIs('parent.child.record') && !request()->input('child_id') && $loop->first)) ? 'active' : '' }}">
                            {{ $child->ChildID }} - {{ $child->FullName }}
                        </a>
                    @endforeach
                </div>
            @else
                <!-- Single Child Record Link (No Dropdown) -->
                <a href="{{ route('parent.child.record') }}" class="nav-item {{ request()->routeIs('parent.child.record') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                    </svg>
                    <span class="nav-text">Child Record</span>
                </a>
            @endif
        @else
            <!-- Nurse Navigation -->
            <a href="{{ route('home') }}" class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                <svg class="nav-icon" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                </svg>
                <span class="nav-text">Home</span>
            </a>
            <a href="{{ route('child.register.csv') }}" class="nav-item {{ request()->routeIs('child.register.csv') || request()->routeIs('child.upload.csv') ? 'active' : '' }}">
                <svg class="nav-icon" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zm4 18v-6h2.5l-2.54-7.63A1.5 1.5 0 0 0 18.54 8H16.5c-.8 0-1.54.5-1.85 1.26L12.5 16H11v6h9zm-11.5 0v-5h-1v-6h3.5c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5H10v3.5h2.5zM5.5 6c1.11 0 2-.89 2-2s-.89-2-2-2-2 .89-2 2 .89 2 2 2zm-1 4h3v9H4.5v-9z"/>
                </svg>
                <span class="nav-text">Register Child</span>
            </a>
            <div class="nav-item {{ request()->routeIs('list.child') || request()->routeIs('booking.form') || request()->routeIs('appointment.status') ? 'active' : '' }}" onclick="toggleAppointmentDropdown()">
                <svg class="nav-icon" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/>
                </svg>
                <span class="nav-text">Appointment</span>
                <svg class="nav-chevron" id="appointmentChevron" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M7 10l5 5 5-5z"/>
                </svg>
            </div>
            <div class="dropdown-menu {{ request()->routeIs('list.child') || request()->routeIs('booking.form') || request()->routeIs('appointment.status') ? 'show' : '' }}" id="appointmentDropdown">
                <a href="{{ route('list.child') }}" class="dropdown-item {{ request()->routeIs('list.child') || request()->routeIs('booking.form') ? 'active' : '' }}">Book Appointment</a>
                <a href="{{ route('appointment.status') }}" class="dropdown-item {{ request()->routeIs('appointment.status') ? 'active' : '' }}">Appointment Status</a>
            </div>
            <a href="{{ route('report.list') }}" class="nav-item {{ request()->routeIs('report.list') ? 'active' : '' }}">
                <svg class="nav-icon" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                </svg>
                <span class="nav-text">Child Report</span>
            </a>
            <div class="nav-item {{ request()->routeIs('birth.record') || request()->routeIs('immunization.record') || request()->routeIs('growth.record') || request()->routeIs('milestone.record') || request()->routeIs('screening.record') || request()->routeIs('feeding.record') ? 'active' : '' }}" onclick="toggleHealthRecordDropdown()">
                <svg class="nav-icon" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19.5 3.5L18 2l-1.5 1.5L15 2l-1.5 1.5L12 2l-1.5 1.5L9 2 7.5 3.5 6 2v14H3v3c0 1.66 1.34 3 3 3h12c1.66 0 3-1.34 3-3V2l-1.5 1.5zM19 19c0 .55-.45 1-1 1s-1-.45-1-1v-3H8V5h11v14z"/>
                    <circle cx="12" cy="8" r="1"/>
                    <circle cx="12" cy="11" r="1"/>
                    <circle cx="12" cy="14" r="1"/>
                </svg>
                <span class="nav-text">Health Record</span>
                <svg class="nav-chevron" id="healthRecordChevron" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M7 10l5 5 5-5z"/>
                </svg>
            </div>
            <div class="dropdown-menu {{ request()->routeIs('birth.record') || request()->routeIs('immunization.record') || request()->routeIs('growth.record') || request()->routeIs('milestone.record') || request()->routeIs('screening.record') || request()->routeIs('feeding.record') || request()->routeIs('health.records.all') ? 'show' : '' }}" id="healthRecordDropdown">
                @if($userRole === 'nurse')
                    <a href="{{ route('health.records.all') }}" class="dropdown-item {{ request()->routeIs('health.records.all') ? 'active' : '' }}">Health Records Overview</a>
                @endif
                <a href="{{ route('birth.record') }}" class="dropdown-item {{ request()->routeIs('birth.record') ? 'active' : '' }}">Birth Record</a>
                <a href="{{ route('immunization.record') }}" class="dropdown-item {{ request()->routeIs('immunization.record') ? 'active' : '' }}">Immunization</a>
                <a href="{{ route('growth.record') }}" class="dropdown-item {{ request()->routeIs('growth.record') ? 'active' : '' }}">Growth Chart</a>
                <a href="{{ route('milestone.record') }}" class="dropdown-item {{ request()->routeIs('milestone.record') ? 'active' : '' }}">Milestone</a>
                <a href="{{ route('screening.record') }}" class="dropdown-item {{ request()->routeIs('screening.record') ? 'active' : '' }}">Screening</a>
                <a href="{{ route('feeding.record') }}" class="dropdown-item {{ request()->routeIs('feeding.record') ? 'active' : '' }}">Feeding</a>
            </div>
        @endif
    </div>
    
    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        @yield('content')
    </div>
    
    <!-- Success/Error Message Modal -->
    @if(session('success') || session('error'))
        <div id="messageModal" class="message-modal-overlay" style="display: flex;">
            <div class="message-modal {{ session('error') ? 'error-modal' : 'success-modal' }}">
                <div class="message-icon {{ session('error') ? 'error-icon' : 'success-icon' }}">
                    @if(session('error'))
                        <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    @else
                        <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    @endif
                </div>
                <h2 class="message-title {{ session('error') ? 'error-title' : 'success-title' }}">
                    {{ session('error') ? 'ERROR!' : 'SUCCESS' }}
                </h2>
                <p class="message-text">{{ session('success') ?? session('error') }}</p>
                <button class="message-button {{ session('error') ? 'error-button' : 'success-button' }}" onclick="closeMessageModal()">
                    {{ session('error') ? 'Try Again' : 'Continue' }}
                </button>
            </div>
        </div>
    @endif
    
    <style>
        .message-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 10000;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        @keyframes fadeOut {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
            }
        }
        
        .message-modal {
            background: white;
            border-radius: 16px;
            padding: 50px 40px;
            max-width: 500px;
            width: 90%;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            animation: slideUp 0.3s ease;
        }
        
        @keyframes slideUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .message-icon {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .success-icon {
            background-color: #4CAF50;
        }
        
        .error-icon {
            background-color: #f44336;
        }
        
        .message-title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .success-title {
            color: #4CAF50;
        }
        
        .error-title {
            color: #f44336;
        }
        
        .message-text {
            font-size: 16px;
            color: #333;
            line-height: 1.6;
            margin-bottom: 30px;
            padding: 0 10px;
        }
        
        .message-button {
            padding: 14px 40px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            min-width: 150px;
        }
        
        .success-button {
            background-color: #4CAF50;
            color: white;
        }
        
        .success-button:hover {
            background-color: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
        }
        
        .error-button {
            background-color: #f44336;
            color: white;
        }
        
        .error-button:hover {
            background-color: #da190b;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(244, 67, 54, 0.3);
        }
        
        @media (max-width: 600px) {
            .message-modal {
                padding: 40px 30px;
                max-width: 90%;
            }
            
            .message-icon {
                width: 80px;
                height: 80px;
            }
            
            .message-title {
                font-size: 24px;
            }
            
            .message-text {
                font-size: 14px;
            }
        }
    </style>
    
    <script>
        function closeMessageModal() {
            const modal = document.getElementById('messageModal');
            if (modal) {
                modal.style.animation = 'fadeOut 0.3s ease';
                setTimeout(() => {
                    modal.style.display = 'none';
                }, 300);
            }
        }
        
        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('messageModal');
            if (modal && event.target === modal) {
                closeMessageModal();
            }
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeMessageModal();
            }
        });
        
        // Auto-close success messages after 5 seconds
        @if(session('success'))
            setTimeout(function() {
                closeMessageModal();
            }, 5000);
        @endif
    </script>
    
    <script>
        function toggleAppointmentDropdown() {
            const dropdown = document.getElementById('appointmentDropdown');
            const chevron = document.getElementById('appointmentChevron');
            if (dropdown && chevron) {
                dropdown.classList.toggle('show');
                
                if (dropdown.classList.contains('show')) {
                    chevron.style.transform = 'rotate(180deg)';
                } else {
                    chevron.style.transform = 'rotate(0deg)';
                }
            }
        }
        
        function toggleHealthRecordDropdown() {
            const dropdown = document.getElementById('healthRecordDropdown');
            const chevron = document.getElementById('healthRecordChevron');
            if (dropdown && chevron) {
                dropdown.classList.toggle('show');
                
                if (dropdown.classList.contains('show')) {
                    chevron.style.transform = 'rotate(180deg)';
                } else {
                    chevron.style.transform = 'rotate(0deg)';
                }
            }
        }
        
        function toggleReportDropdown() {
            const dropdown = document.getElementById('reportDropdown');
            const chevron = document.getElementById('reportChevron');
            if (dropdown && chevron) {
                dropdown.classList.toggle('show');
                
                if (dropdown.classList.contains('show')) {
                    chevron.style.transform = 'rotate(180deg)';
                } else {
                    chevron.style.transform = 'rotate(0deg)';
                }
            }
        }
        
        function toggleChildRecordDropdown() {
            const dropdown = document.getElementById('childRecordDropdown');
            const chevron = document.getElementById('childRecordChevron');
            if (dropdown && chevron) {
                dropdown.classList.toggle('show');
                
                if (dropdown.classList.contains('show')) {
                    chevron.style.transform = 'rotate(180deg)';
                } else {
                    chevron.style.transform = 'rotate(0deg)';
                }
            }
        }
        
        function toggleAppointmentHistoryDropdown() {
            const dropdown = document.getElementById('appointmentHistoryDropdown');
            const chevron = document.getElementById('appointmentHistoryChevron');
            if (dropdown && chevron) {
                dropdown.classList.toggle('show');
                
                if (dropdown.classList.contains('show')) {
                    chevron.style.transform = 'rotate(180deg)';
                } else {
                    chevron.style.transform = 'rotate(0deg)';
                }
            }
        }
        
        function toggleRegisterUserDropdown() {
            const dropdown = document.getElementById('registerUserDropdown');
            const chevron = document.getElementById('registerUserChevron');
            if (dropdown && chevron) {
                dropdown.classList.toggle('show');
                
                if (dropdown.classList.contains('show')) {
                    chevron.style.transform = 'rotate(180deg)';
                } else {
                    chevron.style.transform = 'rotate(0deg)';
                }
            }
        }
        
        // Add transition for chevron rotation
        const appointmentChevron = document.getElementById('appointmentChevron');
        const healthRecordChevron = document.getElementById('healthRecordChevron');
        const registerUserChevron = document.getElementById('registerUserChevron');
        const reportChevron = document.getElementById('reportChevron');
        const childRecordChevron = document.getElementById('childRecordChevron');
        const appointmentHistoryChevron = document.getElementById('appointmentHistoryChevron');
        if (appointmentChevron) appointmentChevron.style.transition = 'transform 0.3s';
        if (healthRecordChevron) healthRecordChevron.style.transition = 'transform 0.3s';
        if (registerUserChevron) registerUserChevron.style.transition = 'transform 0.3s';
        if (reportChevron) reportChevron.style.transition = 'transform 0.3s';
        if (childRecordChevron) childRecordChevron.style.transition = 'transform 0.3s';
        if (appointmentHistoryChevron) appointmentHistoryChevron.style.transition = 'transform 0.3s';
        
        // Center main content if it contains welcome-text
        document.addEventListener('DOMContentLoaded', function() {
            const mainContent = document.getElementById('mainContent');
            const welcomeText = mainContent.querySelector('.welcome-text');
            if (welcomeText) {
                mainContent.classList.add('centered');
            }
        });
        
        // Toggle user dropdown
        function toggleUserDropdown(event) {
            event.stopPropagation();
            const dropdown = document.getElementById('userDropdown');
            const chevron = document.getElementById('userChevron');
            dropdown.classList.toggle('show');
            
            if (dropdown.classList.contains('show')) {
                chevron.classList.add('rotated');
            } else {
                chevron.classList.remove('rotated');
            }
        }
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const headerRight = document.querySelector('.header-right');
            const dropdown = document.getElementById('userDropdown');
            const chevron = document.getElementById('userChevron');
            
            if (headerRight && !headerRight.contains(event.target)) {
                dropdown.classList.remove('show');
                chevron.classList.remove('rotated');
            }
        });

        // Sidebar Toggle Functionality
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleIcon = document.getElementById('sidebarToggleIcon');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('sidebar-collapsed');
            toggleIcon.classList.toggle('rotated');
            
            // Save state to localStorage
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        }

        // Restore sidebar state on page load
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleIcon = document.getElementById('sidebarToggleIcon');
            
            if (sidebarCollapsed) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('sidebar-collapsed');
                toggleIcon.classList.add('rotated');
            }
        });
    </script>
</body>
</html>

