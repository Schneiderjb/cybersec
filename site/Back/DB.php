<?php

function getDBConnection()
{
    $dsn = 'sqlite:' . __DIR__ . '/DataBase/users.db';
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
}
