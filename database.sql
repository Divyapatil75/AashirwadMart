CREATE DATABASE IF NOT EXISTS aashirwad_mart_combined CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE aashirwad_mart_combined;

CREATE TABLE IF NOT EXISTS users (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(120) NOT NULL, email VARCHAR(190) NOT NULL UNIQUE,
 phone VARCHAR(20) NOT NULL, password_hash VARCHAR(255) NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS admins (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(120) NOT NULL, email VARCHAR(190) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL DEFAULT 1, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS categories (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL UNIQUE, slug VARCHAR(120) NOT NULL UNIQUE,
 description VARCHAR(255) NULL, image VARCHAR(255) NULL, is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS products (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, category_id INT UNSIGNED NOT NULL, name VARCHAR(180) NOT NULL,
 description TEXT NULL, price DECIMAL(10,2) NOT NULL, stock INT UNSIGNED NOT NULL DEFAULT 0, image VARCHAR(255) NULL,
 is_active TINYINT(1) NOT NULL DEFAULT 1, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_product_category FOREIGN KEY(category_id) REFERENCES categories(id)
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS cart_items (
 user_id INT UNSIGNED NOT NULL, product_id INT UNSIGNED NOT NULL, quantity INT UNSIGNED NOT NULL DEFAULT 1,
 PRIMARY KEY(user_id,product_id), FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
 FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS wishlist_items (
 user_id INT UNSIGNED NOT NULL, product_id INT UNSIGNED NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY(user_id,product_id), FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
 FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS delivery_pincodes (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, pincode CHAR(6) NOT NULL UNIQUE, area VARCHAR(180) NULL,
 is_active TINYINT(1) NOT NULL DEFAULT 1, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS orders (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, order_number VARCHAR(32) NOT NULL UNIQUE, user_id INT UNSIGNED NOT NULL,
 customer_name VARCHAR(120) NOT NULL, phone VARCHAR(20) NOT NULL, address VARCHAR(500) NOT NULL, pincode CHAR(6) NOT NULL,
 subtotal DECIMAL(10,2) NOT NULL, delivery_charge DECIMAL(10,2) NOT NULL, total DECIMAL(10,2) NOT NULL,
 payment_method ENUM('cod','upi') NOT NULL, payment_status ENUM('not_required','pending','verified','rejected') NOT NULL DEFAULT 'not_required',
 payment_reference VARCHAR(120) NULL, payment_proof VARCHAR(255) NULL,
 status ENUM('awaiting_payment_review','placed','confirmed','packed','out_for_delivery','delivered','cancelled','payment_rejected') NOT NULL,
 admin_note VARCHAR(500) NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 FOREIGN KEY(user_id) REFERENCES users(id)
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS order_items (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, order_id BIGINT UNSIGNED NOT NULL, product_id INT UNSIGNED NULL,
 product_name VARCHAR(180) NOT NULL, unit_price DECIMAL(10,2) NOT NULL, quantity INT UNSIGNED NOT NULL,
 FOREIGN KEY(order_id) REFERENCES orders(id) ON DELETE CASCADE, FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS payment_submissions (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, order_id BIGINT UNSIGNED NOT NULL, user_id INT UNSIGNED NOT NULL,
 reference_number VARCHAR(120) NOT NULL, proof_file VARCHAR(255) NULL, status ENUM('pending','verified','rejected') NOT NULL DEFAULT 'pending',
 reviewed_by INT UNSIGNED NULL, review_note VARCHAR(500) NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 reviewed_at DATETIME NULL, FOREIGN KEY(order_id) REFERENCES orders(id) ON DELETE CASCADE,
 FOREIGN KEY(user_id) REFERENCES users(id), FOREIGN KEY(reviewed_by) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT IGNORE INTO categories(name,slug,description) VALUES
('Groceries','groceries','Everyday kitchen essentials'),
('Stationery','stationery','School and office supplies'),
('Electronics','electronics','Useful electronic essentials'),
('Makeup','makeup','Beauty and makeup essentials'),
('Phone Accessories','phone-accessories','Accessories for your phone');

INSERT IGNORE INTO delivery_pincodes(pincode,area,is_active) VALUES ('431005','Approved delivery area',1);


CREATE TABLE IF NOT EXISTS contact_messages (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id INT UNSIGNED NULL,
 name VARCHAR(120) NOT NULL,
 email VARCHAR(190) NOT NULL,
 message TEXT NOT NULL,
 status ENUM('new','read','replied') NOT NULL DEFAULT 'new',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Product catalogue migrated from the previous Aashirwad Mart website.
-- The real product images are included in assets/images/.
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Ceiling Fan','Reliable ceiling fan for home use.',2000,15,'fan.jpg' FROM categories WHERE slug='electronics' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Ceiling Fan');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Boards','Electrical boards and fittings.',250,30,'board.jpg' FROM categories WHERE slug='electronics' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Boards');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Induction','Compact induction cooking unit.',1200,10,'induction.jpg' FROM categories WHERE slug='electronics' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Induction');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Bulbs','LED bulbs. Price can vary by wattage.',90,40,'bulps.jpg' FROM categories WHERE slug='electronics' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Bulbs');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Press','Electric clothes press/iron.',700,12,'press.jpg' FROM categories WHERE slug='electronics' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Press');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Project Kit','Basic project kit for school activities.',50,30,'project kit.jpg' FROM categories WHERE slug='electronics' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Project Kit');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Headphones','Everyday wired headphones.',70,25,'headphones.jpg' FROM categories WHERE slug='electronics' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Headphones');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Mixer Grinder','Mixer grinder for kitchen use.',200,8,'mixer.jpg' FROM categories WHERE slug='electronics' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Mixer Grinder');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Unomax Pen','Smooth-writing everyday pen.',20,60,'unomax pen.jpg' FROM categories WHERE slug='stationery' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Unomax Pen');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Finegrip Pen','Comfort grip writing pen.',10,80,'finegrip pen.jpg' FROM categories WHERE slug='stationery' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Finegrip Pen');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Butterflow Pen','Smooth-flow writing pen.',10,80,'buterflow pen.jpg' FROM categories WHERE slug='stationery' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Butterflow Pen');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Single Line Notebook','Single-line notebook.',45,50,'sinle line notebook.jpg' FROM categories WHERE slug='stationery' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Single Line Notebook');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'2 Line Notebook','Two-line notebook for practice.',45,50,'2line notebook.jpg' FROM categories WHERE slug='stationery' AND NOT EXISTS (SELECT 1 FROM products WHERE name='2 Line Notebook');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Notebook','General purpose notebook.',50,45,'notebook.jpeg' FROM categories WHERE slug='stationery' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Notebook');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Box Notebook','Box-pattern cover notebook.',70,35,'box notebook.jpg' FROM categories WHERE slug='stationery' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Box Notebook');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Nataraj Pencil','Classic graphite pencil.',10,100,'nataraj.png' FROM categories WHERE slug='stationery' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Nataraj Pencil');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Apsara Pencil','Smooth writing pencil.',10,100,'apsara.jpg' FROM categories WHERE slug='stationery' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Apsara Pencil');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Drawing Book A4','A4 drawing book.',35,40,'drawing book A4.jpg' FROM categories WHERE slug='stationery' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Drawing Book A4');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Big Drawing Book','Large drawing book for art practice.',55,30,'big drawing book.jpg' FROM categories WHERE slug='stationery' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Big Drawing Book');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Sketch Pen','Sketch pen set.',60,25,'sketch pen.jpg' FROM categories WHERE slug='stationery' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Sketch Pen');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Face Primer','Makeup primer.',400,15,'primer.jpg' FROM categories WHERE slug='makeup' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Face Primer');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Choker','Fashion choker accessory.',200,20,'choker.jpg' FROM categories WHERE slug='makeup' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Choker');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Foundation','Everyday foundation.',200,20,'foundation.jpg' FROM categories WHERE slug='makeup' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Foundation');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Blush','Face blush.',200,18,'blush.jpg' FROM categories WHERE slug='makeup' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Blush');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Compact Powder','Compact face powder.',145,25,'compact.jpg' FROM categories WHERE slug='makeup' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Compact Powder');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Lipstick','Everyday lipstick.',100,30,'lipstick.jpg' FROM categories WHERE slug='makeup' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Lipstick');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Clutures','Hair clutches. Sold by dozen.',120,20,'clutures.webp' FROM categories WHERE slug='makeup' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Clutures');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Bindi','Bindi pack.',10,50,'bindi.jpg' FROM categories WHERE slug='makeup' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Bindi');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Bracelates','Fashion bracelet.',100,20,'bracelates.jpg' FROM categories WHERE slug='makeup' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Bracelates');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Pair Bracelate','Pair of bracelets.',100,15,'couple bracelate.jpg' FROM categories WHERE slug='makeup' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Pair Bracelate');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Small Earrings','Small earrings.',120,15,'buds.jpg' FROM categories WHERE slug='makeup' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Small Earrings');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Nosepin','Fashion nose pin.',30,20,'nosepin.jpg' FROM categories WHERE slug='makeup' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Nosepin');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Peanuts','Roasted/raw peanuts.',60,40,'peanuts.jpg' FROM categories WHERE slug='groceries' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Peanuts');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Mung Daal','Mung dal.',120,40,'mung daal.jpg' FROM categories WHERE slug='groceries' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Mung Daal');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Chana Daal','Chana dal.',100,40,'chana daal.jpg' FROM categories WHERE slug='groceries' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Chana Daal');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Udad Daal','Urad dal.',130,40,'udad daal.jpg' FROM categories WHERE slug='groceries' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Udad Daal');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Mhasur Daal','Masoor dal.',120,40,'mhasur daal.jpg' FROM categories WHERE slug='groceries' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Mhasur Daal');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Rice Bag','Rice bag.',200,30,'ricebag.jpg' FROM categories WHERE slug='groceries' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Rice Bag');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Mung','Whole green gram.',110,40,'mung.jpg' FROM categories WHERE slug='groceries' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Mung');
INSERT INTO products(category_id,name,description,price,stock,image) SELECT id,'Sugar','Sugar.',50,50,'sugar.jpg' FROM categories WHERE slug='groceries' AND NOT EXISTS (SELECT 1 FROM products WHERE name='Sugar');
