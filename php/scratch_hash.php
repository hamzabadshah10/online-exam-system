<?php
$hash = '$2y$10$tZ2E7sM1dYfG/qY/P37bJOr5.3N/J9xW5V6oZ3i.A.Tf9P1pZ6h7i';
$pass = 'admin123';
if (password_verify($pass, $hash)) {
    echo "MATCH";
} else {
    echo "NO MATCH\n";
    echo password_hash($pass, PASSWORD_BCRYPT);
}
