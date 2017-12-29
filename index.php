<?php

include_once 'RabotaApi/Client.php';
include_once 'RabotaApi/Response.php';
include_once 'RabotaApi/Exception.php';

use RabotaApi\Client;
use RabotaApi\Exception;

// Создаем API клиента
$client = new Client(
    'Aksgh7ch','1PpWiBv6aUhbs6Dn4P4bAM321G3LYgQn',
    $_SESSION['token'], $_SESSION['expires']
);

// Если редирект с авторизации приложения с токеном
if (isset($_GET['code'])) {
    try {
        $client->getAccessTokenFromCode($_GET['code']);
    } catch (Exception $e) {
        echo "Ошибка: {$e->getMessage()}";
    }
    // Редиректим на себя же, чтоб убрать код из GET параметра
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header('Location: http://'.$_SERVER['HTTP_HOST'], true, 301);
    exit;
}

// Неавторизирован
if (!$client->getAccessToken() && !isset($_GET['auth'])) {
    echo '<a href="?auth">Вход</a>';
    exit;
}

// Авторизация приложения
if (!$client->getAccessToken() && isset($_GET['auth'])) {
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header('Location: '.$client->getAuthenticationUrl('http://'.$_SERVER['HTTP_HOST']), true, 301);
    exit;
}

// Авторизированное состояние
try {
    $response = $client->fetch(
        '/v4/users/get.json',
        ['ids' => ['me'], 'fields' => 'id,name,link,avatar_big']
    );
    echo '<pre>';
    print_r($response->getJsonDecode());
    echo '</pre>';
} catch (Exception $e) {
    echo "Ошибка: {$e->getMessage()}";
}

