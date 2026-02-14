/**
 * Dashboard Module Integration System
 * Handles smooth AJAX content loading and navigation without page reloads
 */

class DashboardManager {
    constructor() {
        this.currentView = 'profile';
        this.currentTab = 'All';
        this.isLoading = false;
        this.mainContent = document.querySelector('main[role="main"]');
        
        // Validate main content element exists
        if (!this.mainContent) {
            console.error('DashboardManager: Could not find main[role="main"] element');
            return;
        }
        
        console.log('DashboardManager: Initializing...');
        this.init();
        console.log('DashboardManager: Initialization complete');
    }

    init() {
        try {
            this.setupNavigation();
            this.setupHistoryAPI();
            this.loadInitialView();
        } catch (error) {
            console.error('DashboardManager init error:', error);
        }
    }

    setupNavigation() {
        // Handle all sidebar menu links with data-view attribute
        document.querySelectorAll('a[data-view]').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const view = link.getAttribute('data-view');
                if (view) {
                    this.switchView(view);
                }
            });
        });

        // Also handle main menu titles
        document.querySelectorAll('.sidebar-menu-title').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const view = link.getAttribute('data-view');
                if (view) {
                    this.switchView(view);
                }
            });
        });

        // Handle submenu links
        document.querySelectorAll('.sidebar-submenu-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const view = link.getAttribute('data-view');
                if (view) {
                    this.switchView(view);
                }
            });
        });

        // Handle order tab buttons (delegate for dynamically loaded content)
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('order-tab-btn')) {
                e.preventDefault();
                const tab = e.target.dataset.tab;
                this.switchTab(tab);
            }
        });

        // Handle view order buttons
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('view-order-btn')) {
                const orderId = e.target.dataset.orderId;
                console.log('View order:', orderId);
            }
        });

        // Handle track order buttons
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('track-order-btn') || e.target.closest('.track-order-btn')) {
                const btn = e.target.classList.contains('track-order-btn') ? e.target : e.target.closest('.track-order-btn');
                const orderId = btn.dataset.orderId;
                window.location.href = '../Categories/best_selling/Tracking.php?order_id=' + orderId;
            }
        });
    }

    setupHistoryAPI() {
        window.addEventListener('popstate', (e) => {
            if (e.state) {
                this.currentView = e.state.view || 'profile';
                this.currentTab = e.state.tab || 'All';
                this.loadViewContent();
            }
        });
    }

    loadInitialView() {
        // Check URL parameters on page load
        const params = new URLSearchParams(window.location.search);
        const view = params.get('view') || 'profile';
        const tab = params.get('tab') || 'All';
        
        this.currentView = view;
        this.currentTab = tab;
        this.loadViewContent();
    }

    switchView(view) {
        if (view === this.currentView) return;
        
        this.currentView = view;
        this.currentTab = 'All'; // Reset tab when switching views
        
        // Update browser history
        const state = { view: view, tab: 'All' };
        window.history.pushState(state, '', `?view=${view}`);
        
        // Load new content
        this.loadViewContent();
        this.updateActiveNav();
    }

    switchTab(tab) {
        if (tab === this.currentTab) return;
        
        this.currentTab = tab;
        
        // Update browser history
        const state = { view: this.currentView, tab: tab };
        window.history.pushState(state, '', `?view=${this.currentView}&tab=${tab}`);
        
        // Load new content with tab filter
        this.loadViewContent();
    }

    async loadViewContent() {
        if (this.isLoading) {
            console.log('DashboardManager: Already loading, skipping request');
            return;
        }
        this.isLoading = true;

        try {
            // Show loading state
            this.mainContent.style.opacity = '0.5';
            this.mainContent.style.pointerEvents = 'none';

            const params = new URLSearchParams({
                view: this.currentView,
                tab: this.currentTab
            });

            const url = `ajax_load_view.php?${params.toString()}`;
            console.log('DashboardManager: Fetching from:', url);

            const response = await fetch(url);
            
            if (!response.ok) {
                throw new Error(`HTTP Error ${response.status}: ${response.statusText}`);
            }

            const responseText = await response.text();
            console.log('DashboardManager: Response received, length:', responseText.length);

            // Try to parse as JSON
            let data;
            try {
                data = JSON.parse(responseText);
            } catch (parseError) {
                console.error('DashboardManager: JSON Parse Error - Response starts with:', responseText.substring(0, 200));
                throw new Error('Invalid JSON response from server: ' + parseError.message);
            }

            // Validate response format
            if (!data || typeof data !== 'object') {
                throw new Error('Invalid response format from server');
            }

            if (!data.success) {
                throw new Error(data.error || 'Server returned error');
            }

            if (!data.html) {
                throw new Error('No HTML content in response');
            }

            console.log('DashboardManager: Content validated successfully');

            // Fade transition effect
            await this.fadeOut(150);
            
            // Replace content with innerHTML
            this.mainContent.innerHTML = data.html;
            
            // Fade in
            await this.fadeIn(150);

            // Re-attach event listeners if needed
            this.setupAddressModal();
            
            console.log('DashboardManager: View loaded successfully');

        } catch (error) {
            console.error('DashboardManager: View Loading Error:', {
                view: this.currentView,
                tab: this.currentTab,
                error: error.message,
                stack: error.stack
            });
            
            this.mainContent.innerHTML = `
                <div class="empty-state" style="text-align: center; padding: 60px 40px; background: #fff9f7; border: 2px solid #fee2e2; border-radius: 12px;">
                    <i class="fas fa-exclamation-circle" style="font-size: 48px; color: #dc2626; margin-bottom: 20px; display: block;"></i>
                    <h3 style="color: #991b1b; margin-bottom: 8px; font-size: 18px; font-weight: 700;">Error Loading Content</h3>
                    <p style="color: #7f1d1d; font-size: 14px; margin: 0 0 15px 0;">${error.message}</p>
                    <button onclick="location.reload()" style="padding: 10px 25px; background: #dc2626; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 13px; transition: all 0.3s;">
                        <i class="fas fa-redo"></i> Reload Page
                    </button>
                </div>
            `;
        } finally {
            this.isLoading = false;
            this.mainContent.style.opacity = '1';
            this.mainContent.style.pointerEvents = 'auto';
        }
    }

    fadeOut(duration) {
        return new Promise(resolve => {
            this.mainContent.style.transition = `opacity ${duration}ms ease-out`;
            this.mainContent.style.opacity = '0';
            setTimeout(resolve, duration);
        });
    }

    fadeIn(duration) {
        return new Promise(resolve => {
            this.mainContent.style.opacity = '0';
            this.mainContent.style.transition = `opacity ${duration}ms ease-in`;
            setTimeout(() => {
                this.mainContent.style.opacity = '1';
                setTimeout(resolve, duration);
            }, 10);
        });
    }

    updateActiveNav() {
        // Update active state on sidebar
        document.querySelectorAll('.sidebar-menu-title').forEach(link => {
            link.classList.remove('active-nav');
            const href = link.getAttribute('href');
            const params = new URLSearchParams(href.substring(1));
            const view = params.get('view');
            
            if (view === this.currentView) {
                link.classList.add('active-nav');
            }
        });
    }

    openAddressModal(addressData = null) {
        // This will be called when modal is needed
        if (addressData) {
            console.log('Edit address:', addressData);
        } else {
            console.log('New address');
        }
        // Initialize modal from the dynamically loaded content
        const modal = document.getElementById('addressModal');
        if (modal) {
            modal.style.display = 'flex';
        }
    }

    deleteAddress(id) {
        if (confirm('Are you sure you want to delete this address?')) {
            const form = document.getElementById('actionForm');
            if (form) {
                form.action.value = 'delete_address';
                form.address_id.value = id;
                form.submit();
            }
        }
    }

    setDefaultAddress(id) {
        const form = document.getElementById('actionForm');
        if (form) {
            form.action.value = 'set_default';
            form.address_id.value = id;
            form.submit();
        }
    }

    setupAddressModal() {
        // Setup address modal if it exists in the loaded content
        const modal = document.getElementById('addressModal');
        if (modal) {
            window.onclick = function (event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            };
        }
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    window.dashboardManager = new DashboardManager();
});
