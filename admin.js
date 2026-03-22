// ============ ADMIN DASHBOARD JS ============

let adminCourses = [...COURSES];
let adminTeachers = [...TEACHERS];
let adminStudents = [...STUDENTS];
let adminEvents = [...EVENTS];

document.addEventListener('DOMContentLoaded', () => {
  initSidebar();
  populateDropdowns();
  renderAdminDashboard();
  renderAdminCourses();
  renderAdminTeachers();
  renderAdminStudents();
  renderAdminEvents();
});

function populateDropdowns() {
  // Teacher dropdown for course assignment
  const ct = document.getElementById('courseTeacher');
  if (ct) ct.innerHTML = adminTeachers.map(t => `<option value="${t.id}">${t.name}</option>`).join('');

  // Course dropdown for student enrollment
  const sc = document.getElementById('studentCourse');
  if (sc) sc.innerHTML = adminCourses.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
}

function renderAdminDashboard() {
  document.getElementById('adminRecentCourses').innerHTML = adminCourses.slice(0, 4).map(c => `
    <div class="recent-item">
      <span>${c.icon} ${c.name}</span>
      <span class="tag tag-blue">${c.code}</span>
    </div>
  `).join('');

  document.getElementById('adminRecentUsers').innerHTML = adminStudents.slice(0, 5).map(s => `
    <div class="recent-item">
      <span>👤 ${s.name}</span>
      <span class="tag tag-green">${s.roll}</span>
    </div>
  `).join('');
}

function renderAdminCourses() {
  document.getElementById('adminCoursesBody').innerHTML = adminCourses.map(c => `
    <tr>
      <td><strong>${c.icon} ${c.name}</strong></td>
      <td><code style="background:var(--gray-100);padding:.2rem .5rem;border-radius:4px;font-size:.8rem">${c.code}</code></td>
      <td>${c.dept}</td>
      <td>${c.teacher}</td>
      <td>${c.students}</td>
      <td><button class="btn-danger" onclick="deleteCourse(${c.id})">Delete</button></td>
    </tr>
  `).join('');
}

function createCourse() {
  const name = document.getElementById('courseName').value.trim();
  const code = document.getElementById('courseCode').value.trim();
  const dept = document.getElementById('courseDept').value;
  const teacherId = parseInt(document.getElementById('courseTeacher').value);
  const desc = document.getElementById('courseDesc').value.trim();

  if (!name || !code) { showToast('⚠️ Please fill Course Name and Code'); return; }

  const teacher = adminTeachers.find(t => t.id === teacherId);
  const icons = ['📘','📗','📙','📕','📓','📔'];
  const newCourse = {
    id: Date.now(),
    name, code, dept,
    teacher: teacher ? teacher.name : 'TBD',
    icon: icons[adminCourses.length % icons.length],
    color: '#dbeafe',
    progress: 0,
    students: 0
  };
  adminCourses.push(newCourse);
  renderAdminCourses();
  populateDropdowns();
  renderAdminDashboard();
  document.getElementById('courseName').value = '';
  document.getElementById('courseCode').value = '';
  document.getElementById('courseDesc').value = '';
  showToast('✅ Course created successfully!');
}

function deleteCourse(id) {
  if (confirm('Delete this course?')) {
    adminCourses = adminCourses.filter(c => c.id !== id);
    renderAdminCourses();
    populateDropdowns();
    showToast('🗑️ Course deleted');
  }
}

function renderAdminTeachers() {
  document.getElementById('adminTeachersBody').innerHTML = adminTeachers.map(t => `
    <tr>
      <td><strong>${t.name}</strong></td>
      <td>${t.email}</td>
      <td>${t.dept}</td>
      <td>${t.subject}</td>
      <td><button class="btn-danger" onclick="deleteTeacher(${t.id})">Remove</button></td>
    </tr>
  `).join('');
}

function addTeacher() {
  const name = document.getElementById('teacherName').value.trim();
  const email = document.getElementById('teacherEmail').value.trim();
  const dept = document.getElementById('teacherDept').value;
  const subject = document.getElementById('teacherSubject').value.trim();

  if (!name || !email) { showToast('⚠️ Please fill Name and Email'); return; }

  adminTeachers.push({
    id: Date.now(),
    name, email, dept, subject,
    initials: name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0,2)
  });
  renderAdminTeachers();
  populateDropdowns();
  document.getElementById('teacherName').value = '';
  document.getElementById('teacherEmail').value = '';
  document.getElementById('teacherSubject').value = '';
  showToast('✅ Teacher added!');
}

function deleteTeacher(id) {
  if (confirm('Remove this teacher?')) {
    adminTeachers = adminTeachers.filter(t => t.id !== id);
    renderAdminTeachers();
    populateDropdowns();
    showToast('🗑️ Teacher removed');
  }
}

function renderAdminStudents() {
  document.getElementById('adminStudentsBody').innerHTML = adminStudents.map(s => `
    <tr>
      <td><strong>${s.name}</strong></td>
      <td>${s.roll}</td>
      <td>${s.email}</td>
      <td><span class="tag tag-blue">${s.course}</span></td>
      <td><button class="btn-danger" onclick="deleteStudent(${s.id})">Remove</button></td>
    </tr>
  `).join('');
}

function addStudent() {
  const name = document.getElementById('studentName').value.trim();
  const email = document.getElementById('studentEmail').value.trim();
  const roll = document.getElementById('studentRoll').value.trim();
  const courseId = parseInt(document.getElementById('studentCourse').value);

  if (!name || !email || !roll) { showToast('⚠️ Please fill all fields'); return; }

  const course = adminCourses.find(c => c.id === courseId);
  adminStudents.push({
    id: Date.now(),
    name, email, roll,
    course: course ? course.code : 'CS',
    attendance: 100
  });
  renderAdminStudents();
  document.getElementById('studentName').value = '';
  document.getElementById('studentEmail').value = '';
  document.getElementById('studentRoll').value = '';
  showToast('✅ Student enrolled!');
}

function deleteStudent(id) {
  if (confirm('Remove this student?')) {
    adminStudents = adminStudents.filter(s => s.id !== id);
    renderAdminStudents();
    showToast('🗑️ Student removed');
  }
}

function renderAdminEvents() {
  const typeColors = { holiday: 'tag-green', event: 'tag-purple', exam: 'tag-orange' };
  document.getElementById('adminEventsList').innerHTML = adminEvents.map((ev, i) => `
    <div class="recent-item" style="padding:1rem 1.25rem">
      <div>
        <div style="font-weight:600">${ev.name}</div>
        <div style="font-size:.78rem;color:#94a3b8">${new Date(ev.date).toLocaleDateString('en-IN', { day:'numeric', month:'long', year:'numeric' })} · ${ev.desc}</div>
      </div>
      <div style="display:flex;align-items:center;gap:.5rem">
        <span class="tag ${typeColors[ev.type]}">${ev.type}</span>
        <button class="btn-danger" onclick="deleteEvent(${i})">Delete</button>
      </div>
    </div>
  `).join('');
}

function addEvent() {
  const name = document.getElementById('eventName').value.trim();
  const date = document.getElementById('eventDate').value;
  const type = document.getElementById('eventType').value.toLowerCase();
  const desc = document.getElementById('eventDesc').value.trim();

  if (!name || !date) { showToast('⚠️ Please fill Event Name and Date'); return; }

  adminEvents.push({ name, date, type, desc: desc || type });
  adminEvents.sort((a, b) => new Date(a.date) - new Date(b.date));
  renderAdminEvents();
  document.getElementById('eventName').value = '';
  document.getElementById('eventDate').value = '';
  document.getElementById('eventDesc').value = '';
  showToast('✅ Event added!');
}

function deleteEvent(idx) {
  if (confirm('Delete this event?')) {
    adminEvents.splice(idx, 1);
    renderAdminEvents();
    showToast('🗑️ Event deleted');
  }
}
