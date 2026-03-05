<?php
// pages/ai-api.php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$prompt = trim($input['prompt'] ?? '');

if ($prompt === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Prompt is required']);
    exit;
}

// Load config
$aiConfig = [];
if (file_exists(__DIR__ . '/../config/ai_config.php')) {
    $aiConfig = require __DIR__ . '/../config/ai_config.php';
}

$apiKey = $aiConfig['gemini_api_key'] ?? getenv('GEMINI_API_KEY');
if (empty($apiKey)) {
    http_response_code(500);
    echo json_encode(['error' => 'AI API key is not configured.']);
    exit;
}

// Gemini specific endpoint and model
$model = $aiConfig['model'] ?? 'gemini-1.5-flash';
// Google Gemini API uses the key as a query parameter
$endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

// Gemini JSON Body Structure
$payload = [
    'contents' => [
        [
            'role' => 'user',
            'parts' => [
                ['text' => "System: You are UC Nexus assistant helping with scheduling and rooms. User: " . $prompt]
            ]
        ]
    ],
    'generationConfig' => [
        'temperature' => 0.2,
        'maxOutputTokens' => 800
    ]
];

$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

$response = curl_exec($ch);
$err = curl_error($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($err) {
    http_response_code(500);
    echo json_encode(['error' => 'Request error: ' . $err]);
    exit;
}

$decoded = json_decode($response, true);

// Extract text from Gemini's response structure
if (isset($decoded['candidates'][0]['content']['parts'][0]['text'])) {
    $replyText = $decoded['candidates'][0]['content']['parts'][0]['text'];
    echo json_encode(['reply' => $replyText]);
} else {
    // If it fails, return the error from Google for debugging
    http_response_code($status);
    echo json_encode([
        'error' => 'Failed to parse Gemini response',
        'debug' => $decoded
    ]);
}