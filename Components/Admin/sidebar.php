<!-- Sidebar / Navigation -->
<aside id="sidebar" class="sidebar" style="background: #111827; border-right: 1px solid #1f2937;">
    <div class="logo-container" style="border-bottom: 1px solid #1f2937; padding: 2rem 1.5rem;">
        <div class="logo-flex" style="gap: 12px;">
            <div style="background: #2563eb; padding: 8px; border-radius: 12px;">
                <i data-lucide="layout-grid" style="color: white; width: 24px; height: 24px;"></i>
            </div>
            <span class="logo-text" style="color: white; font-weight: 800; font-size: 1.25rem;">IMARKET <span style="color: #60a5fa;">PH</span></span>
        </div>
    </div>

    <nav class="nav-menu" style="padding: 1.5rem 1rem;">
        <p style="font-size: 0.7rem; color: #4b5563; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 800; margin: 1.5rem 1rem 1rem 1rem;">Main Menu</p>
        
        <a href="#" class="nav-item active-nav" onclick="showModule('dashboard', this)">
            <i data-lucide="layout-dashboard"></i>
            Dashboard
        </a>
        <div class="group">
            <a href="#" class="nav-item" onclick="toggleSubMenu(this, 'product-submenu'); showSubModule('product', 'products');">
                <i data-lucide="package"></i>
                Product
                <i data-lucide="chevron-right" class="chevron-icon"></i>
            </a>
            <div id="product-submenu" class="submenu hidden">
                <a href="#" onclick="showSubModule('product', 'products'); event.preventDefault();">Product Inventory</a>
                <a href="#" onclick="showSubModule('product', 'sellers'); event.preventDefault();">Sellers & Stores</a>
                <a href="#" onclick="showSubModule('product', 'categories'); event.preventDefault();">Category Management</a>
            </div>
        </div>

        <div class="group">
            <a href="#" class="nav-item" onclick="toggleSubMenu(this, 'order-submenu'); showSubModule('order', 'orders');">
                <i data-lucide="shopping-cart"></i>
                Order & Checkout
                <i data-lucide="chevron-right" class="chevron-icon"></i>
            </a>
            <div id="order-submenu" class="submenu hidden">
                <a href="#" onclick="showSubModule('order', 'orders'); event.preventDefault();">View All Orders</a>
                <a href="#" onclick="showSubModule('order', 'payments'); event.preventDefault();">Transaction Logs</a>
            </div>
        </div>

        <div class="group">
            <a href="#" class="nav-item" onclick="toggleSubMenu(this, 'shipping-submenu'); showSubModule('shipping', 'addresses');">
                <i data-lucide="truck"></i>
                Shipping & Address
                <i data-lucide="chevron-right" class="chevron-icon"></i>
            </a>
            <div id="shipping-submenu" class="submenu hidden">
                <a href="#" onclick="showSubModule('shipping', 'addresses'); event.preventDefault();">Addresses & Validation</a>
                <a href="#" onclick="showSubModule('shipping', 'tracking'); event.preventDefault();">Shipment Tracking</a>
            </div>
        </div>

        <!-- Governance -->
        <div class="group">
            <a href="#" class="nav-item" onclick="toggleSubMenu(this, 'user-submenu'); showSubModule('user', 'profile');">
                <i data-lucide="users"></i>
                User & Roles
                <i data-lucide="chevron-right" class="chevron-icon"></i>
            </a>
            <div id="user-submenu" class="submenu hidden">
                <a href="#" onclick="showSubModule('user', 'profile'); event.preventDefault();">Admin Profile</a>
                <a href="#" onclick="showSubModule('user', 'admins'); event.preventDefault();">Admin Accounts</a>
                <a href="#" onclick="showSubModule('user', 'customers'); event.preventDefault();">Customer List</a>
            </div>
        </div>

        <!-- System & Support -->
        <p style="font-size: 0.7rem; color: #4b5563; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 800; margin: 2rem 1rem 1rem 1rem;">System</p>

        <a href="#" class="nav-item" onclick="showModule('notifications', this)">
            <i data-lucide="alert-triangle"></i>
            Notifications & Alerts
        </a>

        <div class="group">
            <a href="#" class="nav-item" id="support-nav-btn" onclick="toggleSubMenu(this, 'support-submenu'); showSubModule('support', 'chat');">
                <div style="display: flex; align-items: center; width: 100%; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <i data-lucide="message-circle"></i>
                        <span>Customer Support</span>
                    </div>
                    <span id="support-badge" class="support-notification-badge" style="display: none; background: #ef4444; color: white; font-size: 0.65rem; padding: 0.25rem 0.5rem; border-radius: 1rem; font-weight: 700; min-width: 20px; text-align: center;">0</span>
                </div>
                <i data-lucide="chevron-right" class="chevron-icon"></i>
            </a>
            <div id="support-submenu" class="submenu hidden">
                <a href="#" onclick="showSubModule('support', 'chat'); event.preventDefault();">
                    <i data-lucide="message-square" style="width: 0.9rem; height: 0.9rem; margin-right: 0.5rem;"></i>
                    Customer Chat
                </a>
                <a href="#" onclick="showSubModule('support', 'tickets'); event.preventDefault();">
                    <i data-lucide="ticket" style="width: 0.9rem; height: 0.9rem; margin-right: 0.5rem;"></i>
                    Support Tickets
                </a>
                <a href="../CustomerSupport/dashboard.php" style="display: flex; align-items: center; gap: 0.5rem;">
                    <i data-lucide="external-link" style="width: 0.9rem; height: 0.9rem;"></i>
                    Full Support Portal
                </a>
            </div>
        </div>

        <a href="#" class="nav-item" onclick="showModule('settings', this)" style="background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%); color: white; margin-top: 1rem; padding: 1rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(30, 58, 138, 0.45); transition: all 0.3s;"
            onmouseover="this.style.boxShadow='0 8px 20px rgba(30, 58, 138, 0.6)'; this.style.transform='translateY(-2px)'"
            onmouseout="this.style.boxShadow='0 4px 12px rgba(30, 58, 138, 0.45)'; this.style.transform='translateY(0)'">
            <i data-lucide="settings"></i>
            System Settings & Security
        </a>
        
        <style>
            .support-notification-badge {
                animation: pulse-badge 2s infinite;
            }
            @keyframes pulse-badge {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.15); }
            }
        </style>
    </nav>

    <div class="sidebar-footer" style="background: #0f172a; border-top: 1px solid #1f2937; padding: 1.5rem;">
        <p style="margin: 0; color: #64748b; font-weight: 600;">iMARKET Portal v1.0</p>
        <p style="margin: 0.5rem 0 0 0; color: #4b5563; font-size: 0.8rem;">Secure Transaction System</p>
    </div>
</aside>
