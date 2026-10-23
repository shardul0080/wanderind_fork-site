<footer class="foot">
  <svg class="wave" viewBox="0 0 1200 60" preserveAspectRatio="none" aria-hidden="true"><path fill="currentColor" d="M0 0h1200v20C1000 60 800 0 600 25S200 55 0 20Z" /></svg>
  <div class="wrap inner">
    <div class="fgrid">
      <div>
        <div class="logo">The Wandering Fork</div>
        <p>Private chef and coastal catering for gatherings along the Oregon Coast.</p>
        <div class="soc">
          <a href="https://www.instagram.com/thewanderingforkcatering/" aria-label="Instagram" data-name="Instagram" class="social-link" target="_blank" rel="noreferrer"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5" fill="none" stroke="currentColor" stroke-width="1.6" /><circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="1.6" /><circle cx="17.5" cy="6.5" r="1.2" fill="currentColor" stroke="none" /></svg></a>
          <a href="https://www.facebook.com/profile.php?id=61584418147328" aria-label="Facebook" data-name="Facebook" class="social-link" target="_blank" rel="noreferrer"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 8h3V4h-3a4 4 0 0 0-4 4v3H7v4h3v6h4v-6h3l1-4h-4V8z" fill="currentColor" stroke="none" /></svg></a>
          <a href="mailto:thewanderingforkcatering@gmail.com" aria-label="Email" data-name="Email" class="social-link"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2" fill="none" stroke="currentColor" stroke-width="1.6" /><path d="m3 7 9 6 9-6" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" /></svg></a>
        </div>
      </div>
      <div><h4>Explore</h4><ul><li><a href="<?= e(url() . '#about') ?>">About Kindle</a></li><li><a href="<?= e(url() . '#services') ?>">Services</a></li><li><a href="<?= e(url() . '#gallery') ?>">Gallery</a></li><li><a href="<?= e(url() . '#how') ?>">How it works</a></li><li><a href="<?= e(url() . '#contact') ?>">Contact</a></li></ul></div>
      <div><h4>Services</h4><ul><?php foreach (['Private chef experiences', 'Elopements and weddings', 'Retreats and group dining', 'Family reunions', 'Vacation rental meals'] as $label): ?><li><a href="<?= e(url() . '#services') ?>"><?= e($label) ?></a></li><?php endforeach; ?></ul></div>
      <div>
        <h4>Seasonal menu notes</h4>
        <p>Occasional menu ideas and open dates, straight from Kindle.</p>
        <div class="news"><input id="em" type="email" value="" placeholder="Your email address" aria-label="Email address"><button id="sub" type="button">Subscribe</button></div>
        <div class="nmsg" id="nm" aria-live="polite"></div>
        <p style="margin-top:18px">Kindle Lynn Reeder, Chef and Owner<br />562-280-3734</p>
      </div>
    </div>
    <div class="chips"><span>Serving</span><b>Tillamook County</b><b>Neskowin</b><b>Pacific City</b><b>Surrounding coastal communities</b></div>
    <div class="mark" data-p="-.04" aria-hidden="true">The Wandering Fork</div>
    <div class="fbot">
      <span>&copy; 2026 The Wandering Fork Catering. All rights reserved. Powered By || <a href="https://premiumbusinesswebsites.com/">premiumbusinesswebsites</a></span>
      <span><a href="<?= e(url('admin/login.php')) ?>">Admin</a></span>
      <span><a href="#">Privacy</a> &nbsp;|&nbsp; <a href="#">Terms</a></span>
      <button class="top" id="top2" type="button">Back to top <span><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5M5 12l7-7 7 7" /></svg></span></button>
    </div>
  </div>
</footer>
