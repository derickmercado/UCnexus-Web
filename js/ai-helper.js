// ===== AI HELPER PAGE FUNCTIONALITY =====

const aiResponses = {
    'import csv': 'To import room data from CSV: 1) Go to Room Assignment tab 2) Click Import CSV 3) Select your file 4) Preview the data 5) Click Import. CSV format should be: Room Name, Capacity, Floor, Building',
    'room capacity': 'The maximum room capacity depends on your facility. Standard classrooms usually have 30-50 seats, while larger lecture halls can accommodate 100+ students.',
    'export schedules': 'You can export schedules by going to the Schedule Overview tab. Look for the Export option at the top of the page. This will download your schedule data.',
    'add room': 'To add a new room: 1) Go to Room Assignment 2) Click Add Room 3) Fill in Room Name, Capacity, Floor, and Building 4) Click Add Room',
    'schedule class': 'To schedule a class: 1) Go to Schedule Overview 2) Click Add Schedule 3) Fill in Class Name, Room, Instructor, Date, and Time 4) Click Add Schedule',
    'default': 'I can help you with scheduling, room management, and system usage. Try asking about importing CSV, scheduling classes, or managing rooms!'
};

function sendChatMessage() {
    const chatInput = document.getElementById('chatInput');
    const message = chatInput.value.trim();

    if (message === '') return;

    const chatMessages = document.getElementById('chatMessages');

    // Add user message
    const userMsg = document.createElement('div');
    userMsg.classList.add('message', 'user-message');
    userMsg.innerHTML = `<p>${escapeHtml(message)}</p>`;
    chatMessages.appendChild(userMsg);

    // Clear input
    chatInput.value = '';

    // Simulate AI response
    setTimeout(() => {
        const response = getAIResponse(message);
        const aiMsg = document.createElement('div');
        aiMsg.classList.add('message', 'ai-message');
        aiMsg.innerHTML = `<p>${response}</p>`;
        chatMessages.appendChild(aiMsg);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }, 500);

    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function askQuestion(question) {
    document.getElementById('chatInput').value = question;
    sendChatMessage();
}

function getAIResponse(message) {
    const lowerMessage = message.toLowerCase();
    
    for (let key in aiResponses) {
        if (lowerMessage.includes(key)) {
            return aiResponses[key];
        }
    }
    
    return aiResponses['default'];
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Allow Enter key to send message
document.addEventListener('DOMContentLoaded', function() {
    const chatInput = document.getElementById('chatInput');
    if (chatInput) {
        chatInput.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                sendChatMessage();
            }
        });
    }
});
