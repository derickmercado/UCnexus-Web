<?php
// config/ai_config.php

return [
    'gemini_api_key' => getenv('GEMINI_API_KEY') ?: 'AIzaSyAQ39B6d3wgTMjy0lGhZ22Eq58xQ5eRUbs',
    
    // The model identifier for Gemini
    'model' => 'gemini-1.5-flash', 
    
    // Google uses a different URL structure:
    // https://generativelanguage.googleapis.com/v1beta/models/{model}:generateContent?key={apiKey}
    'endpoint_base' => 'https://generativelanguage.googleapis.com/v1beta/models/'
];