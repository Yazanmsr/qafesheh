<?php
/**
 * Seed content — ONE bilingual source used by the importer, the front-page fallback
 * and the default values of every QB widget. Real client data, no lorem ipsum.
 *
 * Shape per item: key (stable import id), shared (language-neutral), ar, en.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

/**
 * Pick a language branch with Arabic fallback.
 *
 * @param array       $item Item with ar/en keys.
 * @param string|null $lang Language.
 * @return array
 */
function qaf_seed_lang( $item, $lang = null ) {
	$lang = $lang ? $lang : qaf_current_lang();
	return $item[ $lang ] ?? $item['ar'] ?? array();
}

/**
 * Homepage copy (per language).
 *
 * @param string|null $lang Language.
 * @return array
 */
function qaf_seed_home( $lang = null ) {
	$home = array(
		'ar' => array(
			'hero'         => array(
				'eyebrow' => 'EST. 1968 — AMMAN, JORDAN',
				'title'   => "مجموعةٌ صناعية أردنية\nفي الأزياء التراثية الفاخرة",
				'text'    => 'ثلاث علاماتٍ متخصصة، ومصنعٌ واحد في المدينة الصناعية بسحاب، يخدمان السوق المحلي وأسواق الخليج والوطن العربي منذ عقود.',
				'btn1'    => 'قطاعات الأعمال',
				'btn1_url' => '#companies',
				'btn2'    => 'نبذة عن المجموعة',
				'btn2_url' => '/about/',
				'slides'  => array(
					array( 'image' => 'factory-exterior.jpg', 'position' => 'center', 'eyebrow' => 'EST. 1968 — AMMAN, JORDAN', 'title' => "مجموعةٌ صناعية أردنية\nفي الأزياء التراثية الفاخرة", 'text' => 'ثلاث علاماتٍ متخصصة، ومصنعٌ واحد في المدينة الصناعية بسحاب، يخدمان السوق المحلي وأسواق الخليج والوطن العربي منذ عقود.', 'btn1' => 'قطاعات الأعمال', 'btn1_url' => '#companies', 'btn2' => 'نبذة عن المجموعة', 'btn2_url' => '/about/' ),
					array( 'image' => 'fur-stitching.jpg', 'position' => 'center', 'eyebrow' => 'MADE IN SAHAB — 100+ ARTISANS', 'title' => "أيدٍ تعرفُ الغرزة\nوخامةٌ لا نساوم عليها", 'text' => 'أكثر من مئة حِرَفيٍّ وعامل يصنعون كل قطعة داخل مصنعنا، من القصّ حتى التشطيب اليدوي الأخير.', 'btn1' => 'القدرات التصنيعية', 'btn1_url' => '/services/', 'btn2' => 'انضم إلى فريقنا', 'btn2_url' => '/careers/' ),
					array( 'image' => 'three-generations.jpg', 'position' => 'right center', 'eyebrow' => 'QAFESHEH · AL JAZI · MAKAREM', 'title' => "إرثٌ يُلبَس\nويُورَّث من جيلٍ إلى جيل", 'text' => 'فرواتٌ وبشوت، أثوابٌ رجالية، ومطرزاتٌ شرقية — ثلاث هوياتٍ تجمعها حِرفةٌ واحدة ومعيارٌ واحد للجودة.', 'btn1' => 'تعرّف على علاماتنا', 'btn1_url' => '#companies', 'btn2' => 'أقرب فرع', 'btn2_url' => '/branches/' ),
				),
				'stats'   => array(
					array( 'num' => '100', 'suffix' => '+', 'label' => 'عاملٌ وحِرَفي' ),
					array( 'num' => '03', 'suffix' => '', 'label' => 'علامات تجارية' ),
					array( 'num' => '05', 'suffix' => '', 'label' => 'معارض في المملكة' ),
					array( 'num' => '01', 'suffix' => '', 'label' => 'مصنع في سحاب' ),
				),
			),
			'statement'    => array(
				'num'      => '01',
				'label'    => 'المجموعة',
				'title'    => 'بدأ المصنع بأيدٍ عاملةٍ قليلة وماكيناتٍ قليلة، واستمرّ بالتطوّر إلى يومنا هذا.',
				'col1'     => 'أصبح عدد الأيدي العاملة يتجاوز مئة عاملٍ من الطراز الأول، مع أحدث الماكينات والآلات والمعدات المتخصصة التي تساعدنا في التطوّر والابتكار.',
				'col2'     => 'تم بيع ملايين الفروات في الأسواق المحلية والعالمية، ونسعى دائماً للوصول إلى أكبر عددٍ ممكن من التجّار والزبائن وتلبية جميع احتياجاتهم.',
				'link'     => 'نبذة كاملة عن المجموعة',
				'link_url' => '/about/',
			),
			'companies'    => array(
				'num'   => '02',
				'label' => 'قطاعات الأعمال',
				'title' => 'ثلاث علاماتٍ متخصصة',
				'text'  => 'كل علامةٍ تخدم قطاعها الخاص، وتشترك مع المجموعة في المصنع والخامة ومعايير الجودة.',
			),
			'capabilities' => array(
				'num'   => '03',
				'label' => 'القدرات التصنيعية',
				'title' => 'مصنعٌ واحد يخدم العلامات الثلاث',
				'rows'  => array(
					array( 'label' => 'الموقع', 'value' => 'مدينة الملك عبدالله الثاني الصناعية — سحاب' ),
					array( 'label' => 'القوى العاملة', 'value' => 'أكثر من ١٠٠ عامل وحِرَفي' ),
					array( 'label' => 'خطوط الإنتاج', 'value' => 'فروات · بشوت · أثواب · مطرزات' ),
					array( 'label' => 'الدوام', 'value' => '٧:٠٠ — ١٩:٠٠' ),
					array( 'label' => 'المنشأ', 'value' => 'صناعة أردنية ١٠٠٪' ),
					array( 'label' => 'التصدير', 'value' => 'الخليج والوطن العربي' ),
				),
			),
			'services'     => array(
				'num'      => '04',
				'label'    => 'الخدمات',
				'title'    => 'ما نقدّمه للتجّار والأفراد',
				'link'     => 'كل الخدمات',
			),
			'standards'    => array(
				'num'   => '05',
				'label' => 'المعايير',
				'rows'  => array(
					array( 'title' => 'الجودة قبل الكمّ', 'text' => 'تُستخدم أفضل أنواع الأقمشة والخيوط، ما يضمن المتانة والمحافظة على الرونق سنواتٍ طويلة.' ),
					array( 'title' => 'صناعةٌ وطنية', 'text' => 'إنتاجٌ أردني بالكامل من مصنعنا في سحاب، بأيدٍ محلية مدرَّبة على الحِرفة جيلاً بعد جيل.' ),
					array( 'title' => 'الإرث والتطوير', 'text' => 'نعتزّ بأصالة الفروة ونورّثها من جيلٍ إلى جيل، مع استحداث تصاميم تناسب جميع الأذواق حول العالم.' ),
				),
			),
			'news'         => array(
				'num'   => '06',
				'label' => 'المقالات والأخبار',
				'title' => 'آخر ما نشرناه',
				'link'  => 'الأرشيف',
			),
			'branches'     => array(
				'num'   => '07',
				'label' => 'الفروع',
				'title' => "خمسة معارضَ\nومصنعٌ واحد",
			),
			'cta'          => array(
				'title'    => 'للاستفسارات التجارية وطلبات الجملة والتصدير',
				'text'     => 'فريق المجموعة يستقبل طلبات الشراكة والتوريد على مدار العام.',
				'btn1'     => 'تواصل معنا',
				'btn1_url' => '/contact/',
				'btn2'     => 'الوظائف',
				'btn2_url' => '/careers/',
			),
		),
		'en' => array(
			'hero'         => array(
				'eyebrow'  => 'EST. 1968 — AMMAN, JORDAN',
				'title'    => "A Jordanian industrial group\nin luxury heritage wear",
				'slides'   => array(
					array( 'image' => 'factory-exterior.jpg', 'position' => 'center', 'eyebrow' => 'EST. 1968 — AMMAN, JORDAN', 'title' => "A Jordanian industrial group\nin luxury heritage wear", 'text' => 'Three specialised houses and one factory in Sahab Industrial City, serving Jordan, the Gulf and the Arab world for decades.', 'btn1' => 'Our businesses', 'btn1_url' => '#companies', 'btn2' => 'About the group', 'btn2_url' => '/en/about/' ),
					array( 'image' => 'fur-stitching.jpg', 'position' => 'center', 'eyebrow' => 'MADE IN SAHAB — 100+ ARTISANS', 'title' => "Hands that know the stitch,\nfabric we never compromise on", 'text' => 'More than a hundred artisans and workers make every piece inside our factory — from the first cut to the last hand finish.', 'btn1' => 'Manufacturing', 'btn1_url' => '/en/services/', 'btn2' => 'Join our team', 'btn2_url' => '/en/careers/' ),
					array( 'image' => 'three-generations.jpg', 'position' => 'right center', 'eyebrow' => 'QAFESHEH · AL JAZI · MAKAREM', 'title' => "A heritage worn\nand handed down", 'text' => 'Farwa and bisht, men’s thobes and oriental embroidery — three identities joined by one craft and one standard of quality.', 'btn1' => 'Meet our brands', 'btn1_url' => '#companies', 'btn2' => 'Nearest branch', 'btn2_url' => '/en/branches/' ),
				),
				'text'     => 'Three specialised houses and one factory in Sahab Industrial City, serving Jordan, the Gulf and the Arab world for decades.',
				'btn1'     => 'Our businesses',
				'btn1_url' => '#companies',
				'btn2'     => 'About the group',
				'btn2_url' => '/en/about/',
				'stats'    => array(
					array( 'num' => '100', 'suffix' => '+', 'label' => 'Workers & artisans' ),
					array( 'num' => '03', 'suffix' => '', 'label' => 'Brands' ),
					array( 'num' => '05', 'suffix' => '', 'label' => 'Showrooms in Jordan' ),
					array( 'num' => '01', 'suffix' => '', 'label' => 'Factory in Sahab' ),
				),
			),
			'statement'    => array(
				'num'      => '01',
				'label'    => 'THE GROUP',
				'title'    => 'The factory began with a few hands and a few machines — and has kept evolving ever since.',
				'col1'     => 'Today more than a hundred first-class workers operate the latest specialised machinery and equipment, driving our craft forward.',
				'col2'     => 'Millions of fur coats have been sold in local and international markets, and we keep reaching more traders and customers every season.',
				'link'     => 'The full group story',
				'link_url' => '/en/about/',
			),
			'companies'    => array(
				'num'   => '02',
				'label' => 'OUR BUSINESSES',
				'title' => 'Three specialised houses',
				'text'  => 'Each brand serves its own sector while sharing the group’s factory, materials and quality standards.',
			),
			'capabilities' => array(
				'num'   => '03',
				'label' => 'MANUFACTURING',
				'title' => 'One factory behind three brands',
				'rows'  => array(
					array( 'label' => 'Location', 'value' => 'King Abdullah II Industrial City — Sahab' ),
					array( 'label' => 'Workforce', 'value' => '100+ workers and artisans' ),
					array( 'label' => 'Product lines', 'value' => 'Fur · Bisht · Thobes · Embroidery' ),
					array( 'label' => 'Hours', 'value' => '07:00 — 19:00' ),
					array( 'label' => 'Origin', 'value' => '100% made in Jordan' ),
					array( 'label' => 'Export', 'value' => 'The Gulf and the Arab world' ),
				),
			),
			'services'     => array(
				'num'   => '04',
				'label' => 'SERVICES',
				'title' => 'What we offer traders and individuals',
				'link'  => 'All services',
			),
			'standards'    => array(
				'num'   => '05',
				'label' => 'STANDARDS',
				'rows'  => array(
					array( 'title' => 'Quality over quantity', 'text' => 'Only the finest fabrics and threads, built to last and keep their lustre for years.' ),
					array( 'title' => 'National craft', 'text' => 'Entirely Jordanian production from our Sahab factory, by local hands trained in the craft for generations.' ),
					array( 'title' => 'Heritage & renewal', 'text' => 'We honour the authentic farwa and pass it on — while designing new pieces for every taste around the world.' ),
				),
			),
			'news'         => array(
				'num'   => '06',
				'label' => 'NEWS & ARTICLES',
				'title' => 'Latest from the group',
				'link'  => 'Archive',
			),
			'branches'     => array(
				'num'   => '07',
				'label' => 'BRANCHES',
				'title' => "Five showrooms,\none factory",
			),
			'cta'          => array(
				'title'    => 'Trade, wholesale and export enquiries',
				'text'     => 'The group team welcomes partnership and supply requests all year round.',
				'btn1'     => 'Contact us',
				'btn1_url' => '/en/contact/',
				'btn2'     => 'Careers',
				'btn2_url' => '/en/careers/',
			),
		),
	);

	return qaf_seed_lang( $home, $lang );
}

/**
 * Brand terms.
 *
 * @return array
 */
function qaf_seed_brands() {
	return array(
		'qafesheh' => array( 'ar' => 'قفيشة', 'en' => 'Qafesheh' ),
		'aljazi'   => array( 'ar' => 'الجازي', 'en' => 'Al Jazi' ),
		'makarem'  => array( 'ar' => 'مكارم', 'en' => 'Makarem' ),
	);
}

/**
 * Companies.
 *
 * @return array
 */
function qaf_seed_companies() {
	return array(
		array(
			'key'    => 'company-qafesheh',
			'shared' => array(
				'order'  => 1,
				'image'  => 'boutique-coats.jpg',
				'brand'  => 'qafesheh',
				'meta'   => array( 'en_name' => 'QAFESHEH', 'sector' => 'FUR & BISHT', 'color' => '#C9A227', 'shop_url' => 'https://qafesha.com', 'img_pos' => 'center', 'logo' => 'assets/images/logos/logo-qafesheh-seal.png', 'logo_dark' => 'assets/images/logos/logo-qafesheh-seal.png', 'gallery' => array( 'boutique-coats.jpg', 'fur-stitching.jpg', 'workshop-cutting.jpg', 'atelier-bench.jpg', 'model-thobe.jpg' ), 'email' => 'info@qafesheh.com', 'phone' => '+962 79 881 7390', 'instagram' => 'https://www.instagram.com/qafesheh.factory/', 'facebook' => 'https://www.facebook.com/qafesheh.factory', 'youtube' => 'https://www.youtube.com/@Qafesheh' ),
			),
			'ar'     => array(
				'title'   => 'قفيشة',
				'excerpt' => 'السلسلة الرائدة والوحيدة على مستوى المملكة المختصة بالفروات، والمصدر الأول للفراء والبشت في الخليج والوطن العربي.',
				'content' => '<p>قفيشة هي السلسلة الرائدة والوحيدة على مستوى المملكة المختصة بالفروات، من خلال مصنعنا المتميّز في سحاب وشبكة فروعنا المنتشرة حول المملكة.</p><p>نقدّم مجموعةً متنوعة من التصاميم الفاخرة التي تجمع بين الجودة العالية والأناقة العصرية لتلبية جميع الأذواق، ونسعى دائماً لتحقيق التميّز والابتكار في منتجاتنا لضمان رضى عملائنا وترك بصمةٍ في عالم الموضة الفاخرة.</p>',
				'meta'    => array( 'specialty' => 'فروات · بشوت · صدريات · أشمغة', 'markets' => 'الأردن · الخليج · الوطن العربي', 'points' => 'المصنع + معرض وسط البلد', 'tagline' => 'المصدر الأول للفراء والبشت في الخليج والوطن العربي — المصنع الأم وأقدم علاماتنا.', 'intro_t' => "متخصصون\nبالفراء والبشت", 'quote_t' => 'ما يهمُّنا حقاً هو أنت', 'quote' => 'تم بيع ملايين الفروات في الأسواق المحلية والعالمية، ونسعى دائماً للوصول إلى أكبر عددٍ ممكن من التجّار والزبائن وتلبية جميع احتياجاتكم.', 'address' => "المصنع — مدينة الملك عبدالله الثاني الصناعية، سحاب\nالمعرض — شارع الملك طلال، وسط البلد", 'categories' => array( array( 'name' => 'فروات', 'image' => 'family-courtyard.jpg' ), array( 'name' => 'بشوت', 'image' => 'showroom.jpg' ), array( 'name' => 'صدريات', 'image' => 'boutique-coats.jpg' ), array( 'name' => 'أشمغة', 'image' => 'textiles-stack.jpg' ) ) ),
			),
			'en'     => array(
				'title'   => 'Qafesheh',
				'excerpt' => 'The Kingdom’s leading — and only — chain dedicated to fur, and the first source of farwa and bisht across the Gulf and the Arab world.',
				'content' => '<p>Qafesheh is the oldest and weightiest house in the group. It began as a fur workshop in Sahab Industrial City and grew into the Kingdom’s leading chain dedicated to fur.</p><h2>A heritage passed on</h2><p>We honour the authentic farwa and hand it down through generations — a piece worn on the great occasions, kept for years, then passed from father to son.</p>',
				'meta'    => array( 'specialty' => 'Farwa · Bisht · Vests · Shemagh', 'markets' => 'Jordan · Gulf · Arab world', 'points' => 'Factory + Downtown showroom', 'tagline' => 'The first source of farwa and bisht in the Gulf and the Arab world — the mother factory and our oldest house.', 'intro_t' => "Specialists in\nfur and bisht", 'quote_t' => 'What truly matters is you', 'quote' => 'Millions of farwas have been sold in local and international markets, and we keep working to reach more traders and customers and meet every need.', 'address' => "Factory — King Abdullah II Industrial City, Sahab\nShowroom — King Talal St., Downtown", 'categories' => array( array( 'name' => 'Farwa', 'image' => 'family-courtyard.jpg' ), array( 'name' => 'Bisht', 'image' => 'showroom.jpg' ), array( 'name' => 'Vests', 'image' => 'boutique-coats.jpg' ), array( 'name' => 'Shemagh', 'image' => 'textiles-stack.jpg' ) ) ),
			),
		),
		array(
			'key'    => 'company-aljazi',
			'shared' => array(
				'order' => 2,
				'image' => 'showroom.jpg',
				'brand' => 'aljazi',
				'meta'  => array( 'en_name' => 'AL JAZI', 'sector' => 'ARABIAN THOBES', 'color' => '#E7E1D3', 'shop_url' => 'https://aljazi.com', 'img_pos' => 'left center', 'logo' => 'assets/images/logos/logo-aljazi-light.png', 'logo_dark' => 'assets/images/logos/logo-aljazi-dark.png', 'gallery' => array( 'showroom.jpg', 'textiles-stack.jpg', 'family-desert.jpg', 'three-generations.jpg', 'boutique-coats.jpg' ), 'email' => 'info@aljazifactory.com', 'phone' => '+962 79 990 0042', 'instagram' => 'https://www.instagram.com/aljazi.brand/' ),
			),
			'ar'     => array(
				'title'   => 'الجازي',
				'excerpt' => 'أثوابٌ عربية أصيلة بتصاميم تمزج التراث بالأناقة العصرية، موجّهة للرجل الباحث عن التميّز والهوية معاً.',
				'content' => '<p>الجازي هي الوجهة المثالية لكل رجلٍ عربي يعشق الأناقة الممزوجة بالهوية الثقافية. أثوابٌ عربية أصيلة بقصّاتٍ معاصرة وخاماتٍ مختارة، تُصنع في مصنع المجموعة بسحاب.</p><p>قوة الجازي في الامتناع: لوحةٌ أحادية نقية — أبيض واسع وأسود حاد — واللون الوحيد المسموح هو لون القماش نفسه.</p>',
				'meta'    => array( 'specialty' => 'أثواب · أشمغة · لبس الإحرام', 'markets' => 'الأردن · الخليج', 'points' => 'وسط البلد + طبربور', 'tagline' => 'الوجهة المثالية لكل رجلٍ عربي يعشق الأناقة الممزوجة بالهوية الثقافية.', 'intro_t' => "أثوابٌ عربية\nبلمسةٍ عصرية", 'quote_t' => 'الأناقة التي تشبهك', 'quote' => 'ثوبٌ لا يصرخ، بل يُلاحَظ — خامةٌ مختارة، قصّةٌ مضبوطة، وهويةٌ عربية في كل تفصيل.', 'address' => "وسط البلد — شارع الملك طلال\nطبربور — شارع طارق", 'categories' => array( array( 'name' => 'أثواب', 'image' => 'showroom.jpg' ), array( 'name' => 'الصيفي', 'image' => 'family-desert.jpg' ), array( 'name' => 'لبس الإحرام', 'image' => 'three-generations.jpg' ), array( 'name' => 'أشمغة', 'image' => 'textiles-stack.jpg' ) ) ),
			),
			'en'     => array(
				'title'   => 'Al Jazi',
				'excerpt' => 'Authentic Arabian thobes that blend heritage with contemporary elegance, for the man who seeks distinction and identity.',
				'content' => '<p>Al Jazi is the destination for every Arab man who loves elegance rooted in cultural identity — authentic thobes with contemporary cuts and chosen fabrics.</p><h2>Strength in restraint</h2><p>A pure monochrome palette — generous white, sharp black — where the only colour allowed is the fabric itself.</p>',
				'meta'    => array( 'specialty' => 'Thobes · Shemagh · Ihram', 'markets' => 'Jordan · Gulf', 'points' => 'Downtown + Tabarbour', 'tagline' => 'The destination for every Arab man who loves elegance rooted in cultural identity.', 'intro_t' => "Arabian thobes,\ncontemporary cut", 'quote_t' => 'Elegance that feels like you', 'quote' => 'A thobe that does not shout, but is noticed — chosen fabric, precise cut and Arab identity in every detail.', 'address' => "Downtown — King Talal St.\nTabarbour — Tariq St.", 'categories' => array( array( 'name' => 'Thobes', 'image' => 'showroom.jpg' ), array( 'name' => 'Summer', 'image' => 'family-desert.jpg' ), array( 'name' => 'Ihram', 'image' => 'three-generations.jpg' ), array( 'name' => 'Shemagh', 'image' => 'textiles-stack.jpg' ) ) ),
			),
		),
		array(
			'key'    => 'company-makarem',
			'shared' => array(
				'order' => 3,
				'image' => 'model-thobe.jpg',
				'brand' => 'makarem',
				'meta'  => array( 'en_name' => 'MAKAREM', 'sector' => 'ORIENTAL EMBROIDERY', 'color' => '#A51E25', 'shop_url' => 'https://makarem.com', 'img_pos' => 'right center', 'logo' => 'assets/images/logos/logo-makarem-light.png', 'logo_dark' => 'assets/images/logos/logo-makarem-dark.png', 'gallery' => array( 'model-thobe.jpg', 'embroidery-hands.jpg', 'atelier-bench.jpg', 'family-courtyard.jpg', 'textiles-stack.jpg' ), 'email' => 'makaremembroideries@gmail.com', 'phone' => '+962 79 676 0028' ),
			),
			'ar'     => array(
				'title'   => 'مكارم',
				'excerpt' => 'مطرزاتٌ شرقية وفروات نسائية مصنّعة من أفخر الأقمشة العالمية، تحت إشراف أمهر الأيادي المختصة.',
				'content' => '<p>عندما تبحث عن مطرزات مكارم تبحث عن أصالة المطرزات العربية — غرزةٌ تحت إشراف أمهر الأيادي المختصة، وتصاميم تجمع الموروث الشرقي بذوق اليوم.</p><p>الأوسع شبكةً بين علامات المجموعة، بخمسة معارض في عمّان وإربد، ومجموعاتٍ تتجدد كل موسم من الأثواب والفساتين والقفاطين والفروات النسائية.</p>',
				'meta'    => array( 'specialty' => 'أثواب · فساتين · قفاطين · فروات', 'markets' => 'الأردن · الخليج', 'points' => '٥ معارض', 'tagline' => 'للمطرزات الشرقية والفراء — تحت إشراف أمهر الأيادي المختصة.', 'intro_t' => "أصالة المطرزات\nالعربية", 'quote_t' => 'الغرزة هي الحكاية', 'quote' => 'التطريز في مكارم ليس زخرفةً تُضاف في النهاية، بل هو المنتج نفسه — والمعيّنات في شعارنا مأخوذة من غرزته.', 'address' => "وسط البلد — شارع الملك طلال\nطبربور · تلاع العلي · إربد", 'categories' => array( array( 'name' => 'أثواب', 'image' => 'model-thobe.jpg' ), array( 'name' => 'فساتين', 'image' => 'family-courtyard.jpg' ), array( 'name' => 'قفطان تطريز', 'image' => 'embroidery-hands.jpg' ), array( 'name' => 'فروات', 'image' => 'boutique-coats.jpg' ) ) ),
			),
			'en'     => array(
				'title'   => 'Makarem',
				'excerpt' => 'Oriental embroidery and women’s farwa crafted from the finest fabrics, under the most skilled specialised hands.',
				'content' => '<p>Looking for Makarem embroidery means looking for authentic Arabic embroidery — every stitch supervised by skilled hands, every design joining eastern heritage with today’s taste.</p><h2>The craft</h2><p>At Makarem, embroidery is not an ornament added at the end — it is the product itself.</p>',
				'meta'    => array( 'specialty' => 'Thobes · Dresses · Kaftans · Farwa', 'markets' => 'Jordan · Gulf', 'points' => '5 showrooms', 'tagline' => 'Oriental embroidery and fur — under the most skilled specialised hands.', 'intro_t' => "Authentic Arabic\nembroidery", 'quote_t' => 'The stitch is the story', 'quote' => 'At Makarem embroidery is not an ornament added at the end — it is the product itself, and the diamonds in our mark come from its stitch.', 'address' => "Downtown — King Talal St.\nTabarbour · Tla’a Al-Ali · Irbid", 'categories' => array( array( 'name' => 'Thobes', 'image' => 'model-thobe.jpg' ), array( 'name' => 'Dresses', 'image' => 'family-courtyard.jpg' ), array( 'name' => 'Embroidered kaftan', 'image' => 'embroidery-hands.jpg' ), array( 'name' => 'Farwa', 'image' => 'boutique-coats.jpg' ) ) ),
			),
		),
	);
}

/**
 * Services (the six in the approved design).
 *
 * @return array
 */
function qaf_seed_services() {
	$rows = array(
		array(
			'icon'  => 'factory',
			'image' => 'atelier-bench.jpg',
			'gal'   => array( 'workshop-cutting.jpg', 'atelier-bench.jpg' ),
			'ar'    => array(
				'التصنيع والإنتاج',
				'خطوط إنتاج متكاملة داخل مصنعنا في سحاب: الفروات الطبيعية والصناعية، البشوت، الصدريات، الأثواب والمطرزات — بطاقةٍ إنتاجية تخدم الطلبات الكبيرة دون المساس بالمعيار.',
				'يقوم مصنع قفيشة بإنتاج وتصنيع مجموعةٍ كبيرة ومتنوعة من الفروات الطبيعية والصناعية بجميع أنواعها، والأشمغة والصدريات والجواكت، المصنوعة من أفضل الخامات وأكثرها جودة مقابل أفضل سعر.',
				"اختيار الخامة من موردين معتمدين، مع عيّنةٍ تُعرض على العميل قبل بدء الإنتاج.\nالقصّ والتفصيل بقياساتٍ دقيقة، وتجهيز كل قطعةٍ على حدة قبل الخياطة.\nتطريزٌ دقيق وحوافٌ مشغولة بعناية تعكس الاهتمام بالتفاصيل واللمسة الماهرة.\nفحصٌ يدوي لكل قطعة، ثم تغليفٌ يحفظها في الشحن والتخزين.",
				"طلب العرض\nالعيّنة\nالإنتاج\nالتسليم",
			),
			'en'    => array(
				'Manufacturing & production',
				'Integrated production lines inside our Sahab factory — natural and synthetic farwa, bisht, vests, thobes and embroidery — with capacity for large orders and no compromise on standard.',
				'The Qafesheh factory produces a large and varied range of natural and synthetic farwa of every type, along with shemagh, vests and jackets, made from the best materials at the best price.',
				"Fabric selection from approved suppliers, with a sample shown before production.\nPrecise cutting and tailoring, each piece prepared individually before sewing.\nFine embroidery and carefully finished edges.\nHand inspection of every piece, then packaging that protects it in shipping and storage.",
				"Request a quote\nSample\nProduction\nDelivery",
			),
		),
		array(
			'icon'  => 'tailor',
			'image' => 'fur-stitching.jpg',
			'gal'   => array( 'fur-stitching.jpg', 'embroidery-hands.jpg' ),
			'ar'    => array( 'التفصيل حسب الطلب', 'قياسات خاصة، اختيار الخامة واللون، وتطريزٌ باسم أو نقشٍ يطلبه العميل — للمناسبات والأعراس والطلبات المؤسسية.', 'نستقبل طلبات التفصيل في المعارض وعبر التواصل المباشر، ونرافق العميل من أخذ القياس حتى استلام القطعة.', "أخذ القياسات في المعرض أو عبر دليل قياس مرسل.\nاختيار الخامة واللون من عيّنات حقيقية.\nتطريز اسم أو نقش خاص حسب الطلب.\nبروفة قبل التسليم للطلبات المعقّدة.", "القياس\nالخامة\nالتفصيل\nالتسليم" ),
			'en'    => array( 'Made to order', 'Custom measurements, fabric and colour selection, and embroidery of a name or motif — for occasions, weddings and corporate orders.', 'We take made-to-order requests in our showrooms and directly, and follow the client from measurement to delivery.', "Measurements in the showroom or via a sizing guide.\nFabric and colour chosen from real samples.\nCustom name or motif embroidery.\nA fitting before delivery for complex orders.", "Measure\nFabric\nTailoring\nDelivery" ),
		),
		array(
			'icon'  => 'truck',
			'image' => 'factory-women.jpg',
			'gal'   => array( 'factory-women.jpg', 'textiles-stack.jpg' ),
			'ar'    => array( 'البيع بالجملة', 'توريد كميات للتجّار والموزّعين داخل المملكة وخارجها، بأسعار جملة وجدول تسليم متّفق عليه مسبقاً.', 'نورّد لعددٍ من التجّار والمعارض في المملكة ودول الجوار، بكتالوج موسمي وحدٍّ أدنى للطلب وجدول تسليم واضح.', "كتالوج موسمي بأسعار الجملة.\nحدٌّ أدنى مرن للطلب حسب الصنف.\nجدول تسليم متّفق عليه مسبقاً.\nمتابعة مباشرة من فريق المبيعات.", "الكتالوج\nعرض السعر\nالتجهيز\nالتوريد" ),
			'en'    => array( 'Wholesale', 'Volume supply for traders and distributors in Jordan and abroad, with wholesale pricing and an agreed delivery schedule.', 'We supply traders and showrooms across Jordan and neighbouring countries, with a seasonal catalogue, a minimum order and a clear delivery plan.', "Seasonal catalogue at wholesale prices.\nFlexible minimum order per item.\nAgreed delivery schedule.\nDirect follow-up from the sales team.", "Catalogue\nQuote\nPreparation\nSupply" ),
		),
		array(
			'icon'  => 'globe',
			'image' => 'factory-exterior.jpg',
			'gal'   => array( 'factory-exterior.jpg', 'showroom.jpg' ),
			'ar'    => array( 'التصدير والشحن الدولي', 'شحنٌ إلى دول الخليج والوطن العربي مع كامل مستندات التصدير والفاتورة الجمركية، وتغليفٌ يحفظ القطعة في الطريق.', 'الخليج هو السوق الخارجي الأول للمجموعة منذ سنوات. نتولّى التغليف التصديري وتجهيز المستندات والتنسيق مع شركات الشحن.', "تغليف تصديري يحمي القطعة.\nتجهيز الفاتورة الجمركية وشهادة المنشأ.\nالتنسيق مع شركة الشحن حتى الوصول.\nصناعة أردنية ١٠٠٪.", "الطلب\nالتغليف\nالمستندات\nالشحن" ),
			'en'    => array( 'Export & international shipping', 'Shipping to the Gulf and the Arab world with full export documents and customs invoicing, and packaging that protects every piece.', 'The Gulf has been the group’s first export market for years. We handle export packaging, documents and coordination with carriers.', "Export packaging that protects the piece.\nCustoms invoice and certificate of origin.\nCoordination with the carrier until arrival.\n100% made in Jordan.", "Order\nPackaging\nDocuments\nShipping" ),
		),
		array(
			'icon'  => 'box',
			'image' => 'workshop-cutting.jpg',
			'gal'   => array( 'workshop-cutting.jpg', 'fur-stitching.jpg' ),
			'ar'    => array( 'العناية والصيانة', 'غسيل جاف متخصص بالبخار، ترميم الحواف والتطريز، وحفظ الفروة والبشت بطريقةٍ صحيحة بين المواسم.', 'الفروة والثوب المطرّز يحتاجان عنايةً مختلفة عن الملابس العادية. نستقبل طلبات العناية والتعديل والإصلاح في المعارض.', "غسيل جاف متخصص بالبخار.\nترميم الحواف والتطريز.\nتعديل المقاس بعد الشراء.\nحفظ القطعة بين المواسم.", "الاستلام\nالفحص\nالعناية\nالتسليم" ),
			'en'    => array( 'Care & maintenance', 'Specialist steam dry cleaning, restoration of edges and embroidery, and proper storage of farwa and bisht between seasons.', 'Farwa and embroidered thobes need different care from everyday clothing. We take care, alteration and repair requests in our showrooms.', "Specialist steam dry cleaning.\nEdge and embroidery restoration.\nSize alterations after purchase.\nSeasonal storage.", "Drop-off\nInspection\nCare\nPick-up" ),
		),
		array(
			'icon'  => 'star',
			'image' => 'textiles-stack.jpg',
			'gal'   => array( 'textiles-stack.jpg', 'atelier-bench.jpg' ),
			'ar'    => array( 'التصنيع بعلامة العميل', 'إنتاجٌ بمواصفات العميل وتحت علامته الخاصة وهويته البصرية، بنفس خطوط الإنتاج ومعايير الجودة.', 'نستقبل طلبات الإنتاج بعلامة العميل: نفس خطوط الإنتاج والجودة، مع ليبل وتغليف باسم التاجر.', "إنتاج بمواصفات العميل.\nليبل وتغليف بهوية التاجر.\nعيّنة معتمدة قبل الإنتاج الكامل.\nسرّية تامة للتصاميم.", "المواصفات\nالعيّنة\nالإنتاج\nالتسليم" ),
			'en'    => array( 'Private label', 'Production to the client’s specification under their own brand and identity, on the same lines and quality standards.', 'We take private-label production requests: the same lines and quality, with labels and packaging in the trader’s name.', "Production to your specification.\nLabels and packaging in your identity.\nApproved sample before full production.\nFull confidentiality of designs.", "Specification\nSample\nProduction\nDelivery" ),
		),
	);

	$shorts = array(
		'factory' => array( 'خطوط إنتاج متكاملة داخل المصنع بطاقةٍ تخدم الطلبات الكبيرة.', 'Integrated production lines inside the factory, sized for large orders.' ),
		'tailor' => array( 'قياسات وخامات وتطريز خاص حسب طلب العميل أو المناسبة.', 'Custom measurements, fabrics and embroidery for the client or the occasion.' ),
		'truck' => array( 'توريد كميات للتجّار والموزّعين داخل المملكة وخارجها.', 'Volume supply for traders and distributors in Jordan and abroad.' ),
		'globe' => array( 'شحن دولي مع كامل مستندات التصدير والفاتورة الجمركية.', 'International shipping with full export documents and customs invoicing.' ),
		'box' => array( 'غسيل جاف متخصص وترميم وحفظ الفروات بين المواسم.', 'Specialist dry cleaning, restoration and seasonal storage for farwa.' ),
		'star' => array( 'إنتاج بمواصفات العميل وتحت علامته الخاصة وهويته البصرية.', 'Production to your specification, under your own brand and identity.' ),
	);

	$out = array();
	foreach ( $rows as $i => $r ) {
		$item = array(
			'key'    => 'service-' . $r['icon'],
			'shared' => array( 'order' => $i + 1, 'image' => $r['image'], 'meta' => array( 'icon' => $r['icon'], 'gallery' => $r['gal'] ) ),
		);
		foreach ( array( 'ar', 'en' ) as $lang ) {
			$l             = $r[ $lang ];
			$item[ $lang ] = array(
				'title'   => $l[0],
				'excerpt' => $l[1],
				'content' => '<p>' . $l[2] . '</p>',
				'meta'    => array( 'includes' => $l[3], 'steps' => $l[4], 'short' => $shorts[ $r['icon'] ][ 'ar' === $lang ? 0 : 1 ] ),
			);
		}
		$out[] = $item;
	}
	return $out;
}

/**
 * Branches — one unified list, tagged by brand.
 *
 * @return array
 */
function qaf_seed_branches() {
	$rows = array(
		array( 'factory', array( 'qafesheh', 'aljazi', 'makarem' ), '+962 79 881 7390', 1,
			array( 'المصنع — سحاب', 'المصنع — سحاب', 'سحاب', 'مدينة الملك عبدالله الثاني الصناعية، سحاب', '٧:٠٠ — ١٩:٠٠' ),
			array( 'The factory — Sahab', 'Factory — Sahab', 'Sahab', 'King Abdullah II Industrial City, Sahab', '07:00 — 19:00' ) ),
		array( 'downtown', array( 'qafesheh', 'aljazi', 'makarem' ), '+962 79 990 0042', 0,
			array( 'معرض وسط البلد', 'وسط البلد — شارع الملك طلال', 'عمّان', 'شارع الملك طلال، مقابل دخلة الهنيني — معرض قفيشة', '٩:٠٠ — ٢١:٠٠' ),
			array( 'Downtown showroom', 'Downtown — King Talal St.', 'Amman', 'King Talal St., opposite Al-Hunaini alley — Qafesheh showroom', '09:00 — 21:00' ) ),
		array( 'tabarbour', array( 'aljazi', 'makarem' ), '+962 79 676 0028', 0,
			array( 'معرض طبربور', 'طبربور — شارع طارق', 'عمّان', 'شارع طارق، مقابل KFC — معرض مكارم', '١١:٠٠ — ٢٣:٠٠' ),
			array( 'Tabarbour showroom', 'Tabarbour — Tariq St.', 'Amman', 'Tariq St., opposite KFC — Makarem showroom', '11:00 — 23:00' ) ),
		array( 'tlaa-ali', array( 'makarem' ), '+962 79 676 0028', 0,
			array( 'معرض تلاع العلي', 'تلاع العلي — سوق السلطان', 'عمّان', 'سوق السلطان، مقابل مطعم زهور الشفا', '١١:٠٠ — ٢٣:٠٠' ),
			array( 'Tla’a Al-Ali showroom', 'Tla’a Al-Ali — Sultan Souq', 'Amman', 'Sultan Souq, opposite Zuhour Al-Shifa restaurant', '11:00 — 23:00' ) ),
		array( 'irbid', array( 'makarem' ), '+962 79 676 0028', 0,
			array( 'معرض إربد', 'إربد — شارع الهاشمي', 'إربد', 'شارع الهاشمي، بعد بنك القاهرة بـ١٠٠ م', '١٠:٠٠ — ٢٢:٠٠' ),
			array( 'Irbid showroom', 'Irbid — Al-Hashemi St.', 'Irbid', 'Al-Hashemi St., 100 m past Cairo Amman Bank', '10:00 — 22:00' ) ),
		array( 'downtown-2', array( 'makarem' ), '+962 79 676 0028', 0,
			array( 'معرض وسط البلد ٢', 'وسط البلد — سوق طلال', 'عمّان', 'شارع الملك طلال، مقابل سوق طلال', '١٠:٠٠ — ٢٢:٠٠' ),
			array( 'Downtown showroom 2', 'Downtown — Talal Souq', 'Amman', 'King Talal St., opposite Talal Souq', '10:00 — 22:00' ) ),
	);

	$out = array();
	foreach ( $rows as $i => $r ) {
		$meta_shared = array(
			'phone'   => $r[2],
			'factory' => $r[3],
			'map_url' => 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $r[5][3] . ', Jordan' ),
		);
		$out[] = array(
			'key'    => 'branch-' . $r[0],
			'shared' => array( 'order' => $i + 1, 'brands' => $r[1], 'meta' => $meta_shared ),
			'ar'     => array( 'title' => $r[4][0], 'meta' => array( 'short' => $r[4][1], 'city' => $r[4][2], 'address' => $r[4][3], 'hours' => $r[4][4] ) ),
			'en'     => array( 'title' => $r[5][0], 'meta' => array( 'short' => $r[5][1], 'city' => $r[5][2], 'address' => $r[5][3], 'hours' => $r[5][4] ) ),
		);
	}
	return $out;
}

/**
 * Job taxonomy terms: slug => [ ar, en ].
 *
 * @return array
 */
function qaf_seed_job_terms() {
	return array(
		'qaf_job_dept' => array(
			'production' => array( 'الإنتاج والتصنيع', 'Production & manufacturing' ),
			'sales'      => array( 'المبيعات والمعارض', 'Sales & showrooms' ),
			'admin'      => array( 'الإدارة', 'Administration' ),
		),
		'qaf_job_loc'  => array(
			'sahab' => array( 'سحاب — المصنع', 'Sahab — Factory' ),
			'amman' => array( 'عمّان — المعارض', 'Amman — Showrooms' ),
			'irbid' => array( 'إربد', 'Irbid' ),
		),
		'qaf_job_type' => array(
			'full-time' => array( 'دوام كامل', 'Full time' ),
			'part-time' => array( 'دوام جزئي', 'Part time' ),
		),
	);
}

/**
 * Jobs.
 *
 * @return array
 */
function qaf_seed_jobs() {
	$rows = array(
		array( 'fur-tailor', 'production', 'sahab', 'full-time', 'خيّاط فروات', 'Fur tailor', 'سنتان فأكثر', '2+ years' ),
		array( 'showroom-sales', 'sales', 'amman', 'full-time', 'مسؤول مبيعات — معرض وسط البلد', 'Sales associate — Downtown showroom', 'سنة فأكثر', '1+ year' ),
		array( 'embroiderer', 'production', 'sahab', 'full-time', 'مطرّز / مطرّزة', 'Embroidery artisan', 'خبرة بالتطريز اليدوي', 'Hand embroidery experience' ),
		array( 'irbid-sales', 'sales', 'irbid', 'part-time', 'مستشار مبيعات — إربد', 'Sales advisor — Irbid', 'لا يشترط', 'Not required' ),
		array( 'quality', 'production', 'sahab', 'full-time', 'مراقب جودة', 'Quality inspector', '٣ سنوات', '3 years' ),
		array( 'accountant', 'admin', 'sahab', 'full-time', 'محاسب', 'Accountant', '٣ سنوات فأكثر', '3+ years' ),
	);
	$out = array();
	foreach ( $rows as $i => $r ) {
		$out[] = array(
			'key'    => 'job-' . $r[0],
			'shared' => array( 'order' => $i + 1, 'terms' => array( 'qaf_job_dept' => $r[1], 'qaf_job_loc' => $r[2], 'qaf_job_type' => $r[3] ) ),
			'ar'     => array(
				'title'   => $r[4],
				'excerpt' => 'انضم إلى فريق مجموعة قفيشة — بيئة عمل حِرَفية وتدريب مستمر.',
				'content' => '<p>نبحث عن شخصٍ شغوف بالحِرفة ودقيق بالتفاصيل للانضمام إلى فريقنا. ستعمل ضمن فريقٍ متمرّس يهتم بجودة كل قطعة تخرج باسم المجموعة، مع تدريبٍ مستمر ومسارٍ واضح للتطوّر.</p>',
				'meta'    => array(
					'experience' => $r[6],
					'deadline'   => '٣١ أكتوبر ٢٠٢٦',
					'tasks'      => "تنفيذ المهام اليومية حسب معايير الجودة المعتمدة.\nالتنسيق مع مشرف القسم والفريق.\nالمحافظة على المعدات وبيئة العمل.",
					'reqs'       => "الالتزام والدقة بالمواعيد.\nالقدرة على العمل ضمن فريق.\nخبرة سابقة بمجال مشابه ميزة إضافية.",
				),
			),
			'en'     => array(
				'title'   => $r[5],
				'excerpt' => 'Join the Qafesheh Group team — a craft-driven workplace with ongoing training.',
				'content' => '<p>We are looking for someone passionate about craft and precise with detail to join our team. You will work with experienced colleagues who care about every piece that carries the group’s name, with ongoing training and a clear path to grow.</p>',
				'meta'    => array(
					'experience' => $r[7],
					'deadline'   => '31 October 2026',
					'tasks'      => "Carry out daily tasks to the approved quality standards.\nCoordinate with the department supervisor and team.\nLook after equipment and the workplace.",
					'reqs'       => "Commitment and punctuality.\nAbility to work in a team.\nPrevious experience in a similar field is a plus.",
				),
			),
		);
	}
	return $out;
}

/**
 * Articles.
 *
 * @return array
 */
function qaf_seed_articles() {
	$body_ar = static function ( $lead ) {
		return '<p>' . $lead . '</p><p>خلف كل قطعة تخرج من مصنعنا قصةٌ من القرارات الصغيرة: اختيار الخامة، ضبط القصّة، والتشطيب اليدوي الذي لا تراه العين من أول نظرة لكنه يصنع الفرق بعد سنوات.</p><h2>من الخامة إلى القطعة</h2><p>نبدأ دائماً من الخامة. الصوف والفرو والخيوط تُفحص قبل دخولها خط الإنتاج، ثم تُقصّ وتُجهّز كل قطعة على حدة.</p><blockquote><p>الجودة لا تُضاف في النهاية — تُبنى من أول غرزة.</p></blockquote><h2>ما الذي يميّز القطعة الأصيلة</h2><p>الوزن المتوازن، الحواف المشغولة بعناية، والتطريز المنتظم. هذه تفاصيل يعرفها الزبون الخبير ويشعر بها من يلبس القطعة لأول مرة.</p><h3>نصيحة سريعة</h3><p>اسأل دائماً عن بلد الصنع ونوع الخامة، واطلب رؤية التشطيب الداخلي للقطعة.</p><p>في المقالات القادمة نكمل الحديث عن العناية والتخزين بين المواسم.</p>';
	};
	$body_en = static function ( $lead ) {
		return '<p>' . $lead . '</p><p>Behind every piece that leaves our factory is a story of small decisions: the fabric, the cut and the hand finishing you may not notice at first glance but that makes the difference years later.</p><h2>From fabric to finished piece</h2><p>We always start with the material. Wool, fur and threads are inspected before they enter the line, then every piece is cut and prepared individually.</p><blockquote><p>Quality is not added at the end — it is built from the first stitch.</p></blockquote><h2>What sets an authentic piece apart</h2><p>Balanced weight, carefully finished edges and even embroidery. Details an experienced customer recognises and a first-time wearer can feel.</p><h3>A quick tip</h3><p>Always ask about the country of origin and the fabric, and look at the inside finishing.</p><p>In the next articles we continue with care and seasonal storage.</p>';
	};
	$rows = array(
		array( 'factory-story', 'three-generations.jpg', '2026-09-10', 'group-news', 'من ورشةٍ صغيرة إلى مئة عامل: قصة مصنع قفيشة', 'From a small workshop to a hundred workers: the Qafesheh factory story', 'كيف تحوّلت ورشة فراء بماكيناتٍ قليلة إلى مصنعٍ يخدم ثلاث علامات ويصدّر إلى الخليج.', 'How a fur workshop with a few machines became a factory behind three brands exporting to the Gulf.' ),
		array( 'farwa-care', 'workshop-cutting.jpg', '2026-09-02', 'care', 'كيف تعتني بفروتك لتبقى سنوات', 'How to care for your farwa so it lasts for years', 'إرشادات الغسيل والكي والتخزين بين المواسم من فريق العناية في المصنع.', 'Cleaning, pressing and seasonal storage advice from our factory care team.' ),
		array( 'embroidery', 'embroidery-hands.jpg', '2026-08-25', 'bisht-thobe', 'المطرزات الشرقية: الغرزة التي تحمل هوية', 'Oriental embroidery: the stitch that carries identity', 'لماذا يبقى التطريز اليدوي قلب علامة مكارم.', 'Why hand embroidery remains the heart of Makarem.' ),
		array( 'choose-bisht', 'showroom.jpg', '2026-08-18', 'bisht-thobe', 'كيف تختار البشت المناسب للمناسبة', 'How to choose the right bisht for the occasion', 'اللون والخامة والطول — ثلاثة أسئلة قبل الشراء.', 'Colour, fabric and length — three questions before you buy.' ),
		array( 'natural-vs-synthetic', 'boutique-coats.jpg', '2026-08-09', 'farwa-guide', 'الفروة الطبيعية أم الصناعية؟', 'Natural or synthetic farwa?', 'مقارنة عملية بين النوعين من حيث الدفء والوزن والعناية.', 'A practical comparison of warmth, weight and care.' ),
		array( 'irbid-showroom', 'family-courtyard.jpg', '2026-07-30', 'group-news', 'افتتاح معرض مكارم في إربد', 'Makarem opens its Irbid showroom', 'خامس معارض المجموعة يفتح أبوابه في شارع الهاشمي.', 'The group’s fifth showroom opens on Al-Hashemi Street.' ),
		array( 'summer-thobes', 'family-desert.jpg', '2026-07-21', 'bisht-thobe', 'أثواب الصيف: خامات تتنفّس', 'Summer thobes: fabrics that breathe', 'مجموعة الجازي الصيفية وكيف نختار أقمشتها.', 'Al Jazi’s summer collection and how its fabrics are chosen.' ),
	);
	$cats = array(
		'group-news'  => array( 'أخبار المجموعة', 'Group news' ),
		'farwa-guide' => array( 'دليل الفروة', 'Farwa guide' ),
		'bisht-thobe' => array( 'البشت والثوب', 'Bisht & thobe' ),
		'care'        => array( 'العناية', 'Care' ),
	);
	$out = array();
	foreach ( $rows as $r ) {
		$out[] = array(
			'key'    => 'article-' . $r[0],
			'shared' => array( 'image' => $r[1], 'date' => $r[2] . ' 10:00:00', 'cat_slug' => $r[3] ),
			'ar'     => array( 'title' => $r[4], 'cat' => $cats[ $r[3] ][0], 'excerpt' => $r[6], 'content' => $body_ar( $r[6] ) ),
			'en'     => array( 'title' => $r[5], 'cat' => $cats[ $r[3] ][1], 'excerpt' => $r[7], 'content' => $body_en( $r[7] ) ),
		);
	}
	return $out;
}

/**
 * Pages. template: default | home.
 *
 * @return array
 */
function qaf_seed_pages() {
	return array(
		array( 'key' => 'page-home', 'shared' => array( 'template' => 'home' ), 'ar' => array( 'title' => 'الرئيسية', 'slug' => 'home' ), 'en' => array( 'title' => 'Home', 'slug' => 'home' ) ),
		array( 'key' => 'page-about', 'shared' => array(), 'ar' => array( 'title' => 'من نحن', 'slug' => 'about', 'content' => '<p>مجموعة قفيشة الصناعية مجموعةٌ أردنية في قطاع الأزياء التراثية الفاخرة، تملك مصنعاً في سحاب وشبكة معارض في عمّان وإربد، وتصدّر إلى الخليج والوطن العربي.</p>' ), 'en' => array( 'title' => 'About', 'slug' => 'about', 'content' => '<p>Qafesheh Industrial Group is a Jordanian group in luxury heritage wear, with a factory in Sahab, showrooms in Amman and Irbid, and exports to the Gulf and the Arab world.</p>' ) ),
		array( 'key' => 'page-news', 'shared' => array( 'template' => 'posts' ), 'ar' => array( 'title' => 'المقالات والأخبار', 'slug' => 'news' ), 'en' => array( 'title' => 'News & Articles', 'slug' => 'news' ) ),
		array( 'key' => 'page-contact', 'shared' => array(), 'ar' => array( 'title' => 'تواصل معنا', 'slug' => 'contact' ), 'en' => array( 'title' => 'Contact us', 'slug' => 'contact' ) ),
		array( 'key' => 'page-complaints', 'shared' => array(), 'ar' => array( 'title' => 'شكاوى واقتراحات', 'slug' => 'complaints' ), 'en' => array( 'title' => 'Complaints & suggestions', 'slug' => 'complaints' ) ),
		array( 'key' => 'page-privacy', 'shared' => array(), 'ar' => array( 'title' => 'سياسة الخصوصية', 'slug' => 'privacy-policy' ), 'en' => array( 'title' => 'Privacy policy', 'slug' => 'privacy-policy' ) ),
		array( 'key' => 'page-terms', 'shared' => array(), 'ar' => array( 'title' => 'الشروط والأحكام', 'slug' => 'terms' ), 'en' => array( 'title' => 'Terms & conditions', 'slug' => 'terms' ) ),
	);
}

/**
 * Menus: location => items. Item: [ type, ref, ar label, en label ].
 * type: page (seed key) | archive (post type) | url.
 *
 * @return array
 */
function qaf_seed_menus() {
	return array(
		'primary'        => array(
			array( 'page', 'page-home', 'الرئيسية', 'Home' ),
			array( 'page', 'page-about', 'المجموعة', 'The Group' ),
			array( 'archive', 'qaf_company', 'قطاعات الأعمال', 'Businesses' ),
			array( 'archive', 'qaf_service', 'الخدمات', 'Services' ),
			array( 'page', 'page-news', 'المقالات والأخبار', 'News' ),
			array( 'archive', 'qaf_branch', 'الفروع', 'Branches' ),
			array( 'archive', 'qaf_job', 'الوظائف', 'Careers' ),
		),
		'utility'        => array(
			array( 'page', 'page-contact', 'بوابة التجّار', 'Trade portal' ),
			array( 'page', 'page-complaints', 'شكاوى واقتراحات', 'Feedback' ),
		),
		'footer_group'   => array(
			array( 'page', 'page-about', 'من نحن', 'About' ),
			array( 'archive', 'qaf_service', 'الخدمات', 'Services' ),
			array( 'page', 'page-news', 'المقالات والأخبار', 'News & articles' ),
			array( 'archive', 'qaf_branch', 'الفروع', 'Branches' ),
		),
		'footer_brands'  => array(
			array( 'post', 'company-qafesheh', 'قفيشة', 'Qafesheh' ),
			array( 'post', 'company-aljazi', 'الجازي', 'Al Jazi' ),
			array( 'post', 'company-makarem', 'مكارم', 'Makarem' ),
		),
		'footer_service' => array(
			array( 'archive', 'qaf_job', 'الوظائف', 'Careers' ),
			array( 'page', 'page-complaints', 'شكاوى واقتراحات', 'Complaints & suggestions' ),
			array( 'page', 'page-contact', 'تواصل معنا', 'Contact us' ),
		),
		'footer_legal'   => array(
			array( 'page', 'page-privacy', 'سياسة الخصوصية', 'Privacy policy' ),
			array( 'page', 'page-terms', 'الشروط والأحكام', 'Terms & conditions' ),
		),
	);
}

/**
 * Inner pages copy (About, Contact, Complaints) per language.
 *
 * @param string      $page about|contact|complaints.
 * @param string|null $lang Language.
 * @return array
 */
function qaf_seed_page_copy( $page, $lang = null ) {
	$copy = array(
		'about'      => array(
			'ar' => array(
				'hero'     => array( 'title' => 'حكايةُ مجموعةِ قفيشة الصناعية', 'text' => 'مجموعةٌ أردنية جمعت ثلاث علاماتٍ في الأزياء التراثية الفاخرة تحت مظلّةٍ واحدة: مصنعٌ واحد في سحاب، ومعيارٌ واحد للجودة، وحِرفةٌ تنتقل من جيلٍ إلى جيل.', 'ghost' => 'إرث', 'align' => 'start' ),
				'story'    => array( 'kicker' => '٠١ — البداية', 'title' => "من ورشةٍ صغيرة\nإلى مصنعٍ يصدّر للعالم", 'text1' => 'بدأ المصنع بأيدٍ عاملةٍ قليلة وماكيناتٍ قليلة، واستمرّ بالتطوّر إلى يومنا هذا. أصبح عدد الأيدي العاملة يتجاوز مئة عاملٍ من الطراز الأول، مع أحدث الماكينات والآلات والمعدات المتخصصة لتساعدنا في التطوّر والابتكار.', 'text2' => 'تم استحداث وتطوير تصاميم الفروات لتنال رضى جميع الأذواق في العالم، مع الحفاظ على الحِرفة كما وصلتنا.', 'image' => 'embroidery-hands.jpg' ),
				'timeline' => array(
					'kicker' => '٠٢ — المحطات',
					'title'  => 'خطُّ الزمن',
					'items'  => array(
						array( 'year' => 'البداية', 'title' => 'تأسيس مصنع قفيشة', 'text' => 'ورشة فراء بأيدٍ قليلة وماكيناتٍ قليلة في عمّان.' ),
						array( 'year' => 'التوسّع', 'title' => 'افتتاح أول معرض', 'text' => 'معرض وسط البلد في شارع الملك طلال يفتح أبوابه للزبائن.' ),
						array( 'year' => 'العلامات', 'title' => 'إطلاق مكارم والجازي', 'text' => 'علامتان جديدتان للمطرزات النسائية والأثواب الرجالية.' ),
						array( 'year' => 'اليوم', 'title' => 'التوسّع في التصدير', 'text' => 'أكثر من مئة عامل، خمسة معارض، وتصدير إلى الخليج والوطن العربي.' ),
					),
				),
				'vision'   => array( 'v_title' => 'رؤيتنا', 'v_text' => 'أن نبقى المرجع الأول للفراء والبشوت والمطرزات في الوطن العربي، وأن يصل ما نصنعه في عمّان إلى كل بيتٍ يقدّر الحِرفة.', 'm_title' => 'رسالتنا', 'm_text' => 'أن نصنع قطعةً تُلبَس وتُورَّث — بخامةٍ لا نساوم عليها، وأيدٍ تعرف الغرزة، وسعرٍ يبقى في متناول من يبحث عن الأصل.' ),
				'values'   => array(
					'kicker' => '٠٣ — قيمنا',
					'title'  => 'ما نلتزمُ به',
					'items'  => array(
						array( 'icon' => 'star', 'title' => 'الجودة قبل الكمّ', 'text' => 'أفضل أنواع الأقمشة والخيوط، ما يضمن المتانة والمحافظة على الرونق سنواتٍ طويلة.' ),
						array( 'icon' => 'shield', 'title' => 'صناعةٌ وطنية', 'text' => 'إنتاج أردني ١٠٠٪ من مصنعنا في سحاب، بأيدٍ محلية مدرَّبة على الحِرفة.' ),
						array( 'icon' => 'heart', 'title' => 'الإرث والتطوير', 'text' => 'نعتزُّ بأصالة الفروة ونورّثها من جيلٍ إلى جيل، مع استحداث تصاميم تناسب كل الأذواق.' ),
					),
				),
			),
			'en' => array(
				'hero'     => array( 'title' => 'The story of Qafesheh Industrial Group', 'text' => 'A Jordanian group bringing three houses of luxury heritage wear under one roof: one factory in Sahab, one standard of quality, and a craft passed from one generation to the next.', 'ghost' => 'Heritage', 'align' => 'start' ),
				'story'    => array( 'kicker' => '01 — THE BEGINNING', 'title' => "From a small workshop\nto a factory exporting worldwide", 'text1' => 'The factory began with a few hands and a few machines and kept evolving to this day. Today more than a hundred first-class workers operate the latest specialised machinery that drives our development and innovation.', 'text2' => 'Farwa designs have been renewed to please every taste around the world — while keeping the craft exactly as it reached us.', 'image' => 'embroidery-hands.jpg' ),
				'timeline' => array(
					'kicker' => '02 — MILESTONES',
					'title'  => 'Timeline',
					'items'  => array(
						array( 'year' => 'Origins', 'title' => 'Qafesheh factory founded', 'text' => 'A fur workshop with a few hands and a few machines in Amman.' ),
						array( 'year' => 'Growth', 'title' => 'First showroom opens', 'text' => 'The Downtown showroom on King Talal Street welcomes its first customers.' ),
						array( 'year' => 'Brands', 'title' => 'Makarem and Al Jazi launch', 'text' => 'Two new houses for women’s embroidery and men’s thobes.' ),
						array( 'year' => 'Today', 'title' => 'Growing exports', 'text' => 'Over a hundred workers, five showrooms and exports across the Gulf and the Arab world.' ),
					),
				),
				'vision'   => array( 'v_title' => 'Our vision', 'v_text' => 'To remain the first reference for fur, bisht and embroidery in the Arab world, and to bring what we make in Amman to every home that values craft.', 'm_title' => 'Our mission', 'm_text' => 'To make pieces that are worn and handed down — with fabric we never compromise on, hands that know the stitch, and prices within reach of those who seek the authentic.' ),
				'values'   => array(
					'kicker' => '03 — OUR VALUES',
					'title'  => 'What we stand by',
					'items'  => array(
						array( 'icon' => 'star', 'title' => 'Quality over quantity', 'text' => 'The finest fabrics and threads, built to last and keep their lustre for years.' ),
						array( 'icon' => 'shield', 'title' => 'National craft', 'text' => '100% Jordanian production from our Sahab factory, by local hands trained in the craft.' ),
						array( 'icon' => 'heart', 'title' => 'Heritage & renewal', 'text' => 'We honour the authentic farwa and hand it down, while designing for every taste.' ),
					),
				),
			),
		),
		'contact'    => array(
			'ar' => array(
				'hero'     => array( 'title' => 'تواصلْ معنا', 'text' => 'للاستفسارات التجارية، طلبات الجملة والتصدير، أو التواصل مع أيٍّ من علاماتنا الثلاث.', 'ghost' => '', 'align' => 'start' ),
				'channels' => array( 'email_title' => 'البريد الإلكتروني', 'phone_title' => 'الهاتف', 'address_title' => 'المقر الرئيسي' ),
				'form'     => array( 'title' => 'أرسلْ رسالتك' ),
			),
			'en' => array(
				'hero'     => array( 'title' => 'Contact us', 'text' => 'For trade enquiries, wholesale and export requests, or to reach any of our three brands.', 'ghost' => '', 'align' => 'start' ),
				'channels' => array( 'email_title' => 'Email', 'phone_title' => 'Phone', 'address_title' => 'Head office' ),
				'form'     => array( 'title' => 'Send us a message' ),
			),
		),
		'complaints' => array(
			'ar' => array(
				'hero'  => array( 'title' => 'رأيُكَ يصلُ للإدارة', 'text' => 'كل شكوى أو اقتراح يصل مباشرةً إلى إدارة الجودة، ويأخذ رقماً يمكنك متابعته. نقرأ كل رسالة — ونرد على كل واحدةٍ منها.', 'ghost' => '', 'align' => 'center' ),
				'steps' => array(
					array( 'title' => 'ترسل الرسالة', 'text' => 'تملأ النموذج وترفق صورة إن لزم.' ),
					array( 'title' => 'يصلك رقم', 'text' => 'رقم متابعة على بريدك مباشرةً.' ),
					array( 'title' => 'تُدرس الرسالة', 'text' => 'إدارة الجودة تراجعها مع الفرع المعني.' ),
					array( 'title' => 'نرد عليك', 'text' => 'خلال ٣ أيام عمل كحدٍّ أقصى.' ),
				),
				'form'  => array( 'title' => 'نموذج الشكاوى والاقتراحات' ),
			),
			'en' => array(
				'hero'  => array( 'title' => 'Your voice reaches management', 'text' => 'Every complaint or suggestion goes straight to our quality team and gets a number you can follow up. We read every message — and we answer every one.', 'ghost' => '', 'align' => 'center' ),
				'steps' => array(
					array( 'title' => 'You send it', 'text' => 'Fill in the form and attach a photo if needed.' ),
					array( 'title' => 'You get a number', 'text' => 'A tracking number arrives by email.' ),
					array( 'title' => 'We review it', 'text' => 'Our quality team reviews it with the branch concerned.' ),
					array( 'title' => 'We reply', 'text' => 'Within 3 working days at most.' ),
				),
				'form'  => array( 'title' => 'Complaints & suggestions form' ),
			),
		),
	);
	$lang = $lang ? $lang : qaf_current_lang();
	return $copy[ $page ][ $lang ] ?? $copy[ $page ]['ar'] ?? array();
}

/**
 * Section recipe per inner page (order + part names). Used by page.php fallback and the importer.
 *
 * @return array seed key => [ page copy key, section => copy key ]
 */
function qaf_page_recipes() {
	return array(
		'page-about'      => array( 'about', array( 'page-hero' => 'hero', 'story' => 'story', 'timeline' => 'timeline', 'vision' => 'vision', 'values' => 'values', 'brands-strip' => '' ) ),
		'page-contact'    => array( 'contact', array( 'page-hero' => 'hero', 'channels' => 'channels', 'contact-form' => 'form' ) ),
		'page-complaints' => array( 'complaints', array( 'page-hero' => 'hero', 'steps-bar' => 'steps', 'complaint-form' => 'form' ) ),
	);
}
