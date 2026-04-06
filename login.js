let currentRole = "student";

document.querySelectorAll('.role-tab').forEach(tab => {
  tab.addEventListener('click', () => {
    document.querySelectorAll('.role-tab').forEach(t => t.classList.remove('active'));
    tab.classList.add('active');

    currentRole = tab.dataset.role;

    document.getElementById('role').value = currentRole;
    document.getElementById('loginBtnText').textContent =
      `Sign in as ${tab.textContent}`;
  });
});