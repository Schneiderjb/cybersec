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

// Vérification du token CSRF envoyé par le client
$csrf = $_SERVER["HTTP_X_CSRF_TOKEN"] ?? "";
if (!hash_equals($_SESSION["csrf_token"], $csrf)) {
    http_response_code(403);
    echo json_encode(["error" => "CSRF token invalide"]);
    exit;
}

//Lecture du JSON envoyé par fetch()
$data = json_decode(file_get_contents("php://input"), true);

// Vérification du Captcha
if (!isset($_SESSION["captcha_answer"])) {
    http_response_code(400);
    echo json_encode(["error" => "Captcha manquant"]);
    exit;
}

// Vérification du Captcha
if (($data["captcha"] ?? "") != $_SESSION["captcha_answer"]) {
    http_response_code(400);
    echo json_encode(["error" => "Captcha incorrect"]);
    exit;
}

$username = htmlspecialchars($data["username"] ?? "", ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars($data["email"] ?? "", ENT_QUOTES, 'UTF-8');
$password = $data["password"] ?? "";

if ($username === "" || $email === "" || $password === "") {
    http_response_code(400);
    echo json_encode(["error" => "Champs manquants"]);
    exit;
}

// Politique de mot de passe OWASP
$pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{12,}$/";

if (!preg_match($pattern, $password)) {
    http_response_code(400);
    echo json_encode(["error" => "Mot de passe trop faible"]);
    exit;
}

$pdo = getDBConnection();

// Vérifier si l'utilisateur existe déjà
$stmt = $pdo->prepare("SELECT id FROM users WHERE login = ?");
$stmt->execute([$username]);

if ($stmt->fetch()) {
    http_response_code(400);
    echo json_encode(["error" => "Impossible de créer le compte"]);
    exit;
}

// Hachage du mot de passe
$hash = password_hash($password, PASSWORD_ARGON2ID);

// Insertion
$stmt = $pdo->prepare("INSERT INTO users (login, email, password_hash) VALUES (?, ?, ?)");
$stmt->execute([$username, $email, $hash]);

echo json_encode(["status" => "ok"]);
