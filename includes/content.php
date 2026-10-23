<?php
declare(strict_types=1);

/**
 * Content layer for The Wandering Fork.
 *
 * Reads editable site content from PostgreSQL. Every getter falls back to
 * the original hardcoded content so the site keeps working even when the
 * database is empty or unreachable.
 */

require_once __DIR__ . '/../config/database.php';

function content_db(): ?PDO
{
    try {
        return new PDO(
            sprintf('pgsql:host=%s;port=%d;dbname=%s', (string) (getenv('DB_HOST') ?: '127.0.0.1'), (int) (getenv('DB_PORT') ?: 5432), (string) (getenv('DB_NAME') ?: 'wandering-fork-site')),
            (string) (getenv('DB_USER') ?: 'wandering-fork-site'),
            (string) (getenv('DB_PASSWORD') ?: '1234'),
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    } catch (Throwable) {
        return null;
    }
}

function content_section(string $section): array
{
    $pdo = content_db();
    if ($pdo === null) {
        return [];
    }
    try {
        $stmt = $pdo->prepare('SELECT key, value FROM site_content WHERE section = :section');
        $stmt->execute([':section' => $section]);
        $out = [];
        while ($row = $stmt->fetch()) {
            $out[(string) $row['key']] = ($row['value'] === null) ? null : (string) $row['value'];
        }
        return $out;
    } catch (Throwable) {
        return [];
    }
}

function content_json(string $section, string $key, $default = null)
{
    $all = content_section($section);
    if (!array_key_exists($key, $all)) {
        return $default;
    }
    $raw = $all[$key];
    if ($raw === null || $raw === '') {
        return $default;
    }
    $decoded = json_decode((string) $raw, true);
    return is_array($decoded) ? $decoded : $default;
}

function content_hero(): array
{
    $defaults = [
        'kicker' => 'Thoughtful food, genuine hospitality',
        'title' => 'Private chef dining on the Oregon Coast',
        'text' => "Thoughtfully prepared food for intimate weddings, elopements, retreats and vacation stays, without the stress of traditional catering.",
        'cta' => 'Plan your gathering',
        'note' => 'Serving Tillamook County, Neskowin, Pacific City and nearby coastal towns.',
    ];
    $saved = content_section('hero');
    $hero = [];
    foreach ($defaults as $key => $default) {
        $hero[$key] = array_key_exists($key, $saved) && $saved[$key] !== null && $saved[$key] !== '' ? $saved[$key] : (string) $default;
    }
    return $hero;
}

function content_site(): array
{
    $defaults = [
        'tagline' => 'Private chef and coastal catering for gatherings along the Oregon Coast.',
        'phone' => '562-280-3734',
        'email' => 'thewanderingforkcatering@gmail.com',
        'address' => 'PO Box 783<br><strong>Garibaldi, Oregon 97118</strong>',
        'serving' => 'Tillamook County, Neskowin, Pacific City and surrounding coastal communities',
    ];
    $saved = content_section('site');
    $site = [];
    foreach ($defaults as $key => $default) {
        $site[$key] = array_key_exists($key, $saved) && $saved[$key] !== null && $saved[$key] !== '' ? $saved[$key] : (string) $default;
    }
    $socials = content_section('social');
    $site['socials'] = [];
    foreach ($socials as $key => $value) {
        if ($key === 'instagram') {
            $site['socials']['instagram'] = ['name' => 'Instagram', 'handle' => '@thewanderingforkcatering', 'url' => 'https://www.instagram.com/thewanderingforkcatering/', 'icon' => 'instagram'];
        } elseif ($key === 'facebook') {
            $site['socials']['facebook'] = ['name' => 'Facebook', 'handle' => '@thewanderingforkcatering', 'url' => 'https://www.facebook.com/profile.php?id=61584418147328', 'icon' => 'facebook'];
        } elseif ($key === 'tiktok') {
            $site['socials']['tiktok'] = ['name' => 'TikTok', 'handle' => '@thewanderingforkcatering', 'url' => 'https://www.tiktok.com/@thewanderingforkcatering', 'icon' => 'tiktok'];
        }
    }
    return $site;
}

function content_home_services(): array
{
    $defaults = [
        ['Private chef experiences', 'Custom menus prepared onsite in your vacation rental, home or gathering space.', 'img/i5.png', '5'],
        ['Elopements and intimate weddings', 'Relaxed, memorable dining and beautiful grazing displays for your coastal celebration.', 'img/i2.png', '1'],
        ['Retreats and group dining', 'Breakfasts, dinners, grazing displays and multi-meal service for creative, wellness and group retreats.', 'img/i3.png', '3'],
        ['Family reunions and celebrations', 'Custom menus for birthdays, anniversaries, reunions and gatherings of all kinds.', 'img/i4.png', '4'],
        ['Vacation rental and Airbnb services', 'Arrival meals, fridge stocking, prepared meals and grazing boards that make your stay effortless.', 'img/i7.png', '7'],
    ];
    $pdo = content_db();
    if ($pdo !== null) {
        try {
            $stmt = $pdo->query('SELECT title, tagline, image, sort_order FROM services WHERE active = TRUE ORDER BY sort_order ASC');
            $rows = $stmt->fetchAll();
            if ($rows) {
                $out = [];
                $homeDefaultImages = [
                    'img/i5.png',
                    'img/i2.png',
                    'img/i3.png',
                    'img/i4.png',
                    'img/i7.png',
                ];
                foreach ($rows as $row) {
                    $index = (int) $row['sort_order'] - 1;
                    $out[] = [
                        (string) $row['title'],
                        (string) $row['tagline'],
                        $homeDefaultImages[$index] ?? (string) $row['image'],
                        (string) $row['sort_order'],
                    ];
                }
                return $out;
            }
        } catch (Throwable) {
            // fall through to defaults
        }
    }
    return $defaults;
}

function content_home_photos(): array
{
    $defaults = [
        ['img/i2.png', 'Intimate wedding buffet, Neskowin'],
        ['img/i4.png', 'Tomahawk steaks and asparagus'],
        ['img/i3.png', 'Individual grazing plates'],
        ['img/i5.png', 'Stuffed steak entree'],
        ['img/i7.png', 'Peach and burrata salad'],
        ['img/i6.png', 'Garlic butter shrimp'],
    ];
    $saved = content_json('home', 'photos');
    if (is_array($saved) && count($saved) > 0) {
        $out = [];
        foreach ($saved as $photo) {
            if (is_array($photo) && isset($photo[0], $photo[1])) {
                $out[] = [(string) $photo[0], (string) $photo[1]];
            }
        }
        if ($out) {
            return $out;
        }
    }
    return $defaults;
}

function content_home_quotes(): array
{
    $defaults = [
        ['The presentation was elegant, food high quality and best of all it was delicious. Our 22 person group was delighted.', 'Amanda Marie, family reunion'],
        ['The food was absolutely exceptional, fresh, beautifully presented, and incredibly high quality.', 'Adam Macko, intimate wedding'],
        ["Our guests haven't stopped talking about it. She truly went above and beyond.", 'Melanie Widmier'],
    ];
    $saved = content_json('home', 'quotes');
    $quotes = $defaults;
    if (is_array($saved)) {
        foreach ($saved as $quote) {
            if (is_array($quote) && isset($quote[0], $quote[1])) {
                $quotes[] = [(string) $quote[0], (string) $quote[1]];
            }
        }
    }
    return $quotes;
}

function content_service_rows(): array
{
    $defaults = [
        ['01', 'Private Chef Experiences', 'Your table, your people, your kind of evening.', 'Enjoy a thoughtfully prepared, multi-course dinner without leaving the comfort of your coastal getaway or home. From romantic evenings for two to lively dinners with friends, each menu is created around your tastes, occasion, and the experience you want to share.', 'images/private-chef-dinner.jpg', 'A plated steak dinner prepared by The Wandering Fork'],
        ['02', 'Elopements & Intimate Weddings', 'A beautiful beginning deserves a memorable meal.', "Whether you're exchanging vows overlooking the Pacific or celebrating with your closest family and friends, we'll create a dining experience that feels as personal as your love story. From elegant plated dinners to relaxed family-style gatherings and grazing displays, the details are thoughtfully tailored to your day.", 'images/coastal-wedding-table.jpg', 'A wedding dinner table with a view of the Oregon Coast'],
        ['03', 'Retreats & Group Dining', 'Gather, reconnect, and leave the cooking to us.', 'Creative retreats, wellness weekends, corporate gatherings, and group getaways are all about sharing experiences. We offer customized breakfasts, dinners, grazing displays, and multi-meal service designed around your group’s schedule, dietary needs, and the moments that bring everyone together.', 'images/beach-group-dinner.jpg', 'A coastal grazing spread arranged for guests to share'],
        ['04', 'Family Reunions & Celebrations', 'More celebrating. Less coordinating.', 'Birthdays, anniversaries, retirement parties, and long-awaited family reunions deserve more than a rushed meal. Whether you’re bringing together a handful of loved ones or the whole extended family, we’ll create a menu that brings everyone to the table while you enjoy the celebration right alongside them.', 'images/grazing-board.jpg', 'A celebratory meal prepared for a gathering'],
        ['05', 'Vacation Rental & Airbnb Experiences', 'Your vacation shouldn’t revolve around dinner reservations.', 'After a day exploring the coast, the last thing you should have to worry about is finding an open restaurant or making another grocery run. Enjoy a personalized dining experience prepared right in your vacation rental, with menus designed around your plans, preferences, and the people you’re traveling with.', 'images/beach-group-dinner.jpg', 'A fresh seasonal meal for a coastal vacation'],
    ];
    $pdo = content_db();
    if ($pdo !== null) {
        try {
            $stmt = $pdo->query('SELECT * FROM services WHERE active = TRUE ORDER BY sort_order ASC');
            $rows = $stmt->fetchAll();
            if ($rows) {
                $out = [];
                foreach ($rows as $row) {
                    $out[] = [
                        (string) $row['number'],
                        (string) $row['title'],
                        (string) $row['tagline'],
                        (string) $row['description'],
                        (string) $row['image'],
                        (string) ($row['image_alt'] ?? ''),
                    ];
                }
                return $out;
            }
        } catch (Throwable) {
            // fall through to defaults
        }
    }
    return $defaults;
}

function content_reviews(): array
{
    $defaults = [
        ['Theresa', 'The food was so delicious, fresh, and phenomenal. We’re still talking about it a month later.'],
        ['Robin', 'Kindle is so passionate about food, flavors, and presentation. Knowing she’s in the kitchen gives peace of mind to enjoy the gathering.'],
        ['Lindsay', 'She helped me plan the most magical night for our friends on the Oregon Coast. Perfect night; so lucky to have found The Wandering Fork.'],
        ['Amanda', 'The presentation was elegant, the food high quality, and best of all, delicious. Our 22 person group was delighted.'],
        ['Adam', 'The food was absolutely exceptional, fresh, beautifully presented, and incredibly high quality. It helped make our wedding day so special.'],
        ['Melanie', 'She truly went above and beyond. The food was absolutely amazing, and our guests haven’t stopped talking about it.'],
    ];
    $pdo = content_db();
    if ($pdo !== null) {
        try {
            $stmt = $pdo->query('SELECT author, quote, rating FROM reviews WHERE active = TRUE ORDER BY sort_order ASC, id ASC');
            $rows = $stmt->fetchAll();
            if ($rows) {
                $out = [];
                foreach ($rows as $row) {
                    $out[] = [(string) $row['author'], (string) $row['quote']];
                }
                return $out;
            }
        } catch (Throwable) {
            // fall through to defaults
        }
    }
    return $defaults;
}

function content_how_steps(): array
{
    $defaults = [
        ['01', 'Tell me what you’re dreaming up.', 'Share the date, where you’ll be gathering, how many people are coming, and what you’re celebrating. A few starting details are all we need.', 'Behind the Scenes/IMG_1924.jpg', 'Guests gathered around a beach fire on the Oregon Coast'],
        ['02', 'We’ll make it feel like yours.', 'Together, we’ll shape a menu around your tastes, dietary needs, and the kind of evening you want. From a relaxed family-style dinner to a once-in-a-lifetime celebration, the details are personal.', 'images/private-chef-dinner.jpg', 'A plated private chef dinner tailored to guests'],
        ['03', 'Settle in. I’ll take it from here.', 'I bring the ingredients, prepare and serve the meal, then take care of the cleanup. You can stay with your people and enjoy the coast, without watching the clock or the kitchen.', 'Behind the Scenes/IMG_1918.jpg', 'A chef serving guests at a beachside gathering'],
    ];
    $pdo = content_db();
    if ($pdo !== null) {
        try {
            $stmt = $pdo->query('SELECT step_number, title, text, image, image_alt FROM how_steps WHERE active = TRUE ORDER BY sort_order ASC, id ASC');
            $rows = $stmt->fetchAll();
            if ($rows) {
                $out = [];
                foreach ($rows as $row) {
                    $out[] = [
                        (string) $row['step_number'],
                        (string) $row['title'],
                        (string) $row['text'],
                        (string) $row['image'],
                        (string) ($row['image_alt'] ?? ''),
                    ];
                }
                return $out;
            }
        } catch (Throwable) {
            // fall through to defaults
        }
    }
    return $defaults;
}

function content_home_how_steps(): array
{
    $defaults = [
        ['Tell me about it', 'Share your date, location, guest count, vision and dietary needs.'],
        ['Build your menu', "I'll create menu options around your gathering, tastes and budget."],
        ['Book your date', 'Choose your menu, sign your agreement and secure the date with a deposit.'],
        ['Gather and enjoy', 'I handle the food so you can spend your time with your guests.'],
    ];
    $saved = content_json('home', 'how');
    if (is_array($saved)) {
        $out = [];
        foreach ($saved as $step) {
            if (is_array($step) && isset($step[0], $step[1])) {
                $out[] = [(string) $step[0], (string) $step[1]];
            }
        }
        if ($out) {
            return $out;
        }
    }
    return $defaults;
}

function content_gallery(): array
{
    $pdo = content_db();
    if ($pdo !== null) {
        try {
            $stmt = $pdo->query('SELECT src, alt, caption, category, shape, width, height FROM gallery_items WHERE active = TRUE ORDER BY sort_order ASC, id ASC');
            $rows = $stmt->fetchAll();
            if ($rows) {
                $out = [];
                foreach ($rows as $row) {
                    $out[] = [
                        'src' => (string) $row['src'],
                        'alt' => (string) $row['alt'],
                        'caption' => (string) $row['caption'],
                        'category' => (string) $row['category'],
                        'shape' => (string) $row['shape'],
                        'width' => (int) $row['width'],
                        'height' => (int) $row['height'],
                    ];
                }
                return $out;
            }
        } catch (Throwable) {
            // fall back to the JSON file
        }
    }
    $items = json_decode((string) file_get_contents(__DIR__ . '/../assets/gallery-items.json'), true);
    if (!is_array($items)) {
        return [];
    }
    $out = [];
    foreach ($items as $item) {
        if (!is_array($item)) {
            continue;
        }
        $out[] = [
            'src' => (string) ($item['src'] ?? ''),
            'alt' => (string) ($item['alt'] ?? ''),
            'caption' => (string) ($item['caption'] ?? ''),
            'category' => (string) ($item['category'] ?? ''),
            'shape' => (string) ($item['shape'] ?? 'square'),
            'width' => (int) ($item['width'] ?? 0),
            'height' => (int) ($item['height'] ?? 0),
        ];
    }
    return $out;
}
