<?php
$howSteps = content_how_steps();
?>
<main class="howPage">
  <section class="howHero">
    <p class="howEyebrow"><span></span> Good food, without the extra planning</p>
    <h1>A lovely meal.<br><em>Made easy.</em></h1>
    <div class="howHeroBottom">
      <p>Tell me a little about your gathering. I’ll take care of the food and the details, so you can be right where you want to be.</p>
      <a class="howHeroLink" href="<?= e(url('start-planning')) ?>">Tell me about your gathering <span aria-hidden="true">â†—</span></a>
    </div>
    <span class="howHeroIndex">PRIVATE CHEF EXPERIENCES · OREGON COAST</span>
  </section>

  <section class="howProcess" aria-labelledby="process-title">
    <div class="howSectionHeading">
      <p class="howEyebrow"><span></span> From first hello to final plate</p>
      <h2 id="process-title">Three simple steps.<br><em>One memorable gathering.</em></h2>
    </div>
    <div class="howSteps">
      <?php foreach ($howSteps as $step): ?>
        <article class="howStep">
          <div class="howStepImage">
            <img src="<?= e(url($step[3])) ?>" alt="<?= e($step[4]) ?>">
            <span><?= e($step[0]) ?></span>
            <small><?= e($step[4]) ?></small>
          </div>
          <div class="howStepCopy">
            <span class="howStepNumber">STEP <?= e($step[0]) ?></span>
            <h3><?= e($step[1]) ?></h3>
            <p><?= e($step[2]) ?></p>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="howRack" aria-labelledby="rack-title">
    <div class="howRackHeading">
      <p class="howEyebrow"><span></span> A little more about the experience</p>
      <h2 id="rack-title">A taste of what<br><em>we can make together.</em></h2>
      <p>From quiet dinners to full-table celebrations, every gathering gets thoughtful food and genuine hospitality.</p>
    </div>
    <div class="howRackCards">
      <figure class="rackCard rackCardOne">
        <img src="<?= e(url('Rack Cards - Chamber/1.jpg')) ?>" alt="The Wandering Fork rack card introducing private chef dining on the Oregon Coast" width="889" height="2000">
        <figcaption>Private chef dining on the Oregon Coast</figcaption>
      </figure>
      <figure class="rackCard rackCardTwo">
        <img src="<?= e(url('Rack Cards - Chamber/2.jpg')) ?>" alt="The Wandering Fork rack card describing private dinners, weddings, retreats, and celebrations" width="889" height="2000">
        <figcaption>Good food. Good people. A beautiful place to gather.</figcaption>
      </figure>
    </div>
  </section>

  <section class="howNote">
    <p class="howEyebrow"><span></span> The best part</p>
    <h2>You make the memories.<br><em>I’ll take care of dinner.</em></h2>
    <p>Stay for the sunset. Take the scenic route. I’ll pack the picnic basket and take care of the meal.</p>
    <a class="howNoteLink" href="<?= e(url('start-planning')) ?>">Start planning your gathering <span aria-hidden="true">â†—</span></a>
  </section>
</main>
