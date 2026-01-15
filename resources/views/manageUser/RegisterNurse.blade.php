@extends('layouts.app')

@section('title', 'Register Nurse - Digital Child Health Record System')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <h1 class="page-title">NURSE</h1>
    </div>
    
    <div class="upload-section">
        <div class="file-input-wrapper">
            <input type="text" class="file-input" id="fileName" value="Document.csv" readonly>
        </div>
        <input type="file" id="csvFileInput" class="hidden-file-input" accept=".csv" onchange="handleFileSelect(this)">
        <button type="button" class="upload-icon-btn" onclick="document.getElementById('csvFileInput').click()" title="Select CSV File">
            <svg class="upload-icon" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM14 13v4h-4v-4H7l5-5 5 5h-3z"/>
            </svg>
        </button>
        <button type="button" class="upload-btn" onclick="handleUpload()">Upload</button>
    </div>
    
    <div id="uploadMessage" style="display: none; margin: 20px 0; padding: 10px; border-radius: 4px;"></div>
    
    <script>
        let selectedFile = null;
        
        function handleFileSelect(input) {
            if (input.files && input.files[0]) {
                selectedFile = input.files[0];
                document.getElementById('fileName').value = selectedFile.name;
                hideMessage();
            }
        }
        
        function showMessage(message, isError = false) {
            const messageDiv = document.getElementById('uploadMessage');
            messageDiv.textContent = message;
            messageDiv.style.display = 'block';
            messageDiv.style.backgroundColor = isError ? '#fee' : '#efe';
            messageDiv.style.color = isError ? '#c00' : '#060';
            messageDiv.style.border = `1px solid ${isError ? '#fcc' : '#cfc'}`;
        }
        
        function hideMessage() {
            document.getElementById('uploadMessage').style.display = 'none';
        }
        
        function handleUpload() {
            const fileInput = document.getElementById('csvFileInput');
            if (selectedFile || (fileInput.files && fileInput.files[0])) {
                const file = selectedFile || fileInput.files[0];
                if (file.name.toLowerCase().endsWith('.csv')) {
                    const formData = new FormData();
                    formData.append('csv_file', file);
                    
                    // Show loading message
                    showMessage('Uploading and processing CSV file...', false);
                    
                    fetch('{{ route("nurse.upload.csv") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Response data:', data); // Debug log
                        if (data.success) {
                            let message = data.message || `Successfully registered ${data.count || 0} nurse(s)!`;
                            
                            // Include errors in message if any
                            if (data.errors && data.errors.length > 0) {
                                message += '\n\nErrors:\n' + data.errors.join('\n');
                            }
                            
                            showMessage(message.replace(/\n/g, ' '), false);
                            
                            // Show success popup with details
                            let popupMessage = '✅ SUCCESS!\n\n' + message;
                            if (data.count > 0) {
                                popupMessage += '\n\n📧 Registration emails with temporary passwords have been sent to all registered nurses.';
                            }
                            alert(popupMessage);
                            
                            // Reload all nurses from database to show complete list
                            fetch('{{ route("nurse.list") }}')
                                .then(response => response.json())
                                .then(listData => {
                                    if (listData.success && listData.nurses) {
                                        updateNursesTable(listData.nurses);
                                    }
                                })
                                .catch(error => {
                                    console.error('Error loading nurse list:', error);
                                });
                            
                            // Reset file input
                            fileInput.value = '';
                            selectedFile = null;
                            document.getElementById('fileName').value = 'Document.csv';
                        } else {
                            const errorMsg = data.message || 'Error uploading file. Please try again.';
                            showMessage(errorMsg, true);
                            alert('❌ ERROR!\n\n' + errorMsg);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        const errorMsg = 'An error occurred while uploading the file. Please try again.';
                        showMessage(errorMsg, true);
                        alert('❌ ERROR!\n\n' + errorMsg);
                    });
                } else {
                    showMessage('Please select a CSV file.', true);
                }
            } else {
                showMessage('Please select a CSV file first.', true);
                document.getElementById('csvFileInput').click();
            }
        }
        
        let allNursesData = []; // Store all nurses data for pagination
        let currentNursePage = 1;
        const nursesPerPage = 20; // 20 items per page

        function renderNursesTable() {
            const tbody = document.querySelector('.doctors-table tbody');
            if (!tbody) {
                console.error('Table body not found!');
                return;
            }
            
            tbody.innerHTML = '';
            
            if (!allNursesData || allNursesData.length === 0) {
                const row = document.createElement('tr');
                row.innerHTML = '<td colspan="3" style="text-align: center; padding: 20px; color: #999;">No nurses registered yet.</td>';
                tbody.appendChild(row);
                updateNursePaginationControls();
                return;
            }
            
            const totalPages = Math.ceil(allNursesData.length / nursesPerPage);
            const startIndex = (currentNursePage - 1) * nursesPerPage;
            const endIndex = startIndex + nursesPerPage;
            const currentPageData = allNursesData.slice(startIndex, endIndex);
            
            currentPageData.forEach(nurse => {
                const row = document.createElement('tr');
                const nurseID = nurse.NurseID || nurse.id || nurse.NurseID || '';
                const fullName = nurse.FullName || nurse.full_name || nurse.FullName || '';
                const email = nurse.Email || nurse.email || nurse.Email || '';
                
                row.innerHTML = `
                    <td>${nurseID}</td>
                    <td>${fullName}</td>
                    <td>${email}</td>
                `;
                tbody.appendChild(row);
            });
            
            updateNursePaginationControls();
        }

        function updateNursePaginationControls() {
            let paginationContainer = document.getElementById('nursePaginationContainer');
            if (!paginationContainer) {
                paginationContainer = document.createElement('div');
                paginationContainer.id = 'nursePaginationContainer';
                paginationContainer.style.cssText = 'margin-top: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; padding: 20px 0;';
                const tableContainer = document.querySelector('.table-container');
                if (tableContainer && tableContainer.parentNode) {
                    tableContainer.parentNode.insertBefore(paginationContainer, tableContainer.nextSibling);
                }
            }
            
            const totalPages = Math.ceil(allNursesData.length / nursesPerPage);
            
            if (totalPages <= 1) {
                paginationContainer.innerHTML = '';
                return;
            }
            
            let html = '';
            html += `<button onclick="goToNursePage(${currentNursePage - 1})" ${currentNursePage === 1 ? 'disabled' : ''} style="padding: 10px 18px; border: 2px solid #e0e0e0; background: white; color: #333; cursor: pointer; border-radius: 8px; font-size: 14px; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);">Previous</button>`;
            
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentNursePage - 2 && i <= currentNursePage + 2)) {
                    html += `<button onclick="goToNursePage(${i})" style="padding: 10px 18px; border: 2px solid #e0e0e0; background: ${i === currentNursePage ? 'linear-gradient(135deg, #ff9eb3, #ff6f91)' : 'white'}; color: ${i === currentNursePage ? 'white' : '#333'}; cursor: pointer; border-radius: 8px; font-size: 14px; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);">${i}</button>`;
                } else if (i === currentNursePage - 3 || i === currentNursePage + 3) {
                    html += `<span style="padding: 10px 4px; color: #666; font-size: 14px;">...</span>`;
                }
            }
            
            html += `<button onclick="goToNursePage(${currentNursePage + 1})" ${currentNursePage === totalPages ? 'disabled' : ''} style="padding: 10px 18px; border: 2px solid #e0e0e0; background: white; color: #333; cursor: pointer; border-radius: 8px; font-size: 14px; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);">Next</button>`;
            
            const startIndex = (currentNursePage - 1) * nursesPerPage + 1;
            const endIndex = Math.min(currentNursePage * nursesPerPage, allNursesData.length);
            html += `<span style="margin-left: 15px; color: #666; font-size: 14px; font-weight: 500;">Showing ${startIndex}-${endIndex} of ${allNursesData.length} entries</span>`;
            
            paginationContainer.innerHTML = html;
        }

        function goToNursePage(page) {
            const totalPages = Math.ceil(allNursesData.length / nursesPerPage);
            if (page < 1 || page > totalPages) return;
            currentNursePage = page;
            renderNursesTable();
        }

        function updateNursesTable(nurses) {
            allNursesData = nurses; // Store all data
            currentNursePage = 1; // Reset to first page
            renderNursesTable();
        }
        
        // Load existing nurses on page load
        document.addEventListener('DOMContentLoaded', function() {
            fetch('{{ route("nurse.list") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.nurses) {
                        updateNursesTable(data.nurses);
                    }
                })
                .catch(error => {
                    console.error('Error loading nurses:', error);
                });
        });
    </script>
    
    <div class="table-container">
        <div class="table-wrapper">
            <table class="doctors-table">
                <thead>
                    <tr>
                        <th>
                            <div class="th-content">
                                <span>Nurse ID</span>
                            </div>
                        </th>
                        <th>
                            <div class="th-content">
                                <span>Full Name</span>
                            </div>
                        </th>
                        <th>
                            <div class="th-content">
                                <span>Email</span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Nurses will be listed here after CSV upload -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .table-container {
        background: linear-gradient(135deg, #ffffff 0%, #fff9fb 100%);
        border-radius: 20px;
        padding: 32px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
        margin-top: 30px;
        border: 1px solid rgba(255, 111, 145, 0.1);
    }

    .table-wrapper {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
        border: 1px solid #f0f0f0;
    }

    .doctors-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin: 0;
    }

    .doctors-table thead {
        background: linear-gradient(135deg, #ffe0e9 0%, #ffb6c1 100%);
        position: relative;
    }

    .doctors-table thead::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #ff6f91, #ff9eb3);
    }

    .doctors-table th {
        padding: 20px 24px;
        text-align: left;
        font-weight: 700;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #1c1c1c;
        position: relative;
    }

    .th-content {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .doctors-table th:first-child {
        border-top-left-radius: 16px;
    }

    .doctors-table th:last-child {
        border-top-right-radius: 16px;
    }

    .doctors-table tbody tr {
        transition: all 0.3s ease;
        border-bottom: 1px solid #f5f5f5;
    }

    .doctors-table tbody tr:last-child {
        border-bottom: none;
    }

    .doctors-table tbody tr:hover {
        background: linear-gradient(90deg, #fff5f5 0%, #ffe0e9 100%);
        transform: translateX(4px);
        box-shadow: 0 4px 12px rgba(255, 111, 145, 0.1);
    }

    .doctors-table tbody tr:nth-child(even) {
        background-color: #fff9fb;
    }

    .doctors-table tbody tr:nth-child(odd) {
        background-color: #ffffff;
    }

    .doctors-table tbody tr:nth-child(even):hover,
    .doctors-table tbody tr:nth-child(odd):hover {
        background: linear-gradient(90deg, #fff5f5 0%, #ffe0e9 100%);
    }

    .doctors-table td {
        padding: 18px 24px;
        font-size: 15px;
        color: #333;
        font-weight: 500;
        vertical-align: middle;
    }

    .doctors-table tbody tr:first-child td:first-child {
        border-top-left-radius: 0;
    }

    .doctors-table tbody tr:first-child td:last-child {
        border-top-right-radius: 0;
    }

    .doctors-table tbody tr:last-child td:first-child {
        border-bottom-left-radius: 16px;
    }

    .doctors-table tbody tr:last-child td:last-child {
        border-bottom-right-radius: 16px;
    }

    @media (max-width: 768px) {
        .table-container {
            padding: 20px;
            border-radius: 16px;
        }

        .doctors-table th,
        .doctors-table td {
            padding: 12px 16px;
            font-size: 13px;
        }
    }
</style>
@endsection
