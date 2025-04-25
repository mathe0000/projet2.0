<?php 
$serveur = "localhost";
$user= "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$serveur;dbname=shoptonmaillot;charset=utf8", $user, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 
} catch(PDOException $e) {
  
}
?>
