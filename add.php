<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
?>

<?php
// Include config and db files
require("config/config.php");
require("config/db.php");
 
// Define variables and initialize with empty values
$itemNumber = $description = $size = $color = $price = "";
$itemNumber_err = $description_err = $size_err = $color_err = $price_err = "";

// (Sanitization and insertion will be handled after validation below.)
// We'll validate inputs first, then use the prepared-statement block to insert safely.

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate item number
    $input_itemNumber = isset($_POST["item_number"]) ? trim($_POST["item_number"]) : '';
    if(empty($input_itemNumber)){
        $itemNumber_err = "Please enter the item number.";     
    } else{
        $itemNumber = $input_itemNumber;
    }

    // Validate description
    $input_description = isset($_POST["description"]) ? trim($_POST["description"]) : '';
    if(empty($input_description)){
        $description_err = "Please enter a description.";     
    } else{
        $description = $input_description;
    }

    // Validate size
    $input_size = isset($_POST["size"]) ? trim($_POST["size"]) : '';
    if(empty($input_size)){
        $size_err = "Please enter the size.";     
    } else{
        $size = $input_size;
    }

    // Validate color
    $input_color = isset($_POST["color"]) ? trim($_POST["color"]) : '';
    if(empty($input_color)){
        $color_err = "Please enter the color.";     
    } else{
        $color = $input_color;
    }

    // Validate price
    $input_price = isset($_POST["price"]) ? trim($_POST["price"]) : '';
    if(empty($input_price)){
        $price_err = "Please enter the price.";     
    } elseif(!is_numeric($input_price)){
        $price_err = "Please enter a valid price.";
    } else{
        $price = $input_price;
    }
    
    // Check input errors before inserting in database
    if(empty($itemNumber_err) && empty($description_err) && empty($size_err) && empty($color_err) && empty($price_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO sales (ItemNumber, Description, Size, Color, Price) VALUES (?, ?, ?, ?, ?)";
 
        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ssssd", $param_itemNumber, $param_description, $param_size, $param_color, $param_price);

            // Set parameters
            $param_itemNumber = $itemNumber;
            $param_description = $description;
            $param_size = $size;
            $param_color = $color;
            $param_price = (float)$price;

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // get new  auto-incremented OrderNumber
                $newOrderNumber = $mysqli->insert_id;
                // Records created successfully. Redirect to landing page
                header("location: index.php");
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later: " . $stmt->error; 
            }
            // Close statement
            $stmt->close();
        }         
    }
    // Close connection
    $mysqli->close();
}
?>
 
 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Record</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper{
            width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5">Create Record</h2>
                    <p>Please fill this form and submit to add sales record to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group">
                            <label>Item Number</label>
                            <input type="text" name="item_number" class="form-control <?php echo (!empty($itemNumber_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $itemNumber; ?>">
                            <span class="invalid-feedback"><?php echo $itemNumber_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control <?php echo (!empty($description_err)) ? 'is-invalid' : ''; ?>"><?php echo $description; ?></textarea>
                            <span class="invalid-feedback"><?php echo $description_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Size</label>
                            <input type="text" name="size" class="form-control <?php echo (!empty($size_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $size; ?>">
                            <span class="invalid-feedback"><?php echo $size_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Color</label>
                            <input type="text" name="color" class="form-control <?php echo (!empty($color_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $color; ?>">
                            <span class="invalid-feedback"><?php echo $color_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Price</label>
                            <input type="text" name="price" class="form-control <?php echo (!empty($price_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $price; ?>">
                            <span class="invalid-feedback"><?php echo $price_err;?></span>
                        </div>
                        
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-secondary ml-2">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
    <?php include 'inc/footer.php'; ?>
</body>
</html>