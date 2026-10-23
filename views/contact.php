<?php
$site = content_site();
$socials = [];
foreach ($site['socials'] as $social) {
    $socials[] = [$social['name'], $social['handle'], $social['url'], $social['icon']];
}
?>
<main class="contactPage">
  <section class="hero">
    <div class="heroCopy">
      <p class="eyebrow"><span></span> Good food, good company</p>
      <h1>A little more<br><em>around the table.</em></h1>
      <p class="heroIntro">Business details, local connections, and a few kind words from the people we’ve cooked for along the Oregon Coast.</p>
      <a class="heroLink" href="mailto:<?= e($site['email']) ?>">Start a conversation <span aria-hidden="true">â†—</span></a>
    </div>
    <div class="heroImage">
      <img src="<?= e(url('img/i4.png')) ?>" alt="A beautifully prepared meal by The Wandering Fork">
      <div class="imageCaption"><span>THE WANDERING FORK</span><span>GARIBALDI Â· OREGON</span></div>
    </div>
    <div class="heroIndex">01 <span></span> 04</div>
  </section>

  <section class="details sectionShell" aria-labelledby="details-title">
    <div class="sectionHeading">
      <p class="eyebrow eyebrowDark">01 / Business information</p>
      <h2 id="details-title">Find us. Follow along.</h2>
    </div>
    <div class="detailsGrid">
      <article class="addressCard">
        <h3 class="detailsTitle">Mailing address</h3>
        <div class="addressMark" aria-hidden="true">WF</div>
        <address><?= $site['address'] ?></address>
        <div class="addressRule"></div>
        <p>Serving gatherings and celebrations on the Oregon Coast.</p>
      </article>
      <div class="socialArea">
        <h3 class="detailsTitle socialTitle">Socials</h3>
        <div class="socialGrid">
          <?php foreach ($socials as $index => [$name, $handle, $href, $icon]): ?>
            <a class="socialCard" href="<?= e($href) ?>" target="_blank" rel="noreferrer">
              <span class="socialNumber">0<?= $index + 1 ?></span>
              <span class="socialIcon">
                <?php if ($icon === 'instagram'): ?>
                  <svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="#fff9ec" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="#fff9ec" stroke="none"/></svg>
                <?php elseif ($icon === 'facebook'): ?>
                  <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="#fff9ec" d="M13.8 21v-8h2.7l.4-3.1h-3.1v-2c0-.9.3-1.5 1.6-1.5H17V3.6c-.3 0-1.3-.1-2.5-.1-2.5 0-4.2 1.5-4.2 4.3v2.1H7.5V13h2.8v8z"/></svg>
                <?php else: ?>
                  <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="#fff9ec" d="M16.4 3c.3 2.2 1.5 3.5 3.6 3.7v3.1a8.5 8.5 0 0 1-3.6-1.1v6.2a5.8 5.8 0 1 1-5.8-5.8c.4 0 .8 0 1.2.1v3.3a2.6 2.6 0 1 0 1.5 2.4V3z"/></svg>
                <?php endif; ?>
              </span>
              <span class="socialName"><?= e($name) ?></span>
              <span class="socialHandle"><?= e($handle) ?></span>
              <span class="socialArrow" aria-hidden="true">â†—</span>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <section class="communityBand">
    <div class="sectionShell communityInner">
      <div class="communityTitle">
        <p class="eyebrow">02 / Community</p>
        <h2>Rooted in the<br><em>coastal community.</em></h2>
      </div>
      <div class="memberList">
        <a class="memberCard" href="https://tillamookchamber.org/" target="_blank" rel="noreferrer">
          <span class="memberSeal">TC</span>
          <span class="memberCopy"><small>PROUD MEMBER</small><strong>Tillamook Chamber<br>of Commerce</strong></span>
          <span class="memberArrow" aria-hidden="true">â†—</span>
        </a>
        <a class="memberCard" href="https://www.rockawaybeachbusiness.org/" target="_blank" rel="noreferrer">
          <span class="memberSeal waveSeal">RB</span>
          <span class="memberCopy"><small>LOCAL BUSINESS MEMBER</small><strong>Rockaway Beach<br>Business Association</strong></span>
          <span class="memberArrow" aria-hidden="true">â†—</span>
        </a>
      </div>
    </div>
  </section>

  <section class="press sectionShell">
    <div class="sectionHeading">
      <p class="eyebrow eyebrowDark">03 / In good company</p>
      <h2>Press &amp; kind words.</h2>
    </div>
    <div class="pressGrid">
      <a class="featureStory" href="https://theoregoncoastguide.com/meet-your-neighbor-kindle-reeder-the-wandering-fork/" target="_blank" rel="noreferrer">
        <span class="storyBrand">THE OREGON<br>COAST GUIDE</span>
        <span class="storyMeta">MEET YOUR NEIGHBOR <span>Â·</span> LOCAL PROFILE</span>
        <strong>Meet Kindle Reeder,<br>The Wandering Fork</strong>
        <span class="storyRead">Read the story <b>â†—</b></span>
      </a>
      <div class="reviewsPanel">
        <span class="miniLabel">GUEST REVIEWS</span>
        <h3>Good food travels<br>by word of mouth.</h3>
        <p>Find more guest reviews and recommendations on:</p>
        <div class="reviewLinks">
          <a href="https://www.facebook.com/profile.php?id=61584418147328" target="_blank" rel="noreferrer"><span class="reviewMonogram">f</span>Facebook <b>â†—</b></a>
          <a href="https://www.yelp.com/search?find_desc=The+Wandering+Fork&amp;find_loc=Garibaldi%2C+OR" target="_blank" rel="noreferrer"><span class="reviewMonogram reviewMonogramYelp">y</span>Yelp <b>â†—</b></a>
          <a href="https://www.google.com/search?q=The+Wandering+Fork+Garibaldi+OR+reviews" target="_blank" rel="noreferrer"><span class="reviewMonogram reviewMonogramGoogle">G</span>Google <b>â†—</b></a>
        </div>
        <div class="reviewFoodPhoto">
          <img src="<?= e(url('images/private-chef-dinner.jpg')) ?>" alt="A richly plated private chef dinner, ready to share">
          <span>Made for lingering at the table</span>
        </div>
      </div>
    </div>
    <a class="weddingNote" href="https://www.facebook.com/profile.php?id=61584418147328" target="_blank" rel="noreferrer">
      <span class="noteStar">âœ³</span>
      <span><small>A NOTE FROM THE KITCHEN</small><strong>Looking for Adam Macko’s wedding review?</strong><em>Find it with our Facebook reviews.</em></span>
      <b class="noteArrow" aria-hidden="true">â†—</b>
    </a>
  </section>
</main>

<section class="contactExtra" aria-label="More ways to connect">
  <div class="contactExtraTop">
    <span class="contactExtraWordmark">The Wandering Fork</span>
    <a href="https://thewanderingfork.carrd.co" target="_blank" rel="noreferrer">View our Carrd page <b>â†—</b></a>
  </div>
  <div class="contactExtraBottom">
    <span><a href="mailto:<?= e($site['email']) ?>"><?= e($site['email']) ?></a><i>·</i><a href="tel:<?= e($site['phone']) ?>"><?= e($site['phone']) ?></a></span>
    <small>GARIBALDI, OREGON COAST</small>
  </div>
</section>
