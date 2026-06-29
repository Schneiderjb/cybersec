<?php

function secure_session_start()
{
    // Cookies de session sécurisés
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => 'localhost',
        'secure' => true,
        'httponly' => true,    // JS ne peut pas lire le cookie
        'samesite' => 'Strict' // empêche les requêtes CSRF venant d'autres sites
    ]);

    session_start(); // démarre la session

    // 🔥 Protection CSRF : génération d'un token unique
    // Ce token sera envoyé au client et vérifié à chaque requête POST
    if (!isset($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
    }
}
