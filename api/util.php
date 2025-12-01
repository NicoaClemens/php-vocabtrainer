<?php

function json_input() {
    return json_decode(file_get_contents('php://input'), true);
}

function json_output($data, $status_code = 200) {
    http_response_code($status_code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function validate_required_fields($data, $required_fields) {
    $missing = [];
    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || $data[$field] === '') {
            $missing[] = $field;
        }
    }
    return $missing;
}

function validate_id($id) {
    if (!$id || !is_numeric($id) || $id <= 0) {
        return false;
    }
    return true;
}

function error_response($message, $code = 400) {
    json_output(['error' => $message], $code);
}

function success_response($data, $code = 200) {
    json_output($data, $code);
}

function setup_cors() {
    if (CORS_ENABLED) {
        header('Access-Control-Allow-Origin: ' . CORS_ORIGIN);
        header('Access-Control-Allow-Methods: ' . CORS_METHODS);
        header('Access-Control-Allow-Headers: ' . CORS_HEADERS);
        
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }
}

function check_auth() {
    if (AUTH_ENABLED) {
        $headers = getallheaders();
        $api_key = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        
        $api_key = str_replace('Bearer ', '', $api_key);
        
        if ($api_key !== API_KEY) {
            error_response('Unauthorized', 401);
        }
    }
}

function validate_meta($meta, &$errors = []) {
    if ($meta === null) {
        return true;
    }

    if (!is_array($meta)) {
        $errors[] = 'meta must be an object';
        return false;
    }

    $allowedKeys = ['word_type', 'conjugation', 'sessions'];
    foreach ($meta as $key => $_) {
        if (!in_array($key, $allowedKeys, true)) {
            $errors[] = "Unknown meta field: $key";
        }
    }

    if (array_key_exists('word_type', $meta)) {
        $allowedTypes = ['noun','verb','adjective','adverb','pronoun','preposition','conjunction','interjection','article','phrase'];
        $wordType = $meta['word_type'];
        if (!is_string($wordType) || $wordType === '') {
            $errors[] = 'word_type must be a non-empty string';
        } elseif (!in_array(strtolower($wordType), $allowedTypes, true)) {
            $errors[] = 'word_type must be one of: ' . implode(', ', $allowedTypes);
        }
    }

    if (array_key_exists('conjugation', $meta)) {
        $conj = $meta['conjugation'];
        if (!is_array($conj)) {
            $errors[] = 'conjugation must be an object';
        } else {
            foreach ($conj as $tense => $value) {
                if (!is_string($tense) || $tense === '') {
                    $errors[] = 'conjugation keys must be non-empty strings';
                    break;
                }
                if (is_array($value)) {
                    foreach ($value as $i => $form) {
                        if (!is_string($form) || $form === '') {
                            $errors[] = "conjugation[$tense][$i] must be a non-empty string";
                        }
                    }
                } elseif (!is_string($value) || $value === '') {
                    $errors[] = "conjugation[$tense] must be a string or array of strings";
                }
            }
        }
    }

    if (array_key_exists('sessions', $meta)) {
        $sessions = $meta['sessions'];
        if (!is_array($sessions)) {
            $errors[] = 'sessions must be an object';
        } else {
            foreach ($sessions as $sessionId => $stats) {
                if (!is_string($sessionId) || $sessionId === '') {
                    $errors[] = 'sessions keys must be non-empty strings';
                    break;
                }
                if (!is_array($stats)) {
                    $errors[] = "sessions[$sessionId] must be an object";
                    continue;
                }
                if (!array_key_exists('right', $stats) || !is_int($stats['right']) || $stats['right'] < 0) {
                    $errors[] = "sessions[$sessionId].right must be a non-negative integer";
                }
                if (!array_key_exists('wrong', $stats) || !is_int($stats['wrong']) || $stats['wrong'] < 0) {
                    $errors[] = "sessions[$sessionId].wrong must be a non-negative integer";
                }
            }
        }
    }

    return empty($errors);
}