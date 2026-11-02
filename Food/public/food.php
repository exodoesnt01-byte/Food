<?php
require_once __DIR__ . '/../config/FoodDatabase.php';
require_once __DIR__ . '/../classes/Food.php';

function isValidDate(string $value): bool
{
    $date = DateTime::createFromFormat('!Y-m-d', $value);
    $errors = DateTime::getLastErrors();

    return $date !== false
        && $date->format('Y-m-d') === $value
        && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0));
}

$error = '';
$foods = [];
$connection = null;

try {
    $connection = (new FoodDatabase())->connect();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim((string) ($_POST['name'] ?? ''));
        $category = trim((string) ($_POST['category'] ?? ''));
        $cuisine = trim((string) ($_POST['cuisine'] ?? ''));
        $priceInput = trim((string) ($_POST['price'] ?? ''));
        $quantityInput = trim((string) ($_POST['quantity'] ?? ''));
        $expirationDate = trim((string) ($_POST['expiration_date'] ?? ''));

        if ($name === '' || $category === '' || $cuisine === '') {
            $error = 'Name, category, and cuisine are required.';
        } elseif (!is_numeric($priceInput) || (float) $priceInput < 0) {
            $error = 'Price must be a non-negative number.';
        } elseif (filter_var($quantityInput, FILTER_VALIDATE_INT) === false || (int) $quantityInput < 0) {
            $error = 'Quantity must be a non-negative whole number.';
        } elseif (
            $expirationDate !== ''
            && !isValidDate($expirationDate)
        ) {
            $error = 'Expiration date must be a valid date.';
        } else {
            $food = new Food(
                $name,
                $category,
                $cuisine,
                (float) $priceInput,
                (int) $quantityInput,
                $expirationDate === '' ? null : $expirationDate
            );
            $food->insert($connection);

            header('Location: food.php?added=1');
            exit;
        }
    }

    $foods = Food::getAll($connection);
} catch (mysqli_sql_exception $exception) {
    error_log($exception->getMessage());
    $error = 'Database error. Start MySQL and import food.sql into phpMyAdmin, then try again.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Food Inventory</title>
</head>
<body>
    <h1>Food Inventory</h1>

    <?php if (isset($_GET['added'])): ?>
        <p>Food added successfully.</p>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <p role="alert"><?= escapeHtml($error) ?></p>
    <?php endif; ?>

    <h2>Add food</h2>
    <form method="post">
        <p><label>Name: <input name="name" required></label></p>
        <p><label>Category: <input name="category" required></label></p>
        <p><label>Cuisine: <input name="cuisine" required></label></p>
        <p><label>Price: <input name="price" type="number" min="0" step="0.01" required></label></p>
        <p><label>Quantity: <input name="quantity" type="number" min="0" step="1" required></label></p>
        <p><label>Expiration date: <input name="expiration_date" type="date"></label></p>
        <button type="submit">Add food</button>
    </form>
</body>
</html>
