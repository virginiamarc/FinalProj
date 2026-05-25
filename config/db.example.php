<?php
    // Create connection
    $conn = mysqli_connect("localhost", "your_username_here", "your_password_here", "your_database_name_here");

    // Check connection
    if(mysqli_connect_errno()){
        // Connection failed
        echo 'Failed to connect to MySQL' . mysqli_connect_errno();
    }
?>