<?php

try {
    $data = json_input();
    
    if (!$data) {
        error_response('Invalid JSON data', 400);
    }
    
    $missing = validate_required_fields($data, ['lang_a', 'lang_b']);
    if (!empty($missing)) {
        error_response('Missing required fields: ' . implode(', ', $missing), 400);
    }
    if (!is_string($data['lang_a']) || trim($data['lang_a']) === '') {
        error_response('lang_a must be a non-empty string', 400);
    }
    if (!is_string($data['lang_b']) || trim($data['lang_b']) === '') {
        error_response('lang_b must be a non-empty string', 400);
    }
    
    $meta = $data['meta'] ?? null;
    $errors = [];
    if (!validate_meta($meta, $errors)) {
        error_response('Invalid meta: ' . implode('; ', $errors), 400);
    }
    
    $sql = "INSERT INTO " . VOCAB_TABLE . " (lang_a, lang_b, meta) VALUES (:a, :b, :m)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':a' => trim($data['lang_a']),
        ':b' => trim($data['lang_b']),
        ':m' => json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    ]);
    
    success_response(['id' => $pdo->lastInsertId(), 'message' => 'Vocabulary created successfully'], 201);
} catch (PDOException $e) {
    if (DEBUG_MODE) {
        error_response('Database error: ' . $e->getMessage(), 500);
    } else {
        error_response('Internal server error', 500);
    }
}
