// --- UTILITY FUNCTIONS ---
function setPageTitle(title) {
    const contentHeader = document.querySelector('#content-container .page-header');
    if (contentHeader) {
        contentHeader.innerText = title;
    }
    document.getElementById('page-title').innerText = title;
}

function setActiveNav(element) {
    document.querySelectorAll('.nav-menu a').forEach(item => {
        item.classList.remove('active-nav');
        const icon = item.querySelector('.chevron-icon');
        if (icon) {
            icon.classList.remove('rotate-90');
        }
    });
    element.classList.add('active-nav');
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
    modal.classList.add('hidden');
}

// Support Dashboard
function renderDashboard() {
    setPageTitle('Support Overview');
    const content = document.getElementById('content-container');

    const openTickets = supportTicketsData.filter(t => t.status === 'Open' || t.status === 'In Progress').length;
    const urgentTickets = supportTicketsData.filter(t => t.priority === 'Urgent').length;
    const resolvedToday = supportTicketsData.filter(t => t.status === 'Resolved' && new Date(t.updated_at).toDateString() === new Date().toDateString()).length;

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

    const ticketRows = supportTicketsData.map(t => {
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
                        ${supportTicketsData.length > 0 ? ticketRows : `<tr><td colspan="7" class="text-center py-4">No tickets assigned.</td></tr>`}
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
            if (activeSubmodule === 'tickets') {
                renderSupportModule('tickets');
            }
        }
    } catch (e) { console.error('Error fetching tickets:', e); }
}

async function showTicketDetails(ticketId) {
    const ticket = supportTicketsData.find(t => t.id == ticketId);
    if (!ticket) return;

    // Fetch replies before showing modal
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

    const modalOverlay = document.getElementById('ai-modal-overlay'); // Using the site-wide AI modal overlay if suitable
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
                    <div class="form-group">
                        <label style="display: block; font-size: 0.8rem; margin-bottom: 0.3rem;">Status</label>
                        <select name="status" style="width: 100%; border-radius: 0.5rem; padding: 0.5rem; border: 1px solid #ddd;">
                            <option value="Open" ${ticket.status === 'Open' ? 'selected' : ''}>Open</option>
                            <option value="In Progress" ${ticket.status === 'In Progress' ? 'selected' : ''}>In Progress</option>
                            <option value="Resolved" ${ticket.status === 'Resolved' ? 'selected' : ''}>Resolved</option>
                            <option value="Closed" ${ticket.status === 'Closed' ? 'selected' : ''}>Closed</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="display: block; font-size: 0.8rem; margin-bottom: 0.3rem;">Update Priority</label>
                        <select name="priority" style="width: 100%; border-radius: 0.5rem; padding: 0.5rem; border: 1px solid #ddd;">
                            <option value="Low" ${ticket.priority === 'Low' ? 'selected' : ''}>Low</option>
                            <option value="Medium" ${ticket.priority === 'Medium' ? 'selected' : ''}>Medium</option>
                            <option value="High" ${ticket.priority === 'High' ? 'selected' : ''}>High</option>
                            <option value="Urgent" ${ticket.priority === 'Urgent' ? 'selected' : ''}>Urgent</option>
                        </select>
                    </div>
                </div>
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn-base btn-primary" style="flex: 1; padding: 0.75rem; border-radius: 0.5rem; border: none; background: #3b82f6; color: white; cursor: pointer; font-weight: 600;">Update & Send Reply</button>
                    <button type="button" class="btn-base btn-secondary" onclick="closeSupportModal()" style="flex: 1; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid #ddd; background: white; cursor: pointer;">Close</button>
                </div>
            </form>
        `;
        modalOverlay.style.display = 'flex';
        lucide.createIcons();
    }
}

function closeSupportModal() {
    document.getElementById('ai-modal-overlay').style.display = 'none';
}

// Chat Module
let activeChatUser = null;
let activeChatStore = null;

function renderChatModule() {
    activeModule = 'chat';
    activeSubmodule = '';
    setPageTitle('Store Chat Hub');
    const content = document.getElementById('content-container');

    content.innerHTML = `
        <div class="flex" style="height: calc(100vh - 180px); background: #fff; border-radius: 0.75rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden;">
            <div style="width: 320px; border-right: 1px solid #f3f4f6; display: flex; flex-direction: column;">
                <div style="padding: 1.25rem; border-bottom: 1px solid #f3f4f6;">
                    <h3 style="font-size: 1.1rem; font-weight: 700;">Customer Inquiries</h3>
                </div>
                <div id="admin-chat-list" style="flex: 1; overflow-y: auto;">
                    <div style="padding: 2rem; text-align: center; color: #94a3b8;">Loading conversations...</div>
                </div>
            </div>
            <div style="flex: 1; display: flex; flex-direction: column; background: #f9fafb;">
                <div id="admin-chat-header" style="padding: 1rem 1.5rem; background: #fff; border-bottom: 1px solid #f3f4f6; display: none;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div id="chat-user-avatar" style="width: 2.5rem; height: 2.5rem; border-radius: 50%; background: #3b82f6; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">?</div>
                        <div>
                            <div id="chat-user-name" style="font-weight: 600; font-size: 0.9375rem;">Select a chat</div>
                            <div id="chat-store-name" style="font-size: 0.75rem; color: #64748b;">to start messaging</div>
                        </div>
                    </div>
                </div>
                <div id="admin-chat-messages" style="flex: 1; overflow-y: auto; padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
                    <div style="height: 100%; display: flex; align-items: center; justify-content: center; color: #94a3b8; flex-direction: column; gap: 1rem;">
                        <i data-lucide="message-square" style="width: 4rem; height: 4rem; opacity: 0.2;"></i>
                        <p>Open a conversation to start helping customers</p>
                    </div>
                </div>
                <div id="admin-chat-input-container" style="padding: 1.25rem; background: #fff; border-top: 1px solid #f3f4f6; display: none;">
                    <form onsubmit="sendAdminChatReply(event)" style="display: flex; gap: 0.75rem;">
                        <input type="text" id="admin-chat-input" placeholder="Type your response here..." style="flex: 1; padding: 0.75rem 1rem; border: 1px solid #e2e8f0; border-radius: 9999px; outline: none; transition: all 0.2s;" onfocus="this.style.borderColor='#3b82f6'">
                        <button type="submit" style="width: 3rem; height: 3rem; border-radius: 50%; background: #3b82f6; color: white; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;">
                            <i data-lucide="send" style="width: 1.25rem; height: 1.25rem;"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
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
            listContainer.innerHTML = data.chats.map(chat => {
                const safeCustomerName = chat.customer_name || 'Anonymous User';
                const safeStoreName = (chat.store_name || 'General Support').replace(/'/g, "\\'");
                const displayName = safeCustomerName.replace(/'/g, "\\'");

                return `
                <div onclick="selectChat(${chat.user_id}, '${safeStoreName}', '${displayName}')" style="padding: 1rem 1.25rem; border-bottom: 1px solid #f3f4f6; cursor: pointer; transition: background 0.2s; ${chat.unread_count > 0 ? 'background: #eff6ff;' : ''}" onmouseover="this.style.background='#f8fafc'">
                    <div style="display: flex; gap: 0.75rem; align-items: center;">
                        <div style="width: 2.5rem; height: 2.5rem; border-radius: 50%; background: ${chat.unread_count > 0 ? '#3b82f6' : '#94a3b8'}; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; flex-shrink: 0;">
                            ${safeCustomerName.charAt(0).toUpperCase()}
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2px;">
                                <div style="font-weight: 600; font-size: 0.875rem; color: #1e293b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${safeCustomerName}</div>
                                <span style="font-size: 0.7rem; color: #94a3b8;">${chat.time_ago || (chat.last_time ? new Date(chat.last_time).toLocaleTimeString() : '')}</span>
                            </div>
                            <div style="font-size: 0.8rem; color: #64748b; margin-bottom: 2px;">Store: ${chat.store_name}</div>
                            <div style="font-size: 0.75rem; color: ${chat.unread_count > 0 ? '#1e293b' : '#94a3b8'}; font-weight: ${chat.unread_count > 0 ? '600' : '400'}; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${chat.last_message}</div>
                        </div>
                        ${chat.unread_count > 0 ? `<div style="width: 10px; height: 10px; background: #3b82f6; border-radius: 50%;"></div>` : ''}
                    </div>
                </div>
            `;
            }).join('');
        } else {
            listContainer.innerHTML = '<div style="padding: 2rem; text-align: center; color: #94a3b8;">No active chats found.</div>';
        }
    } catch (e) { console.error(e); }
}

function selectChat(userId, storeName, customerName) {
    activeChatUser = userId;
    activeChatStore = storeName;
    const displayName = customerName || 'Anonymous User';

    document.getElementById('admin-chat-header').style.display = 'block';
    document.getElementById('admin-chat-input-container').style.display = 'block';
    document.getElementById('chat-user-name').textContent = displayName;
    document.getElementById('chat-store-name').textContent = "Inquiry regarding " + storeName;
    document.getElementById('chat-user-avatar').textContent = displayName.charAt(0).toUpperCase();

    loadAdminMessages();
}

async function loadAdminMessages() {
    if (!activeChatUser || !activeChatStore) return;
    try {
        const res = await fetch(`get_admin_chat_messages.php?user_id=${activeChatUser}&store_name=${encodeURIComponent(activeChatStore)}`);
        const data = await res.json();
        const msgContainer = document.getElementById('admin-chat-messages');

        if (data.success) {
            msgContainer.innerHTML = data.messages.map(m => `
                <div style="display: flex; flex-direction: column; align-items: ${m.sender_type === 'admin' ? 'flex-end' : 'flex-start'};">
                    <div style="max-width: 75%; padding: 0.75rem 1rem; border-radius: 1rem; font-size: 0.9375rem; ${m.sender_type === 'admin' ? 'background: #3b82f6; color: white; border-bottom-right-radius: 0.25rem;' : 'background: white; color: #1e293b; border-bottom-left-radius: 0.25rem; border: 1px solid #e2e8f0;'}">
                        ${m.message}
                    </div>
                    <span style="font-size: 0.7rem; color: #94a3b8; margin-top: 0.25rem;">${m.timestamp || (m.created_at ? new Date(m.created_at).toLocaleString() : '')}</span>
                </div>
            `).join('');
            msgContainer.scrollTop = msgContainer.scrollHeight;
        }
    } catch (e) { console.error(e); }
}

async function sendAdminChatReply(event) {
    event.preventDefault();
    const input = document.getElementById('admin-chat-input');
    const msg = input.value.trim();
    if (!msg || !activeChatUser) return;

    const formData = new FormData();
    formData.append('user_id', activeChatUser);
    formData.append('store_name', activeChatStore);
    formData.append('message', msg);

    input.value = '';
    try {
        const res = await fetch('send_chat_reply.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) loadAdminMessages();
    } catch (e) { console.error(e); }
}

function renderCustomersModule() {
    setPageTitle('Customer Directory');
    const content = document.getElementById('content-container');

    const customerRows = customersData.map(c => {
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
                        <i data-lucide="message-square" style="width: 1rem; height: 1rem;"></i>
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
                        ${customersData.length > 0 ? customerRows : `<tr><td colspan="7" class="text-center py-4">No customers found.</td></tr>`}
                    </tbody>
                </table>
            </div>
        </div>
    `;
    lucide.createIcons();
}

function startChatWithCustomer(userId, customerName) {
    // Redirect to chat and select this user
    renderChatModule();
    // In a real system, we'd need to know the store. For now, we'll try to find an existing chat or start a new one.
    // This is a simplified implementation.
    activeChatUser = userId;
    activeChatStore = "General Support"; // Default store name for support-initiated chats
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

// Polling for updates
setInterval(() => {
    if (activeSubmodule === 'tickets') fetchSupportTickets();
    if (activeModule === 'chat') {
        loadAdminChatList(); // Renamed from fetchChatList to match existing function
        if (activeChatUser) loadAdminMessages();
    }
}, 5000);

let activeModule = 'dashboard';
let activeSubmodule = '';

// Initial Load
document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const module = urlParams.get('module') || 'dashboard';
    const submodule = urlParams.get('submodule');

    if (module === 'support') renderSupportModule(submodule || 'tickets');
    else renderDashboard();

    lucide.createIcons();
});
