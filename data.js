// ============ SHARED DATA ============

const COURSES = [
  { id: 1, name: 'Operating Systems', code: 'CS301', dept: 'Computer Science', teacher: 'Prof. Rawal', icon: '🖥️', color: '#dbeafe', progress: 65, students: 45 },
  { id: 2, name: 'Computer Networks', code: 'CS302', dept: 'Computer Science', teacher: 'Prof. Mehta', icon: '🌐', color: '#dcfce7', progress: 80, students: 42 },
  { id: 3, name: 'DBMS', code: 'CS303', dept: 'Computer Science', teacher: 'Prof. Sharma', icon: '🗄️', color: '#fef9c3', progress: 55, students: 48 },
  { id: 4, name: 'Algorithms', code: 'CS304', dept: 'Computer Science', teacher: 'Prof. Rawal', icon: '⚙️', color: '#fce7f3', progress: 70, students: 40 },
  { id: 5, name: 'Computer Architecture', code: 'CS305', dept: 'Computer Science', teacher: 'Prof. Jain', icon: '🔧', color: '#ede9fe', progress: 45, students: 38 },
  { id: 6, name: 'Software Engineering', code: 'CS306', dept: 'Computer Science', teacher: 'Prof. Gupta', icon: '🧩', color: '#ffedd5', progress: 60, students: 50 },
];

const ASSIGNMENTS = [
  { id: 1, title: 'Deadlock Detection Algorithm', subject: 'Operating Systems', due: '2026-03-25', status: 'pending', urgent: true },
  { id: 2, title: 'TCP/IP Protocol Analysis', subject: 'Computer Networks', due: '2026-03-28', status: 'submitted', urgent: false },
  { id: 3, title: 'ER Diagram for Library System', subject: 'DBMS', due: '2026-03-30', status: 'pending', urgent: false },
  { id: 4, title: 'QuickSort Implementation', subject: 'Algorithms', due: '2026-04-02', status: 'pending', urgent: false },
  { id: 5, title: 'Cache Memory Analysis', subject: 'Computer Architecture', due: '2026-04-05', status: 'graded', urgent: false },
];

const EVENTS = [
  { name: 'Holi Holiday', date: '2026-03-25', type: 'holiday', desc: 'Festival holiday, no classes' },
  { name: 'Mid-Semester Exams', date: '2026-04-01', type: 'exam', desc: 'All subjects mid-term exams' },
  { name: 'Tech Fest 2026', date: '2026-04-10', type: 'event', desc: 'Annual college technical festival' },
  { name: 'Ambedkar Jayanti', date: '2026-04-14', type: 'holiday', desc: 'National holiday' },
  { name: 'Project Presentations', date: '2026-04-20', type: 'event', desc: 'Final year project demos' },
  { name: 'Summer Break Begins', date: '2026-05-01', type: 'holiday', desc: 'End of semester' },
];

const TIMETABLE = [
  { time: '9:00–10:00',  Mon: 'OS', Tue: 'CN', Wed: 'DBMS', Thu: 'Algo', Fri: 'SE' },
  { time: '10:00–11:00', Mon: 'Algo', Tue: 'CA', Wed: 'OS', Thu: 'CN', Fri: 'DBMS' },
  { time: '11:00–12:00', Mon: 'CN', Tue: 'SE', Wed: 'CA', Thu: 'OS', Fri: 'Algo' },
  { time: '12:00–1:00',  Mon: '—', Tue: '—', Wed: '—', Thu: '—', Fri: '—' },
  { time: '1:00–2:00',   Mon: 'DBMS', Tue: 'Algo', Wed: 'SE', Thu: 'CA', Fri: 'OS' },
  { time: '2:00–3:00',   Mon: 'SE', Tue: 'OS', Wed: 'CN', Thu: 'SE', Fri: 'CA' },
  { time: '3:00–4:00',   Mon: 'CA', Tue: 'DBMS', Wed: 'Algo', Thu: 'DBMS', Fri: 'CN' },
];

const ATTENDANCE = [
  { subject: 'Operating Systems', conducted: 42, attended: 38 },
  { subject: 'Computer Networks', conducted: 40, attended: 35 },
  { subject: 'DBMS', conducted: 38, attended: 25 },
  { subject: 'Algorithms', conducted: 44, attended: 42 },
  { subject: 'Computer Architecture', conducted: 36, attended: 30 },
  { subject: 'Software Engineering', conducted: 40, attended: 36 },
];

const GRADES = [
  { subject: 'Operating Systems', exams: [{ name: 'Unit Test 1', score: 38, total: 50 }, { name: 'Mid-Sem', score: 72, total: 100 }] },
  { subject: 'Computer Networks', exams: [{ name: 'Unit Test 1', score: 44, total: 50 }, { name: 'Mid-Sem', score: 81, total: 100 }] },
  { subject: 'DBMS', exams: [{ name: 'Unit Test 1', score: 30, total: 50 }, { name: 'Mid-Sem', score: 65, total: 100 }] },
  { subject: 'Algorithms', exams: [{ name: 'Unit Test 1', score: 47, total: 50 }, { name: 'Mid-Sem', score: 90, total: 100 }] },
];

const TEACHERS = [
  { id: 1, name: 'Prof. Rawal',  email: 'rawal@university.edu',  dept: 'CS', subject: 'OS, Algorithms', initials: 'PR' },
  { id: 2, name: 'Prof. Mehta',  email: 'mehta@university.edu',  dept: 'CS', subject: 'Networks', initials: 'PM' },
  { id: 3, name: 'Prof. Sharma', email: 'sharma@university.edu', dept: 'CS', subject: 'DBMS', initials: 'PS' },
  { id: 4, name: 'Prof. Jain',   email: 'jain@university.edu',   dept: 'CS', subject: 'Architecture', initials: 'PJ' },
  { id: 5, name: 'Prof. Gupta',  email: 'gupta@university.edu',  dept: 'CS', subject: 'Software Engg', initials: 'PG' },
];

const STUDENTS = [
  { id: 1, name: 'Aryan Shah',   roll: 'CS2024001', course: 'CS', attendance: 90, email: 'aryan@university.edu' },
  { id: 2, name: 'Priya Mehta',  roll: 'CS2024002', course: 'CS', attendance: 78, email: 'priya@university.edu' },
  { id: 3, name: 'Rohan Patel',  roll: 'CS2024003', course: 'CS', attendance: 55, email: 'rohan@university.edu' },
  { id: 4, name: 'Sneha Joshi',  roll: 'CS2024004', course: 'CS', attendance: 95, email: 'sneha@university.edu' },
  { id: 5, name: 'Karan Verma',  roll: 'CS2024005', course: 'CS', attendance: 70, email: 'karan@university.edu' },
  { id: 6, name: 'Ananya Roy',   roll: 'CS2024006', course: 'CS', attendance: 85, email: 'ananya@university.edu' },
];

const DOUBTS = [
  { id: 1, subject: 'OS', question: 'What is the difference between process and thread?', answer: 'A process is an independent program in execution with its own memory space. A thread is a lightweight unit within a process sharing the same memory.' },
  { id: 2, subject: 'CN', question: 'How does TCP ensure reliability?', answer: null },
];

// Chat messages storage
const CHAT_DATA = {};

function getChatKey(a, b) { return [a, b].sort().join('_'); }

function addChatMessage(from, to, text) {
  const key = getChatKey(from, to);
  if (!CHAT_DATA[key]) CHAT_DATA[key] = [];
  CHAT_DATA[key].push({ from, text, time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) });
}

function getMessages(a, b) {
  return CHAT_DATA[getChatKey(a, b)] || [];
}

// Init some sample messages
addChatMessage('student', 'Prof. Rawal', 'Sir, I had a doubt about paging in OS.');
addChatMessage('Prof. Rawal', 'student', 'Sure! Paging divides memory into fixed-size pages. Ask away!');
addChatMessage('student', 'Prof. Mehta', 'Ma\'am, when is the CN assignment due?');

// Shared sidebar/navigation
function initSidebar() {
  const menuToggle = document.getElementById('menuToggle');
  const sidebar = document.getElementById('sidebar');
  if (menuToggle) {
    menuToggle.addEventListener('click', () => sidebar.classList.toggle('open'));
  }

  document.querySelectorAll('.nav-item').forEach(item => {
    item.addEventListener('click', e => {
      e.preventDefault();
      const section = item.dataset.section;
      document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
      item.classList.add('active');
      document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
      const target = document.getElementById('section-' + section);
      if (target) target.classList.add('active');
      document.getElementById('pageTitle').textContent = item.querySelector('span:last-child').textContent;
      if (window.innerWidth <= 768) sidebar.classList.remove('open');
    });
  });
}

// Calendar renderer
function renderCalendar(containerId, highlightDates = []) {
  const container = document.getElementById(containerId);
  if (!container) return;
  const now = new Date();
  let year = now.getFullYear(), month = now.getMonth();

  function draw() {
    const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const today = new Date();

    let html = `<div class="cal-header">
      <button class="cal-nav" onclick="calPrev_${containerId}()">‹</button>
      <span>${monthNames[month]} ${year}</span>
      <button class="cal-nav" onclick="calNext_${containerId}()">›</button>
    </div>
    <div class="cal-grid">
      ${['Su','Mo','Tu','We','Th','Fr','Sa'].map(d => `<div class="cal-day-name">${d}</div>`).join('')}
    `;
    for (let i = 0; i < firstDay; i++) html += `<div class="cal-day empty"></div>`;
    for (let d = 1; d <= daysInMonth; d++) {
      const dateStr = `${year}-${String(month+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
      const isToday = d === today.getDate() && month === today.getMonth() && year === today.getFullYear();
      const hasEvent = highlightDates.includes(dateStr);
      html += `<div class="cal-day ${isToday ? 'today' : ''} ${hasEvent && !isToday ? 'has-event' : ''}">${d}</div>`;
    }
    html += `</div>`;
    container.innerHTML = html;
  }

  window[`calPrev_${containerId}`] = () => { month--; if (month < 0) { month = 11; year--; } draw(); };
  window[`calNext_${containerId}`] = () => { month++; if (month > 11) { month = 0; year++; } draw(); };
  draw();
}

function renderEvents(containerId, limit = null) {
  const el = document.getElementById(containerId);
  if (!el) return;
  const list = limit ? EVENTS.slice(0, limit) : EVENTS;
  el.innerHTML = list.map(ev => {
    const d = new Date(ev.date);
    const day = d.getDate();
    const mon = d.toLocaleString('default', { month: 'short' }).toUpperCase();
    return `<div class="event-item">
      <div class="event-date-box"><div class="event-day">${day}</div><div class="event-month">${mon}</div></div>
      <div class="event-content">
        <div class="event-name">${ev.name}</div>
        <div class="event-type-label">${ev.desc}</div>
      </div>
    </div>`;
  }).join('');
}

function renderEventsFull(containerId) {
  const el = document.getElementById(containerId);
  if (!el) return;
  el.innerHTML = EVENTS.map(ev => `
    <div class="event-full-card ${ev.type}">
      <div class="event-full-name">${ev.name}</div>
      <div class="event-full-date">📅 ${new Date(ev.date).toLocaleDateString('en-IN', { day:'numeric', month:'long', year:'numeric' })}</div>
      <div class="event-full-desc">${ev.desc}</div>
    </div>
  `).join('');
}

function showToast(msg) {
  let t = document.querySelector('.toast');
  if (!t) { t = document.createElement('div'); t.className = 'toast'; document.body.appendChild(t); }
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 2500);
}

// Chat renderer
let activeChatContact = null;

function renderChatContacts(containerId, contacts, selfRole) {
  const el = document.getElementById(containerId);
  if (!el) return;
  el.innerHTML = contacts.map(c => `
    <div class="chat-contact" onclick="openChat('${c.name}', '${c.initials}', '${selfRole}')">
      <div class="chat-contact-avatar">${c.initials}</div>
      <div>
        <div class="chat-contact-name">${c.name}</div>
        <div class="chat-contact-sub">${c.subject || c.course || ''}</div>
      </div>
    </div>
  `).join('');
}

function openChat(contactName, initials, selfRole) {
  activeChatContact = contactName;
  document.getElementById('chatHeaderBar').innerHTML = `<div style="display:flex;align-items:center;gap:.75rem"><div class="chat-contact-avatar" style="width:32px;height:32px;font-size:.75rem">${initials}</div><span>${contactName}</span></div>`;
  renderChatMessages(selfRole, contactName);
  document.querySelectorAll('.chat-contact').forEach(c => c.classList.remove('active'));
  event.currentTarget && event.currentTarget.classList.add('active');
}

function renderChatMessages(selfRole, contactName) {
  const msgs = getMessages(selfRole === 'student' ? 'student' : 'teacher', contactName);
  const el = document.getElementById('chatMessages');
  if (!el) return;
  const selfKey = selfRole === 'student' ? 'student' : 'teacher';
  el.innerHTML = msgs.map(m => `
    <div>
      <div class="chat-msg ${m.from === selfKey ? 'sent' : 'recv'}">${m.text}</div>
      <div class="chat-msg-time" style="text-align:${m.from === selfKey ? 'right' : 'left'};color:#94a3b8">${m.time}</div>
    </div>
  `).join('');
  el.scrollTop = el.scrollHeight;
}

function sendMessage(role) {
  if (!activeChatContact) { showToast('Select a contact first'); return; }
  const input = document.getElementById('chatInput');
  const text = input.value.trim();
  if (!text) return;
  const selfKey = role === 'student' ? 'student' : 'teacher';
  addChatMessage(selfKey, activeChatContact, text);
  input.value = '';
  renderChatMessages(role, activeChatContact);
}

document.addEventListener('keydown', e => {
  if (e.key === 'Enter' && document.activeElement.id === 'chatInput') {
    const role = document.body.classList.contains('teacher-theme') ? 'teacher' : 'student';
    sendMessage(role);
  }
});
