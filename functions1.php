<?php

declare(strict_types=1);

const DB_HOST = '127.0.0.1';
const DB_PORT = 3304;
const DB_USER = 'root';
const DB_PASSWORD = '123123';
const DB_NAME = 'library';
const DB_DSN = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME;

function connectDB(): PDO
{
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
        $pdo = new PDO(DB_DSN, DB_USER, DB_PASSWORD, $options);
        $pdo->exec('SET NAMES utf8mb4');
        return $pdo;
    } catch (PDOException $e) {
        throw new Exception('Не удалось подключиться к БД: ' . $e->getMessage());
    }
}

function createProduct(string $name, float $price, string $category, int $stock): bool
{
    $pdo = connectDB();
    $sql = 'INSERT INTO books (name, price, category, stock) VALUES (?, ?, ?, ?)';
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$name, $price, $category, $stock]);
    $pdo = null;
    return $result;
}

function readProducts(?string $category = null): array
{
    $pdo = connectDB();
    $sql = 'SELECT * FROM books';

    if ($category) {
        $sql .= ' WHERE category = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$category]);
    } else {
        $stmt = $pdo->query($sql);
    }

    $products = $stmt->fetchAll();
    $pdo = null;
    return $products;
}

function updateProduct(int $id, string $field, mixed $value): bool
{
    $pdo = connectDB();
    $sql = "UPDATE books SET $field = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$value, $id]);
    $pdo = null;
    return $result;
}

function deleteProduct(int $id): bool
{
    $pdo = connectDB();
    $sql = 'DELETE FROM books WHERE id = ?';
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([$id]);
    $pdo = null;
    return $result;
}

function getVisits(string $period): int
{
    $pdo = connectDB();
    $days = match ($period) {
        'day' => 1,
        'week' => 7,
        'month' => 30,
        default => 1
    };

    $sql = 'SELECT COUNT(*) as count FROM visits WHERE date >= CURDATE() - INTERVAL ? DAY';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$days]);
    $row = $stmt->fetch();
    $pdo = null;
    return (int)$row['count'];
}
?>
