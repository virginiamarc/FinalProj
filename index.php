<?php

    session_start();
    require('config/config.php');
    require('config/db.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Records Management System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <style>
        .wrapper{
            width: 600px;
            margin: 0 auto;
        }
        /* Make the Action column wider and prevent its contents from wrapping */
        table th.action-col,
        table td.action-col{
            width: 200px;
            white-space: nowrap; /* keep icons on a single line */
        }
        /* ensure action links are inline and spaced */
        table td.action-col a{
            display: inline-block;
            vertical-align: middle;
            margin-right: .75rem; /* similar to bootstrap mr-3 */
        }
    </style>
    <script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();   
        });
    </script>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row mt-5">
                <div class="col-md-12">
                    <div class="mt-5 mb-3 clearfix">
                        <h2 class="text-center">SALES RECORDS MANAGEMENT SYSTEM</h2>
                        <a href="view.php" class="btn btn-primary"><i class="fa fa-eye"></i> View Sale Record</a>

                        <?php if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true): ?>
                            <!-- Visitor sees Login -->
                            <a href="login.php" class="btn btn-outline-primary"><i class="fa fa-sign-in"></i> Login</a>
                        <?php else: ?>
                            <!-- Admin sees Add/Delete/Logout -->
                            <a href="add.php" class="btn btn-success"><i class="fa fa-plus"></i> Add New Sale Record</a>
                            <a href="delete.php" class="btn btn-danger"><i class="fa fa-trash"></i> Delete Sale Record</a>
                            <a href="logout.php" class="btn btn-outline-secondary"><i class="fa fa-sign-out"></i> Logout</a>
                        <?php endif; ?>
                        
                    </div>
                    <?php
                    // Include config file
                    require_once "config/config.php";
                    
                    // Attempt select query execution
                    $limit = 10; // number of records per page
                    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number
                    $offset = ($page - 1) * $limit; // calculate offset for SQL query

                    // Get total number of records for pagination
                    $totalResult = $mysqli->query("SELECT COUNT(*) AS total FROM sales");
                    $totalRow = $totalResult->fetch_assoc();
                    $totalRecords = $totalRow['total'];
                    
                    // Calculate total pages
                    $totalPages = ceil($totalRecords / $limit);

                    $maxLinks = 6;
                    // Calculate start and end of window
                    $start = max(1, $page - floor($maxLinks / 2));
                    $end   = $start + $maxLinks - 1;

                    // Adjust if end goes beyond total pages
                    if($end > $totalPages){
                        $end = $totalPages;
                        $start = max(1, $end - $maxLinks + 1);
                    }

                    $sql = "SELECT * FROM sales LIMIT $limit OFFSET $offset";
                    if($result = $mysqli->query($sql)){
                        if($result->num_rows > 0){
                            echo '<table class="table table-bordered table-striped text-center">';
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th>Order Number</th>";
                                        echo "<th>Item Number</th>";
                                        echo "<th>Description</th>";
                                        echo "<th>Size</th>";
                                        echo "<th>Color</th>";
                                        echo "<th>Price</th>";
                                        echo "<th class=\"action-col\">Action</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = $result->fetch_assoc()){
                                    echo "<tr>";
                                        echo "<td>" . $row['OrderNumber'] . "</td>";
                                        echo "<td>" . $row['ItemNumber'] . "</td>";
                                        echo "<td>" . $row['Description'] . "</td>";
                                        echo "<td>" . $row['Size'] . "</td>";
                                        echo "<td>" . $row['Color'] . "</td>";
                                        echo "<td>" . $row['Price'] . "</td>";
                                        echo "<td class=\"action-col\">";
                                            // Always allow viewing
                                            echo '<a href="view.php?id='. $row['OrderNumber'] .'" title="View Record" data-toggle="tooltip"><span class="fa fa-eye"></span></a>';
                                        
                                            // Only show Edit/Delete if logged in
                                            if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
                                                echo '<a href="edit.php?id='. $row['OrderNumber'] .'" title="Edit Record" data-toggle="tooltip"><span class="fa fa-pencil"></span></a>';
                                                echo '<a href="delete.php?id='. $row['OrderNumber'] .'" title="Delete Record" data-toggle="tooltip"><span class="fa fa-trash"></span></a>';
                                            }
                                        echo "</td>";

                                    echo "</tr>";
                                }
                                echo "</tbody>";                            
                            echo "</table>";

                            // Pagination links
                            echo '<nav aria-label="Page navigation example">';
                            echo '<ul class="pagination justify-content-center">';

                            // Previous page link
                            if($page > 1){
                                echo '<li class="page-item"><a class="page-link" href="index.php?page=' . ($page - 1) . '">Previous</a></li>';
                            } else{
                                echo '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
                            }

                            // Page number links
                            for($i = $start; $i <= $end; $i++){
                                if($i == $page){
                                    echo '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
                                } else{
                                    echo '<li class="page-item"><a class="page-link" href="index.php?page=' . $i . '">' . $i . '</a></li>';
                                }
                            }

                            if($end < $totalPages){
                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                            }

                            // Next button
                            if($page < $totalPages){
                                echo '<li class="page-item"><a class="page-link" href="index.php?page=' . ($page + 1) . '">Next</a></li>';
                            } else{
                                echo '<li class="page-item disabled"><span class="page-link">Next</span></li>';
                            }

                            echo '</ul>';
                            echo '</nav>';

                            // Back to top Link
                            echo '<div class="text-center mb-4"><a href="#top" class="btn btn-secondary">Back to Top</a></div>';
                            
                            // Back to main page Link
                            echo '<div class="text-center mb-4"><a href="index.php" class="btn btn-primary">Back to Main Page</a></div>';
                            
                            // Free result set
                            $result->free();
                        } else{
                            echo '<div class="alert alert-danger"><em>No records were found.</em></div>';
                        }
                    } else{
                        echo "Oops! Something went wrong. Please try again later.";
                    }
                    
                    // Close connection
                    $mysqli->close();
                    ?>
                </div>
            </div>        
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <?php include 'inc/footer.php'; ?>
</body>
</html>