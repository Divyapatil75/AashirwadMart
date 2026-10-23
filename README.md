# Aashirwad Mart — PHP/MySQL e-commerce starter

A responsive XAMPP-ready store with customer registration/login, separate admins table, categories, product management, cart, wishlist, checkout, approved PIN-code delivery, COD, UPI QR payment submission, manual admin review, order tracking, and a basic FAQ chatbot.

## Local setup (Windows / XAMPP)

1. Install and start **Apache** and **MySQL** in XAMPP.
2. Extract this folder to `C:\xampp\htdocs\AashirwadMart_Combined\`. Keep the folder name `AashirwadMart_Combined`, because `BASE_URL` in `config.php` assumes this.
3. Open phpMyAdmin at `http://localhost/phpmyadmin/`.
4. Import `database.sql`. It creates the separate `aashirwad_mart_combined` database, tables, five categories, the previous website’s 40-product catalog, and delivery PIN `431005`.
5. Check `config.php`: default XAMPP DB user is `root` with a blank password. Update it if your local MySQL credentials differ.
6. The previous website’s `QR.jpeg` was copied to `assets/images/` and configured for the UPI payment page. Confirm it is your correct merchant QR before accepting payments.
7. Visit `http://localhost/AashirwadMart_Combined/setup_admin.php` and create the first admin with a unique email and a strong password (12+ characters). Then **delete `setup_admin.php`**.
8. Visit `http://localhost/AashirwadMart_Combined/` to shop, register a customer, and test the site. Admin login: `http://localhost/AashirwadMart_Combined/admin/login.php`.

## Combined features

This package combines the previous site’s product images/catalog and adds contact messages and category management to the newer separate-admin, approved-PIN, UPI-review architecture. The combined site uses a new database name and folder so it does not overwrite the older website.

## Included defaults

- Business phone: `8329513245`
- Business email: `admin@ashirwadmart.com`
- Business address: `Aashirwad Mart, Main Road, Hindustan Awas`
- Approved delivery PIN: `431005` (editable in Admin → Delivery PINs)
- Delivery fee: ₹30 for orders below ₹499; free at ₹499 or above. Change `delivery_charge()` in `includes/functions.php` to adjust.
- Currency: INR (`₹`).
- Chatbot: simple local FAQ matching; it is not an AI chatbot and does not send messages to an external service.

## How online payment review works

The customer scans the genuine QR, pays in their UPI app, and submits the UPI reference number (optional screenshot). The order remains `awaiting_payment_review` and payment `pending` until the admin checks the merchant's actual transaction records and clicks **Verify payment**. A screenshot/reference is not proof of settled funds by itself. Never approve solely because the customer uploaded an image.

The QR file was carried over from the previous website. Before using it for payments, verify that it is the correct merchant QR and that the displayed merchant details match your account. Do not use a personal/incorrect QR in production. A production payment gateway/webhook is more reliable than manual review.

## Combined product images and catalog

The previous website’s product images and its existing product catalog have been included in this combined package. New image files should be placed in `assets/images/` and enter only the filename in Admin → Products. If no file is present, a local SVG placeholder appears. The catalog and prices were copied from the previous website; check and update all names, prices, stock, and descriptions before accepting real orders.

## Production readiness checklist

This is a **starter implementation**, not a substitute for a security review before a real customer launch.

- Use HTTPS and a real domain.
- Do not deploy with default/blank DB credentials. Create a restricted MySQL account and use environment variables/secrets for DB credentials.
- Set secure session cookie flags in production (`Secure`, `HttpOnly`, `SameSite`) and review CSRF/session handling.
- Delete `setup_admin.php` immediately after first admin setup.
- Restrict uploaded proof access; consider storing proof outside the public web root and serving it only to authorized admins.
- Configure automated database backups, monitoring, error logging, privacy policy, terms, shipping/returns/refunds, and contact consent.
- Add rate limiting, email verification/password reset, admin audit logs, inventory reservation/expiry handling, and stronger order cancellation/refund rules before public launch.
- Validate all product images and business claims. The starter has no real payment-gateway integration and no email/SMS notifications.

## Google Cloud deployment outline

The PHP app can be containerized and deployed to Cloud Run, with Cloud SQL for MySQL and Secret Manager for credentials. Before deploying:
1. Set the app's base URL for the deployed domain (typically `BASE_URL = ''` when hosted at the domain root).
2. Move database credentials to environment variables/secrets and update `db.php` to read them.
3. Configure Cloud SQL connectivity, migrations/import, writable uploads or (preferably) Cloud Storage for uploaded images/proofs, and a production session strategy. Cloud Run instances are ephemeral; do not rely on local disk for durable uploads or sessions.
4. Configure HTTPS, domain mapping, backups, IAM, logs and billing alerts.
5. Test checkout, stock races, payment review, authorization, and recovery in a staging environment before accepting real orders.

For a real client launch, ask a PHP/cloud developer to review security, privacy, tax and payment obligations before accepting customer data or payments.
