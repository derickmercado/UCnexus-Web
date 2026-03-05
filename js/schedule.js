// ===== SCHEDULE PAGE FUNCTIONALITY =====

// ===== FILTER FUNCTIONALITY =====
function applyFilters() {
    const yearFilter = document.getElementById('filterYear')?.value || 'all';
    const typeFilter = document.getElementById('filterType')?.value || 'all';
    const dayFilter = document.getElementById('filterDay')?.value || 'all';
    const instructorFilter = document.getElementById('filterInstructor')?.value || 'all';
    
    // Get all schedule items
    const scheduleItems = document.querySelectorAll('.schedule-item');
    const yearSections = document.querySelectorAll('[data-year-section]');
    
    scheduleItems.forEach(item => {
        let show = true;
        
        // Get item data attributes
        const itemYear = item.dataset.year || '';
        const itemCode = item.dataset.code || '';
        const itemDays = (item.dataset.days || '').toLowerCase();
        const itemInstructor = item.dataset.instructor || '';
        const itemLabInstructor = item.dataset.labInstructor || '';
        
        // Year filter
        if (yearFilter !== 'all' && itemYear !== yearFilter) {
            show = false;
        }
        
        // Type filter
        if (typeFilter !== 'all') {
            const codeUpper = itemCode.toUpperCase();
            if (typeFilter === 'citcc') {
                if (!codeUpper.startsWith('CIT') && !codeUpper.startsWith('CC')) {
                    show = false;
                }
            } else if (typeFilter === 'gened') {
                const genEdPrefixes = ['ENGL', 'HIST', 'MATH', 'PSYCH', 'TECHNO', 'CORDI', 'FL', 'SCIENCE', 'SOC'];
                const isGenEd = genEdPrefixes.some(p => codeUpper.startsWith(p));
                if (!isGenEd) show = false;
            } else if (typeFilter === 'pe') {
                if (!codeUpper.startsWith('PATHFIT') && !codeUpper.startsWith('NSTP')) {
                    show = false;
                }
            }
        }
        
        // Day filter
        if (dayFilter !== 'all') {
            if (!itemDays.includes(dayFilter)) {
                show = false;
            }
        }
        
        // Instructor filter
        if (instructorFilter !== 'all') {
            if (itemInstructor !== instructorFilter && itemLabInstructor !== instructorFilter) {
                show = false;
            }
        }
        
        // Show/hide item
        item.style.display = show ? '' : 'none';
    });
    
    // Hide year sections with no visible items
    yearSections.forEach(section => {
        const visibleItems = section.querySelectorAll('.schedule-item:not([style*="display: none"])');
        section.style.display = visibleItems.length > 0 ? '' : 'none';
    });
}

function resetFilters() {
    document.getElementById('filterYear').value = 'all';
    document.getElementById('filterType').value = 'all';
    document.getElementById('filterDay').value = 'all';
    document.getElementById('filterInstructor').value = 'all';
    
    // Show all items
    document.querySelectorAll('.schedule-item').forEach(item => {
        item.style.display = '';
    });
    document.querySelectorAll('[data-year-section]').forEach(section => {
        section.style.display = '';
    });
}

function editSchedule(id) {
    alert('Edit functionality to be implemented with backend');
}

function deleteSchedule(id) {
    if (confirm('Are you sure you want to delete this schedule?')) {
        // Implement delete functionality with backend
        showNotification('Schedule deleted successfully');
    }
}

function addSchedule() {
    // Implement add schedule functionality
    showNotification('Schedule added successfully');
}

function exportSchedule() {
    // Implement export schedule functionality
    showNotification('Schedule exported successfully');
}
