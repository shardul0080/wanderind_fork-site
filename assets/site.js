(() => {
  const nav = document.getElementById('nav');
  const burger = document.querySelector('.burger');
  const menu = document.getElementById('mobile-menu');
  const setMenu = (open) => {
    if (!burger || !menu) return;
    burger.classList.toggle('o', open);
    menu.classList.toggle('o', open);
    burger.setAttribute('aria-expanded', String(open));
    burger.setAttribute('aria-label', open ? 'Close navigation' : 'Open navigation');
    menu.setAttribute('aria-hidden', String(!open));
    menu.inert = !open;
  };
  burger?.addEventListener('click', () => setMenu(burger.getAttribute('aria-expanded') !== 'true'));
  menu?.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => setMenu(false)));
  document.addEventListener('keydown', (event) => { if (event.key === 'Escape') setMenu(false); });
  const scrollState = () => nav?.classList.toggle('s', window.scrollY > 40);
  window.addEventListener('scroll', scrollState, { passive: true });
  scrollState();

  const filters = [...document.querySelectorAll('[data-filter]')];
  const galleryCards = [...document.querySelectorAll('.galleryCard[data-category]')];
  filters.forEach((button) => button.addEventListener('click', () => {
    filters.forEach((filter) => {
      const active = filter === button;
      filter.classList.toggle('active', active);
      filter.setAttribute('aria-pressed', String(active));
    });
    const category = button.dataset.filter;
    let count = 0;
    galleryCards.forEach((card) => {
      const visible = category === 'Everything' || card.dataset.category === category;
      card.hidden = !visible;
      if (visible) count++;
    });
    const counter = document.getElementById('gallery-count');
    if (counter) counter.textContent = String(count);
  }));

  const lightbox = document.querySelector('.galleryLightbox');
  const lightboxImage = lightbox?.querySelector('img');
  let activeGalleryButton = null;
  const showGalleryPhoto = (button) => {
    const image = button?.querySelector('img');
    if (!image || !lightbox || !lightboxImage) return;
    activeGalleryButton = button;
    lightboxImage.src = image.currentSrc || image.src;
    lightboxImage.alt = image.alt;
  };
  const showNextGalleryPhoto = () => {
    const visibleButtons = [...document.querySelectorAll('.galleryCard:not([hidden]) .galleryImage')];
    const currentIndex = visibleButtons.indexOf(activeGalleryButton);
    if (currentIndex < 0 || visibleButtons.length < 2) return;
    showGalleryPhoto(visibleButtons[(currentIndex + 1) % visibleButtons.length]);
  };
  const showPreviousGalleryPhoto = () => {
    const visibleButtons = [...document.querySelectorAll('.galleryCard:not([hidden]) .galleryImage')];
    const currentIndex = visibleButtons.indexOf(activeGalleryButton);
    if (currentIndex < 0 || visibleButtons.length < 2) return;
    showGalleryPhoto(visibleButtons[(currentIndex - 1 + visibleButtons.length) % visibleButtons.length]);
  };
  document.querySelectorAll('.galleryImage').forEach((button) => button.addEventListener('click', () => {
    showGalleryPhoto(button);
    lightbox.showModal();
  }));
  lightbox?.querySelector('.galleryLightboxClose')?.addEventListener('click', () => lightbox.close());
  lightbox?.querySelector('.galleryLightboxPrevious')?.addEventListener('click', showPreviousGalleryPhoto);
  lightbox?.querySelector('.galleryLightboxNext')?.addEventListener('click', showNextGalleryPhoto);
  lightbox?.addEventListener('keydown', (event) => {
    if (event.key !== 'ArrowRight' && event.key !== 'ArrowLeft') return;
    event.preventDefault();
    if (event.key === 'ArrowRight') showNextGalleryPhoto();
    else showPreviousGalleryPhoto();
  });
  lightbox?.addEventListener('click', (event) => {
    if (event.target === lightbox) lightbox.close();
  });
  lightbox?.addEventListener('close', () => { activeGalleryButton = null; });

  document.querySelector('.reviewsToggle')?.addEventListener('click', (event) => {
    const button = event.currentTarget;
    const expanded = button.getAttribute('aria-expanded') !== 'true';
    button.setAttribute('aria-expanded', String(expanded));
    button.innerHTML = expanded ? 'Show less <span>−</span>' : 'Read more <span>+</span>';
    document.querySelector('.guestReviews')?.classList.toggle('expanded', expanded);
  });

  const planningForm = document.querySelector('.planningForm');
  const inquiryMessage = document.getElementById('inquiryMsg');
  const inquirySubmit = document.getElementById('inquirySubmit');
  planningForm?.addEventListener('submit', async (event) => {
    event.preventDefault();
    const form = event.currentTarget;
    const formValues = new FormData(form);
    const field = (name) => String(formValues.get(name) ?? '').trim();
    const payload = {
      name: field('name'),
      email: field('email'),
      phone: field('phone'),
      event_date: field('event_date'),
      guest_count: field('guest_count'),
      location: field('location'),
      details: field('details'),
    };
    if (inquirySubmit) inquirySubmit.disabled = true;
    if (inquiryMessage) { inquiryMessage.textContent = ''; inquiryMessage.style.display = 'none'; }
    try {
      const response = await fetch((window.SITE_BASE ? window.SITE_BASE + '/api/submit-inquiry.php' : '/api/submit-inquiry.php'), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
      });
      const data = await response.json().catch(() => ({}));
      if (response.ok && data.ok) {
        form.reset();
        if (inquiryMessage) { inquiryMessage.textContent = 'Thank you. Your inquiry was sent to the team.'; inquiryMessage.style.display = 'block'; }
      } else {
        if (inquiryMessage) { inquiryMessage.textContent = data.error || 'Sorry, we could not send your inquiry. Please try again.'; inquiryMessage.style.display = 'block'; }
      }
    } catch (error) {
      if (inquiryMessage) { inquiryMessage.textContent = 'Sorry, we could not send your inquiry. Please try again.'; inquiryMessage.style.display = 'block'; }
    } finally {
      if (inquirySubmit) inquirySubmit.disabled = false;
    }
  });

  document.getElementById('sub')?.addEventListener('click', () => {
    const input = document.getElementById('em');
    const message = document.getElementById('nm');
    const valid = /^\S+@\S+\.\S+$/.test(input.value);
    message.textContent = valid ? 'Thank you. You are on the list.' : 'Please enter a valid email address.';
    if (valid) input.value = '';
  });
  document.getElementById('top2')?.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
})();
