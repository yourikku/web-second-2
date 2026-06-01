<?php

require_once 'functions.php';

echo "Добро пожаловать в онлайн-библиотеку 'Онлайн-библиотека'!\n";

while (true) {
    echo "\nМеню:\n";
    echo "1. Просмотреть каталог книг\n";
    echo "2. Добавить книгу в корзину (укажите ID книги)\n";
    echo "3. Посмотреть корзину\n";
    echo "4. Подтвердить оплату\n";
    echo "5. Выйти\n";

    echo "\nВыберите действие (1–5): ";
    $choice = trim(fgets(STDIN));

    switch ($choice) {
        case '1':
            showCatalog();
            break;

        case '2':
            echo "Введите ID книги: ";
            $productId = (int)trim(fgets(STDIN));
            addToCart($productId);
            break;

        case '3':
            showCart();
            break;

        case '4':
            confirmPayment();
            break;

        case '5':
            echo "До свидания!\n";
            exit(0);

        default:
            echo "\nНеверный выбор. Попробуйте ещё раз.\n";
    }
}
?>
