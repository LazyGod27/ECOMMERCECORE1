// --- UTILITY FUNCTIONS ---
function setPageTitle(title) {
    // Update the large title in the content area
    const contentHeader = document.querySelector('#content-container .page-header');
    if (contentHeader) {
        contentHeader.innerText = title;
    }
    // Update the invisible title used in the header bar for alignment
    document.getElementById('page-title').innerText = title;
}

function setActiveNav(element) {
    // Reset all nav items
    document.querySelectorAll('.nav-menu a').forEach(item => {
        item.classList.remove('active-nav');
        // Also reset rotation on chevron icons
        const icon = item.querySelector('.chevron-icon');
        if (icon) {
            icon.classList.remove('rotate-90');
        }
    });
    // Set the main element to active
    element.classList.add('active-nav');
}

function toggleSubMenu(element, submenuId) {
    const submenu = document.getElementById(submenuId);
    const icon = element.querySelector('.chevron-icon');

    if (submenu) submenu.classList.toggle('hidden');
    if (icon) icon.classList.toggle('rotate-90');

    if (typeof event !== 'undefined') {
        event.preventDefault();
    }
}

// Custom Modal Implementation (Replaces alert/confirm)
function showCustomActionModal(title, message, confirmText = 'OK', actionCallback = null) {
    const backdrop = document.getElementById('custom-modal-backdrop');
    const modalContainer = document.getElementById('modal-container');

    // Simple markup for bolding **text**
    const safeMessage = message
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/\*\*(.*?)\*\*/g, '<b>$1</b>');

    modalContainer.innerHTML = `
                <h3 id="modal-title" class="modal-title" style="font-size: 1.25rem; font-weight: 700; color: #1f2937;">${title}</h3>
                <p id="modal-message" class="modal-message" style="font-size: 1rem; color: var(--color-gray-500); margin-bottom: 1.5rem;">${safeMessage}</p>
                <div class="modal-actions" style="display: flex; justify-content: flex-end; column-gap: 0.75rem;">
                    <button id="modal-cancel" class="btn-base btn-secondary hidden">Cancel</button>
                    <button id="modal-confirm" class="btn-base btn-primary">${confirmText}</button>
                </div>
            `;

    const confirmBtn = document.getElementById('modal-confirm');
    const cancelBtn = document.getElementById('modal-cancel');

    if (actionCallback) {
        cancelBtn.classList.remove('hidden');
        cancelBtn.onclick = () => backdrop.classList.add('hidden');
        confirmBtn.onclick = () => {
            actionCallback();
            backdrop.classList.add('hidden');
        };
    } else {
        cancelBtn.classList.add('hidden');
        confirmBtn.onclick = () => backdrop.classList.add('hidden');
    }

    backdrop.classList.remove('hidden');
}

// --- MAIN CONTENT FUNCTIONS (MODULES - ENHANCED) ---

// Helper function for KPI Card HTML
function createKPICard(title, value, iconName, kpiClass) {
    return `
                <div class="kpi-card ${kpiClass}">
                    <div class="card-content" style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="flex: 1;">
                            <p class="card-title" style="font-size: 0.875rem; font-weight: 600; color: var(--color-gray-500); margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">${title}</p>
                            <h2 class="card-value" style="font-size: 2rem; font-weight: 800; line-height: 1.2; margin: 0;">${value}</h2>
                        </div>
                        <div class="card-icon" style="padding: 1rem; border-radius: 1rem; width: 4.5rem; height: 4.5rem; display: flex; align-items: center; justify-content: center; transition: transform 0.3s;">
                            <i data-lucide="${iconName}" style="width: 2rem; height: 2rem;"></i>
                        </div>
                    </div>
                </div>
            `;
}

// 4. Dashboard & Analytics
function renderDashboard() {
    setPageTitle('Intelligence Hub');
    const content = document.getElementById('content-container');

    // Calculate stats from transactions data
    const stats = {
        totalRevenue: transactionsData.reduce((sum, t) => sum + parseFloat(t.amount || 0), 0),
        pendingOrders: transactionsData.filter(t => t.status === 'Pending').length,
        completedToday: transactionsData.filter(t => t.status === 'Delivered' || t.status === 'Completed').length,
    };

    content.innerHTML = `
        <div style="animation: fadeIn 0.5s ease-out;">
            <!-- Premium Header Section -->
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 1.5rem; padding: 3rem; color: white; margin-bottom: 2.5rem; position: relative; overflow: hidden; box-shadow: 0 20px 60px rgba(102, 126, 234, 0.3);">
                <div style="position: absolute; top: -10%; right: -5%; width: 400px; height: 400px; background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);"></div>
                <div style="position: absolute; bottom: -20%; left: 10%; width: 300px; height: 300px; background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);"></div>
                
                <div style="position: relative; z-index: 2;">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 2rem;">
                        <div>
                            <h1 style="font-size: 2.8rem; font-weight: 900; margin: 0 0 0.5rem 0; letter-spacing: -0.02em;">Welcome back, ${adminConfig.username}! 👋</h1>
                            <p style="font-size: 1.125rem; opacity: 0.9; margin: 0; max-width: 600px;">Your marketplace is running smoothly. Monitor live transactions and manage operations below.</p>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 0.875rem; opacity: 0.8; margin-bottom: 0.5rem;">Last updated <span id="update-time">now</span></div>
                            <div style="display: flex; gap: 0.75rem; align-items: center; justify-content: flex-end;">
                                <div style="width: 12px; height: 12px; background: #4ade80; border-radius: 50%; animation: pulse 2s infinite;"></div>
                                <span style="font-weight: 600;">System Online</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                        <button class="btn-base" style="background: white; color: #667eea; padding: 0.875rem 1.75rem; font-weight: 700; border-radius: 0.75rem; border: none; cursor: pointer; transition: all 0.3s;" 
                            onclick="showSubModule('order', 'orders')"
                            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 25px rgba(0,0,0,0.2)'"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                            <i data-lucide="shopping-bag" style="display: inline; width: 1.1rem; height: 1.1rem; margin-right: 0.5rem; vertical-align: middle;"></i>
                            View All Orders
                        </button>
                        <button class="btn-base" style="background: rgba(255,255,255,0.15); color: white; border: 2px solid rgba(255,255,255,0.3); padding: 0.875rem 1.75rem; font-weight: 700; border-radius: 0.75rem; cursor: pointer; transition: all 0.3s;" 
                            onclick="showSubModule('product', 'products')"
                            onmouseover="this.style.background='rgba(255,255,255,0.2)'; this.style.transform='translateY(-2px)'"
                            onmouseout="this.style.background='rgba(255,255,255,0.15)'; this.style.transform='translateY(0)'">
                            <i data-lucide="package" style="display: inline; width: 1.1rem; height: 1.1rem; margin-right: 0.5rem; vertical-align: middle;"></i>
                            Manage Inventory
                        </button>
                        <button class="btn-base" style="background: rgba(255,255,255,0.15); color: white; border: 2px solid rgba(255,255,255,0.3); padding: 0.875rem 1.75rem; font-weight: 700; border-radius: 0.75rem; cursor: pointer; transition: all 0.3s;"
                            onclick="showSubModule('support', 'chat')"
                            onmouseover="this.style.background='rgba(255,255,255,0.2)'; this.style.transform='translateY(-2px)'"
                            onmouseout="this.style.background='rgba(255,255,255,0.15)'; this.style.transform='translateY(0)'">
                            <i data-lucide="message-circle" style="display: inline; width: 1.1rem; height: 1.1rem; margin-right: 0.5rem; vertical-align: middle;"></i>
                            Customer Support
                        </button>
                    </div>
                </div>
            </div>

            <!-- KPI Cards Grid -->
            <div class="kpi-card-grid">
                ${createKPICard('Total Revenue', formatCurrency(kpiData.totalRevenue || 0), 'dollar-sign', 'kpi-indigo')}
                ${createKPICard('Active Orders', kpiData.totalOrders || 0, 'shopping-bag', 'kpi-green')}
                ${createKPICard('Critical Stock', kpiData.lowStockCount || 0, 'alert-circle', 'kpi-red')}
                ${createKPICard('Total Customers', kpiData.newCustomers || 0, 'users', 'kpi-yellow')}
            </div>

            <!-- Live Transactions Section -->
            <div style="margin-top: 3rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 4px; height: 2rem; background: linear-gradient(180deg, #667eea, #764ba2); border-radius: 2px;"></div>
                        <h2 style="font-size: 1.75rem; font-weight: 800; color: #1f2937; margin: 0; display: flex; align-items: center; gap: 0.75rem;">
                            <i data-lucide="zap" style="color: #f59e0b; width: 1.5rem; height: 1.5rem;"></i>
                            Live Transactions
                        </h2>
                        <span style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 0.375rem 0.875rem; border-radius: 2rem; font-size: 0.8rem; font-weight: 700;">LIVE</span>
                    </div>
                    <button onclick="showSubModule('order', 'orders')" style="padding: 0.625rem 1.25rem; background: linear-gradient(135deg, #667eea, #764ba2); color: white; border: none; border-radius: 0.75rem; cursor: pointer; font-weight: 600; transition: all 0.3s; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);"
                        onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 25px rgba(102, 126, 234, 0.4)'"
                        onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(102, 126, 234, 0.3)'">
                        View All Transactions →
                    </button>
                </div>
                <div id="recent-transactions-container" class="kpi-card" style="padding: 0; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.08); border: 1px solid #f1f5f9;">
                    <div style="padding: 2rem; text-align: center; color: #9ca3af;">
                        <i data-lucide="loader" style="width: 2.5rem; height: 2.5rem; margin: 0 auto 1rem; opacity: 0.5; animation: spin 1s linear infinite;"></i>
                        <p style="margin: 0; font-size: 1rem;">Loading live transactions...</p>
                    </div>
                </div>
            </div>

            <!-- Transaction Stats Section -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-top: 2rem;">
                <div style="background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%); padding: 1.5rem; border-radius: 1rem; border: 1px solid #86efac;">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div>
                            <p style="color: #6b7280; font-size: 0.875rem; margin: 0 0 0.5rem 0; font-weight: 600;">COMPLETED TODAY</p>
                            <h3 style="color: #15803d; font-size: 2.5rem; font-weight: 800; margin: 0;">${stats.completedToday}</h3>
                        </div>
                        <div style="width: 3rem; height: 3rem; background: #dcfce7; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="check-circle" style="color: #15803d; width: 1.5rem; height: 1.5rem;"></i>
                        </div>
                    </div>
                </div>

                <div style="background: linear-gradient(135deg, #fef3c7 0%, #fef9e7 100%); padding: 1.5rem; border-radius: 1rem; border: 1px solid #fcd34d;">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div>
                            <p style="color: #6b7280; font-size: 0.875rem; margin: 0 0 0.5rem 0; font-weight: 600;">PENDING ORDERS</p>
                            <h3 style="color: #b45309; font-size: 2.5rem; font-weight: 800; margin: 0;">${stats.pendingOrders}</h3>
                        </div>
                        <div style="width: 3rem; height: 3rem; background: #fef08a; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="clock" style="color: #b45309; width: 1.5rem; height: 1.5rem;"></i>
                        </div>
                    </div>
                </div>

                <div style="background: linear-gradient(135deg, #dbeafe 0%, #f0f9ff 100%); padding: 1.5rem; border-radius: 1rem; border: 1px solid #93c5fd;">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div>
                            <p style="color: #6b7280; font-size: 0.875rem; margin: 0 0 0.5rem 0; font-weight: 600;">TODAY'S REVENUE</p>
                            <h3 style="color: #1e40af; font-size: 2.5rem; font-weight: 800; margin: 0; white-space: nowrap;">₱ ${Math.floor(Math.random() * 50000).toLocaleString()}</h3>
                        </div>
                        <div style="width: 3rem; height: 3rem; background: #bfdbfe; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="trending-up" style="color: #1e40af; width: 1.5rem; height: 1.5rem;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                @keyframes spin {
                    from { transform: rotate(0deg); }
                    to { transform: rotate(360deg); }
                }
                @keyframes pulse {
                    0%, 100% { opacity: 1; }
                    50% { opacity: 0.5; }
                }
                @keyframes fadeIn {
                    from { opacity: 0; transform: translateY(10px); }
                    to { opacity: 1; transform: translateY(0); }
                }
            </style>
        </div>
    `;
    lucide.createIcons();
    loadRecentTransactions();
    
    // Update time display
    updateTimeDisplay();
    setInterval(updateTimeDisplay, 10000);
}

function updateTimeDisplay() {
    const timeEl = document.getElementById('update-time');
    if (timeEl) {
        const now = new Date();
        timeEl.textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }
}

// --- MOCK SELLERS DATA ---
const sellersData = [
    { id: 1, name: "UrbanWear PH", category: "Streetwear & Casual Outfits", rating: "4.8", sold: "25k", initials: "UW", bg: "000000", status: "Active" },
    { id: 2, name: "StyleHub Manila", category: "Trendy Men & Women Fashion", rating: "4.9", sold: "18k", initials: "SH", bg: "d68910", status: "Active" },
    { id: 3, name: "DailyFits Co.", category: "Pang-daily na damit", rating: "4.7", sold: "30k", initials: "DF", bg: "27ae60", status: "Active" },
    { id: 4, name: "LuxeBasics", category: "Minimalist & Basic Wear", rating: "4.8", sold: "22k", initials: "LB", bg: "333333", status: "Active" },
    { id: 5, name: "TechZone PH", category: "Gadgets & Accessories", rating: "4.9", sold: "40k", initials: "TZ", bg: "2980b9", status: "Active" }
];

// 1. Product & Storefront Management
function renderProductModule(submodule, filterValue = null) {
    setPageTitle('Inventory & Seller Management');
    const content = document.getElementById('content-container');
    let moduleTitle = 'Inventory Management';
    let submoduleContent = '';

    const getProductStatusBadge = (stock) => {
        if (stock >= 50) return 'active';
        if (stock >= 10 && stock < 50) return 'low-stock';
        if (stock < 10) return 'critical-stock';
        return 'inactive';
    };

    // Assign mock stores to products if not present
    const enhancedProductsData = productsData.map((p, idx) => {
        if (!p.store_name) {
            p.store_name = sellersData[idx % sellersData.length].name;
        }
        return p;
    });

    const filteredProducts = filterValue
        ? enhancedProductsData.filter(p => p.store_name === filterValue)
        : enhancedProductsData;

    const productRows = filteredProducts.map((p, index) => {
        const statusClass = p.status === 'Active' ? 'active' : (p.status === 'Low Stock' ? 'low-stock' : (p.status === 'Critical Stock' ? 'critical-stock' : 'inactive'));
        return `

                <tr style="transition: all 0.2s; border-bottom: 1px solid #f3f4f6;" 
                    onmouseover="this.style.backgroundColor='#f9fafb'; this.style.transform='scale(1.01)';" 
                    onmouseout="this.style.backgroundColor='transparent'; this.style.transform='scale(1)';">
                    <td style="padding: 1rem 1.5rem; color: #6b7280; font-weight: 600; font-size: 0.875rem;">#${p.id}</td>
                    <td style="padding: 1rem 1.5rem;">
                        <div style="color: #1f2937; font-weight: 600; font-size: 0.9375rem; margin-bottom: 0.25rem;">${p.name}</div>
                        ${p.description ? `<div style="color: #6b7280; font-size: 0.8125rem; line-height: 1.4; max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${p.description.substring(0, 60)}${p.description.length > 60 ? '...' : ''}</div>` : ''}
                    </td>
                    <td style="padding: 1rem 1.5rem;">
                        <span style="color: #059669; font-weight: 700; font-size: 1rem;">${formatCurrency(parseFloat(p.price))}</span>
                    </td>
                    <td style="padding: 1rem 1.5rem;">
                        <span class="status-badge ${getProductStatusBadge(parseInt(p.stock))}" style="font-weight: 600; padding: 0.375rem 0.75rem; border-radius: 0.375rem; font-size: 0.8125rem;">${p.stock} units</span>
                    </td>
                    <td style="padding: 1rem 1.5rem;">
                        <span style="display: inline-flex; align-items: center; gap: 0.375rem; background: #e0e7ff; color: #4f46e5; padding: 0.375rem 0.75rem; border-radius: 0.375rem; font-size: 0.8125rem; font-weight: 500;">
                            <i data-lucide="tag" style="width: 0.75rem; height: 0.75rem;"></i>
                            ${p.category || 'N/A'}
                        </span>
                    </td>
                    <td style="padding: 1rem 1.5rem;">
                        <span class="status-badge ${statusClass}" style="font-weight: 600; padding: 0.375rem 0.75rem; border-radius: 0.375rem; font-size: 0.8125rem; text-transform: capitalize;">${p.status}</span>
                    </td>
                    <td style="padding: 1rem 1.5rem; width: 100px;">
                        <div style="display: flex; gap: 0.5rem; align-items: center; justify-content: center;">
                            <button class="btn-base" 
                                onclick="showProductDetails(${p.id})" 
                                title="View Details"
                                style="padding: 0.5rem 0.875rem; font-size: 0.8125rem; background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; border-radius: 0.5rem; font-weight: 500; display: flex; align-items: center; gap: 0.375rem; transition: all 0.2s; cursor: pointer;"
                                onmouseover="this.style.background='#dbeafe'; this.style.borderColor='#93c5fd'; this.style.transform='translateY(-1px)'"
                                onmouseout="this.style.background='#eff6ff'; this.style.borderColor='#bfdbfe'; this.style.transform='translateY(0)'">
                                <i data-lucide="eye" style="width: 0.875rem; height: 0.875rem;"></i>
                                <span>View</span>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
    }).join('');


    switch (submodule) {
        case 'products':
            moduleTitle = filterValue ? `Products from ${filterValue}` : 'Global Product Inventory';
            submoduleContent = `
                                    <div class="mb-6">
                                        <!-- Header Section -->
                                        <div style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); border-radius: 0.75rem; padding: 2rem; margin-bottom: 1.5rem; color: white;">
                                            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                                                <div>
                                                    <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem; color: white;">
                                                        ${filterValue ? `<i data-lucide="store" class="inline-block mr-2"></i> ${filterValue} Catalog` : 'Central Product Directory'}
                                                    </h2>
                                                    <p style="opacity: 0.9; font-size: 0.9375rem; margin: 0;">
                                                        ${filterValue ? `Viewing all products currently listed by ${filterValue}.` : 'Comprehensive list of all products across all connected sellers.'}
                                                    </p>
                                                </div>
                                                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                                                     ${filterValue ? `
                                                        <button onclick="renderProductModule('sellers')" style="background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.3); padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; color: white; cursor: pointer;">
                                                            Back to Sellers
                                                        </button>
                                                     ` : '<span style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem;">View-Only Administrator Mode</span>'}
                                                </div>
                                            </div>
                                        </div>
                            
                                        ${filteredProducts.length === 0 ? `
                            <div class="kpi-card p-8 mb-4" style="background: linear-gradient(135deg, #f0f9ff 0%, #e0e7ff 100%); border: 2px dashed #c7d2fe; text-align: center; border-radius: 1rem;">
                                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                                    <i data-lucide="package" style="width: 2.5rem; height: 2.5rem; color: white;"></i>
                                </div>
                                <h3 style="font-size: 1.5rem; font-weight: 700; color: #1f2937; margin-bottom: 0.75rem;">No Products Found</h3>
                                <p style="color: #6b7280; font-size: 1rem; max-width: 500px; margin-left: auto; margin-right: auto;">No products match the selected criteria.</p>
                            </div>
                            ` : ''}
                                    </div>

                        
                                    <!-- Products Table Card -->
                                    <div class="kpi-card" style="padding: 0; overflow: hidden; border-radius: 0.75rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);">
                                        <div style="background: #f9fafb; padding: 1.25rem 1.5rem; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                                            <div>
                                                <h3 style="font-size: 1.125rem; font-weight: 700; color: #1f2937; margin: 0;">Product Inventory</h3>
                                                <p style="font-size: 0.875rem; color: #6b7280; margin: 0.25rem 0 0 0;">${productsData.length} ${productsData.length === 1 ? 'product' : 'products'} in your inventory</p>
                                            </div>
                                            <div style="display: flex; align-items: center; gap: 0.5rem; background: white; padding: 0.5rem 0.75rem; border-radius: 0.5rem; border: 1px solid #e5e7eb;">
                                                <i data-lucide="package" style="width: 1rem; height: 1rem; color: #667eea;"></i>
                                                <span style="font-weight: 600; color: #1f2937; font-size: 0.875rem;">${productsData.length}</span>
                                            </div>
                                        </div>
                                        <div class="table-container" style="overflow-x: auto;">
                                            <table class="data-table" style="margin: 0;">
                                                <thead>
                                                    <tr style="background: #f9fafb;">
                                                        <th style="padding: 1rem 1.5rem; font-weight: 600; color: #374151; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid #e5e7eb;">ID</th>
                                                        <th style="padding: 1rem 1.5rem; font-weight: 600; color: #374151; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid #e5e7eb;">Product Name</th>
                                                        <th style="padding: 1rem 1.5rem; font-weight: 600; color: #374151; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid #e5e7eb;">Price</th>
                                                        <th style="padding: 1rem 1.5rem; font-weight: 600; color: #374151; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid #e5e7eb;">Stock</th>
                                                        <th style="padding: 1rem 1.5rem; font-weight: 600; color: #374151; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid #e5e7eb;">Category</th>
                                                        <th style="padding: 1rem 1.5rem; font-weight: 600; color: #374151; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid #e5e7eb;">Status</th>
                                                        <th style="padding: 1rem 1.5rem; font-weight: 600; color: #374151; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 2px solid #e5e7eb; text-align: center;">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    ${productsData.length > 0 ? productRows : `
                                    <td colspan="7" style="text-align: center; padding: 3rem; color: #9ca3af;">
                                                <i data-lucide="package-x" style="width: 3rem; height: 3rem; margin: 0 auto 1rem; display: block; opacity: 0.5;"></i>
                                                <p style="font-size: 1rem; font-weight: 500; margin-bottom: 0.5rem;">No products found</p>
                                                <p style="font-size: 0.875rem;">The catalog for this selection is empty.</p>
                                            </td>

                                        `}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                `;
            break;
        case 'sellers':
            moduleTitle = 'Marketplace Sellers';
            const sellerCards = sellersData.map(seller => `
                <div class="kpi-card" style="padding: 1.5rem; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 25px -5px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.25rem;">
                        <div style="width: 50px; height: 50px; border-radius: 12px; background: #${seller.bg}; color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.25rem; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                            ${seller.initials}
                        </div>
                        <div>
                            <h4 style="margin: 0; font-size: 1.1rem; color: #1e293b;">${seller.name}</h4>
                            <p style="margin: 0; font-size: 0.8rem; color: #64748b;">${seller.category}</p>
                        </div>
                        <div style="margin-left: auto;">
                            <span class="status-badge active" style="font-size: 0.7rem;">${seller.status}</span>
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem; background: #f8fafc; padding: 1rem; border-radius: 8px;">
                        <div style="text-align: center;">
                            <div style="font-size: 1.1rem; font-weight: 700; color: #1e293b;">${seller.rating}</div>
                            <div style="font-size: 0.7rem; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.05em;">Rating</div>
                        </div>
                        <div style="text-align: center; border-left: 1px solid #e2e8f0;">
                            <div style="font-size: 1.1rem; font-weight: 700; color: #1e293b;">${seller.sold}</div>
                            <div style="font-size: 0.7rem; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.05em;">Sold</div>
                        </div>
                    </div>
                    
                    <button onclick="showSubModule('product', 'products', '${seller.name}')" 
                        style="width: 100%; padding: 0.75rem; background: #eff6ff; color: #2563eb; border: 1px solid #dbeafe; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s;"
                        onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
                        View Products
                    </button>
                </div>
            `).join('');

            submoduleContent = `
                <div style="margin-bottom: 2rem;">
                    <h2 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem;">Store Marketplace</h2>
                    <p style="color: #64748b;">Manage and monitor registered sellers in the IMARKET hierarchy.</p>
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
                    ${sellerCards}
                </div>
            `;
            break;
        case 'categories':
            moduleTitle = 'Category Management';
            const categoryRows = categoriesData.map(cat => `
                                    <tr>
                                        <td>${cat.id}</td>
                                        <td style="color: #1f2937; font-weight: 500;">${cat.name}</td>
                                        <td>${cat.description || 'N/A'}</td>
                                        <td><span class="status-badge ${cat.status === 'Active' ? 'active' : 'inactive'}">${cat.status}</span></td>
                                        <td style="width: 100px;">
                                            <button class="btn-base" style="padding: 0.25rem 0.5rem; background-color: var(--color-light-grey);" 
                                                onclick="showCategoryDetails(${cat.id})">
                                                <i data-lucide="eye" style="width: 1rem; height: 1rem;"></i>
                                            </button>
                                        </td>
                                    </tr>
                                `).join('');

            submoduleContent = `
                                        <p class="text-gray-500">Categories are managed by the System Administrator.</p>
                                    <div class="kpi-card p-6">
                                        <h3 class="text-xl font-semibold mb-4">Category List (${categoriesData.length} Categories)</h3>
                                        <div class="table-container" style="overflow-x: auto;">
                                            <table class="data-table">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Name</th>
                                                        <th>Description</th>
                                                        <th>Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                     ${categoriesData.length > 0 ? categoryRows : `<tr><td colspan="5" class="text-center text-gray-500 py-4">No categories found in the system.</td></tr>`}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                `;
            break;
        case 'storefront':
            moduleTitle = 'Storefront Preview';
            const activeProducts = productsData.filter(p => p.status === 'Active').slice(0, 6);
            const productCards = activeProducts.map(p => `
                                    <div style="border: 1px solid #e5e7eb; border-radius: 0.75rem; overflow: hidden; background: white; transition: transform 0.2s, box-shadow 0.2s;" 
                                         onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 10px 15px -3px rgba(0,0,0,0.1)'"
                                         onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                        <div style="width: 100%; height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; font-weight: bold;">
                                            ${p.name.charAt(0)}
                                        </div>
                                        <div style="padding: 1rem;">
                                            <h4 style="font-weight: 600; color: #1f2937; margin-bottom: 0.5rem; font-size: 1rem;">${p.name}</h4>
                                            <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.75rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">${p.description || 'No description'}</p>
                                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                                <span style="font-size: 1.25rem; font-weight: 700; color: #1f2937;">${formatCurrency(parseFloat(p.price))}</span>
                                                <span class="status-badge ${p.stock < 10 ? 'low-stock' : 'active'}" style="font-size: 0.75rem;">Stock: ${p.stock}</span>
                                            </div>
                                        </div>
                                    </div>
                                `).join('');

            submoduleContent = `
                                    <p class="mb-6 text-gray-500">Preview how products appear to customers on the storefront.</p>
                                    <div class="kpi-card p-6">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                                            <h3 class="text-xl font-semibold">Storefront Preview</h3>
                                            <div style="display: flex; gap: 0.5rem;">
                                                <button class="btn-base btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                                                    <i data-lucide="monitor" style="width: 1rem; height: 1rem; margin-right: 0.5rem;"></i>
                                                    Desktop
                                                </button>
                                                <button class="btn-base btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                                                    <i data-lucide="smartphone" style="width: 1rem; height: 1rem; margin-right: 0.5rem;"></i>
                                                    Mobile
                                                </button>
                                            </div>
                                        </div>
                                        <div style="border: 2px solid #e5e7eb; border-radius: 0.75rem; overflow: hidden; background-color: #f9fafb; padding: 3rem; text-align: center;">
                                            <i data-lucide="layout" style="width: 3rem; height: 3rem; color: #94a3b8; margin: 0 auto 1rem; display: block;"></i>
                                            <h4 style="font-weight: 600; color: #1f2937; margin-bottom: 0.5rem;">Storefront Preview Information</h4>
                                            <p style="color: #6b7280; max-width: 400px; margin: 0 auto;">The live storefront layout is now optimized for better conversion. Click the preview buttons above to see the layout on different devices.</p>
                                        </div>
                                    </div>
                                `;
            break;
    }

    content.innerHTML = `<h2 class="page-header">${moduleTitle}</h2>${submoduleContent}`;
    lucide.createIcons();
}


// --- PRODUCT VIEW FUNCTIONS ---
function showProductDetails(productId) {
    const product = productsData.find(p => p.id == productId);
    if (!product) {
        showCustomActionModal('Error', 'Product not found.', 'OK');
        return;
    }

    const detailsHTML = `
        <div style="padding: 1.5rem;">
            <div style="display: flex; gap: 2rem; align-items: start; margin-bottom: 2rem;">
                <div style="width: 200px; height: 200px; background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); border-radius: 1rem; display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem; font-weight: 800; flex-shrink: 0; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);">
                    ${product.name.charAt(0)}
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; justify-content: space-between; align-items: start;">
                        <div>
                            <h3 style="font-size: 1.5rem; font-weight: 800; color: #1e293b; margin: 0 0 0.5rem 0;">${product.name}</h3>
                            <span class="status-badge ${product.status.toLowerCase().replace(' ', '-')}" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">${product.status}</span>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 1.5rem; font-weight: 800; color: #059669;">${formatCurrency(parseFloat(product.price))}</div>
                            <div style="font-size: 0.8125rem; color: #64748b; font-weight: 500;">Retail Price</div>
                        </div>
                    </div>
                    <p style="color: #4b5563; font-size: 0.9375rem; line-height: 1.6; margin: 1.5rem 0;">${product.description || 'No detailed description available for this item.'}</p>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; background: #f8fafc; padding: 1.5rem; border-radius: 1rem; border: 1px solid #f1f5f9; margin-bottom: 2rem;">
                <div>
                    <div style="font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 0.5rem;">Current Stock</div>
                    <div style="font-size: 1.1rem; font-weight: 700; color: #1e293b;">${product.stock} Units</div>
                </div>
                <div>
                    <div style="font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 0.5rem;">Category</div>
                    <div style="font-size: 1.1rem; font-weight: 700; color: #1e293b;">${product.category || 'Uncategorized'}</div>
                </div>
                <div>
                    <div style="font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 0.5rem;">Product ID</div>
                    <div style="font-size: 1.1rem; font-weight: 700; color: #1e293b;">#${product.id}</div>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 1rem;">
                <button onclick="document.getElementById('custom-modal-backdrop').classList.add('hidden')" class="btn-base btn-secondary" style="padding: 0.75rem 1.5rem; font-weight: 700;">Close Preview</button>
            </div>
        </div>
    `;

    const modalContainer = document.getElementById('modal-container');
    modalContainer.innerHTML = detailsHTML;
    document.getElementById('custom-modal-backdrop').classList.remove('hidden');
    lucide.createIcons();
}

// 2. Order & Checkout Management
function renderOrderModule(submodule) {
    setPageTitle('Order & Checkout Management');
    const content = document.getElementById('content-container');
    let moduleTitle = 'Order & Checkout Management';
    let submoduleContent = '';

    const getOrderStatusBadge = (status) => {
        // Ensure statuses match CSS classes
        return status.toLowerCase().replace(' ', '-');
    };

    const orderRows = ordersData.map((o, index) => `
                            <tr class="order-row" data-order-id="${o.id}" data-customer="${o.customer.toLowerCase()}" data-status="${o.status.toLowerCase()}" data-amount="${o.total}">
                                <td><input type="checkbox" class="order-checkbox" value="${o.id}" onchange="updateSelectedCount()"></td>
                                <td style="color: #1f2937; font-weight: 500;">#${o.id}</td>
                                <td>${o.customer}</td>
                                <td>${formatCurrency(o.total)}</td>
                                <td>${o.date || new Date().toLocaleDateString()}</td>
                                <td><span class="status-badge ${getOrderStatusBadge(o.status)}">${o.status}</span></td>
                                <td style="width: 200px;">
                                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                                        <button onclick="viewOrderDetails(${o.id})" class="btn-base btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.75rem;">
                                            <i data-lucide="eye" style="width: 0.875rem; height: 0.875rem; margin-right: 0.25rem;"></i>
                                            View
                                        </button>
                                        <select onchange="showCustomActionModal('Confirm Status Change', 'Change status for order **#${o.id}** to **' + this.value + '**?', 'Confirm', () => updateOrderStatus(${o.id}, this.value))" 
                                            class="form-group" style="padding: 0.35rem 0.5rem; font-size: 0.75rem; border-radius: 0.3rem; flex: 1;">
                                            <option value="${o.status}" selected disabled>${o.status}</option>
                                            <option value="Pending">Pending</option>
                                            <option value="Processing">Processing</option>
                                            <option value="Shipped">Shipped</option>
                                            <option value="Delivered">Delivered</option>
                                            <option value="Cancelled">Cancelled</option>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                        `).join('');

    switch (submodule) {
        case 'orders':
            moduleTitle = 'View All Orders & Status Management';
            submoduleContent = `
                                    <div class="mb-6">
                                        <p class="text-gray-500 mb-4">View all orders and manage their status. Search, filter, and export orders.</p>
                            
                                        <!-- Search and Filter Controls -->
                                        <div class="kpi-card p-4 mb-4" style="background: #f9fafb;">
                                            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 1rem; align-items: end;">
                                                <div>
                                                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Search Orders</label>
                                                    <input type="text" id="order-search" placeholder="Search by Order ID, Customer, or Amount..." 
                                                        style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;"
                                                        onkeyup="filterOrders()">
                                                </div>
                                                <div>
                                                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Filter by Status</label>
                                                    <select id="status-filter" onchange="filterOrders()" 
                                                        style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;">
                                                        <option value="">All Statuses</option>
                                                        <option value="Pending">Pending</option>
                                                        <option value="Processing">Processing</option>
                                                        <option value="Shipped">Shipped</option>
                                                        <option value="Delivered">Delivered</option>
                                                        <option value="Cancelled">Cancelled</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem;">Sort By</label>
                                                    <select id="sort-orders" onchange="filterOrders()" 
                                                        style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;">
                                                        <option value="id-desc">Newest First</option>
                                                        <option value="id-asc">Oldest First</option>
                                                        <option value="amount-desc">Highest Amount</option>
                                                        <option value="amount-asc">Lowest Amount</option>
                                                        <option value="customer-asc">Customer A-Z</option>
                                                    </select>
                                                </div>
                                                <div style="display: flex; gap: 0.5rem;">
                                                    <button onclick="exportOrders()" class="btn-base btn-secondary" style="padding: 0.75rem 1rem; font-size: 0.875rem; white-space: nowrap;">
                                                        <i data-lucide="download" style="width: 1rem; height: 1rem; margin-right: 0.5rem;"></i>
                                                        Export
                                                    </button>
                                                    <button onclick="printOrders()" class="btn-base btn-secondary" style="padding: 0.75rem 1rem; font-size: 0.875rem; white-space: nowrap;">
                                                        <i data-lucide="printer" style="width: 1rem; height: 1rem; margin-right: 0.5rem;"></i>
                                                        Print
                                                    </button>
                                                </div>
                                            </div>
                                
                                            <!-- Bulk Actions -->
                                            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb; display: flex; gap: 0.5rem; align-items: center;">
                                                <input type="checkbox" id="select-all-orders" onchange="toggleSelectAllOrders()" style="margin-right: 0.5rem;">
                                                <label for="select-all-orders" style="font-size: 0.875rem; color: #374151; margin-right: 1rem;">Select All</label>
                                                <select id="bulk-status-action" style="padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                                                    <option value="">Bulk Actions</option>
                                                    <option value="Processing">Mark as Processing</option>
                                                    <option value="Shipped">Mark as Shipped</option>
                                                    <option value="Delivered">Mark as Delivered</option>
                                                    <option value="Cancelled">Mark as Cancelled</option>
                                                </select>
                                                <button onclick="bulkUpdateOrderStatus()" class="btn-base btn-primary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                                                    Apply
                                                </button>
                                                <span id="selected-count" style="font-size: 0.875rem; color: #6b7280; margin-left: auto;"></span>
                                            </div>
                                        </div>
                                    </div>
                        
                                    <div class="kpi-card p-6">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                            <h3 class="text-xl font-semibold" id="order-count-title">Order List (<span id="filtered-order-count">${ordersData.length}</span> Orders Found)</h3>
                                        </div>
                                        <div class="table-container" style="overflow-x: auto;">
                                            <table class="data-table" id="orders-table">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 40px;"><input type="checkbox" id="table-select-all" onchange="toggleSelectAllOrders()"></th>
                                                        <th>Order ID</th>
                                                        <th>Customer</th>
                                                        <th>Total Amount</th>
                                                        <th>Date</th>
                                                        <th>Current Status</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="orders-table-body">
                                                    ${ordersData.length > 0 ? orderRows : `<tr><td colspan="7" class="text-center text-gray-500 py-4">No orders placed yet.</td></tr>`}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                `;
            break;
        case 'payments':
            moduleTitle = 'Payment Transaction Logs';
            const transactionRows = transactionsData.map(t => {
                const statusClass = t.status === 'Completed' ? 'active' : (t.status === 'Pending' ? 'pending' : (t.status === 'Failed' ? 'cancelled' : 'pending'));
                return `
                                    <tr>
                                        <td>${t.transaction_number}</td>
                                        <td>${t.order_number || 'N/A'}</td>
                                        <td>${t.customer_name || 'N/A'}</td>
                                        <td>${formatCurrency(parseFloat(t.amount))}</td>
                                        <td>${t.payment_method}</td>
                                        <td><span class="status-badge ${statusClass}">${t.status}</span></td>
                                        <td>${new Date(t.transaction_date).toLocaleDateString()}</td>
                                    </tr>
                                `;
            }).join('');

            submoduleContent = `
                                    <p class="mb-6 text-gray-500">Monitor all payment transactions and their status.</p>
                                    <div class="kpi-card p-6">
                                        <h3 class="text-xl font-semibold mb-4">Transaction Logs (${transactionsData.length} Transactions)</h3>
                                        <div class="table-container" style="overflow-x: auto;">
                                            <table class="data-table">
                                                <thead>
                                                    <tr>
                                                        <th>Transaction #</th>
                                                        <th>Order #</th>
                                                        <th>Customer</th>
                                                        <th>Amount</th>
                                                        <th>Payment Method</th>
                                                        <th>Status</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    ${transactionsData.length > 0 ? transactionRows : `<tr><td colspan="7" class="text-center text-gray-500 py-4">No transactions found.</td></tr>`}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                `;
            break;
    }

    content.innerHTML = `<h2 class="page-header">${moduleTitle}</h2>${submoduleContent}`;
    lucide.createIcons();

    // Store original orders data for filtering
    if (submodule === 'orders') {
        window.allOrdersData = ordersData;
        updateSelectedCount();
    }
}

// --- ORDER MANAGEMENT FUNCTIONS ---
function updateOrderStatus(orderId, newStatus) {
    // This submits a functional PHP POST form to update the database
    const updateForm = document.createElement('form');
    updateForm.method = 'POST';
    updateForm.action = 'index.php';
    updateForm.style.display = 'none';

    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = 'update_order_status';
    updateForm.appendChild(actionInput);

    const idInput = document.createElement('input');
    idInput.type = 'hidden';
    idInput.name = 'id';
    idInput.value = orderId;
    updateForm.appendChild(idInput);

    const statusInput = document.createElement('input');
    statusInput.type = 'hidden';
    statusInput.name = 'status';
    statusInput.value = newStatus;
    updateForm.appendChild(statusInput);

    document.body.appendChild(updateForm);
    updateForm.submit();
}

function filterOrders() {
    const searchTerm = document.getElementById('order-search')?.value.toLowerCase() || '';
    const statusFilter = document.getElementById('status-filter')?.value.toLowerCase() || '';
    const sortBy = document.getElementById('sort-orders')?.value || 'id-desc';
    const tbody = document.getElementById('orders-table-body');
    const countSpan = document.getElementById('filtered-order-count');

    if (!tbody) return;

    let filtered = window.allOrdersData || ordersData;

    // Apply search filter
    if (searchTerm) {
        filtered = filtered.filter(o =>
            o.id.toString().includes(searchTerm) ||
            o.customer.toLowerCase().includes(searchTerm) ||
            o.total.toString().includes(searchTerm)
        );
    }

    // Apply status filter
    if (statusFilter) {
        filtered = filtered.filter(o => o.status.toLowerCase() === statusFilter);
    }

    // Apply sorting
    filtered.sort((a, b) => {
        switch (sortBy) {
            case 'id-desc': return b.id - a.id;
            case 'id-asc': return a.id - b.id;
            case 'amount-desc': return b.total - a.total;
            case 'amount-asc': return a.total - b.total;
            case 'customer-asc': return a.customer.localeCompare(b.customer);
            default: return b.id - a.id;
        }
    });

    // Update table
    const getOrderStatusBadge = (status) => status.toLowerCase().replace(' ', '-');
    const rows = filtered.map(o => `
                            <tr class="order-row" data-order-id="${o.id}" data-customer="${o.customer.toLowerCase()}" data-status="${o.status.toLowerCase()}" data-amount="${o.total}">
                                <td><input type="checkbox" class="order-checkbox" value="${o.id}" onchange="updateSelectedCount()"></td>
                                <td style="color: #1f2937; font-weight: 500;">#${o.id}</td>
                                <td>${o.customer}</td>
                                <td>${formatCurrency(o.total)}</td>
                                <td>${o.date || new Date().toLocaleDateString()}</td>
                                <td><span class="status-badge ${getOrderStatusBadge(o.status)}">${o.status}</span></td>
                                <td style="width: 200px;">
                                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                                        <button onclick="viewOrderDetails(${o.id})" class="btn-base btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.75rem;">
                                            <i data-lucide="eye" style="width: 0.875rem; height: 0.875rem; margin-right: 0.25rem;"></i>
                                            View
                                        </button>
                                        <select onchange="showCustomActionModal('Confirm Status Change', 'Change status for order **#${o.id}** to **' + this.value + '**?', 'Confirm', () => updateOrderStatus(${o.id}, this.value))" 
                                            class="form-group" style="padding: 0.35rem 0.5rem; font-size: 0.75rem; border-radius: 0.3rem; flex: 1;">
                                            <option value="${o.status}" selected disabled>${o.status}</option>
                                            <option value="Pending">Pending</option>
                                            <option value="Processing">Processing</option>
                                            <option value="Shipped">Shipped</option>
                                            <option value="Delivered">Delivered</option>
                                            <option value="Cancelled">Cancelled</option>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                        `).join('');

    tbody.innerHTML = filtered.length > 0 ? rows : `<tr><td colspan="7" class="text-center text-gray-500 py-4">No orders found matching your criteria.</td></tr>`;
    if (countSpan) countSpan.textContent = filtered.length;

    lucide.createIcons();
    updateSelectedCount();
}

function viewOrderDetails(orderId) {
    const order = (window.allOrdersData || ordersData).find(o => o.id == orderId);
    if (!order) {
        showCustomActionModal('Error', 'Order not found.', 'OK');
        return;
    }

    const detailsHTML = `
                            <div style="padding: 1.5rem;">
                                <h3 style="font-size: 1.25rem; font-weight: 700; color: #1f2937; margin-bottom: 1.5rem;">Order Details #${order.id}</h3>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                                    <div style="grid-column: span 2;">
                                        <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.25rem;">Shipment Tracking Number (OTP)</p>
                                        <p style="font-weight: 700; color: #4f46e5; font-size: 1.1rem; letter-spacing: 0.05em; background: #eef2ff; padding: 0.5rem; border-radius: 0.375rem; display: inline-block; border: 1px dashed #6366f1;">${order.tracking_number || 'Generating...'}</p>
                                    </div>
                                    <div>
                                        <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.25rem;">Customer</p>
                                        <p style="font-weight: 600; color: #1f2937;">${order.customer}</p>
                                    </div>
                                    <div>
                                        <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.25rem;">Order Date</p>
                                        <p style="font-weight: 600; color: #1f2937;">${order.date || new Date().toLocaleDateString()}</p>
                                    </div>
                                    <div>
                                        <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.25rem;">Status</p>
                                        <p><span class="status-badge ${order.status.toLowerCase().replace(' ', '-')}">${order.status}</span></p>
                                    </div>
                                    <div>
                                        <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.25rem;">Total Amount</p>
                                        <p style="font-weight: 700; color: #059669; font-size: 1.125rem;">${formatCurrency(order.total)}</p>
                                    </div>
                                </div>
                                <div style="border-top: 1px solid #e5e7eb; padding-top: 1rem; margin-top: 1rem;">
                                    <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;">Order Items</p>
                                    <p style="color: #1f2937;">Product details and items will be displayed here.</p>
                                </div>
                                <div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
                                    <button onclick="document.getElementById('custom-modal-backdrop').classList.add('hidden')" class="btn-base btn-secondary" style="padding: 0.5rem 1.5rem;">Close</button>
                                </div>
                            </div>
                        `;

    const modalContainer = document.getElementById('modal-container');
    modalContainer.innerHTML = detailsHTML;
    document.getElementById('custom-modal-backdrop').classList.remove('hidden');
    lucide.createIcons();
}

function exportOrders() {
    const searchTerm = document.getElementById('order-search')?.value.toLowerCase() || '';
    const statusFilter = document.getElementById('status-filter')?.value.toLowerCase() || '';
    let filtered = window.allOrdersData || ordersData;

    if (searchTerm) {
        filtered = filtered.filter(o =>
            o.id.toString().includes(searchTerm) ||
            o.customer.toLowerCase().includes(searchTerm) ||
            o.total.toString().includes(searchTerm)
        );
    }
    if (statusFilter) {
        filtered = filtered.filter(o => o.status.toLowerCase() === statusFilter);
    }

    // Create CSV content
    const headers = ['Order ID', 'Customer', 'Total Amount', 'Status', 'Date'];
    const rows = filtered.map(o => [
        o.id,
        o.customer,
        o.total,
        o.status,
        o.date || new Date().toLocaleDateString()
    ]);

    const csvContent = [
        headers.join(','),
        ...rows.map(row => row.map(cell => `"${cell}"`).join(','))
    ].join('\\n');

    // Download CSV
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `orders_export_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function printOrders() {
    const printWindow = window.open('', '_blank');
    const searchTerm = document.getElementById('order-search')?.value.toLowerCase() || '';
    const statusFilter = document.getElementById('status-filter')?.value.toLowerCase() || '';
    let filtered = window.allOrdersData || ordersData;

    if (searchTerm) {
        filtered = filtered.filter(o =>
            o.id.toString().includes(searchTerm) ||
            o.customer.toLowerCase().includes(searchTerm) ||
            o.total.toString().includes(searchTerm)
        );
    }
    if (statusFilter) {
        filtered = filtered.filter(o => o.status.toLowerCase() === statusFilter);
    }

    const printContent = `
                            <!DOCTYPE html>
                            <html>
                            <head>
                                <title>Orders Report</title>
                                <style>
                                    body { font-family: Arial, sans-serif; padding: 20px; }
                                    h1 { color: #1f2937; }
                                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                                    th { background-color: #f3f4f6; font-weight: 600; }
                                </style>
                            </head>
                            <body>
                                <h1>Orders Report</h1>
                                <p>Generated: ${new Date().toLocaleString()}</p>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Total Amount</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${filtered.map(o => `
                                            <tr>
                                                <td>#${o.id}</td>
                                                <td>${o.customer}</td>
                                                <td>${formatCurrency(o.total)}</td>
                                                <td>${o.status}</td>
                                                <td>${o.date || new Date().toLocaleDateString()}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </body>
                            </html>
                        `;

    printWindow.document.write(printContent);
    printWindow.document.close();
    printWindow.print();
}

function toggleSelectAllOrders() {
    const selectAll = document.getElementById('select-all-orders') || document.getElementById('table-select-all');
    const checkboxes = document.querySelectorAll('.order-checkbox');
    checkboxes.forEach(cb => cb.checked = selectAll?.checked || false);
    updateSelectedCount();
}

function updateSelectedCount() {
    const checked = document.querySelectorAll('.order-checkbox:checked');
    const countSpan = document.getElementById('selected-count');
    if (countSpan) {
        countSpan.textContent = checked.length > 0 ? `${checked.length} selected` : '';
    }
}

function bulkUpdateOrderStatus() {
    const selected = Array.from(document.querySelectorAll('.order-checkbox:checked')).map(cb => cb.value);
    const newStatus = document.getElementById('bulk-status-action')?.value;

    if (selected.length === 0) {
        showCustomActionModal('No Selection', 'Please select at least one order.', 'OK');
        return;
    }

    if (!newStatus) {
        showCustomActionModal('No Action', 'Please select a status to apply.', 'OK');
        return;
    }

    showCustomActionModal(
        'Confirm Bulk Update',
        `Update ${selected.length} order(s) to status "${newStatus}"?`,
        'Confirm',
        () => {
            selected.forEach(orderId => {
                updateOrderStatus(parseInt(orderId), newStatus);
            });
        }
    );
}

// 3. Shipping & Address Management
function renderShippingModule(submodule) {
    setPageTitle('Shipping & Address Management');
    const content = document.getElementById('content-container');
    let moduleTitle = 'Shipping & Address Management';
    let submoduleContent = '';

    switch (submodule) {
        case 'addresses':
            moduleTitle = 'Customer Addresses & Validation';
            const addressRows = mockAddresses.map(a => `
                <tr>
                    <td>${a.id}</td>
                    <td>${a.customer}</td>
                    <td>${a.address}</td>
                    <td>${a.phone || 'N/A'}</td>
                    <td><span class="status-badge ${a.status === 'Verified' ? 'active' : 'pending'}">${a.status}</span></td>
                     <td style="display: flex; gap: 0.5rem;">
                        <button class="btn-base" style="padding: 0.25rem 0.5rem; background-color: var(--color-indigo-600); color: white;" 
                            onclick="showCustomActionModal('View Address', 'Customer: ${a.customer}<br>Address: ${a.address}<br>Phone: ${a.phone || 'N/A'}', 'Close')">
                            View
                        </button>
                        <button class="btn-base" style="padding: 0.25rem 0.5rem; background-color: var(--color-blue-600); color: white;" 
                            onclick="showCustomActionModal('Edit Address', 'Update address for **${a.customer}**?', 'Update')">
                            Update
                        </button>
                    </td>
                </tr>
            `).join('');

            submoduleContent = `
                <p class="mb-6 text-gray-500">View and validate customer shipping addresses. Data pulled from the <strong>users</strong> table.</p>
                <div class="kpi-card p-6">
                    <h3 class="text-xl font-semibold mb-4">Customer Addresses List (${mockAddresses.length} Addresses)</h3>
                    <div class="table-container" style="overflow-x: auto;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Full Address</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${mockAddresses.length > 0 ? addressRows : `<tr><td colspan="6" class="text-center text-gray-500 py-4">No addresses found in users table.</td></tr>`}
                            </tbody>
                        </table>
                    </div>
                </div>
            `;
            break;
        case 'tracking':
            moduleTitle = 'Shipment Tracking';
            const shipmentRows = shipmentsData.map(s => {
                const statusClass = s.status === 'Delivered' ? 'delivered' : (s.status === 'Out for Delivery' ? 'out-for-delivery' : (s.status === 'In Transit' ? 'in-transit' : 'processing'));
                return `
                                    <tr>
                                        <td>${s.tracking_number}</td>
                                        <td>${s.order_number || 'N/A'}</td>
                                        <td>${s.customer_name || 'N/A'}</td>
                                        <td>${s.courier || 'N/A'}</td>
                                        <td><span class="status-badge ${statusClass}">${s.status}</span></td>
                                        <td>${s.current_location || 'N/A'}</td>
                                        <td>${s.estimated_delivery ? new Date(s.estimated_delivery).toLocaleDateString() : 'N/A'}</td>
                                    </tr>
                                `;
            }).join('');

            submoduleContent = `
                                    <p class="mb-6 text-gray-500">Track shipments and monitor real-time delivery status.</p>
                                    <div class="kpi-card p-6">
                                        <h3 class="text-xl font-semibold mb-4">Shipment Tracking (${shipmentsData.length} Shipments)</h3>
                                        <div class="table-container" style="overflow-x: auto;">
                                            <table class="data-table">
                                                <thead>
                                                    <tr>
                                                        <th>Tracking #</th>
                                                        <th>Order #</th>
                                                        <th>Customer</th>
                                                        <th>Courier</th>
                                                        <th>Status</th>
                                                        <th>Current Location</th>
                                                        <th>Est. Delivery</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    ${shipmentsData.length > 0 ? shipmentRows : `<tr><td colspan="7" class="text-center text-gray-500 py-4">No shipments found.</td></tr>`}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                `;
            break;
    }

    content.innerHTML = `<h2 class="page-header">${moduleTitle}</h2>${submoduleContent}`;
    lucide.createIcons();
}

// 5. User & Role Management
function renderUserModule(submodule) {
    setPageTitle('User & Role Management');
    const content = document.getElementById('content-container');
    let moduleTitle = 'User & Role Management';
    let submoduleContent = '';

    const currentUsername = adminDetails.username || adminConfig.username;
    const currentRole = adminDetails.role || adminConfig.role;
    const currentFullName = adminDetails.full_name || '';
    const currentEmail = adminDetails.email || '';
    const currentPhoneNumber = adminDetails.phone_number || '';
    const profileImageUrl = adminDetails.profile_image_url || '';

    switch (submodule) {
        case 'profile':
            moduleTitle = 'Admin Profile (Functional Update)';
            const accountCreatedAt = adminDetails.created_at ? new Date(adminDetails.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : 'N/A';
            const lastUpdated = adminDetails.updated_at ? new Date(adminDetails.updated_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : 'N/A';
            const avatarFallback = `https://placehold.co/140x140/4bc5ec/FFFFFF?text=${currentUsername.charAt(0).toUpperCase() || 'A'}`;
            const avatarSrc = profileImageUrl || avatarFallback;

            submoduleContent = `
                                    <div class="module-container" style="grid-template-columns: 1fr; gap: 1.5rem;">
                                        <!-- Profile Header Card -->
                                        <div class="kpi-card" style="background: linear-gradient(135deg, var(--color-primary-dark) 0%, var(--color-dark-grey) 100%); color: white; padding: 2.5rem;">
                                            <div style="display: flex; gap: 2rem; align-items: flex-start; flex-wrap: wrap;">
                                                <div style="position: relative; flex-shrink: 0;">
                                                    <img id="profile-avatar" 
                                                        src="${avatarSrc}" 
                                                        alt="Admin Avatar"
                                                        style="width: 140px; height: 140px; border-radius: 50%; border: 4px solid rgba(255,255,255,0.3); object-fit: cover; box-shadow: 0 8px 16px rgba(0,0,0,0.2);">
                                                    <button type="button" 
                                                        onclick="triggerProfileImagePicker()"
                                                        title="Upload new picture"
                                                        style="position: absolute; bottom: 10px; right: 10px; background: var(--color-white); color: var(--color-primary-dark); border: none; border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 4px 8px rgba(0,0,0,0.2); transition: transform 0.2s;"
                                                        onmouseover="this.style.transform='scale(1.1)'"
                                                        onmouseout="this.style.transform='scale(1)'">
                                                        <i data-lucide="camera" style="width: 18px; height: 18px;"></i>
                                                    </button>
                                                </div>
                                                <div style="flex: 1; min-width: 250px;">
                                                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.75rem;">
                                                        <h2 style="font-size: 2rem; font-weight: 800; margin: 0; color: white;">${currentFullName || currentUsername}</h2>
                                                        <span style="background: rgba(255,255,255,0.2); padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
                                                            ${currentRole}
                                                        </span>
                                                    </div>
                                                    <p style="font-size: 1.125rem; margin: 0 0 0.5rem 0; color: rgba(255,255,255,0.9); font-weight: 500;">@${currentUsername}</p>
                                                    ${currentEmail ? `<p style="font-size: 0.9375rem; margin: 0.25rem 0; color: rgba(255,255,255,0.8); display: flex; align-items: center; gap: 0.5rem;">
                                                        <i data-lucide="mail" style="width: 16px; height: 16px;"></i> ${currentEmail}
                                                    </p>` : ''}
                                                    ${currentPhoneNumber ? `<p style="font-size: 0.9375rem; margin: 0.25rem 0; color: rgba(255,255,255,0.8); display: flex; align-items: center; gap: 0.5rem;">
                                                        <i data-lucide="phone" style="width: 16px; height: 16px;"></i> ${currentPhoneNumber}
                                                    </p>` : ''}
                                                </div>
                                            </div>
                                            <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.2); display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1.5rem;">
                                                <div>
                                                    <p style="font-size: 0.75rem; color: rgba(255,255,255,0.7); margin: 0 0 0.25rem 0; text-transform: uppercase; letter-spacing: 0.05em;">Member Since</p>
                                                    <p style="font-size: 0.9375rem; color: white; margin: 0; font-weight: 600;">${accountCreatedAt}</p>
                                                </div>
                                                <div>
                                                    <p style="font-size: 0.75rem; color: rgba(255,255,255,0.7); margin: 0 0 0.25rem 0; text-transform: uppercase; letter-spacing: 0.05em;">Last Updated</p>
                                                    <p style="font-size: 0.9375rem; color: white; margin: 0; font-weight: 600;">${lastUpdated}</p>
                                                </div>
                                                <div>
                                                    <p style="font-size: 0.75rem; color: rgba(255,255,255,0.7); margin: 0 0 0.25rem 0; text-transform: uppercase; letter-spacing: 0.05em;">Status</p>
                                                    <p style="font-size: 0.9375rem; color: #10b981; margin: 0; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                                                        <i data-lucide="check-circle" style="width: 16px; height: 16px;"></i> Active
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Profile Edit Form Card -->
                                        <div class="kpi-card p-6">
                                            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 2px solid var(--color-gray-100);">
                                                <div style="background: var(--color-indigo-600); padding: 0.75rem; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                                                    <i data-lucide="user-cog" style="width: 24px; height: 24px; color: white;"></i>
                                                </div>
                                                <div>
                                                    <h3 style="font-size: 1.5rem; font-weight: 700; color: #1f2937; margin: 0;">Edit Profile Information</h3>
                                                    <p style="font-size: 0.875rem; color: var(--color-gray-500); margin: 0.25rem 0 0 0;">Update your account details and preferences</p>
                                                </div>
                                            </div>

                                            <form id="profile-edit-form" method="POST" action="dashboard.php" enctype="multipart/form-data" onsubmit="return validateProfileForm(event);">
                                                <input type="hidden" name="action" value="update_profile">
                                                <input type="hidden" name="module" value="user">
                                                <input type="hidden" name="submodule" value="profile">
                                                <input type="file" id="profile_image" name="profile_image" accept="image/png,image/jpeg,image/jpg,image/webp" style="display: none;" onchange="handleProfileImageChange(event)">

                                                <!-- Personal Information Section -->
                                                <div style="margin-bottom: 2.5rem;">
                                                    <h4 style="font-size: 1.125rem; font-weight: 600; color: #1f2937; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                                                        <i data-lucide="user" style="width: 20px; height: 20px; color: var(--color-indigo-600);"></i>
                                                        Personal Information
                                                    </h4>
                                                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                                                        <div class="form-group">
                                                            <label for="full_name" style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                                                <i data-lucide="user-circle" style="width: 16px; height: 16px; color: var(--color-gray-500);"></i>
                                                                Full Name
                                                            </label>
                                                            <input type="text" id="full_name" name="full_name" value="${currentFullName}" placeholder="Enter your full name" style="padding: 0.875rem 1rem; border: 1px solid var(--color-gray-300); border-radius: 0.5rem; font-size: 0.9375rem; transition: all 0.2s; width: 100%;">
                                                        </div>
                                            
                                                        <div class="form-group">
                                                            <label for="new_username" style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                                                <i data-lucide="at-sign" style="width: 16px; height: 16px; color: var(--color-gray-500);"></i>
                                                                Username
                                                            </label>
                                                            <input type="text" id="new_username" name="new_username" value="${currentUsername}" required 
                                                                style="padding: 0.875rem 1rem; border: 1px solid var(--color-gray-300); border-radius: 0.5rem; font-size: 0.9375rem; transition: all 0.2s; width: 100%;">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Contact Information Section -->
                                                <div style="margin-bottom: 2.5rem;">
                                                    <h4 style="font-size: 1.125rem; font-weight: 600; color: #1f2937; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                                                        <i data-lucide="phone" style="width: 20px; height: 20px; color: var(--color-indigo-600);"></i>
                                                        Contact Information
                                                    </h4>
                                                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                                                        <div class="form-group">
                                                            <label for="email" style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                                                <i data-lucide="mail" style="width: 16px; height: 16px; color: var(--color-gray-500);"></i>
                                                                Email Address <span style="color: var(--color-red-600);">*</span>
                                                            </label>
                                                            <input type="email" id="email" name="email" value="${currentEmail}" placeholder="Enter your email address" required 
                                                                style="padding: 0.875rem 1rem; border: 1px solid var(--color-gray-300); border-radius: 0.5rem; font-size: 0.9375rem; transition: all 0.2s; width: 100%;">
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="phone_number" style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                                                <i data-lucide="phone-call" style="width: 16px; height: 16px; color: var(--color-gray-500);"></i>
                                                                Mobile Number <span style="color: var(--color-gray-500); font-size: 0.75rem;">(Optional)</span>
                                                            </label>
                                                            <input type="tel" id="phone_number" name="phone_number" value="${currentPhoneNumber}" placeholder="Enter your mobile number" pattern="[0-9]*"
                                                                style="padding: 0.875rem 1rem; border: 1px solid var(--color-gray-300); border-radius: 0.5rem; font-size: 0.9375rem; transition: all 0.2s; width: 100%;">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Security Section -->
                                                <div style="margin-bottom: 2.5rem;">
                                                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px solid var(--color-gray-100);">
                                                        <div style="background: #fee2e2; padding: 0.75rem; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                                                            <i data-lucide="shield" style="width: 20px; height: 20px; color: var(--color-red-600);"></i>
                                                        </div>
                                                        <div>
                                                            <h4 style="font-size: 1.125rem; font-weight: 600; color: #1f2937; margin: 0;">Password & Security</h4>
                                                            <p style="font-size: 0.875rem; color: var(--color-gray-500); margin: 0.25rem 0 0 0;">Change your password to keep your account secure</p>
                                                        </div>
                                                    </div>
                                        
                                                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                                                        <div class="form-group">
                                                            <label for="current_password" style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                                                <i data-lucide="lock" style="width: 16px; height: 16px; color: var(--color-gray-500);"></i>
                                                                Current Password <span style="color: var(--color-red-600);">*</span>
                                                            </label>
                                                            <input type="password" id="current_password" name="current_password" placeholder="Enter current password" autocomplete="off" required 
                                                                style="padding: 0.875rem 1rem; border: 1px solid var(--color-gray-300); border-radius: 0.5rem; font-size: 0.9375rem; transition: all 0.2s; width: 100%;">
                                                            <p style="font-size: 0.75rem; color: var(--color-gray-500); margin: 0.5rem 0 0 0;">Required to save any changes</p>
                                                        </div>
                                                    </div>

                                                    <div style="margin-top: 1.5rem; padding: 1.5rem; background: var(--color-gray-100); border-radius: 0.5rem; border-left: 4px solid var(--color-indigo-600);">
                                                        <p style="font-size: 0.875rem; font-weight: 600; color: #1f2937; margin: 0 0 0.75rem 0;">Change Password (Optional)</p>
                                                        <p style="font-size: 0.8125rem; color: var(--color-gray-500); margin: 0 0 1rem 0;">Leave blank if you don't want to change your password</p>
                                            
                                                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                                                            <div class="form-group" style="margin-bottom: 0;">
                                                                <label for="new_password" style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                                                    <i data-lucide="key" style="width: 16px; height: 16px; color: var(--color-gray-500);"></i>
                                                                    New Password
                                                                </label>
                                                                <input type="password" id="new_password" name="new_password" placeholder="Enter new password (min 6 characters)" autocomplete="new-password"
                                                                    style="padding: 0.875rem 1rem; border: 1px solid var(--color-gray-300); border-radius: 0.5rem; font-size: 0.9375rem; transition: all 0.2s; width: 100%;">
                                                            </div>
                                                
                                                            <div class="form-group" style="margin-bottom: 0;">
                                                                <label for="confirm_password" style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                                                    <i data-lucide="key-round" style="width: 16px; height: 16px; color: var(--color-gray-500);"></i>
                                                                    Confirm New Password
                                                                </label>
                                                                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" autocomplete="new-password"
                                                                    style="padding: 0.875rem 1rem; border: 1px solid var(--color-gray-300); border-radius: 0.5rem; font-size: 0.9375rem; transition: all 0.2s; width: 100%;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                    
                                                <!-- Action Buttons -->
                                                <div style="display: flex; gap: 1rem; padding-top: 2rem; border-top: 2px solid var(--color-gray-100); margin-top: 1rem;">
                                                    <button type="submit" class="btn-base btn-primary" style="padding: 0.875rem 2rem; font-size: 1rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                                                        <i data-lucide="save" style="width: 20px; height: 20px;"></i>
                                                        Save Changes
                                                    </button>
                                                    <button type="button" class="btn-base btn-secondary" onclick="window.location.reload()" style="padding: 0.875rem 2rem; font-size: 1rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                                                        <i data-lucide="x" style="width: 20px; height: 20px;"></i>
                                                        Cancel
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                `;
            break;
        case 'admins':
            moduleTitle = 'Admin Accounts & Role-Based Access Control (RBAC)';
            const adminRows = adminUsersData.map(a => `
                                    <tr>
                                        <td>${a.username}</td>
                                        <td>${a.full_name || 'N/A'}</td>
                                        <td>${a.email || 'N/A'}</td>
                                        <td>${a.role}</td>
                                        <td>${new Date(a.created_at).toLocaleDateString()}</td>
                                        <td style="width: 140px;">
                                            <div style="display: flex; gap: 0.5rem; align-items: center;">
                                                <button class="btn-base" 
                                                    onclick="viewUserDetails(${a.id}, 'admin')" 
                                                    style="padding: 0.4rem 0.75rem; font-size: 0.75rem; background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; border-radius: 6px; font-weight: 700; cursor: pointer;">
                                                    <i data-lucide="eye" style="width: 0.8rem; height: 0.8rem; margin-right: 0.25rem;"></i> View
                                                </button>
                                                ${a.id != adminConfig.currentAdminId ? `
                                                <button class="btn-base" style="padding: 0.4rem 0.5rem; background-color: #fef2f2; color: #dc2626; border: 1px solid #fee2e2; border-radius: 6px; cursor: pointer;"
                                                    onclick="deleteAdmin(${a.id}, '${a.username.replace(/'/g, "\\'")}')">
                                                    <i data-lucide="trash-2" style="width: 0.8rem; height: 0.8rem;"></i>
                                                </button>
                                                ` : ''}
                                            </div>
                                        </td>
                                    </tr>
                `).join('');

            submoduleContent = `
                <p class="mb-6 text-gray-500">Manage admin accounts and role-based access control.</p>
                    <div class="kpi-card p-6">
                        <h3 class="text-xl font-semibold mb-4">admin/Staff List (${adminUsersData.length} Admins)</h3>
                        <div class="table-container" style="overflow-x: auto;">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${adminUsersData.length > 0 ? adminRows : `<tr><td colspan="6" class="text-center text-gray-500 py-4">No admin users found.</td></tr>`}
                                </tbody>
                            </table>
                        </div>
                    </div>
            `;
            break;
        case 'customers':
            moduleTitle = 'Customer List';
            const customerRows = customersData.map(c => {
                const statusClass = c.status === 'Active' ? 'active' : (c.status === 'Banned' ? 'cancelled' : 'inactive');
                return `
                <tr>
                                        <td>${c.full_name}</td>
                                        <td>${c.email}</td>
                                        <td>${c.phone_number || 'N/A'}</td>
                                        <td>${c.total_orders || 0}</td>
                                        <td>${c.total_spent ? formatCurrency(parseFloat(c.total_spent)) : '₱0.00'}</td>
                                        <td><span class="status-badge ${statusClass}">${c.status}</span></td>
                                        <td>${new Date(c.created_at).toLocaleDateString()}</td>
                                        <td style="width: 100px;">
                                            <button class="btn-base" 
                                                onclick="viewUserDetails(${c.id}, 'customer')" 
                                                style="padding: 0.4rem 0.75rem; font-size: 0.75rem; background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; border-radius: 6px; font-weight: 700; cursor: pointer;">
                                                <i data-lucide="eye" style="width: 0.8rem; height: 0.8rem; margin-right: 0.25rem;"></i> View
                                            </button>
                                        </td>
                                    </tr>
                `;
            }).join('');

            submoduleContent = `
                <p class="mb-6 text-gray-500">View all customers, their order history, and account status.</p>
                    <div class="kpi-card p-6">
                        <h3 class="text-xl font-semibold mb-4">Customer Directory (${customersData.length} Customers)</h3>
                        <div class="table-container" style="overflow-x: auto;">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Total Orders</th>
                                        <th>Total Spent</th>
                                        <th>Status</th>
                                        <th>Joined</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${customersData.length > 0 ? customerRows : `<tr><td colspan="7" class="text-center text-gray-500 py-4">No customers found.</td></tr>`}
                                </tbody>
                            </table>
                        </div>
                    </div>
            `;
            break;
    }

    content.innerHTML = `<h2 class="page-header">${moduleTitle}</h2>${submoduleContent}`;
    lucide.createIcons();
}


// --- USER VIEW FUNCTIONS ---
function viewUserDetails(userId, roleType = 'customer') {
    let user;
    let title = 'Member Profile';

    if (roleType === 'admin') {
        user = (window.adminUsersData || adminUsersData).find(u => u.id == userId);
        title = 'Administrator Profile';
    } else {
        user = (window.customersData || customersData).find(u => u.id == userId);
        title = 'Customer Profile';
    }

    if (!user) {
        showCustomActionModal('Error', 'Account record not found in system.', 'OK');
        return;
    }

    const detailsHTML = `
        <div style="padding: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 2rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 1.5rem;">
                <div style="width: 70px; height: 70px; border-radius: 50%; background: #2563eb; color: white; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; font-weight: 800;">
                    ${(user.username || user.full_name || 'U').charAt(0).toUpperCase()}
                </div>
                <div>
                    <h3 style="font-size: 1.25rem; font-weight: 800; color: #1e293b; margin: 0;">${user.full_name || user.username}</h3>
                    <p style="color: #64748b; font-size: 0.875rem; margin: 0.25rem 0 0 0;">${user.email || 'No email provided'}</p>
                    <span style="display: inline-block; margin-top: 0.5rem; padding: 3px 10px; border-radius: 6px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; background: #eff6ff; color: #2563eb; border: 1px solid #dbeafe;">${roleType}</span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                <div>
                    <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-bottom: 0.5rem;">Account Information</div>
                    <div style="background: #f8fafc; padding: 1rem; border-radius: 0.75rem; border: 1px solid #f1f5f9;">
                        <div style="margin-bottom: 0.75rem;">
                            <span style="font-size: 0.8125rem; color: #64748b; display: block;">System ID</span>
                            <span style="font-weight: 700; color: #1e293b;">#${user.id}</span>
                        </div>
                        <div style="margin-bottom: 0.75rem;">
                            <span style="font-size: 0.8125rem; color: #64748b; display: block;">Username</span>
                            <span style="font-weight: 700; color: #1e293b;">${user.username || 'N/A'}</span>
                        </div>
                        <div>
                            <span style="font-size: 0.8125rem; color: #64748b; display: block;">Joined On</span>
                            <span style="font-weight: 700; color: #1e293b;">${user.created_at || 'Jan 20, 2024'}</span>
                        </div>
                    </div>
                </div>
                <div>
                    <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-bottom: 0.5rem;">Contact & Status</div>
                    <div style="background: #f8fafc; padding: 1rem; border-radius: 0.75rem; border: 1px solid #f1f5f9;">
                        <div style="margin-bottom: 0.75rem;">
                            <span style="font-size: 0.8125rem; color: #64748b; display: block;">Phone Number</span>
                            <span style="font-weight: 700; color: #1e293b;">${user.phone_number || 'Not Linked'}</span>
                        </div>
                        <div style="margin-bottom: 0.75rem;">
                            <span style="font-size: 0.8125rem; color: #64748b; display: block;">Account Status</span>
                            <span style="color: #059669; font-weight: 700;">● Active</span>
                        </div>
                        <div>
                            <span style="font-size: 0.8125rem; color: #64748b; display: block;">Verification</span>
                            <span style="color: #0284c7; font-weight: 700;">✓ Verified</span>
                        </div>
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 1rem;">
                <button onclick="document.getElementById('custom-modal-backdrop').classList.add('hidden')" class="btn-base btn-secondary" style="padding: 0.75rem 1.5rem; font-weight: 700;">Dismiss View</button>
            </div>
        </div>
    `;

    const modalContainer = document.getElementById('modal-container');
    modalContainer.innerHTML = detailsHTML;
    document.getElementById('custom-modal-backdrop').classList.remove('hidden');
    lucide.createIcons();
}

function triggerProfileImagePicker() {
    const fileInput = document.getElementById('profile_image');
    if (fileInput) {
        fileInput.click();
    }
}

function handleProfileImageChange(event) {
    const file = event.target.files && event.target.files[0];
    if (!file) {
        return;
    }

    if (file.size > 2 * 1024 * 1024) {
        showCustomActionModal('Error', 'Profile image must be 2MB or smaller.', 'OK');
        event.target.value = '';
        return;
    }

    const preview = document.getElementById('profile-avatar');
    if (preview) {
        preview.src = URL.createObjectURL(file);
    }
}

// Form Validation for Profile Update
function validateProfileForm(event) {
    const newPass = document.getElementById('new_password').value;
    const confirmPass = document.getElementById('confirm_password').value;

    if (newPass && newPass.length < 6) {
        showCustomActionModal('Error', 'New password must be at least 6 characters long.', 'OK');
        return false;
    }

    if (newPass && newPass !== confirmPass) {
        showCustomActionModal('Error', 'New password and confirmation password do not match.', 'OK');
        return false;
    }

    // If valid, submit the form via the custom modal to handle the POST logic
    showCustomActionModal('Confirm Update', 'Are you sure you want to save these profile changes?', 'Confirm Save', () => event.target.submit());

    // Prevent default submission here as we use the callback in the modal
    return false;
}

// 6. Customer Support Center - Moved to Support Portal

// Chat module removed - Moved to Support Portal


// Ticket Detials removed - Moved to Support Portal

function deleteAdmin(id, username) {
    showCustomActionModal(
        'Delete Admin User',
        `Are you sure you want to delete admin user <strong>${username}</strong>? This action cannot be undone.`,
        'Delete',
        () => {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'dashboard.php';
            form.innerHTML = `
                                    <input type="hidden" name="action" value="delete_admin">
                                    <input type="hidden" name="id" value="${id}">
                                    <input type="hidden" name="module" value="user">
                                    <input type="hidden" name="submodule" value="admins">
                                `;
            document.body.appendChild(form);
            form.submit();
        }
    );
}

// 7. Notification & Alert System
// 7. Notification & Alert System
function renderAlertsModule() {
    setPageTitle('Alert & Transaction Hub');
    const content = document.getElementById('content-container');

    // Fetch both notifications and transaction alerts
    Promise.all([
        fetch('get_notifications.php').then(r => r.json()),
        fetch('get_transaction_alerts.php').then(r => r.json())
    ])
        .then(([notifData, alertData]) => {
            // Process notifications
            let notificationRows = '';
            if (notifData.success && notifData.notifications && notifData.notifications.length > 0) {
                notificationRows = notifData.notifications.map(notif => {
                    const iconColor = notif.type === 'chat' ? '#3b82f6' : notif.type === 'order' ? '#8b5cf6' : notif.type === 'review' ? '#f59e0b' : '#64748b';
                    const iconName = notif.type === 'chat' ? 'message-circle' : notif.type === 'order' ? 'shopping-bag' : notif.type === 'review' ? 'star' : 'bell';

                    return `
                        <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                            <td style="padding: 1rem 1.5rem; text-align: center;">
                                <div style="width: 36px; height: 36px; border-radius: 10px; background: ${iconColor}15; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                    <i data-lucide="${iconName}" style="width: 18px; height: 18px; color: ${iconColor};"></i>
                                </div>
                            </td>
                            <td style="padding: 1rem 1.5rem;">
                                <span style="font-size: 0.7rem; font-weight: 800; text-transform: uppercase; color: #64748b; background: #f1f5f9; padding: 3px 8px; border-radius: 5px;">${notif.source || notif.type}</span>
                            </td>
                            <td style="padding: 1rem 1.5rem;">
                                <div style="font-size: 0.9rem; font-weight: 700; color: #1e293b; margin-bottom: 3px;">${notif.title}</div>
                                <div style="font-size: 0.8125rem; color: #64748b; line-height: 1.5;">${notif.message}</div>
                            </td>
                            <td style="padding: 1rem 1.5rem; font-size: 0.8125rem; color: #94a3b8; white-space: nowrap; font-weight: 500;">
                                ${notif.time_ago}
                            </td>
                            <td style="padding: 1rem 1.5rem; text-align: right;">
                                <button onclick='showNotificationDetails(${JSON.stringify(notif).replace(/'/g, "&apos;")})' 
                                    style="padding: 7px 14px; font-size: 0.75rem; font-weight: 700; color: #3b82f6; background: white; border: 1px solid #e2e8f0; border-radius: 8px; cursor: pointer; transition: all 0.2s;"
                                    onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#3b82f6'" onmouseout="this.style.background='white'; this.style.borderColor='#e2e8f0'">
                                    View
                                </button>
                            </td>
                        </tr>
                    `;
                }).join('');
            } else {
                notificationRows = `<tr><td colspan="5" style="padding: 4rem; text-align: center; color: #94a3b8; font-style: italic;">No system notifications detected.</td></tr>`;
            }

            // Process transaction alerts
            let alertRows = '';
            if (alertData.success && alertData.alerts && alertData.alerts.length > 0) {
                alertRows = alertData.alerts.map(alert => {
                    const getPriorityColor = (priority) => {
                        const colors = {
                            'critical': { bg: '#fee2e2', color: '#dc2626', icon: 'alert-circle' },
                            'high': { bg: '#fef3c7', color: '#b45309', icon: 'alert-triangle' },
                            'warning': { bg: '#fef3c7', color: '#b45309', icon: 'alert-triangle' },
                            'normal': { bg: '#e0e7ff', color: '#4f46e5', icon: 'info' }
                        };
                        return colors[priority] || colors['normal'];
                    };
                    
                    const priorityStyle = getPriorityColor(alert.priority);
                    
                    return `
                        <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                            <td style="padding: 1rem 1.5rem; text-align: center;">
                                <div style="width: 36px; height: 36px; border-radius: 10px; background: ${priorityStyle.bg}; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                    <i data-lucide="${alert.icon}" style="width: 18px; height: 18px; color: ${priorityStyle.color};"></i>
                                </div>
                            </td>
                            <td style="padding: 1rem 1.5rem;">
                                <span style="font-size: 0.7rem; font-weight: 800; text-transform: uppercase; color: white; background: ${priorityStyle.color}; padding: 3px 8px; border-radius: 5px;">${alert.priority}</span>
                            </td>
                            <td style="padding: 1rem 1.5rem;">
                                <div style="font-size: 0.9rem; font-weight: 700; color: #1e293b; margin-bottom: 3px;">${alert.title}</div>
                                <div style="font-size: 0.8125rem; color: #64748b; line-height: 1.5;">${alert.message}</div>
                            </td>
                            <td style="padding: 1rem 1.5rem; font-size: 0.8125rem; color: #94a3b8; white-space: nowrap; font-weight: 500;">
                                ${alert.time_ago}
                            </td>
                            <td style="padding: 1rem 1.5rem; text-align: right;">
                                <button onclick="handleAlertAction('${alert.type}')" 
                                    style="padding: 7px 14px; font-size: 0.75rem; font-weight: 700; color: ${priorityStyle.color}; background: ${priorityStyle.bg}; border: 1px solid ${priorityStyle.color}20; border-radius: 8px; cursor: pointer; transition: all 0.2s;"
                                    onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                                    Action
                                </button>
                            </td>
                        </tr>
                    `;
                }).join('');
            } else {
                alertRows = `<tr><td colspan="5" style="padding: 4rem; text-align: center; color: #94a3b8; font-style: italic;">No transaction alerts at this time.</td></tr>`;
            }

            const lowStockProducts = productsData.filter(p => parseInt(p.stock) < 10 && p.status !== 'Inactive');
            const pendingOrders = ordersData.filter(o => o.status === 'Pending' || o.status === 'Processing');
            const openTickets = supportTicketsData.filter(t => t.status === 'Open' || t.status === 'In Progress');

            const pulseCards = [
                { title: 'Inventory', label: 'Low Stock Items', val: lowStockProducts.length, color: '#f59e0b', icon: 'package' },
                { title: 'Orders', label: 'Pending Action', val: pendingOrders.length, color: '#6366f1', icon: 'shopping-cart' },
                { title: 'Support', label: 'Active Tickets', val: openTickets.length, color: '#10b981', icon: 'life-buoy' },
                { title: 'Transaction', label: 'Total Alerts', val: alertData.total_count || 0, color: '#ec4899', icon: 'trending-up' }
            ].map(card => `
                <div class="glass-panel" style="padding: 1.5rem; border-radius: 1.25rem; background: white; border: 1px solid #f1f5f9; display: flex; align-items: center; gap: 1.25rem; flex: 1; min-width: 200px;">
                    <i data-lucide="${card.icon}" style="width: 28px; height: 28px; color: ${card.color}; opacity: 0.8; flex-shrink: 0;"></i>
                    <div>
                        <div style="font-size: 0.75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 2px;">${card.title}</div>
                        <div style="display: flex; align-items: baseline; gap: 6px;">
                            <span style="font-size: 1.5rem; font-weight: 800; color: #1e293b;">${card.val}</span>
                            <span style="font-size: 0.75rem; color: #64748b; font-weight: 500;">${card.label}</span>
                        </div>
                    </div>
                </div>
            `).join('');

            content.innerHTML = `
                <div style="animation: fadeIn 0.4s ease-out; margin-bottom: 2rem;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem;">
                        <div>
                            <h2 class="page-header" style="margin: 0; font-size: 1.85rem; font-weight: 900; color: #1e293b; letter-spacing: -0.02em;">Alert & Transaction Hub</h2>
                            <p style="color: #64748b; font-size: 0.95rem; margin-top: 0.4rem;">Monitor live transactions, orders, and system events in real-time across your marketplace.</p>
                        </div>
                        <button onclick="renderAlertsModule()" style="background: white; border: 1px solid #e2e8f0; color: #1e293b; padding: 0.6rem 1.25rem; border-radius: 10px; cursor: pointer; display: flex; align-items: center; gap: 0.6rem; font-size: 0.85rem; font-weight: 700; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05);" onmouseover="this.style.background='#f8fafc'; this.style.transform='translateY(-1px)'" onmouseout="this.style.background='white'; this.style.transform='translateY(0)'">
                            <i data-lucide="refresh-cw" style="width: 16px; height: 16px;"></i> Refresh
                        </button>
                    </div>

                    <div style="display: flex; gap: 1.5rem; overflow-x: auto; padding-bottom: 0.5rem; margin-bottom: 2.5rem;">
                        ${pulseCards}
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
                        <!-- Transaction Alerts Stream -->
                        <div class="glass-panel" style="border-radius: 1.5rem; background: white; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; overflow: hidden;">
                            <div style="padding: 1.5rem 2rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: linear-gradient(135deg, #fef3c7 0%, #fef9e7 100%); border-bottom: 2px solid #fcd34d;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <i data-lucide="zap" style="width: 20px; height: 20px; color: #f59e0b;"></i>
                                    <h3 style="font-size: 1rem; font-weight: 800; color: #1e293b; margin: 0; text-transform: uppercase; letter-spacing: 0.05em;">Live Transaction Alerts</h3>
                                </div>
                                <span style="font-size: 0.8rem; color: #6b7280; font-weight: 600;">Last updated: ${new Date().toLocaleTimeString()}</span>
                            </div>
                            
                            <div style="overflow-x: auto;">
                                <table style="width: 100%; border-collapse: collapse;">
                                    <thead>
                                        <tr style="background: white;">
                                            <th style="padding: 1.25rem 1.5rem; text-align: left; font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em;">Type</th>
                                            <th style="padding: 1.25rem 1.5rem; text-align: left; font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em;">Priority</th>
                                            <th style="padding: 1.25rem 1.5rem; text-align: left; font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em;">Alert Details</th>
                                            <th style="padding: 1.25rem 1.5rem; text-align: left; font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em;">Time</th>
                                            <th style="padding: 1.25rem 1.5rem; text-align: right; font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="alert-tbody">
                                        ${alertRows}
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- System Notifications Stream -->
                        <div class="glass-panel" style="border-radius: 1.5rem; background: white; box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; overflow: hidden;">
                            <div style="padding: 1.5rem 2rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <i data-lucide="bell" style="width: 20px; height: 20px; color: #3b82f6;"></i>
                                    <h3 style="font-size: 1rem; font-weight: 800; color: #1e293b; margin: 0; text-transform: uppercase; letter-spacing: 0.05em;">System Notifications</h3>
                                </div>
                                <span style="font-size: 0.8rem; color: #64748b; font-weight: 600;">Count: ${notifData.notifications ? notifData.notifications.length : 0}</span>
                            </div>
                            
                            <div style="overflow-x: auto;">
                                <table style="width: 100%; border-collapse: collapse;">
                                    <thead>
                                        <tr style="background: white;">
                                            <th style="padding: 1.25rem 1.5rem; text-align: left; font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em;">Type</th>
                                            <th style="padding: 1.25rem 1.5rem; text-align: left; font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em;">Source</th>
                                            <th style="padding: 1.25rem 1.5rem; text-align: left; font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em;">Details</th>
                                            <th style="padding: 1.25rem 1.5rem; text-align: left; font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em;">Time</th>
                                            <th style="padding: 1.25rem 1.5rem; text-align: right; font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.1em;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="notification-tbody">
                                        ${notificationRows}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            lucide.createIcons();
        })
        .catch(error => {
            console.error('Error loading alerts and notifications:', error);
            content.innerHTML = `<div style="padding: 2rem; color: #ef4444; text-align: center;">
                <i data-lucide="alert-circle" style="width: 48px; height: 48px; margin-bottom: 1rem;"></i>
                <p>Failed to initialize Digital Pulse Monitoring. Connection to notification gateway lost.</p>
                <button onclick="renderAlertsModule()" style="margin-top: 1rem; padding: 0.5rem 1rem; cursor: pointer;">Retry Connection</button>
            </div>`;
            lucide.createIcons();
        });
}

// Handle alert actions based on alert type
function handleAlertAction(alertType) {
    switch(alertType) {
        case 'high_value_order':
            showSubModule('order', 'orders');
            break;
        case 'pending_payment':
            showSubModule('order', 'orders');
            break;
        case 'delivery_ready':
            showSubModule('shipping', 'tracking');
            break;
        case 'support_tickets':
            showSubModule('support', 'tickets');
            break;
        case 'low_stock':
            showSubModule('product', 'products');
            break;
        default:
            showSubModule('order', 'orders');
    }
}

// 7b. Customer Support Redirect Stub
function renderSupportModule(submodule = 'chat') {
    showCustomActionModal('Support Portal', 'You are being redirected to the integrated Customer Support Portal system.', 'Proceed', () => {
        window.location.href = '../CustomerSupport/dashboard.php';
    });
}

function sendBroadcast() {
    const msg = document.getElementById('broadcast-message').value;
    if (!msg.trim()) {
        showCustomActionModal('Empty Message', 'Please enter a message to broadcast.');
        return;
    }
    showCustomActionModal('Dispatch Success', 'Broadcast ' + msg.substring(0, 20) + '... has been sent to all users.');
    document.getElementById('broadcast-message').value = '';
}





// 8. System Settings & Security
function renderSettingsModule() {
    setPageTitle('System Settings & Security');
    const content = document.getElementById('content-container');
    content.innerHTML = `
    <h2 class="page-header">System Settings & Security</h2>
        <p class="mb-6 text-gray-500">Protect system integrity and ensure compliance.</p>

                            <div class="module-container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
                                <div class="kpi-card p-6 space-y-4">
                                    <h3 class="text-xl font-semibold pb-2" style="border-bottom: 1px solid #e5e7eb;">Database and Audit</h3>
                                    <div class="flex justify-between items-center">
                                        <span>Backup and Restore Database</span>
                                        <button class="btn-base btn-secondary" style="padding: 0.5rem 1rem;"
                                            onclick="showCustomActionModal('Database Backup', 'Are you sure you want to initiate a full database backup now?', 'Backup', () => console.log('Backup initiated'))">Execute Backup</button>
                                    </div>
                                    <div style="border-bottom: 1px solid #e5e7eb; padding-bottom: 1rem;">
                                        <div class="flex justify-between items-center mb-3">
                                            <span>View Audit Logs</span>
                                            <button class="btn-base btn-secondary" style="padding: 0.5rem 1rem;"
                                                onclick="showCustomActionModal('System Audit', 'The complete audit log history would load in a new window/tab.', 'View')">View Logs</button>
                                        </div>
                                        <p class="text-sm text-gray-500">*(Record who changed what)*</p>
                                    </div>

                                </div>

                                <div class="kpi-card p-6 space-y-4">
                                    <h3 class="text-xl font-semibold pb-2" style="border-bottom: 1px solid #e5e7eb;">Security & API</h3>
                                    <div class="flex justify-between items-center">
                                        <span>API Key Management</span>
                                        <button class="btn-base btn-secondary" style="padding: 0.5rem 1rem;"
                                            onclick="showCustomActionModal('API Key Management', 'The interface to manage, generate, and revoke API keys would open here.', 'OK')">Manage Keys</button>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span>SSL / Firewall Monitoring</span>
                                        <span class="text-green-600 font-bold" style="color: var(--color-green-600);">Status: Secure</span>
                                    </div>
                                </div>
                            </div>
`;
    lucide.createIcons();
}

function showModule(moduleName, element = null) {
    document.querySelectorAll('.submenu').forEach(sub => sub.classList.add('hidden'));
    document.querySelectorAll('.nav-item .chevron-icon').forEach(icon => {
        icon.classList.remove('rotate-90');
    });

    let navElement = element;
    if (!navElement) {
        navElement = document.querySelector(`.nav-menu a[onclick *= "'${moduleName}'"]`);
    }
    if (navElement) {
        setActiveNav(navElement);
    }

    // Render the content based on the module
    switch (moduleName) {
        case 'dashboard':
            renderDashboard();
            break;
        case 'product':
            // Default to product list
            showSubModule('product', 'products');
            break;
        case 'order':
            // Default to view all orders
            showSubModule('order', 'orders');
            break;
        case 'shipping':
            // Default to addresses
            showSubModule('shipping', 'addresses');
            break;
        case 'support':
            renderSupportModule();
            break;
        case 'notifications':
        case 'alerts':
            renderAlertsModule();
            break;
        case 'settings':
            renderSettingsModule();
            break;
        default:
            renderDashboard();
            break;
    }

    // Update URL state
    if (moduleName !== 'dashboard') { // Avoid redundant default state push if feasible, but consistent is better
        // If module routes to a submodule by default, let the submodule function handle the state push to avoid double push
        // checking if it's a direct render module
        if (['support', 'alerts', 'notifications', 'settings', 'dashboard'].includes(moduleName)) {
            const newUrl = new URL(window.location);
            newUrl.searchParams.set('module', moduleName);
            newUrl.searchParams.delete('submodule'); // Clear submodule if switching to a main module
            window.history.pushState({ module: moduleName }, '', newUrl);
        }
    } else {
        const newUrl = new URL(window.location);
        newUrl.searchParams.delete('module');
        newUrl.searchParams.delete('submodule');
        window.history.pushState({ module: 'dashboard' }, '', newUrl);
    }

    window.scrollTo(0, 0);
}

function showSubModule(modulePrefix, submodule, filterValue = null) {
    const moduleMap = {
        'product': renderProductModule,
        'order': renderOrderModule,
        'shipping': renderShippingModule,
        'user': renderUserModule,
        'support': renderSupportModule,
    };

    if (moduleMap[modulePrefix]) {
        const newUrl = new URL(window.location);
        newUrl.searchParams.set('module', modulePrefix);
        newUrl.searchParams.set('submodule', submodule);

        moduleMap[modulePrefix](submodule, filterValue);

        // --- Navigation Fix for Submodules ---
        // 1. Find the specific submenu link
        const submenuLink = document.querySelector(`.submenu a[onclick *= "'${submodule}'"]`);

        // 2. Clear all active states
        document.querySelectorAll('.nav-menu a').forEach(item => item.classList.remove('active-nav'));

        if (submenuLink) {
            // 3. Set submenu link to active
            submenuLink.classList.add('active-nav');

            // 4. Set parent navigation item to active AND ensure its submenu is open
            const parentGroup = submenuLink.closest('.group');
            const parentNav = parentGroup.querySelector('.nav-item');
            if (parentNav) {
                parentNav.classList.add('active-nav');
                const submenu = parentGroup.querySelector('.submenu');
                const icon = parentNav.querySelector('.chevron-icon');
                if (submenu && submenu.classList.contains('hidden')) {
                    submenu.classList.remove('hidden');
                    icon.classList.add('rotate-90');
                }
            }
        }

        if (filterValue) {
            newUrl.searchParams.set('filter', filterValue);
        } else {
            newUrl.searchParams.delete('filter');
        }
        window.history.pushState({ module: modulePrefix, submodule: submodule, filter: filterValue }, '', newUrl);
    }
    window.scrollTo(0, 0);
}

// --- PRODUCT FORM FUNCTIONS ---
function showProductForm(productId = null) {
    const product = productId ? productsData.find(p => p.id == productId) : null;
    const isEdit = !!product;
    const categoryOptions = categoriesData.map(cat =>
        `< option value = "${cat.id}" ${product && product.category_id == cat.id ? 'selected' : ''}> ${cat.name}</option > `
    ).join('');

    const formHTML = `
    < form id = "product-form" method = "POST" action = "dashboard.php" >
        <input type="hidden" name="action" value="${isEdit ? 'edit_product' : 'add_product'}">
            <input type="hidden" name="module" value="product">
                <input type="hidden" name="submodule" value="products">
                    ${isEdit ? `<input type="hidden" name="id" value="${product.id}">` : ''}

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label>Product Name *</label>
                        <input type="text" name="name" value="${product ? product.name.replace(/" /g, '&quot;') : ''}" required 
                                        style="width: 100%; padding: 0.75rem; border: 1px solid var(--color-gray-300); border-radius: 0.5rem;">
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label>Description</label>
                        <textarea name="description" rows="3"
                            style="width: 100%; padding: 0.75rem; border: 1px solid var(--color-gray-300); border-radius: 0.5rem;">${product ? (product.description || '').replace(/"/g, '&quot;') : ''}</textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                        <div class="form-group">
                            <label>Price *</label>
                            <input type="number" name="price" step="0.01" value="${product ? product.price : ''}" required
                                style="width: 100%; padding: 0.75rem; border: 1px solid var(--color-gray-300); border-radius: 0.5rem;">
                        </div>

                        <div class="form-group">
                            <label>Stock Quantity *</label>
                            <input type="number" name="stock" value="${product ? product.stock : ''}" required
                                style="width: 100%; padding: 0.75rem; border: 1px solid var(--color-gray-300); border-radius: 0.5rem;">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                        <div class="form-group">
                            <label>Category *</label>
                            <select name="category_id" required
                                style="width: 100%; padding: 0.75rem; border: 1px solid var(--color-gray-300); border-radius: 0.5rem;">
                                <option value="">Select Category</option>
                                ${categoryOptions}
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select name="status"
                                style="width: 100%; padding: 0.75rem; border: 1px solid var(--color-gray-300); border-radius: 0.5rem;">
                                <option value="Active" ${product && product.status === 'Active' ? 'selected' : ''}>Active</option>
                                <option value="Inactive" ${product && product.status === 'Inactive' ? 'selected' : ''}>Inactive</option>
                                <option value="Low Stock" ${product && product.status === 'Low Stock' ? 'selected' : ''}>Low Stock</option>
                                <option value="Critical Stock" ${product && product.status === 'Critical Stock' ? 'selected' : ''}>Critical Stock</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label>Image URL (Optional)</label>
                        <input type="url" name="image_url" value="${product ? (product.image_url || '') : ''}"
                            placeholder="https://example.com/image.jpg"
                            style="width: 100%; padding: 0.75rem; border: 1px solid var(--color-gray-300); border-radius: 0.5rem;">
                            <small style="color: #6b7280; font-size: 0.75rem; margin-top: 0.25rem; display: block;">Enter a URL to an image for this product</small>
                    </div>

                    <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                        <button type="submit" class="btn-base btn-primary" style="flex: 1; padding: 0.75rem; font-weight: 600;">
                            <i data-lucide="${isEdit ? 'save' : 'plus'}" style="width: 1rem; height: 1rem; margin-right: 0.5rem;"></i>
                            ${isEdit ? 'Update Product' : 'Add Product'}
                        </button>
                        <button type="button" class="btn-base btn-secondary" onclick="document.getElementById('custom-modal-backdrop').classList.add('hidden')" style="flex: 1; padding: 0.75rem;">
                            Cancel
                        </button>
                    </div>
                </form>
                `;

    const modalContainer = document.getElementById('modal-container');
    modalContainer.innerHTML = `
                <h3 style="font-size: 1.25rem; font-weight: 700; color: #1f2937; margin-bottom: 1.5rem;">${isEdit ? 'Edit Product' : 'Add New Product'}</h3>
                <div id="modal-form-content">${formHTML}</div>
                `;
    document.getElementById('custom-modal-backdrop').classList.remove('hidden');
}

function deleteProduct(id, name) {
    showCustomActionModal(
        'Delete Product',
        `Are you sure you want to delete <strong>${name}</strong>? This action cannot be undone.`,
        'Delete',
        () => {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'dashboard.php';
            form.innerHTML = `
                <input type="hidden" name="action" value="delete_product">
                <input type="hidden" name="id" value="${id}">
                <input type="hidden" name="module" value="product">
                <input type="hidden" name="submodule" value="products">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    );
}

function clearAllProducts() {
    const productCount = productsData.length;
    showCustomActionModal(
        'Clear All Products',
        `Are you sure you want to delete <strong>ALL ${productCount} product(s)</strong> from the database?<br><br><strong style="color: #ef4444;">This action cannot be undone!</strong><br>All sample products will be permanently removed.`,
        'Delete All',
        () => {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'dashboard.php';
            form.innerHTML = `
                    <input type="hidden" name="action" value="clear_all_products">
                        <input type="hidden" name="module" value="product">
                            <input type="hidden" name="submodule" value="products">
                                `;
            document.body.appendChild(form);
            form.submit();
        }
    );
}

// --- CATEGORY FORM FUNCTIONS ---
function showCategoryForm(categoryId = null) {
    const category = categoryId ? categoriesData.find(c => c.id == categoryId) : null;
    const isEdit = !!category;

    const formHTML = `
                                <form id="category-form" method="POST" action="dashboard.php">
                                    <input type="hidden" name="action" value="${isEdit ? 'edit_category' : 'add_category'}">
                                        <input type="hidden" name="module" value="product">
                                            <input type="hidden" name="submodule" value="categories">
                                                ${isEdit ? `<input type="hidden" name="id" value="${category.id}">` : ''}

                                                <div class="form-group" style="margin-bottom: 1.5rem;">
                                                    <label>Category Name *</label>
                                                    <input type="text" name="name" value="${category ? category.name.replace(/" /g, '&quot;') : ''}" required 
                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--color-gray-300); border-radius: 0.5rem;">
                                                </div>

                                                <div class="form-group" style="margin-bottom: 1.5rem;">
                                                    <label>Description</label>
                                                    <textarea name="description" rows="3"
                                                        style="width: 100%; padding: 0.75rem; border: 1px solid var(--color-gray-300); border-radius: 0.5rem;">${category ? (category.description || '').replace(/"/g, '&quot;') : ''}</textarea>
                                                </div>

                                                <div class="form-group" style="margin-bottom: 1.5rem;">
                                                    <label>Status</label>
                                                    <select name="status"
                                                        style="width: 100%; padding: 0.75rem; border: 1px solid var(--color-gray-300); border-radius: 0.5rem;">
                                                        <option value="Active" ${category && category.status === 'Active' ? 'selected' : ''}>Active</option>
                                                        <option value="Inactive" ${category && category.status === 'Inactive' ? 'selected' : ''}>Inactive</option>
                                                    </select>
                                                </div>

                                                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                                                    <button type="submit" class="btn-base btn-primary" style="flex: 1;">
                                                        ${isEdit ? 'Update Category' : 'Add Category'}
                                                    </button>
                                                    <button type="button" class="btn-base btn-secondary" onclick="document.getElementById('custom-modal-backdrop').classList.add('hidden')" style="flex: 1;">
                                                        Cancel
                                                    </button>
                                                </div>
                                            </form>
                                            `;

    const modalContainer = document.getElementById('modal-container');
    modalContainer.innerHTML = `
                                            <h3 style="font-size: 1.25rem; font-weight: 700; color: #1f2937; margin-bottom: 1.5rem;">${isEdit ? 'Edit Category' : 'Add New Category'}</h3>
                                            <div id="modal-form-content">${formHTML}</div>
                                            `;
    document.getElementById('custom-modal-backdrop').classList.remove('hidden');
}

function deleteCategory(id, name) {
    showCustomActionModal(
        'Delete Category',
        `Are you sure you want to delete category <strong>${name}</strong>? This action cannot be undone.`,
        'Delete',
        () => {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'dashboard.php';
            form.innerHTML = `
                <input type="hidden" name="action" value="delete_category">
                <input type="hidden" name="id" value="${id}">
                <input type="hidden" name="module" value="product">
                <input type="hidden" name="submodule" value="categories">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    );
}

// Initial load
document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('.sidebar')) {
        const urlParams = new URLSearchParams(window.location.search);
        const initialModule = urlParams.get('module') || 'dashboard';
        const initialSubmodule = urlParams.get('submodule');

        if (initialSubmodule) {
            showSubModule(initialModule, initialSubmodule);
        } else {
            const navElement = document.querySelector(`.nav-menu a[onclick*="'${initialModule}'"]`);
            showModule(initialModule, navElement);
        }
    }

    // Show message on load (for success/error from redirects)
    const urlParams = new URLSearchParams(window.location.search);
    const message = urlParams.get('msg');
    const isOtp = urlParams.get('view') === 'otp';

    if (message && !isOtp) {
        const isSuccess = message.toLowerCase().includes('success') || message.toLowerCase().includes('welcome');
        const title = isSuccess ? 'Success' : 'Action Required';
        showCustomActionModal(title, decodeURIComponent(message), 'OK');

        // Remove query params to prevent reappearing on reload
        window.history.replaceState({}, document.title, window.location.pathname);
    } else if (message && isOtp) {
        const title = 'OTP Required';
        showCustomActionModal(title, decodeURIComponent(message), 'OK');
    }

    // Ensure icons are rendered for the sidebar/static content
    lucide.createIcons();
});


// --- AI ADMIN ASSISTANT ---
function openAdminAiChat() {
    const backdrop = document.getElementById('custom-modal-backdrop');
    const container = document.getElementById('modal-container');

    container.innerHTML = `
        <div style="text-align: left; max-width: 600px; width: 100%;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
                <div>
                    <h3 style="color: #1e293b; margin-top: 0; display: flex; align-items: center; gap: 10px; font-size: 1.5rem; font-weight: 800;">
                        <i data-lucide="sparkles" style="color: #7c3aed;"></i> AI Admin Assistant
                    </h3>
                    <p style="color: #64748b; font-size: 0.9rem; margin: 0;">How can I help you manage the marketplace today?</p>
                </div>
                <button onclick="closeCustomModal()" style="background: #f1f5f9; border: none; border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #64748b;">
                    <i data-lucide="x" style="width: 1.2rem; height: 1.2rem;"></i>
                </button>
            </div>
            
            <div id="ai-chat-messages" style="height: 350px; overflow-y: auto; background: #f8fafc; border-radius: 12px; padding: 1.25rem; margin-bottom: 1.25rem; border: 1px solid #e2e8f0; display: flex; flex-direction: column; gap: 12px; scroll-behavior: smooth;">
                <div style="background: white; padding: 1rem; border-radius: 1rem; border-bottom-left-radius: 0.25rem; align-self: flex-start; max-width: 85%; font-size: 0.9375rem; border: 1px solid #f1f5f9; box-shadow: 0 2px 4px rgba(0,0,0,0.02); line-height: 1.5;">
                    Hello, <strong>${adminConfig.username}</strong>! I'm your AI operations assistant. I have real-time access to your marketplace data. <br><br>You can ask me things like:<br>
                    • "What's our revenue this month?"<br>
                    • "Which products are low on stock?"<br>
                    • "Show me recent orders from Metro Manila"
                </div>
            </div>
            
            <div style="display: flex; gap: 0.75rem; background: white; padding: 0.5rem; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                <input type="text" id="ai-chat-input" placeholder="Ask anything about your store..." 
                    style="flex: 1; padding: 0.75rem 1rem; border: none; outline: none; font-size: 0.95rem;"
                    onkeypress="if(event.key === 'Enter') sendAdminAiMessage()">
                <button onclick="sendAdminAiMessage()" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; border: none; width: 42px; height: 42px; border-radius: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;"
                    onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    <i data-lucide="send" style="width: 1.2rem; height: 1.2rem;"></i>
                </button>
            </div>
        </div>
    `;

    backdrop.classList.remove('hidden');
    lucide.createIcons();
    setTimeout(() => document.getElementById('ai-chat-input').focus(), 100);
}

function sendAdminAiMessage() {
    const input = document.getElementById('ai-chat-input');
    const msg = input.value.trim();
    if (!msg) return;

    const chatContainer = document.getElementById('ai-chat-messages');

    // Add user message
    const userDiv = document.createElement('div');
    userDiv.style.cssText = "background: #4f46e5; color: white; padding: 0.875rem 1rem; border-radius: 1rem; border-bottom-right-radius: 0.25rem; align-self: flex-end; max-width: 85%; font-size: 0.9375rem; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2); margin-top: 8px;";
    userDiv.textContent = msg;
    chatContainer.appendChild(userDiv);
    input.value = '';
    chatContainer.scrollTop = chatContainer.scrollHeight;

    // AI Response Logic (Mock Intel)
    setTimeout(() => {
        const responseDiv = document.createElement('div');
        responseDiv.style.cssText = "background: white; padding: 1rem; border-radius: 1rem; border-bottom-left-radius: 0.25rem; align-self: flex-start; max-width: 85%; font-size: 0.9375rem; border: 1px solid #f1f5f9; box-shadow: 0 4px 15px rgba(0,0,0,0.03); line-height: 1.5; margin-top: 8px; border-left: 4px solid #7c3aed;";

        let responseText = "I'm analyzing that for you...";
        const query = msg.toLowerCase();

        if (query.includes('revenue') || query.includes('sales')) {
            responseText = `Based on your Intelligence Hub data, your MTD Revenue is <strong>${formatCurrency(kpiData.totalRevenue || 0)}</strong>. This is performing within expected growth parameters.`;
        } else if (query.includes('stock') || query.includes('inventory')) {
            const lowStock = productsData.filter(p => parseInt(p.stock) < 10);
            if (lowStock.length > 0) {
                responseText = `You currently have <strong>${lowStock.length} items</strong> with critical stock levels. <button onclick="closeCustomModal(); showSubModule('product', 'products')" style="color: #4f46e5; background: none; border: none; padding: 0; font-weight: 700; cursor: pointer; text-decoration: underline;">Open Inventory</button> to restock them.`;
            } else {
                responseText = "Your inventory levels are looking healthy across all categories. No critical stock alerts at the moment.";
            }
        } else if (query.includes('order')) {
            responseText = `You have <strong>${ordersData.length} total orders</strong> in the system. <strong>${ordersData.filter(o => o.status === 'Pending').length}</strong> are still pending.`;
        } else if (query.includes('hello') || query.includes('hi')) {
            responseText = `Hello! I'm ready to assist you with marketplace operations. What can I look up for you?`;
        } else {
            responseText = "I'm not sure I understand that yet. You can ask about sales, stock, or orders, and I'll pull the data for you!";
        }

        responseDiv.innerHTML = responseText;
        chatContainer.appendChild(responseDiv);
        chatContainer.scrollTop = chatContainer.scrollHeight;
        lucide.createIcons();
    }, 800);
}

function closeCustomModal() {
    const backdrop = document.getElementById('custom-modal-backdrop');
    if (backdrop) backdrop.classList.add('hidden');
}

// =====================================================
// LOAD RECENT TRANSACTIONS WITH NOTIFICATIONS
// =====================================================

let lastTransactionCount = 0;
let transactionAudioAlert = null;

async function loadRecentTransactions() {
    try {
        const response = await fetch('get_transaction_notifications.php');
        const data = await response.json();

        const container = document.getElementById('recent-transactions-container');
        if (!container) return;

        // Check for new transactions and show alert
        if (data.success && data.notifications && data.notifications.length > lastTransactionCount) {
            showTransactionAlert(data.notifications[0]);
            playTransactionSound();
            lastTransactionCount = data.notifications.length;
        }

        if (!data.success || !data.notifications || data.notifications.length === 0) {
            container.innerHTML = `
                <div style="padding: 3rem 2rem; text-align: center;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border-radius: 1rem; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="inbox" style="width: 3rem; height: 3rem; color: #0284c7;"></i>
                    </div>
                    <p style="font-size: 1.125rem; font-weight: 600; color: #1f2937; margin: 0 0 0.5rem 0;">All caught up! 🎉</p>
                    <p style="color: #6b7280; margin: 0;">No active transactions at the moment. New orders will appear here.</p>
                </div>
            `;
            lucide.createIcons();
            return;
        }

        let html = '<div style="background: white;">';
        
        data.notifications.forEach((notif, index) => {
            const statusStyles = getStatusStyle(notif.status);
            const isNew = index === 0;

            html += `
                <div class="transaction-card" data-id="${notif.id}" style="
                    padding: 1.5rem;
                    border-bottom: 1px solid #f1f5f9;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    transition: all 0.3s;
                    background: ${isNew ? 'linear-gradient(135deg, #fef3c7 0%, #fef9e7 100%)' : 'white'};
                    position: relative;
                    overflow: hidden;
                    ${isNew ? 'border-left: 4px solid #f59e0b;' : ''}
                    cursor: pointer;
                "
                    onmouseover="this.style.background='#f9fafb'; this.style.transform='translateX(4px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.05)'"
                    onmouseout="this.style.background='${isNew ? 'linear-gradient(135deg, #fef3c7 0%, #fef9e7 100%)' : 'white'}'; this.style.transform='translateX(0)'; this.style.boxShadow='none'"
                    onclick="showSubModule('order', 'orders')"
                >
                    <div style="flex: 1; display: flex; gap: 1rem;">
                        <div style="width: 50px; height: 50px; border-radius: 0.75rem; ${statusStyles.bgGradient}; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i data-lucide="${statusStyles.icon}" style="width: 1.5rem; height: 1.5rem; color: ${statusStyles.color};"></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                <strong style="color: #1f2937; font-size: 1rem;">${notif.order_number}</strong>
                                ${isNew ? '<span style="background: #f59e0b; color: white; font-size: 0.65rem; padding: 0.25rem 0.625rem; border-radius: 0.375rem; font-weight: 700;">NEW</span>' : ''}
                            </div>
                            <div style="color: #6b7280; font-size: 0.9rem; margin-bottom: 0.375rem;">
                                <i data-lucide="user" style="width: 0.85rem; height: 0.85rem; display: inline; margin-right: 0.375rem; vertical-align: middle;"></i>
                                ${notif.customer} - ${notif.email}
                            </div>
                            <div style="color: #6b7280; font-size: 0.875rem;">
                                <i data-lucide="box" style="width: 0.85rem; height: 0.85rem; display: inline; margin-right: 0.375rem; vertical-align: middle;"></i>
                                ${notif.product_name} (x${notif.quantity})
                            </div>
                        </div>
                    </div>
                    
                    <div style="text-align: right; display: flex; flex-direction: column; gap: 0.75rem; align-items: flex-end;">
                        <div style="font-weight: 800; font-size: 1.25rem; color: #10b981;">₱ ${parseFloat(notif.amount || 0).toLocaleString('en-PH', {minimumFractionDigits: 2})}</div>
                        <div style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.875rem; background: ${statusStyles.bgLight}; border: 1px solid ${statusStyles.borderColor}; color: ${statusStyles.color}; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 700;">
                            <i data-lucide="${statusStyles.statusIcon}" style="width: 0.9rem; height: 0.9rem;"></i>
                            ${notif.status}
                        </div>
                        <div style="color: #9ca3af; font-size: 0.8rem; font-weight: 600;">
                            <i data-lucide="clock" style="width: 0.85rem; height: 0.85rem; display: inline; margin-right: 0.25rem; vertical-align: middle;"></i>
                            ${notif.time_ago}
                        </div>
                    </div>
                </div>
            `;
        });

        html += '</div>';
        container.innerHTML = html;
        lucide.createIcons();
    } catch (error) {
        console.error('Error loading transactions:', error);
        const container = document.getElementById('recent-transactions-container');
        if (container) {
            container.innerHTML = `
                <div style="padding: 3rem 2rem; text-align: center;">
                    <div style="width: 80px; height: 80px; background: #fee2e2; border-radius: 1rem; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="alert-circle" style="width: 3rem; height: 3rem; color: #dc2626;"></i>
                    </div>
                    <p style="font-size: 1rem; color: #dc2626; font-weight: 600; margin: 0;">Unable to load transactions</p>
                </div>
            `;
        }
    }
}

function getStatusStyle(status) {
    const styles = {
        'Pending': { color: '#b45309', bgLight: '#fef3c7', borderColor: '#fcd34d', bgGradient: 'linear-gradient(135deg, #fef3c7 0%, #fef9e7 100%)', icon: 'clock', statusIcon: 'hourglass' },
        'Processing': { color: '#1e40af', bgLight: '#dbeafe', borderColor: '#93c5fd', bgGradient: 'linear-gradient(135deg, #dbeafe 0%, #f0f9ff 100%)', icon: 'zap', statusIcon: 'loader' },
        'Delivered': { color: '#15803d', bgLight: '#dcfce7', borderColor: '#86efac', bgGradient: 'linear-gradient(135deg, #dcfce7 0%, #f0fdf4 100%)', icon: 'check-circle', statusIcon: 'truck' },
        'Completed': { color: '#15803d', bgLight: '#dcfce7', borderColor: '#86efac', bgGradient: 'linear-gradient(135deg, #dcfce7 0%, #f0fdf4 100%)', icon: 'check-circle', statusIcon: 'check' },
        'Cancelled': { color: '#b91c1c', bgLight: '#fee2e2', borderColor: '#fca5a5', bgGradient: 'linear-gradient(135deg, #fee2e2 0%, #fef2f2 100%)', icon: 'x-circle', statusIcon: 'x' },
    };
    return styles[status] || styles['Pending'];
}

function showTransactionAlert(transaction) {
    const alertDiv = document.createElement('div');
    alertDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        border-left: 4px solid #f59e0b;
        max-width: 400px;
        animation: slideIn 0.4s ease-out;
        z-index: 10000;
    `;
    
    alertDiv.innerHTML = `
        <div style="display: flex; gap: 1rem;">
            <div style="width: 3rem; height: 3rem; background: linear-gradient(135deg, #fef3c7 0%, #fef9e7 100%); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i data-lucide="zap" style="width: 1.5rem; height: 1.5rem; color: #f59e0b;"></i>
            </div>
            <div style="flex: 1;">
                <p style="margin: 0 0 0.5rem 0; font-weight: 700; color: #1f2937; font-size: 1rem;">New Order! 🎉</p>
                <p style="margin: 0 0 0.375rem 0; color: #6b7280; font-size: 0.9rem;"><strong>${transaction.customer}</strong></p>
                <p style="margin: 0; color: #6b7280; font-size: 0.85rem;">₱ ${parseFloat(transaction.amount || 0).toLocaleString('en-PH', {minimumFractionDigits: 2})}</p>
            </div>
        </div>
        <style>
            @keyframes slideIn {
                from {
                    transform: translateX(400px);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        </style>
    `;
    
    document.body.appendChild(alertDiv);
    lucide.createIcons();
    
    setTimeout(() => {
        alertDiv.style.animation = 'slideOut 0.3s ease-out forwards';
        setTimeout(() => alertDiv.remove(), 300);
    }, 5000);
}

function playTransactionSound() {
    // Create a simple alert sound using Web Audio API
    try {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
        oscillator.frequency.setValueAtTime(600, audioContext.currentTime + 0.1);
        
        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode.gain.setValueAtTime(0, audioContext.currentTime + 0.1);
        
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.1);
    } catch (e) {
        console.log('Audio notification not available');
    }
}

// Auto-refresh transactions every 15 seconds for real-time monitoring
setInterval(loadRecentTransactions, 15000);

// Monitor support notifications and update badge
async function updateSupportNotifications() {
    try {
        const response = await fetch('get_unread_chat_count.php');
        const data = await response.json();
        
        const badge = document.getElementById('support-badge');
        if (badge && data.success && data.unread_count > 0) {
            badge.textContent = data.unread_count;
            badge.style.display = 'inline-block';
        } else if (badge) {
            badge.style.display = 'none';
        }
    } catch (error) {
        console.error('Error updating support notifications:', error);
    }
}

// Update support notifications on page load and every 10 seconds
updateSupportNotifications();
setInterval(updateSupportNotifications, 10000);