<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$products = [
    ["id" => 1, "name" => "Мастер и Маргарита", "price" => 650, "description" => "М. Булгаков. Роман, жанр: классика"],
    ["id" => 2, "name" => "1984", "price" => 550, "description" => "Дж. Оруэлл. Роман-антиутопия"],
    ["id" => 3, "name" => "Преступление и наказание", "price" => 700, "description" => "Ф. Достоевский. Психологический роман"],
    ["id" => 4, "name" => "Гарри Поттер и философский камень", "price" => 800, "description" => "Дж. Роулинг. Фэнтези"],
    ["id" => 5, "name" => "Война и мир", "price" => 900, "description" => "Л. Толстой. Исторический роман"],
];

$cart = [];

$config = [
    "smtp_server" => "smtp.gmail.com",
    "smtp_port" => 587,
    "email_address" => "library@gmail.com",
    "email_password" => "пароль",
    "admin_email" => "manager@gmail.com",
];

function showCatalog(): void
{
    global $products;
    echo "\nКАТАЛОГ КНИГ" . PHP_EOL;
    foreach ($products as $product) {
        echo "ID: {$product['id']}, Название: {$product['name']}, Цена: {$product['price']} руб., Описание: {$product['description']}" . PHP_EOL;
    }
    echo "----------------------\n";
}

function addToCart(int $productId): void
{
    global $products, $cart;
    foreach ($products as $product) {
        if ($product['id'] == $productId) {
            $cart[] = $product;
            echo "\nКнига '{$product['name']}' добавлена в корзину!" . PHP_EOL;
            return;
        }
    }
    echo "\nКнига не найдена!" . PHP_EOL;
}

function showCart(): void
{
    global $cart;
    if (empty($cart)) {
        echo "\nВаша корзина пуста!" . PHP_EOL;
        return;
    }

    echo "\nВАША КОРЗИНА" . PHP_EOL;
    $total = 0;
    foreach ($cart as $product) {
        echo "Название: {$product['name']}, Цена: {$product['price']} руб." . PHP_EOL;
        $total += $product['price'];
    }
    echo "Общая сумма: {$total} руб." . PHP_EOL;
    echo "----------------------\n";
}

function confirmPayment(): void
{
    global $cart, $config;
    if (empty($cart)) {
        echo "\nКорзина пуста! Добавьте книги." . PHP_EOL;
        return;
    }

    $total = array_sum(array_column($cart, 'price'));
    echo "\nПодтверждение оплаты:" . PHP_EOL;
    echo "Общая сумма: {$total} руб." . PHP_EOL;

    echo "Подтвердите оплату (да/нет): ";
    $answer = trim(fgets(STDIN));

    if (strtolower($answer) === 'да') {
        echo "\nОплата подтверждена! Спасибо за покупку!" . PHP_EOL;
        sendOrderNotification($cart, $total);
        $cart = [];
    } else {
        echo "\nОплата отменена." . PHP_EOL;
    }
}

function sendOrderNotification(array $cart, float $total): void
{
    global $config;

    require __DIR__ . '/vendor/phpmailer/phpmailer/src/Exception.php';
    require __DIR__ . '/vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require __DIR__ . '/vendor/phpmailer/phpmailer/src/SMTP.php';

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = $config['smtp_server'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['email_address'];
        $mail->Password = $config['email_password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $config['smtp_port'];

        $mail->setFrom($config['email_address'], 'Онлайн-библиотека');
        $mail->addAddress($config['admin_email'], 'Администратор');

        $mail->Subject = 'Новый заказ в "Онлайн-библиотека"';
        $body = "Новый заказ подтверждён!\n\n";
        $body .= "Детали заказа:\n";
        foreach ($cart as $product) {
            $body .= "- {$product['name']} — {$product['price']} руб.\n";
        }
        $body .= "\nОбщая сумма: {$total} руб.\n";
        $body .= "Дата заказа: " . date('Y-m-d H:i:s') . "\n";
        $mail->Body = $body;

        $mail->send();
        echo "Уведомление администратору отправлено!" . PHP_EOL;
    } catch (Exception $e) {
        echo "Ошибка при отправке уведомления: {$e->getMessage()}" . PHP_EOL;
    }
}
?>
