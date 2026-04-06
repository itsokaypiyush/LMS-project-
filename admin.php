<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Handle Attendance Updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_attendance'])) {
    $att_id = $_POST['attendance_id'];
    $new_status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE attendance SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $att_id);
    $stmt->execute();
    header("Location: admin.php");
    exit();
}

// Fetch all attendance records for Admin
$attendance_records = [];
$att_query = "SELECT a.id, u.full_name, c.course_name, a.class_date, a.status 
              FROM attendance a 
              JOIN users u ON a.student_id = u.id 
              JOIN courses c ON a.course_id = c.id 
              ORDER BY a.class_date DESC LIMIT 50";
$res = $conn->query($att_query);
while($row = $res->fetch_assoc()) { $attendance_records[] = $row; }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <title>EduFlow – Admin Dashboard</title>
  <link rel="stylesheet" href="style.css"/>
  <link rel="stylesheet" href="dashboard.css"/>
  <style>.section { display: none; } .section.active { display: block; }</style>
</head>
<body class="dashboard-page">

<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">🎓 EduFlow Admin</div>
  <nav class="sidebar-nav">
    <a href="#" data-target="dashboard" class="nav-item active">🏠 System Overview</a>
    <a href="#" data-target="attendance" class="nav-item">✅ Manage Attendance</a>
  </nav>
  <div class="sidebar-footer"><a href="logout.php" class="logout-btn">⬅ Logout</a></div>
</aside>

<div class="main-content">
  <header class="topbar"><div class="topbar-title">Admin Control Panel</div></header>

  <section class="section active" id="dashboard">
    <h2>System Activity</h2>
    <div class="card"><p>Welcome to the admin panel. From here you can oversee all system actions.</p></div>
  </section>

  <section class="section" id="attendance">
    <h2>Manage Student Attendance</h2>
    <div class="card">
      <table border="1" width="100%" cellpadding="10" style="border-collapse: collapse;">
        <tr><th>Student</th><th>Course</th><th>Date</th><th>Current Status</th><th>Update Action</th></tr>
        <?php foreach($attendance_records as $att): ?>
            <tr>
                <td><?php echo htmlspecialchars($att['full_name']); ?></td>
                <td><?php echo htmlspecialchars($att['course_name']); ?></td>
                <td><?php echo date('M d, Y', strtotime($att['class_date'])); ?></td>
                <td><strong><?php echo ucfirst($att['status']); ?></strong></td>
                <td>
                    <form method="POST" style="display:flex; gap: 5px;">
                        <input type="hidden" name="attendance_id" value="<?php echo $att['id']; ?>">
                        <select name="status">
                            <option value="present" <?php if($att['status'] == 'present') echo 'selected'; ?>>Present</option>
                            <option value="absent" <?php if($att['status'] == 'absent') echo 'selected'; ?>>Absent</option>
                            <option value="late" <?php if($att['status'] == 'late') echo 'selected'; ?>>Late</option>
                        </select>
                        <button type="submit" name="update_attendance">Update</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </section>

</div>

<script>
  document.querySelectorAll('.nav-item').forEach(link => {
    link.addEventListener('click', function(e) {
      if(this.getAttribute('href') === 'logout.php') return;
      e.preventDefault();
      document.querySelectorAll('.nav-item').forEach(l => l.classList.remove('active'));
      this.classList.add('active');
      document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
      document.getElementById(this.getAttribute('data-target')).classList.add('active');
    });
  });
</script>
</body>
</html>