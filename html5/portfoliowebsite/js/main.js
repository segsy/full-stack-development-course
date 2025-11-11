// ...existing code...
document.addEventListener('DOMContentLoaded', () => {
  // nav toggle
  const navToggle = document.getElementById('navToggle');
  const mainNav = document.getElementById('mainNav');
  navToggle?.addEventListener('click', () => mainNav.classList.toggle('open'));

  // smooth scroll and active link (declare before using)
  const navLinks = Array.from(document.querySelectorAll('.nav-link'));
  const sections = Array.from(document.querySelectorAll('main section[id]'));

  function setActiveLink() {
    const y = window.scrollY + 120;
    let activeId = sections.length ? sections[0].id : '';
    for (const sec of sections) {
      if (sec.offsetTop <= y) activeId = sec.id;
    }
    navLinks.forEach(a => a.classList.toggle('active', a.getAttribute('data-target') === activeId));
  }

  // initialize and attach listener
  setActiveLink();
  window.addEventListener('scroll', setActiveLink);

  navLinks.forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      const href = link.getAttribute('href') || '';
      const targetId = href.startsWith('#') ? href.slice(1) : href;
      const target = document.getElementById(targetId);
      if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      mainNav.classList.remove('open');
    });
  });

  // tabs
  const tabButtons = document.querySelectorAll('.tab-btn');
  const tabPanels = document.querySelectorAll('.tab-panel');
  tabButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const key = btn.dataset.tab;
      tabButtons.forEach(b => b.classList.toggle('active', b === btn));
      tabPanels.forEach(p => p.classList.toggle('active', p.dataset.panel === key));
    });
  });

  // project modal
  const modal = document.getElementById('projectModal');
  const modalImg = document.getElementById('modalImg');
  const modalTitle = document.getElementById('modalTitle');
  const modalDesc = document.getElementById('modalDesc');
  const modalLink = document.getElementById('modalLink');

  function openModal(card) {
    if (!modal) return;
    modalImg.src = card.dataset.img || '';
    modalImg.alt = card.dataset.title || 'Project image';
    modalTitle.textContent = card.dataset.title || '';
    modalDesc.textContent = card.dataset.desc || '';
    modalLink.href = card.dataset.url || '#';
    modal.setAttribute('aria-hidden', 'false');
    modal.classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function closeModal() {
    if (!modal) return;
    modal.classList.remove('open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  document.querySelectorAll('.card-link').forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      const card = link.closest('.card');
      if (card) openModal(card);
    });
  });

  modal?.addEventListener('click', (e) => {
    if (e.target.matches('[data-close]') || e.target === modal) closeModal();
  });
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && modal?.classList.contains('open')) closeModal();
  });

  // contact form (simple client validation demo)
  const form = document.getElementById('contactForm');
  const formMsg = document.getElementById('formMsg');
  if (form) {
    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const name = (form.name?.value || '').trim();
      const email = (form.email?.value || '').trim();
      const message = (form.message?.value || '').trim();
      if (!name || !email || !message) {
        if (formMsg) { formMsg.textContent = 'Please fill in all fields.'; formMsg.style.color = 'tomato'; }
        return;
      }
      if (formMsg) { formMsg.textContent = 'Thanks — message preview logged to console.'; formMsg.style.color = '#9eeed1'; }
      console.log({ name, email, message });
      form.reset();
    });
  }

  // set year
  const yr = document.getElementById('year');
  if (yr) yr.textContent = String(new Date().getFullYear());
});
// ...existing code...