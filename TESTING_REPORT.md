# Testing report — please read

**Honest status:** this Laravel project was written in a workspace that had **no internet** (no Composer, no MySQL). So Laravel itself could not be installed there and the application **has not been run end-to-end yet**. What was and was not verified:

## Verified in the workspace
| Check | Result |
|---|---|
| `php -l` syntax check of every PHP file (controllers, models, services, migrations, seeders, routes, tests, config) | all pass |
| Blade templates: matching `@if/@foreach/@forelse/@auth` blocks in every view | all balanced |
| Old Python password hashes verified by `App\Support\LegacyScrypt` (correct password accepted, wrong rejected, 4/4 samples + the one used in the test-suite) | pass |
| Every text key used in the views has an Urdu translation (266 keys + dynamic status keys) | complete |
| Original business rules re-implemented from `server.py` (plans, OTP limits, order/payment rules, doctor flow) | written, see `docs/MIGRATION_SPEC.md` |

## NOT yet verified (needs your PC with Composer + MySQL)
`composer install`, `php artisan migrate` on MySQL, the 15 feature tests in `tests/Feature/JanwarTest.php`, and clicking through every page. Run:
```bash
php artisan migrate:fresh --seed
php artisan test
```
The tests cover: all public pages open; only admin-approved ads are public; signup with OTP (wrong/right code), login by phone/email, blocked users; old Python password login + upgrade; sell flow + plan limit + photo rule; full Cash-on-Delivery order (cannot complete before payment is confirmed); cannot buy own animal / unavailable payment method; cancel returns the animal to sale and marks refund; admin area returns 403 for non-admins and all 14 admin tabs open for an admin; admin approval shows the ad publicly; doctor registration, private documents, no self-approval, consultations; contact/report/favorites; HTML escaping (XSS); CSRF token on forms; Urdu/English switch.

If any test fails or a page shows an error, send me the message from `storage/logs/laravel.log` (or the failing test output) and I will fix it. Expect a small number of first-run fixes: this is normal for code that could not be executed where it was written.

## Differences from the Python version (by design)
* Admin is now a normal account with role `admin` (not one shared password); every admin action is written to the Admin log.
* Contact messages, ad reports, disputes and admin-present meetings are now saved in the database (before they only lived in the browser).
* Categories are a database table (cow, buffalo, goat, sheep), breed stays free text.
* Cart is stored in the visitor's session.
* The sell wizard, filters and "load more" are server-rendered pages (paging links instead of an in-page "load more" button).
