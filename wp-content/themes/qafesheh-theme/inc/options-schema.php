<?php
/**
 * SINGLE SOURCE OF TRUTH for every theme option.
 *
 * tab_id => [ label, icon, fields => [ key => field ] ]
 * Field types: text, textarea, number, range, select, switch, color, image, url, email, repeater, note, heading.
 * Flags: default, default__{lang} (per-language default), desc, i18n => true, min/max/step, options, fields (repeater).
 *
 * Adding a field here makes it appear in the panel, get a default, get sanitised and get per-language inputs.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

/**
 * Icon choices for select fields.
 *
 * @return array
 */
function qaf_icon_choices() {
	return array(
		'star'    => 'نجمة',
		'clock'   => 'ساعة',
		'chart'   => 'نمو',
		'pin'     => 'موقع',
		'factory' => 'مصنع',
		'tailor'  => 'تفصيل',
		'needle'  => 'تطريز',
		'globe'   => 'عالمي',
		'truck'   => 'شحن',
		'box'     => 'عناية',
		'pen'     => 'تصميم',
		'shield'  => 'أمان',
		'heart'   => 'رعاية',
	);
}

/**
 * Options schema.
 *
 * @return array
 */
function qaf_options_schema() {
	static $schema = null;
	if ( null !== $schema ) {
		return $schema;
	}

	$schema = array(

		/* ------------------------------------------------ General */
		'general'     => array(
			'label'  => 'عام',
			'icon'   => 'dashicons-admin-generic',
			'fields' => array(
				'note_brand'   => array( 'type' => 'note', 'label' => 'الهوية', 'desc' => 'الشعار والاسم يظهران في الهيدر والفوتر وشاشة التحميل.' ),
				'logo'         => array( 'type' => 'image', 'label' => 'الشعار', 'desc' => 'PNG شفاف أو SVG. يظهر على الخلفية الداكنة في الهيدر والفوتر.', 'default' => 'assets/images/logos/logo-qafesheh-seal.png' ),
				'logo_h'       => array( 'type' => 'range', 'label' => 'ارتفاع الشعار — ديسكتوب', 'min' => 32, 'max' => 96, 'step' => 1, 'unit' => 'px', 'default' => 56 ),
				'logo_h_m'     => array( 'type' => 'range', 'label' => 'ارتفاع الشعار — موبايل', 'min' => 28, 'max' => 72, 'step' => 1, 'unit' => 'px', 'default' => 42 ),
				'brand_text'   => array( 'type' => 'switch', 'label' => 'إظهار الاسم بجانب الشعار', 'default' => 1 ),
				'brand_name'   => array( 'type' => 'text', 'label' => 'اسم المجموعة', 'i18n' => true, 'default' => 'مجموعة قفيشة الصناعية', 'default__en' => 'Qafesheh Industrial Group' ),
				'brand_sub'    => array( 'type' => 'text', 'label' => 'السطر اللاتيني تحت الاسم', 'i18n' => true, 'default' => 'QAFESHEH INDUSTRIAL GROUP', 'default__en' => 'EST. 1968 — AMMAN, JORDAN' ),
				'favicon'      => array( 'type' => 'image', 'label' => 'أيقونة المتصفح', 'desc' => 'مربعة 512×512. تُتجاهل إذا ضُبطت «أيقونة الموقع» من التخصيص.' ),
				'note_layout'  => array( 'type' => 'note', 'label' => 'التخطيط' ),
				'container'    => array( 'type' => 'range', 'label' => 'عرض المحتوى الأقصى', 'min' => 1100, 'max' => 1680, 'step' => 10, 'unit' => 'px', 'default' => 1440 ),
				'radius'       => array( 'type' => 'range', 'label' => 'استدارة الزوايا', 'desc' => 'التصميم المعتمد بزوايا حادة (0).', 'min' => 0, 'max' => 24, 'step' => 1, 'unit' => 'px', 'default' => 0 ),
				'scroll_top'   => array( 'type' => 'switch', 'label' => 'زر العودة للأعلى', 'default' => 1 ),
				'progress'     => array( 'type' => 'switch', 'label' => 'شريط تقدّم القراءة', 'default' => 1 ),
			),
		),

		/* ------------------------------------------------ Colors */
		'colors'      => array(
			'label'  => 'الألوان',
			'icon'   => 'dashicons-art',
			'fields' => array(
				'note_c'        => array( 'type' => 'note', 'label' => 'ألوان المجموعة', 'desc' => 'كل الموقع يقرأ من هذه الألوان. تغيير لون هنا يغيّره في كل مكان.' ),
				'c_primary'     => array( 'type' => 'color', 'label' => 'الذهبي (الأكسنت)', 'default' => '#B8912F' ),
				'c_primary_dk'  => array( 'type' => 'color', 'label' => 'الذهبي الغامق (روابط)', 'default' => '#8C6B1F' ),
				'c_ink'         => array( 'type' => 'color', 'label' => 'الحبر — العناوين والأقسام الداكنة', 'default' => '#0F0E0C' ),
				'c_dark'        => array( 'type' => 'color', 'label' => 'الأسود الأعمق — الشريط العلوي والفوتر', 'default' => '#0A0906' ),
				'c_dark_2'      => array( 'type' => 'color', 'label' => 'سطح داكن — البطاقات والمعايير', 'default' => '#14130F' ),
				'c_bg'          => array( 'type' => 'color', 'label' => 'الخلفية — الحجر الدافئ', 'default' => '#F2EEE7' ),
				'c_bg_soft'     => array( 'type' => 'color', 'label' => 'الخلفية الثانية — أقسام متناوبة', 'default' => '#E8E2D7' ),
				'c_line'        => array( 'type' => 'color', 'label' => 'الخطوط الفاصلة', 'default' => '#D6CDBD' ),
				'c_body'        => array( 'type' => 'color', 'label' => 'نص الفقرات', 'default' => '#3A362E' ),
				'c_muted'       => array( 'type' => 'color', 'label' => 'النص الثانوي', 'default' => '#7A7263' ),
				'c_on_dark'     => array( 'type' => 'color', 'label' => 'النص على الخلفيات الداكنة', 'default' => '#ADA694' ),
				'note_brands'   => array( 'type' => 'note', 'label' => 'ألوان العلامات', 'desc' => 'تظهر فقط داخل بطاقة كل علامة.' ),
				'c_b_qafesheh'  => array( 'type' => 'color', 'label' => 'قفيشة', 'default' => '#C9A227' ),
				'c_b_aljazi'    => array( 'type' => 'color', 'label' => 'الجازي', 'default' => '#E7E1D3' ),
				'c_b_makarem'   => array( 'type' => 'color', 'label' => 'مكارم', 'default' => '#A51E25' ),
			),
		),

		/* ------------------------------------------------ Typography */
		'typography'  => array(
			'label'  => 'الخطوط',
			'icon'   => 'dashicons-editor-textcolor',
			'fields' => array(
				'note_t'     => array( 'type' => 'note', 'label' => 'الخطوط محلية بالكامل', 'desc' => 'نسخ عربي للعناوين، IBM Plex Sans Arabic للنصوص، Libre Baskerville للأرقام واللاتيني. لا طلبات خارجية.' ),
				'fs_base'    => array( 'type' => 'range', 'label' => 'حجم النص الأساسي', 'min' => 14, 'max' => 19, 'step' => 0.5, 'unit' => 'px', 'default' => 16.5 ),
				'h_scale'    => array(
					'type'    => 'select',
					'label'   => 'سلّم العناوين',
					'options' => array(
						'compact'   => 'مضغوط',
						'balanced'  => 'متوازن',
						'editorial' => 'تحريري (التصميم)',
					),
					'default' => 'editorial',
				),
				'body_weight' => array(
					'type'    => 'select',
					'label'   => 'وزن الفقرات',
					'options' => array(
						'300' => 'خفيف 300 (التصميم)',
						'400' => 'عادي 400',
					),
					'default' => '300',
				),
			),
		),

		/* ------------------------------------------------ Header */
		'header'      => array(
			'label'  => 'الهيدر',
			'icon'   => 'dashicons-table-row-before',
			'fields' => array(
				'topbar'          => array( 'type' => 'switch', 'label' => 'الشريط العلوي', 'default' => 1 ),
				'top_address'     => array( 'type' => 'text', 'label' => 'العنوان في الشريط العلوي', 'i18n' => true, 'default' => 'المدينة الصناعية — سحاب، عمّان، الأردن', 'default__en' => 'Industrial City — Sahab, Amman, Jordan' ),
				'top_phone'       => array( 'type' => 'text', 'label' => 'الهاتف في الشريط العلوي', 'default' => '+962 79 881 7390' ),
				'lang_switch'     => array( 'type' => 'switch', 'label' => 'مبدّل اللغة', 'desc' => 'يعمل تلقائياً مع WPML.', 'default' => 1 ),
				'header_sticky'   => array( 'type' => 'switch', 'label' => 'تثبيت الهيدر عند التمرير', 'default' => 1 ),
				'header_autohide' => array( 'type' => 'switch', 'label' => 'إخفاء ناعم عند النزول وإظهار عند الصعود', 'default' => 1 ),
				'header_cta_text' => array( 'type' => 'text', 'label' => 'نص زر الهيدر', 'i18n' => true, 'default' => 'تواصل معنا', 'default__en' => 'Contact us' ),
				'header_cta_url'  => array( 'type' => 'url', 'label' => 'رابط زر الهيدر', 'i18n' => true, 'default' => '/contact/', 'default__en' => '/en/contact/' ),
			),
		),

		/* ------------------------------------------------ Loader */
		'loader'      => array(
			'label'  => 'شاشة التحميل',
			'icon'   => 'dashicons-update',
			'fields' => array(
				'loader_on'   => array( 'type' => 'switch', 'label' => 'تفعيل شاشة التحميل', 'default' => 1 ),
				'loader_mode' => array(
					'type'    => 'select',
					'label'   => 'السلوك',
					'desc'    => '«ذكي»: مقدّمة كاملة بأول زيارة، ثم انتقال سريع ناعم بين الصفحات.',
					'options' => array(
						'smart' => 'ذكي (مستحسن)',
						'full'  => 'مقدّمة كاملة بكل صفحة',
						'quick' => 'انتقال سريع فقط',
					),
					'default' => 'smart',
				),
				'loader_mark' => array(
					'type'    => 'select',
					'label'   => 'العلامة في المنتصف',
					'options' => array(
						'star' => 'النجمة الثمانية (مرسومة)',
						'logo' => 'الشعار',
					),
					'default' => 'star',
				),
				'loader_min'  => array( 'type' => 'range', 'label' => 'أقل مدة للمقدّمة الكاملة', 'min' => 900, 'max' => 3200, 'step' => 100, 'unit' => 'ms', 'default' => 1700 ),
				'page_fx'     => array( 'type' => 'switch', 'label' => 'انتقال ناعم عند مغادرة الصفحة', 'default' => 1 ),
			),
		),

		/* ------------------------------------------------ Page banner */
		'banner'      => array(
			'label'  => 'بنر الصفحات',
			'icon'   => 'dashicons-format-image',
			'fields' => array(
				'banner_on'      => array( 'type' => 'switch', 'label' => 'بنر أعلى الصفحات الداخلية', 'default' => 1 ),
				'banner_image'   => array( 'type' => 'image', 'label' => 'صورة البنر الافتراضية', 'default' => 'assets/import/workshop-cutting.jpg' ),
				'banner_h'       => array( 'type' => 'range', 'label' => 'الارتفاع — ديسكتوب', 'min' => 280, 'max' => 720, 'step' => 10, 'unit' => 'px', 'default' => 440 ),
				'banner_h_m'     => array( 'type' => 'range', 'label' => 'الارتفاع — موبايل', 'min' => 220, 'max' => 520, 'step' => 10, 'unit' => 'px', 'default' => 320 ),
				'banner_overlay' => array( 'type' => 'range', 'label' => 'قوة التعتيم', 'min' => 0.3, 'max' => 0.97, 'step' => 0.01, 'default' => 0.84 ),
				'crumbs'         => array( 'type' => 'switch', 'label' => 'مسار التنقل (Breadcrumbs)', 'default' => 1 ),
			),
		),

		/* ------------------------------------------------ Footer */
		'footer'      => array(
			'label'  => 'الفوتر',
			'icon'   => 'dashicons-table-row-after',
			'fields' => array(
				'footer_stitch' => array( 'type' => 'switch', 'label' => 'شريط الغرز أعلى الفوتر', 'default' => 1 ),
				'footer_about'  => array( 'type' => 'textarea', 'label' => 'نبذة الفوتر', 'i18n' => true, 'default' => 'ثلاث علاماتٍ في الأزياء التراثية الفاخرة، من مصنعنا في سحاب إلى الخليج والوطن العربي.', 'default__en' => 'Three houses of luxury heritage wear — from our factory in Sahab to the Gulf and the Arab world.' ),
				'footer_t1'     => array( 'type' => 'text', 'label' => 'عنوان العمود ١', 'i18n' => true, 'default' => 'المجموعة', 'default__en' => 'The Group' ),
				'footer_t2'     => array( 'type' => 'text', 'label' => 'عنوان العمود ٢', 'i18n' => true, 'default' => 'شركاتنا', 'default__en' => 'Our companies' ),
				'footer_t3'     => array( 'type' => 'text', 'label' => 'عنوان العمود ٣', 'i18n' => true, 'default' => 'خدمة العملاء', 'default__en' => 'Customer care' ),
				'footer_t4'     => array( 'type' => 'text', 'label' => 'عنوان عمود التواصل', 'i18n' => true, 'default' => 'تواصل', 'default__en' => 'Contact' ),
				'footer_copy'   => array( 'type' => 'text', 'label' => 'سطر الحقوق', 'desc' => 'استخدم {year} للسنة الحالية.', 'i18n' => true, 'default' => '© {year} مجموعة قفيشة الصناعية — جميع الحقوق محفوظة', 'default__en' => '© {year} Qafesheh Industrial Group — All rights reserved' ),
				'footer_credit' => array( 'type' => 'switch', 'label' => 'إظهار «تطوير QB Studio»', 'default' => 1 ),
				'footer_credit_text' => array( 'type' => 'text', 'label' => 'نص التطوير', 'i18n' => true, 'default' => 'تطوير QB Studio', 'default__en' => 'Crafted by QB Studio' ),
			),
		),

		/* ------------------------------------------------ Contact & social */
		'contact'     => array(
			'label'  => 'التواصل والسوشال',
			'icon'   => 'dashicons-share',
			'fields' => array(
				'c_email'      => array( 'type' => 'email', 'label' => 'الإيميل', 'default' => 'info@qafesheh.com' ),
				'c_phone'      => array( 'type' => 'text', 'label' => 'الهاتف', 'default' => '+962 79 881 7390' ),
				'c_address'    => array( 'type' => 'text', 'label' => 'العنوان', 'i18n' => true, 'default' => 'المدينة الصناعية — سحاب، عمّان', 'default__en' => 'Industrial City — Sahab, Amman' ),
				's_instagram'  => array( 'type' => 'url', 'label' => 'Instagram', 'default' => 'https://www.instagram.com/qafesheh.factory/' ),
				's_facebook'   => array( 'type' => 'url', 'label' => 'Facebook', 'default' => 'https://www.facebook.com/qafesheh.factory' ),
				's_youtube'    => array( 'type' => 'url', 'label' => 'YouTube', 'default' => 'https://www.youtube.com/@Qafesheh' ),
				's_tiktok'     => array( 'type' => 'url', 'label' => 'TikTok' ),
				's_whatsapp'   => array( 'type' => 'text', 'label' => 'واتساب (رقم دولي)', 'desc' => 'مثال: 962798817390' ),
			),
		),

		/* ------------------------------------------------ Content types */
		'content'     => array(
			'label'  => 'أنواع المحتوى',
			'icon'   => 'dashicons-database',
			'fields' => array(
				'note_slugs'   => array( 'type' => 'note', 'label' => 'روابط الأقسام', 'desc' => 'تغيير الرابط يُحدّث الروابط الدائمة تلقائياً عند الحفظ.' ),
				'slug_company' => array( 'type' => 'text', 'label' => 'رابط الشركات', 'default' => 'companies' ),
				'slug_service' => array( 'type' => 'text', 'label' => 'رابط الخدمات', 'default' => 'services' ),
				'slug_branch'  => array( 'type' => 'text', 'label' => 'رابط الفروع', 'default' => 'branches' ),
				'slug_job'     => array( 'type' => 'text', 'label' => 'رابط الوظائف', 'default' => 'careers' ),
				'pp_blog'      => array( 'type' => 'number', 'label' => 'عدد المقالات بالصفحة', 'min' => 3, 'max' => 48, 'default' => 9 ),
				'pp_archive'   => array( 'type' => 'number', 'label' => 'عدد العناصر بصفحة الأرشيف', 'min' => 3, 'max' => 48, 'default' => 12 ),
			),
		),

		/* ------------------------------------------------ Section pages (archives) */
		'sections'    => array(
			'label'  => 'صفحات الأقسام',
			'icon'   => 'dashicons-screenoptions',
			'fields' => array(
				'note_co'        => array( 'type' => 'note', 'label' => 'قطاعات الأعمال', 'desc' => 'رأس صفحة أرشيف الشركات.' ),
				'co_title'       => array( 'type' => 'text', 'label' => 'العنوان', 'i18n' => true, 'default' => 'قطاعات الأعمال', 'default__en' => 'Our businesses' ),
				'co_text'        => array( 'type' => 'textarea', 'label' => 'النص', 'i18n' => true, 'default' => 'ثلاث علاماتٍ متخصصة، يجمعها مصنعٌ واحد ومعيارٌ واحد للجودة.', 'default__en' => 'Three specialised houses, one factory and one standard of quality.' ),

				'note_svc'       => array( 'type' => 'note', 'label' => 'الخدمات', 'desc' => 'رأس صفحة الخدمات والشريط الذهبي أسفلها.' ),
				'svc_title'      => array( 'type' => 'text', 'label' => 'العنوان', 'i18n' => true, 'default' => 'خدماتُ المجموعة', 'default__en' => 'Group services' ),
				'svc_text'       => array( 'type' => 'textarea', 'label' => 'النص', 'i18n' => true, 'default' => 'نخدم التاجر والموزّع والفرد على حدٍّ سواء — من التصنيع بالجملة إلى التفصيل حسب الطلب والعناية بالقطعة بعد سنوات.', 'default__en' => 'We serve traders, distributors and individuals alike — from wholesale manufacturing to made-to-order pieces and care years later.' ),
				'svc_cta_title'  => array( 'type' => 'text', 'label' => 'عنوان الشريط', 'i18n' => true, 'default' => 'عندك طلبٌ خاص؟', 'default__en' => 'Have a special request?' ),
				'svc_cta_text'   => array( 'type' => 'text', 'label' => 'نص الشريط', 'i18n' => true, 'default' => 'أرسل تفاصيله ونرجع لك بعرض سعر خلال يومي عمل.', 'default__en' => 'Send us the details and we will reply with a quote within two working days.' ),
				'svc_cta_btn'    => array( 'type' => 'text', 'label' => 'زر الشريط', 'i18n' => true, 'default' => 'اطلب عرض سعر', 'default__en' => 'Request a quote' ),
				'svc_cta_url'    => array( 'type' => 'url', 'label' => 'رابط الزر', 'i18n' => true, 'default' => '/contact/', 'default__en' => '/en/contact/' ),
				'svc_form'       => array( 'type' => 'text', 'label' => 'فورم «اطلب هذه الخدمة»', 'desc' => 'الصق شورتكود Contact Form 7. فارغ = زر يفتح صفحة التواصل.' ),

				'note_blog'      => array( 'type' => 'note', 'label' => 'المقالات والأخبار' ),
				'blog_title'     => array( 'type' => 'text', 'label' => 'العنوان', 'i18n' => true, 'default' => 'من دفترِ المجموعة', 'default__en' => 'From the group’s journal' ),
				'blog_text'      => array( 'type' => 'textarea', 'label' => 'النص', 'i18n' => true, 'default' => 'أخبار المصنع والمعارض، ومقالاتٌ عن الفروة والبشت والثوب — كيف تُصنع، كيف تُختار، وكيف تُحفظ.', 'default__en' => 'News from the factory and showrooms, and articles on farwa, bisht and thobes — how they are made, chosen and kept.' ),
				'blog_featured'  => array( 'type' => 'switch', 'label' => 'إبراز أحدث مقال (أو المثبّت) بأعلى الصفحة الأولى', 'default' => 1 ),

				'note_br'        => array( 'type' => 'note', 'label' => 'الفروع' ),
				'br_title'       => array( 'type' => 'text', 'label' => 'العنوان', 'i18n' => true, 'default' => 'أينَ تجدنا', 'default__en' => 'Where to find us' ),
				'br_text'        => array( 'type' => 'textarea', 'label' => 'النص', 'i18n' => true, 'default' => 'معارضنا موزّعة في عمّان وإربد، ومصنعنا في المدينة الصناعية بسحاب. كل معرضٍ يحمل علامةً أو أكثر من علاماتنا الثلاث.', 'default__en' => 'Our showrooms are in Amman and Irbid, and our factory is in Sahab Industrial City. Every showroom carries one or more of our three brands.' ),
			),
		),

		/* ------------------------------------------------ Careers */
		'careers'     => array(
			'label'  => 'الوظائف والتقديم',
			'icon'   => 'dashicons-id-alt',
			'fields' => array(
				'note_jobs_hero' => array( 'type' => 'note', 'label' => 'رأس صفحة الوظائف' ),
				'jobs_title'     => array( 'type' => 'text', 'label' => 'العنوان', 'i18n' => true, 'default' => 'انضمَّ إلى أيدينا', 'default__en' => 'Join our hands' ),
				'jobs_text'      => array( 'type' => 'textarea', 'label' => 'النص', 'i18n' => true, 'default' => 'أكثر من مئة حِرَفيٍّ وعامل من الطراز الأول يصنعون منتجاتنا كل يوم. إن كنت تبحث عن مكانٍ تتعلّم فيه حِرفةً وتبني فيه مساراً — ابدأ من هنا.', 'default__en' => 'More than a hundred first-class artisans and workers make our pieces every day. If you are looking for a place to learn a craft and build a career — start here.' ),
				'jobs_image'     => array( 'type' => 'image', 'label' => 'صورة الرأس', 'default' => 'assets/import/factory-women.jpg' ),

				'note_why'       => array( 'type' => 'note', 'label' => 'لماذا تعمل معنا', 'desc' => 'أربع ميزات تحت الرأس. اترك العنوان فارغاً لإخفاء الميزة.' ),
				'why1_icon'      => array( 'type' => 'select', 'label' => 'الميزة ١ — الأيقونة', 'options' => qaf_icon_choices(), 'default' => 'star' ),
				'why1_title'     => array( 'type' => 'text', 'label' => 'الميزة ١ — العنوان', 'i18n' => true, 'default' => 'تدريبٌ على الحِرفة', 'default__en' => 'Craft training' ),
				'why1_text'      => array( 'type' => 'text', 'label' => 'الميزة ١ — النص', 'i18n' => true, 'default' => 'تتعلّم من أمهر الأيادي في المصنع.', 'default__en' => 'Learn from the most skilled hands in the factory.' ),
				'why2_icon'      => array( 'type' => 'select', 'label' => 'الميزة ٢ — الأيقونة', 'options' => qaf_icon_choices(), 'default' => 'clock' ),
				'why2_title'     => array( 'type' => 'text', 'label' => 'الميزة ٢ — العنوان', 'i18n' => true, 'default' => 'دوامٌ واضح', 'default__en' => 'Clear hours' ),
				'why2_text'      => array( 'type' => 'text', 'label' => 'الميزة ٢ — النص', 'i18n' => true, 'default' => 'ساعات عمل ثابتة ومعروفة مسبقاً.', 'default__en' => 'Fixed working hours, known in advance.' ),
				'why3_icon'      => array( 'type' => 'select', 'label' => 'الميزة ٣ — الأيقونة', 'options' => qaf_icon_choices(), 'default' => 'chart' ),
				'why3_title'     => array( 'type' => 'text', 'label' => 'الميزة ٣ — العنوان', 'i18n' => true, 'default' => 'مسارُ ترقية', 'default__en' => 'A path to grow' ),
				'why3_text'      => array( 'type' => 'text', 'label' => 'الميزة ٣ — النص', 'i18n' => true, 'default' => 'من المصنع إلى إدارة الخط أو المعرض.', 'default__en' => 'From the factory floor to leading a line or a showroom.' ),
				'why4_icon'      => array( 'type' => 'select', 'label' => 'الميزة ٤ — الأيقونة', 'options' => qaf_icon_choices(), 'default' => 'pin' ),
				'why4_title'     => array( 'type' => 'text', 'label' => 'الميزة ٤ — العنوان', 'i18n' => true, 'default' => 'مواقعُ متعددة', 'default__en' => 'Several locations' ),
				'why4_text'      => array( 'type' => 'text', 'label' => 'الميزة ٤ — النص', 'i18n' => true, 'default' => 'سحاب، عمّان، وإربد.', 'default__en' => 'Sahab, Amman and Irbid.' ),

				'note_open'      => array( 'type' => 'note', 'label' => 'التقديم المفتوح' ),
				'open_label'     => array( 'type' => 'text', 'label' => 'السطر العلوي', 'i18n' => true, 'default' => 'تقديمٌ مفتوح', 'default__en' => 'Open application' ),
				'open_title'     => array( 'type' => 'text', 'label' => 'العنوان', 'i18n' => true, 'default' => 'أرسل سيرتك الذاتية', 'default__en' => 'Send us your CV' ),
				'open_text'      => array( 'type' => 'textarea', 'label' => 'النص', 'i18n' => true, 'default' => 'نحتفظ بها في قاعدة بياناتنا ونتواصل معك فور توفّر شاغرٍ يناسب خبرتك. كل الطلبات تصل مباشرةً إلى إدارة الموارد البشرية.', 'default__en' => 'We keep it on file and contact you as soon as a matching vacancy opens. Every application goes straight to HR.' ),
				'side_title'     => array( 'type' => 'text', 'label' => 'بطاقة «ما لقيت شاغر»', 'i18n' => true, 'default' => 'ما لقيت شاغر مناسب؟', 'default__en' => 'No matching vacancy?' ),
				'side_text'      => array( 'type' => 'text', 'label' => 'نص البطاقة', 'i18n' => true, 'default' => 'أرسل سيرتك ونحتفظ بها لحين توفّر شاغر.', 'default__en' => 'Send your CV and we will keep it until a vacancy opens.' ),

				'note_apply'     => array( 'type' => 'note', 'label' => 'استقبال الطلبات', 'desc' => 'الطلبات تُخزَّن في «الوظائف ← الطلبات» مع السيرة الذاتية، ويصل إشعار بالإيميل.' ),
				'apply_email'    => array( 'type' => 'email', 'label' => 'إيميل الموارد البشرية', 'desc' => 'فارغ = إيميل مدير الموقع.', 'default' => '' ),
				'apply_show_email' => array( 'type' => 'switch', 'label' => 'إظهار الإيميل بقسم التقديم المفتوح', 'default' => 1 ),
				'apply_attach'   => array( 'type' => 'switch', 'label' => 'إرفاق السيرة الذاتية بإيميل الإشعار', 'default' => 1 ),
				'apply_autoreply' => array( 'type' => 'switch', 'label' => 'إرسال إيميل تأكيد للمتقدّم', 'default' => 1 ),
				'apply_max_mb'   => array( 'type' => 'range', 'label' => 'أقصى حجم للسيرة الذاتية', 'min' => 1, 'max' => 10, 'step' => 1, 'unit' => 'MB', 'default' => 5 ),
				'apply_retention' => array( 'type' => 'number', 'label' => 'حذف الطلبات تلقائياً بعد (يوم)', 'desc' => '0 = لا حذف تلقائي. الحذف يشمل ملف السيرة.', 'min' => 0, 'max' => 3650, 'default' => 0 ),
				'apply_success'  => array( 'type' => 'text', 'label' => 'رسالة النجاح', 'i18n' => true, 'default' => 'وصلنا طلبك بنجاح. سنتواصل معك قريباً.', 'default__en' => 'Your application has been received. We will be in touch soon.' ),
				'apply_reply_subject' => array( 'type' => 'text', 'label' => 'عنوان إيميل التأكيد', 'i18n' => true, 'default' => 'استلمنا طلبك — مجموعة قفيشة', 'default__en' => 'We received your application — Qafesheh Group' ),
				'apply_reply_body' => array( 'type' => 'textarea', 'label' => 'نص إيميل التأكيد', 'desc' => '{name} = اسم المتقدّم، {job} = الوظيفة.', 'i18n' => true, 'default' => "مرحباً {name}،\n\nشكراً لتقديمك على «{job}». استلمنا طلبك وسيراجعه فريق الموارد البشرية ويتواصل معك إن كان مناسباً.\n\nمجموعة قفيشة الصناعية", 'default__en' => "Hello {name},\n\nThank you for applying for “{job}”. We have received your application; our HR team will review it and contact you if it is a match.\n\nQafesheh Industrial Group" ),
			),
		),

		/* ------------------------------------------------ Forms */
		'forms'       => array(
			'label'  => 'النماذج والرسائل',
			'icon'   => 'dashicons-email-alt',
			'fields' => array(
				'note_frm'             => array( 'type' => 'note', 'label' => 'استقبال الرسائل', 'desc' => 'كل الرسائل تُخزَّن في «الرسائل» بلوحة التحكم. افصل أكثر من إيميل بفاصلة.' ),
				'form_contact_email'   => array( 'type' => 'text', 'label' => 'إيميلات نموذج التواصل', 'desc' => 'فارغ = إيميل مدير الموقع.' ),
				'form_complaint_email' => array( 'type' => 'text', 'label' => 'إيميلات الشكاوى (إدارة الجودة)', 'desc' => 'فارغ = إيميل مدير الموقع.' ),
				'form_route_company'   => array( 'type' => 'switch', 'label' => 'إرسال نسخة لإيميل الشركة المختارة', 'desc' => 'يُقرأ من صفحة الشركة بالداشبورد.', 'default' => 1 ),
				'form_reply_days'      => array( 'type' => 'text', 'label' => 'مدة الرد على الشكاوى', 'i18n' => true, 'default' => '٣ أيام عمل', 'default__en' => '3 working days' ),
				'form_contact_success' => array( 'type' => 'text', 'label' => 'رسالة نجاح التواصل', 'i18n' => true, 'default' => 'وصلتنا رسالتك، وسنرد عليك خلال يومي عمل.', 'default__en' => 'Your message has arrived — we will reply within two working days.' ),
				'form_complaint_success' => array( 'type' => 'text', 'label' => 'رسالة نجاح الشكوى', 'desc' => '{tracking} = رقم المتابعة', 'i18n' => true, 'default' => 'وصلت رسالتك إلى إدارة الجودة. رقم المتابعة: {tracking}', 'default__en' => 'Your message reached our quality team. Tracking number: {tracking}' ),
				'form_track_subject'   => array( 'type' => 'text', 'label' => 'عنوان إيميل رقم المتابعة', 'i18n' => true, 'default' => 'رقم متابعة رسالتك: {tracking}', 'default__en' => 'Your tracking number: {tracking}' ),
				'form_track_body'      => array( 'type' => 'textarea', 'label' => 'نص إيميل رقم المتابعة', 'desc' => '{name} و {tracking}', 'i18n' => true, 'default' => "مرحباً {name}،\n\nوصلت رسالتك إلى إدارة الجودة في مجموعة قفيشة.\nرقم المتابعة: {tracking}\n\nتقدر تستعلم عن حالتها من صفحة الشكاوى والاقتراحات باستخدام هذا الرقم وبريدك الإلكتروني.\n\nمجموعة قفيشة الصناعية", 'default__en' => "Hello {name},\n\nYour message has reached the Qafesheh Group quality team.\nTracking number: {tracking}\n\nYou can check its status on the complaints & suggestions page using this number and your email.\n\nQafesheh Industrial Group" ),

				'note_frm_contact'     => array( 'type' => 'note', 'label' => 'صفحة التواصل — العمود الجانبي' ),
				'fc_brands_label'      => array( 'type' => 'text', 'label' => 'بطاقة العلامات', 'i18n' => true, 'default' => 'تواصلٌ مباشر مع العلامات', 'default__en' => 'Reach our brands directly' ),
				'fc_promo_title'       => array( 'type' => 'text', 'label' => 'بطاقة الشكاوى — العنوان', 'i18n' => true, 'default' => 'شكوى أو اقتراح؟', 'default__en' => 'A complaint or suggestion?' ),
				'fc_promo_text'        => array( 'type' => 'text', 'label' => 'بطاقة الشكاوى — النص', 'i18n' => true, 'default' => 'لدينا نموذجٌ مخصص يصل مباشرةً إلى إدارة الجودة برقم متابعة.', 'default__en' => 'We have a dedicated form that goes straight to our quality team, with a tracking number.' ),
				'fc_promo_link'        => array( 'type' => 'text', 'label' => 'بطاقة الشكاوى — الرابط', 'i18n' => true, 'default' => 'تقديم شكوى أو اقتراح', 'default__en' => 'Submit a complaint or suggestion' ),
				'fc_map_query'         => array( 'type' => 'text', 'label' => 'عنوان الخريطة', 'desc' => 'نص يُبحث عنه في خرائط جوجل، أو إحداثيات lat,lng', 'default' => 'King Abdullah II Industrial Estate, Sahab, Jordan' ),

				'note_frm_comp'        => array( 'type' => 'note', 'label' => 'صفحة الشكاوى — العمود الجانبي' ),
				'fp_pledge_title'      => array( 'type' => 'text', 'label' => 'التعهّد — العنوان', 'i18n' => true, 'default' => 'تعهُّدنا لك', 'default__en' => 'Our pledge to you' ),
				'fp_pledge_text'       => array( 'type' => 'textarea', 'label' => 'التعهّد — النص', 'i18n' => true, 'default' => 'كل رسالة تُقرأ، ولا تُغلق أي شكوى قبل الرد على صاحبها. المعلومات التي ترسلها تبقى داخل إدارة الجودة فقط.', 'default__en' => 'Every message is read, and no complaint is closed before we reply. What you send stays within the quality team only.' ),
				'fp_direct_title'      => array( 'type' => 'text', 'label' => 'التواصل المباشر — العنوان', 'i18n' => true, 'default' => 'تفضّل التواصل المباشر؟', 'default__en' => 'Prefer to talk directly?' ),
				'fp_track_title'       => array( 'type' => 'text', 'label' => 'متابعة رسالة — العنوان', 'i18n' => true, 'default' => 'متابعة رسالة سابقة', 'default__en' => 'Follow up a message' ),
				'fp_track_text'        => array( 'type' => 'text', 'label' => 'متابعة رسالة — النص', 'i18n' => true, 'default' => 'عندك رقم متابعة؟ أدخله مع بريدك لمعرفة حالة رسالتك.', 'default__en' => 'Have a tracking number? Enter it with your email to see its status.' ),
			),
		),

		/* ------------------------------------------------ Strings */
		'strings'     => array(
			'label'  => 'نصوص الواجهة',
			'icon'   => 'dashicons-translation',
			'fields' => array(
				'note_str'        => array( 'type' => 'note', 'label' => 'نصوص صغيرة تتكرر بالموقع', 'desc' => 'تُترجم من هنا مباشرة — لا حاجة لـString Translation.' ),
				'str_skip'        => array( 'type' => 'text', 'label' => 'تخطّي للمحتوى', 'i18n' => true, 'default' => 'تخطّي إلى المحتوى', 'default__en' => 'Skip to content' ),
				'str_home'        => array( 'type' => 'text', 'label' => 'الرئيسية (مسار التنقل)', 'i18n' => true, 'default' => 'الرئيسية', 'default__en' => 'Home' ),
				'str_menu'        => array( 'type' => 'text', 'label' => 'القائمة', 'i18n' => true, 'default' => 'القائمة', 'default__en' => 'Menu' ),
				'str_close'       => array( 'type' => 'text', 'label' => 'إغلاق', 'i18n' => true, 'default' => 'إغلاق', 'default__en' => 'Close' ),
				'str_read'        => array( 'type' => 'text', 'label' => 'اقرأ', 'i18n' => true, 'default' => 'اقرأ', 'default__en' => 'Read' ),
				'str_brand_page'  => array( 'type' => 'text', 'label' => 'صفحة العلامة', 'i18n' => true, 'default' => 'صفحة العلامة', 'default__en' => 'Brand page' ),
				'str_specialty'   => array( 'type' => 'text', 'label' => 'التخصص', 'i18n' => true, 'default' => 'التخصص', 'default__en' => 'Specialty' ),
				'str_markets'     => array( 'type' => 'text', 'label' => 'الأسواق', 'i18n' => true, 'default' => 'الأسواق', 'default__en' => 'Markets' ),
				'str_points'      => array( 'type' => 'text', 'label' => 'نقاط البيع', 'i18n' => true, 'default' => 'نقاط البيع', 'default__en' => 'Points of sale' ),
				'str_map'         => array( 'type' => 'text', 'label' => 'فتح بالخرائط', 'i18n' => true, 'default' => 'فتح بالخرائط', 'default__en' => 'Open in Maps' ),
				'str_back_top'    => array( 'type' => 'text', 'label' => 'العودة للأعلى', 'i18n' => true, 'default' => 'العودة للأعلى', 'default__en' => 'Back to top' ),
				'str_404'         => array( 'type' => 'text', 'label' => 'عنوان صفحة 404', 'i18n' => true, 'default' => 'الصفحة غير موجودة', 'default__en' => 'Page not found' ),
				'str_404_text'    => array( 'type' => 'text', 'label' => 'نص صفحة 404', 'i18n' => true, 'default' => 'ربما نُقلت الصفحة أو تغيّر رابطها.', 'default__en' => 'The page may have moved or its link changed.' ),
				'str_search'      => array( 'type' => 'text', 'label' => 'ابحث', 'i18n' => true, 'default' => 'ابحث في الموقع…', 'default__en' => 'Search the site…' ),
				'str_no_results'  => array( 'type' => 'text', 'label' => 'لا نتائج', 'i18n' => true, 'default' => 'لا يوجد محتوى هنا بعد.', 'default__en' => 'Nothing here yet.' ),
				'note_str_co'     => array( 'type' => 'note', 'label' => 'صفحة الشركة' ),
				'str_visit_shop'  => array( 'type' => 'text', 'label' => 'زيارة المتجر', 'desc' => '%s = اسم الشركة', 'i18n' => true, 'default' => 'زيارة متجر %s', 'default__en' => 'Visit the %s store' ),
				'str_catalog'     => array( 'type' => 'text', 'label' => 'تحميل الكتالوج', 'i18n' => true, 'default' => 'تحميل الكتالوج', 'default__en' => 'Download catalogue' ),
				'str_about'       => array( 'type' => 'text', 'label' => 'من نحن', 'i18n' => true, 'default' => 'من نحن', 'default__en' => 'About' ),
				'str_cats'        => array( 'type' => 'text', 'label' => 'التصنيفات', 'i18n' => true, 'default' => 'التصنيفات', 'default__en' => 'Categories' ),
				'str_cats_title'  => array( 'type' => 'text', 'label' => 'ماذا نصنع', 'i18n' => true, 'default' => 'ماذا نصنع', 'default__en' => 'What we make' ),
				'str_contact_co'  => array( 'type' => 'text', 'label' => 'تواصل مع', 'desc' => '%s = اسم الشركة', 'i18n' => true, 'default' => 'تواصل مع %s', 'default__en' => 'Contact %s' ),
				'str_sisters'     => array( 'type' => 'text', 'label' => 'باقي الشركات', 'i18n' => true, 'default' => 'تعرّف على باقي شركاتنا', 'default__en' => 'Meet our other companies' ),
				'note_str_svc'    => array( 'type' => 'note', 'label' => 'الخدمات والفروع' ),
				'str_svc_details' => array( 'type' => 'text', 'label' => 'تفاصيل الخدمة', 'i18n' => true, 'default' => 'تفاصيل الخدمة', 'default__en' => 'Service details' ),
				'str_includes'    => array( 'type' => 'text', 'label' => 'ما تشمله الخدمة', 'i18n' => true, 'default' => 'ما تشمله الخدمة', 'default__en' => 'What is included' ),
				'str_steps'       => array( 'type' => 'text', 'label' => 'مراحل العمل', 'i18n' => true, 'default' => 'مراحل العمل', 'default__en' => 'How it works' ),
				'str_request'     => array( 'type' => 'text', 'label' => 'اطلب هذه الخدمة', 'i18n' => true, 'default' => 'اطلب هذه الخدمة', 'default__en' => 'Request this service' ),
				'str_request_t'   => array( 'type' => 'text', 'label' => 'احكِ لنا عن طلبك', 'i18n' => true, 'default' => 'احكِ لنا عن طلبك', 'default__en' => 'Tell us about your request' ),
				'str_request_btn' => array( 'type' => 'text', 'label' => 'زر الطلب (بدون فورم)', 'i18n' => true, 'default' => 'أرسل طلبك', 'default__en' => 'Send your request' ),
				'str_other_svc'   => array( 'type' => 'text', 'label' => 'خدماتٌ أخرى', 'i18n' => true, 'default' => 'خدماتٌ أخرى', 'default__en' => 'Other services' ),
				'str_direct'      => array( 'type' => 'text', 'label' => 'تواصل مباشر', 'i18n' => true, 'default' => 'تواصل مباشر', 'default__en' => 'Direct contact' ),
				'str_filter'      => array( 'type' => 'text', 'label' => 'تصفية حسب العلامة', 'i18n' => true, 'default' => 'تصفية حسب العلامة', 'default__en' => 'Filter by brand' ),
				'str_all'         => array( 'type' => 'text', 'label' => 'الكل', 'i18n' => true, 'default' => 'الكل', 'default__en' => 'All' ),
				'str_on_map'      => array( 'type' => 'text', 'label' => 'الموقع على الخريطة', 'i18n' => true, 'default' => 'الموقع على الخريطة', 'default__en' => 'Show on map' ),
				'note_str_blog'   => array( 'type' => 'note', 'label' => 'المقالات والوظائف' ),
				'str_search_posts' => array( 'type' => 'text', 'label' => 'ابحث في المقالات', 'i18n' => true, 'default' => 'ابحث في المقالات', 'default__en' => 'Search articles' ),
				'str_featured'    => array( 'type' => 'text', 'label' => 'مقال مميّز', 'i18n' => true, 'default' => 'مقال مميّز', 'default__en' => 'Featured' ),
				'str_read_article' => array( 'type' => 'text', 'label' => 'اقرأ المقال', 'i18n' => true, 'default' => 'اقرأ المقال', 'default__en' => 'Read article' ),
				'str_minutes'     => array( 'type' => 'text', 'label' => 'دقائق قراءة', 'desc' => '%s = العدد', 'i18n' => true, 'default' => '%s دقائق قراءة', 'default__en' => '%s min read' ),
				'str_share'       => array( 'type' => 'text', 'label' => 'مشاركة', 'i18n' => true, 'default' => 'مشاركة', 'default__en' => 'Share' ),
				'str_copy'        => array( 'type' => 'text', 'label' => 'نسخ الرابط', 'i18n' => true, 'default' => 'نسخ الرابط', 'default__en' => 'Copy link' ),
				'str_copied'      => array( 'type' => 'text', 'label' => 'تم النسخ', 'i18n' => true, 'default' => 'تم النسخ', 'default__en' => 'Copied' ),
				'str_toc'         => array( 'type' => 'text', 'label' => 'في هذا المقال', 'i18n' => true, 'default' => 'في هذا المقال', 'default__en' => 'In this article' ),
				'str_related'     => array( 'type' => 'text', 'label' => 'مقالاتٌ ذات صلة', 'i18n' => true, 'default' => 'مقالاتٌ ذات صلة', 'default__en' => 'Related articles' ),
				'str_next'        => array( 'type' => 'text', 'label' => 'التالي', 'i18n' => true, 'default' => 'التالي', 'default__en' => 'Next' ),
				'str_prev'        => array( 'type' => 'text', 'label' => 'السابق', 'i18n' => true, 'default' => 'السابق', 'default__en' => 'Previous' ),
				'str_dept'        => array( 'type' => 'text', 'label' => 'القسم', 'i18n' => true, 'default' => 'القسم', 'default__en' => 'Department' ),
				'str_location'    => array( 'type' => 'text', 'label' => 'الموقع', 'i18n' => true, 'default' => 'الموقع', 'default__en' => 'Location' ),
				'str_jobtype'     => array( 'type' => 'text', 'label' => 'نوع الدوام', 'i18n' => true, 'default' => 'نوع الدوام', 'default__en' => 'Employment type' ),
				'str_experience'  => array( 'type' => 'text', 'label' => 'الخبرة', 'i18n' => true, 'default' => 'الخبرة', 'default__en' => 'Experience' ),
				'str_deadline'    => array( 'type' => 'text', 'label' => 'آخر موعد', 'i18n' => true, 'default' => 'آخر موعد', 'default__en' => 'Deadline' ),
				'str_vacancies'   => array( 'type' => 'text', 'label' => 'الشواغر المتاحة', 'i18n' => true, 'default' => 'الشواغر المتاحة', 'default__en' => 'Open positions' ),
				'str_jobs_count'  => array( 'type' => 'text', 'label' => 'عدد الوظائف', 'desc' => '%s = العدد', 'i18n' => true, 'default' => '%s وظائف', 'default__en' => '%s positions' ),
				'str_posted'      => array( 'type' => 'text', 'label' => 'نُشرت', 'desc' => '%s = التاريخ', 'i18n' => true, 'default' => 'نُشرت %s', 'default__en' => 'Posted %s' ),
				'str_details'     => array( 'type' => 'text', 'label' => 'التفاصيل', 'i18n' => true, 'default' => 'التفاصيل', 'default__en' => 'Details' ),
				'str_apply'       => array( 'type' => 'text', 'label' => 'قدّم على الوظيفة', 'i18n' => true, 'default' => 'قدّم على الوظيفة', 'default__en' => 'Apply now' ),
				'str_about_job'   => array( 'type' => 'text', 'label' => 'نبذة عن الوظيفة', 'i18n' => true, 'default' => 'نبذة عن الوظيفة', 'default__en' => 'About the role' ),
				'str_tasks'       => array( 'type' => 'text', 'label' => 'المهام والمسؤوليات', 'i18n' => true, 'default' => 'المهام والمسؤوليات', 'default__en' => 'Responsibilities' ),
				'str_reqs'        => array( 'type' => 'text', 'label' => 'المؤهلات المطلوبة', 'i18n' => true, 'default' => 'المؤهلات المطلوبة', 'default__en' => 'Requirements' ),
				'str_job_details' => array( 'type' => 'text', 'label' => 'تفاصيل الوظيفة', 'i18n' => true, 'default' => 'تفاصيل الوظيفة', 'default__en' => 'Role details' ),
				'str_other_jobs'  => array( 'type' => 'text', 'label' => 'شواغر أخرى', 'i18n' => true, 'default' => 'شواغر أخرى', 'default__en' => 'Other vacancies' ),
				'str_form_title'  => array( 'type' => 'text', 'label' => 'نموذج التقديم', 'i18n' => true, 'default' => 'نموذج التقديم', 'default__en' => 'Application form' ),
				'str_f_name'      => array( 'type' => 'text', 'label' => 'حقل الاسم', 'i18n' => true, 'default' => 'الاسم الكامل', 'default__en' => 'Full name' ),
				'str_f_phone'     => array( 'type' => 'text', 'label' => 'حقل الهاتف', 'i18n' => true, 'default' => 'رقم الهاتف', 'default__en' => 'Phone number' ),
				'str_f_email'     => array( 'type' => 'text', 'label' => 'حقل الإيميل', 'i18n' => true, 'default' => 'البريد الإلكتروني', 'default__en' => 'Email address' ),
				'str_f_dept'      => array( 'type' => 'text', 'label' => 'حقل القسم', 'i18n' => true, 'default' => 'القسم المطلوب', 'default__en' => 'Preferred department' ),
				'str_f_msg_job'   => array( 'type' => 'text', 'label' => 'رسالة (وظيفة)', 'i18n' => true, 'default' => 'لماذا تناسبك هذه الوظيفة؟', 'default__en' => 'Why is this role right for you?' ),
				'str_f_msg_open'  => array( 'type' => 'text', 'label' => 'رسالة (مفتوح)', 'i18n' => true, 'default' => 'نبذة عن خبرتك', 'default__en' => 'About your experience' ),
				'str_f_cv'        => array( 'type' => 'text', 'label' => 'إرفاق السيرة', 'i18n' => true, 'default' => 'إرفاق السيرة الذاتية', 'default__en' => 'Attach your CV' ),
				'str_f_cv_hint'   => array( 'type' => 'text', 'label' => 'تلميح الملف', 'desc' => '%s = الحجم', 'i18n' => true, 'default' => 'PDF أو Word — حتى %s ميغابايت', 'default__en' => 'PDF or Word — up to %s MB' ),
				'str_f_choose'    => array( 'type' => 'text', 'label' => 'اختيار ملف', 'i18n' => true, 'default' => 'اختيار ملف', 'default__en' => 'Choose file' ),
				'str_f_submit'    => array( 'type' => 'text', 'label' => 'إرسال الطلب', 'i18n' => true, 'default' => 'إرسال الطلب', 'default__en' => 'Submit application' ),
				'str_f_note'      => array( 'type' => 'text', 'label' => 'ملاحظة التأكيد', 'i18n' => true, 'default' => 'سيصلك إشعار تأكيد على بريدك', 'default__en' => 'You will receive a confirmation email' ),
				'str_f_sending'   => array( 'type' => 'text', 'label' => 'جارٍ الإرسال', 'i18n' => true, 'default' => 'جارٍ الإرسال…', 'default__en' => 'Sending…' ),
				'str_f_error'     => array( 'type' => 'text', 'label' => 'خطأ عام', 'i18n' => true, 'default' => 'تعذّر إرسال الطلب. تأكد من الحقول وحاول مرة أخرى.', 'default__en' => 'We could not send your application. Please check the fields and try again.' ),
				'str_f_required'  => array( 'type' => 'text', 'label' => 'حقل مطلوب', 'i18n' => true, 'default' => 'هذا الحقل مطلوب', 'default__en' => 'This field is required' ),
				'str_f_bad_email' => array( 'type' => 'text', 'label' => 'إيميل غير صالح', 'i18n' => true, 'default' => 'البريد الإلكتروني غير صالح', 'default__en' => 'Please enter a valid email' ),
				'str_f_bad_file'  => array( 'type' => 'text', 'label' => 'ملف غير مقبول', 'desc' => '%s = الحجم', 'i18n' => true, 'default' => 'الملف يجب أن يكون PDF أو Word وحجمه حتى %s ميغابايت', 'default__en' => 'The file must be PDF or Word and up to %s MB' ),
				'str_f_limit'     => array( 'type' => 'text', 'label' => 'تجاوز عدد المحاولات', 'i18n' => true, 'default' => 'أرسلت عدداً كبيراً من الطلبات. حاول لاحقاً.', 'default__en' => 'Too many submissions. Please try again later.' ),
				'str_f_consent'   => array( 'type' => 'text', 'label' => 'موافقة الخصوصية', 'i18n' => true, 'default' => 'أوافق على حفظ بياناتي لغرض التوظيف.', 'default__en' => 'I agree to my data being stored for recruitment.' ),
				'str_closed'      => array( 'type' => 'text', 'label' => 'الوظيفة مغلقة', 'i18n' => true, 'default' => 'انتهى التقديم على هذه الوظيفة.', 'default__en' => 'Applications for this role are closed.' ),
				'note_str_forms'  => array( 'type' => 'note', 'label' => 'نماذج التواصل والشكاوى' ),
				'str_f_target'    => array( 'type' => 'text', 'label' => 'الجهة المعنية', 'i18n' => true, 'default' => 'الجهة المعنية', 'default__en' => 'Department' ),
				'str_f_wholesale' => array( 'type' => 'text', 'label' => 'خيار الجملة والتصدير', 'i18n' => true, 'default' => 'الجملة والتصدير', 'default__en' => 'Wholesale & export' ),
				'str_f_subject'   => array( 'type' => 'text', 'label' => 'الموضوع', 'i18n' => true, 'default' => 'الموضوع', 'default__en' => 'Subject' ),
				'str_f_message'   => array( 'type' => 'text', 'label' => 'نص الرسالة', 'i18n' => true, 'default' => 'نص الرسالة', 'default__en' => 'Your message' ),
				'str_f_kind'      => array( 'type' => 'text', 'label' => 'نوع الرسالة', 'i18n' => true, 'default' => 'نوع الرسالة', 'default__en' => 'Message type' ),
				'str_k_complaint' => array( 'type' => 'text', 'label' => 'شكوى', 'i18n' => true, 'default' => 'شكوى', 'default__en' => 'Complaint' ),
				'str_k_suggestion' => array( 'type' => 'text', 'label' => 'اقتراح', 'i18n' => true, 'default' => 'اقتراح', 'default__en' => 'Suggestion' ),
				'str_k_praise'    => array( 'type' => 'text', 'label' => 'شكر وثناء', 'i18n' => true, 'default' => 'شكر وثناء', 'default__en' => 'Praise' ),
				'str_f_company'   => array( 'type' => 'text', 'label' => 'الشركة المعنية', 'i18n' => true, 'default' => 'الشركة المعنية', 'default__en' => 'Company' ),
				'str_f_branch'    => array( 'type' => 'text', 'label' => 'الفرع', 'i18n' => true, 'default' => 'الفرع', 'default__en' => 'Branch' ),
				'str_f_order'     => array( 'type' => 'text', 'label' => 'رقم الطلب', 'i18n' => true, 'default' => 'رقم الطلب أو الفاتورة (اختياري)', 'default__en' => 'Order or invoice number (optional)' ),
				'str_f_details'   => array( 'type' => 'text', 'label' => 'التفاصيل', 'i18n' => true, 'default' => 'اشرح لنا التفاصيل', 'default__en' => 'Tell us the details' ),
				'str_f_attach'    => array( 'type' => 'text', 'label' => 'إرفاق ملف', 'i18n' => true, 'default' => 'إرفاق صورة أو ملف (اختياري)', 'default__en' => 'Attach a photo or file (optional)' ),
				'str_f_attach_hint' => array( 'type' => 'text', 'label' => 'تلميح المرفق', 'i18n' => true, 'default' => 'JPG أو PNG أو PDF — حتى ٥ ميغابايت', 'default__en' => 'JPG, PNG or PDF — up to 5 MB' ),
				'str_f_bad_attach' => array( 'type' => 'text', 'label' => 'مرفق غير مقبول', 'i18n' => true, 'default' => 'المرفق يجب أن يكون صورة أو PDF وحجمه حتى %s ميغابايت', 'default__en' => 'The attachment must be an image or PDF up to %s MB' ),
				'str_send'        => array( 'type' => 'text', 'label' => 'إرسال', 'i18n' => true, 'default' => 'إرسال', 'default__en' => 'Send' ),
				'str_reply_note'  => array( 'type' => 'text', 'label' => 'ملاحظة الرد (تواصل)', 'i18n' => true, 'default' => 'نردّ خلال يومَي عمل', 'default__en' => 'We reply within two working days' ),
				'str_track_note'  => array( 'type' => 'text', 'label' => 'ملاحظة رقم المتابعة', 'i18n' => true, 'default' => 'سيصلك رقم متابعة على بريدك', 'default__en' => 'You will receive a tracking number by email' ),
				'str_track_number' => array( 'type' => 'text', 'label' => 'رقم المتابعة', 'i18n' => true, 'default' => 'رقم المتابعة', 'default__en' => 'Tracking number' ),
				'str_track_btn'   => array( 'type' => 'text', 'label' => 'استعلام', 'i18n' => true, 'default' => 'استعلام', 'default__en' => 'Check status' ),
				'str_track_none'  => array( 'type' => 'text', 'label' => 'لا نتيجة للاستعلام', 'i18n' => true, 'default' => 'لم نجد رسالة بهذا الرقم والبريد.', 'default__en' => 'No message matches this number and email.' ),
				'str_status_new'  => array( 'type' => 'text', 'label' => 'حالة: جديدة', 'i18n' => true, 'default' => 'استُلمت وبانتظار المراجعة', 'default__en' => 'Received, awaiting review' ),
				'str_status_progress' => array( 'type' => 'text', 'label' => 'حالة: قيد المتابعة', 'i18n' => true, 'default' => 'قيد المتابعة لدى إدارة الجودة', 'default__en' => 'Being handled by the quality team' ),
				'str_status_resolved' => array( 'type' => 'text', 'label' => 'حالة: تم الحل', 'i18n' => true, 'default' => 'تمت معالجتها والرد عليك', 'default__en' => 'Resolved and answered' ),
				'str_status_closed' => array( 'type' => 'text', 'label' => 'حالة: مغلقة', 'i18n' => true, 'default' => 'مغلقة', 'default__en' => 'Closed' ),
				'str_back_home'   => array( 'type' => 'text', 'label' => 'العودة للرئيسية', 'i18n' => true, 'default' => 'العودة للرئيسية', 'default__en' => 'Back to home' ),
			),
		),

		/* ------------------------------------------------ Animations */
		'animations'  => array(
			'label'  => 'الحركة',
			'icon'   => 'dashicons-controls-play',
			'fields' => array(
				'anim_on'       => array( 'type' => 'switch', 'label' => 'حركات الدخول', 'desc' => 'تُعطَّل تلقائياً لمن يفضّل تقليل الحركة في نظامه.', 'default' => 1 ),
				'anim_dur'      => array( 'type' => 'range', 'label' => 'مدة الحركة', 'min' => 400, 'max' => 1800, 'step' => 50, 'unit' => 'ms', 'default' => 1100 ),
				'anim_dist'     => array( 'type' => 'range', 'label' => 'مسافة الصعود', 'min' => 0, 'max' => 60, 'step' => 1, 'unit' => 'px', 'default' => 22 ),
				'anim_stagger'  => array( 'type' => 'range', 'label' => 'التدرّج بين العناصر', 'min' => 30, 'max' => 240, 'step' => 10, 'unit' => 'ms', 'default' => 90 ),
				'hero_parallax' => array( 'type' => 'switch', 'label' => 'عمق ناعم لصورة الهيرو', 'default' => 1 ),
				'count_up'      => array( 'type' => 'switch', 'label' => 'عدّ الأرقام تصاعدياً', 'default' => 1 ),
			),
		),

		/* ------------------------------------------------ Performance */
		'security'    => array(
			'label'  => 'الأمان',
			'icon'   => 'dashicons-lock',
			'fields' => array(
				'note_login'         => array( 'type' => 'note', 'label' => 'رابط الدخول الخاص', 'desc' => 'إخفاء wp-login.php خلف رابط سري — أول وأقوى خط دفاع ضد محاولات الاختراق الآلية. احفظ الرابط قبل الحفظ؛ للطوارئ ضع define( \'QAF_NO_HIDE_LOGIN\', true ); في wp-config.php.' ),
				'hide_login'         => array( 'type' => 'switch', 'label' => 'تفعيل رابط الدخول الخاص', 'default' => 1 ),
				'login_slug'         => array( 'type' => 'text', 'label' => 'الرابط', 'default' => 'qb-dash', 'desc' => 'مثال: qb-dash ← الدخول من /qb-dash. حروف إنجليزية وأرقام وشرطات فقط.' ),
				'note_harden'        => array( 'type' => 'note', 'label' => 'الحماية', 'desc' => 'إعدادات آمنة للمواقع المؤسسية — اتركها مفعّلة.' ),
				'limit_logins'       => array( 'type' => 'switch', 'label' => 'إيقاف مؤقت بعد ٦ محاولات دخول فاشلة', 'default' => 1 ),
				'hide_login_errors'  => array( 'type' => 'switch', 'label' => 'رسالة خطأ موحّدة عند فشل الدخول', 'default' => 1 ),
				'block_enum'         => array( 'type' => 'switch', 'label' => 'منع كشف أسماء المستخدمين', 'default' => 1 ),
				'disable_xmlrpc'     => array( 'type' => 'switch', 'label' => 'تعطيل XML-RPC', 'default' => 1 ),
				'disallow_file_edit' => array( 'type' => 'switch', 'label' => 'منع تعديل ملفات الثيم والإضافات من اللوحة', 'default' => 1 ),
				'security_headers'   => array( 'type' => 'switch', 'label' => 'ترويسات الحماية (clickjacking / sniffing)', 'default' => 1 ),
				'note_shield'        => array( 'type' => 'note', 'label' => 'درع الموقع', 'desc' => 'طبقات حماية إضافية تعمل قبل ما يبدأ ووردبريس. للطوارئ: define( \'QAF_NO_HARDENING\', true ); في wp-config.php.' ),
				'waf'                => array( 'type' => 'switch', 'label' => 'جدار حماية — صدّ محاولات الاختراق', 'default' => 1, 'desc' => 'يوقف أنماط الهجوم المعروفة (حقن SQL وسكربتات، التلصص على الملفات، أدوات الاختراق) قبل ما توصل للموقع.' ),
				'harden_htaccess'    => array( 'type' => 'switch', 'label' => 'حماية ملفات السيرفر', 'default' => 1, 'desc' => 'يحمي wp-config.php والسجلات، ويمنع تشغيل أي سكربت داخل مجلد الرفع. ينحذف تلقائياً إذا الاستضافة ما بتسمح.' ),
				'admin_alerts'       => array( 'type' => 'switch', 'label' => 'تنبيهات أمنية بالإيميل', 'default' => 1, 'desc' => 'عند إضافة مدير جديد، أو دخول مدير من جهاز جديد.' ),
				'disable_app_passwords' => array( 'type' => 'switch', 'label' => 'إيقاف كلمات مرور التطبيقات', 'default' => 1, 'desc' => 'بتسمح لتطبيقات تدخل بدون صفحة الدخول — الموقع ما بحتاجها.' ),
				'note_2fa'           => array( 'type' => 'note', 'label' => 'التحقق بخطوتين', 'desc' => 'رمز من تطبيق على الهاتف بعد كلمة المرور. كل شخص بيفعّله من ملفه الشخصي. للطوارئ: define( \'QAF_NO_2FA\', true ); في wp-config.php.' ),
				'twofa_enable'       => array( 'type' => 'switch', 'label' => 'تفعيل التحقق بخطوتين (تطبيق المصادقة)', 'default' => 1 ),
				'twofa_admins'       => array( 'type' => 'switch', 'label' => 'تذكير المدراء بتفعيله', 'default' => 1 ),
			),
		),
		'captcha'     => array(
			'label'  => 'الكابتشا',
			'icon'   => 'dashicons-privacy',
			'fields' => array(
				'captcha_note'       => array( 'type' => 'note', 'label' => 'كل نموذج محمي من روبوتات السبام', 'desc' => 'نماذج التواصل والتقديم، الدخول، نسيت كلمة المرور، والتعليقات. QB Shield مدمج: بدون حساب ولا كوكيز ولا إرسال بيانات لأي جهة — متصفح الزائر بيحل لغزاً صغيراً رخيصاً على الإنسان ومكلفاً جداً على الروبوت.' ),
				'captcha_provider'   => array( 'type' => 'select', 'label' => 'نوع الحماية', 'default' => 'shield', 'options' => array( 'shield' => 'QB Shield — مدمج (موصى به)', 'turnstile' => 'Cloudflare Turnstile (يحتاج مفاتيح)', 'recaptcha' => 'Google reCAPTCHA (يحتاج مفاتيح)', 'off' => 'متوقف' ) ),
				'captcha_mode'       => array( 'type' => 'select', 'label' => 'الشكل', 'default' => 'invisible', 'options' => array( 'invisible' => 'غير مرئي — بدون أي ضغطة', 'checkbox' => 'مربع «لست روبوتاً»' ), 'desc' => 'غير المرئي الأنعم للزوار، والمربع طمأنة ظاهرة.' ),
				'captcha_strength'   => array( 'type' => 'select', 'label' => 'قوة QB Shield', 'default' => 'balanced', 'options' => array( 'light' => 'خفيف — الأسرع على الهواتف القديمة', 'balanced' => 'متوازن (موصى به)', 'strong' => 'قوي — إذا وصل سبام' ), 'desc' => 'الروبوتات الكثيفة بتاخذ ألغاز أصعب تلقائياً.' ),
				'captcha_site_key'   => array( 'type' => 'text', 'label' => 'Site key (Turnstile / reCAPTCHA فقط)', 'default' => '', 'desc' => 'reCAPTCHA: مفاتيح v2 للمربع و v3 لغير المرئي. بدون مفاتيح يُستخدم QB Shield.' ),
				'captcha_secret_key' => array( 'type' => 'text', 'label' => 'Secret key (Turnstile / reCAPTCHA فقط)', 'default' => '' ),
				'captcha_login'      => array( 'type' => 'switch', 'label' => 'حماية الدخول ونسيت كلمة المرور', 'default' => 1 ),
				'captcha_comments'   => array( 'type' => 'switch', 'label' => 'حماية التعليقات', 'default' => 1 ),
			),
		),
		'performance' => array(
			'label'  => 'الأداء',
			'icon'   => 'dashicons-performance',
			'fields' => array(
				'perf_emoji'     => array( 'type' => 'switch', 'label' => 'إزالة سكربت الإيموجي', 'default' => 1 ),
				'perf_embed'     => array( 'type' => 'switch', 'label' => 'إزالة wp-embed', 'default' => 1 ),
				'perf_dashicons' => array( 'type' => 'switch', 'label' => 'إزالة Dashicons للزوار', 'default' => 1 ),
				'perf_migrate'   => array( 'type' => 'switch', 'label' => 'إزالة jquery-migrate', 'default' => 1 ),
				'perf_block_css' => array( 'type' => 'switch', 'label' => 'إزالة ستايلات Gutenberg خارج المقالات', 'default' => 1 ),
				'perf_speculate' => array( 'type' => 'switch', 'label' => 'تحميل مسبق للصفحة عند مرور المؤشر (Speculation Rules)', 'default' => 1, 'desc' => 'ينقل الزائر بين الصفحات شبه فوراً — المتصفحات الداعمة فقط.' ),
				'perf_cv'        => array( 'type' => 'switch', 'label' => 'تخطّي رسم الأقسام خارج الشاشة (content-visibility)', 'default' => 1 ),
				'perf_preload'   => array( 'type' => 'switch', 'label' => 'Preload لصورة الهيرو والخطوط الحرجة', 'default' => 1 ),
			),
		),

		/* ------------------------------------------------ Integrations */
		'integrations' => array(
			'label'  => 'الربط والتتبّع',
			'icon'   => 'dashicons-chart-area',
			'fields' => array(
				'ga4_id' => array( 'type' => 'text', 'label' => 'Google Analytics 4', 'desc' => 'مثال: G-XXXXXXXXXX' ),
				'gtm_id' => array( 'type' => 'text', 'label' => 'Google Tag Manager', 'desc' => 'مثال: GTM-XXXXXXX' ),
			),
		),
	);

	return apply_filters( 'qaf_options_schema', $schema );
}
