<main class="planningPage">
  <?php $site = content_site(); ?>
  <a class="planningBrand" href="<?= e(url()) ?>">
    The Wandering Fork
    <span>PRIVATE CHEF · OREGON COAST</span>
  </a>

  <div class="planningLayout">
    <section class="planningStory">
      <p class="planningEyebrow">Good food brings us together</p>
      <h1>Make room for a meal worth remembering.</h1>
      <p class="planningIntro">
        Tell us what you’re dreaming up. We’ll help shape the menu and take care of the details, so you can be present with your people.
      </p>
      <div class="planningPromise">
        <span>01</span>
        <p><b>Share your plans</b><br>A few details are all we need to begin.</p>
      </div>
      <div class="planningPromise">
        <span>02</span>
        <p><b>We’ll be in touch</b><br>We’ll follow up to talk through your gathering.</p>
      </div>
      <div class="planningDirect">
        Prefer to reach out directly?<br>
        <a href="mailto:<?= e($site['email']) ?>"><?= e($site['email']) ?></a><br>
        <a href="tel:<?= e($site['phone']) ?>"><?= e($site['phone']) ?></a>
      </div>
    </section>

    <section class="planningCard">
      <div class="formHeading">
        <div>
          <p class="formKicker">YOUR GATHERING</p>
          <h2>Start a conversation</h2>
        </div>
        <span class="formSpark" aria-hidden="true">&#10033;</span>
      </div>
      <p class="formIntro">Tell us a little about your event. We’ll take it from there.</p>
      <form id="inquiryForm" action="<?= e(url('api/submit-inquiry.php')) ?>" method="post" class="planningForm">
        <label>
          Your name <span class="required">*</span>
          <input name="name" autocomplete="name" placeholder="Name" required>
        </label>
        <label>
          Email address <span class="required">*</span>
          <input name="email" type="email" autocomplete="email" placeholder="you@example.com" required>
        </label>
        <label>
          Phone number
          <input name="phone" type="tel" autocomplete="tel" placeholder="(555) 555-5555" required>
        </label>
        <div class="planningRow">
          <label>
            Event date
            <input name="event_date" type="date" required>
          </label>
          <label>
            Guest count
            <input name="guest_count" type="number" min="1" placeholder="Approx. guests" required>
          </label>
        </div>
        <label class="planningLocation">
          Gathering location
          <input name="location" placeholder="City or venue" required>
        </label>
        <label class="planningMessage">
          What are you celebrating?
          <textarea name="details" rows="4" placeholder="Occasion, menu ideas, dietary needs, or anything else on your mind" required></textarea>
        </label>
        <p class="formRequired">Required fields</p>
        <p class="formStatus" id="inquiryMsg" role="status" style="display:none"></p>
        <button type="submit" class="btn" id="inquirySubmit">Send your inquiry <span aria-hidden="true">&rarr;</span></button>
      </form>
    </section>
  </div>

  <a class="planningBack" href="<?= e(url()) ?>">&larr; Back to the website</a>
</main>
