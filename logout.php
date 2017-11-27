<?php

session_start();
$_SESSION = [];

$params = session_get_cookie_params();

setcookie(
    session_name(),         // nombre
    '',                     // valor
    1,                      // 1 segundo despues de Enero del 1970
    $params['path'],        // ruta (no lo sé)
    $params['domain'],      // dominio (tampoco lo sé)
    $params['secure'],      // secure
    $params['httponly']    //  httponly tampoco
);
session_destroy();
header('Location: index.php');
