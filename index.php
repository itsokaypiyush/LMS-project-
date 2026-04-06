<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>EduFlow – Login</title>
  
  <style>
    /* Reset and Base Styles */
    * {
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
    }

    body {
        background-color: #f4f7f6;
        color: #333;
    }

    /* --- Centered Login Wrapper --- */
    .login-wrapper {
        display: flex;
        justify-content: center; /* Centers horizontally */
        align-items: center;     /* Centers vertically */
        height: 100vh;           /* Takes up the full height of the screen */
        background: linear-gradient(135deg, #e0eafc, #cfdef3);
    }

    .login-card {
        background: #fff;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 400px;
        text-align: center;
    }

    .login-card h2 {
        margin-bottom: 25px;
        color: #2c3e50;
        font-size: 24px;
    }

    /* The Role Selection Tabs */
    .role-tabs {
        display: flex;
        background: #f1f1f1;
        border-radius: 30px;
        margin-bottom: 25px;
        padding: 5px;
    }

    .role-tab {
        flex: 1;
        padding: 10px;
        border: none;
        background: transparent;
        cursor: pointer;
        border-radius: 30px;
        font-weight: bold;
        color: #666;
        transition: all 0.3s ease;
    }

    .role-tab.active {
        background: #fff;
        color: #0056b3;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    /* Form Inputs */
    .form-group {
        margin-bottom: 20px;
        text-align: left;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        font-size: 14px;
        color: #555;
    }

    .form-group input {
        width: 100%;
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 6px;
        outline: none;
        transition: border-color 0.3s;
        font-size: 15px;
    }

    .form-group input:focus {
        border-color: #0056b3;
    }

    /* Styled Login Button */
    button[type="submit"] {
        width: 100%;
        padding: 12px;
        background: #0056b3;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.3s;
        margin-top: 10px;
    }

    button[type="submit"]:hover {
        background: #004494;
    }

    .login-card p {
        margin-top: 20px;
        font-size: 14px;
        color: #666;
    }

    .login-card a {
        color: #0056b3;
        text-decoration: none;
        font-weight: bold;
    }

    .login-card a:hover {
        text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="login-wrapper">
    <div class="login-card">
      <h2>Welcome back</h2>
      <form action="login_process.php" method="POST">
        <input type="hidden" name="role" id="selectedRole" value="student">
        
        <div class="role-tabs">
          <button type="button" class="role-tab active" onclick="selectRole('student', this)">Student</button>
          <button type="button" class="role-tab" onclick="selectRole('teacher', this)">Teacher</button>
          <button type="button" class="role-tab" onclick="selectRole('admin', this)">Admin</button>
        </div>
        
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" required placeholder="Enter your email"/>
        </div>
        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" required placeholder="Enter your password"/>
        </div>
        
        <div style="color: red; text-align: center; margin-bottom: 10px;">
            <?php session_start(); if(isset($_SESSION['error'])) { echo $_SESSION['error']; unset($_SESSION['error']); } ?>
        </div>
        
        <button type="submit">Sign In</button>
      </form>
      <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
  </div>

  <script>
    function selectRole(role, btn) {
        document.getElementById('selectedRole').value = role;
        document.querySelectorAll('.role-tab').forEach(t => t.classList.remove('active'));
        btn.classList.add('active');
    }
  </script>
</body>
</html>