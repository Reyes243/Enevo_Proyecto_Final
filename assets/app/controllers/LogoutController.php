<?php
session_start();

$_SESSION = array();

// Destruir la cookie de sesión si existe
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

session_destroy();

// Calcular ruta base correctamente
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$scriptPath = $_SERVER['SCRIPT_NAME'];

$assetsPos = strpos($scriptPath, '/assets/');
if ($assetsPos !== false) {
    $projectPath = substr($scriptPath, 0, $assetsPos);
} else {
    $projectPath = '';
}

$baseUrl = "{$protocol}://{$host}{$projectPath}";

header("Location: {$baseUrl}/index.html");
exit();
?>