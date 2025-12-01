<?php

try {
    $id = $_GET['id'] ?? null;
    
    if (!validate_id($id)) {
        error_response('Invalid or missing ID', 400);
    }
    
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
    
    $sql = "UPDATE " . VOCAB_TABLE . " SET lang_a = :a, lang_b = :b, meta = :m WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':a' => trim($data['lang_a']),
        ':b' => trim($data['lang_b']),
        ':m' => json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ':id' => $id,
    ]);
    
    if ($stmt->rowCount() > 0) {
        success_response(['message' => 'Vocabulary updated successfully', 'id' => $id]);
    } else {
        error_response('Vocabulary not found or no changes made', 404);
    }
} catch (PDOException $e) {
    if (DEBUG_MODE) {
        error_response('Database error: ' . $e->getMessage(), 500);
    } else {
        error_response('Internal server error', 500);
    }
}