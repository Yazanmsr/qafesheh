# Qafesheh Group Theme — Changelog

## 3.1.0 — 2026-09-30 (الدرع + العروض)
- **عروض الشركات:** نوع محتوى «العروض» — كل عرض مرتبط بشركة (قفيشة / الجازي / مكارم) وبلونها، سطر خصم، شارة، بداية ونهاية، وزر لمتجر الشركة تلقائياً. سكشن QB Offers هادئ بلغة الموقع (زوايا حادة، خط ذهبي بلون الشركة، الخصم بخط النسخ، عدّاد رفيع) بيظهر لحاله وبيختفي لما ما في عرض. تاب بالداشبورد: محرّر بأربع بطاقات (الشركة، العرض، المدة، الزر)، ملخّص، عمود حالة، وتنبيه للعروض اللي بتنتهي خلال ٤٨ ساعة.
- **الأمان من Feta & Olive:** كابتشا QB (أو Turnstile/reCAPTCHA) على الدخول ونسيت كلمة المرور والتعليقات ونماذج التواصل والشكاوى والتقديم على الوظائف، جدار حماية + قواعد .htaccess، التحقق بخطوتين، تنبيهات أمنية بالإيميل، ترويسات CSP/COOP، وجلسات أقصر — وكلها بمركز الإطلاق.


## 3.0.0 — 2026-09-23 (منصّة العميل — بمستوى مكارم)
- **لوحة تحكم مجموعة قفيشة:** شاشة رئيسية خاصة (أرقام اليوم، ٨ أزرار سريعة، آخر طلبات التوظيف والرسائل، «بحاجة انتباه»، دليل سريع)، قائمة جانبية مبسّطة بلغة العميل، إخفاء تنبيهات الإضافات، سكِن كامل بألوان المجموعة (ذهبي/حبر) وشاشة دخول بالهوية، وصندوق «خطوات نشر وظيفة» داخل محرّر الوظيفة.
- **وضع المطوّر:** زر بالشريط العلوي للمدراء يرجّع ووردبريس كامل لحساب واحد فقط، وضغطة ثانية يرجّع اللوحة المبسّطة.
- **الأمان:** رابط دخول خاص (`/qb-dash`، قابل للتغيير من تبويب الأمان)، و404 لـ`wp-login.php` و`/wp-admin` للزوار، إيقاف مؤقت بعد ٦ محاولات فاشلة، رسالة خطأ موحّدة، منع كشف أسماء المستخدمين، تعطيل XML-RPC، إخفاء نسخة ووردبريس، منع تعديل الملفات من اللوحة، ترويسات حماية، وحماية مرفقات السير الذاتية والشكاوى. مخرج الطوارئ: `define( 'QAF_NO_HIDE_LOGIN', true );`.
- **الفريق:** شاشة بسيطة لإضافة زميل (محرّر محتوى / محرّر أخبار) مع إيميل تعيين كلمة المرور، وحذف الحساب مع نقل محتواه.
- **الإيميلات بالهوية:** قالب موحّد (شعار، خط ذهبي، كرت ورقي، فوتر داكن مع واتساب والهاتف والبريد) يلبس كل رسائل الموقع وووردبريس تلقائياً، بدل الشكل الافتراضي.
- **مركز الإطلاق:** قائمة جاهزية (المحتوى، الهوية والتواصل، الوظائف والرسائل، تقني، الأمان) بنسبة إنجاز، و**ورقة بيانات الفروع** CSV: تنزيل، تعديل بإكسل، ورفع (مطابقة برقم الفرع، الخانات الفاضية ما بتتغيّر).
- **شاشة الدخول بالهوية:** شاشة مقسومة (صورة الحِرفة تحت غطاء حبري، الختم، «EST. 1968»، بيان المجموعة وأسماء الشركات) وكرت ورقي بخط ذهبي، «أهلاً بعودتك»، وزر ذهبي — نفس التصميم لنسيت كلمة المرور وإعادة التعيين ورسائل الخطأ، وشريط صورة على الموبايل.
- **المحرّر (Gutenberg) بالهوية:** الأزرق صار ذهبي، الختم بدل شعار ووردبريس، بدون جولة الترحيب ووضع الشاشة الكاملة، وصناديق الحقول ككروت.
- **تواصل سريع** داخل كل رسالة وطلب توظيف: واتساب برسالة جاهزة، اتصال، إيميل بموضوع جاهز، وزر السيرة الذاتية.
- **تنظيف اللوحة:** طلبات التوظيف بقائمتها الخاصة (بدون تكرار تحت الوظائف)، إخفاء Screen Options والمساعدة، إزالة «— Private» من الرسائل والطلبات، ملف شخصي مختصر (الاسم، الإيميل، كلمة المرور)، وإيقاف كلمات مرور التطبيقات بالوضع المبسّط.
- **إيميلات النماذج:** سطور «الحقل: القيمة» صارت كرت جدول، رابط اللوحة صار زر، ورقم الهاتف بيضيف زر «الرد على واتساب».
- **إصلاح حرج:** فلتر كلمات مرور التطبيقات كان يستدعي المستخدم الحالي أثناء تحديده (`determine_current_user`) فيدخل بحلقة لا نهائية ويستنزف الذاكرة (Fatal: Allowed memory size…) على طلبات الـcron والـREST. صار يستخدم الفلتر الخاص بالمستخدم، و`qaf_simple_admin()` ما عاد يحدد المستخدم بنفسه أبداً. فحص كل أنواع الطلبات بعدها: الواجهة، الـcron، الـREST، admin-ajax، الدخول، وكل شاشات اللوحة — بدون أي خطأ.
- **تلميع اللوحة:** الذهبي صار دايماً بنص حبري (زي الموقع) بالقائمة والأزرار والشارات، جدول آخر الطلبات بالرئيسية منسّق، صفحة الإعدادات بالعربي مع الختم، شاشة الفريق بكروت وأفاتار وشارات صلاحية، وإخفاء «Private / All» من الرسائل والطلبات.
- **الأداء:** Speculation Rules للتحميل المسبق عند مرور المؤشر، و`content-visibility` للأقسام خارج الشاشة، مع خيارين جديدين بتبويب الأداء.


## 2.4.0 — Hero slider + reliability fixes
- **Home hero is now a slider** with the exact same design: per-slide image, eyebrow, title, text and two buttons; the data plinth stays fixed.
  - Desktop: directional clip wipe with a gold light sweep; slow Ken Burns settle on the active image; words leave softly and the next title rises word by word.
  - Touch / small screens: soft cross-fade + swipe. Reduced motion: instant, no autoplay.
  - Progress bars, rolling counter, arrows, keyboard, aria-live; autoplay pauses on hover, focus, hidden tab and off-screen.
  - Performance: only slide 1 loads with the page (LCP, preloaded with srcset); the next image is fetched on idle and decoded before it is shown; words split server-side (no layout shift).
  - QB Hero Slider widget: slides repeater + autoplay / interval; heroes saved before 2.4 still render. WPML: slides registered for translation.
- **Fix — text stuck at opacity 0** (hero copy, inner-page titles): on Elementor pages the widget-ready hook flipped the engine to “ready” before the loader finished, stranding the queued elements. The queue is now always flushed; a watchdog reveals anything on screen that is still hidden; script errors reveal everything; late-running scripts keep already-visible content visible.
- LiteSpeed / Cloudflare: theme scripts and their inline config are excluded from combine / defer / delay (`data-no-optimize`, `data-no-defer`, `data-cfasync`, LiteSpeed exclude filters).
- Importer: empty Elementor pages are rebuilt automatically; new “rebuild Elementor pages even if edited” option keeps a backup of the previous layout (`_qaf_elementor_backup`).

## 2.3.0 — About, Contact, Complaints + site forms
- About page: page hero with ghost word, story (arched image), timeline that draws its track and lights each milestone, vision / mission, values grid, brands strip.
- Contact page: channels (from Theme Options), contact form, direct brand contacts, complaints promo, lazy map.
- Complaints page: centred hero, “how it works” steps, complaint form (type chips, company → branch narrowing, optional photo/PDF), pledge, direct contact, **status lookup by tracking number + email**.
- 10 new QB Elementor widgets (Page Hero, Story, Timeline, Vision & Mission, Values, Brands Strip, Contact Channels, Steps Bar, Contact Form, Complaint Form) — all registered for WPML incl. repeaters.
- Pages work without Elementor too: `page.php` renders the same sections from the page recipe; the importer builds all three pages in Elementor per language when Elementor is active (keeps pages edited in the builder).
- Forms engine (`inc/forms.php`) + **Form Entries** screen «الرسائل»: private storage, tracking numbers (QG-YYMM-XXXXX), statuses visible to the sender, internal notes, unread badge, filters, formula-safe CSV export, secure attachment download, optional copy to the selected company’s inbox, personal-data eraser. Same protections as applications (nonce, honeypot, time trap, rate limit, MIME + magic-byte checks, private folder).
- New option tab «النماذج والرسائل» + 25 UI strings per language.
- Refactor: form styles/scripts moved to `forms.css` / `forms.js` (shared by applications, contact and complaints); `careers.js` now only filters vacancies. Unused `qaf_complaint` type removed.

## 2.2.0 — Articles, careers and the application system
- Articles: `home.php` / `category.php` / `tag.php` / `search.php` share one listing — stacked hero, category chips + article search, featured article (sticky or latest, excluded from the grid), arched 3-column grid, design pagination.
- Article page: centred header with reading time, overlapping cover, share (Facebook / X / WhatsApp / copy link), auto table of contents from H2/H3 with scroll spy, styled lead paragraph, quotes, captions, tables, tags and related articles.
- Careers: split hero, four “why us” slots, sticky filters by department / location / type with live counts (client-side, animated), vacancy rows, open-application band.
- Job page: header with tags + apply, about / responsibilities / requirements, details sidebar, other vacancies, “applications closed” switch.
- Application system (`inc/applications.php`): private storage in «الوظائف ← الطلبات», CV upload (PDF/DOC/DOCX, extension + MIME + magic-byte checks, size from options, random names, deny-all private folder), nonce + honeypot + time trap + per-IP rate limit, HR email with optional attachment, applicant confirmation, AJAX with no-JS fallback, statuses, unread badge, filters, secure CV download, formula-safe CSV export, retention purge, WordPress personal-data eraser, file deleted with its application.
- New option tab «الوظائف والتقديم» + blog hero options + 45 UI strings, all per language.
- Jobs: department / location / type are taxonomies now (text values from 2.1 migrate automatically).
- Importer: 7 articles with real structure, 6 jobs with terms in both languages, Latin slugs.
- Shared sidebar / checklist styles moved to base.css; `careers.css`, `careers.js`, `article.js` load only on their views.

## 2.1.0 — Companies, services and branches pages
- New templates built from the approved artboards: `single-qaf_company.php` (hero, intro, arched gallery, categories, quote + contact, sister brands), `archive-qaf_company.php`, `archive-qaf_service.php` (editorial rows + gold CTA band), `single-qaf_service.php` (media hero, includes, images, animated steps, sticky sidebar), `archive-qaf_branch.php` (brand filter, list, sticky live map, deep links `#branch-slug`).
- Branch single URLs 301 to their card in the branches list.
- New option tab «صفحات الأقسام» (hero copy for companies/services/branches, gold CTA, service request form shortcode) + 20 new UI strings — all per language.
- Company and service entries gained page fields: tagline, catalogue, intro title, gallery (sortable multi-picker), categories (repeater), quote, contact and socials; service includes, steps, gallery, short home-card text.
- Admin: generic `gallery` and `repeater` controls (add / remove / drag to sort) shared by meta boxes and the options panel.
- Components: gold foil titles with a one-time light sweep, kicker labels, arch frames, brand tags/chips, ink button.
- Importer: fills every new field in both languages, converts bundled images to attachments, creates clean Latin slugs (`/companies/qafesheh/`, `/services/manufacturing-production/`).
- Menus: the matching section stays highlighted on its archive and entries (WordPress no longer marks “News” as current on content-type pages); breadcrumbs use the section names from options.
- Per-view CSS: `companies.css`, `services.css`, `branches.css` load only on their pages; `blog.css` no longer loads there.

## 2.0.0 — Full rebuild on the QB Studio architecture
- New structure: `functions.php` bootstrap only, single `qaf_options` array driven by `inc/options-schema.php`, conditional assets with `filemtime` versions.
- Theme Options: 13 tabs (general, colors, typography, header, loader, banner, footer, contact, content types, UI strings, motion, performance, tracking) with per-language inputs — no String Translation needed.
- WPML ready: `wpml-config.xml`, custom fields copy/translate rules, every QB widget registered for Elementor translation (including repeaters).
- Self-hosted fonts (Noto Naskh Arabic 700, IBM Plex Sans Arabic 300–600, Libre Baskerville) split by unicode-range. Zero external font requests.
- Loader: drawn eight-point star, real load progress, ink + gold curtains; full intro on first visit, quick curtain on the next pages, soft curtain on leave, bfcache-safe, reduced-motion aware, CSS failsafes.
- Homepage rebuilt 1:1 from the approved design: hero + data plinth, 01 statement, 02 companies, 03 capabilities, 04 services, 05 standards, 06 news, 07 branches with live map switching, CTA.
- 9 Elementor widgets (QB category) sharing the exact template parts used by `front-page.php`.
- Importer rewritten: bilingual, merges by key/title, keeps manual edits (content hash, meta, images, Elementor pages, menus), builds the Elementor home per language, sets reading settings and permalinks.
- Removed: hero slider (not in the design), second options system, raw head/body code fields, Google Fonts fallback, missing `home.css`/`inner.css` references.
- v1 meta keys (`q_*`) migrate automatically to `_qaf_*` on first admin load.
