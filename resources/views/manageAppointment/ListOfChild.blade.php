@extends('layouts.app')

@section('title', 'List of Children - Digital Child Health Record System')

@section('content')
<div class="content-wrapper">
    <div class="filter-section">
        <div class="filter-container">
            <div class="search-wrapper">
                <svg class="search-icon" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                </svg>
                <input type="text" class="search-input" placeholder="Search by name or Child ID..." id="searchInput">
            </div>
            <button class="sort-btn" onclick="toggleSort()" title="Sort Alphabetically">
                <svg class="sort-icon" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M3 18h6v-2H3v2zM3 6v2h18V6H3zm0 7h12v-2H3v2z"/>
                </svg>
                <span class="sort-text">A↑Z↓</span>
            </button>
        </div>
    </div>
    
    <div class="children-list" id="childrenList" data-sort="asc">
        @forelse($children as $child)
            @php
                // Handle both object and array formats
                if (is_object($child)) {
                    $childIdValue = $child->ChildID ?? '';
                    $childFullName = $child->FullName ?? 'Unknown';
                } else {
                    $childIdValue = $child['ChildID'] ?? '';
                    $childFullName = $child['FullName'] ?? 'Unknown';
                }
                
                // Log if ChildID is missing for debugging
                if (empty($childIdValue)) {
                    \Log::error('Child ID is missing in ListOfChild view', [
                        'child_type' => gettype($child),
                        'child_data' => is_object($child) ? (array)$child : $child
                    ]);
                }
            @endphp
            @if(!empty($childIdValue))
                <a href="{{ route('booking.form', $childIdValue) }}" class="child-card" data-name="{{ strtolower($childFullName) }}" data-childid="{{ strtolower($childIdValue) }}">
                    <span class="child-id">{{ $childIdValue }}</span>
                    <span class="child-name">{{ $childFullName }}</span>
                </a>
            @else
                <div class="child-card" style="background-color: #ffebee; color: red;">
                    <span class="child-name">ERROR: Child ID missing for {{ $childFullName }}</span>
                </div>
            @endif
        @empty
            <div style="padding: 20px; text-align: center; color: #999;">
                No children registered yet.
            </div>
        @endforelse
    </div>
</div>

<style>
    .filter-section {
        margin-bottom: 24px;
        padding: 0 20px;
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

    .sort-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 20px;
        background: linear-gradient(135deg, #ffe0e9, #ffb6c1);
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 15px;
        font-weight: 600;
        color: #333;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .sort-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        background: linear-gradient(135deg, #ffb6c1, #ff9eb3);
    }

    .sort-btn:active {
        transform: translateY(0);
    }

    .sort-icon {
        width: 20px;
        height: 20px;
        color: #333;
    }

    .sort-text {
        font-weight: 700;
        color: #333;
        letter-spacing: 0.5px;
    }
    
    .children-list {
        padding: 0 20px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    
    .child-card {
        background-color: #FFF5F5;
        padding: 15px 20px;
        border-radius: 10px;
        cursor: pointer;
        transition: background-color 0.3s, transform 0.2s;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .child-card:hover {
        background-color: #FFE5E5;
        transform: translateX(5px);
    }
    
    .child-id {
        font-size: 14px;
        font-weight: 600;
        color: #ff6f91;
        background-color: white;
        padding: 4px 10px;
        border-radius: 6px;
        border: 1px solid #ffb6c1;
        white-space: nowrap;
    }
    
    .child-name {
        font-size: 16px;
        font-weight: 500;
        color: #000;
        text-transform: uppercase;
        flex: 1;
    }

    @media (max-width: 768px) {
        .filter-container {
            flex-direction: column;
            align-items: stretch;
        }

        .search-wrapper {
            max-width: 100%;
        }

        .sort-btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<script>
    let sortOrder = 'asc'; // 'asc' for A-Z, 'desc' for Z-A

    function toggleSort() {
        const list = document.getElementById('childrenList');
        const children = Array.from(list.children);
        const sortText = document.querySelector('.sort-text');
        
        // Filter out empty state message if present - get all child cards
        const childCards = children.filter(child => {
            return child.classList && child.classList.contains('child-card');
        });
        
        // If no child cards, return
        if (childCards.length === 0) {
            return;
        }
        
        // Sort children alphabetically by name
        childCards.sort((a, b) => {
            // Get the child name from the card
            const nameElementA = a.querySelector('.child-name');
            const nameElementB = b.querySelector('.child-name');
            const nameA = (nameElementA ? nameElementA.textContent : a.textContent || '').trim().toLowerCase();
            const nameB = (nameElementB ? nameElementB.textContent : b.textContent || '').trim().toLowerCase();
            
            if (sortOrder === 'asc') {
                // A-Z sorting
                return nameA.localeCompare(nameB);
            } else {
                // Z-A sorting
                return nameB.localeCompare(nameA);
            }
        });
        
        // Create a document fragment to preserve links
        const fragment = document.createDocumentFragment();
        childCards.forEach(child => fragment.appendChild(child));
        
        // Clear and re-append
        list.innerHTML = '';
        list.appendChild(fragment);
        
        // Update sort state and button text
        sortOrder = sortOrder === 'asc' ? 'desc' : 'asc';
        if (sortText) {
            sortText.textContent = sortOrder === 'asc' ? 'A↑Z↓' : 'Z↓A↑';
        }
    }
    
    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase().trim();
        const cards = document.querySelectorAll('.child-card');
        
        cards.forEach(card => {
            const name = card.dataset.name || '';
            const childId = card.dataset.childid || '';
            const nameText = card.querySelector('.child-name')?.textContent.toLowerCase() || '';
            
            if (name.includes(searchTerm) || childId.includes(searchTerm) || nameText.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>
@endsection
