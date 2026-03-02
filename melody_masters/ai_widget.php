<style>
    /* Floating Button */
    .chat-bubble-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 65px;
        height: 65px;
        background: #ffc107;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        color: #000;
        cursor: pointer;
        box-shadow: 0 10px 25px rgba(255, 193, 7, 0.4);
        z-index: 9999;
        transition: all 0.3s ease;
        animation: float-animation 3s ease-in-out infinite;
    }

    @keyframes float-animation {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-15px); }
        100% { transform: translateY(0px); }
    }

    .chat-bubble-btn:hover { 
        transform: scale(1.1) rotate(5deg); 
        animation-play-state: paused;
    }

    .ai-chat-window {
        position: fixed;
        bottom: 110px;
        right: 30px;
        width: 380px;
        height: 600px;
        background: #0a0a0a;
        border-radius: 25px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        display: none; 
        flex-direction: column;
        z-index: 9999;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(0,0,0,0.8);
        animation: slideUp 0.4s ease-out;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(50px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .chat-header {
        background: #ffc107;
        padding: 15px 20px;
        color: #000;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 800;
    }

    .chat-video-area {
        width: 100%;
        height: 200px;
        background: #000;
    }

    .chat-messages {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .msg {
        padding: 10px 15px;
        border-radius: 15px;
        max-width: 85%;
        font-size: 0.9rem;
        word-wrap: break-word;
    }

    .bot { background: rgba(255,255,255,0.1); color: #fff; align-self: flex-start; }
    .user { background: #ffc107; color: #000; align-self: flex-end; font-weight: 600; }

    .chat-input {
        padding: 15px;
        border-top: 1px solid rgba(255,255,255,0.1);
        display: flex;
        gap: 10px;
    }

    .chat-input input {
        flex: 1;
        background: #1a1a1a;
        border: none;
        padding: 12px 15px;
        border-radius: 20px;
        color: #fff;
        outline: none;
    }

    .send-btn {
        background: #ffc107;
        border: none;
        width: 45px;
        height: 45px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Mobile view fixes */
    @media (max-width: 576px) {
        .ai-chat-window { width: 90%; right: 5%; left: 5%; bottom: 100px; height: 70vh; }
    }
</style>

<div class="chat-bubble-btn" onclick="toggleChat()">
    <i class="bi bi-robot"></i>
</div>

<div class="ai-chat-window" id="aiChatWindow">
    <div class="chat-header">
        <span><i class="bi bi-robot"></i> Melody Assistant</span>
        <i class="bi bi-xlg" style="cursor:pointer;" onclick="toggleChat()"></i>
    </div>

    <div class="chat-video-area">
        <video width="100%" height="100%" controls style="border-radius: 0;">
            <source src="assets/uploads/products/melody_vedio.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>

    <div class="chat-messages" id="chatBox">
        <div class="msg bot">Hi! 👋 I'm Melody AI. How can I help you today with our musical instruments?</div>
    </div>

    <div class="chat-input">
        <input type="text" id="userInp" placeholder="Type a message..." autocomplete="off">
        <button class="send-btn" onclick="sendMessage()"><i class="bi bi-send-fill"></i></button>
    </div>
</div>

<script>
    // --- මෙහි ඔබේ GROQ API KEY එක ඇතුළත් කරන්න ---
    const GROQ_API_KEY = "";

    function toggleChat() {
        const chatWin = document.getElementById('aiChatWindow');
        chatWin.style.display = (chatWin.style.display === 'flex') ? 'none' : 'flex';
        if(chatWin.style.display === 'flex') {
            const box = document.getElementById('chatBox');
            box.scrollTop = box.scrollHeight;
        }
    }

    async function sendMessage() {
        const input = document.getElementById('userInp');
        const box = document.getElementById('chatBox');
        const userText = input.value.trim();

        if (userText === "") return;

        // 1. User message එක පෙන්වීම
        box.innerHTML += `<div class="msg user">${userText}</div>`;
        input.value = ""; 
        box.scrollTo({ top: box.scrollHeight, behavior: 'smooth' });

        // 2. Loading state එක පෙන්වීම
        const loadingId = "loading-" + Date.now();
        box.innerHTML += `<div class="msg bot" id="${loadingId}">Typing...</div>`;
        box.scrollTo({ top: box.scrollHeight, behavior: 'smooth' });

        try {
            // 3. Groq API Call එක
            const response = await fetch("https://api.groq.com/openai/v1/chat/completions", {
                method: "POST",
                headers: {
                    "Authorization": `Bearer ${GROQ_API_KEY}`,
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    model: "llama-3.3-70b-versatile", // වේගවත්ම මාදිලිය
                    messages: [
                        {
                            role: "system",
                            content: "You are an expert musical instrument assistant for 'Melody Masters'. Be professional, helpful, and answer in English or Sinhala as per user request. Focus on musical instruments and store-related help."
                        },
                        { role: "user", content: userText }
                    ],
                    temperature: 0.7
                })
            });

            const data = await response.json();
            const aiReply = data.choices[0].message.content;

            // 4. AI පිළිතුර පෙන්වීම
            document.getElementById(loadingId).innerText = aiReply;

        } catch (error) {
            console.error("Error:", error);
            document.getElementById(loadingId).innerText = "Sorry, I'm having trouble connecting. Please try again later.";
        }

        box.scrollTo({ top: box.scrollHeight, behavior: 'smooth' });
    }

    document.getElementById('userInp').addEventListener("keypress", function(e) {
        if (e.key === "Enter") sendMessage();
    });
</script>