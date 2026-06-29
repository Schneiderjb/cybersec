<?php

// CORS
header("Access-Control-Allow-Origin: http://localhost:8000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, X-CSRF-Token");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

require_once "DB.php";
require_once "Session.php";

secure_session_start();

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["error" => "Méthode non autorisée"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

// Vérification de username et password
$username = $data["username"] ?? "";
$password = $data["password"] ?? "";

if ($username === "" || $password === "") {
    http_response_code(400);
    echo json_encode(["error" => "Champs manquants"]);
    exit;
}

$pdo = getDBConnection();

// Vérifier si l'utilisateur existe déjà
$stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE login = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérifier si l'utilisateur a le bon password
if (!$user || !password_verify($password, $user["password_hash"])) {
    http_response_code(401);
    echo json_encode(["error" => "Identifiants invalides"]);
    exit;
}

session_regenerate_id(true);

$_SESSION["user_id"] = $user["id"];

echo json_encode(["status" => "ok"]);
