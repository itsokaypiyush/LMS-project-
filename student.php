<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit();
}

$student_id = $_SESSION['user_id'];
$first_name = htmlspecialchars(explode(' ', trim($_SESSION['full_name']))[0]);

// ==========================================
// 1. PROCESS FORM SUBMISSIONS
// ==========================================

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_assignment'])) {
    $assignment_id = $_POST['assignment_id'];
    $submission_text = $_POST['submission_text'] ?? '';
    $file_path = null;

    if (isset($_FILES['assignment_file']) && $_FILES['assignment_file']['error'] == 0) {
        $target_dir = "uploads/";
        $file_name = time() . "_" . basename($_FILES["assignment_file"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["assignment_file"]["tmp_name"], $target_file)) {
            $file_path = $target_file;
        }
    }

    $stmt = $conn->prepare("INSERT INTO submissions (assignment_id, student_id, submission_text, file_path) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiss", $assignment_id, $student_id, $submission_text, $file_path);
    $stmt->execute();
    $stmt->close();

    $count_res = $conn->query("SELECT COUNT(*) as total FROM submissions WHERE student_id = $student_id");
    $total_submitted = $count_res->fetch_assoc()['total'];

    $_SESSION['alert'] = "✅ Assignment submitted successfully! You have completed $total_submitted assignments.";
    header("Location: student.php"); 
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_message'])) {
    $receiver_id = $_POST['receiver_id'];
    $message = $_POST['message'];
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $student_id, $receiver_id, $message);
    $stmt->execute();
    $stmt->close();
    
    $_SESSION['chat_alert'] = "✅ Message sent!";
    header("Location: student.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_doubt'])) {
    $course_id = $_POST['course_id'];
    $question = $_POST['question'];
    $stmt = $conn->prepare("INSERT INTO doubts (student_id, course_id, question) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $student_id, $course_id, $question);
    $stmt->execute();
    $stmt->close();
    
    $_SESSION['alert'] = "✅ Your doubt has been posted to the teacher.";
    header("Location: student.php");
    exit();
}

// ==========================================
// 2. FETCH DATA FROM DATABASE
// ==========================================

$courses = [];
$res = $conn->query("SELECT c.id, c.course_name FROM courses c JOIN enrollments e ON c.id = e.course_id WHERE e.student_id = $student_id");
if($res) while($row = $res->fetch_assoc()) { $courses[] = $row; }

$teachers = [];
$res = $conn->query("SELECT id, full_name FROM users WHERE role = 'teacher'");
if($res) while($row = $res->fetch_assoc()) { $teachers[] = $row; }

$assignments = [];
$res = $conn->query("SELECT a.id, a.title, a.due_date, c.course_name FROM assignments a JOIN courses c ON a.course_id = c.id JOIN enrollments e ON c.id = e.course_id WHERE e.student_id = $student_id AND a.id NOT IN (SELECT assignment_id FROM submissions WHERE student_id = $student_id) ORDER BY a.due_date ASC");
if($res) while($row = $res->fetch_assoc()) { $assignments[] = $row; }

$grades = [];
$res = $conn->query("SELECT a.title, s.grade, c.course_name FROM submissions s JOIN assignments a ON s.assignment_id = a.id JOIN courses c ON a.course_id = c.id WHERE s.student_id = $student_id");
if($res) while($row = $res->fetch_assoc()) { $grades[] = $row; }

$doubts = [];
$res = $conn->query("SELECT d.question, d.answer, d.status, c.course_name FROM doubts d JOIN courses c ON d.course_id = c.id WHERE d.student_id = $student_id ORDER BY d.created_at DESC");
if($res) while($row = $res->fetch_assoc()) { $doubts[] = $row; }

$messages = [];
$res = $conn->query("SELECT m.message, m.sent_at, m.sender_id, m.is_read, u.full_name AS sender_name FROM messages m JOIN users u ON m.sender_id = u.id WHERE m.sender_id = $student_id OR m.receiver_id = $student_id ORDER BY m.sent_at ASC");
if($res) while($row = $res->fetch_assoc()) { $messages[] = $row; }

$events = [];
$res = $conn->query("SELECT title, event_date FROM events ORDER BY event_date ASC");
if($res) while($row = $res->fetch_assoc()) { $events[] = $row; }

// RESTORED: Fetch Timetable
$timetable = [];
$res = $conn->query("SELECT t.day_of_week, t.start_time, t.end_time, t.room, c.course_name FROM timetable t JOIN courses c ON t.course_id = c.id JOIN enrollments e ON c.id = e.course_id WHERE e.student_id = $student_id ORDER BY FIELD(t.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'), t.start_time");
if($res) while($row = $res->fetch_assoc()) { $timetable[] = $row; }

$alert = $_SESSION['alert'] ?? null;
$chat_alert = $_SESSION['chat_alert'] ?? null;
unset($_SESSION['alert'], $_SESSION['chat_alert']);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <title>EduFlow – Student Portal</title>
  <style>
    * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; }
    body { display: flex; height: 100vh; background-color: #f4f7f6; color: #333; overflow: hidden; }
    .sidebar { width: 260px; background-color: #2c3e50; color: #fff; display: flex; flex-direction: column; }
    .sidebar-brand { padding: 20px; font-size: 24px; font-weight: bold; text-align: center; border-bottom: 1px solid #34495e; }
    .sidebar-nav { flex: 1; padding: 20px 0; overflow-y: auto; }
    .nav-item { display: block; padding: 15px 25px; color: #bdc3c7; text-decoration: none; font-size: 16px; transition: all 0.3s; border-left: 4px solid transparent; }
    .nav-item:hover, .nav-item.active { background-color: #34495e; color: #fff; border-left-color: #3498db; }
    .sidebar-footer { padding: 20px; border-top: 1px solid #34495e; text-align: center; }
    .logout-btn { display: inline-block; padding: 10px 20px; background: #e74c3c; color: #fff; text-decoration: none; border-radius: 5px; font-weight: bold; width: 100%; transition: 0.3s; }
    .main-content { flex: 1; display: flex; flex-direction: column; overflow-y: auto; position: relative; }
    .topbar { background: #fff; padding: 20px 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; }
    .section-container { padding: 30px; }
    .section { display: none; animation: fadeIn 0.4s; }
    .section.active { display: block; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .card { background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 25px; }
    .card h2 { margin-bottom: 20px; color: #2c3e50; border-bottom: 2px solid #f1f2f6; padding-bottom: 10px; }
    
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 15px; text-align: left; border-bottom: 1px solid #f1f2f6; }
    th { background-color: #f8f9fa; color: #2c3e50; font-weight: 600; }
    
    input[type="text"], input[type="file"], select, textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; margin-bottom: 15px; font-size: 14px; outline: none; }
    button { background: #3498db; color: #fff; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: bold; transition: 0.3s; }
    button:hover { background: #2980b9; }
    
    .badge-pending { background: #f39c12; color: #fff; padding: 5px 10px; border-radius: 4px; font-size: 12px; }
    .badge-success { background: #2ecc71; color: #fff; padding: 5px 10px; border-radius: 4px; font-size: 12px; }
    .alert-box { background: #d1e7dd; color: #0f5132; padding: 15px; border-radius: 6px; margin-bottom: 20px; border-left: 5px solid #198754; font-weight: bold; animation: fadeOut 5s forwards; }
    @keyframes fadeOut { 0% { opacity: 1; } 80% { opacity: 1; } 100% { opacity: 0; display: none; } }

    /* --- Interactive Timetable CSS --- */
    .day-filters { display: flex; gap: 10px; margin-bottom: 20px; overflow-x: auto; padding-bottom: 5px; }
    .day-btn { background: #f1f2f6; color: #2c3e50; flex: 1; border-radius: 20px; padding: 10px 15px; text-align: center; cursor: pointer; border: 2px solid transparent; font-weight: bold; transition: 0.3s; }
    .day-btn:hover { background: #e0eafc; }
    .day-btn.active { background: #3498db; color: #fff; border-color: #2980b9; }
    .class-card { background: #fdfdfd; border-left: 4px solid #3498db; padding: 15px; margin-bottom: 10px; border-radius: 4px; box-shadow: 0 2px 5px rgba(0,0,0,0.02); display: flex; justify-content: space-between; align-items: center; }
    .class-time { font-size: 18px; font-weight: bold; color: #2c3e50; }
    .class-details { text-align: right; }

    /* Chat Elements */
    .chat-box { height: 350px; overflow-y: auto; background: #fafafa; border: 1px solid #eee; border-radius: 8px; padding: 20px; margin-bottom: 15px; display: flex; flex-direction: column; gap: 10px; }
    .msg { padding: 12px 18px; border-radius: 20px; max-width: 75%; font-size: 14px; line-height: 1.4; position: relative; }
    .msg.me { background: #3498db; color: #fff; align-self: flex-end; border-bottom-right-radius: 4px; }
    .msg.them { background: #e2e8f0; color: #333; align-self: flex-start; border-bottom-left-radius: 4px; }
    .msg-time { display: block; font-size: 11px; margin-bottom: 5px; opacity: 0.8; }
    .msg-status { display: block; text-align: right; font-size: 11px; margin-top: 5px; font-weight: bold; }
    .status-delivered { color: #ecf0f1; }
    .status-seen { color: #f1c40f; } 
  </style>
</head>
<body>

<aside class="sidebar">
  <div class="sidebar-brand">🎓 EduFlow</div>
  <nav class="sidebar-nav">
    <a href="#" data-target="dashboard" class="nav-item active">🏠 Dashboard</a>
    <a href="#" data-target="timetable" class="nav-item">🗓️ Timetable</a>
    <a href="#" data-target="assignments" class="nav-item">📝 Assignments</a>
    <a href="#" data-target="grades" class="nav-item">🎓 Grades</a>
    <a href="#" data-target="doubts" class="nav-item">❓ Doubts</a>
    <a href="#" data-target="chat" class="nav-item">💬 Chat</a>
    <a href="#" data-target="events" class="nav-item">🎉 Events</a>
  </nav>
  <div class="sidebar-footer">
    <a href="logout.php" class="logout-btn">Log Out</a>
  </div>
</aside>

<main class="main-content">
  <header class="topbar">
    <h1 style="font-size: 20px;">Student Portal</h1>
    <div style="font-weight: bold; color: #0056b3;">👤 <?php echo $first_name; ?></div>
  </header>

  <div class="section-container">
    
    <?php if($alert): ?>
        <div class="alert-box"><?php echo $alert; ?></div>
    <?php endif; ?>

    <section class="section active" id="dashboard">
      <h2 style="margin-bottom: 20px; color: #2c3e50;">Welcome back, <?php echo $first_name; ?>!</h2>
      <div style="display: flex; gap: 20px; margin-bottom: 30px;">
        <div class="card" style="flex: 1; text-align: center; border-top: 4px solid #3498db;">
            <h3 style="font-size: 36px; color: #2c3e50;"><?php echo count($courses); ?></h3>
            <p style="color: #7f8c8d; font-weight: bold; text-transform: uppercase; font-size: 13px;">Active Courses</p>
        </div>
        <div class="card" style="flex: 1; text-align: center; border-top: 4px solid #e74c3c;">
            <h3 style="font-size: 36px; color: #2c3e50;"><?php echo count($assignments); ?></h3>
            <p style="color: #7f8c8d; font-weight: bold; text-transform: uppercase; font-size: 13px;">Pending Tasks</p>
        </div>
      </div>
    </section>

    <section class="section" id="timetable">
      <div class="card">
        <h2>My Class Schedule</h2>
        
        <div class="day-filters">
            <button class="day-btn active" onclick="filterTimetable('Monday', this)">Mon</button>
            <button class="day-btn" onclick="filterTimetable('Tuesday', this)">Tue</button>
            <button class="day-btn" onclick="filterTimetable('Wednesday', this)">Wed</button>
            <button class="day-btn" onclick="filterTimetable('Thursday', this)">Thu</button>
            <button class="day-btn" onclick="filterTimetable('Friday', this)">Fri</button>
            <button class="day-btn" onclick="filterTimetable('All', this)">All</button>
        </div>

        <div id="timetable-container">
            <?php if(empty($timetable)): ?>
                <p>No classes scheduled.</p>
            <?php else: ?>
                <?php foreach($timetable as $class): ?>
                    <div class="class-card" data-day="<?php echo $class['day_of_week']; ?>">
                        <div>
                            <span class="badge-pending" style="background:#9b59b6; margin-bottom:5px; display:inline-block;"><?php echo $class['day_of_week']; ?></span>
                            <div class="class-time"><?php echo date('h:i A', strtotime($class['start_time'])) . ' - ' . date('h:i A', strtotime($class['end_time'])); ?></div>
                        </div>
                        <div class="class-details">
                            <strong style="color:#2980b9; font-size:16px;"><?php echo htmlspecialchars($class['course_name']); ?></strong><br>
                            <span style="color:#7f8c8d; font-size:14px;">📍 Room: <?php echo htmlspecialchars($class['room']); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
      </div>
    </section>

    <section class="section" id="assignments">
      <div class="card">
        <h2>Pending Assignments</h2>
        <?php if(empty($assignments)) echo "<p style='color: green; font-weight: bold;'>All caught up! No pending assignments. 🎉</p>"; ?>
        
        <?php foreach($assignments as $task): ?>
            <div style="border: 1px solid #e1e8ed; padding: 20px; border-radius: 8px; margin-bottom: 20px; background: #fdfdfd;">
                <h3 style="color: #2980b9; margin-bottom: 5px;"><?php echo htmlspecialchars($task['title']); ?></h3>
                <p style="margin-bottom: 15px;">Course: <strong><?php echo htmlspecialchars($task['course_name']); ?></strong></p>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="assignment_id" value="<?php echo $task['id']; ?>">
                    <input type="file" name="assignment_file" required>
                    <button type="submit" name="submit_assignment">Submit Assignment</button>
                </form>
            </div>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="section" id="grades">
      <div class="card">
        <h2>Academic Record</h2>
        <table>
          <tr><th>Assignment Name</th><th>Course</th><th>Status</th></tr>
          <?php foreach($grades as $grade): ?>
              <tr>
                  <td><?php echo htmlspecialchars($grade['title']); ?></td>
                  <td><?php echo htmlspecialchars($grade['course_name']); ?></td>
                  <td>
                      <?php if($grade['grade']): ?>
                          <span class="badge-success"><?php echo htmlspecialchars($grade['grade']); ?></span>
                      <?php else: ?>
                          <span class="badge-pending">Pending Review</span>
                      <?php endif; ?>
                  </td>
              </tr>
          <?php endforeach; ?>
        </table>
      </div>
    </section>

    <section class="section" id="doubts">
      <div class="card">
        <h2>Ask a Question</h2>
        <form method="POST">
            <select name="course_id" required>
                <option value="">-- Select Course --</option>
                <?php foreach($courses as $c): ?>
                    <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['course_name']); ?></option>
                <?php endforeach; ?>
            </select>
            <textarea name="question" required rows="2" placeholder="Describe your doubt..."></textarea>
            <button type="submit" name="submit_doubt">Post Doubt</button>
        </form>
      </div>
    </section>

    <section class="section" id="chat">
      <div class="card">
        <h2>Messages</h2>
        <?php if($chat_alert): ?>
            <div class="alert-box" style="padding: 10px; margin-bottom: 10px;"><?php echo $chat_alert; ?></div>
        <?php endif; ?>
        
        <div class="chat-box" id="chatBox">
            <?php foreach($messages as $msg): ?>
                <?php $is_me = ($msg['sender_id'] == $student_id); ?>
                <div class="msg <?php echo $is_me ? 'me' : 'them'; ?>">
                    <span class="msg-time"><?php echo $is_me ? 'You' : htmlspecialchars($msg['sender_name']); ?> • <?php echo date('h:i A', strtotime($msg['sent_at'])); ?></span>
                    <?php echo htmlspecialchars($msg['message']); ?>
                    <?php if($is_me): ?>
                        <?php if($msg['is_read'] == 1): ?>
                            <span class="msg-status status-seen">✓✓ Seen</span>
                        <?php else: ?>
                            <span class="msg-status status-delivered">✓ Delivered</span>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <form method="POST" style="display:flex; gap:10px;">
            <select name="receiver_id" required style="width: 30%; margin-bottom: 0;">
                <option value="">Select Teacher...</option>
                <?php foreach($teachers as $t): ?>
                    <option value="<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['full_name']); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="message" required placeholder="Type your message..." autocomplete="off" style="margin-bottom: 0;">
            <button type="submit" name="send_message">Send</button>
        </form>
      </div>
    </section>

    <section class="section" id="events">
      <div class="card">
        <h2>Upcoming Campus Events</h2>
        <ul style="list-style: none;">
          <?php if(empty($events)) echo "<li>No upcoming events.</li>"; ?>
          <?php foreach($events as $event): ?>
              <li style="background: #f8f9fa; padding: 15px; border-left: 4px solid #9b59b6; border-radius: 4px; margin-bottom: 10px;">
                  <strong style="font-size: 16px; color: #2c3e50;"><?php echo htmlspecialchars($event['title']); ?></strong>
                  <br>
                  <span style="color: #7f8c8d; font-size: 14px;">📅 Date: <?php echo date('F d, Y', strtotime($event['event_date'])); ?></span>
              </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </section>

  </div>
</main>

<script>
  // Tab Navigation Logic
  const navItems = document.querySelectorAll('.nav-item');
  const sections = document.querySelectorAll('.section');

  navItems.forEach(link => {
    link.addEventListener('click', function(e) {
      if(this.getAttribute('href') === 'logout.php') return; 
      e.preventDefault();
      navItems.forEach(l => l.classList.remove('active'));
      this.classList.add('active');
      sections.forEach(s => s.classList.remove('active'));
      document.getElementById(this.getAttribute('data-target')).classList.add('active');
    });
  });

  // Auto-scroll chat to bottom
  const chatBox = document.getElementById('chatBox');
  if(chatBox) { chatBox.scrollTop = chatBox.scrollHeight; }

  // Interactive Timetable Filtering Logic
  function filterTimetable(day, btnElement) {
      // Highlight selected button
      document.querySelectorAll('.day-btn').forEach(btn => btn.classList.remove('active'));
      btnElement.classList.add('active');

      // Show/Hide class cards
      const cards = document.querySelectorAll('.class-card');
      cards.forEach(card => {
          if (day === 'All' || card.getAttribute('data-day') === day) {
              card.style.display = 'flex';
          } else {
              card.style.display = 'none';
          }
      });
  }

  // Pre-filter to Monday on load if classes exist
  document.addEventListener("DOMContentLoaded", function() {
      const monBtn = document.querySelector(".day-btn");
      if(monBtn) filterTimetable('Monday', monBtn);
  });
</script>

</body>
</html>