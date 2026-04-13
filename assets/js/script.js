// Custom JavaScript untuk Event Rental System

// Document Ready
document.addEventListener('DOMContentLoaded', function() {
    
    // Initialize tooltips (if Bootstrap JS is available)
    if (window.bootstrap && bootstrap.Tooltip) {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    // Navbar scroll effect
    setupNavbarScrollEffect();
    
    // Auto-hide alerts after 5 seconds (if Bootstrap JS is available)
    if (window.bootstrap && bootstrap.Alert) {
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    }
    
    // Animate numbers on stats cards
    animateNumbers();
    
    // Add hover effects to cards
    addCardHoverEffects();
    
    // Form validation enhancement
    enhanceFormValidation();
    
    // Table search functionality
    addTableSearch();
    
    // Confirm delete actions
    confirmDeleteActions();
    
    // Auto-calculate dates
    autoCalculateDates();
    
    // Smooth scroll
    enableSmoothScroll();
    
    // Mobile sidebar toggle
    setupMobileSidebar();

    // Tabs fallback (if Bootstrap JS is unavailable)
    setupTabsFallback();
});

// Navbar scroll effect
function setupNavbarScrollEffect() {
    const navbar = document.querySelector('.navbar');
    if (!navbar) return;
    
    let lastScroll = 0;
    
    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
        
        lastScroll = currentScroll;
    });
}

// Animate numbers in stats cards
function animateNumbers() {
    const numbers = document.querySelectorAll('.stats-number, h3, h4');
    
    numbers.forEach(function(number) {
        const text = number.textContent.trim();
        const numMatch = text.match(/\d+/);
        
        if (numMatch) {
            const finalNumber = parseInt(numMatch[0]);
            const duration = 1000;
            const increment = finalNumber / (duration / 16);
            let current = 0;
            
            const timer = setInterval(function() {
                current += increment;
                if (current >= finalNumber) {
                    current = finalNumber;
                    clearInterval(timer);
                }
                number.textContent = text.replace(/\d+/, Math.floor(current));
            }, 16);
        }
    });
}

// Add hover effects to cards
function addCardHoverEffects() {
    const cards = document.querySelectorAll('.card');
    
    cards.forEach(function(card) {
        card.classList.add('hover-lift');
    });
}

// Enhanced form validation
function enhanceFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
}

// Add search functionality to tables
function addTableSearch() {
    // Keep pemantau pengembalian clean: avoid injecting many auto-search boxes
    if (document.querySelector('.return-monitor-page')) return;

    const tables = document.querySelectorAll('table');

    tables.forEach(function(table) {
        // Keep search only for main listing tables
        if (table.closest('.modal') || table.classList.contains('table-sm')) return;
        if (table.dataset.searchReady === '1') return;
        table.dataset.searchReady = '1';

        const searchDiv = document.createElement('div');
        searchDiv.className = 'mb-3 table-search-wrap';
        searchDiv.innerHTML = '<input type="text" class="form-control table-search-input" placeholder="Cari data...">';

        const input = searchDiv.querySelector('input');
        input.addEventListener('input', function() {
            searchTable(this, table);
        });

        if (table.parentElement.classList.contains('table-responsive')) {
            table.parentElement.parentElement.insertBefore(searchDiv, table.parentElement);
        } else {
            table.parentElement.insertBefore(searchDiv, table);
        }
    });
}

// Search table function
function searchTable(input, tableId) {
    const filter = input.value.toUpperCase();
    const table = typeof tableId === 'object'
        ? tableId
        : (tableId ? document.getElementById(tableId) : input.closest('.card-body').querySelector('table'));
    if (!table) return;
    const tr = table.getElementsByTagName('tr');
    
    for (let i = 1; i < tr.length; i++) {
        let found = false;
        const td = tr[i].getElementsByTagName('td');
        
        for (let j = 0; j < td.length; j++) {
            if (td[j]) {
                const txtValue = td[j].textContent || td[j].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }
        
        tr[i].style.display = found ? '' : 'none';
    }
}

// Confirm delete actions
function confirmDeleteActions() {
    const deleteLinks = document.querySelectorAll('a[href*="?delete="], a[href*="&delete="], button[onclick*="delete"]');
    
    deleteLinks.forEach(function(link) {
        // Skip links that are just for viewing deleted items
        if (link.href && link.href.includes('show=deleted')) {
            return;
        }
        
        link.addEventListener('click', function(event) {
            if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                event.preventDefault();
            }
        });
    });
}

// Auto-calculate dates and totals
function autoCalculateDates() {
    const tanggalPinjam = document.querySelector('input[name="tanggal_pinjam"]');
    const tanggalKembali = document.querySelector('input[name="tanggal_kembali"]');
    
    if (tanggalPinjam && tanggalKembali) {
        tanggalPinjam.addEventListener('change', function() {
            // Set minimum return date
            tanggalKembali.min = this.value;
            
            // Auto-set return date to 3 days later
            const pinjamDate = new Date(this.value);
            pinjamDate.setDate(pinjamDate.getDate() + 3);
            tanggalKembali.value = pinjamDate.toISOString().split('T')[0];
        });
        
        // Calculate total when dates change
        tanggalKembali.addEventListener('change', calculateTotal);
    }
}

// Calculate total cost
function calculateTotal() {
    const tanggalPinjam = document.querySelector('input[name="tanggal_pinjam"]');
    const tanggalKembali = document.querySelector('input[name="tanggal_kembali"]');
    
    if (tanggalPinjam && tanggalKembali && tanggalPinjam.value && tanggalKembali.value) {
        const days = Math.ceil((new Date(tanggalKembali.value) - new Date(tanggalPinjam.value)) / (1000 * 60 * 60 * 24));
        // Durasi dihitung tapi tidak perlu ditampilkan sebagai notifikasi
    }
}

// Show notification
function showNotification(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
    alertDiv.style.zIndex = '9999';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(function() {
        alertDiv.remove();
    }, 3000);
}

// Smooth scroll
function enableSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Mobile sidebar toggle
function setupMobileSidebar() {
    const sidebar = document.querySelector('.sidebar');
    
    if (sidebar && window.innerWidth <= 768) {
        // Create toggle button
        const toggleBtn = document.createElement('button');
        toggleBtn.className = 'btn btn-primary position-fixed';
        toggleBtn.style.cssText = 'top: 70px; left: 10px; z-index: 1001;';
        toggleBtn.innerHTML = '<i class="bi bi-list"></i>';
        
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });
        
        document.body.appendChild(toggleBtn);
        
        // Close sidebar when clicking outside
        document.addEventListener('click', function(event) {
            if (!sidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
                sidebar.classList.remove('show');
            }
        });
    }
}

// Tabs fallback when Bootstrap JS is not loaded
function setupTabsFallback() {
    if (window.bootstrap && bootstrap.Tab) return;

    const tabLinks = document.querySelectorAll('.nav-tabs .nav-link[data-bs-toggle="tab"]');
    if (!tabLinks.length) return;

    tabLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetSelector = this.getAttribute('href');
            if (!targetSelector) return;
            const targetPane = document.querySelector(targetSelector);
            if (!targetPane) return;

            const nav = this.closest('.nav-tabs');
            if (nav) {
                nav.querySelectorAll('.nav-link').forEach(function(l) {
                    l.classList.remove('active');
                });
            }

            const tabContent = targetPane.closest('.tab-content');
            if (tabContent) {
                tabContent.querySelectorAll('.tab-pane').forEach(function(pane) {
                    pane.classList.remove('show', 'active');
                });
            }

            this.classList.add('active');
            targetPane.classList.add('show', 'active');
        });
    });
}

// Format currency input
function formatCurrency(input) {
    let value = input.value.replace(/[^0-9]/g, '');
    value = parseInt(value) || 0;
    input.value = value.toLocaleString('id-ID');
}

// Print function
function printPage() {
    window.print();
}

// Export to CSV
function exportTableToCSV(tableId, filename = 'data.csv') {
    const table = document.getElementById(tableId);
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = [], cols = rows[i].querySelectorAll('td, th');
        
        for (let j = 0; j < cols.length; j++) {
            row.push(cols[j].innerText);
        }
        
        csv.push(row.join(','));
    }
    
    downloadCSV(csv.join('\n'), filename);
}

function downloadCSV(csv, filename) {
    const csvFile = new Blob([csv], { type: 'text/csv' });
    const downloadLink = document.createElement('a');
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}

// Real-time input validation
document.addEventListener('input', function(e) {
    if (e.target.type === 'number') {
        const min = parseInt(e.target.min) || 0;
        const max = parseInt(e.target.max) || Infinity;
        const value = parseInt(e.target.value) || 0;
        
        if (value < min) e.target.value = min;
        if (value > max) e.target.value = max;
    }
});

// Loading overlay
function showLoading() {
    const overlay = document.createElement('div');
    overlay.id = 'loadingOverlay';
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    `;
    overlay.innerHTML = '<div class="spinner-border text-light" role="status"></div>';
    document.body.appendChild(overlay);
}

function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) overlay.remove();
}

// Auto-save form data to localStorage
function autoSaveForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    // Load saved data
    const savedData = localStorage.getItem(formId);
    if (savedData) {
        const data = JSON.parse(savedData);
        Object.keys(data).forEach(key => {
            const input = form.querySelector(`[name="${key}"]`);
            if (input) input.value = data[key];
        });
    }
    
    // Save on input
    form.addEventListener('input', function() {
        const formData = new FormData(form);
        const data = {};
        formData.forEach((value, key) => data[key] = value);
        localStorage.setItem(formId, JSON.stringify(data));
    });
    
    // Clear on submit
    form.addEventListener('submit', function() {
        localStorage.removeItem(formId);
    });
}

// Notification Bell Animation
function animateNotificationBell() {
    const bell = document.querySelector('.bi-bell-fill');
    if (bell) {
        setInterval(function() {
            const badge = document.querySelector('.badge.bg-danger');
            if (badge && parseInt(badge.textContent) > 0) {
                bell.style.animation = 'ring 1s ease-in-out';
                setTimeout(function() {
                    bell.style.animation = '';
                }, 1000);
            }
        }, 5000); // Ring every 5 seconds if there are notifications
    }
}

// Auto-refresh notification count (optional - untuk real-time updates)
function refreshNotifications() {
    // Bisa ditambahkan AJAX call untuk update notifikasi tanpa reload
    // Untuk sekarang, notifikasi akan update saat page reload
}

// Play notification sound (optional)
function playNotificationSound() {
    // Bisa ditambahkan audio notification
    // const audio = new Audio('path/to/notification.mp3');
    // audio.play();
}

// Mark notification as read
function markAsRead(notificationId) {
    // AJAX call untuk mark notification sebagai read
    // Untuk sekarang, notifikasi akan hilang saat di-dismiss
}

// Initialize notification features
document.addEventListener('DOMContentLoaded', function() {
    animateNotificationBell();
    
    // Add click sound to notification items
    const notifItems = document.querySelectorAll('.dropdown-menu .dropdown-item');
    notifItems.forEach(function(item) {
        item.addEventListener('click', function() {
            // Bisa ditambahkan action saat notifikasi diklik
        });
    });
});

console.log('🎉 Event Rental System loaded successfully!');

