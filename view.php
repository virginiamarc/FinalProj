<?php
require('config/config.php');
require('config/db.php');

$order_number = '';
$selected_color = '';

// Accept order number from POST (form) or GET (link)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit']) && !empty(trim($_POST['order_number']))) {
    $order_number = trim($_POST['order_number']);
} elseif (!empty($_GET['order_number'])) {
    $order_number = trim($_GET['order_number']);
} elseif (!empty($_GET['id'])) {
    // allow legacy "id" parameter from links
    $order_number = trim($_GET['id']);
}

// If an order number was provided, fetch and display the single record
if ($order_number !== '') {
    $sql = "SELECT OrderNumber, ItemNumber, Description, Size, Color, Price FROM sales WHERE OrderNumber = ? LIMIT 1";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, 's', $order_number);
        if (mysqli_stmt_execute($stmt)) {
            // get associative result (requires mysqlnd)
            $res = mysqli_stmt_get_result($stmt);
            if ($res && $row = mysqli_fetch_assoc($res)) {
                echo "<div class=\"container mt-4\">";
                echo "<h3>Sale Record Details</h3>";
                echo "<table class=\"table table-bordered\">";
                echo "<tr><th>Order Number</th><td>" . htmlspecialchars($row['OrderNumber']) . "</td></tr>";
                echo "<tr><th>Item Number</th><td>" . htmlspecialchars($row['ItemNumber']) . "</td></tr>";
                echo "<tr><th>Description</th><td>" . htmlspecialchars($row['Description']) . "</td></tr>";
                echo "<tr><th>Size</th><td>" . htmlspecialchars($row['Size']) . "</td></tr>";
                echo "<tr><th>Color</th><td>" . htmlspecialchars($row['Color']) . "</td></tr>";
                echo "<tr><th>Price</th><td>" . htmlspecialchars($row['Price']) . "</td></tr>";
                echo "</table>";
                echo "<a href=\"index.php\" class=\"btn btn-secondary\">Back to Main Page</a> ";
                echo "<a href=\"view.php\" class=\"btn btn-outline-secondary\">View/Search Another</a>";
                echo "</div>";
            } else {
                echo "<div class=\"container mt-4\">";
                echo "<div class=\"alert alert-warning\">No record found for Order Number: " . htmlspecialchars($order_number) . "</div>";
                echo "<a href=\"view.php\" class=\"btn btn-secondary\">Back</a>";
                echo "</div>";
            }
            if ($res && $res instanceof mysqli_result) {
                mysqli_free_result($res);
            }
        } else {
            echo "<div class=\"alert alert-danger\">Failed to execute statement.</div>";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<div class=\"alert alert-danger\">Failed to prepare statement.</div>";
    }
}

// Handle color filter (separate action) using prepared statement
if (isset($_POST['filter']) && !empty($_POST['color'])) {
    $selected_color = trim($_POST['color']);
    $sql = "SELECT OrderNumber, ItemNumber, Description, Size, Color, Price FROM sales WHERE Color = ? LIMIT 500";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, 's', $selected_color);
        if (mysqli_stmt_execute($stmt)) {
            $res = mysqli_stmt_get_result($stmt);
            if ($res) {
                $records = mysqli_fetch_all($res, MYSQLI_ASSOC);
                $count = count($records);
            } else {
                $records = [];
                $count = 0;
            }
            // output
            echo "<div class=\"container mt-4\">";
            echo "<p>Number of records found: " . $count . "</p>";
            if ($count > 0) {
                echo "<h3>Records with Color: " . htmlspecialchars($selected_color) . "</h3>";
                echo "<table class=\"table table-striped\">";
                echo "<thead><tr><th>Order Number</th><th>Item Number</th><th>Description</th><th>Size</th><th>Color</th><th>Price</th></tr></thead>";
                echo "<tbody>";
                foreach ($records as $record) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($record['OrderNumber']) . "</td>";
                    echo "<td>" . htmlspecialchars($record['ItemNumber']) . "</td>";
                    echo "<td>" . htmlspecialchars($record['Description']) . "</td>";
                    echo "<td>" . htmlspecialchars($record['Size']) . "</td>";
                    echo "<td>" . htmlspecialchars($record['Color']) . "</td>";
                    echo "<td>" . htmlspecialchars($record['Price']) . "</td>";
                    echo "</tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<div class=\"alert alert-warning\">No records found for Color: " . htmlspecialchars($selected_color) . "</div>";
            }
            if ($res && $res instanceof mysqli_result) {
                mysqli_free_result($res);
            }
            echo "</div>";
        } else {
            echo "<div class=\"alert alert-danger\">Failed to execute color query.</div>";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<div class=\"alert alert-danger\">Failed to prepare color query.</div>";
    }
}

// Close connection now that DB work is done
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Records</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style >.wrapper{width:600px;margin:0 auto;}</style>
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-primary">View Sale Records</h2>
        <form method="POST" action="view.php">
            <div class="form-group input-group mb-3">
                <label for="order_number">Enter Order Number:</label>
                <input type="text" id="order_number" name="order_number" class="form-control" value="<?php echo htmlspecialchars($order_number); ?>">
                <button class="btn btn-primary" type="submit" name="submit">View Record</button>
            </div>
        </form>
        
        <hr>
        
        <h3 class="text-secondary">Filter by Color</h3>
        <form method="POST" action="view.php">
            <div class="form-group input-group mb-3">
                <label for="color" class="input-group-text">Select Color:</label>
                <select name="color" id="color" class="form-control">
                    <option value="">-- Select Color --</option>
                    <option value="Red">Red</option>
                    <option value="Blue">Blue</option>
                    <option value="Green">Green</option>
                    <option value="Yellow">Yellow</option>
                    <option value="Black">Black</option>
                    <option value="White">White</option>
                </select>
                <button type="submit" name="filter" class="btn btn-primary">Filter</button>
            </div>
        </form>
        <a href="view.php" class="btn btn-secondary ml-2">Reset</a>
        <a href="index.php" class="btn btn-secondary">Back to Main Page</a>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <?php include 'inc/footer.php'; ?>
</body>
</html>