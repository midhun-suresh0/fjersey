<?php
$hash = '$2y$10$8MuRXOLkMeaUUZQbPsEYVeRq9jlXhNjenMTOf6F.11LMxTXJpd5Oi';
$password = 'admin123';
var_dump(password_verify($password, $hash));
?>