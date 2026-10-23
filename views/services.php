<?php
$reviewPhotos = ['Amanda', 'Alex', 'Chayce', 'Corbyn', 'Gretchen', 'Josi', 'Kai', 'Laurie', 'Lindsay', 'Melanie', 'Robin', 'Theresa'];
$guestReviews = content_reviews();
$serviceRows = content_service_rows();
?>
<main class="servicesPage">
  <section class="servicesHero">
    <p class="servicesKicker"><span></span> Thoughtful food, genuine hospitality</p>
    <h1>Private chef &amp;<br><em>coastal gathering services.</em></h1>
    <div class="servicesHeroBottom">
      <p>From intimate dinners to once-in-a-lifetime celebrations, every gathering deserves something special. I bring personalized menus, fresh coastal flavors, and genuine hospitality directly to your table.</p>
      <a href="<?= e(url('start-planning')) ?>" class="servicesHeroCta">Tell me what you’re gathering for <span aria-hidden="true">&#8599;</span></a>
    </div>
  </section>

  <section class="servicesList" id="experiences" aria-label="Private chef and gathering services">
    <?php foreach ($serviceRows as $index => $service): ?>
      <article class="serviceRow<?= $index % 2 ? ' reverse' : '' ?>">
        <div class="serviceRowImage">
          <img src="<?= e(url($service[4])) ?>" alt="<?= e($service[5]) ?>">
          <span class="serviceImageNumber"><?= e($service[0]) ?></span>
        </div>
        <div class="serviceRowCopy">
          <p class="servicesKicker"><span></span> The Wandering Fork</p>
          <h2><?= e($service[1]) ?></h2>
          <h3><?= e($service[2]) ?></h3>
          <p><?= e($service[3]) ?></p>
          <a href="<?= e(url('start-planning')) ?>" class="serviceLink">Plan this experience <span aria-hidden="true">&#8599;</span></a>
        </div>
      </article>
    <?php endforeach; ?>
  </section>

  <section class="guestReviews" aria-labelledby="reviews-title">
    <div class="guestReviewsHeading">
      <p class="servicesKicker"><span></span> Kind words from the table</p>
      <h2 id="reviews-title">Good food is better<br><em>when shared.</em></h2>
      <p>Gatherings are made of little moments. Here are a few memories guests have shared after theirs.</p>
    </div>
    <div id="review-content">
      <div class="reviewCollage" aria-label="Photos shared by The Wandering Fork guests">
        <?php foreach ($reviewPhotos as $index => $name): ?>
          <figure class="reviewPhoto review-photo-<?= $index + 1 ?><?= $index >= 5 ? ' extra-review' : '' ?>">
            <img src="<?= e(url('review/' . $name . '.JPG')) ?>" alt="Photo shared by guest <?= e($name) ?>">
            <figcaption><?= e($name) ?></figcaption>
          </figure>
        <?php endforeach; ?>
      </div>
      <div class="reviewQuoteGrid">
        <?php foreach ($guestReviews as $index => [$name, $quote]): ?>
          <blockquote<?= $index >= 2 ? ' class="extra-review"' : '' ?>>
            <span aria-label="5 out of 5 stars">★★★★★</span>
            <p>“<?= e($quote) ?>”</p>
            <cite><?= e($name) ?></cite>
          </blockquote>
        <?php endforeach; ?>
      </div>
    </div>
    <button class="reviewsToggle" type="button" aria-expanded="false" aria-controls="review-content">Read more <span aria-hidden="true">+</span></button>
  </section>
</main>
