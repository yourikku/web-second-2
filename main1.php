<?php
require 'functions1.php';

echo "Доступ разрешён!\n";

function showMenu() {
    echo "\n=== Меню администратора (Онлайн-библиотека) ===\n";
    echo "1. Добавить книгу\n";
    echo "2. Показать книги\n";
    echo "3. Обновить книгу\n";
    echo "4. Удалить книгу\n";
    echo "5. Статистика посещений\n";
    echo "6. Выйти\n";
    echo "Выберите пункт меню (1-6): ";
}

function processCommand($choice) {
    switch ($choice) {
        case '1':
            echo "Введите данные книги (название, цена, жанр, количество через пробел): ";
            $input = trim(fgets(STDIN));
            $params = explode(' ', $input);

            if (count($params) !== 4) {
                echo "Ошибка: нужно 4 аргумента (название, цена, жанр, количество)\n";
                return;
            }

            list($name, $price, $category, $stock) = $params;

            if (!is_numeric($price) || !is_numeric($stock)) {
                echo "Ошибка типов данных\n";
                return;
            }

            if (createProduct((string)$name, (float)$price, (string)$category, (int)$stock)) {
                echo "Книга добавлена\n";
            } else {
                echo "Ошибка при добавлении\n";
            }
            break;

        case '2':
            echo "Введите жанр (или оставьте пустым для всех): ";
            $category = trim(fgets(STDIN));
            $products = readProducts((string)$category ?: null);

            if (empty($products)) {
                echo "Книг нет\n";
            } else {
                foreach ($products as $product) {
                    echo sprintf(
                        "%s. %s (%d руб., жанр: %s, в наличии: %d)\n",
                        $product['id'],
                        $product['name'],
                        (int)$product['price'],
                        $product['category'],
                        (int)$product['stock']
                    );
                }
            }
            break;

        case '3':
            echo "Введите ID книги, поле и значение для обновления (через пробел, например: 1 name НовоеНазвание): ";
            $input = trim(fgets(STDIN));
            $params = explode(' ', $input);

            if (count($params) !== 3) {
                echo "Ошибка: нужно 3 аргумента (ID, поле, значение)\n";
                return;
            }

            list($id, $field, $value) = $params;

            if (!is_numeric($id) || !in_array((string)$field, ['name', 'price', 'category', 'stock'])) {
                echo "Ошибка параметров\n";
                return;
            }

            if (updateProduct((int)$id, (string)$field, (string)$value)) {
                echo "Книга обновлена\n";
            } else {
                echo "Ошибка обновления\n";
            }
            break;

        case '4':
            echo "Введите ID книги для удаления: ";
            $id = trim(fgets(STDIN));

            if (!is_numeric($id)) {
                echo "Ошибка: ID должен быть числом\n";
                return;
            }

            if (deleteProduct((int)$id)) {
                echo "Книга удалена\n";
            } else {
                echo "Ошибка удаления\n";
            }
            break;

        case '5':
            echo "Введите период (day/week/month): ";
            $period = trim(fgets(STDIN));

            if (!in_array(strtolower($period), ['day', 'week', 'month'])) {
                echo "Неверный период\n";
                return;
            }

            $visits = getVisits($period);
            echo "Посещений за $period: $visits\n";
            break;

        case '6':
            echo "До свидания!\n";
            exit;

        default:
            echo "Неверный выбор. Пожалуйста, выберите пункт от 1 до 6.\n";
    }
}

while (true) {
    showMenu();
    $choice = trim(fgets(STDIN));
    processCommand($choice);
}
