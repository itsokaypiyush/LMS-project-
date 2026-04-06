<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>EduFlow – Register</title>
  <link rel="stylesheet" href="style.css"/>
</head>
<body>
  <div class="login-wrapper">
    <div class="login-card">
      <h2>Create an Account</h2>
      <form action="register_process.php" method="POST">
        <input type="hidden" name="role" id="selectedRoleReg" value="student">
        
        <div class="role-tabs">
          <button type="button" class="role-tab active" onclick="selectRoleReg('student', this)">Student</button>
          <button type="button" class="role-tab" onclick="selectRoleReg('teacher', this)">Teacher</button>
        </div>
        
        <div class="form-group">
          <label>Full Name</label>
          <input type="text" name="full_name" required/>
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" required/>
        </div>
        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" required/>
        </div>
        
        <div style="color: red; text-align: center;">
            <?php session_start(); if(isset($_SESSION['error'])) { echo $_SESSION['error']; unset($_SESSION['error']); } ?>
        </div>
        
        <button type="submit">Register</button>
      </form>
      <p>Already have an account? <a href="index.php">Sign in</a></p>
    </div>
  </div>

  <script>
    function selectRoleReg(role, btn) {
        document.getElementById('selectedRoleReg').value = role;
        document.querySelectorAll('.role-tab').forEach(t => t.classList.remove('active'));
        btn.classList.add('active');
    }
  </script>
</body>
</html>