// Login logic
const CREDENTIALS = {
  student: { email: 'student@university.edu', password: 'student123', redirect: 'student.html' },
  teacher: { email: 'teacher@university.edu', password: 'teacher123', redirect: 'teacher.html' },
  admin:   { email: 'admin@university.edu',   password: 'admin123',   redirect: 'admin.html' }
};

let currentRole = 'student';

document.querySelectorAll('.role-tab').forEach(tab => {
  tab.addEventListener('click', () => {
    document.querySelectorAll('.role-tab').forEach(t => t.classList.remove('active'));
    tab.classList.add('active');
    currentRole = tab.dataset.role;
    document.getElementById('loginBtnText').textContent = `Sign in as ${tab.textContent}`;
    const creds = CREDENTIALS[currentRole];
    document.getElementById('email').placeholder = creds.email;
    document.getElementById('loginError').textContent = '';
  });
});

document.getElementById('loginBtn').addEventListener('click', () => {
  const email = document.getElementById('email').value.trim();
  const password = document.getElementById('password').value.trim();
  const errorEl = document.getElementById('loginError');
  const creds = CREDENTIALS[currentRole];

  // Accept demo credentials OR any filled input for demo purposes
  if ((email === creds.email && password === creds.password) ||
      (email !== '' && password !== '')) {
    errorEl.textContent = '';
    window.location.href = creds.redirect;
  } else {
    errorEl.textContent = 'Please enter email and password.';
  }
});

// Allow Enter key
document.addEventListener('keydown', e => {
  if (e.key === 'Enter') document.getElementById('loginBtn').click();
});
