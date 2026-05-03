<?php
$password = 'admin123';
$hash = '$2y$10$Wpw9eI99oG14k.y.Q5X.7eG2qKpwqI.IpmzP11iVq1GqO2w1u1p0S';
if (password_verify($password, $hash)) {
    echo "Password Correct!";
} else {
    echo "Password Incorrect!";
}
?>
