// --- UTILITY FUNCTIONS ---
function setPageTitle(title) {
    const contentHeader = document.querySelector('#content-container .page-header');
    if (contentHeader) {
        contentHeader.innerText = title;
    }
    const pageTitle = document.getElementById('page-title');
    if (pageTitle) pageTitle.innerText = title;
}

function setActiveNav(element) {
    document.querySelectorAll('.nav-menu a').forEach(item => {
        item.classList.remove('active-nav');
        const icon = item.querySelector('.chevron-icon');
        if (icon) {
            icon.classList.remove('rotate-90');
        }
    });
    if (element) element.classList.add('active-nav');
}

function toggleSubMenu(element, submenuId) {
    const submenu = document.getElementById(submenuId);
    if (!submenu) return;
    const icon = element.querySelector('.chevron-icon');

    submenu.classList.toggle('hidden');
    if (icon) icon.classList.toggle('rotate-90');

    if (event) event.preventDefault();
}

function closeCustomModal() {
    const modal = document.getElementById('custom-modal-backdrop');
    if (modal) modal.classList.add('hidden');
}

// Support Dashboard
function renderDashboard() {
    setPageTitle('Support Overview');
    const content = document.getElementById('content-container');

    // Safe access to data
    const openTickets = (window.supportTicketsData || []).filter(t => t.status === 'Open' || t.status === 'In Progress').length;
    const urgentTickets = (window.supportTicketsData || []).filter(t => t.priority === 'Urgent').length;
    const resolvedToday = (window.supportTicketsData || []).filter(t => t.status === 'Resolved' && new Date(t.updated_at).toDateString() === new Date().toDateString()).length;

    content.innerHTML = `
        <h2 class="page-header">Support Intelligence</h2>
        <div class="kpi-card-grid" style="margin-bottom: 2rem;">
            <div class="kpi-card kpi-yellow">
                <div class="card-content">
                    <div>
                        <p class="card-title">Pending Tickets</p>
                        <h2 class="card-value">${openTickets}</h2>
                    </div>
                </div>
            </div>
            <div class="kpi-card kpi-red">
                <div class="card-content">
                    <div>
                        <p class="card-title">Urgent Attn</p>
                        <h2 class="card-value">${urgentTickets}</h2>
                    </div>
                </div>
            </div>
            <div class="kpi-card kpi-green">
                <div class="card-content">
                    <div>
                        <p class="card-title">Resolved Today</p>
                        <h2 class="card-value">${resolvedToday}</h2>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="kpi-card p-6">
            <h3 class="text-xl font-semibold mb-4">Quick Links</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <button class="btn-base btn-primary" onclick="showSubModule('support', 'tickets')">Handle Tickets</button>
                <button class="btn-base btn-indigo" onclick="showSubModule('support', 'chat')">Open Store Chat</button>
            </div>
        </div>
    `;
    lucide.createIcons();
}

// Support Tickets Module
function renderSupportModule(submodule = 'tickets') {
    activeSubmodule = submodule;
    activeModule = 'support';

    if (submodule === 'chat') {
        renderChatModule();
        return;
    }

    setPageTitle('Support Tickets');
    const content = document.getElementById('content-container');

    const ticketRows = (window.supportTicketsData || []).map(t => {
        const statusClass = t.status === 'Resolved' ? 'active' : (t.status === 'In Progress' ? 'processing' : (t.status === 'Closed' ? 'inactive' : 'pending'));
        const priorityClass = t.priority === 'Urgent' ? 'critical-stock' : (t.priority === 'High' ? 'low-stock' : 'active');
        return `
            <tr>
                <td>${t.ticket_number}</td>
                <td>${t.customer_name || 'N/A'}</td>
                <td>${t.subject}</td>
                <td><span class="status-badge ${statusClass}">${t.status}</span></td>
                <td><span class="status-badge ${priorityClass}">${t.priority}</span></td>
                <td>${new Date(t.created_at).toLocaleDateString()}</td>
                <td>
                    <button class="btn-base" style="padding: 0.25rem 0.5rem; background-color: var(--color-light-grey);" onclick="showTicketDetails(${t.id})">
                        <i data-lucide="message-circle" style="width: 1rem; height: 1rem;"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');

    content.innerHTML = `
        <h2 class="page-header">Ticket Management</h2>
        <div class="kpi-card p-6">
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Ticket #</th>
                            <th>Customer</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${(window.supportTicketsData || []).length > 0 ? ticketRows : `<tr><td colspan="7" class="text-center py-4">No tickets assigned.</td></tr>`}
                    </tbody>
                </table>
            </div>
        </div>
    `;
    lucide.createIcons();
}

async function fetchSupportTickets() {
    try {
        const res = await fetch('get_support_tickets.php');
        const data = await res.json();
        if (data.success) {
            window.supportTicketsData = data.tickets;
            // Only re-render if we are currently looking at tickets list to avoid wiping modal state
            if (activeSubmodule === 'tickets' && !document.getElementById('ai-modal-overlay')?.style.display?.includes('flex')) {
                renderSupportModule('tickets');
            }
        }
    } catch (e) { console.error('Error fetching tickets:', e); }
}

async function showTicketDetails(ticketId) {
    const ticket = window.supportTicketsData.find(t => t.id == ticketId);
    if (!ticket) return;

    // Fetch replies logic...
    let repliesHtml = '';
    try {
        const res = await fetch(`get_ticket_replies.php?ticket_id=${ticket.id}`);
        const data = await res.json();
        if (data.success && data.replies.length > 0) {
            repliesHtml = `
                <div style="margin-top: 1rem;">
                    <p><strong>Conversation History:</strong></p>
                    <div style="max-height: 200px; overflow-y: auto; background: #f8fafc; border: 1px solid #eef2f6; border-radius: 0.5rem; padding: 1rem; display: flex; flex-direction: column; gap: 0.75rem; margin-top: 0.5rem;">
                        ${data.replies.map(r => `
                            <div style="align-self: ${r.sender_type === 'admin' ? 'flex-end' : 'flex-start'}; background: ${r.sender_type === 'admin' ? '#3b82f6' : '#fff'}; color: ${r.sender_type === 'admin' ? '#fff' : '#333'}; padding: 0.75rem; border-radius: 0.75rem; border: ${r.sender_type === 'admin' ? 'none' : '1px solid #eef2f6'}; max-width: 85%; font-size: 0.9rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                <strong>${r.sender_type === 'admin' ? 'Support' : 'Customer'}:</strong> ${r.message}
                                <div style="font-size: 0.75rem; opacity: 0.7; margin-top: 0.25rem; text-align: right;">${new Date(r.created_at).toLocaleString()}</div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        }
    } catch (e) { console.error(e); }

    const modalOverlay = document.getElementById('ai-modal-overlay') || createModalElement();
    const modalContent = document.getElementById('ai-modal-content-inject');

    if (modalContent) {
        modalContent.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 style="margin: 0;">Ticket Details</h2>
                <button onclick="closeSupportModal()" style="background: none; border: none; cursor: pointer; color: #64748b;"><i data-lucide="x"></i></button>
            </div>
            <form action="dashboard.php" method="POST">
                <input type="hidden" name="action" value="update_ticket">
                <input type="hidden" name="id" value="${ticket.id}">
                <input type="hidden" name="module" value="support">
                <div style="margin-bottom: 1.5rem;">
                    <p><strong>Ticket:</strong> ${ticket.ticket_number}</p>
                    <p><strong>Customer:</strong> ${ticket.customer_name}</p>
                    <p><strong>Original Message:</strong></p>
                    <div style="padding: 1rem; background: #fffcf0; border: 1px solid #fef3c7; border-radius: 0.5rem; margin-top: 0.5rem; font-size: 0.95rem;">${ticket.message}</div>
                    ${repliesHtml}
                    <div class="form-group" style="margin-top: 1.5rem;">
                        <label style="font-weight: 600; font-size: 0.9rem; margin-bottom: 0.5rem; display: block;">Send a new reply</label>
                        <textarea name="reply_message" rows="3" placeholder="Type your response here..." style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 0.5rem; outline: none;" onfocus="this.style.borderColor='#3b82f6'"></textarea>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <button type="submit" class="btn-base btn-primary" style="padding: 0.75rem; border-radius: 0.5rem;">Update & Send</button>
                    <button type="button" class="btn-base btn-secondary" onclick="closeSupportModal()" style="padding: 0.75rem; border-radius: 0.5rem;">Close</button>
                </div>
            </form>
        `;
        modalOverlay.style.display = 'flex';
        lucide.createIcons();
    }
}

function createModalElement() {
    const div = document.createElement('div');
    div.id = 'ai-modal-overlay';
    div.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: none; justify-content: center; align-items: center; z-index: 1000;';
    div.innerHTML = '<div id="ai-modal-content-inject" style="background: white; padding: 2rem; border-radius: 12px; width: 90%; max-width: 600px; max-height: 90vh; overflow-y: auto;"></div>';
    document.body.appendChild(div);
    return div;
}

function closeSupportModal() {
    const el = document.getElementById('ai-modal-overlay');
    if (el) el.style.display = 'none';
}

// -----------------------------------------------------
// ENHANCED CHAT MODULE
// -----------------------------------------------------
let activeChatUser = null;
let activeChatStore = null;

function renderChatModule() {
    activeModule = 'chat';
    activeSubmodule = '';
    setPageTitle('Store Chat Hub');
    const content = document.getElementById('content-container');

    content.innerHTML = `
        <div class="chat-container" style="height: calc(100vh - 140px); background: #ffffff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); overflow: hidden; display: flex; border: 1px solid #e2e8f0;">
            <!-- Left Sidebar (Chat List) -->
            <div style="width: 320px; border-right: 1px solid #f1f5f9; display: flex; flex-direction: column; background: #fff;">
                <div style="padding: 1.5rem; border-bottom: 1px solid #f1f5f9; background: #fff;">
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; letter-spacing: -0.025em;">Messages</h3>
                    <div style="margin-top: 1rem; position: relative;">
                        <i data-lucide="search" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); width: 1rem; height: 1rem; color: #94a3b8;"></i>
                        <input type="text" placeholder="Search conversations..." style="width: 100%; padding: 0.75rem 1rem 0.75rem 2.5rem; background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 12px; font-size: 0.875rem; outline: none; transition: all 0.2s;" onfocus="this.style.borderColor='#3b82f6'; this.style.background='#fff'">
                    </div>
                </div>
                <div id="admin-chat-list" style="flex: 1; overflow-y: auto; scroll-behavior: smooth;">
                    <!-- Chat list items injected here -->
                    <div style="padding: 2rem; text-align: center; color: #94a3b8;">
                        <div class="loader-spinner" style="margin: 0 auto 1rem; width: 24px; height: 24px; border-width: 3px; border-color: #cbd5e1; border-top-color: #3b82f6;"></div>
                        Loading...
                    </div>
                </div>
            </div>

            <!-- Right Content (Conversation) -->
            <div style="flex: 1; display: flex; flex-direction: column; background: #f8fafc; position: relative;">
                
                <!-- Chat Header -->
                <div id="admin-chat-header" style="padding: 1rem 1.75rem; background: #fff; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; height: 76px; display: none;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div id="chat-user-avatar" style="width: 2.75rem; height: 2.75rem; border-radius: 50%; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.1rem; box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);">?</div>
                        <div>
                            <div id="chat-user-name" style="font-weight: 700; font-size: 1rem; color: #0f172a;">Select a chat</div>
                            <div id="chat-store-name" style="font-size: 0.8rem; color: #64748b; display: flex; align-items: center; gap: 0.25rem;">
                                <span style="width: 6px; height: 6px; background: #22c55e; border-radius: 50%;"></span> Active Now
                            </div>
                        </div>
                    </div>
                    <div style="display: flex; gap: 0.5rem;">
                        <button class="btn-icon-only" title="View Profile"><i data-lucide="user"></i></button>
                        <button class="btn-icon-only" title="More Options"><i data-lucide="more-vertical"></i></button>
                    </div>
                </div>

                <!-- Messages Area -->
                <div id="admin-chat-messages" style="flex: 1; overflow-y: auto; padding: 2rem; display: flex; flex-direction: column; gap: 1.5rem; scroll-behavior: smooth;">
                    <div style="height: 100%; display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 1.5rem; color: #94a3b8;">
                        <div style="width: 80px; height: 80px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="message-square" style="width: 40px; height: 40px; color: #cbd5e1;"></i>
                        </div>
                        <div style="text-align: center;">
                            <h3 style="color: #475569; font-weight: 600; margin-bottom: 0.5rem;">No conversation selected</h3>
                            <p style="font-size: 0.9rem;">Choose a customer from the left to start support.</p>
                        </div>
                    </div>
                </div>

                <!-- Input Area -->
                <div id="admin-chat-input-container" style="padding: 1.5rem; background: #fff; border-top: 1px solid #f1f5f9; display: none;">
                    <form onsubmit="sendAdminChatReply(event)" style="display: flex; gap: 1rem; align-items: center;">
                        <button type="button" class="btn-icon-only" title="Attach file"><i data-lucide="paperclip" style="width: 1.25rem; height: 1.25rem;"></i></button>
                        <div style="flex: 1; position: relative;">
                            <input type="text" id="admin-chat-input" placeholder="Type your message..." style="width: 100%; padding: 0.875rem 1.25rem; border: 1px solid #e2e8f0; border-radius: 9999px; outline: none; transition: all 0.2s; background: #f8fafc; font-size: 0.95rem;" onfocus="this.style.background='#fff'; this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59, 130, 246, 0.1)'" onblur="this.style.background='#f8fafc'; this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                        </div>
                        <button type="submit" style="width: 3.25rem; height: 3.25rem; border-radius: 50%; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                            <i data-lucide="send" style="width: 1.25rem; height: 1.25rem; margin-left: 2px;"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <style>
            .btn-icon-only { width: 2.5rem; height: 2.5rem; display: flex; align-items: center; justify-content: center; border-radius: 8px; border: none; background: transparent; color: #64748b; cursor: pointer; transition: all 0.2s; }
            .btn-icon-only:hover { background: #f1f5f9; color: #1e293b; }
            .message-bubble { max-width: 70%; padding: 1rem 1.25rem; font-size: 0.95rem; line-height: 1.5; position: relative; word-wrap: break-word; }
            .message-admin { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; border-radius: 18px 18px 4px 18px; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15); margin-left: auto; }
            .message-customer { background: white; color: #1e293b; border: 1px solid #e2e8f0; border-radius: 18px 18px 18px 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
            .chat-list-item:hover { background: #f8fafc; }
            .chat-list-item.active { background: #eff6ff; border-right: 3px solid #3b82f6; }
        </style>
    `;
    loadAdminChatList();
    lucide.createIcons();
}

async function loadAdminChatList() {
    try {
        const res = await fetch('get_chat_list.php');
        const data = await res.json();
        const listContainer = document.getElementById('admin-chat-list');

        if (data.success && data.chats.length > 0) {
            listContainer.innerHTML = data.chats.map((chat) => {
                const safeCustomerName = chat.customer_name || 'Anonymous User';
                const storeName = chat.store_name || 'General Support';
                const timeStr = chat.time_ago || (chat.last_time ? new Date(chat.last_time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '');

                // Escape double quotes for data attributes
                const storeNameSafe = storeName.replace(/"/g, '&quot;');
                const customerNameSafe = safeCustomerName.replace(/"/g, '&quot;');

                const isActive = (activeChatUser == chat.user_id && activeChatStore === storeName);

                return `
                <div class="chat-list-item ${isActive ? 'active' : ''}" 
                     data-user-id="${chat.user_id}" 
                     data-store-name="${storeNameSafe}" 
                     data-customer-name="${customerNameSafe}"
                     style="padding: 1.25rem; border-bottom: 1px solid #f3f4f6; cursor: pointer; transition: all 0.2s;">
                    <div style="display: flex; gap: 1rem; align-items: start;">
                        <div style="width: 3rem; height: 3rem; border-radius: 50%; background: ${chat.unread_count > 0 ? 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)' : '#e2e8f0'}; display: flex; align-items: center; justify-content: center; color: ${chat.unread_count > 0 ? 'white' : '#64748b'}; font-weight: 700; font-size: 1.1rem; flex-shrink: 0; box-shadow: ${chat.unread_count > 0 ? '0 4px 6px rgba(59, 130, 246, 0.3)' : 'none'};">
                            ${safeCustomerName.charAt(0).toUpperCase()}
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem;">
                                <div style="font-weight: 700; font-size: 0.95rem; color: #1e293b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${safeCustomerName}</div>
                                <span style="font-size: 0.75rem; color: #94a3b8; font-weight: 500;">${timeStr}</span>
                            </div>
                            <div style="font-size: 0.8rem; color: #64748b; margin-bottom: 0.35rem; display: flex; align-items: center; gap: 4px;">
                                <i data-lucide="store" style="width: 12px; height: 12px;"></i> ${storeName}
                            </div>
                            <div style="font-size: 0.85rem; color: ${chat.unread_count > 0 ? '#1e293b' : '#94a3b8'}; font-weight: ${chat.unread_count > 0 ? '600' : '400'}; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                ${chat.last_message ? chat.last_message : '<span style="font-style:italic;">No messages</span>'}
                            </div>
                        </div>
                        ${chat.unread_count > 0 ? `<div style="width: 10px; height: 10px; background: #ef4444; border-radius: 50%; margin-top: 6px; box-shadow: 0 0 0 3px white;"></div>` : ''}
                    </div>
                </div>
            `;
            }).join('');

            document.querySelectorAll('.chat-list-item').forEach(item => {
                item.addEventListener('click', function () {
                    const uid = this.getAttribute('data-user-id');
                    const store = this.getAttribute('data-store-name');
                    const name = this.getAttribute('data-customer-name');
                    selectChat(uid, store, name);
                });
            });

            // Auto-select first chat if none active
            if (!activeChatUser && data.chats.length > 0) {
                const first = data.chats[0];
                const sName = first.store_name || 'General Support';
                const cName = first.customer_name || 'Anonymous User';
                selectChat(first.user_id, sName, cName);
            }

            lucide.createIcons();

        } else {
            listContainer.innerHTML = `
                <div style="padding: 3rem 1.5rem; text-align: center; color: #94a3b8;">
                    <i data-lucide="message-square-off" style="width: 48px; height: 48px; margin-bottom: 1rem; opacity: 0.5;"></i>
                    <p>No active conversations found.</p>
                </div>`;
            lucide.createIcons();
        }
    } catch (e) { console.error('Error loading chats:', e); }
}

function selectChat(userId, storeName, customerName) {
    activeChatUser = userId;
    activeChatStore = storeName;
    const displayName = customerName || 'Anonymous User';

    // Toggle visibility
    const header = document.getElementById('admin-chat-header');
    const inputContainer = document.getElementById('admin-chat-input-container');
    const messagesContainer = document.getElementById('admin-chat-messages');

    if (header) {
        header.style.display = 'flex';
        header.style.animation = 'fadeIn 0.2s ease-out';
    }
    if (inputContainer) {
        inputContainer.style.display = 'block';
        inputContainer.style.animation = 'slideUp 0.2s ease-out';
    }

    // Update Header Info
    document.getElementById('chat-user-name').textContent = displayName;
    // document.getElementById('chat-store-name').innerHTML = `<span style="width: 6px; height: 6px; background: #22c55e; border-radius: 50%;"></span> ${storeName}`;
    document.getElementById('chat-user-avatar').textContent = displayName.charAt(0).toUpperCase();

    // Show loading state
    messagesContainer.innerHTML = `
        <div style="height: 100%; display: flex; align-items: center; justify-content: center; flex-direction: column; color: #94a3b8;">
            <div class="loader-spinner" style="margin-bottom: 1rem; border-width: 3px; width: 32px; height: 32px;"></div>
            <p>Loading conversation...</p>
        </div>
    `;

    loadAdminMessages();

    // Highlight active item
    document.querySelectorAll('.chat-list-item').forEach(item => {
        item.classList.remove('active');
        if (item.getAttribute('data-user-id') == userId && item.getAttribute('data-store-name') == storeName) {
            item.classList.add('active');
        }
    });

    // Auto-focus input
    setTimeout(() => document.getElementById('admin-chat-input').focus(), 100);
}

async function loadAdminMessages() {
    if (!activeChatUser || !activeChatStore) return;
    try {
        const res = await fetch(`get_admin_chat_messages.php?user_id=${activeChatUser}&store_name=${encodeURIComponent(activeChatStore)}`);
        const data = await res.json();
        const msgContainer = document.getElementById('admin-chat-messages');

        if (data.success) {
            if (data.messages.length === 0) {
                msgContainer.innerHTML = `
                    <div style="text-align: center; margin-top: 3rem; color: #94a3b8;">
                        <p>No messages yet.</p>
                        <p style="font-size: 0.85rem;">Start the conversation with ${activeChatStore}!</p>
                    </div>`;
            } else {
                msgContainer.innerHTML = data.messages.map(m => {
                    const isAdmin = m.sender_type === 'admin';
                    return `
                    <div style="display: flex; flex-direction: column; align-items: ${isAdmin ? 'flex-end' : 'flex-start'}; margin-bottom: 0.5rem;">
                         <div class="message-bubble ${isAdmin ? 'message-admin' : 'message-customer'}">
                            ${m.message}
                        </div>
                        <span style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.35rem; margin-right: 0.5rem; margin-left: 0.5rem;">
                            ${m.timestamp || (m.created_at ? new Date(m.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '')}
                        </span>
                    </div>
                `}).join('');
                // Scroll to bottom
                msgContainer.scrollTop = msgContainer.scrollHeight;
            }
        }
    } catch (e) {
        console.error(e);
        const msgContainer = document.getElementById('admin-chat-messages');
        if (msgContainer) msgContainer.innerHTML = '<div style="text-align:center; color: red;">Error loading messages.</div>';
    }
}

async function sendAdminChatReply(event) {
    event.preventDefault();
    const input = document.getElementById('admin-chat-input');
    const msg = input.value.trim();
    if (!msg || !activeChatUser) return;

    // Optimistic UI update
    const msgContainer = document.getElementById('admin-chat-messages');
    const tempDiv = document.createElement('div');
    tempDiv.style.cssText = "display: flex; flex-direction: column; align-items: flex-end; margin-bottom: 0.5rem; opacity: 0.7;";
    tempDiv.innerHTML = `
        <div class="message-bubble message-admin">${msg}</div>
        <span style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.35rem; margin-right: 0.5rem;">Sending...</span>
    `;
    msgContainer.appendChild(tempDiv);
    msgContainer.scrollTop = msgContainer.scrollHeight;

    const formData = new FormData();
    formData.append('user_id', activeChatUser);
    formData.append('store_name', activeChatStore);
    formData.append('message', msg);

    input.value = '';

    try {
        const res = await fetch('send_chat_reply.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
            loadAdminMessages(); // Refresh to show real frame
        } else {
            tempDiv.innerHTML = `<div style="color: red; font-size: 0.8rem;">Failed to send. Retrying...</div>`;
        }
    } catch (e) { console.error(e); }
}

// Customers Module
function renderCustomersModule() {
    setPageTitle('Customer Directory');
    const content = document.getElementById('content-container');

    const customerRows = (window.customersData || []).map(c => {
        const statusClass = c.status === 'Active' ? 'active' : (c.status === 'Banned' ? 'cancelled' : 'inactive');
        return `
            <tr>
                <td>${c.full_name}</td>
                <td>${c.email}</td>
                <td>${c.phone_number || 'N/A'}</td>
                <td>${c.total_orders || 0}</td>
                <td><span class="status-badge ${statusClass}">${c.status}</span></td>
                <td>${new Date(c.created_at).toLocaleDateString()}</td>
                <td>
                    <button class="btn-base" style="padding: 0.25rem 0.5rem; background-color: var(--color-light-grey);" onclick="startChatWithCustomer(${c.id}, '${c.full_name}')">
                        <i data-lucide="message-circle" style="width: 1rem; height: 1rem;"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');

    content.innerHTML = `
        <h2 class="page-header">Customer Management</h2>
        <div class="kpi-card p-6">
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Orders</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${(window.customersData || []).length > 0 ? customerRows : `<tr><td colspan="7" class="text-center py-4">No customers found.</td></tr>`}
                    </tbody>
                </table>
            </div>
        </div>
    `;
    lucide.createIcons();
}

function startChatWithCustomer(userId, customerName) {
    renderChatModule();
    // Default store name for support-initiated chats
    activeChatUser = userId;
    activeChatStore = "General Support";
    selectChat(userId, activeChatStore, customerName);
}

// Navigation Router
function showModule(module, element) {
    activeModule = module;
    activeSubmodule = '';
    if (element) setActiveNav(element);
    if (module === 'dashboard') renderDashboard();
    else if (module === 'customers') renderCustomersModule();
    else if (module === 'alerts') renderAlertsModule();
}

function showSubModule(module, submodule) {
    activeModule = module;
    activeSubmodule = submodule;
    if (module === 'support') renderSupportModule(submodule);
}

// Polling
setInterval(() => {
    if (activeSubmodule === 'tickets') fetchSupportTickets();
    if (activeModule === 'chat') {
        loadAdminChatList();
        if (activeChatUser) loadAdminMessages();
    }
}, 3000);

// Helper for initial data if not defined
window.supportTicketsData = window.supportTicketsData || [];
window.customersData = window.customersData || [];

let activeModule = 'dashboard';
let activeSubmodule = '';

// Initial Load
document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const module = urlParams.get('module') || 'dashboard';
    const submodule = urlParams.get('submodule');

    if (module === 'support') {
        showSubModule(module, submodule || 'tickets');
        // If chat is requested, ensure we render it
        if ((submodule || 'tickets') === 'chat') renderChatModule();
    } else {
        renderDashboard();
    }
});
