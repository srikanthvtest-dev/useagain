# UseAgain — Complete Application

Everything is now one working codebase. This replaces all the separate module
zips — you don't need to combine anything by hand anymore.

## What's included and working end-to-end

- **Homepage** (`index.php`) — hero, category cards, featured/latest products, stats, how it works, testimonials, donate banner, floating WhatsApp button
- **Register / Login / Logout** — full user auth with CSRF, honeypot spam protection, rate limiting
- **My Account** (`my-account.php`) — profile info + all of the user's own listings with status
- **Post a Free Ad** (`post-product.php`) — sell/donate toggle, category→subcategory cascading dropdown, up to 10 photos (drag-and-drop, live preview), full validation
- **Browse / Search** (`products.php`) — filter by category, subcategory, listing type, condition, price range, city; sort by newest/price/views; pagination
- **Product Details** (`product-details.php`) — gallery, WhatsApp + call buttons, seller/location info, similar products
- **Admin Login + Dashboard** (`admin/login.php`, `admin/dashboard.php`) — stats, recent pending listings, recent users
- **Admin Product Moderation** (`admin/products.php`) — filter by status, approve, reject (with reason), delete
- **Contact form** → saved to `contact_messages`
- **About page**

Every image uploaded through "Post a Free Ad" is validated (real MIME-type check, not just the filename), resized to a sane max dimension, and stored under `uploads/products/YYYY/MM/` — matching the schema exactly.

## What's referenced but not yet built

The admin sidebar links to a few pages that don't exist yet — clicking them will 404:
`admin/categories.php`, `admin/users.php`, `admin/testimonials.php`, `admin/messages.php`,
`admin/homepage-settings.php`, `admin/settings.php`. These are all "nice to have" admin
management screens — the core buy/sell/donate/approve loop works completely without them.
Say the word and I'll build any of these next.

---

## Installation (GoDaddy shared hosting or local)

### 1. Upload the files
Upload everything so `index.php` sits directly in `public_html` (or your local
web root), keeping the folder structure intact.

### 2. Create the database
cPanel → MySQL Databases → create a database + user → add the user to the
database with **All Privileges**. Then cPanel → phpMyAdmin → select your
database → **Import** → choose `database/schema.sql` → Go.

### 3. Configure the connection
Edit `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'yourcpaneluser_useagain');
define('DB_USER', 'yourcpaneluser_dbuser');
define('DB_PASS', 'your-password');
```

### 4. Configure site settings
Edit `config/config.php` — update `CONTACT_EMAIL`, `WHATSAPP_NUMBER`,
`CONTACT_PHONE_DISPLAY`. Set `APP_DEBUG` to `false` once everything works.

### 5. Create your admin account
Visit `database/create-admin.php` in your browser **once** — it refuses to
run again after the first admin exists, so there's no risk of it being
abused later. Fill in your name/email/password, then:
- **Delete `database/create-admin.php` from the server.**
- Log in at `/admin/login.php`.

### 6. File permissions
`uploads/` needs to be writable (755 is usually fine on GoDaddy) — the
`YYYY/MM` subfolders are created automatically the first time someone
posts a listing with photos.

### 7. Test the full flow
1. Register a user account at `/register.php`.
2. Post an ad at `/post-product.php` with at least one photo.
3. Log into `/admin/login.php` → **Products** → find your pending listing → **Approve**.
4. Visit `/products.php` — your listing should now appear.
5. Open it and confirm the WhatsApp button builds a working `wa.me` link.

---

## Folder structure

```
useagain/
├── .htaccess
├── config/                 config.php (site constants + BASE_URL), database.php (PDO)
├── includes/
│   ├── functions.php        All data-access + auth + security helpers (single source of truth)
│   ├── image-upload.php     Product photo validation, resizing, storage
│   ├── header.php / footer.php / navbar.php
│   └── product-card.php     Reusable product card partial
├── database/
│   ├── schema.sql
│   └── create-admin.php     ⚠️ Delete after first use
├── assets/{css,js,images}/
├── uploads/products/YYYY/MM/   Created automatically at runtime
├── admin/
│   ├── login.php / logout.php / dashboard.php / products.php
│   └── includes/admin-header.php, admin-sidebar.php, admin-footer.php
├── index.php, register.php, login.php, logout.php, my-account.php
├── post-product.php, products.php, product-details.php
├── ajax-subcategories.php   AJAX endpoint for the category→subcategory dropdown
├── about.php, contact.php
```
