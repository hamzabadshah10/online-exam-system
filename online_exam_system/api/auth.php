<?php
// api/auth.php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Unified login handler, slightly modified for dual form semantics
    if ($action === 'login_student' || $action === 'login_admin') {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        
        $expected_role = ($action === 'login_admin') ? 'admin' : 'student';

        $stmt = $pdo->prepare("SELECT id, name, password_hash, role FROM users WHERE email = ? AND role = ?");
        $stmt->execute([$email, $expected_role]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            
            if (isset($_POST['remember'])) {
                setcookie('remember_user', $user['id'], time() + (30 * 24 * 60 * 60), "/");
            }
            
            if ($user['role'] === 'admin') header('Location: ../admin/dashboard.php');
            else header('Location: ../student/dashboard.php');
            exit;
        } else {
            $_SESSION['error'] = "Wrong Credentials";
            header('Location: ../index.php');
            exit;
        }
    } 
    
    elseif ($action === 'register') {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirm = $_POST['confirm_password'];

        if ($password !== $confirm) {
            $_SESSION['error'] = "Passwords do not match.";
            header('Location: ../register.php');
            exit;
        }

        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $_SESSION['error'] = "Email already registered.";
            header('Location: ../register.php');
            exit;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, 'student')");
        if ($stmt->execute([$name, $email, $hash])) {
            $_SESSION['success'] = "Registration successful. Please log in.";
            header('Location: ../index.php');
        } else {
            $_SESSION['error'] = "Something went wrong.";
            header('Location: ../register.php');
        }
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    setcookie('remember_user', '', time() - 3600, "/");
    session_start();
    header('Location: ../index.php');
    exit;
}
?>
