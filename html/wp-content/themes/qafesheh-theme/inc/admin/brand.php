<?php
/**
 * The Qafesheh Group dashboard — wp-admin dressed as the group's own control panel.
 *
 * · Simple mode (default for everyone): a short, ordered menu in plain Arabic (الرئيسية،
 *   طلبات التوظيف، الرسائل، الوظائف، الأخبار، الشركات، الفروع، الصفحات، الصور، إعدادات الموقع),
 *   no WordPress/plugin clutter, no third-party notices, and a home screen with today's numbers,
 *   quick actions, the latest applications and messages, and what needs attention.
 * · Developer mode (per user, one click in the top bar): the full WordPress admin, untouched.
 * · Brand skin everywhere: group colours and font, the mark in the top bar, own footer.
 *
 * Nothing is locked: hidden screens stay reachable by URL for whoever has the capability.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

/* ================================================= Mode */

/**
 * Is the current user in simple mode?
 *
 * @return bool
 */
function qaf_simple_admin() {
	/* Never resolve the current user from here: this runs inside auth filters too. */
	if ( ! did_action( 'set_current_user' ) ) {
		return false;
	}
	$uid = get_current_user_id();
	return $uid && '1' !== (string) get_user_meta( $uid, 'qaf_dev_mode', true );
}

/**
 * Toggle developer mode (admin-post).
 */
function qaf_toggle_dev_mode() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Not allowed.' );
	}
	check_admin_referer( 'qaf_dev_mode' );
	update_user_meta( get_current_user_id(), 'qaf_dev_mode', qaf_simple_admin() ? '1' : '0' );
	wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url() );
	exit;
}
add_action( 'admin_post_qaf_dev_mode', 'qaf_toggle_dev_mode' );

/**
 * Count posts of a type carrying an unread flag.
 *
 * @param string $type Post type.
 * @param string $meta Meta key.
 * @param string $status Post status.
 * @return int
 */
function qaf_unread_count( $type, $meta, $status = 'private' ) {
	if ( ! post_type_exists( $type ) ) {
		return 0;
	}
	$q = new WP_Query(
		array(
			'post_type'      => $type,
			'post_status'    => $status,
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => $meta, // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'     => 1, // phpcs:ignore WordPress.DB.SlowDBQuery
			'no_found_rows'  => false,
		)
	);
	return (int) $q->found_posts;
}

/**
 * New job applications.
 *
 * @return int
 */
function qaf_new_applications() {
	return qaf_unread_count( 'qaf_application', '_qaf_app_unread', 'any' );
}

/**
 * Unread messages (contact, complaints, suggestions).
 *
 * @return int
 */
function qaf_new_entries() {
	return qaf_unread_count( 'qaf_entry', '_qaf_e_unread' );
}

/**
 * Applications screen URL.
 *
 * @return string
 */
function qaf_applications_url() {
	return 'edit.php?post_type=qaf_application';
}

/**
 * Small inline mark (stacked diamonds) used as the menu icon and in the top bar.
 *
 * @param string $color Fill.
 * @return string
 */
function qaf_admin_mark( $color = '#a7aaad' ) {
	$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><g fill="' . $color . '"><path d="M10 1l3 3-3 3-3-3z"/><path d="M10 7l3 3-3 3-3-3z"/><path d="M10 13l3 3-3 3-3-3z"/></g></svg>';
	return 'data:image/svg+xml;base64,' . base64_encode( $svg ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions
}

/* ================================================= Home screen */

/**
 * Register the home screen.
 */
function qaf_admin_home_menu() {
	add_menu_page( 'الرئيسية — مجموعة قفيشة', 'الرئيسية', 'edit_posts', 'qaf-home', 'qaf_render_admin_home', qaf_admin_mark(), 1 );
}
add_action( 'admin_menu', 'qaf_admin_home_menu', 5 );

/**
 * Dashboard → group home (simple mode).
 */
function qaf_admin_home_redirect() {
	if ( qaf_simple_admin() ) {
		wp_safe_redirect( admin_url( 'admin.php?page=qaf-home' ) );
		exit;
	}
}
add_action( 'load-index.php', 'qaf_admin_home_redirect' );

/**
 * Land on the home screen after login.
 *
 * @param string  $to   Redirect.
 * @param string  $req  Requested.
 * @param WP_User $user User.
 * @return string
 */
function qaf_login_redirect( $to, $req, $user ) {
	if ( $user instanceof WP_User && $user->has_cap( 'edit_posts' ) && ( ! $req || admin_url() === $req || false !== strpos( (string) $req, 'wp-admin/index.php' ) ) ) {
		return admin_url( 'admin.php?page=qaf-home' );
	}
	return $to;
}
add_filter( 'login_redirect', 'qaf_login_redirect', 20, 3 );

/**
 * Home screen.
 */
function qaf_render_admin_home() {
	$user    = wp_get_current_user();
	$hour    = (int) wp_date( 'G' );
	$greet   = $hour < 12 ? 'صباح الخير' : 'مساء الخير';
	$apps    = qaf_new_applications();
	$entries = qaf_new_entries();
	$jobs    = (int) wp_count_posts( 'qaf_job' )->publish;
	$news    = (int) wp_count_posts( 'post' )->publish;
	$home_id = (int) get_option( 'page_on_front' );
	$home_url = $home_id && did_action( 'elementor/loaded' ) && get_post_meta( $home_id, '_elementor_edit_mode', true )
		? admin_url( 'post.php?post=' . $home_id . '&action=elementor' )
		: ( $home_id ? (string) get_edit_post_link( $home_id, 'raw' ) : admin_url( 'edit.php?post_type=page' ) );

	$tiles = array(
		array( 'id-alt', 'طلبات التوظيف', 'السير الذاتية والمتقدمون', admin_url( qaf_applications_url() ), $apps ),
		array( 'email-alt', 'الرسائل', 'تواصل، شكاوى واقتراحات', admin_url( 'edit.php?post_type=qaf_entry' ), $entries ),
		array( 'megaphone', 'الوظائف', 'نشر وإغلاق الشواغر', admin_url( 'edit.php?post_type=qaf_job' ), 0 ),
		array( 'admin-post', 'الأخبار والمقالات', 'إضافة وتعديل الأخبار', admin_url( 'edit.php' ), 0 ),
		array( 'tag', 'العروض', function_exists( 'qaf_offers_live' ) && qaf_offers_live() ? sprintf( '%d عرض يعمل الآن', count( qaf_offers_live() ) ) : 'عروض الشركات لفترة محدودة', admin_url( 'edit.php?post_type=qaf_offer' ), 0 ),
		array( 'location', 'الفروع', 'العناوين وساعات الدوام', admin_url( 'edit.php?post_type=qaf_branch' ), 0 ),
		array( 'admin-home', 'الصفحة الرئيسية', 'الأقسام والنصوص والصور', $home_url, 0 ),
		array( 'admin-appearance', 'إعدادات الموقع', 'الشعار، الألوان، التواصل، الفوتر', admin_url( 'admin.php?page=qaf-options' ), 0 ),
	);

	$latest_apps    = post_type_exists( 'qaf_application' ) ? get_posts( array( 'post_type' => 'qaf_application', 'post_status' => 'any', 'posts_per_page' => 6 ) ) : array();
	$latest_entries = post_type_exists( 'qaf_entry' ) ? get_posts( array( 'post_type' => 'qaf_entry', 'post_status' => 'private', 'posts_per_page' => 4 ) ) : array();
	$open_jobs      = get_posts( array( 'post_type' => 'qaf_job', 'posts_per_page' => 20, 'fields' => 'ids' ) );
	$closed         = 0;
	foreach ( $open_jobs as $job_id ) {
		if ( function_exists( 'qaf_meta' ) && qaf_meta( 'closed', $job_id ) ) {
			++$closed;
		}
	}
	?>
	<div class="wrap qaf-home" dir="rtl">
		<h1 class="screen-reader-text">الرئيسية</h1>

		<section class="qaf-home__hero">
			<div>
				<p class="qaf-home__date"><?php echo esc_html( wp_date( 'l، j F Y' ) ); ?></p>
				<h2><?php echo esc_html( $greet . '، ' . ( $user->first_name ? $user->first_name : $user->display_name ) ); ?> <span aria-hidden="true">✦</span></h2>
				<p class="qaf-home__lead">لوحة تحكم مجموعة قفيشة — الأخبار، الوظائف، الرسائل، والشركات في مكان واحد.</p>
			</div>
			<div class="qaf-home__heroacts">
				<a class="qaf-home__btn is-primary" href="<?php echo esc_url( admin_url( 'post-new.php' ) ); ?>"><span class="dashicons dashicons-plus-alt2"></span>خبر جديد</a>
				<a class="qaf-home__btn" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank"><span class="dashicons dashicons-external"></span>زيارة الموقع</a>
			</div>
		</section>

		<section class="qaf-home__stats">
			<a class="qaf-home__stat<?php echo $apps ? ' is-hot' : ''; ?>" href="<?php echo esc_url( admin_url( qaf_applications_url() ) ); ?>"><span>طلبات توظيف جديدة</span><strong><?php echo esc_html( number_format_i18n( $apps ) ); ?></strong></a>
			<a class="qaf-home__stat<?php echo $entries ? ' is-hot' : ''; ?>" href="<?php echo esc_url( admin_url( 'edit.php?post_type=qaf_entry' ) ); ?>"><span>رسائل جديدة</span><strong><?php echo esc_html( number_format_i18n( $entries ) ); ?></strong></a>
			<a class="qaf-home__stat" href="<?php echo esc_url( admin_url( 'edit.php?post_type=qaf_job' ) ); ?>"><span>وظائف منشورة</span><strong><?php echo esc_html( number_format_i18n( $jobs ) ); ?></strong></a>
			<a class="qaf-home__stat" href="<?php echo esc_url( admin_url( 'edit.php' ) ); ?>"><span>أخبار منشورة</span><strong><?php echo esc_html( number_format_i18n( $news ) ); ?></strong></a>
		</section>

		<section class="qaf-home__tiles">
			<?php foreach ( $tiles as $t ) : ?>
				<a class="qaf-home__tile" href="<?php echo esc_url( $t[3] ); ?>">
					<span class="qaf-home__ticon"><span class="dashicons dashicons-<?php echo esc_attr( $t[0] ); ?>"></span></span>
					<strong><?php echo esc_html( $t[1] ); ?><?php if ( $t[4] ) : ?> <em><?php echo esc_html( (string) $t[4] ); ?></em><?php endif; ?></strong>
					<span><?php echo esc_html( $t[2] ); ?></span>
				</a>
			<?php endforeach; ?>
		</section>

		<div class="qaf-home__cols">
			<section class="qaf-home__card qaf-home__apps">
				<header><h3>آخر طلبات التوظيف</h3><a href="<?php echo esc_url( admin_url( qaf_applications_url() ) ); ?>">كل الطلبات ←</a></header>
				<?php if ( $latest_apps ) : ?>
					<table>
						<thead><tr><th>المتقدم</th><th>الوظيفة</th><th>الهاتف</th><th>الوقت</th></tr></thead>
						<tbody>
							<?php foreach ( $latest_apps as $a ) : ?>
								<?php
								$job_id = (int) get_post_meta( $a->ID, '_qaf_app_job', true );
								$new    = get_post_meta( $a->ID, '_qaf_app_unread', true );
								?>
								<tr<?php echo $new ? ' class="is-new"' : ''; ?>>
									<td><a href="<?php echo esc_url( (string) get_edit_post_link( $a->ID, 'raw' ) ); ?>"><?php echo esc_html( (string) get_post_meta( $a->ID, '_qaf_app_name', true ) ); ?></a></td>
									<td><?php echo esc_html( $job_id ? get_the_title( $job_id ) : '—' ); ?></td>
									<td dir="ltr"><?php echo esc_html( (string) get_post_meta( $a->ID, '_qaf_app_phone', true ) ); ?></td>
									<td><?php echo esc_html( human_time_diff( get_post_time( 'U', true, $a ) ) ); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php else : ?>
					<p class="qaf-home__empty">لا توجد طلبات توظيف بعد — أول طلب رح يظهر هون.</p>
				<?php endif; ?>
			</section>

			<aside class="qaf-home__side">
				<section class="qaf-home__card">
					<header><h3>بحاجة انتباه</h3></header>
					<ul class="qaf-home__attn">
						<?php if ( $apps ) : ?>
							<li class="is-hot"><a href="<?php echo esc_url( admin_url( qaf_applications_url() ) ); ?>"><span class="dashicons dashicons-id-alt"></span><?php echo esc_html( sprintf( '%d طلب توظيف لم يُقرأ', $apps ) ); ?></a></li>
						<?php endif; ?>
						<?php if ( $entries ) : ?>
							<li class="is-hot"><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=qaf_entry' ) ); ?>"><span class="dashicons dashicons-email-alt"></span><?php echo esc_html( sprintf( '%d رسالة لم تُقرأ', $entries ) ); ?></a></li>
						<?php endif; ?>
						<?php if ( ! $jobs ) : ?>
							<li><a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=qaf_job' ) ); ?>"><span class="dashicons dashicons-megaphone"></span>ما في وظائف منشورة حالياً</a></li>
						<?php elseif ( $closed ) : ?>
							<li><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=qaf_job' ) ); ?>"><span class="dashicons dashicons-megaphone"></span><?php echo esc_html( sprintf( '%d وظيفة مُغلقة — راجع إذا بدها تنشال', $closed ) ); ?></a></li>
						<?php endif; ?>
						<?php
						$qaf_ending = array();
						if ( function_exists( 'qaf_offers_live' ) ) {
							foreach ( qaf_offers_live( 20 ) as $qaf_of ) {
								$qaf_left = qaf_offer_ts( $qaf_of['end'] ) - time();
								if ( $qaf_of['end'] && $qaf_left > 0 && $qaf_left < 2 * DAY_IN_SECONDS ) {
									$qaf_ending[] = $qaf_of;
								}
							}
						}
						foreach ( $qaf_ending as $qaf_of ) :
							?>
							<li><a href="<?php echo esc_url( (string) get_edit_post_link( $qaf_of['id'], 'raw' ) ); ?>"><span class="dashicons dashicons-tag"></span><?php echo esc_html( sprintf( 'عرض «%s» — %s', get_the_title( $qaf_of['id'] ), qaf_offer_time_left( $qaf_of ) ) ); ?></a></li>
						<?php endforeach; ?>
						<?php if ( ! $apps && ! $entries && $jobs && ! $closed && ! $qaf_ending ) : ?>
							<li class="is-ok"><span class="dashicons dashicons-yes-alt"></span>كل شي تمام — ما في شي بحاجة انتباه.</li>
						<?php endif; ?>
					</ul>
				</section>
				<section class="qaf-home__card">
					<header><h3>آخر الرسائل</h3><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=qaf_entry' ) ); ?>">كل الرسائل ←</a></header>
					<?php if ( $latest_entries ) : ?>
						<ul class="qaf-home__msgs">
							<?php foreach ( $latest_entries as $m ) : ?>
								<?php
								$vals = (array) get_post_meta( $m->ID, '_qaf_e_values', true );
								$form = (string) get_post_meta( $m->ID, '_qaf_e_form', true );
								$defs = function_exists( 'qaf_forms' ) ? qaf_forms() : array();
								?>
								<li<?php echo get_post_meta( $m->ID, '_qaf_e_unread', true ) ? ' class="is-new"' : ''; ?>>
									<a href="<?php echo esc_url( (string) get_edit_post_link( $m->ID, 'raw' ) ); ?>">
										<strong><?php echo esc_html( ( $vals['name'] ?? '—' ) . ( isset( $defs[ $form ]['label'] ) ? ' · ' . $defs[ $form ]['label'] : '' ) ); ?></strong>
										<span><?php echo esc_html( wp_trim_words( (string) ( $vals['message'] ?? $vals['details'] ?? '' ), 9 ) ); ?></span>
										<small><?php echo esc_html( human_time_diff( get_post_time( 'U', true, $m ) ) ); ?></small>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php else : ?>
						<p class="qaf-home__empty">لا توجد رسائل بعد.</p>
					<?php endif; ?>
				</section>
			</aside>
		</div>

		<section class="qaf-home__help">
			<div>
				<strong>دليل سريع</strong>
				<ol>
					<li><b>وظيفة جديدة؟</b> «الوظائف» ← أضف وظيفة ← العنوان والوصف والقسم والموقع ← «نشر». التقديم بيوصلك على «طلبات التوظيف».</li>
					<li><b>خبر جديد؟</b> «الأخبار والمقالات» ← أضف جديد ← العنوان والنص والصورة البارزة ← «نشر».</li>
					<li><b>رسالة أو شكوى؟</b> «الرسائل» — كل رسالة إلها رقم متابعة، وبتقدر ترد على واتساب مباشرة.</li>
					<li><b>تعديل نصوص أو صور الرئيسية؟</b> «الصفحة الرئيسية» ← اضغط على أي قسم وعدّله مباشرة.</li>
				</ol>
			</div>
			<p class="qaf-home__support">الدعم الفني: <a href="https://qb.studio" target="_blank" rel="noopener">QB Studio</a></p>
		</section>
	</div>
	<?php
}

/* ================================================= Simple menu */

/**
 * Curate the menu in simple mode.
 */
function qaf_simple_menu() {
	if ( ! qaf_simple_admin() ) {
		return;
	}
	global $menu, $submenu;
	$keep = array(
		'qaf-home'                       => 'الرئيسية',
		'edit.php?post_type=qaf_application' => 'طلبات التوظيف',
		'edit.php?post_type=qaf_entry'   => null,
		'edit.php?post_type=qaf_job'     => 'الوظائف',
		'edit.php'                       => 'الأخبار والمقالات',
		'edit.php?post_type=qaf_offer'   => 'العروض',
		'edit.php?post_type=qaf_company' => 'الشركات',
		'edit.php?post_type=qaf_service' => 'الخدمات',
		'edit.php?post_type=qaf_branch'  => 'الفروع',
		'edit.php?post_type=page'        => 'الصفحات',
		'upload.php'                     => 'الصور والملفات',
		'qaf-options'                    => 'إعدادات الموقع',
		'profile.php'                    => 'حسابي',
	);
	if ( ! is_array( $menu ) ) {
		return;
	}
	/* Applications live under Jobs by default — give them their own entry. */
	if ( post_type_exists( 'qaf_application' ) ) {
		$n = qaf_new_applications();
		add_menu_page( 'طلبات التوظيف', 'طلبات التوظيف' . ( $n ? ' <span class="awaiting-mod"><span class="pending-count">' . $n . '</span></span>' : '' ), 'edit_posts', 'edit.php?post_type=qaf_application', '', 'dashicons-id-alt', 2 );
	}
	foreach ( $menu as $i => $item ) {
		$slug = $item[2] ?? '';
		if ( ! array_key_exists( $slug, $keep ) ) {
			unset( $menu[ $i ] );
			continue;
		}
		if ( $keep[ $slug ] ) {
			$bubble        = preg_match( '/<span class="awaiting-mod.*$/s', (string) $item[0], $m ) ? ' ' . $m[0] : '';
			$menu[ $i ][0] = $keep[ $slug ] . $bubble; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		}
	}
	/* Posts submenu: list + add + categories only. */
	if ( isset( $submenu['edit.php'] ) ) {
		foreach ( $submenu['edit.php'] as $k => $s ) {
			if ( ! in_array( $s[2], array( 'edit.php', 'post-new.php', 'edit-tags.php?taxonomy=category', 'edit-tags.php?taxonomy=category&amp;post_type=post' ), true ) ) {
				unset( $submenu['edit.php'][ $k ] ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
			}
		}
	}
	/* Site settings: options tabs + launch centre + team + menus; the importer stays for developer mode. */
	if ( isset( $submenu['qaf-options'] ) ) {
		foreach ( $submenu['qaf-options'] as $k => $s ) {
			if ( 'qaf-import' === $s[2] ) {
				unset( $submenu['qaf-options'][ $k ] ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
			}
		}
		$submenu['qaf-options'][] = array( 'القوائم', 'edit_theme_options', 'nav-menus.php' ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
	}
}
add_action( 'admin_menu', 'qaf_simple_menu', 9999 );

/**
 * Keep our order.
 *
 * @return bool
 */
function qaf_custom_menu_order() {
	return qaf_simple_admin();
}
add_filter( 'custom_menu_order', 'qaf_custom_menu_order' );

/**
 * Explicit order for the simple menu.
 *
 * @param array $order Slugs.
 * @return array
 */
function qaf_menu_order( $order ) {
	if ( ! qaf_simple_admin() ) {
		return $order;
	}
	$want = array(
		'qaf-home',
		'edit.php?post_type=qaf_application',
		'edit.php?post_type=qaf_entry',
		'edit.php?post_type=qaf_job',
		'edit.php',
		'edit.php?post_type=qaf_offer',
		'edit.php?post_type=qaf_company',
		'edit.php?post_type=qaf_service',
		'edit.php?post_type=qaf_branch',
		'edit.php?post_type=page',
		'upload.php',
		'qaf-options',
		'profile.php',
	);
	return array_merge( array_values( array_intersect( $want, $order ) ), array_values( array_diff( $order, $want ) ) );
}
add_filter( 'menu_order', 'qaf_menu_order', 99 );

/**
 * Hide third-party notices in simple mode (save messages are printed by the screens themselves).
 */
function qaf_quiet_notices() {
	if ( ! qaf_simple_admin() ) {
		return;
	}
	remove_all_actions( 'admin_notices' );
	remove_all_actions( 'all_admin_notices' );
	remove_all_actions( 'network_admin_notices' );
	if ( function_exists( 'qaf_2fa_nudge' ) ) {
		add_action( 'admin_notices', 'qaf_2fa_nudge' );
	}
}
add_action( 'in_admin_header', 'qaf_quiet_notices', 1000 );

/* ================================================= Top bar */

/**
 * Brand the admin bar; in simple mode keep only what helps.
 *
 * @param WP_Admin_Bar $bar Bar.
 */
function qaf_admin_bar( $bar ) {
	if ( ! is_user_logged_in() ) {
		return;
	}
	$bar->remove_node( 'wp-logo' );
	if ( qaf_simple_admin() ) {
		foreach ( array( 'updates', 'comments', 'new-content', 'customize', 'search', 'themes', 'widgets', 'menus', 'dashboard', 'appearance', 'site-editor', 'litespeed-menu', 'WPML_ALS', 'wpml-als' ) as $n ) {
			$bar->remove_node( $n );
		}
	}
	$bar->add_node(
		array(
			'id'    => 'qaf-brand',
			'title' => '<svg class="qaf-ab-svg" width="14" height="18" viewBox="0 0 14 20" aria-hidden="true"><g fill="#D9B85C"><path d="M7 1l3 3-3 3-3-3z"/><path d="M7 7l3 3-3 3-3-3z"/><path d="M7 13l3 3-3 3-3-3z"/></g></svg><span class="qaf-ab-name">مجموعة قفيشة</span>',
			'href'  => is_admin() ? home_url( '/' ) : admin_url( 'admin.php?page=qaf-home' ),
			'meta'  => array( 'title' => is_admin() ? 'زيارة الموقع' : 'لوحة التحكم' ),
		)
	);
	$apps = qaf_new_applications();
	$bar->add_node( array( 'id' => 'qaf-apps', 'title' => '<span class="ab-icon dashicons dashicons-id-alt"></span>طلبات التوظيف' . ( $apps ? ' <span class="qaf-ab-count">' . $apps . '</span>' : '' ), 'href' => admin_url( qaf_applications_url() ) ) );
	$bar->add_node( array( 'id' => 'qaf-new', 'title' => '<span class="ab-icon dashicons dashicons-plus-alt2"></span>خبر جديد', 'href' => admin_url( 'post-new.php' ) ) );
	if ( current_user_can( 'manage_options' ) ) {
		$bar->add_node(
			array(
				'id'     => 'qaf-devmode',
				'parent' => 'top-secondary',
				'title'  => qaf_simple_admin() ? 'وضع المطوّر' : '<span class="qaf-ab-dev">● وضع المطوّر مفعّل</span>',
				'href'   => wp_nonce_url( admin_url( 'admin-post.php?action=qaf_dev_mode' ), 'qaf_dev_mode' ),
				'meta'   => array( 'title' => qaf_simple_admin() ? 'إظهار كل قوائم ووردبريس والإضافات (لك فقط)' : 'رجوع للوحة المبسّطة' ),
			)
		);
	}
}
add_action( 'admin_bar_menu', 'qaf_admin_bar', 999 );

/**
 * Footer text + version.
 *
 * @return string
 */
function qaf_admin_footer_text() {
	return '<span class="qaf-admin-foot">لوحة تحكم <b>مجموعة قفيشة</b> · تصميم وتطوير <a href="https://qb.studio" target="_blank" rel="noopener">QB Studio</a></span>';
}
add_filter( 'admin_footer_text', 'qaf_admin_footer_text', 99 );
add_filter( 'update_footer', static fn() => qaf_simple_admin() ? '' : 'v' . QAF_VERSION, 99 );

/**
 * Tab title without "— WordPress".
 *
 * @param string $title Title.
 * @return string
 */
function qaf_admin_title( $title ) {
	return preg_replace( '/\s*(?:[—–-]|&#8212;|&mdash;|&#8211;)\s*WordPress\s*$/u', '', $title );
}
add_filter( 'admin_title', 'qaf_admin_title' );

/* ================================================= Skin */

/**
 * Brand skin on every admin screen.
 */
function qaf_admin_skin() {
	wp_enqueue_style( 'qaf-admin-fonts', QAF_URI . 'assets/css/qaf-fonts.css', array(), QAF_VERSION );
	wp_enqueue_style( 'qaf-admin-skin', QAF_URI . 'assets/css/admin-skin.css', array( 'qaf-admin-fonts' ), QAF_VERSION );
}
add_action( 'admin_enqueue_scripts', 'qaf_admin_skin', 20 );

/**
 * Same top bar branding on the front for logged-in users.
 */
function qaf_front_bar_skin() {
	if ( is_admin_bar_showing() ) {
		wp_enqueue_style( 'qaf-admin-bar-skin', QAF_URI . 'assets/css/admin-bar.css', array(), QAF_VERSION );
	}
}
add_action( 'wp_enqueue_scripts', 'qaf_front_bar_skin', 30 );

/**
 * Body class for simple mode.
 *
 * @param string $classes Classes.
 * @return string
 */
function qaf_admin_body_class( $classes ) {
	return $classes . ( qaf_simple_admin() ? ' qaf-simple' : ' qaf-dev' );
}
add_filter( 'admin_body_class', 'qaf_admin_body_class' );

/**
 * A short "how to" box on the job editor — the screen the client uses most.
 */
function qaf_job_help_box() {
	add_meta_box( 'qaf_job_help', 'خطوات نشر وظيفة', 'qaf_job_help_render', 'qaf_job', 'side', 'high' );
}
add_action( 'add_meta_boxes_qaf_job', 'qaf_job_help_box', 99 );

/**
 * The box.
 */
function qaf_job_help_render() {
	?>
	<ol class="qaf-help-steps">
		<li><b>العنوان</b> — المسمّى الوظيفي.</li>
		<li><b>الوصف</b> — المهام والمتطلبات.</li>
		<li><b>القسم</b> و<b>موقع العمل</b> و<b>نوع الدوام</b> من الجانب.</li>
		<li><b>الشركة</b> التابع لها الشاغر.</li>
		<li>اضغط <b>نشر</b> — الوظيفة بتظهر بصفحة الوظائف مع نموذج التقديم.</li>
		<li>لما تنتهي: علّم <b>«مغلقة»</b> بدل ما تحذفها، حتى تضل بالأرشيف.</li>
	</ol>
	<p class="qaf-help-note">طلبات التقديم بتوصل على <a href="<?php echo esc_url( admin_url( qaf_applications_url() ) ); ?>">طلبات التوظيف</a> مع السيرة الذاتية.</p>
	<?php
}

/**
 * Applications have their own top-level entry: highlight it, and drop the duplicate under Jobs.
 *
 * @param string $parent Parent file.
 * @return string
 */
function qaf_admin_parent_file( $parent ) {
	global $typenow;
	if ( 'qaf_application' === $typenow ) {
		return 'edit.php?post_type=qaf_application';
	}
	return $parent;
}
add_filter( 'parent_file', 'qaf_admin_parent_file', 99 );

/**
 * Submenu highlight for applications.
 *
 * @param string|null $file Submenu file.
 * @return string|null
 */
function qaf_admin_submenu_file( $file ) {
	global $typenow;
	return 'qaf_application' === $typenow ? 'edit.php?post_type=qaf_application' : $file;
}
add_filter( 'submenu_file', 'qaf_admin_submenu_file', 99 );

/**
 * Remove the duplicate applications item under Jobs (simple mode).
 */
function qaf_jobs_submenu() {
	global $submenu;
	if ( ! qaf_simple_admin() || empty( $submenu['edit.php?post_type=qaf_job'] ) ) {
		return;
	}
	foreach ( $submenu['edit.php?post_type=qaf_job'] as $k => $s ) {
		if ( false !== strpos( (string) $s[2], 'post_type=qaf_application' ) || false !== strpos( (string) $s[2], 'taxonomy=post_tag' ) ) {
			unset( $submenu['edit.php?post_type=qaf_job'][ $k ] ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		}
	}
}
add_action( 'admin_menu', 'qaf_jobs_submenu', 10000 );

/**
 * Messages and applications are private by nature — no "— Private" label on every row.
 *
 * @param array   $states States.
 * @param WP_Post $post   Post.
 * @return array
 */
function qaf_hide_private_state( $states, $post ) {
	if ( in_array( $post->post_type, array( 'qaf_entry', 'qaf_application' ), true ) ) {
		unset( $states['private'] );
	}
	return $states;
}
add_filter( 'display_post_states', 'qaf_hide_private_state', 10, 2 );

/**
 * Block editor defaults (no welcome tour / fullscreen) for everyone.
 */
function qaf_block_editor_defaults() {
	wp_enqueue_script( 'qaf-admin-editor', QAF_URI . 'assets/js/admin-editor.js', array( 'wp-data', 'wp-dom-ready', 'wp-preferences' ), QAF_VERSION, true );
}
add_action( 'enqueue_block_editor_assets', 'qaf_block_editor_defaults' );

/* ================================================= Quick contact on messages and applications */

/**
 * Register the box.
 */
function qaf_quick_contact_box() {
	foreach ( array( 'qaf_entry', 'qaf_application' ) as $type ) {
		add_meta_box( 'qaf_quick_contact', 'تواصل سريع', 'qaf_quick_contact_render', $type, 'side', 'high' );
	}
}
add_action( 'add_meta_boxes', 'qaf_quick_contact_box', 40 );

/**
 * Normalise a Jordanian number for wa.me.
 *
 * @param string $phone Phone.
 * @return string
 */
function qaf_wa_number( $phone ) {
	$d = preg_replace( '/\D/', '', (string) $phone );
	if ( '' === $d ) {
		return '';
	}
	if ( 0 === strpos( $d, '00' ) ) {
		$d = substr( $d, 2 );
	} elseif ( 0 === strpos( $d, '07' ) && 10 === strlen( $d ) ) {
		$d = '962' . substr( $d, 1 );
	}
	return $d;
}

/**
 * Box content: WhatsApp, call, email — and the CV for applications.
 *
 * @param WP_Post $post Post.
 */
function qaf_quick_contact_render( $post ) {
	if ( 'qaf_application' === $post->post_type ) {
		$name  = (string) get_post_meta( $post->ID, '_qaf_app_name', true );
		$phone = (string) get_post_meta( $post->ID, '_qaf_app_phone', true );
		$email = (string) get_post_meta( $post->ID, '_qaf_app_email', true );
		$job   = (int) get_post_meta( $post->ID, '_qaf_app_job', true );
		$cv    = (int) get_post_meta( $post->ID, '_qaf_app_cv', true );
		$topic = $job ? 'طلب التوظيف — ' . get_the_title( $job ) : 'طلب التوظيف';
		$hello = sprintf( 'مرحباً %s، معك مجموعة قفيشة بخصوص طلبك لوظيفة %s.', $name, $job ? get_the_title( $job ) : '' );
	} else {
		$vals  = (array) get_post_meta( $post->ID, '_qaf_e_values', true );
		$name  = (string) ( $vals['name'] ?? '' );
		$phone = (string) ( $vals['phone'] ?? '' );
		$email = (string) ( $vals['email'] ?? '' );
		$track = (string) get_post_meta( $post->ID, '_qaf_e_tracking', true );
		$cv    = 0;
		$topic = 'رسالتك إلى مجموعة قفيشة' . ( $track ? ' — ' . $track : '' );
		$hello = sprintf( 'مرحباً %s، معك مجموعة قفيشة بخصوص رسالتك%s.', $name, $track ? ' رقم ' . $track : '' );
	}
	$wa = qaf_wa_number( $phone );
	echo '<div class="qaf-qc">';
	if ( $wa ) {
		echo '<a class="qaf-qc__btn is-wa" href="' . esc_url( 'https://wa.me/' . $wa . '?text=' . rawurlencode( $hello ) ) . '" target="_blank" rel="noopener"><span class="dashicons dashicons-whatsapp"></span>واتساب</a>';
		echo '<a class="qaf-qc__btn" href="' . esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $phone ) ) . '"><span class="dashicons dashicons-phone"></span>اتصال</a>';
	}
	if ( is_email( $email ) ) {
		echo '<a class="qaf-qc__btn" href="' . esc_url( 'mailto:' . $email . '?subject=' . rawurlencode( $topic ) . '&body=' . rawurlencode( $hello . "\n\n" ) ) . '"><span class="dashicons dashicons-email-alt"></span>إيميل</a>';
	}
	if ( $cv && wp_get_attachment_url( $cv ) ) {
		echo '<a class="qaf-qc__btn is-cv" href="' . esc_url( (string) wp_get_attachment_url( $cv ) ) . '" target="_blank" rel="noopener"><span class="dashicons dashicons-media-document"></span>السيرة الذاتية</a>';
	}
	if ( ! $wa && ! is_email( $email ) && ! $cv ) {
		echo '<p class="description">ما في رقم أو إيميل بهذا الطلب.</p>';
	}
	echo '</div>';
}

/**
 * Application passwords are a developer tool — only for users in developer mode.
 * Uses the per-user filter (it receives the user) so it never has to resolve the current user,
 * which would loop through determine_current_user.
 *
 * @param bool    $available Available.
 * @param WP_User $user      User.
 * @return bool
 */
function qaf_app_passwords_for_user( $available, $user ) {
	return $user instanceof WP_User && '1' === (string) get_user_meta( $user->ID, 'qaf_dev_mode', true ) ? $available : false;
}
add_filter( 'wp_is_application_passwords_available_for_user', 'qaf_app_passwords_for_user', 10, 2 );

/**
 * Everyone gets the default colour scheme (the skin replaces it anyway).
 */
remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );
