<?php
    // Create connection
    $conn = mysqli_connect("localhost", "virgacwx_fpuser", "Auburn3471@", "virgacwx_finalproj");

    // Check connection
    if(mysqli_connect_errno()){
        // Connection failed
        echo 'Failed to connect to MySQL' . mysqli_connect_errno();
    }
?>