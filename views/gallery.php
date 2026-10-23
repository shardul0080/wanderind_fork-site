<?php
$items = content_gallery();
$categories = ['Everything', 'At the table', 'Gatherings', 'The coast', 'Behind the scenes'];
?>
<main class="galleryPage">
  <section class="galleryIntro">
    <p class="galleryEyebrow"><span></span> A collection of coastal moments</p>
    <h1>Good food.<br><em>Good company.</em></h1>
    <div class="galleryIntroBottom">
      <p>Thoughtful plates, long-table celebrations, and the little moments that make gathering by the coast feel like yours.</p>
      <span class="galleryIndex">OREGON COAST&nbsp; / &nbsp;01—102</span>
    </div>
  </section>

  <section class="galleryCollection" aria-label="Photo gallery">
    <div class="galleryToolbar">
      <p>Collected around the table <span>—</span> <b id="gallery-count"><?= count($items) ?></b> moments</p>
      <div class="galleryFilters" aria-label="Filter gallery photos">
        <?php foreach ($categories as $index => $category): ?>
          <button type="button" data-filter="<?= e($category) ?>" class="<?= $index === 0 ? 'active' : '' ?>" aria-pressed="<?= $index === 0 ? 'true' : 'false' ?>"><?= e($category) ?></button>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="galleryGrid">
      <?php foreach ($items as $index => $item): ?>
        <figure class="galleryCard card-<?= $index % 8 ?>" data-category="<?= e($item['category']) ?>">
          <button class="galleryImage" type="button" aria-label="View photo: <?= e($item['alt']) ?>">
            <img loading="lazy" src="<?= e(url(ltrim($item['src'], '/'))) ?>" alt="<?= e($item['alt']) ?>" width="<?= (int) $item['width'] ?>" height="<?= (int) $item['height'] ?>" sizes="(max-width: 600px) 100vw, (max-width: 900px) 50vw, 33vw">
          </button>
          <figcaption class="galleryCaption">
            <small><?= e($item['category']) ?></small>
            <span><?= e($item['caption']) ?></span>
          </figcaption>
        </figure>
      <?php endforeach; ?>
    </div>
  </section>

  <dialog class="galleryLightbox" aria-label="Enlarged gallery photo">
    <button class="galleryLightboxClose" type="button" aria-label="Close photo">&times;</button>
    <img src="" alt="">
    <button class="galleryLightboxPrevious" type="button" aria-label="Previous photo">&#9664;</button>
    <button class="galleryLightboxNext" type="button" aria-label="Next photo">&#9654;</button>
  </dialog>
  <section class="galleryOutro">
    <p>Good food brings us together.</p>
    <span>Here’s to the moments worth keeping.</span>
  </section>
</main>
