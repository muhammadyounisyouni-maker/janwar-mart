# Janwar Mart: Python -> Laravel migration spec (handoff)

Source project: `janwar-mart.zip` (Python `server.py` + SQLite + vanilla-JS app). **Never modify it.** Build the Laravel project in a NEW folder `janwar-mart-laravel/`.
Target: PHP 8.3, latest stable Laravel, MySQL, Eloquent, Blade + Bootstrap 5.3, Laravel auth, REST API where needed.

## 0. Decisions already made by the owner
| Question | Answer |
|---|---|
| Install | Owner enables package installs, then starts a NEW chat with this file + `janwar-mart.zip` attached. Then create the real project with composer and **run the tests**. |
| Frontend | **Blade + Bootstrap 5**, every page a Blade view, forms post to controllers (small JS only for enhancements). Keep the original identity (colours, fonts, Urdu RTL + English, light/dark, responsive). |
| Categories | **Keep as original**: `categories` table seeded with cow, buffalo, goat, sheep; breed stays free text; **no subcategories**. |
| Admin login | **Admin accounts with roles**: `users.role = admin`, `admin` middleware, first admin from `.env` seeder; admin actions logged with the admin's user id. |
| Rules | Do not change business logic. Do not remove features. Ask before assuming anything unclear. Test every page/button/form and fix all errors. |

## 1. Design tokens to keep (from `static/style.css`)
Primary green `#216B45` (dark `#164B33`), gold `#F2B705`, background `#F8FAF6`, error `#B42318`; dark mode bg `#121a14`, card `#1b261e`, border `#2c3b30`, primary `#3d9a69`.
Fonts: Inter (UI), Playfair Display (headings), Noto Nastaliq Urdu (Urdu). Default language Urdu (RTL); English (LTR) toggle; theme toggle light/dark saved per user/browser. Animal cut-out images and logo are embedded in `index.html` / `style.css` (base64): extract to `public/img/`.

## 2. Database (migrate SQLite -> MySQL). Keep original numeric ids so public refs stay identical.
Refs: listing `JM-(1100+id)`, order `RQ-(2000+id)`, doctor `DR-(3000+id)`, consultation `CN-(4000+id)`.

| Laravel table | Columns (from server.py) | Notes |
|---|---|---|
| `users` | id, name, phone (unique), email (unique, nullable), password, role (`buyer`/`seller`/`doctor`/`admin`), city, plan (`basic`/`silver`/`gold`), status (`active`/`blocked`), last_login_at, login_count, legacy_pw (nullable), timestamps | `legacy_pw` = old `salt:scrypt` hash; on first login verify with `App\Support\LegacyScrypt` (provided + tested), then store bcrypt and null `legacy_pw`. |
| `categories` | id, key (`cow`,`buffalo`,`goat`,`sheep`), name_ur, name_en, icon | seeded; no subcategories. |
| `listings` | id, title, category_id, price (int), city, age, breed, weight, descr, status (`pending`,`active`,`reserved`,`sold`,`rejected`,`removed`), verified (bool), owner_id -> users, owner_name, contact, approved_at, sold_at, buyer_name, buyer_phone, sold_price, timestamps | `images` JSON -> table below. |
| `listing_images` | id, listing_id, path, position | max 4 per listing, public disk. |
| `orders` | id, buyer_id -> users, name, phone, city, note, admin_present (bool), total, status (`requested`,`contacted`,`completed`,`cancelled`), pay_method (`jazz`,`easy`,`bank`,`cod`), pay_tid, pay_status (`unpaid`,`submitted`,`confirmed`,`rejected`,`refund`,`cod`), completed_at, timestamps | `items` JSON -> `order_items`. |
| `order_items` | order_id, listing_id, price | |
| `order_meetings` | order_id, meet_at (text/datetime), place, admin_name | **browser-only today**, now persisted (admin sets after "admin present"). |
| `disputes` | id, order_id, user_id, reason (min 10 chars), status (`open`,`resolved`), resolution, resolved_at, timestamps | **browser-only today** (complaint/refund after completed order). |
| `ad_reports` | id, listing_id, user_id nullable, reason, status (`open`,`resolved`), timestamps | **browser-only today** ("report this ad"). |
| `contact_messages` | id, type (`ad_issue`,`seller_complaint`,`account_login`,`other`), ad_ref nullable, message, name, phone, status (`open`,`resolved`), timestamps | **browser-only today** (Contact Admin form: issue type, ad reference, message, name, phone). |
| `favorites` | user_id, listing_id, timestamps, PK (user_id, listing_id) | |
| `otps` | phone, purpose (`signup`/`reset`), code_hash, expires_at, attempts, sent_at, PK (phone, purpose) | |
| `doctors` | id, user_id (unique) -> users, name, phone, wa, email, qual, uni, reg, exp, clinic, city, addr, photo, cert_name, cert_path, lic_name, lic_path, consent, status (`pending`,`review`,`verified`,`rejected`,`suspended`), note, timestamps | cert/lic on **private** disk, served only to admins. |
| `doctor_status_histories` | doctor_id, status, note, created_at | replaces `hist` JSON. |
| `consultations` | id, doctor_id, user_id, animal, text, photo1, photo2, name, phone, status (`new`,`answered`), reply, replied_at, timestamps | |
| `login_events` | id, user_id nullable, who, event (`signup`,`login`,`login_failed`,`password_reset`,`admin_login`,`admin_login_failed`), ip, ua, created_at | |
| `admin_notifications` | id, icon, text, read (bool), created_at | |
| `audit_logs` | id, admin_id, action, created_at | |
| `settings` | key (PK), value (JSON) | `pay_accounts`. The old `admin_pw` row is NOT imported (admin = user account). |

Artisan command `php artisan janwar:import-legacy {sqlite-path} {--uploads=path}`: read-only on the SQLite file, copies rows in a transaction preserving ids, maps `listings.cat` -> category, splits `items`/`images`/`hist` JSON, moves `data/uploads/*` to `storage/app/public/listings`, `data/private/*` to private disk. Never deletes anything.

## 3. Business rules (do not change)
- **Phone** regex `^(03\d{9}|\+923\d{9})$`, stored normalised `03xxxxxxxxx`. Email optional, unique. Password min 6. Roles at signup: buyer, seller, doctor. Cities: lhr Lahore, mul Multan, fsd Faisalabad, khi Karachi, isb Islamabad.
- **OTP**: signup needs it; password reset needs it. 6 digits, valid 5 min, single use, 5 wrong tries then locked, resend cooldown 60 s, max 3 per phone per 15 min, 10 per IP per hour. Reset for an unknown phone returns the same answer (no phone enumeration). SMS provider from `.env`: `console` (log), `twilio`, `webhook`.
- **Login**: phone OR email + password; 10 failures per 15 min per IP+id -> locked. Blocked users cannot log in. Password reset logs the user out everywhere. Every signup/login/failure/reset is written to `login_events` (IP + user agent).
- **Plans**: ad limit counts `active` + `pending` listings: basic 3, silver 10, gold unlimited. Prices shown: silver Rs 1,500/month, gold Rs 3,000/month. User requests a plan -> admin notification; admin sets the plan in Users. Over the limit -> error "plan limit reached".
- **Listing**: must be logged in; category valid; price 1..100,000,000; city; 1..4 images (jpeg/png/webp, max 3 MB each, verify real MIME); valid contact phone; starts `pending`. Admin: approve (-> `active`, `verified=1`, `approved_at`), reject, give/remove verified badge, hide (-> `pending`), remove. Owner may delete own ad while pending/active/rejected (-> `removed`). Public lists show only `active`. Sorted newest approved first.
- **Browse**: search (text, category names ur/en, city, breed), category chips, city filter, min/max price, sort (newest / lowest / highest), "load more", result count, details dialog/page with call (`tel:`) and WhatsApp (`wa.me`) links, favourites heart, add to cart, buy now, "report this ad".
- **Cart**: per visitor (session). Only `active` animals. 3 steps: cart, your details + payment, admin confirmation.
- **Order**: >=1 item, all `active`; cannot buy your own animal; payment method must be available (account filled in by admin, or COD enabled). Creates order, items become `reserved`, admin notification. **COD**: no TID, `pay_status=cod`. Otherwise `submitted` if TID given else `unpaid`. Optional "admin present" tick.
- **Admin order actions**: contacted; payment received (COD: "cash received") -> `pay_status=confirmed`; payment rejected; **complete** only when `pay_status=confirmed` -> listings `sold` (`sold_at`, buyer name/phone, `sold_price`), order `completed`; **cancel** -> listings back to `active`, order `cancelled`, `pay_status=refund` if it was confirmed/submitted. Set meeting (date/time, place, admin name). Resolve disputes.
- **Payment accounts** (admin edits, never hard-coded): JazzCash {title,no}, Easypaisa {title,no}, Bank {bank,title,iban (upper-case, no spaces)}, `cod` on/off. A method with an empty account cannot be chosen. Only logged-in users can read them.
- **Doctors**: user registers (name, phone, WhatsApp, email opt., qualification, university, registration no., years 0..60, clinic, city, address, photo, certificate, licence, consent). Status flow: pending -> review/verified/rejected/suspended. Admin actions: review, approve, reject (reason required), suspend (reason required), ask for more documents (reason required -> `review`), reinstate. Only `verified` + consent appear publicly (public data excludes registration no., email, documents). A doctor can re-register after rejected/review; a doctor can never approve themselves. Documents: private disk, admin-only.
- **Consultations**: logged in; verified doctor only; text min 10 chars; <=2 photos; cannot consult yourself; max 10 per hour per user. Only the assigned verified doctor can reply. Buyer sees reply; admin sees all.
- **Favourites**: per user; guest favourites merged on login.
- **Admin dashboard tabs**: Overview (stats), Users (plan change, block/unblock), Login activity, Ads, Purchase requests, Vet doctors, Consultations, Payments & COD, Sold/Bought report, Notifications **+ new**: Contact messages, Ad reports, Disputes/Meetings. Stats: members by role, logins/failed today, ads pending/active/reserved/sold/listed, orders, open orders, sales value, doctors pending/verified, COD orders, favourites, per-category listed/sold.
- **Notifications to admin** on: signup, new ad, new order, plan request, doctor registration, consultation, contact message, report, dispute.
- **PWA**: manifest, icons (`static/icons`), service worker `static/sw.js` (never cache admin, API except public lists, or user data), install button in menu. Port to `public/`.
- **Security headers**: nosniff, X-Frame-Options DENY, Referrer-Policy same-origin, HSTS over HTTPS.

## 4. Routes to build
Web (Blade): `/`, `/animals`, `/animals/{ref}`, `/sell` (6-step wizard), `/my-ads`, `/cart`, `/checkout`, `/orders`, `/favorites`, `/doctors`, `/doctor/register`, `/doctor/dashboard`, `/profile`, `/plans`, `/help`, `/contact`, `/login`, `/register`, `/password/reset`, `/lang/{ur|en}`, `/admin/*` (all tabs).
API (`/api/v1`, Sanctum or session): `GET /listings`, `GET /doctors`, favourites, `POST /otp/send`, orders, consultations (same JSON shapes as the old `server.py` where possible).
Old -> new mapping of all 38 old routes is in `server.py` (`@route(...)` lines).

## 5. Structure
`app/Models`, `app/Http/Controllers` (+ `Admin/`), `app/Http/Middleware` (`EnsureAdmin`, `EnsureNotBlocked`, `SetLocale`, `SecurityHeaders`), `app/Http/Requests`, `app/Http/Resources`, `app/Services` (`OtpService`, `SmsService`, `CartService`, `OrderService`, `ImageService`, `PlanService`), `app/Support/LegacyScrypt.php` (provided), `app/Console/Commands/ImportLegacy.php`, `database/migrations`, `database/seeders` (categories, first admin from `.env`), `resources/views`, `public/`, `tests/Feature`.

## 6. `.env` keys
`APP_NAME="Janwar Mart"`, `APP_URL`, `DB_CONNECTION=mysql`, `DB_HOST`, `DB_DATABASE=janwar_mart`, `DB_USERNAME`, `DB_PASSWORD`, `ADMIN_NAME`, `ADMIN_PHONE`, `ADMIN_EMAIL`, `ADMIN_PASSWORD` (seeder refuses weak passwords: 12+ chars, upper/lower/digit/symbol), `SMS_PROVIDER` (`console`/`twilio`/`webhook`), `TWILIO_SID`, `TWILIO_TOKEN`, `TWILIO_FROM`, `SMS_WEBHOOK_URL`, `SMS_WEBHOOK_TOKEN`, `FILESYSTEM_DISK=public`.

## 7. Testing plan (write as Laravel Feature tests, then run; report in `TESTING_REPORT.md`)
Auth (signup+OTP, wrong/expired/reused OTP, lockout, login by phone/email, legacy-password login then bcrypt upgrade, reset + sessions revoked, blocked user), listings (create, plan limit, validation, image rules, approve/reject/verify/hide/remove, owner delete), browse (search, filters, sort, pagination), favourites, cart, checkout for all 4 methods incl. COD and unavailable method, order actions (paid -> complete, complete without payment refused, cancel -> refund), meeting/dispute, doctors (register, private docs not public, every admin action, self-approval impossible), consultations (rules above), admin middleware (non-admin -> 403 on every admin route), CSRF, XSS escaping, upload safety, import command (counts match, original SQLite unchanged), responsive pages (browser test at 360/768/1280 px), RTL/LTR, dark mode, PWA offline. Note: MySQL is not installed in the Claude workspace, so tests there run on SQLite; also run migrations once against real MySQL on your PC.

## 8. Files in this handoff
- `app-support/LegacyScrypt.php`: pure-PHP scrypt that verifies old Python password hashes (tested against hashes made by the real `server.py` `hash_pw`: correct passwords pass, wrong ones are rejected, ~2 s per check, 22 MB).
