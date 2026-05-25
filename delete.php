<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
?>

<?php
// Corrected delete.php

require "config/config.php";
require "config/db.php";

$orderNumber = "";
$orderNumber_err = "";

// If id provided via GET, prefill (treat '0' as a valid id)
if (isset($_GET["id"])) {
    $tmp = trim($_GET["id"]);
    if ($tmp !== '') {
        $orderNumber = $tmp;
    }
}

// Handle initial submit (show confirmation) and confirmed delete
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // get posted order number (either from initial form or hidden confirm input)
    $input_orderNumber = isset($_POST["order_number"]) ? trim($_POST["order_number"]) : "";

    // Basic validation: require non-empty (treat '0' as valid) and allow letters, numbers, spaces and dashes
    if ($input_orderNumber === '') {
        $orderNumber_err = "Please enter an order number.";
    } elseif (!preg_match("/^[A-Za-z0-9\s-]+$/", $input_orderNumber)) {
        $orderNumber_err = "Please enter a valid order number (letters, numbers, spaces, and dashes only).";
    } else {
        $orderNumber = $input_orderNumber;
    }

    // If this is the initial submit (not final confirmation), check whether the record exists.
    // This prevents showing the confirmation for a non-existent OrderNumber.
    if (empty($orderNumber_err) && !isset($_POST['confirm_delete'])) {
        $checkSql = "SELECT 1 FROM sales WHERE OrderNumber = ? LIMIT 1";
        if ($chk = mysqli_prepare($conn, $checkSql)) {
            mysqli_stmt_bind_param($chk, "s", $orderNumber);
            if (mysqli_stmt_execute($chk)) {
                mysqli_stmt_store_result($chk);
                if (mysqli_stmt_num_rows($chk) === 0) {
                    $orderNumber_err = "No record found for Order Number: " . htmlspecialchars($orderNumber);
                }
            } else {
                // if check fails for some reason, set a generic error so confirmation is not shown
                $orderNumber_err = "Could not verify order number. Please try again.";
            }
            mysqli_stmt_close($chk);
        } else {
            $orderNumber_err = "Database error: could not prepare existence check.";
        }
    }

    // If user clicked the final confirm delete button and validation passed, perform deletion
    if (empty($orderNumber_err) && isset($_POST['confirm_delete'])) {
        $sql = "DELETE FROM sales WHERE OrderNumber = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $orderNumber);
            if (mysqli_stmt_execute($stmt)) {
                $affected = mysqli_stmt_affected_rows($stmt);
                mysqli_stmt_close($stmt);
                if ($affected > 0) {
                    mysqli_close($conn);
                    header("Location: index.php");
                    exit();
                } else {
                    // No rows deleted -> record didn't exist (or was already deleted)
                    $orderNumber_err = "No matching record found to delete for Order Number: " . htmlspecialchars($orderNumber);
                }
            } else {
                $orderNumber_err = "Oops! Something went wrong. Please try again later.";
                mysqli_stmt_close($stmt);
            }
        } else {
            $orderNumber_err = "Database error: could not prepare statement.";
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delete Record</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>.wrapper{width:600px;margin:0 auto;}</style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <h2 class="mt-5 mb-3">Delete Record</h2>
            <p>Please enter your order number to confirm deletion of sale record.</p>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label>Order Number</label>
                    <input type="text" name="order_number" class="form-control <?php echo (!empty($orderNumber_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($orderNumber); ?>">
                    <span class="invalid-feedback"><?php echo $orderNumber_err; ?></span>
                </div>

                <?php if ($_SERVER["REQUEST_METHOD"] === "POST" && empty($orderNumber_err) && !isset($_POST['confirm_delete'])): ?>
                    <div class="alert alert-danger">
                        <p>Are you sure you want to delete this sale record?</p>
                    </div>
                    <input type="hidden" name="order_number" value="<?php echo htmlspecialchars($orderNumber); ?>">
                    <input type="submit" name="confirm_delete" class="btn btn-danger" value="Delete">
                    <a href="index.php" class="btn btn-secondary ml-2">Cancel</a>
                <?php else: ?>
                    <input type="submit" name="submit" class="btn btn-primary" value="Submit">
                    <a href="index.php" class="btn btn-secondary ml-2">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <?php include 'inc/footer.php'; ?>
</body>
</html>
