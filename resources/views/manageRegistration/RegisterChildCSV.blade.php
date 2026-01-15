@extends('layouts.app')

@section('title', 'Register Child via CSV - Digital Child Health Record System')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <h1 class="page-title">CHILD</h1>
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
        let allChildrenData = []; // Store all children data
        let currentPage = 1;
        const itemsPerPage = 20;
        
        function handleFileSelect(input) {
            if (input.files && input.files[0]) {
                selectedFile = input.files[0];
                document.getElementById('fileName').value = selectedFile.name;
                hideMessage();
            }
        }
        
        function showMessage(message, isError = false) {
            const messageDiv = document.getElementById('uploadMessage');
            // Replace newlines with <br> for HTML display
            messageDiv.innerHTML = message.replace(/\n/g, '<br>');
            messageDiv.style.display = 'block';
            messageDiv.style.backgroundColor = isError ? '#fee' : '#efe';
            messageDiv.style.color = isError ? '#c00' : '#060';
            messageDiv.style.border = `1px solid ${isError ? '#fcc' : '#cfc'}`;
            messageDiv.style.whiteSpace = 'pre-line';
            messageDiv.style.padding = '15px';
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
                    
                    fetch('{{ route("child.upload.csv") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    })
                    .then(async response => {
                        // Clone the response to read it as text if JSON parsing fails
                        const responseClone = response.clone();
                        let data;
                        
                        try {
                            const contentType = response.headers.get('content-type');
                            if (contentType && contentType.includes('application/json')) {
                                data = await response.json();
                            } else {
                                // If not JSON, try to read as text
                                const text = await responseClone.text();
                                console.error('Non-JSON response:', text);
                                throw new Error('Server returned non-JSON response. Please check the server logs.');
                            }
                        } catch (e) {
                            // If JSON parsing fails, try to read as text for error details
                            try {
                                const text = await responseClone.text();
                                console.error('Response text:', text);
                                throw new Error(text || 'Error uploading file. Invalid response from server.');
                            } catch (textError) {
                                throw new Error('Error uploading file. Unable to parse server response.');
                            }
                        }
                        
                        if (!response.ok) {
                            throw new Error(data.message || `Server error: ${response.status} ${response.statusText}`);
                        }
                        return data;
                    })
                    .then(data => {
                        console.log('Response data:', data); // Debug log
                        if (data.success) {
                            let message = data.message || `Successfully registered ${data.count || 0} child(ren)!`;
                            
                            showMessage(message.replace(/\n/g, ' '), false);
                            
                            // Show success popup with details
                            let popupMessage = '✅ SUCCESS!\n\n' + message;
                            if (data.count > 0) {
                                popupMessage += '\n\nAll registered children are now available in the system for appointment booking.';
                            }
                            alert(popupMessage);
                            
                            // Reload all children from database to show complete list
                            fetch('{{ route("child.list") }}', {
                                method: 'GET',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                credentials: 'same-origin'
                            })
                                .then(response => response.json())
                                .then(listData => {
                                    if (listData.success && listData.children) {
                                        console.log('All children from database:', listData.children); // Debug log
                                        updateChildrenTable(listData.children);
                                    } else if (data.children && data.children.length > 0) {
                                        // Fallback: add new children to existing table
                                        addChildrenToTable(data.children);
                                    }
                                })
                                .catch(error => {
                                    console.error('Error loading child list:', error);
                                    // Fallback: add new children to existing table
                                    if (data.children && data.children.length > 0) {
                                        addChildrenToTable(data.children);
                                    }
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
                        console.error('Fetch error:', error);
                        let errorMessage = 'An error occurred while uploading the file.';
                        
                        if (error.message) {
                            errorMessage = error.message;
                        } else if (error.name === 'TypeError' && error.message.includes('fetch')) {
                            errorMessage = 'Network error: Unable to connect to server. Please check your internet connection and try again.';
                        }
                        
                        console.error('Full error details:', {
                            name: error.name,
                            message: error.message,
                            stack: error.stack
                        });
                        
                        alert('❌ ERROR!\n\n' + errorMessage);
                        showMessage(errorMessage, true);
                    });
                } else {
                    showMessage('Please select a CSV file.', true);
                }
            } else {
                showMessage('Please select a CSV file first.', true);
                document.getElementById('csvFileInput').click();
            }
        }
        
        function updateChildrenTable(children) {
            // Store all children data
            allChildrenData = children || [];
            currentPage = 1; // Reset to first page
            renderTable();
        }
        
        function renderTable() {
            const tbody = document.querySelector('.doctors-table tbody');
            if (!tbody) {
                console.error('Table body not found!');
                return;
            }
            
            // Clear existing rows
            tbody.innerHTML = '';
            
            if (!allChildrenData || allChildrenData.length === 0) {
                const row = document.createElement('tr');
                row.innerHTML = '<td colspan="3" style="text-align: center; padding: 20px; color: #999;">No children registered yet.</td>';
                tbody.appendChild(row);
                updatePaginationControls();
                return;
            }
            
            // Calculate pagination
            const totalPages = Math.ceil(allChildrenData.length / itemsPerPage);
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            const currentPageData = allChildrenData.slice(startIndex, endIndex);
            
            // Render current page data
            currentPageData.forEach(child => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${child.FullName || ''}</td>
                    <td>${child.MotherName || ''}</td>
                    <td>${child.FatherName || ''}</td>
                `;
                tbody.appendChild(row);
            });
            
            updatePaginationControls();
        }
        
        function updatePaginationControls() {
            let paginationContainer = document.getElementById('paginationContainer');
            if (!paginationContainer) {
                // Create pagination container if it doesn't exist
                paginationContainer = document.createElement('div');
                paginationContainer.id = 'paginationContainer';
                
                const tableContainer = document.querySelector('.table-container');
                if (tableContainer) {
                    tableContainer.appendChild(paginationContainer);
                }
            }
            
            const totalPages = Math.ceil(allChildrenData.length / itemsPerPage);
            
            if (totalPages <= 1) {
                paginationContainer.innerHTML = '';
                return;
            }
            
            let html = '';
            
            // Previous button
            html += `<button onclick="goToPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>Previous</button>`;
            
            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                    const isActive = i === currentPage;
                    html += `<button onclick="goToPage(${i})" ${isActive ? 'style="background: linear-gradient(135deg, #ff9eb3, #ff6f91) !important; color: white !important; border-color: #ff6f91 !important; box-shadow: 0 4px 12px rgba(255, 111, 145, 0.3) !important;"' : ''}>${i}</button>`;
                } else if (i === currentPage - 3 || i === currentPage + 3) {
                    html += `<span>...</span>`;
                }
            }
            
            // Next button
            html += `<button onclick="goToPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>Next</button>`;
            
            // Page info
            const startIndex = (currentPage - 1) * itemsPerPage + 1;
            const endIndex = Math.min(currentPage * itemsPerPage, allChildrenData.length);
            html += `<span style="margin-left: 15px; color: #666; font-size: 14px; font-weight: 500;">Showing ${startIndex}-${endIndex} of ${allChildrenData.length} entries</span>`;
            
            paginationContainer.innerHTML = html;
        }
        
        function goToPage(page) {
            const totalPages = Math.ceil(allChildrenData.length / itemsPerPage);
            if (page < 1 || page > totalPages) return;
            currentPage = page;
            renderTable();
        }
        
        function addChildrenToTable(children) {
            // Add new children to the existing data
            if (children && children.length > 0) {
                // Prepend new children to the beginning (newest first)
                allChildrenData = [...children, ...allChildrenData];
                currentPage = 1; // Reset to first page to show new entries
                renderTable();
            }
        }
        
        // Load existing children on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Load all children from database
            @if(isset($children) && count($children) > 0)
                updateChildrenTable(@json($children));
            @else
                // Try to load via API if available
                fetch('{{ route("child.list") }}')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.children) {
                            updateChildrenTable(data.children);
                        }
                    })
                    .catch(error => {
                        console.error('Error loading children:', error);
                    });
            @endif
        });
    </script>
    
    <div class="table-container">
        <div class="table-wrapper">
            <table class="doctors-table">
                <thead>
                    <tr>
                        <th>
                            <div class="th-content">
                                <span>Child Name</span>
                            </div>
                        </th>
                        <th>
                            <div class="th-content">
                                <span>Mother Name</span>
                            </div>
                        </th>
                        <th>
                            <div class="th-content">
                                <span>Father Name</span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Children will be listed here after CSV upload -->
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

    #paginationContainer {
        margin-top: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        padding: 20px 0;
    }

    #paginationContainer button {
        padding: 10px 18px;
        border: 2px solid #e0e0e0;
        background: white;
        color: #333;
        cursor: pointer;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    #paginationContainer button:hover:not(:disabled) {
        background: linear-gradient(135deg, #ffe0e9, #ffb6c1);
        border-color: #ff6f91;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 111, 145, 0.2);
        color: #1c1c1c;
    }

    #paginationContainer button:disabled {
        background: #f5f5f5;
        color: #999;
        cursor: not-allowed;
        border-color: #e0e0e0;
        opacity: 0.6;
    }

    #paginationContainer button[style*="background: #ff6f91"] {
        background: linear-gradient(135deg, #ff9eb3, #ff6f91) !important;
        color: white !important;
        border-color: #ff6f91 !important;
        box-shadow: 0 4px 12px rgba(255, 111, 145, 0.3) !important;
    }

    #paginationContainer span {
        color: #666;
        font-size: 14px;
        font-weight: 500;
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

        #paginationContainer {
            flex-direction: column;
            align-items: stretch;
        }

        #paginationContainer button {
            width: 100%;
        }
    }
</style>
@endsection
