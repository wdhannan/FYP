@extends('layouts.app')

@section('title', 'Report List')

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
            gap: 12px;
        }

        .report-page {
            background-color: #fff5f5;
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
            min-height: calc(100vh - 160px);
            display: flex;
            flex-direction: column;
            gap: 24px;
        }


        .report-page__subtitle {
            font-size: 16px;
            color: #8c8c8c;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 4px;
        }

        .report-page__actions {
            display: flex;
            gap: 12px;
        }

        .btn-pink {
            border: none;
            background: linear-gradient(135deg, #ff9eb3, #ff6f91);
            color: white;
            padding: 12px 28px;
            border-radius: 30px;
            font-weight: 600;
            letter-spacing: 1px;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            text-decoration: none;
        }

        .btn-pink:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 111, 145, 0.35);
        }

        .report-card {
            background-color: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.06);
        }

        .report-card__title {
            text-align: center;
            font-size: 20px;
            font-weight: 600;
            letter-spacing: 3px;
            color: #333;
            margin-bottom: 20px;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
        }

        .report-table thead tr {
            background-color: #ffe0e9;
            color: #333;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .report-table th,
        .report-table td {
            padding: 16px 18px;
            text-align: left;
            font-size: 15px;
        }

        .report-table tbody tr {
            border-bottom: 1px solid #f1f1f1;
            transition: background-color 0.2s ease;
        }

        .report-table tbody tr:nth-child(odd) {
            background-color: #fff9fb;
        }

        .report-table tbody tr:hover {
            background-color: rgba(255, 158, 179, 0.15);
        }

        .report-link {
            color: #ff6f91;
            text-decoration: none;
            font-weight: 600;
        }

        .report-link:hover {
            text-decoration: underline;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #999;
            font-size: 16px;
        }

        .filter-section {
            margin-bottom: 24px;
            width: 100%;
        }

        .filter-container {
            background-color: white;
            border-radius: 12px;
            padding: 16px 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .search-wrapper {
            flex: 1;
            position: relative;
            min-width: 250px;
            max-width: 500px;
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            color: #999;
            pointer-events: none;
            z-index: 1;
        }

        .search-input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            outline: none;
            transition: all 0.3s;
            background-color: #fafafa;
        }

        .search-input:focus {
            border-color: #ff6f91;
            background-color: white;
            box-shadow: 0 0 0 3px rgba(255, 111, 145, 0.1);
        }

        .pagination-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 24px;
            padding: 16px 0;
            flex-wrap: wrap;
            gap: 15px;
        }

        .pagination-info {
            color: #666;
            font-size: 14px;
        }

        .pagination-buttons {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .pagination-btn {
            padding: 8px 16px;
            border: 1px solid #ddd;
            background: #fff;
            color: #333;
            cursor: pointer;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .pagination-btn:hover:not(:disabled) {
            background: #ffe0e9;
            border-color: #ff6f91;
        }

        .pagination-btn:disabled {
            background: #f5f5f5;
            color: #999;
            cursor: not-allowed;
        }

        .pagination-btn.active {
            background: linear-gradient(135deg, #ff9eb3, #ff6f91);
            color: white;
            border-color: #ff6f91;
            font-weight: 600;
        }

        .pagination-btn.page-number {
            padding: 8px 12px;
            min-width: 40px;
        }

        @media (max-width: 768px) {
            .report-page {
                padding: 20px;
            }

            .report-table th,
            .report-table td {
                padding: 12px;
                font-size: 14px;
            }

            .report-page__title {
                font-size: 26px;
            }
        }
    </style>

    <div class="report-page">
    <div class="page-wrapper">
        <div class="page-header">
            <h1 class="page-title">📊 Report List</h1>
        </div>

        <div class="filter-section">
            <div class="filter-container">
                <div class="search-wrapper">
                    <svg class="search-icon" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                    </svg>
                    <input type="text" class="search-input" placeholder="Search by Child ID or Report ID" id="searchInput">
                </div>
            </div>
        </div>

        <div class="report-card">
            
            @if(isset($reports) && count($reports) > 0)
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Report ID</th>
                            <th>Child ID</th>
                            <th>Report Date</th>
                            @if(session('user_role') === 'doctor')
                                <th>Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="reportTableBody">
                        @foreach($reports as $report)
                            @php
                                $reportId = $report->ReportID ?? '';
                                $childId = $report->ChildID ?? '';
                            @endphp
                            <tr class="report-row" data-childid="{{ strtolower($childId) }}" data-reportid="{{ strtolower($reportId) }}">
                                <td>
                                    @if($reportId)
                                        <a class="report-link" href="{{ route('report.view.id', ['reportId' => $reportId]) }}">
                                            {{ $reportId }}
                                        </a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    {{ $childId }}
                                </td>
                                <td>
                                    @php
                                        $dateValue = $report->ReportDate ?? null;
                                    @endphp
                                    @if($dateValue)
                                        {{ \Carbon\Carbon::parse($dateValue)->format('d/m/Y') }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                @if(session('user_role') === 'doctor')
                                    <td>
                                        <div style="display: flex; gap: 8px; align-items: center;">
                                            <a href="{{ route('report.edit', ['reportId' => $reportId]) }}" 
                                               style="padding: 6px 12px; background: linear-gradient(135deg, #4caf50, #45a049); color: white; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600; transition: all 0.3s;"
                                               onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(76, 175, 80, 0.3)'"
                                               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                                ✏️ Edit
                                            </a>
                                            <form action="{{ route('report.destroy', ['reportId' => $reportId]) }}" 
                                                  method="POST" 
                                                  style="display: inline;"
                                                  id="deleteForm_{{ $reportId }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" 
                                                        onclick="showDeleteConfirmation('{{ $reportId }}')"
                                                        style="padding: 6px 12px; background: linear-gradient(135deg, #d32f2f, #c62828); color: white; border: none; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.3s;"
                                                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(211, 47, 47, 0.3)'"
                                                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                                    🗑️ Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="pagination-controls">
                    <div class="pagination-info" id="paginationInfo"></div>
                    <div class="pagination-buttons" id="paginationButtons"></div>
                </div>
            @else
                <div class="empty-state">
                    No reports available yet. Completed appointments will appear here.
                </div>
            @endif
        </div>
    </div>

<script>
    let currentPage = 1;
    const rowsPerPage = 20;
    let allReportRows = []; // Store all report rows for pagination
    let filteredRows = []; // Store filtered rows

    // Initialize pagination
    document.addEventListener('DOMContentLoaded', function() {
        // Store all report rows
        allReportRows = Array.from(document.querySelectorAll('.report-row'));
        filteredRows = [...allReportRows];
        
        // Initial render
        renderTable();
    });

    function renderTable() {
        const tbody = document.getElementById('reportTableBody');
        if (!tbody) return;

        // Get rows for current page
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const paginatedRows = filteredRows.slice(start, end);

        // Clear and render current page rows
        tbody.innerHTML = '';
        paginatedRows.forEach(row => {
            tbody.appendChild(row.cloneNode(true));
        });

        // Update pagination controls
        setupPagination(filteredRows.length);
    }

    function setupPagination(totalItems) {
        const totalPages = Math.ceil(totalItems / rowsPerPage);
        const paginationInfo = document.getElementById('paginationInfo');
        const paginationButtons = document.getElementById('paginationButtons');

        // Update info text
        const startIndex = totalItems === 0 ? 0 : (currentPage - 1) * rowsPerPage + 1;
        const endIndex = Math.min(currentPage * rowsPerPage, totalItems);
        paginationInfo.textContent = `Showing ${startIndex}-${endIndex} of ${totalItems} entries`;

        // Clear buttons
        paginationButtons.innerHTML = '';

        if (totalPages <= 1) {
            return;
        }

        // Previous button
        const prevBtn = document.createElement('button');
        prevBtn.textContent = 'Previous';
        prevBtn.className = 'pagination-btn';
        prevBtn.disabled = currentPage === 1;
        prevBtn.onclick = () => goToPage(currentPage - 1);
        paginationButtons.appendChild(prevBtn);

        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                const pageBtn = document.createElement('button');
                pageBtn.textContent = i;
                pageBtn.className = 'pagination-btn page-number';
                if (i === currentPage) {
                    pageBtn.classList.add('active');
                }
                pageBtn.onclick = () => goToPage(i);
                paginationButtons.appendChild(pageBtn);
            } else if (i === currentPage - 3 || i === currentPage + 3) {
                const ellipsis = document.createElement('span');
                ellipsis.textContent = '...';
                ellipsis.style.padding = '8px 4px';
                ellipsis.style.color = '#666';
                paginationButtons.appendChild(ellipsis);
            }
        }

        // Next button
        const nextBtn = document.createElement('button');
        nextBtn.textContent = 'Next';
        nextBtn.className = 'pagination-btn';
        nextBtn.disabled = currentPage === totalPages;
        nextBtn.onclick = () => goToPage(currentPage + 1);
        paginationButtons.appendChild(nextBtn);
    }

    function goToPage(page) {
        const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
        if (page < 1 || page > totalPages) return;
        currentPage = page;
        renderTable();
    }

    // Search functionality - filter by Child ID or Report ID
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase().trim();
        
        // Filter rows
        filteredRows = allReportRows.filter(row => {
            const childId = row.dataset.childid || '';
            const reportId = row.dataset.reportid || '';
            return childId.includes(searchTerm) || reportId.includes(searchTerm);
        });

        // Reset to first page when filtering
        currentPage = 1;
        
        // Re-render table with filtered results
        renderTable();
    });

    // Custom confirmation modal for delete
    function showDeleteConfirmation(reportId) {
        const modal = document.getElementById('confirmModal');
        if (modal) {
            modal.style.display = 'flex';
            modal.dataset.reportId = reportId;
        }
    }

    function closeConfirmModal() {
        const modal = document.getElementById('confirmModal');
        if (modal) {
            modal.style.animation = 'fadeOut 0.3s ease';
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }
    }

    function confirmDelete() {
        const modal = document.getElementById('confirmModal');
        const reportId = modal.dataset.reportId;
        if (reportId) {
            const form = document.getElementById('deleteForm_' + reportId);
            if (form) {
                form.submit();
            }
        }
        closeConfirmModal();
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('confirmModal');
        if (modal && event.target === modal) {
            closeConfirmModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeConfirmModal();
        }
    });
</script>

<!-- Custom Confirmation Modal -->
<div id="confirmModal" class="confirm-modal-overlay" style="display: none;">
    <div class="confirm-modal">
        <div class="confirm-icon">
            <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
        </div>
        <h2 class="confirm-title">CONFIRM DELETE</h2>
        <p class="confirm-text">Are you sure you want to delete this report? This action cannot be undone.</p>
        <div class="confirm-buttons">
            <button class="confirm-button cancel-button" onclick="closeConfirmModal()">Cancel</button>
            <button class="confirm-button delete-button" onclick="confirmDelete()">Delete</button>
        </div>
    </div>
</div>

<style>
    .confirm-modal-overlay {
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
    
    .confirm-modal {
        background: white;
        border-radius: 16px;
        padding: 50px 40px;
        max-width: 500px;
        width: 90%;
        text-align: center;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        animation: slideUp 0.3s ease;
    }
    
    .confirm-icon {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        margin: 0 auto 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #ff9800;
    }
    
    .confirm-title {
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #ff9800;
    }
    
    .confirm-text {
        font-size: 16px;
        color: #333;
        line-height: 1.6;
        margin-bottom: 30px;
        padding: 0 10px;
    }
    
    .confirm-buttons {
        display: flex;
        gap: 12px;
        justify-content: center;
    }
    
    .confirm-button {
        padding: 14px 40px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        min-width: 120px;
    }
    
    .cancel-button {
        background-color: #999;
        color: white;
    }
    
    .cancel-button:hover {
        background-color: #777;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(153, 153, 153, 0.3);
    }
    
    .delete-button {
        background-color: #f44336;
        color: white;
    }
    
    .delete-button:hover {
        background-color: #da190b;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(244, 67, 54, 0.3);
    }
    
    @media (max-width: 600px) {
        .confirm-modal {
            padding: 40px 30px;
            max-width: 90%;
        }
        
        .confirm-icon {
            width: 80px;
            height: 80px;
        }
        
        .confirm-title {
            font-size: 24px;
        }
        
        .confirm-text {
            font-size: 14px;
        }
        
        .confirm-buttons {
            flex-direction: column;
        }
        
        .confirm-button {
            width: 100%;
        }
    }
</style>
    </div>
@endsection

