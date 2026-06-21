<?php
require_once 'config/config.php';

$tinyPngBase64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
$prompt = "Respond with JSON containing status ok: {\"status\": \"ok\"}";

$tests = [
    // Test 1: No generationConfig
    [
        'name' => 'v1 / gemini-2.5-flash / No Config',
        'url' => 'https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent?key=' . GEMINI_API_KEY,
        'body' => [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inlineData' => [
                                'mimeType' => 'image/png',
                                'data' => $tinyPngBase64
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ],
    // Test 2: response_mime_type (snake_case)
    [
        'name' => 'v1 / gemini-2.5-flash / response_mime_type',
        'url' => 'https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent?key=' . GEMINI_API_KEY,
        'body' => [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inlineData' => [
                                'mimeType' => 'image/png',
                                'data' => $tinyPngBase64
                            ]
                        ]
                    ]
                ]
            ],
            'generationConfig' => [
                'response_mime_type' => 'application/json'
            ]
        ]
    ],
    // Test 3: responseMimeType in v1beta
    [
        'name' => 'v1beta / gemini-2.5-flash / responseMimeType',
        'url' => 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . GEMINI_API_KEY,
        'body' => [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inlineData' => [
                                'mimeType' => 'image/png',
                                'data' => $tinyPngBase64
                            ]
                        ]
                    ]
                ]
            ],
            'generationConfig' => [
                'responseMimeType' => 'application/json'
            ]
        ]
    ]
];

foreach ($tests as $t) {
    echo "Running test: " . $t['name'] . "\n";
    $ch = curl_init($t['url']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($t['body']));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "Status: HTTP $httpCode\n";
    if ($httpCode === 200) {
        echo "Response: $response\n\n";
    } else {
        echo "Error: " . $response . "\n\n";
    }
}
