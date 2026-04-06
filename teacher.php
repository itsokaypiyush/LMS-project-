<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: index.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];
$first_name = htmlspecialchars(explode(' ', trim($_SESSION['full_name']))[0]);

// Handle Answering a Doubt
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['answer_doubt'])) {
    $doubt_id = $_POST['doubt_id'];
    $answer = $_POST['answer_text'];
    $stmt = $conn->prepare("UPDATE doubts SET answer = ?, status = 'answered' WHERE id = ?");
    $stmt->bind_param("si", $answer, $doubt_id);
    $stmt->execute();
    $stmt->close();
    header("Location: teacher.php");
    exit();
}

// Fetch Submissions (so teachers can view uploaded files)
$submissions = [];
$sub_query = "SELECT s.id, u.full_name, a.title, s.submission_text, s.file_path, s.grade, s.submitted_at 
              FROM submissions s 
              JOIN assignments a ON s.assignment_id = a.id 
              JOIN users u ON s.student_id = u.id 
              WHERE a.teacher_id = ? ORDER BY s.submitted_at DESC";
$stmt = $conn->prepare($sub_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
while($row = $result->fetch_assoc()) { $submissions[] = $row; }
$stmt->close();

// Fetch Pending Doubts
$doubts = [];
$doubt_query = "SELECT d.id, u.full_name, c.course_name, d.question 
                FROM doubts d 
                JOIN users u ON d.student_id = u.id 
                JOIN courses c ON d.course_id = c.id 
                WHERE c.teacher_id = ? AND d.status = 'pending'";
$stmt = $conn->prepare($doubt_query);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
while($row = $result->fetch_assoc()) { $doubts[] = $row; }
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <title>EduFlow – Teacher Dashboard</title>
  <link rel="stylesheet" href="style.css"/>
  <link rel="stylesheet" href="dashboard.css"/>
  <style>.section { display: none; } .section.active { display: block; }</style>
</head>
<body class="dashboard-page">

<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">🎓 EduFlow</div>
  <nav class="sidebar-nav">
    <a href="#" data-target="dashboard" class="nav-item active">🏠 Dashboard</a>
    <a href="#" data-target="submissions" class="nav-item">📥 Submissions</a>
    <a href="#" data-target="doubts" class="nav-item">❓ Student Doubts</a>
    <a href="#" data-target="chat" class="nav-item">💬 Chat</a>
  </nav>
  <div class="sidebar-footer">
    <a href="logout.php" class="logout-btn">⬅ Logout</a>
  </div>
</aside>

<div class="main-content">
  <header class="topbar"><div class="topbar-title">Teacher Portal</div></header>

  <section class="section active" id="dashboard">
    <h2>Welcome, Professor <?php echo $first_name; ?></h2>
    <p>You have <?php echo count($submissions); ?> submissions to review and <?php echo count($doubts); ?> pending doubts.</p>
  </section>

  <section class="section" id="submissions">
    <h2>Student Submissions</h2>
    <div class="card">
      <table border="1" width="100%" cellpadding="10" style="border-collapse: collapse;">
        <tr><th>Student</th><th>Assignment</th><th>Text</th><th>Attached File</th><th>Grade</th></tr>
        <?php foreach($submissions as $sub): ?>
            <tr>
                <td><?php echo htmlspecialchars($sub['full_name']); ?></td>
                <td><?php echo htmlspecialchars($sub['title']); ?></td>
                <td><?php echo htmlspecialchars($sub['submission_text']); ?></td>
                <td>
                    <?php if($sub['file_path']): ?>
                        <a href="<?php echo htmlspecialchars($sub['file_path']); ?>" target="_blank" style="color: blue; text-decoration: underline;">View Document</a>
                    <?php else: ?>
                        <em>No file</em>
                    <?php endif; ?>
                </td>
                <td>
                    <input type="text" value="<?php echo htmlspecialchars($sub['grade'] ?? ''); ?>" placeholder="A, B+, etc" size="4">
                    <button>Save</button>
                </td>
            </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </section>

  <section class="section" id="doubts">
    <h2>Answer Student Doubts</h2>
    <div class="card">
        <?php if(empty($doubts)) echo "<p>All caught up! No pending doubts.</p>"; ?>
        <?php foreach($doubts as $doubt): ?>
            <div style="border: 1px solid #ddd; padding: 15px; margin-bottom: 10px;">
                <p><strong><?php echo htmlspecialchars($doubt['full_name']); ?></strong> asks:</p>
                <p style="background: #f9f9f9; padding: 10px;">"<?php echo htmlspecialchars($doubt['question']); ?>"</p>
                <form method="POST">
                    <input type="hidden" name="doubt_id" value="<?php echo $doubt['id']; ?>">
                    <textarea name="answer_text" required style="width: 100%; height: 60px; margin-top: 10px;" placeholder="Type your answer here..."></textarea>
                    <button type="submit" name="answer_doubt" style="margin-top: 10px; padding: 8px 15px;">Post Answer</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
  </section>

  <section class="section" id="chat">
    <h2>Chat with Students</h2>
    <div class="card"><p><em>Select a student to begin chatting.</em></p></div>
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