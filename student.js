// ============ STUDENT DASHBOARD JS ============

document.addEventListener('DOMContentLoaded', () => {
  initSidebar();
  renderDashboard();
  renderCourses();
  renderTimetable();
  renderAssignments();
  renderAttendance();
  renderGrades();
  renderDoubts();
  renderChatSection();
  renderEventsFull('eventsFull');
});

function renderDashboard() {
  // Calendar
  const assignmentDates = ASSIGNMENTS.map(a => a.due);
  renderCalendar('studentCalendar', assignmentDates);
  renderEvents('eventList', 4);

  // Upcoming assignments
  const pending = ASSIGNMENTS.filter(a => a.status === 'pending').slice(0, 4);
  document.getElementById('upcomingAssignments').innerHTML = pending.map(a => `
    <div class="assignment-item">
      <div class="assign-dot ${a.urgent ? 'urgent' : 'normal'}"></div>
      <div class="assign-info">
        <div class="assign-title">${a.title}</div>
        <div class="assign-meta">${a.subject} · Due ${new Date(a.due).toLocaleDateString('en-IN', { day:'numeric', month:'short' })}</div>
      </div>
      <span class="tag ${a.urgent ? 'tag-red' : 'tag-blue'}">${a.urgent ? 'Urgent' : 'Upcoming'}</span>
    </div>
  `).join('');
}

function renderCourses() {
  document.getElementById('coursesGrid').innerHTML = COURSES.map(c => `
    <div class="course-card">
      <div class="course-card-top" style="background:${c.color}">${c.icon}</div>
      <div class="course-card-body">
        <div class="course-name">${c.name}</div>
        <div class="course-code">${c.code} · ${c.teacher}</div>
        <div class="course-progress-bar"><div class="course-progress-fill" style="width:${c.progress}%"></div></div>
        <div class="course-progress-label">${c.progress}% completed</div>
      </div>
    </div>
  `).join('');
}

function renderTimetable() {
  const days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
  let html = `<table class="timetable-table">
    <thead><tr><th>Time</th>${days.map(d => `<th>${d}</th>`).join('')}</tr></thead>
    <tbody>`;
  TIMETABLE.forEach(row => {
    html += `<tr><td>${row.time}</td>${days.map(d => {
      const val = row[d];
      const isFree = val === '—';
      return `<td><div class="timetable-cell ${isFree ? 'free' : ''}">${val}</div></td>`;
    }).join('')}</tr>`;
  });
  html += `</tbody></table>`;
  document.getElementById('timetableWrap').innerHTML = html;
}

function renderAssignments() {
  document.getElementById('assignmentsWrap').innerHTML = ASSIGNMENTS.map(a => {
    const statusTag = a.status === 'submitted' ? '<span class="tag tag-green">Submitted</span>'
      : a.status === 'graded' ? '<span class="tag tag-purple">Graded</span>'
      : '<span class="tag tag-orange">Pending</span>';
    const canSubmit = a.status === 'pending';
    return `
    <div class="assignment-card">
      <div class="assignment-card-header">
        <div>
          <h4>${a.title}</h4>
          <div class="subject">${a.subject}</div>
        </div>
        <div style="text-align:right">
          ${statusTag}
          <div class="due" style="margin-top:.4rem">Due: ${new Date(a.due).toLocaleDateString('en-IN', { day:'numeric', month:'short', year:'numeric' })}</div>
        </div>
      </div>
      ${canSubmit ? `
      <div class="assignment-card-body">
        <div class="file-upload-area" onclick="fakeUpload(${a.id})">
          📎 Click to attach file (PDF, DOCX, ZIP)
        </div>
        <textarea class="comment-box" rows="2" placeholder="Add a comment for your teacher..." id="comment_${a.id}"></textarea>
        <div style="display:flex;gap:.75rem">
          <button class="btn-primary" onclick="submitAssignment(${a.id})">Submit Assignment</button>
          <button class="btn-sm" onclick="saveComment(${a.id})">💬 Save Comment</button>
        </div>
      </div>
      ` : `<div style="padding:.5rem 0;color:#94a3b8;font-size:.875rem">✅ This assignment has been ${a.status}.</div>`}
    </div>`;
  }).join('');
}

function fakeUpload(id) { showToast('📎 File attached successfully!'); }

function submitAssignment(id) {
  const idx = ASSIGNMENTS.findIndex(a => a.id === id);
  if (idx !== -1) {
    ASSIGNMENTS[idx].status = 'submitted';
    renderAssignments();
    showToast('✅ Assignment submitted!');
  }
}

function saveComment(id) {
  const val = document.getElementById('comment_' + id)?.value;
  if (val?.trim()) showToast('💬 Comment saved!');
  else showToast('Please write a comment first.');
}

function renderAttendance() {
  document.getElementById('attendanceWrap').innerHTML = ATTENDANCE.map(a => {
    const pct = Math.round((a.attended / a.conducted) * 100);
    const cls = pct >= 80 ? 'good' : pct >= 65 ? 'warn' : 'bad';
    return `
    <div class="attendance-card">
      <div class="att-header">
        <div class="att-subject">${a.subject}</div>
        <div class="att-percent ${cls}">${pct}%</div>
      </div>
      <div class="att-bar"><div class="att-bar-fill ${cls}" style="width:${pct}%"></div></div>
      <div class="att-detail">${a.attended} / ${a.conducted} classes attended · ${pct < 75 ? '⚠️ Below 75% threshold' : '✅ Attendance OK'}</div>
    </div>`;
  }).join('');
}

function renderGrades() {
  document.getElementById('gradesWrap').innerHTML = GRADES.map(g => `
    <div class="grade-card">
      <div class="grade-subject">${g.subject}</div>
      ${g.exams.map(e => {
        const pct = Math.round((e.score / e.total) * 100);
        const grade = pct >= 90 ? 'A+' : pct >= 80 ? 'A' : pct >= 70 ? 'B+' : pct >= 60 ? 'B' : pct >= 50 ? 'C' : 'F';
        return `<div class="grade-row">
          <span>${e.name}</span>
          <span>${e.score}/${e.total}</span>
          <span class="grade-score tag ${pct >= 70 ? 'tag-green' : pct >= 50 ? 'tag-orange' : 'tag-red'}">${grade}</span>
        </div>`;
      }).join('')}
    </div>
  `).join('');
}

function renderDoubts() {
  const list = document.getElementById('doubtList');
  list.innerHTML = '<div class="card-header">📋 Previous Doubts</div>' +
    DOUBTS.map(d => `
      <div class="doubt-item">
        <span class="tag tag-blue" style="font-size:.72rem">${d.subject}</span>
        <strong>${d.question}</strong>
        ${d.answer ? `<div class="doubt-answer">💡 ${d.answer}</div>` : `<div style="font-size:.78rem;color:#94a3b8;margin-top:.3rem">⏳ Awaiting teacher response</div>`}
      </div>
    `).join('');
}

function submitDoubt() {
  showToast('❓ Doubt posted to your teacher!');
}

function renderChatSection() {
  renderChatContacts('chatContacts', TEACHERS, 'student');
}
