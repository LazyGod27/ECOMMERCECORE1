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
                const confidence = topResult.probability;

                // ENHANCED PRODUCT DETECTION MAPPING
                // Maps detected objects to products with better accuracy
                let product = null;
                let detection_type = 'no_match';

                // Electronics Detection
                if (detectedName.includes('telephone') || detectedName.includes('phone') || detectedName.includes('mobile')) {
                    detection_type = 'phone';
                    if (confidence > 0.7) {
                        product = { 
                            id: "iphone15", 
                            name: "iPhone 15 Pro Max", 
                            price: "₱84,990.00", 
                            store: "TechZone PH", 
                            image: ai_path_prefix + "image/electronics/Portable Power Bank 20,000mAh.jpeg",
                            category: "Electronics"
                        };
                    }
                } 
                // Footwear Detection
                else if (detectedName.includes('shoe') || detectedName.includes('sneaker') || detectedName.includes('sandal') || detectedName.includes('boot')) {
                    detection_type = 'footwear';
                    if (confidence > 0.6) {
                        product = { 
                            id: "sneakers_casual", 
                            name: "Casual Sneakers", 
                            price: "₱1,299.00", 
                            store: "UrbanWear PH", 
                            image: ai_path_prefix + "image/Shop/UrbanWear PH/Casual Sneakers.jpeg",
                            category: "Fashion"
                        };
                    }
                } 
                // Clothing Detection
                else if (detectedName.includes('shirt') || detectedName.includes('jersey') || detectedName.includes('clothing') || detectedName.includes('hoodie') || detectedName.includes('jacket') || detectedName.includes('pants') || detectedName.includes('dress')) {
                    detection_type = 'clothing';
                    if (confidence > 0.6) {
                        product = { 
                            id: "hoodie_black", 
                            name: "H&M Loose Fit Hoodie", 
                            price: "₱999.00", 
                            store: "UrbanWear PH", 
                            image: ai_path_prefix + "image/Shop/UrbanWear PH/H&M Loose Fit Hoodie.jpeg",
                            category: "Fashion"
                        };
                    }
                }
                // Headphone/Audio Detection
                else if (detectedName.includes('headphone') || detectedName.includes('earphone') || detectedName.includes('speaker') || detectedName.includes('audio')) {
                    detection_type = 'audio';
                    if (confidence > 0.65) {
                        product = { 
                            id: "wireless_earbuds", 
                            name: "Premium Wireless Earbuds", 
                            price: "₱2,499.00", 
                            store: "TechZone PH", 
                            image: ai_path_prefix + "image/electronics/Wireless Earbuds.jpeg",
                            category: "Electronics"
                        };
                    }
                }
                // Accessory Detection
                else if (detectedName.includes('bag') || detectedName.includes('backpack') || detectedName.includes('hat') || detectedName.includes('cap') || detectedName.includes('watch')) {
                    detection_type = 'accessory';
                    if (confidence > 0.6) {
                        product = { 
                            id: "stylish_bag", 
                            name: "Stylish Backpack", 
                            price: "₱1,899.00", 
                            store: "UrbanWear PH", 
                            image: ai_path_prefix + "image/Shop/UrbanWear PH/Stylish Backpack.jpeg",
                            category: "Fashion"
                        };
                    }
                }
                // Home & Living Detection
                else if (detectedName.includes('lamp') || detectedName.includes('light') || detectedName.includes('furniture') || detectedName.includes('chair') || detectedName.includes('table')) {
                    detection_type = 'home';
                    if (confidence > 0.6) {
                        product = { 
                            id: "desk_lamp", 
                            name: "LED Desk Lamp", 
                            price: "₱499.00", 
                            store: "CozyLiving Store", 
                            image: ai_path_prefix + "image/Shop/CozyLiving/LED Desk Lamp.jpeg",
                            category: "Home"
                        };
                    }
                }
                // Food Detection
                else if (detectedName.includes('food') || detectedName.includes('cup') || detectedName.includes('plate') || detectedName.includes('beverage') || detectedName.includes('coffee')) {
                    detection_type = 'food_home';
                    if (confidence > 0.6) {
                        product = { 
                            id: "coffee_maker", 
                            name: "Automatic Coffee Maker", 
                            price: "₱1,299.00", 
                            store: "HomeEssentials PH", 
                            image: ai_path_prefix + "image/Shop/HomeEssentials/Coffee Maker.jpeg",
                            category: "Home"
                        };
                    }
                }

                scanner.style.display = 'none';

                // Show Result Card with Confidence Indicator
                let resultCard = '';

                if (product && confidence > 0.5) {
                    // PRODUCT FOUND IN SYSTEM WITH GOOD CONFIDENCE
                    const confidence_percent = Math.round(confidence * 100);
                    const confidence_color = confidence > 0.8 ? '#0f8392' : confidence > 0.6 ? '#f59e0b' : '#e74c3c';
                    
                    resultCard = `
                        <div style="
                            position: absolute; bottom: 10px; left: 10px; right: 10px; 
                            background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(10px);
                            padding: 15px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); 
                            display: flex; align-items: center; gap: 15px; text-align: left; 
                            animation: slideUp 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                            border: 3px solid ${confidence_color}; z-index: 10;
                        ">
                            <div style="width: 60px; height: 60px; border-radius: 10px; overflow: hidden; flex-shrink: 0; border: 1px solid #eee;">
                                <img src="${capturedImageUrl}" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div style="flex: 1;">
                                <div style="font-size: 0.65rem; color: ${confidence_color}; text-transform: uppercase; font-weight: 700;">Match: ${confidence_percent}% - ${product.category}</div>
                                <div style="font-weight: 700; font-size: 0.95rem; color: #333;">${product.name}</div>
                                <div style="color: #e74c3c; font-weight: 800; font-size: 1rem;">${product.price}</div>
                            </div>
                            <div style="background: ${confidence_color}; color: white; width: 35px; height: 35px; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 0.75rem; font-weight: 700;">
                                ${confidence_percent}%
                            </div>
                        </div>
                    `;
                } else if (product) {
                    // PRODUCT FOUND BUT LOW CONFIDENCE
                    const confidence_percent = Math.round(confidence * 100);
                    resultCard = `
                        <div style="
                            position: absolute; bottom: 10px; left: 10px; right: 10px; 
                            background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(10px);
                            padding: 15px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); 
                            display: flex; align-items: center; gap: 15px; text-align: left; 
                            animation: slideUp 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                            border: 3px solid #fbbf24; z-index: 10;
                        ">
                            <div style="width: 60px; height: 60px; border-radius: 10px; overflow: hidden; flex-shrink: 0; border: 1px solid #eee;">
                                <img src="${capturedImageUrl}" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div style="flex: 1;">
                                <div style="font-size: 0.65rem; color: #fbbf24; text-transform: uppercase; font-weight: 700;">Possible Match: ${confidence_percent}%</div>
                                <div style="font-weight: 700; font-size: 0.95rem; color: #333;">${product.name}</div>
                                <div style="color: #e74c3c; font-weight: 800; font-size: 1rem;">${product.price}</div>
                                <div style="font-size: 0.75rem; color: #666; margin-top: 3px;">Low confidence - verify product</div>
                            </div>
                            <div style="background: #fbbf24; color: white; width: 35px; height: 35px; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 0.75rem; font-weight: 700;">
                                ${confidence_percent}%
                            </div>
                        </div>
                    `;
                } else {
                    // PRODUCT NOT FOUND / UNRECOGNIZED OBJECT
                    const detected_readable = detectedName.replace(/,/g, ' •').replace(/([a-z])([A-Z])/g, '$1 $2');
                    const confidence_percent = Math.round(confidence * 100);
                    
                    resultCard = `
                        <div style="
                            position: absolute; bottom: 10px; left: 10px; right: 10px; 
                            background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(10px);
                            padding: 15px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); 
                            display: flex; align-items: center; gap: 15px; text-align: left; 
                            animation: slideUp 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                            border: 3px solid #e74c3c; z-index: 10;
                        ">
                            <div style="width: 60px; height: 60px; border-radius: 10px; overflow: hidden; flex-shrink: 0; border: 1px solid #eee; opacity: 0.7;">
                                <img src="${capturedImageUrl}" style="width: 100%; height: 100%; object-fit: cover; filter: grayscale(100%);">
                            </div>
                            <div style="flex: 1;">
                                <div style="font-size: 0.65rem; color: #e74c3c; text-transform: uppercase; font-weight: 700;">Detected: ${detected_readable}</div>
                                <div style="font-weight: 700; font-size: 0.95rem; color: #333;">NOT IN CATALOG</div>
                                <div style="color: #666; font-size: 0.8rem;">Try voice search or browse categories</div>
                                <div style="font-size: 0.7rem; color: #999; margin-top: 3px;">Detection confidence: ${confidence_percent}%</div>
                            </div>
                            <div style="background: #e74c3c; color: white; width: 35px; height: 35px; border-radius: 50%; display: flex; justify-content: center; align-items: center;">
                                <i class="fas fa-search"></i>
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
            { keywords: ['cart', 'shopping cart', 'bucket', 'buksan ang cart', 'check out'], action: ai_path_prefix + 'Content/add-to-cart.php' },
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

    // Bot Typing Indicator
    const typing = document.createElement('div');
    typing.innerText = '⚙️ Processing...';
    typing.style = "font-size: 12px; color: #94a3b8; font-style: italic; margin-left: 5px;";
    container.appendChild(typing);

    // Enhanced AI Response Logic with Context Understanding
    setTimeout(() => {
        container.removeChild(typing);
        const botMsg = document.createElement('div');
        botMsg.className = 'ai-msg ai-msg-bot';
        botMsg.style = "align-self: flex-start; background: white; padding: 12px 18px; border-radius: 15px 15px 15px 0; max-width: 80%; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; font-size: 14px; line-height: 1.5;";

        const userInput = text.toLowerCase();
        let response = generateAIResponse(userInput);

        botMsg.innerText = response;
        container.appendChild(botMsg);
        container.scrollTop = container.scrollHeight;
    }, 800);
}

/**
 * Enhanced AI Response Generator with Multiple Categories
 */
function generateAIResponse(input) {
    // Category: Orders & Tracking
    if (input.includes('order') || input.includes('track') || input.includes('shipment') || input.includes('delivery') || input.includes('ordena') || input.includes('order status')) {
        const order_responses = [
            "To track your orders, go to your Account page and click 'Order History'. You can see real-time tracking updates there! 📦",
            "You can check all your orders and their delivery status in the 'My Orders' section of your account. Click 'Order History' on the left menu.",
            "Need help with an order? Go to Account → Order History to track your package. If you need more help, feel free to contact our support team!"
        ];
        return order_responses[Math.floor(Math.random() * order_responses.length)];
    }
    
    // Category: Payments & Refunds
    else if (input.includes('refund') || input.includes('return') || input.includes('money back') || input.includes('bumalik') || input.includes('payment')) {
        const refund_responses = [
            "Refunds typically take 3-5 banking days after we receive the returned item. You'll get a notification once your refund is processed! 💰",
            "To initiate a return, go to your Order History, select the item, and click 'Return Item'. Follow the instructions, and we'll process it quickly!",
            "Having payment issues? We accept credit cards, debit cards, and e-wallets. If your payment failed, please try again or contact support for assistance."
        ];
        return refund_responses[Math.floor(Math.random() * refund_responses.length)];
    }
    
    // Category: Product & Recommendations
    else if (input.includes('recommend') || input.includes('suggest') || input.includes('best') || input.includes('popular') || input.includes('trending') || input.includes('bagay')) {
        const product_responses = [
            "Our current best sellers include: 📱 Wireless Earbuds, 👟 Nike Basketball Shoes, and 💻 Portable Power Banks! Check them out in our Shop.",
            "Looking for something specific? Try using our voice search feature or image search to find exactly what you need!",
            "Check out our Best Selling section for popular items! Each product has verified reviews to help you decide."
        ];
        return product_responses[Math.floor(Math.random() * product_responses.length)];
    }
    
    // Category: Account & Security
    else if (input.includes('account') || input.includes('profile') || input.includes('password') || input.includes('security') || input.includes('sign in') || input.includes('login')) {
        const account_responses = [
            "To update your profile, go to Account → My Profile. You can change your name, email, and other details there. 👤",
            "For security, we recommend changing your password regularly. Go to Account → Change Password to update it anytime!",
            "All your account information is encrypted and secure. If you suspect any issues, please contact our support team immediately."
        ];
        return account_responses[Math.floor(Math.random() * account_responses.length)];
    }
    
    // Category: Checkout & Buying
    else if (input.includes('checkout') || input.includes('buy') || input.includes('purchase') || input.includes('cart') || input.includes('bumili') || input.includes('bili')) {
        const checkout_responses = [
            "To checkout, click 'View Cart' or 'Add to Cart' on any product. Then review your items and click 'Proceed to Payment' 🛒",
            "Our checkout process is quick and secure! Add items to cart, review, and pay. You'll get an order confirmation immediately.",
            "You can save items for later by adding them to your wishlist. When you're ready to buy, add them to cart and checkout! ✨"
        ];
        return checkout_responses[Math.floor(Math.random() * checkout_responses.length)];
    }
    
    // Category: Shipping & Address
    else if (input.includes('address') || input.includes('shipping') || input.includes('location') || input.includes('deliver') || input.includes('lugar') || input.includes('address')) {
        const shipping_responses = [
            "To set a delivery address, go to Account → Addresses. You can set a default address and add multiple delivery locations!",
            "We deliver to most areas in the Philippines! Standard shipping takes 2-5 business days. Express options are available too! 🚚",
            "Make sure your address is complete and accurate for faster delivery. Go to your Account to verify or update your delivery location."
        ];
        return shipping_responses[Math.floor(Math.random() * shipping_responses.length)];
    }
    
    // Category: Technical Support
    else if (input.includes('bug') || input.includes('error') || input.includes('broken') || input.includes('doesn') || input.includes('sira') || input.includes('hindi gumagana')) {
        const technical_responses = [
            "Sorry if you're experiencing issues! Please try refreshing the page. If the problem persists, contact our support team with details. 🔧",
            "Having technical issues? Clear your browser cache and try again. If it still doesn't work, we're here to help!",
            "Report any bugs through our support chat or ticket system. We'll investigate and fix issues quickly! Thank you for reporting! 🐛"
        ];
        return technical_responses[Math.floor(Math.random() * technical_responses.length)];
    }
    
    // Category: Greeting & Conversation
    else if (input.includes('hello') || input.includes('hi') || input.includes('hey') || input.includes('kumusta') || input.includes('magandang') || input.includes('morning') || input.includes('afternoon')) {
        const greeting_responses = [
            "👋 Hello! Welcome to IMarket! I'm your AI assistant. How can I help you today? You can ask me about orders, products, or anything else!",
            "Hi there! 😊 I'm here to help you shop, track orders, or answer questions. What can I assist you with?",
            "Magandang araw! Welcome to IMarket! How can I serve you today? 🎉"
        ];
        return greeting_responses[Math.floor(Math.random() * greeting_responses.length)];
    }
    
    // Category: Help & Support
    else if (input.includes('help') || input.includes('support') || input.includes('tulong') || input.includes('assistance') || input.includes('need')) {
        const support_responses = [
            "I'm here to help! You can ask me about: orders, products, payment, shipping, your account, or anything else! 💬",
            "Need support? You can: 1) Chat with me here, 2) Visit the Help section, or 3) Contact our customer service team! 📞",
            "What do you need help with? I can assist with questions about shopping, orders, payments, and more!"
        ];
        return support_responses[Math.floor(Math.random() * support_responses.length)];
    }
    
    // Category: Search & Discovery
    else if (input.includes('search') || input.includes('find') || input.includes('look for') || input.includes('hanap') || input.includes('kategorya')) {
        const search_responses = [
            "Use our search bar at the top to find products! You can also try voice search 🎤 or image search 📸 for more options!",
            "Browse by category: Electronics, Fashion, Home, Beauty, Sports, and more! Click on any category to explore.",
            "Not sure what you're looking for? Try our bestsellers, new arrivals, or use our AI search features! 🔍"
        ];
        return search_responses[Math.floor(Math.random() * search_responses.length)];
    }
    
    // Category: Promotions & Deals
    else if (input.includes('promo') || input.includes('discount') || input.includes('sale') || input.includes('offer') || input.includes('special') || input.includes('kapakanan')) {
        const promo_responses = [
            "Check our Shop for current promotions! Many products have discounts and special offers. Keep an eye out for our daily deals! 🎁",
            "Subscribe to our notifications to get alerts about new promotions and sales! Don't miss out on great deals. ✨",
            "Visit the Best Selling section to find products with the best prices and highest customer satisfaction ratings!"
        ];
        return promo_responses[Math.floor(Math.random() * promo_responses.length)];
    }
    
    // Category: Reviews & Ratings
    else if (input.includes('review') || input.includes('rating') || input.includes('feedback') || input.includes('rate') || input.includes('puna')) {
        const review_responses = [
            "Read customer reviews on each product page to make informed decisions! Our reviews are verified and include ratings from real buyers. ⭐",
            "You can rate products after you receive them! Go to Order History → 'Rate Product' to share your feedback.",
            "Your reviews help other customers! Share honest feedback about products you've purchased to help the community. 🌟"
        ];
        return review_responses[Math.floor(Math.random() * review_responses.length)];
    }
    
    // Category: Stores & Sellers
    else if (input.includes('store') || input.includes('seller') || input.includes('shop') || input.includes('vendor') || input.includes('brand') || input.includes('tindahan')) {
        const store_responses = [
            "We have verified sellers across multiple categories! Visit our Shop section to explore different stores and brands.",
            "Each seller/store has ratings and verified reviews. You can follow your favorite stores to stay updated on new products! 🏪",
            "Looking for a specific brand? Use our search to find authorized sellers of that brand in our marketplace!"
        ];
        return store_responses[Math.floor(Math.random() * store_responses.length)];
    }
    
    // Default Response for Unrecognized Input
    else {
        const default_responses = [
            "I'm not sure about that, but I can help with orders, products, shipping, accounts, and more! What would you like to know?",
            "That's an interesting question! Try asking me about: shopping, orders, shipping, your account, or our products. 😊",
            "I didn't quite understand that. Can you rephrase? I'm here to help with your shopping needs! 🛍️"
        ];
        return default_responses[Math.floor(Math.random() * default_responses.length)];
    }
}
