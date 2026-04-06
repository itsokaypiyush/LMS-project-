<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $full_name, $email, $password, $role);

    if ($stmt->execute()) {
        $new_user_id = $conn->insert_id;

        // Auto-Enroll new students in Data Structures (1) & Database Management (3)
        if ($role === 'student') {
            $enroll_stmt = $conn->prepare("INSERT IGNORE INTO enrollments (student_id, course_id) VALUES (?, 1), (?, 3)");
            $enroll_stmt->bind_param("ii", $new_user_id, $new_user_id);
            $enroll_stmt->execute();
            $enroll_stmt->close();
        }

        $_SESSION['error'] = "<span style='color:green;'>Registration successful! Please login.</span>";
        header("Location: index.php");
    } else {
        $_SESSION['error'] = "Email already exists.";
        header("Location: register.php");
    }
    $stmt->close();
    $conn->close();
}
?>