-- The Wandering Fork - PostgreSQL schema
-- Private chef catering website - database and seed

-- ---------------------------------------------------------------------------
-- Admin access
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS admin_users (
    id BIGSERIAL PRIMARY KEY,
    username VARCHAR(191) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Default admin login: username "admin", password "admin@123"
-- Change this password before deploying to a public server.
INSERT INTO admin_users (username, password_hash) VALUES ('admin', '$2y$12$8z4zJCi4ampXc3jzDHv5feFamP5WIWvxUG3K8GHd6sNru.hZZ4beO')
ON CONFLICT (username) DO NOTHING;

-- ---------------------------------------------------------------------------
-- Site content (key / value pairs used by the content editor)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS site_content (
    id BIGSERIAL PRIMARY KEY,
    section VARCHAR(64) NOT NULL,
    key VARCHAR(128) NOT NULL,
    value TEXT,
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (section, key)
);

INSERT INTO site_content (section, key, value) VALUES
    ('site', 'tagline', 'Private chef and coastal catering for gatherings along the Oregon Coast.'),
    ('site', 'phone', '562-280-3734'),
    ('site', 'email', 'thewanderingforkcatering@gmail.com'),
    ('site', 'address', 'PO Box 783<br><strong>Garibaldi, Oregon 97118</strong>'),
    ('site', 'serving', 'Tillamook County, Neskowin, Pacific City and surrounding coastal communities'),
    ('social', 'instagram', 'https://www.instagram.com/thewanderingforkcatering/'),
    ('social', 'facebook', 'https://www.facebook.com/profile.php?id=61584418147328'),
    ('social', 'tiktok', 'https://www.tiktok.com/@thewanderingforkcatering'),
    ('hero', 'kicker', 'Thoughtful food, genuine hospitality'),
    ('hero', 'title', 'Private chef dining on the Oregon Coast'),
    ('hero', 'text', 'Thoughtfully prepared food for intimate weddings, elopements, retreats and vacation stays, without the stress of traditional catering.'),
    ('hero', 'cta', 'Plan your gathering'),
    ('hero', 'note', 'Serving Tillamook County, Neskowin, Pacific City and nearby coastal towns.'),
    ('home', 'photos', '[["img/i2.png","Intimate wedding buffet, Neskowin"],["img/i4.png","Tomahawk steaks and asparagus"],["img/i3.png","Individual grazing plates"],["img/i5.png","Stuffed steak entree"],["img/i7.png","Peach and burrata salad"],["img/i6.png","Garlic butter shrimp"]]'),
    ('home', 'quotes', '[["The presentation was elegant, food high quality and best of all it was delicious. Our 22 person group was delighted.","Amanda Marie, family reunion"],["The food was absolutely exceptional, fresh, beautifully presented, and incredibly high quality.","Adam Macko, intimate wedding"],["Our guests haven''t stopped talking about it. She truly went above and beyond.","Melanie Widmier"]]')
ON CONFLICT DO NOTHING;

-- ---------------------------------------------------------------------------
-- Services
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS services (
    id BIGSERIAL PRIMARY KEY,
    number VARCHAR(8) NOT NULL DEFAULT '01',
    title VARCHAR(255) NOT NULL,
    tagline TEXT,
    description TEXT,
    image VARCHAR(255),
    image_alt TEXT,
    price_from NUMERIC(10, 2),
    sort_order INT NOT NULL DEFAULT 0,
    active BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE UNIQUE INDEX IF NOT EXISTS idx_services_number_unique ON services (number);

INSERT INTO services (number, title, tagline, description, image, image_alt, sort_order, active) VALUES
    ('01', 'Private Chef Experiences', 'Your table, your people, your kind of evening.', 'Enjoy a thoughtfully prepared, multi-course dinner without leaving the comfort of your coastal getaway or home. From romantic evenings for two to lively dinners with friends, each menu is created around your tastes, occasion, and the experience you want to share.', 'images/private-chef-dinner.jpg', 'A plated steak dinner prepared by The Wandering Fork', 1, TRUE),
    ('02', 'Elopements & Intimate Weddings', 'A beautiful beginning deserves a memorable meal.', 'Whether you''re exchanging vows overlooking the Pacific or celebrating with your closest family and friends, we''ll create a dining experience that feels as personal as your love story. From elegant plated dinners to relaxed family-style gatherings and grazing displays, the details are thoughtfully tailored to your day.', 'images/coastal-wedding-table.jpg', 'A wedding dinner table with a view of the Oregon Coast', 2, TRUE),
    ('03', 'Retreats & Group Dining', 'Gather, reconnect, and leave the cooking to us.', 'Creative retreats, wellness weekends, corporate gatherings, and group getaways are all about sharing experiences. We offer customized breakfasts, dinners, grazing displays, and multi-meal service designed around your group’s schedule, dietary needs, and the moments that bring everyone together.', 'images/beach-group-dinner.jpg', 'A coastal grazing spread arranged for guests to share', 3, TRUE),
    ('04', 'Family Reunions & Celebrations', 'More celebrating. Less coordinating.', 'Birthdays, anniversaries, retirement parties, and long-awaited family reunions deserve more than a rushed meal. Whether you’re bringing together a handful of loved ones or the whole extended family, we’ll create a menu that brings everyone to the table while you enjoy the celebration right alongside them.', 'images/grazing-board.jpg', 'A celebratory meal prepared for a gathering', 4, TRUE),
    ('05', 'Vacation Rental & Airbnb Experiences', 'Your vacation shouldn’t revolve around dinner reservations.', 'After a day exploring the coast, the last thing you should have to worry about is finding an open restaurant or making another grocery run. Enjoy a personalized dining experience prepared right in your vacation rental, with menus designed around your plans, preferences, and the people you’re traveling with.', 'images/beach-group-dinner.jpg', 'A fresh seasonal meal for a coastal vacation', 5, TRUE)
ON CONFLICT DO NOTHING;

-- ---------------------------------------------------------------------------
-- Guest reviews
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS reviews (
    id BIGSERIAL PRIMARY KEY,
    author VARCHAR(191) NOT NULL,
    quote TEXT NOT NULL,
    rating INT NOT NULL DEFAULT 5,
    active BOOLEAN NOT NULL DEFAULT TRUE,
    sort_order INT NOT NULL DEFAULT 0
);

CREATE UNIQUE INDEX IF NOT EXISTS idx_reviews_author_quote_unique ON reviews (author, quote);

INSERT INTO reviews (author, quote, rating, sort_order, active) VALUES
    ('Theresa', 'The food was so delicious, fresh, and phenomenal. We’re still talking about it a month later.', 5, 1, TRUE),
    ('Robin', 'Kindle is so passionate about food, flavors, and presentation. Knowing she’s in the kitchen gives peace of mind to enjoy the gathering.', 5, 2, TRUE),
    ('Lindsay', 'She helped me plan the most magical night for our friends on the Oregon Coast. Perfect night; so lucky to have found The Wandering Fork.', 5, 3, TRUE),
    ('Amanda', 'The presentation was elegant, the food high quality, and best of all, delicious. Our 22 person group was delighted.', 5, 4, TRUE),
    ('Adam', 'The food was absolutely exceptional, fresh, beautifully presented, and incredibly high quality. It helped make our wedding day so special.', 5, 5, TRUE),
    ('Melanie', 'She truly went above and beyond. The food was absolutely amazing, and our guests haven’t stopped talking about it.', 5, 6, TRUE)
ON CONFLICT DO NOTHING;

-- ---------------------------------------------------------------------------
-- How it works steps
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS how_steps (
    id BIGSERIAL PRIMARY KEY,
    step_number VARCHAR(8) NOT NULL,
    title TEXT NOT NULL,
    text TEXT NOT NULL,
    image VARCHAR(255),
    image_alt TEXT,
    sort_order INT NOT NULL DEFAULT 0,
    active BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE UNIQUE INDEX IF NOT EXISTS idx_how_steps_number_unique ON how_steps (step_number);

INSERT INTO how_steps (step_number, title, text, image, image_alt, sort_order, active) VALUES
    ('01', 'Tell me what you’re dreaming up.', 'Share the date, where you’ll be gathering, how many people are coming, and what you’re celebrating. A few starting details are all we need.', 'Behind the Scenes/IMG_1924.jpg', 'Guests gathered around a beach fire on the Oregon Coast', 1, TRUE),
    ('02', 'We’ll make it feel like yours.', 'Together, we’ll shape a menu around your tastes, dietary needs, and the kind of evening you want. From a relaxed family-style dinner to a once-in-a-lifetime celebration, the details are personal.', 'images/private-chef-dinner.jpg', 'A plated private chef dinner tailored to guests', 2, TRUE),
    ('03', 'Settle in. I’ll take it from here.', 'I bring the ingredients, prepare and serve the meal, then take care of the cleanup. You can stay with your people and enjoy the coast, without watching the clock or the kitchen.', 'Behind the Scenes/IMG_1918.jpg', 'A chef serving guests at a beachside gathering', 3, TRUE)
ON CONFLICT DO NOTHING;

-- ---------------------------------------------------------------------------
-- Gallery items
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS gallery_items (
    id BIGSERIAL PRIMARY KEY,
    src TEXT NOT NULL,
    alt TEXT NOT NULL DEFAULT '',
    caption TEXT NOT NULL DEFAULT '',
    category VARCHAR(64) NOT NULL DEFAULT 'At the table',
    shape VARCHAR(16) NOT NULL DEFAULT 'square',
    width INT,
    height INT,
    active BOOLEAN NOT NULL DEFAULT TRUE,
    sort_order INT NOT NULL DEFAULT 0
);

CREATE INDEX IF NOT EXISTS idx_gallery_category ON gallery_items (category);
CREATE INDEX IF NOT EXISTS idx_gallery_active ON gallery_items (active);
CREATE UNIQUE INDEX IF NOT EXISTS idx_gallery_src_unique ON gallery_items (src);

-- Gallery seed rows (originally from assets/gallery-items.json)
INSERT INTO gallery_items (src, alt, caption, category, shape, width, height, sort_order, active) VALUES
    ('/Behind the Scenes/IMG_1913.jpg', 'A behind-the-scenes moment with The Wandering Fork', 'A little behind the scenes', 'Behind the scenes', 'portrait', 2800, 2100, 1, TRUE),
    ('/Behind the Scenes/IMG_1918.jpg', 'A behind-the-scenes moment with The Wandering Fork', 'A little behind the scenes', 'Behind the scenes', 'landscape', 2100, 2800, 2, TRUE),
    ('/Behind the Scenes/IMG_1924.jpg', 'A behind-the-scenes moment with The Wandering Fork', 'A little behind the scenes', 'Behind the scenes', 'square', 2800, 2100, 3, TRUE),
    ('/Behind the Scenes/IMG_1928.jpg', 'A behind-the-scenes moment with The Wandering Fork', 'A little behind the scenes', 'Behind the scenes', 'portrait', 2800, 2100, 4, TRUE),
    ('/Behind the Scenes/IMG_1940.jpg', 'A behind-the-scenes moment with The Wandering Fork', 'A little behind the scenes', 'Behind the scenes', 'landscape', 2800, 2100, 5, TRUE),
    ('/Behind the Scenes/IMG_1945.jpg', 'A behind-the-scenes moment with The Wandering Fork', 'A little behind the scenes', 'Behind the scenes', 'square', 2800, 2100, 6, TRUE),
    ('/Behind the Scenes/IMG_2490.jpg', 'A behind-the-scenes moment with The Wandering Fork', 'A little behind the scenes', 'Behind the scenes', 'portrait', 2800, 2100, 7, TRUE),
    ('/Hero Food Pics/IMG_0055.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'landscape', 2100, 2800, 8, TRUE),
    ('/Hero Food Pics/IMG_0058.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'square', 2100, 2800, 9, TRUE),
    ('/Hero Food Pics/IMG_0140.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'portrait', 2100, 2800, 10, TRUE),
    ('/Hero Food Pics/IMG_0142.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'landscape', 2100, 2800, 11, TRUE),
    ('/Hero Food Pics/IMG_0143.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'square', 2100, 2800, 12, TRUE),
    ('/Hero Food Pics/IMG_0144.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'portrait', 2100, 2800, 13, TRUE),
    ('/Hero Food Pics/IMG_0488.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'landscape', 2100, 2800, 14, TRUE),
    ('/Hero Food Pics/IMG_0489.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'square', 2800, 2100, 15, TRUE),
    ('/Hero Food Pics/IMG_0599.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'portrait', 2800, 2100, 16, TRUE),
    ('/Hero Food Pics/IMG_0662.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'landscape', 2800, 2100, 17, TRUE),
    ('/Hero Food Pics/IMG_0885.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'square', 2100, 2800, 18, TRUE),
    ('/Hero Food Pics/IMG_0945.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'portrait', 2100, 2800, 19, TRUE),
    ('/Hero Food Pics/IMG_0979.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'landscape', 2100, 2800, 20, TRUE),
    ('/Hero Food Pics/IMG_1008.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'square', 2100, 2800, 21, TRUE),
    ('/Hero Food Pics/IMG_1292.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'portrait', 2100, 2800, 22, TRUE),
    ('/Hero Food Pics/IMG_1304.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'landscape', 2800, 2100, 23, TRUE),
    ('/Hero Food Pics/IMG_1305.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'square', 2100, 2800, 24, TRUE),
    ('/Hero Food Pics/IMG_1306.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'portrait', 2100, 2800, 25, TRUE),
    ('/Hero Food Pics/IMG_1307.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'landscape', 2100, 2800, 26, TRUE),
    ('/Hero Food Pics/IMG_1619.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'square', 2100, 2800, 27, TRUE),
    ('/Oregon Coast & Local Sourcing/IMG_0008.jpg', 'A scenic moment from the Oregon Coast', 'Along the Oregon Coast', 'The coast', 'portrait', 4032, 3024, 28, TRUE),
    ('/Oregon Coast & Local Sourcing/IMG_0053.jpg', 'A scenic moment from the Oregon Coast', 'Along the Oregon Coast', 'The coast', 'landscape', 4284, 5712, 29, TRUE),
    ('/Oregon Coast & Local Sourcing/IMG_0256.jpg', 'A scenic moment from the Oregon Coast', 'Along the Oregon Coast', 'The coast', 'square', 4032, 3024, 30, TRUE),
    ('/Oregon Coast & Local Sourcing/IMG_0263.jpg', 'A scenic moment from the Oregon Coast', 'Along the Oregon Coast', 'The coast', 'portrait', 3846, 5381, 31, TRUE),
    ('/Oregon Coast & Local Sourcing/IMG_0269.jpg', 'A scenic moment from the Oregon Coast', 'Along the Oregon Coast', 'The coast', 'landscape', 3728, 2677, 32, TRUE),
    ('/Oregon Coast & Local Sourcing/IMG_0508.jpg', 'A scenic moment from the Oregon Coast', 'Along the Oregon Coast', 'The coast', 'square', 3022, 3706, 33, TRUE),
    ('/Oregon Coast & Local Sourcing/IMG_0531.jpg', 'A scenic moment from the Oregon Coast', 'Along the Oregon Coast', 'The coast', 'portrait', 5712, 4284, 34, TRUE),
    ('/Oregon Coast & Local Sourcing/IMG_0559.jpg', 'A scenic moment from the Oregon Coast', 'Along the Oregon Coast', 'The coast', 'landscape', 4284, 5712, 35, TRUE),
    ('/Oregon Coast & Local Sourcing/IMG_1869.jpg', 'A scenic moment from the Oregon Coast', 'Along the Oregon Coast', 'The coast', 'square', 3955, 5273, 36, TRUE),
    ('/Oregon Coast & Local Sourcing/IMG_1927.jpg', 'A scenic moment from the Oregon Coast', 'Along the Oregon Coast', 'The coast', 'portrait', 3024, 4032, 37, TRUE),
    ('/Oregon Coast & Local Sourcing/IMG_1932.jpg', 'A scenic moment from the Oregon Coast', 'Along the Oregon Coast', 'The coast', 'landscape', 5712, 4284, 38, TRUE),
    ('/Oregon Coast & Local Sourcing/IMG_2343.jpg', 'A scenic moment from the Oregon Coast', 'Along the Oregon Coast', 'The coast', 'square', 2598, 3464, 39, TRUE),
    ('/Oregon Coast & Local Sourcing/IMG_2380.jpg', 'A scenic moment from the Oregon Coast', 'Along the Oregon Coast', 'The coast', 'portrait', 4284, 5712, 40, TRUE),
    ('/Oregon Coast & Local Sourcing/IMG_2561.jpg', 'A scenic moment from the Oregon Coast', 'Along the Oregon Coast', 'The coast', 'landscape', 5374, 4030, 41, TRUE),
    ('/Oregon Coast & Local Sourcing/IMG_2608.jpg', 'A scenic moment from the Oregon Coast', 'Along the Oregon Coast', 'The coast', 'square', 4284, 5712, 42, TRUE),
    ('/Oregon Coast & Local Sourcing/IMG_2910.jpg', 'A scenic moment from the Oregon Coast', 'Along the Oregon Coast', 'The coast', 'portrait', 4284, 5712, 43, TRUE),
    ('/Oregon Coast & Local Sourcing/IMG_7566.jpg', 'A scenic moment from the Oregon Coast', 'Along the Oregon Coast', 'The coast', 'landscape', 4284, 5712, 44, TRUE),
    ('/Oregon Coast & Local Sourcing/IMG_7851.jpg', 'A scenic moment from the Oregon Coast', 'Along the Oregon Coast', 'The coast', 'square', 3968, 5291, 45, TRUE),
    ('/Oregon Coast & Local Sourcing/IMG_7902.jpg', 'A scenic moment from the Oregon Coast', 'Along the Oregon Coast', 'The coast', 'portrait', 4032, 2268, 46, TRUE),
    ('/Oregon Coast & Local Sourcing/IMG_7981.jpg', 'A scenic moment from the Oregon Coast', 'Along the Oregon Coast', 'The coast', 'landscape', 4284, 5712, 47, TRUE),
    ('/Private Dinners/IMG_0357.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'square', 3024, 4032, 48, TRUE),
    ('/Private Dinners/IMG_0358.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'portrait', 3024, 4032, 49, TRUE),
    ('/Private Dinners/IMG_0360.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'landscape', 3024, 4032, 50, TRUE),
    ('/Private Dinners/IMG_0361.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'square', 3024, 4032, 51, TRUE),
    ('/Private Dinners/IMG_0995.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'portrait', 5712, 4284, 52, TRUE),
    ('/Private Dinners/IMG_0998.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'landscape', 4284, 5712, 53, TRUE),
    ('/Private Dinners/IMG_0999.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'square', 4284, 5712, 54, TRUE),
    ('/Private Dinners/IMG_1001.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'portrait', 5712, 4284, 55, TRUE),
    ('/Private Dinners/IMG_1007.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'landscape', 4284, 5712, 56, TRUE),
    ('/Private Dinners/IMG_1008.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'square', 3024, 4032, 57, TRUE),
    ('/Private Dinners/IMG_1009.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'portrait', 3024, 4032, 58, TRUE),
    ('/Private Dinners/IMG_1011.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'landscape', 5712, 4284, 59, TRUE),
    ('/Private Dinners/IMG_1013.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'square', 4032, 3024, 60, TRUE),
    ('/Private Dinners/IMG_1017.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'portrait', 4284, 5712, 61, TRUE),
    ('/Private Dinners/IMG_1020.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'landscape', 3024, 4032, 62, TRUE),
    ('/Private Dinners/IMG_1873.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'square', 5712, 4284, 63, TRUE),
    ('/Private Dinners/IMG_1876.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'portrait', 3968, 5291, 64, TRUE),
    ('/Private Dinners/IMG_1877.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'landscape', 4284, 5712, 65, TRUE),
    ('/Private Dinners/IMG_1878.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'square', 4284, 5712, 66, TRUE),
    ('/Private Dinners/IMG_1879.jpg', 'A chef-prepared dish for a coastal gathering', 'Fresh from the kitchen', 'At the table', 'portrait', 4284, 5712, 67, TRUE),
    ('/Weddings & Elopements/IMG_0363.jpg', 'A wedding or celebration catered by The Wandering Fork', 'A day worth remembering', 'Gatherings', 'landscape', 2100, 2800, 68, TRUE),
    ('/Weddings & Elopements/IMG_0367.jpg', 'A wedding or celebration catered by The Wandering Fork', 'A day worth remembering', 'Gatherings', 'square', 2800, 2100, 69, TRUE),
    ('/Weddings & Elopements/IMG_0425.jpg', 'A wedding or celebration catered by The Wandering Fork', 'A day worth remembering', 'Gatherings', 'portrait', 2800, 2100, 70, TRUE),
    ('/Weddings & Elopements/IMG_0428.jpg', 'A wedding or celebration catered by The Wandering Fork', 'A day worth remembering', 'Gatherings', 'landscape', 2100, 2800, 71, TRUE),
    ('/Weddings & Elopements/IMG_0430.jpg', 'A wedding or celebration catered by The Wandering Fork', 'A day worth remembering', 'Gatherings', 'square', 2100, 2800, 72, TRUE),
    ('/Weddings & Elopements/IMG_0432.jpg', 'A wedding or celebration catered by The Wandering Fork', 'A day worth remembering', 'Gatherings', 'portrait', 2800, 2100, 73, TRUE),
    ('/Weddings & Elopements/IMG_0434.jpg', 'A wedding or celebration catered by The Wandering Fork', 'A day worth remembering', 'Gatherings', 'landscape', 2100, 2800, 74, TRUE),
    ('/Weddings & Elopements/IMG_0435.jpg', 'A wedding or celebration catered by The Wandering Fork', 'A day worth remembering', 'Gatherings', 'square', 2800, 2100, 75, TRUE),
    ('/Weddings & Elopements/IMG_0436.jpg', 'A wedding or celebration catered by The Wandering Fork', 'A day worth remembering', 'Gatherings', 'portrait', 2100, 2800, 76, TRUE),
    ('/Weddings & Elopements/IMG_0438.jpg', 'A wedding or celebration catered by The Wandering Fork', 'A day worth remembering', 'Gatherings', 'landscape', 2100, 2800, 77, TRUE),
    ('/Weddings & Elopements/IMG_0442.jpg', 'A wedding or celebration catered by The Wandering Fork', 'A day worth remembering', 'Gatherings', 'square', 2100, 2800, 78, TRUE),
    ('/Weddings & Elopements/IMG_0445.jpg', 'A wedding or celebration catered by The Wandering Fork', 'A day worth remembering', 'Gatherings', 'portrait', 2100, 2800, 79, TRUE),
    ('/Weddings & Elopements/IMG_0446.jpg', 'A wedding or celebration catered by The Wandering Fork', 'A day worth remembering', 'Gatherings', 'landscape', 2100, 2800, 80, TRUE),
    ('/Weddings & Elopements/IMG_0451.jpg', 'A wedding or celebration catered by The Wandering Fork', 'A day worth remembering', 'Gatherings', 'square', 2800, 2100, 81, TRUE),
    ('/Weddings & Elopements/IMG_0456.jpg', 'A wedding or celebration catered by The Wandering Fork', 'A day worth remembering', 'Gatherings', 'portrait', 2100, 2800, 82, TRUE),
    ('/Weddings & Elopements/IMG_0460.jpg', 'A wedding or celebration catered by The Wandering Fork', 'A day worth remembering', 'Gatherings', 'landscape', 2100, 2800, 83, TRUE),
    ('/Weddings & Elopements/IMG_0461.jpg', 'A wedding or celebration catered by The Wandering Fork', 'A day worth remembering', 'Gatherings', 'square', 2800, 2100, 84, TRUE),
    ('/Weddings & Elopements/IMG_0464.jpg', 'A wedding or celebration catered by The Wandering Fork', 'A day worth remembering', 'Gatherings', 'portrait', 2100, 2800, 85, TRUE),
    ('/Weddings & Elopements/IMG_0466.jpg', 'A wedding or celebration catered by The Wandering Fork', 'A day worth remembering', 'Gatherings', 'landscape', 2100, 2800, 86, TRUE),
    ('/Weddings & Elopements/IMG_0660.jpg', 'A wedding or celebration catered by The Wandering Fork', 'A day worth remembering', 'Gatherings', 'square', 2800, 2100, 87, TRUE),
    ('/Private Dinners/IMG_1881.jpg', 'A closer look at food and details from a Wandering Fork gathering', 'Details from a gathering', 'Behind the scenes', 'landscape', 3894, 5192, 88, TRUE),
    ('/Private Dinners/IMG_1887.jpg', 'A closer look at food and details from a Wandering Fork gathering', 'Details from a gathering', 'Behind the scenes', 'landscape', 4024, 5365, 89, TRUE),
    ('/Private Dinners/IMG_1899.jpg', 'A closer look at food and details from a Wandering Fork gathering', 'Details from a gathering', 'Behind the scenes', 'landscape', 5712, 4284, 90, TRUE),
    ('/Private Dinners/IMG_1901.jpg', 'A closer look at food and details from a Wandering Fork gathering', 'Details from a gathering', 'Behind the scenes', 'landscape', 4284, 5712, 91, TRUE),
    ('/Private Dinners/IMG_1906.jpg', 'A closer look at food and details from a Wandering Fork gathering', 'Details from a gathering', 'Behind the scenes', 'landscape', 4284, 5712, 92, TRUE),
    ('/Private Dinners/IMG_1909.jpg', 'A closer look at food and details from a Wandering Fork gathering', 'Details from a gathering', 'Behind the scenes', 'landscape', 5712, 4284, 93, TRUE),
    ('/Private Dinners/IMG_1913.jpg', 'A closer look at food and details from a Wandering Fork gathering', 'Details from a gathering', 'Behind the scenes', 'landscape', 5712, 4284, 94, TRUE),
    ('/Private Dinners/IMG_1918.jpg', 'A closer look at food and details from a Wandering Fork gathering', 'Details from a gathering', 'Behind the scenes', 'landscape', 4284, 5712, 95, TRUE),
    ('/Weddings & Elopements/IMG_0667.jpg', 'A closer look at food and details from a Wandering Fork gathering', 'Details from a gathering', 'Behind the scenes', 'landscape', 2100, 2800, 96, TRUE),
    ('/Weddings & Elopements/IMG_0675.jpg', 'A closer look at food and details from a Wandering Fork gathering', 'Details from a gathering', 'Behind the scenes', 'landscape', 2100, 2800, 97, TRUE),
    ('/Weddings & Elopements/IMG_0683.jpg', 'A closer look at food and details from a Wandering Fork gathering', 'Details from a gathering', 'Behind the scenes', 'landscape', 2100, 2800, 98, TRUE),
    ('/Weddings & Elopements/IMG_0692.jpg', 'A closer look at food and details from a Wandering Fork gathering', 'Details from a gathering', 'Behind the scenes', 'landscape', 2100, 2800, 99, TRUE),
    ('/Weddings & Elopements/IMG_2886.jpg', 'A closer look at food and details from a Wandering Fork gathering', 'Details from a gathering', 'Behind the scenes', 'landscape', 2100, 2800, 100, TRUE),
    ('/Weddings & Elopements/IMG_2889.jpg', 'A closer look at food and details from a Wandering Fork gathering', 'Details from a gathering', 'Behind the scenes', 'landscape', 2100, 2800, 101, TRUE),
    ('/Weddings & Elopements/IMG_2891.jpg', 'A closer look at food and details from a Wandering Fork gathering', 'Details from a gathering', 'Behind the scenes', 'landscape', 2100, 2800, 102, TRUE)

ON CONFLICT DO NOTHING;

-- ---------------------------------------------------------------------------
-- Inquiries (submissions from the "Start planning" form)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS inquiries (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(255),
    event_date DATE,
    guest_count INT,
    location VARCHAR(255),
    details TEXT,
    status VARCHAR(32) NOT NULL DEFAULT 'new',
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_inquiries_status ON inquiries (status);
CREATE INDEX IF NOT EXISTS idx_inquiries_created ON inquiries (created_at DESC);
