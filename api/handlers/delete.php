<?php

try {
    $id = $_GET['id'] ?? null;
    
    if (!validate_id($id)) {
        error_response('Invalid or missing ID', 400);
    }
    
    $sql = "DELETE FROM " . VOCAB_TABLE . " WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    
    if ($stmt->rowCount() > 0) {
        success_response(['message' => 'Vocabulary deleted successfully', 'id' => $id]);
    } else {
        error_response('Vocabulary not found', 404);
    }
} catch (PDOException $e) {
    if (DEBUG_MODE) {
        error_response('Database error: ' . $e->getMessage(), 500);
    } else {
        error_response('Internal server error', 500);
    }
}
