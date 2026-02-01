<?php
// pages/ai-helper.php
?>
<section id="ai-helper" class="tab-content active">
    <div class="card">
        <h2>AI Assistant</h2>
        <p class="subtitle">Ask questions about scheduling, room management, or general help</p>
        
        <div class="chat-container">
            <div id="chatMessages" class="chat-messages">
                <div class="message ai-message">
                    <p>Hello! I'm your UC Nexus AI Assistant. How can I help you today?</p>
                </div>
            </div>
            <div class="chat-input-area">
                <input type="text" id="chatInput" placeholder="Ask me anything..." class="chat-input">
                <button class="btn-send" onclick="sendChatMessage()">Send</button>
            </div>
        </div>

        <div class="quick-questions">
            <p><strong>Quick Questions:</strong></p>
            <button class="btn-quick" onclick="askQuestion('How do I import room data?')">How do I import room data?</button>
            <button class="btn-quick" onclick="askQuestion('What is the room capacity limit?')">What is the room capacity limit?</button>
            <button class="btn-quick" onclick="askQuestion('How do I export schedules?')">How do I export schedules?</button>
        </div>
    </div>
</section>
