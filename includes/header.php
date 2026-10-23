<nav id="nav" class="<?= $page === 'home' ? 'home-nav' : '' ?>" aria-label="Main navigation">
  <div class="pill">
    <a class="brand" href="<?= e(url()) ?>" aria-label="The Wandering Fork home" style="font-family:'Cormorant Garamond',Georgia,serif"><img src="<?= e(url('img/logo.jpg')) ?>" alt="The Wandering Fork" width="301" height="61" style="width:auto;height:50px"></a>
    <ul id="links">
      <li><a href="<?= e(url()) ?>">Home</a></li><li><a href="<?= e(url('about')) ?>">About Us</a></li>
      <li><a href="<?= e(url('services')) ?>">Services</a></li><li><a href="<?= e(url('gallery')) ?>">Gallery</a></li>
      <li><a href="<?= e(url('how')) ?>">How it works</a></li><li><a href="<?= e(url('contact')) ?>">Contact</a></li>
    </ul>
    <a class="btn sm" href="<?= e(url('start-planning')) ?>">Book a Date</a>
    <button class="burger" type="button" aria-label="Open navigation" aria-expanded="false" aria-controls="mobile-menu"><i></i><i></i><i></i></button>
  </div>
  <div class="menu" id="mobile-menu" aria-hidden="true" inert>
    <div class="menu-links">
      <a href="<?= e(url()) ?>">Home</a><a href="<?= e(url('about')) ?>">About Us</a><a href="<?= e(url('services')) ?>">Services</a>
      <a href="<?= e(url('gallery')) ?>">Gallery</a><a href="<?= e(url('how')) ?>">How it works</a><a href="<?= e(url('contact')) ?>">Contact</a>
    </div><a class="menu-cta" href="<?= e(url('start-planning')) ?>">Book a Date</a>
  </div>
</nav>