<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("SELECT id, full_name, password, role FROM users WHERE email = ? AND role = ?");
    $stmt->bind_param("ss", $email, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'student') header("Location: student.php");
            elseif ($user['role'] == 'teacher') header("Location: teacher.php");
            elseif ($user['role'] == 'admin') header("Location: admin.php");
            exit();
        } else {
            $_SESSION['error'] = "Incorrect password.";
            header("Location: index.php");
        }
    } else {
        $_SESSION['error'] = "Account not found.";
        header("Location: index.php");
    }
    $stmt->close();
    $conn->close();
}
?>