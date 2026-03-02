<?php
require 'vendor/autoload.php';

\Stripe\Stripe::setApiKey('sk_test_your_secret_key');

// Stripe Checkout handler
if (isset($_POST['stripe_checkout'])) {

    $product_name = $_POST['product_name'];
    $amount = $_POST['amount'];

    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'uah',
                'product_data' => [
                    'name' => $product_name,
                ],
                'unit_amount' => $amount,
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => 'http://localhost/sales.php?success=1',
        'cancel_url' => 'http://localhost/sales.php?cancel=1',
    ]);

    header("Location: " . $session->url);
    exit();
}

// Connect to DB
$conn = mysqli_connect("localhost", "root", "", "nuzp_proj2");

// Default query
$sql = "SELECT product_name, month_of_sale, sold_quantity, sale_price 
        FROM sales 
        INNER JOIN products ON sales.product_code = products.id";

// Filters
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['min_price']) || isset($_POST['max_price'])) {
        $min_price = $_POST['min_price'];
        $max_price = $_POST['max_price'];

        $sql .= " WHERE sale_price >= $min_price AND sale_price <= $max_price";

    } else if (isset($_POST['start_date']) || isset($_POST['end_date'])) {
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];

        $sql .= " WHERE month_of_sale >= '$start_date' AND month_of_sale <= '$end_date'";
    }
}

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Продаж товарів</title>
    <link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body class="background-image">

<div class="green-banner">
    <p>Вітаємо!</p>
</div>

<table width="100%">
<tr>
<td colspan="3" class="header">
    <img src="Images/img1.jpeg" alt="Header image" />
</td>
</tr>

<tr>
<td class="left_col">
<?php include("menu.php"); ?>
</td>

<td class="center_col">
<h1>Продаж товарів</h1>
<p><strong>Оплата здійснюється через Stripe API.</strong></p>

<?php
if (isset($_GET['success'])) {
    echo "<p style='color:green;'>Оплата успішна!</p>";
}
if (isset($_GET['cancel'])) {
    echo "<p style='color:red;'>Оплату скасовано.</p>";
}
?>

<!-- Price Filter -->
<form method="post">
    Мінімальна ціна:
    <input type="number" name="min_price">
    Максимальна ціна:
    <input type="number" name="max_price">
    <input type="submit" value="Фільтрувати за ціною">
</form>

<!-- Date Filter -->
<form method="post">
    Початкова дата:
    <input type="date" name="start_date">
    Кінцева дата:
    <input type="date" name="end_date">
    <input type="submit" value="Фільтрувати за датою">
</form>

<br>

<table cellpadding="5" width="100%" border="1">
<tr>
    <th>Назва товару</th>
    <th>Місяць продажу</th>
    <th>Кількість</th>
    <th>Ціна</th>
    <th>Оплата</th>
</tr>

<?php while ($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?php echo $row['product_name']; ?></td>
    <td><?php echo $row['month_of_sale']; ?></td>
    <td><?php echo $row['sold_quantity']; ?></td>
    <td><?php echo $row['sale_price']; ?> ₴</td>
    <td>
        <form method="post">
            <input type="hidden" name="product_name" value="<?php echo $row['product_name']; ?>">
            <input type="hidden" name="amount" value="<?php echo $row['sale_price'] * 100; ?>">
            <button type="submit" name="stripe_checkout">
                Оплатити через Stripe
            </button>
        </form>
    </td>
</tr>
<?php } ?>

</table>
</td>
</tr>

<tr>
<td colspan="3" class="footer">© 2023</td>
</tr>

</table>
</body>
</html>
