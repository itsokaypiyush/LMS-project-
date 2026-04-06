<?php
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $role = $_POST['role'];

    $sql = "SELECT * FROM users 
            WHERE email='$email' 
            AND password='$password' 
            AND role='$role'";

    $result = $conn->query($sql);

    if ($result->num_rows == 1) {

        $_SESSION['user'] = $result->fetch_assoc();

        if ($role == "student") {
            header("Location: student.php");
        } elseif ($role == "teacher") {
            header("Location: teacher.php");
        } else {
            header("Location: admin.php");
        }

    } else {
        echo "<script>alert('Invalid email or password'); window.location='index.php';</script>";
    }
}
?>