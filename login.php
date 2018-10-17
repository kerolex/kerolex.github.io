<?php
session_start();

$password = $_POST['password'];

if (isset($_SESSION['valid']) && $_SESSION['valid'] == true) { 
  header("Location: restricted.php");
} else {
  if ( $password == 'coslam2018') {
    $_SESSION['id'] = 'alessiokero';
    $_SESSION['valid'] = true;

    header("Location: restricted.php");
  } else {
    echo "Wrong password";
    header("Location: private.html");
  }
}
?>
