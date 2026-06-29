<?php
header("Access-Control-Allow-Origin: http://localhost:8000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, OPTIONS");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

require_once "Session.php";
secure_session_start();

// Génère deux nombres aléatoires
$a = rand(1, 9);
$b = rand(1, 9);

// Stocke la réponse dans la session
$_SESSION["captcha_answer"] = $a + $b;

header("Content-Type: application/json");

// Envoie la question au client
echo json_encode([
    "question" => "$a + $b"
]);
