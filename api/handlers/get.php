<?php

try {
    $id = $_GET['id'] ?? null;

    if ($id) {
        if (!validate_id($id)) {
            error_response('Invalid ID format', 400);
        }
        
        $sql = "SELECT * FROM " . VOCAB_TABLE . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        
        if ($result) {
            if (isset($result['meta'])) {
                $result['meta'] = json_decode($result['meta'], true);
            }
            success_response($result);
        } else {
            error_response('Not Found', 404);
        }
    } else {
        $sql = "SELECT * FROM " . VOCAB_TABLE;
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($results as &$result) {
            if (isset($result['meta'])) {
                $result['meta'] = json_decode($result['meta'], true);
            }
        }
        
        success_response($results);
    }
} catch (PDOException $e) {
    if (DEBUG_MODE) {
        error_response('Database error: ' . $e->getMessage(), 500);
    } else {
        error_response('Internal server error', 500);
    }
}