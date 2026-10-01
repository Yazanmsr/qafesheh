<?php
/**
 * Group offers — the three houses' current offers, managed from one place.
 *
 * Each offer belongs to a company (Qafesha, Al Jazi, Makarem), carries its colour, a discount line
 * («حتى 30%»), a start and an end, and leads to that company's store. The section on the site shows
 * only what is running right now and hides itself when nothing is — no one has to remember to
 * take an expired offer down.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the offer type.
 */
function qaf_register_offers() {
	register_post_type(
		'qaf_offer',
		array(
			'labels'             => array(
				'name'               => 'العروض',
				'singular_name'      => 'عرض',
				'add_new'            => 'عرض جديد',
				'add_new_item'       => 'عرض جديد',
				'edit_item'          => 'تعديل العرض',
				'search_items'       => 'بحث في العروض',
				'not_found'          => 'لا توجد عروض بعد',
				'not_found_in_trash' => 'لا توجد عروض محذوفة',
				'all_items'          => 'كل العروض',
				'menu_name'          => 'العروض',
			),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_rest'       => false,
			'menu_icon'          => 'dashicons-tag',
			'menu_position'      => 5,
			'supports'           => array( 'title', 'thumbnail', 'excerpt', 'page-attributes' ),
		)
	);
}
add_action( 'init', 'qaf_register_offers' );

/**
 * Offer data.
 *
 * @param int $id Offer.
 * @return array
 */
function qaf_offer( $id ) {
	$m = static fn( $k, $d = '' ) => metadata_exists( 'post', $id, '_qaf_offer_' . $k ) ? get_post_meta( $id, '_qaf_offer_' . $k, true ) : $d;
	return array(
		'id'        => (int) $id,
		'company'   => (int) $m( 'company', 0 ),
		'amount'    => (string) $m( 'amount' ),
		'badge'     => (string) $m( 'badge' ),
		'start'     => (string) $m( 'start' ),
		'end'       => (string) $m( 'end' ),
		'cta'       => (string) $m( 'cta' ),
		'url'       => (string) $m( 'url' ),
		'countdown' => '0' !== (string) $m( 'countdown', '1' ),
		'paused'    => '1' === (string) $m( 'paused', '0' ),
	);
}

/**
 * Local datetime → timestamp.
 *
 * @param string $local Y-m-d\TH:i.
 * @return int
 */
function qaf_offer_ts( $local ) {
	if ( ! $local ) {
		return 0;
	}
	try {
		return ( new DateTimeImmutable( str_replace( 'T', ' ', $local ), wp_timezone() ) )->getTimestamp();
	} catch ( Exception $e ) {
		return 0;
	}
}

/**
 * Status: live | scheduled | ended | paused | draft.
 *
 * @param array $o Offer.
 * @return string
 */
function qaf_offer_status( $o ) {
	if ( 'publish' !== get_post_status( $o['id'] ) ) {
		return 'draft';
	}
	if ( $o['paused'] ) {
		return 'paused';
	}
	$start = qaf_offer_ts( $o['start'] );
	$end   = qaf_offer_ts( $o['end'] );
	if ( $start && time() < $start ) {
		return 'scheduled';
	}
	if ( $end && time() >= $end ) {
		return 'ended';
	}
	return 'live';
}

/**
 * Status labels.
 *
 * @return array
 */
function qaf_offer_status_labels() {
	return array( 'live' => 'يعمل الآن', 'scheduled' => 'مجدول', 'ended' => 'انتهى', 'paused' => 'متوقف', 'draft' => 'مسودة' );
}

/**
 * Running offers, in the team's order.
 *
 * @param int $limit Limit.
 * @return array[]
 */
function qaf_offers_live( $limit = 12 ) {
	$out = array();
	foreach ( get_posts( array( 'post_type' => 'qaf_offer', 'post_status' => 'publish', 'posts_per_page' => 40, 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'DESC' ), 'suppress_filters' => false ) ) as $p ) {
		$o = qaf_offer( $p->ID );
		if ( 'live' === qaf_offer_status( $o ) ) {
			$out[] = $o;
		}
		if ( count( $out ) >= $limit ) {
			break;
		}
	}
	return $out;
}

/**
 * Where the offer leads: its own link, else the company's store, else the company page.
 *
 * @param array $o Offer.
 * @return string
 */
function qaf_offer_url( $o ) {
	if ( $o['url'] ) {
		return $o['url'];
	}
	if ( $o['company'] ) {
		$shop = (string) qaf_meta( 'shop_url', $o['company'] );
		return $shop ? $shop : (string) get_permalink( $o['company'] );
	}
	return home_url( '/' );
}

/**
 * Human time left.
 *
 * @param array $o Offer.
 * @return string
 */
function qaf_offer_time_left( $o ) {
	$end = qaf_offer_ts( $o['end'] );
	$en  = 'en' === qaf_current_lang();
	if ( ! $end ) {
		return $en ? 'For a limited time' : 'لفترة محدودة';
	}
	$left = $end - time();
	if ( $left <= 0 ) {
		return $en ? 'Ended' : 'انتهى';
	}
	$d = (int) floor( $left / DAY_IN_SECONDS );
	if ( $d >= 1 ) {
		return $en ? sprintf( 'Ends in %d day%s', $d, $d > 1 ? 's' : '' ) : 'ينتهي خلال ' . ( 1 === $d ? 'يوم' : ( 2 === $d ? 'يومين' : $d . ( $d <= 10 ? ' أيام' : ' يوم' ) ) );
	}
	$h = max( 1, (int) floor( $left / HOUR_IN_SECONDS ) );
	return $en ? sprintf( 'Ends in %d h', $h ) : sprintf( 'ينتهي خلال %d ساعة', $h );
}

/* ================================================= Dashboard */

/**
 * Editor boxes.
 */
function qaf_offer_boxes() {
	add_meta_box( 'qaf_offer_settings', 'إعدادات العرض', 'qaf_offer_settings_box', 'qaf_offer', 'normal', 'high' );
	add_meta_box( 'qaf_offer_summary', 'ملخّص العرض', 'qaf_offer_summary_box', 'qaf_offer', 'side', 'high' );
	remove_meta_box( 'postexcerpt', 'qaf_offer', 'normal' );
	add_meta_box( 'postexcerpt', 'سطر وصفي (يظهر تحت العنوان)', 'post_excerpt_meta_box', 'qaf_offer', 'normal', 'default' );
	remove_meta_box( 'pageparentdiv', 'qaf_offer', 'side' );
	add_meta_box( 'pageparentdiv', 'الترتيب', 'page_attributes_meta_box', 'qaf_offer', 'side', 'low' );
	remove_meta_box( 'postimagediv', 'qaf_offer', 'side' );
	add_meta_box( 'postimagediv', 'صورة العرض', 'post_thumbnail_meta_box', 'qaf_offer', 'side', 'default' );
}
add_action( 'add_meta_boxes_qaf_offer', 'qaf_offer_boxes' );

/**
 * Settings box.
 *
 * @param WP_Post $post Post.
 */
function qaf_offer_settings_box( $post ) {
	$o     = qaf_offer( $post->ID );
	$comps = get_posts( array( 'post_type' => 'qaf_company', 'posts_per_page' => 12, 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'ASC' ), 'suppress_filters' => false ) );
	$f     = static fn( $k ) => 'qaf_offer[' . $k . ']';
	wp_nonce_field( 'qaf_offer_save', 'qaf_offer_nonce' );
	?>
	<div class="qaf-ofx" data-qaf-ofx>
		<section class="qaf-ofx__block">
			<h4><span>01</span>الشركة</h4>
			<div class="qaf-ofx__companies">
				<?php foreach ( $comps as $c ) : ?>
					<?php $col = qaf_company_color( $c->ID ); ?>
					<label class="qaf-ofx__company" style="--c:<?php echo esc_attr( $col ? $col : '#B8912F' ); ?>">
						<input type="radio" name="<?php echo esc_attr( $f( 'company' ) ); ?>" value="<?php echo absint( $c->ID ); ?>" <?php checked( $c->ID, $o['company'] ); ?> required>
						<span><i></i><b><?php echo esc_html( get_the_title( $c ) ); ?></b><small><?php echo esc_html( (string) qaf_meta( 'sector', $c->ID ) ); ?></small></span>
					</label>
				<?php endforeach; ?>
			</div>
			<p class="qaf-ofx__hint">العرض بياخذ لون الشركة، والزر بيودّي لمتجرها تلقائياً.</p>
		</section>

		<section class="qaf-ofx__block">
			<h4><span>02</span>العرض</h4>
			<div class="qaf-ofx__row">
				<label class="qaf-ofx__field"><span>سطر الخصم</span><input type="text" maxlength="24" name="<?php echo esc_attr( $f( 'amount' ) ); ?>" value="<?php echo esc_attr( $o['amount'] ); ?>" placeholder="حتى 30%"></label>
				<label class="qaf-ofx__field"><span>الشارة</span><input type="text" maxlength="28" name="<?php echo esc_attr( $f( 'badge' ) ); ?>" value="<?php echo esc_attr( $o['badge'] ); ?>" placeholder="عرض العيد"></label>
			</div>
			<p class="qaf-ofx__hint">سطر الخصم بيظهر كبير بالبطاقة — اكتبه قصير: «حتى 30%»، «خصم 20%»، «الثانية مجاناً».</p>
		</section>

		<section class="qaf-ofx__block">
			<h4><span>03</span>المدة</h4>
			<div class="qaf-ofx__row">
				<label class="qaf-ofx__field"><span>يبدأ</span><input type="datetime-local" name="<?php echo esc_attr( $f( 'start' ) ); ?>" value="<?php echo esc_attr( $o['start'] ); ?>"></label>
				<label class="qaf-ofx__field"><span>ينتهي</span><input type="datetime-local" name="<?php echo esc_attr( $f( 'end' ) ); ?>" value="<?php echo esc_attr( $o['end'] ); ?>" data-qaf-ofx-end></label>
			</div>
			<p class="qaf-ofx__quick">مدة سريعة من الآن:
				<button type="button" class="button" data-qaf-ofx-days="3">٣ أيام</button>
				<button type="button" class="button" data-qaf-ofx-days="7">أسبوع</button>
				<button type="button" class="button" data-qaf-ofx-days="14">أسبوعين</button>
				<button type="button" class="button" data-qaf-ofx-days="30">شهر</button>
			</p>
			<p class="qaf-ofx__hint">«يبدأ» فارغ = من لحظة النشر. «ينتهي» فارغ = بدون نهاية. العرض بيظهر وبيختفي لحاله بالوقت المحدد.</p>
		</section>

		<section class="qaf-ofx__block">
			<h4><span>04</span>الزر والعرض على الموقع</h4>
			<div class="qaf-ofx__row">
				<label class="qaf-ofx__field"><span>نص الزر</span><input type="text" maxlength="28" name="<?php echo esc_attr( $f( 'cta' ) ); ?>" value="<?php echo esc_attr( $o['cta'] ); ?>" placeholder="تسوّق العرض"></label>
				<label class="qaf-ofx__field"><span>رابط مخصص (اختياري)</span><input type="url" dir="ltr" name="<?php echo esc_attr( $f( 'url' ) ); ?>" value="<?php echo esc_attr( $o['url'] ); ?>" placeholder="فارغ = متجر الشركة"></label>
			</div>
			<div class="qaf-ofx__toggles">
				<label class="qaf-ofx__toggle"><input type="checkbox" name="<?php echo esc_attr( $f( 'countdown' ) ); ?>" value="1" <?php checked( $o['countdown'] ); ?>><i></i><span>إظهار العدّاد التنازلي</span></label>
				<label class="qaf-ofx__toggle"><input type="checkbox" name="<?php echo esc_attr( $f( 'paused' ) ); ?>" value="1" <?php checked( $o['paused'] ); ?>><i></i><span>إيقاف العرض مؤقتاً</span></label>
			</div>
		</section>
	</div>
	<?php
}

/**
 * Summary box.
 *
 * @param WP_Post $post Post.
 */
function qaf_offer_summary_box( $post ) {
	$o  = qaf_offer( $post->ID );
	$st = qaf_offer_status( $o );
	$c  = $o['company'] ? qaf_company_color( $o['company'] ) : '#B8912F';
	?>
	<div class="qaf-ofsum" style="--c:<?php echo esc_attr( $c ? $c : '#B8912F' ); ?>">
		<span class="qaf-ofpill is-<?php echo esc_attr( $st ); ?>"><?php echo esc_html( qaf_offer_status_labels()[ $st ] ); ?></span>
		<p class="qaf-ofsum__big"><?php echo esc_html( $o['amount'] ? $o['amount'] : '—' ); ?></p>
		<ul>
			<li><span>الشركة</span><b><?php echo esc_html( $o['company'] ? get_the_title( $o['company'] ) : '—' ); ?></b></li>
			<li><span>يبدأ</span><b><?php echo esc_html( $o['start'] ? wp_date( 'j M · H:i', qaf_offer_ts( $o['start'] ) ) : 'عند النشر' ); ?></b></li>
			<li><span>ينتهي</span><b><?php echo esc_html( $o['end'] ? wp_date( 'j M · H:i', qaf_offer_ts( $o['end'] ) ) : 'بدون نهاية' ); ?></b></li>
		</ul>
		<a class="button" href="<?php echo esc_url( qaf_offer_url( $o ) ); ?>" target="_blank" rel="noopener">فتح وجهة الزر ↗</a>
	</div>
	<?php
}

/**
 * Save.
 *
 * @param int $post_id Post.
 */
function qaf_offer_save( $post_id ) {
	if ( ! isset( $_POST['qaf_offer_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['qaf_offer_nonce'] ) ), 'qaf_offer_save' ) || ! current_user_can( 'edit_post', $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}
	$in = isset( $_POST['qaf_offer'] ) && is_array( $_POST['qaf_offer'] ) ? wp_unslash( $_POST['qaf_offer'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	$dt = static fn( $v ) => preg_match( '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', (string) $v ) ? (string) $v : '';
	$co = absint( $in['company'] ?? 0 );
	$set = array(
		'company'   => 'qaf_company' === get_post_type( $co ) ? $co : 0,
		'amount'    => sanitize_text_field( (string) ( $in['amount'] ?? '' ) ),
		'badge'     => sanitize_text_field( (string) ( $in['badge'] ?? '' ) ),
		'start'     => $dt( $in['start'] ?? '' ),
		'end'       => $dt( $in['end'] ?? '' ),
		'cta'       => sanitize_text_field( (string) ( $in['cta'] ?? '' ) ),
		'url'       => esc_url_raw( (string) ( $in['url'] ?? '' ) ),
		'countdown' => empty( $in['countdown'] ) ? '0' : '1',
		'paused'    => empty( $in['paused'] ) ? '0' : '1',
	);
	foreach ( $set as $k => $v ) {
		update_post_meta( $post_id, '_qaf_offer_' . $k, $v );
	}
}
add_action( 'save_post_qaf_offer', 'qaf_offer_save' );

/**
 * List columns.
 *
 * @param array $cols Columns.
 * @return array
 */
function qaf_offer_columns( $cols ) {
	return array(
		'cb'           => $cols['cb'] ?? '',
		'qaf_of_img'   => '',
		'title'        => 'العرض',
		'qaf_of_co'    => 'الشركة',
		'qaf_of_amt'   => 'الخصم',
		'qaf_of_when'  => 'المدة',
		'qaf_of_state' => 'الحالة',
	);
}
add_filter( 'manage_qaf_offer_posts_columns', 'qaf_offer_columns' );

/**
 * Column values.
 *
 * @param string $col Column.
 * @param int    $id  Post.
 */
function qaf_offer_column( $col, $id ) {
	$o = qaf_offer( $id );
	switch ( $col ) {
		case 'qaf_of_img':
			echo has_post_thumbnail( $id ) ? get_the_post_thumbnail( $id, 'thumbnail', array( 'class' => 'qaf-of-thumb' ) ) : '<span class="qaf-of-thumb is-empty">◆</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
			break;
		case 'qaf_of_co':
			if ( $o['company'] ) {
				echo '<span class="qaf-of-co" style="--c:' . esc_attr( (string) qaf_company_color( $o['company'] ) ) . '"><i></i>' . esc_html( get_the_title( $o['company'] ) ) . '</span>';
			} else {
				echo '—';
			}
			break;
		case 'qaf_of_amt':
			echo '<b class="qaf-of-amt">' . esc_html( $o['amount'] ? $o['amount'] : '—' ) . '</b>';
			break;
		case 'qaf_of_when':
			$s = $o['start'] ? wp_date( 'j M', qaf_offer_ts( $o['start'] ) ) : 'الآن';
			$e = $o['end'] ? wp_date( 'j M Y', qaf_offer_ts( $o['end'] ) ) : 'مفتوح';
			echo esc_html( $s . ' ← ' . $e );
			if ( 'live' === qaf_offer_status( $o ) && $o['end'] ) {
				echo '<small class="qaf-of-sub">' . esc_html( qaf_offer_time_left( $o ) ) . '</small>';
			}
			break;
		case 'qaf_of_state':
			$st = qaf_offer_status( $o );
			echo '<span class="qaf-ofpill is-' . esc_attr( $st ) . '">' . esc_html( qaf_offer_status_labels()[ $st ] ) . '</span>';
			break;
	}
}
add_action( 'manage_qaf_offer_posts_custom_column', 'qaf_offer_column', 10, 2 );

/**
 * Title placeholder.
 *
 * @param string  $text Text.
 * @param WP_Post $post Post.
 * @return string
 */
function qaf_offer_title_placeholder( $text, $post ) {
	return 'qaf_offer' === $post->post_type ? 'اسم العرض — مثال: عروض الشتاء على الفروات' : $text;
}
add_filter( 'enter_title_here', 'qaf_offer_title_placeholder', 10, 2 );

/**
 * Admin assets.
 */
function qaf_offer_admin_assets() {
	$screen = get_current_screen();
	if ( ! $screen || 'qaf_offer' !== $screen->post_type ) {
		return;
	}
	wp_enqueue_style( 'qaf-offers-admin', QAF_URI . 'assets/css/offers-admin.css', array(), (string) filemtime( QAF_DIR . 'assets/css/offers-admin.css' ) );
	wp_enqueue_script( 'qaf-offers-admin', QAF_URI . 'assets/js/offers-admin.js', array(), (string) filemtime( QAF_DIR . 'assets/js/offers-admin.js' ), true );
}
add_action( 'admin_enqueue_scripts', 'qaf_offer_admin_assets' );
