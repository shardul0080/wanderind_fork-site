(() => {
  if (!document.body.classList.contains('page-home')) return;

  document.querySelectorAll('h2.rv').forEach((heading) => {
    const words = (heading.textContent || '').trim().split(/\s+/);
    heading.innerHTML = words.map((word, i) => `<span class="sw"><span style="--k:${i}">${word}</span></span>`).join(' ');
    heading.classList.add('sp');
  });

  document.querySelectorAll('#qs blockquote p').forEach((paragraph) => {
    paragraph.innerHTML = (paragraph.textContent || '').split(/\s+/).map((word, i) => `<span class="qw" style="--k:${i}">${word}</span>`).join(' ');
  });

  const scrub = [...document.querySelectorAll('.scrub')].map((paragraph) => {
    paragraph.innerHTML = (paragraph.textContent || '').split(/\s+/).map((word) => `<span class="w">${word}</span>`).join(' ');
    return [paragraph, [...paragraph.querySelectorAll('.w')]];
  });
  const reveal = new IntersectionObserver((entries, observer) => entries.forEach((entry) => {
    if (entry.isIntersecting) { entry.target.classList.add('v'); observer.unobserve(entry.target); }
  }), { threshold: .15 });
  document.querySelectorAll('.rv').forEach((element) => reveal.observe(element));

  const items = [...document.querySelectorAll('#svcList li')];
  const images = [...document.querySelectorAll('#stage img')];
  const pick = (item) => {
    if (item.classList.contains('on')) return;
    items.forEach((row) => row.classList.toggle('on', row === item));
    const old = images.find((image) => image.classList.contains('on'));
    images.forEach((image) => image.classList.remove('prev'));
    if (old) { old.classList.add('prev'); old.classList.remove('on'); }
    images.forEach((image) => {
      if (image.dataset.i === item.dataset.i) { image.classList.remove('prev'); image.classList.add('on'); }
    });
  };
  items.forEach((item) => {
    item.addEventListener('mouseenter', () => pick(item));
    item.addEventListener('focusin', () => pick(item));
    item.querySelector('button')?.addEventListener('click', () => pick(item));
  });

  const quotes = [...document.querySelectorAll('#qs blockquote')];
  const dots = [...document.querySelectorAll('#dots button')];
  let quoteIndex = 0;
  const showQuote = (index) => {
    quoteIndex = index;
    quotes.forEach((quote, i) => quote.classList.toggle('on', i === index));
    dots.forEach((dot, i) => dot.classList.toggle('on', i === index));
  };
  let quoteTimer;
  const runQuotes = () => {
    window.clearInterval(quoteTimer);
    if (quotes.length > 1) quoteTimer = window.setInterval(() => showQuote((quoteIndex + 1) % quotes.length), 6000);
  };
  dots.forEach((dot, index) => dot.addEventListener('click', () => { showQuote(index); runQuotes(); }));
  runQuotes();

  const movingRows = [...document.querySelectorAll('[data-x]')];
  const parallax = [...document.querySelectorAll('[data-p]')];
  const steps = document.getElementById('steps');
  const cards = steps ? [...steps.children].filter((child) => child.tagName === 'DIV') : [];
  const track = steps?.querySelector('.track i');
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  let scheduled = false;
  const clamp = (value) => Math.max(0, Math.min(1, value));
  const frame = () => {
    scheduled = false;
    const viewport = window.innerHeight;
    scrub.forEach(([paragraph, words]) => {
      const rect = paragraph.getBoundingClientRect();
      const count = Math.round(clamp((viewport * .88 - rect.top) / (viewport * .45)) * words.length);
      words.forEach((word, i) => word.classList.toggle('on', i < count));
    });
    if (steps && track) {
      const rect = steps.getBoundingClientRect();
      const progress = clamp((viewport * .82 - rect.top) / (viewport * .5));
      track.style.width = `${progress * 100}%`;
      cards.forEach((card, i) => card.classList.toggle('lit', progress > (i + .25) / 4));
    }
    if (!reducedMotion) {
      movingRows.forEach((element) => {
        const rect = element.getBoundingClientRect();
        const center = rect.top + rect.height / 2 - viewport / 2;
        if (Math.abs(center) < viewport * 1.5) element.style.translate = `${(center * Number(element.dataset.x)).toFixed(1)}px 0`;
      });
      const badge = document.getElementById('badge');
      if (badge) badge.style.rotate = `${(window.scrollY * .22).toFixed(1)}deg`;
      parallax.forEach((element) => {
        const rect = element.getBoundingClientRect();
        const center = rect.top + rect.height / 2 - viewport / 2;
        if (Math.abs(center) < viewport * 1.3) element.style.translate = `0 ${(center * Number(element.dataset.p)).toFixed(1)}px`;
      });
    }
  };
  const requestFrame = () => {
    if (!scheduled) { scheduled = true; window.requestAnimationFrame(frame); }
  };
  window.addEventListener('scroll', requestFrame, { passive: true });
  window.addEventListener('resize', requestFrame);
  frame();

  if (window.matchMedia('(hover:hover) and (pointer:fine)').matches && !reducedMotion) {
    document.querySelectorAll('.tilt').forEach((element) => {
      element.addEventListener('mousemove', (event) => {
        const rect = element.getBoundingClientRect();
        const x = (event.clientX - rect.left) / rect.width;
        const y = (event.clientY - rect.top) / rect.height;
        element.style.transition = 'transform .12s ease-out,box-shadow .4s';
        element.style.transform = `perspective(900px) rotateX(${((.5-y)*12).toFixed(2)}deg) rotateY(${((x-.5)*14).toFixed(2)}deg) translateZ(16px)`;
        element.style.setProperty('--mx', `${x*100}%`);
        element.style.setProperty('--my', `${y*100}%`);
        element.classList.add('hot');
      });
      element.addEventListener('mouseleave', () => {
        element.style.transition = 'transform .8s cubic-bezier(.2,.7,.2,1),box-shadow .6s';
        element.style.transform = '';
        element.classList.remove('hot');
      });
    });
  }
  document.querySelectorAll('.btn').forEach((button) => {
    button.addEventListener('mousemove', (event) => {
      const rect = button.getBoundingClientRect();
      button.style.translate = `${((event.clientX-rect.left-rect.width/2)*.22).toFixed(1)}px ${((event.clientY-rect.top-rect.height/2)*.32).toFixed(1)}px`;
    });
    button.addEventListener('mouseleave', () => { button.style.translate = ''; });
  });
})();
