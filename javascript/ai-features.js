let videoStream = null;

function openCameraSearch() {
    const modal = document.getElementById('ai-modal-overlay');
    const content = document.getElementById('ai-modal-content-inject');

    modal.style.display = 'flex';
    content.innerHTML = `
        <div class="ai-modal-header">
            <h3 class="ai-modal-title"><i class="fas fa-camera"></i> Image Search</h3>
            <span class="ai-modal-close" onclick="closeAiModal()">&times;</span>
        </div>
        <div class="ai-modal-body">
            <div class="camera-preview-box" id="camera-view">
                <video id="camera-stream" autoplay playsinline muted style="width:100%; height:100%; object-fit:cover; display:none;"></video>
                <img id="image-preview" style="display:none; width:100%; height:100%; object-fit:cover;">
                
                <div id="camera-placeholder">
                    <i class="fas fa-image" style="font-size: 40px; margin-bottom: 10px;"></i>
                    <p>Starting Camera...</p>
                </div>

                <!-- Scanner Overlay -->
                <div class="scanner-overlay" id="scanner-ui">
                    <div class="scanner-line"></div>
                </div>
            </div>
            
            <p style="color:#666; font-size: 0.9rem;" id="camera-status">Point at a product to search.</p>
            
            <div style="display:flex; gap:10px;">
                <button class="btn-capture" onclick="captureAndSearch()" id="btn-action">
                    <i class="fas fa-search"></i> Capture & Search
                </button>
                <button class="btn-capture" style="background:#555;" onclick="triggerFileUpload()" title="Upload File">
                    <i class="fas fa-upload"></i>
                </button>
                <input type="file" id="file-upload-input" accept="image/*" style="display:none;" onchange="previewImage(this)">
            </div>
        </div>
    `;

    // Try to start camera
    startCamera();
}

function startCamera() {
    const video = document.getElementById('camera-stream');
    const placeholder = document.getElementById('camera-placeholder');

    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
            .then(function (stream) {
                videoStream = stream;
                video.srcObject = stream;
                video.style.display = 'block';
                placeholder.style.display = 'none';
            })
            .catch(function (err) {
                console.log("Camera Error: " + err);
                placeholder.innerHTML = '<i class="fas fa-exclamation-triangle"></i><p>Camera access denied</p>';
            });
    } else {
        placeholder.innerHTML = '<p>Camera not supported</p>';
    }
}

function triggerFileUpload() {
    document.getElementById('file-upload-input').click();
}

function previewImage(input) {
    if (input.files && input.files[0]) {
        // Stop camera if running
        stopCamera();

        var reader = new FileReader();
        reader.onload = function (e) {
            const video = document.getElementById('camera-stream');
            const img = document.getElementById('image-preview');
            const placeholder = document.getElementById('camera-placeholder');

            video.style.display = 'none';
            placeholder.style.display = 'none';
            img.src = e.target.result;
            img.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

let aiModel = null;

// Pre-load the model when script runs
async function loadAiModel() {
    try {
        aiModel = await mobilenet.load();
    } catch (error) {
        console.error("Failed to load AI model:", error);
    }
}
loadAiModel();

async function captureAndSearch() {
    const scanner = document.getElementById('scanner-ui');
    const status = document.getElementById('camera-status');
    const btn = document.getElementById('btn-action');
    const cameraView = document.getElementById('camera-view');

    // UI Updates
    scanner.style.display = 'block';
    status.innerText = "Scanning objects...";
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Analyzing...';

    // Get the image source (Video or Preview Image)
    let imgElement = document.getElementById('camera-stream'); // Default to video
    if (imgElement.style.display === 'none') {
        imgElement = document.getElementById('image-preview'); // Use uploaded image if video is hidden
    }

    // Create a canvas to capture the current frame/image
    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');

    // Set canvas dimensions based on the imgElement
    canvas.width = imgElement.videoWidth || imgElement.naturalWidth || imgElement.width;
    canvas.height = imgElement.videoHeight || imgElement.naturalHeight || imgElement.height;

    // Draw the current frame/image onto the canvas
    context.drawImage(imgElement, 0, 0, canvas.width, canvas.height);

    // Convert to Blob
    canvas.toBlob(async blob => {
        // Create a URL for the captured image to show in the result card
        const capturedImageUrl = URL.createObjectURL(blob);

        // Prepare FormData
        const formData = new FormData();
        formData.append('image', blob, 'capture.jpg');

        // Show scanning UI
        scanner.style.display = 'block';
        status.innerText = "Analyzing...";
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Analyzing...';

        // NOTE: We are doing Client-Side Analysis, but still sending to PHP for specific logging/fallback if needed
        // For the result card, we will use the logic below directly.

        // Ensure model is loaded
        if (!aiModel) {
            status.innerText = "Loading Model...";
            await loadAiModel();
        }

        // Predict using MobileNet (classify the canvas content)
        try {
            const predictions = await aiModel.classify(canvas);
            console.log('Predictions:', predictions);

            if (predictions && predictions.length > 0) {
                const topResult = predictions[0];
                const detectedName = topResult.className;

                // MAP RESULT TO PRODUCT
                let product = null;

                if (detectedName.includes('telephone') || detectedName.includes('phone')) {
                    product = { id: "iphone15", name: "iPhone 15 Pro Max", price: "₱84,990.00", store: "TechZone PH", image: ai_path_prefix + "image/electronics/Portable Power Bank 20,000mAh.jpeg" };
                } else if (detectedName.includes('shoe') || detectedName.includes('sneaker') || detectedName.includes('sandal')) {
                    product = { id: "sneakers_casual", name: "Casual Sneakers", price: "₱1,299.00", store: "UrbanWear PH", image: ai_path_prefix + "image/Shop/UrbanWear PH/Casual Sneakers.jpeg" };
                } else if (detectedName.includes('shirt') || detectedName.includes('jersey') || detectedName.includes('clothing')) {
                    product = { id: "hoodie_black", name: "H&M Loose Fit Hoodie", price: "₱999.00", store: "UrbanWear PH", image: ai_path_prefix + "image/Shop/UrbanWear PH/H&M Loose Fit Hoodie.jpeg" };
                } else {
                    // NO MATCH IN SYSTEM
                    product = null;
                }

                scanner.style.display = 'none';

                // Show Result Card
                let resultCard = '';

                if (product) {
                    // PRODUCT FOUND IN SYSTEM
                    resultCard = `
                        <div style="
                            position: absolute; bottom: 10px; left: 10px; right: 10px; 
                            background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);
                            padding: 15px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); 
                            display: flex; align-items: center; gap: 15px; text-align: left; 
                            animation: slideUp 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                            border: 3px solid #0f8392; z-index: 10;
                        ">
                            <div style="width: 60px; height: 60px; border-radius: 10px; overflow: hidden; flex-shrink: 0; border: 1px solid #eee;">
                                <img src="${capturedImageUrl}" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div style="flex: 1;">
                                <div style="font-size: 0.7rem; color: #0f8392; text-transform: uppercase; font-weight: 700;">Included in System</div>
                                <div style="font-weight: 700; font-size: 0.95rem; color: #333;">${product.name}</div>
                                <div style="color: #e74c3c; font-weight: 800; font-size: 1rem;">${product.price}</div>
                            </div>
                            <div style="background: #0f8392; color: white; width: 35px; height: 35px; border-radius: 50%; display: flex; justify-content: center; align-items: center;">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                        </div>
                    `;
                } else {
                    // PRODUCT NOT FOUND / OUT OF ORDER
                    resultCard = `
                        <div style="
                            position: absolute; bottom: 10px; left: 10px; right: 10px; 
                            background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);
                            padding: 15px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); 
                            display: flex; align-items: center; gap: 15px; text-align: left; 
                            animation: slideUp 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                            border: 3px solid #e74c3c; z-index: 10;
                        ">
                            <div style="width: 60px; height: 60px; border-radius: 10px; overflow: hidden; flex-shrink: 0; border: 1px solid #eee; opacity: 0.7;">
                                <img src="${capturedImageUrl}" style="width: 100%; height: 100%; object-fit: cover; filter: grayscale(100%);">
                            </div>
                            <div style="flex: 1;">
                                <div style="font-size: 0.7rem; color: #e74c3c; text-transform: uppercase; font-weight: 700;">Detection: ${detectedName}</div>
                                <div style="font-weight: 700; font-size: 0.95rem; color: #333;">THIS PRODUCT IS OUT OF ORDER</div>
                                <div style="color: #777; font-size: 0.8rem;">Item not available in current inventory.</div>
                            </div>
                            <div style="background: #e74c3c; color: white; width: 35px; height: 35px; border-radius: 50%; display: flex; justify-content: center; align-items: center;">
                                <i class="fas fa-times"></i>
                            </div>
                        </div>
                    `;
                }

                cameraView.insertAdjacentHTML('beforeend', resultCard);
                status.innerText = "";
                btn.style.display = 'none';

                // Send to PHP Backend for Logging (Background)
                fetch(ai_path_prefix + 'php/ai_search.php', { method: 'POST', body: formData });

                setTimeout(() => {
                    closeAiModal();

                    // Save image
                    const reader = new FileReader();
                    reader.readAsDataURL(blob);
                    reader.onloadend = function () {
                        const base64data = reader.result;
                        sessionStorage.setItem('ai_captured_image', base64data);

                        // Redirect to CONFIRMATION MODAL only if product found
                        if (product) {
                            // DETERMINE CATEGORY based on Product Name/ID
                            let category = "General";
                            if (product.store === "TechZone PH" || product.name.includes("Phone") || product.name.includes("Camera")) {
                                category = "Electronics";
                            } else if (product.store === "UrbanWear PH" || product.name.includes("Hoodie") || product.name.includes("Sneakers")) {
                                category = "Fashion";
                            }

                            window.location.href = `${ai_path_prefix}Content/Dashboard.php?ai_action=confirm_scan&detected=${encodeURIComponent(product.name)}&category=${encodeURIComponent(category)}`;
                        } else {
                            // If not found, stay on modal so they see "Out of Order" message, then maybe close after delay
                            setTimeout(() => {
                                // Optional: Reset UI or close
                                // closeAiModal();
                            }, 2000);
                        }
                    }

                }, 1500);

            } else {
                status.innerText = "No object detected.";
                btn.disabled = false;
                URL.revokeObjectURL(capturedImageUrl); // Revoke if no product found
            }
        } catch (error) {
            console.error(error);
            status.innerText = "AI Error. Try again.";
            btn.disabled = false;
            URL.revokeObjectURL(capturedImageUrl); // Revoke on error
        }
    }, 'image/jpeg');
}

function openVoiceCommand() {
    stopCamera(); // Ensure camera is off
    const modal = document.getElementById('ai-modal-overlay');
    const content = document.getElementById('ai-modal-content-inject');

    modal.style.display = 'flex';
    content.innerHTML = `
        <div class="ai-modal-header">
            <h3 class="ai-modal-title"><i class="fas fa-microphone"></i> Voice Commander</h3>
            <span class="ai-modal-close" onclick="closeAiModal()">&times;</span>
        </div>
        <div class="ai-modal-body">
            <div class="voice-wave">
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
            </div>
            <p class="voice-status" id="voice-status-text">I'm listening...</p>
            <p id="voice-subtext" style="color:#64748b; font-size: 0.85rem; font-weight: 500;">Command anything: "Go to cart", "Show orders", "Find shoes"...</p>
        </div>
    `;

    // Check browser support
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

    if (!SpeechRecognition) {
        document.getElementById('voice-status-text').innerText = "Browser doesn't support Voice API.";
        return;
    }

    const recognition = new SpeechRecognition();
    recognition.lang = 'en-US';
    recognition.interimResults = false;
    recognition.maxAlternatives = 1;

    recognition.start();

    recognition.onresult = (event) => {
        const transcript = event.results[0][0].transcript.toLowerCase();
        console.log("Voice Command Recognized:", transcript);

        const statusText = document.getElementById('voice-status-text');
        const subText = document.getElementById('voice-subtext');
        statusText.innerText = `Executing: "${transcript}"`;
        subText.innerText = "Processing command...";

        // System-wide "Auto" Commands
        const commands = [
            { keywords: ['home', 'dashboard', 'main page', 'pumunta sa home', 'balik sa home'], action: ai_path_prefix + 'Content/Dashboard.php' },
            { keywords: ['cart', 'shopping cart', 'bucket', 'buksan ang cart', 'check out'], action: ai_path_prefix + 'Content/Check-out.php' },
            { keywords: ['order', 'orders', 'history', 'mga order', 'binili'], action: ai_path_prefix + 'Content/Order-history.php' },
            { keywords: ['profile', 'account', 'setting', 'security', 'sarili', 'impormasyon'], action: ai_path_prefix + 'Content/user-account.php' },
            { keywords: ['support', 'help', 'customer service', 'chat', 'tulong'], action: ai_path_prefix + 'Services/Customer_Service.php' },
            { keywords: ['logout', 'sign out', 'alis', 'log out'], action: ai_path_prefix + 'php/logout.php' },
            { keywords: ['best seller', 'best selling', 'sikat', 'mabenta'], action: ai_path_prefix + 'Shop/index.php?search=best+sellers' },
            { keywords: ['mall', 'shops', 'stores', 'tindahan'], action: ai_path_prefix + 'Shop/index.php' }
        ];

        let foundAction = null;
        for (const cmd of commands) {
            if (cmd.keywords.some(k => transcript.includes(k))) {
                foundAction = cmd.action;
                break;
            }
        }

        if (foundAction) {
            speakResponse("Affirmative. Navigating to your request.");
            setTimeout(() => {
                closeAiModal();
                window.location.href = foundAction;
            }, 1200);
        } else if (transcript.includes('hello') || transcript.includes('hi') || transcript.includes('kumusta')) {
            speakResponse("Hello! I am your I-Market assistant. I can navigate you through the store. Try saying 'Go to my orders' or 'Search for gadgets'.");
            statusText.innerText = "System: Hello! How can I help?";
            subText.innerText = "Listening for next command...";
            setTimeout(() => recognition.start(), 3000);
        } else {
            // Default to Search
            speakResponse("Searching the marketplace for " + transcript);
            statusText.innerText = "Finding products...";

            setTimeout(() => {
                closeAiModal();
                window.location.href = ai_path_prefix + 'Shop/index.php?search=' + encodeURIComponent(transcript);
            }, 1500);
        }
    };

    recognition.onerror = (event) => {
        document.getElementById('voice-status-text').innerText = "Recognition error: " + event.error;
    };
}

function speakResponse(text) {
    if ('speechSynthesis' in window) {
        const utterance = new SpeechSynthesisUtterance(text);
        // Optional: Set voice
        // const voices = window.speechSynthesis.getVoices();
        // utterance.voice = voices[0]; 
        window.speechSynthesis.speak(utterance);
    }
}

function closeAiModal() {
    document.getElementById('ai-modal-overlay').style.display = 'none';
    stopCamera();
}

function stopCamera() {
    if (videoStream) {
        videoStream.getTracks().forEach(track => track.stop());
        videoStream = null;
    }
}



function openAiChat() {
    stopCamera();
    const modal = document.getElementById('ai-modal-overlay');
    const content = document.getElementById('ai-modal-content-inject');

    modal.style.display = 'flex';
    content.innerHTML = `
        <div class="ai-modal-header">
            <h3 class="ai-modal-title"><i class="fas fa-comments"></i> IMarket Support AI</h3>
            <span class="ai-modal-close" onclick="closeAiModal()">&times;</span>
        </div>
        <div class="ai-modal-body" style="padding: 0; display: flex; flex-direction: column; height: 500px;">
            <div id="ai-chat-messages" style="flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 15px; background: #f8fafc;">
                <div class="ai-msg ai-msg-bot" style="align-self: flex-start; background: white; padding: 12px 18px; border-radius: 15px 15px 15px 0; max-width: 80%; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; font-size: 14px;">
                    Hello! I'm your IMarket AI assistant. How can I help you today?
                </div>
            </div>
            <div class="ai-chat-input-area" style="padding: 15px; background: white; border-top: 1px solid #e2e8f0; display: flex; gap: 10px;">
                <input type="text" id="ai-chat-input" placeholder="Type your message..." style="flex: 1; border: 1px solid #e2e8f0; border-radius: 20px; padding: 10px 15px; outline: none; font-size: 14px;">
                <button onclick="sendAiChatMessage()" style="background: #2A3B7E; color: white; border: none; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    `;

    const input = document.getElementById('ai-chat-input');
    input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendAiChatMessage();
    });
}

async function sendAiChatMessage() {
    const input = document.getElementById('ai-chat-input');
    const container = document.getElementById('ai-chat-messages');
    const text = input.value.trim();

    if (!text) return;

    // User Message
    const userMsg = document.createElement('div');
    userMsg.className = 'ai-msg ai-msg-user';
    userMsg.style = "align-self: flex-end; background: #2A3B7E; color: white; padding: 12px 18px; border-radius: 15px 15px 0 15px; max-width: 80%; box-shadow: 0 2px 5px rgba(0,0,0,0.1); font-size: 14px;";
    userMsg.innerText = text;
    container.appendChild(userMsg);

    input.value = '';
    container.scrollTop = container.scrollHeight;

    // Bot Typing
    const typing = document.createElement('div');
    typing.innerText = 'AI is thinking...';
    typing.style = "font-size: 12px; color: #94a3b8; font-style: italic; margin-left: 5px;";
    container.appendChild(typing);

    // AI logic (Simplified for now - can be expanded with real API)
    setTimeout(() => {
        container.removeChild(typing);
        const botMsg = document.createElement('div');
        botMsg.className = 'ai-msg ai-msg-bot';
        botMsg.style = "align-self: flex-start; background: white; padding: 12px 18px; border-radius: 15px 15px 15px 0; max-width: 80%; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; font-size: 14px;";

        // Simple responses
        let response = "I'm not sure about that, but you can check our Support Tickets for help!";
        if (text.toLowerCase().includes('order')) response = "You can track your orders in your Account page under 'My Orders'.";
        if (text.toLowerCase().includes('hello') || text.toLowerCase().includes('hi')) response = "Hi there! How can I assist you with your shopping today?";
        if (text.toLowerCase().includes('refund')) response = "Refunds take 3-5 banking days. Please submit a ticket for specific requests.";
        if (text.toLowerCase().includes('best')) response = "Our current best sellers include Wireless Earbuds and the Nike Basketball collection!";

        botMsg.innerText = response;
        container.appendChild(botMsg);
        container.scrollTop = container.scrollHeight;
    }, 1000);
}
