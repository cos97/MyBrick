<?php
    session_start();
    if (isset($_SESSION["username"])) {
        $user = $_SESSION["username"];
    }
    
    session_destroy();
    header("Location: ../index.php?error=despedida");       
?>