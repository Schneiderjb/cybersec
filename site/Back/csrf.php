<?php
// CORS pour autoriser localhost:8000
header("Access-Control-Allow-Origin: http://localhost:8000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, X-CSRF-Token");
header("Access-Control-Allow-Methods: GET, OPTIONS");

// 🔥 Réponse aux requêtes OPTIONS (préflight)
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

require_once "Session.php";
secure_session_start();

// 🔥 Envoi du token CSRF
header("Content-Type: application/json");

echo json_encode([
    "csrf_token" => $_SESSION["csrf_token"]
]);
