<?php
/**
 * Taiko Tournament API
 * File: api/tournament.php
 *
 * Actions:
 *   save  - create or update a tournament JSON in /data/
 *   load  - load a tournament by 6-digit code
 *
 * Expected folder structure:
 *   /                  → index.html
 *   /api/              → tournament.php  (this file)
 *   /data/             → *.json files    (auto-created, must be writable)
 */

// CORS / headers
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Parse JSON body
$body = file_get_contents('php://input');
$data = json_decode($body, true);

if (json_last_error() !== JSON_ERROR_NONE || !isset($data['action'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid JSON or missing action']);
    exit;
}

// Data directory
// This file lives in /api/ so we go one level up to reach /data/
$dataDir = dirname(__DIR__) . '/data';

// Create /data/ if it doesn't exist yet
if (!is_dir($dataDir)) {
    if (!mkdir($dataDir, 0755, true)) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Cannot create data directory']);
        exit;
    }
}

// Helpers

/**
 * Build the file path for a given 6-digit code.
 */
function tournamentPath(string $dataDir, string $code): string {
    // Sanitise: only digits, exactly 6
    $safe = preg_replace('/\D/', '', $code);
    if (strlen($safe) !== 6) return '';
    return $dataDir . '/' . $safe . '.json';
}

/**
 * Atomic write: write to a temp file then rename.
 */
function atomicWrite(string $path, string $content): bool {
    $tmp = $path . '.tmp.' . uniqid();
    if (file_put_contents($tmp, $content, LOCK_EX) === false) return false;
    return rename($tmp, $path);
}

// Router
$action = $data['action'];

switch ($action) {

    // Save
    case 'save': {
        if (!isset($data['tournament']) || !is_array($data['tournament'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing tournament object']);
            exit;
        }

        $t    = $data['tournament'];
        $code = isset($t['code']) ? trim($t['code']) : '';

        if (!preg_match('/^\d{6}$/', $code)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid code format']);
            exit;
        }

        $path = tournamentPath($dataDir, $code);
        if ($path === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid code']);
            exit;
        }

        // Add server-side timestamps
        if (!isset($t['createdAt'])) {
            $t['createdAt'] = date('c');   // ISO 8601
        }
        $t['updatedAt'] = date('c');

        $json = json_encode($t, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        if (!atomicWrite($path, $json)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Could not write file. Check /data/ permissions.']);
            exit;
        }

        echo json_encode(['success' => true, 'code' => $code]);
        break;
    }

    // Load
    case 'load': {
        $code = isset($data['code']) ? trim($data['code']) : '';

        if (!preg_match('/^\d{6}$/', $code)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid code format']);
            exit;
        }

        $path = tournamentPath($dataDir, $code);
        if ($path === '' || !file_exists($path)) {
            echo json_encode(['success' => false, 'error' => 'Tournament not found']);
            exit;
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Could not read file']);
            exit;
        }

        $tournament = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Corrupt tournament file']);
            exit;
        }

        echo json_encode(['success' => true, 'tournament' => $tournament]);
        break;
    }

    // List (optional debug)
    case 'list': {
        // Return just the codes and names. Handy for debugging
        $files = glob($dataDir . '/*.json') ?: [];
        $list  = [];
        foreach ($files as $f) {
            $raw = file_get_contents($f);
            if (!$raw) continue;
            $t = json_decode($raw, true);
            if (!$t) continue;
            $list[] = [
                'code'      => $t['code']      ?? basename($f, '.json'),
                'name'      => $t['name']      ?? '-',
                'updatedAt' => $t['updatedAt'] ?? null,
                'players'   => count($t['players'] ?? []),
                'rounds'    => count($t['rounds']  ?? []),
            ];
        }
        // Sort newest first
        usort($list, fn($a,$b) => strcmp($b['updatedAt'] ?? '', $a['updatedAt'] ?? ''));
        echo json_encode(['success' => true, 'tournaments' => $list]);
        break;
    }

    default: {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Unknown action: ' . htmlspecialchars($action)]);
    }
}
