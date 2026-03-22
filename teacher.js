// ============ TEACHER DASHBOARD JS ============

let submissionsAllowed = true;
let studentsData = [...STUDENTS];

document.addEventListener('DOMContentLoaded', () => {
  initSidebar();
  renderTeacherDashboard();
  renderTeacherAssignments();
  renderGrading();
  renderTeacherTimetable();
  renderStudentsTable();
  renderTeacherChat();
  renderEventsFull('eventsFull');
});

function renderTeacherDashboard() {
  renderEvents('eventList', 4);

  const topics = [
    { name: 'Semaphore & Mutex', sub: 'Operating Systems · Today' },
    { name: 'Deadlock Avoidance (Banker\'s Algo)', sub: 'Operating Systems · Tomorrow' },
    { name: 'Dijkstra\'s SSSP Algorithm', sub: 'Algorithms · Wed' },
    { name: 'TCP Congestion Control', sub: 'Computer Networks · Thu' },
    { name: 'ER to Relational Mapping', sub: 'DBMS · Fri' },
  ];

  document.getElementById('upcomingTopics').innerHTML = topics.map((t, i) => `
    <div class="topic-item">
      <div class="topic-num">${i + 1}</div>
      <div class="topic-content">
        <div class="topic-name">${t.name}</div>
        <div class="topic-sub">${t.sub}</div>
      </div>
    </div>
  `).join('');
}

function renderTeacherAssignments() {
  document.getElementById('teacherAssignments').innerHTML = ASSIGNMENTS.map(a => {
    const submitted = STUDENTS.filter(() => Math.random() > 0.4).slice(0, Math.floor(Math.random() * 5) + 1);
    const count = a.status === 'pending' ? Math.floor(Math.random() * 30) + 10 : 42;
    return `
    <div style="padding:1rem 1.25rem;border-bottom:1px solid var(--gray-100)">
      <div style="display:flex;justify-content:space-between;align-items:center">
        <div>
          <div style="font-weight:600;font-size:.9rem">${a.title}</div>
          <div style="font-size:.78rem;color:#94a3b8;margin-top:2px">${a.subject} · Due ${new Date(a.due).toLocaleDateString('en-IN', { day:'numeric', month:'short' })}</div>
        </div>
        <div style="display:flex;align-items:center;gap:.75rem">
          <span class="tag tag-blue">${count} submitted</span>
          <span class="tag ${a.status === 'pending' ? 'tag-orange' : 'tag-green'}">${a.status}</span>
        </div>
      </div>
    </div>`;
  }).join('');
}

function toggleSubmissions() {
  submissionsAllowed = document.getElementById('allowSubmit').checked;
  showToast(submissionsAllowed ? '✅ Submissions are now open' : '🔒 Submissions closed');
}

function renderGrading() {
  document.getElementById('gradingWrap').innerHTML = ASSIGNMENTS.slice(0, 3).map(a => `
    <div class="grading-card">
      <div class="grading-header">
        <div>
          <h4>${a.title}</h4>
          <div style="font-size:.82rem;color:#94a3b8">${a.subject}</div>
        </div>
        <span class="tag tag-orange">Pending Review</span>
      </div>
      <div class="submission-list">
        ${STUDENTS.slice(0, 4).map(s => `
          <div class="submission-row">
            <div class="s-name">${s.name}</div>
            <div class="s-time">Submitted ${new Date(a.due).toLocaleDateString('en-IN', { day:'numeric', month:'short' })} 10:30 AM</div>
            <input class="grade-input" type="number" min="0" max="100" placeholder="—" title="Enter grade"/>
            <button class="btn-sm" onclick="saveGrade(this, '${s.name}')">Save</button>
            <button class="btn-sm" onclick="showPrivateComment('${s.name}')">🔒 Comment</button>
          </div>
        `).join('')}
      </div>
    </div>
  `).join('');
}

function saveGrade(btn, name) {
  const input = btn.parentElement.querySelector('.grade-input');
  if (input.value) showToast(`✅ Grade saved for ${name}: ${input.value}/100`);
  else showToast('Enter a grade first');
}

function showPrivateComment(name) {
  const comment = prompt(`Private comment for ${name}:`);
  if (comment) showToast(`🔒 Private comment sent to ${name}`);
}

function renderTeacherTimetable() {
  const days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
  let html = `<table class="timetable-table">
    <thead><tr><th>Time</th>${days.map(d => `<th>${d}</th>`).join('')}</tr></thead>
    <tbody>`;
  TIMETABLE.forEach(row => {
    html += `<tr><td>${row.time}</td>${days.map(d => {
      const val = row[d];
      const isFree = val === '—';
      // Teacher only teaches OS and Algo
      const isMyClass = ['OS', 'Algo'].includes(val);
      return `<td><div class="timetable-cell ${isFree ? 'free' : !isMyClass ? 'free' : ''}" style="${isMyClass ? 'background:#f0fdfa;color:#0d9488' : ''}">${isFree ? '—' : isMyClass ? val : val}</div></td>`;
    }).join('')}</tr>`;
  });
  html += `</tbody></table>`;
  document.getElementById('timetableWrap').innerHTML = html;
}

function renderStudentsTable() {
  const tbody = document.getElementById('studentsBody');
  tbody.innerHTML = studentsData.map(s => `
    <tr id="student-row-${s.id}">
      <td><strong>${s.name}</strong></td>
      <td>${s.roll}</td>
      <td><span class="tag tag-blue">${s.course}</span></td>
      <td><span style="color:${s.attendance >= 80 ? 'var(--green)' : s.attendance >= 65 ? 'var(--orange)' : 'var(--red)'}; font-weight:600">${s.attendance}%</span></td>
      <td style="display:flex;gap:.5rem">
        <button class="btn-danger" onclick="removeStudent(${s.id})">Remove</button>
      </td>
    </tr>
  `).join('');
}

function filterStudents() {
  const q = document.getElementById('studentSearch').value.toLowerCase();
  studentsData = STUDENTS.filter(s => s.name.toLowerCase().includes(q) || s.roll.toLowerCase().includes(q));
  renderStudentsTable();
}

function removeStudent(id) {
  if (confirm('Remove this student from your class?')) {
    studentsData = studentsData.filter(s => s.id !== id);
    renderStudentsTable();
    showToast('🗑️ Student removed');
  }
}

function renderTeacherChat() {
  renderChatContacts('chatContacts', STUDENTS.map(s => ({
    name: s.name,
    initials: s.name.split(' ').map(n => n[0]).join(''),
    course: s.course + ' · ' + s.roll
  })), 'teacher');
}
