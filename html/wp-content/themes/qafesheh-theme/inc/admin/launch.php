<?php
/**
 * Launch centre — the handover screen for the group site.
 *
 * · Readiness checklist: content, identity, the three companies, careers, forms, security.
 * · Branches data sheet: export every branch to CSV, edit in Excel, upload back (matched by id).
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

/**
 * Menu entry.
 */
function qaf_launch_menu() {
	add_submenu_page( 'qaf-options', 'مركز الإطلاق', 'مركز الإطلاق', 'manage_options', 'qaf-launch', 'qaf_render_launch_page' );
}
add_action( 'admin_menu', 'qaf_launch_menu', 20 );

/**
 * A readiness row.
 *
 * @param array  $rows   Rows by reference.
 * @param string $group  Group.
 * @param string $label  Label.
 * @param string $state  ok|warn|todo.
 * @param string $detail Detail.
 * @param string $url    Fix link.
 */
function qaf_launch_row( &$rows, $group, $label, $state, $detail = '', $url = '' ) {
	$rows[ $group ][] = compact( 'label', 'state', 'detail', 'url' );
}

/**
 * Build the checklist.
 *
 * @return array
 */
function qaf_launch_rows() {
	$rows = array();
	$opt  = static fn( $k ) => trim( (string) qaf_opt( $k ) );

	/* Content */
	$companies = (int) wp_count_posts( 'qaf_company' )->publish;
	qaf_launch_row( $rows, 'المحتوى', 'صفحات الشركات', $companies >= 3 ? 'ok' : 'todo', sprintf( '%d شركة منشورة (المطلوب ٣)', $companies ), admin_url( 'edit.php?post_type=qaf_company' ) );
	$branches = (int) wp_count_posts( 'qaf_branch' )->publish;
	qaf_launch_row( $rows, 'المحتوى', 'الفروع', $branches ? 'ok' : 'todo', sprintf( '%d فرع', $branches ), admin_url( 'edit.php?post_type=qaf_branch' ) );
	$news = (int) wp_count_posts( 'post' )->publish;
	qaf_launch_row( $rows, 'المحتوى', 'الأخبار', $news >= 3 ? 'ok' : 'warn', sprintf( '%d خبر منشور', $news ), admin_url( 'edit.php' ) );
	$jobs = (int) wp_count_posts( 'qaf_job' )->publish;
	qaf_launch_row( $rows, 'المحتوى', 'الوظائف', $jobs ? 'ok' : 'warn', sprintf( '%d وظيفة', $jobs ), admin_url( 'edit.php?post_type=qaf_job' ) );
	qaf_launch_row( $rows, 'المحتوى', 'الصفحة الرئيسية معيّنة', get_option( 'page_on_front' ) ? 'ok' : 'todo', get_option( 'page_on_front' ) ? get_the_title( (int) get_option( 'page_on_front' ) ) : 'غير معيّنة', admin_url( 'options-reading.php' ) );

	/* Placeholders left in content */
	$holders = get_posts(
		array(
			'post_type'      => array( 'page', 'post', 'qaf_company', 'qaf_service', 'qaf_job', 'qaf_branch' ),
			'posts_per_page' => 5,
			'fields'         => 'ids',
			's'              => '[',
		)
	);
	$found = array();
	foreach ( $holders as $id ) {
		if ( preg_match( '/\[[^\]]{3,40}\]/u', (string) get_post_field( 'post_content', $id ), $m ) && false === strpos( $m[0], 'qaf_' ) && false === strpos( $m[0], 'caption' ) ) {
			$found[] = get_the_title( $id );
		}
	}
	qaf_launch_row( $rows, 'المحتوى', 'نصوص مؤقتة بين [ ]', $found ? 'warn' : 'ok', $found ? implode( '، ', array_slice( $found, 0, 3 ) ) : 'ما في نصوص مؤقتة' );

	/* Identity & contact */
	qaf_launch_row( $rows, 'الهوية والتواصل', 'الشعار', $opt( 'logo' ) ? 'ok' : 'todo', $opt( 'logo' ) ? 'مرفوع' : 'الشعار الافتراضي', admin_url( 'admin.php?page=qaf-options' ) );
	qaf_launch_row( $rows, 'الهوية والتواصل', 'الهاتف والواتساب', $opt( 'c_phone' ) && $opt( 's_whatsapp' ) ? 'ok' : 'warn', $opt( 'c_phone' ) . ( $opt( 's_whatsapp' ) ? ' · واتساب ✓' : ' · بدون واتساب' ), admin_url( 'admin.php?page=qaf-options&tab=contact' ) );
	$email = $opt( 'c_email' );
	qaf_launch_row( $rows, 'الهوية والتواصل', 'البريد الرسمي', $email && false === strpos( $email, 'gmail' ) ? 'ok' : 'warn', $email ? $email : 'غير محدد', admin_url( 'admin.php?page=qaf-options&tab=contact' ) );
	$socials = array_filter( array( $opt( 's_instagram' ), $opt( 's_facebook' ), $opt( 's_youtube' ) ) );
	qaf_launch_row( $rows, 'الهوية والتواصل', 'روابط السوشال', $socials ? 'ok' : 'warn', sprintf( '%d رابط', count( $socials ) ), admin_url( 'admin.php?page=qaf-options&tab=contact' ) );

	/* Careers & forms */
	qaf_launch_row( $rows, 'الوظائف والرسائل', 'إيميل استقبال طلبات التوظيف', $opt( 'apply_email' ) || get_option( 'admin_email' ) ? 'ok' : 'todo', $opt( 'apply_email' ) ? $opt( 'apply_email' ) : get_option( 'admin_email' ), admin_url( 'admin.php?page=qaf-options&tab=careers' ) );
	qaf_launch_row( $rows, 'الوظائف والرسائل', 'إيميل استقبال الرسائل', $opt( 'form_contact_email' ) ? 'ok' : 'warn', $opt( 'form_contact_email' ) ? $opt( 'form_contact_email' ) : 'فارغ — بيروح لإيميل مدير الموقع', admin_url( 'admin.php?page=qaf-options&tab=forms' ) );
	qaf_launch_row( $rows, 'الوظائف والرسائل', 'إيميل الشكاوى', $opt( 'form_complaint_email' ) ? 'ok' : 'warn', $opt( 'form_complaint_email' ) ? $opt( 'form_complaint_email' ) : 'فارغ — بيروح لإيميل مدير الموقع', admin_url( 'admin.php?page=qaf-options&tab=forms' ) );
	qaf_launch_row( $rows, 'الوظائف والرسائل', 'تجربة إرسال نموذج', 'warn', 'ابعث رسالة تجريبية من صفحة التواصل وتأكد إنها وصلت على الإيميل (مش السبام).', home_url( '/' ) );

	/* Technical */
	qaf_launch_row( $rows, 'تقني', 'الروابط الدائمة', get_option( 'permalink_structure' ) ? 'ok' : 'todo', get_option( 'permalink_structure' ) ? 'مضبوطة' : 'افتراضية — لازم تتغيّر', admin_url( 'options-permalink.php' ) );
	qaf_launch_row( $rows, 'تقني', 'ظهور الموقع لمحركات البحث', get_option( 'blog_public' ) ? 'ok' : 'todo', get_option( 'blog_public' ) ? 'ظاهر' : 'مخفي — شيل العلامة قبل الإطلاق', admin_url( 'options-reading.php' ) );
	qaf_launch_row( $rows, 'تقني', 'WPML', defined( 'ICL_SITEPRESS_VERSION' ) ? 'ok' : 'warn', defined( 'ICL_SITEPRESS_VERSION' ) ? 'مفعّل' : 'غير مفعّل — الموقع بلغة وحدة' );
	qaf_launch_row( $rows, 'تقني', 'كاش الموقع', defined( 'LSCWP_V' ) || defined( 'WP_ROCKET_VERSION' ) ? 'ok' : 'warn', defined( 'LSCWP_V' ) ? 'LiteSpeed Cache مفعّل' : 'ما في إضافة كاش' );

	/* Security */
	if ( function_exists( 'qaf_security_checks' ) ) {
		foreach ( qaf_security_checks() as $c ) {
			qaf_launch_row( $rows, 'الأمان', $c['label'], $c['state'], $c['detail'], admin_url( 'admin.php?page=qaf-options&tab=security' ) );
		}
	}
	return $rows;
}

/**
 * The screen.
 */
function qaf_render_launch_page() {
	$rows  = qaf_launch_rows();
	$all   = array_merge( ...array_values( $rows ) );
	$done  = count( array_filter( $all, static fn( $r ) => 'ok' === $r['state'] ) );
	$total = count( $all );
	$pct   = $total ? (int) round( $done / $total * 100 ) : 0;
	?>
	<div class="wrap qaf-launch" dir="rtl">
		<h1>مركز الإطلاق</h1>
		<p class="qaf-launch__lead">قائمة واحدة تقول لك: الموقع جاهز للإطلاق ولا لسا. ولما تصير كلها ✓، سلّم بثقة.</p>

		<section class="qaf-launch__meter">
			<div class="qaf-launch__bar"><span style="width:<?php echo absint( $pct ); ?>%"></span></div>
			<strong><?php echo esc_html( sprintf( '%d من %d جاهزة (%d%%)', $done, $total, $pct ) ); ?></strong>
		</section>

		<?php foreach ( $rows as $group => $items ) : ?>
			<section class="qaf-launch__group">
				<h2><?php echo esc_html( $group ); ?></h2>
				<ul>
					<?php foreach ( $items as $r ) : ?>
						<li class="is-<?php echo esc_attr( $r['state'] ); ?>">
							<span class="qaf-launch__mark"><?php echo 'ok' === $r['state'] ? '✓' : ( 'warn' === $r['state'] ? '!' : '✕' ); ?></span>
							<span class="qaf-launch__label"><?php echo esc_html( $r['label'] ); ?></span>
							<span class="qaf-launch__detail"><?php echo esc_html( $r['detail'] ); ?></span>
							<?php if ( $r['url'] ) : ?>
								<a class="qaf-launch__fix" href="<?php echo esc_url( $r['url'] ); ?>">فتح ←</a>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</section>
		<?php endforeach; ?>

		<section class="qaf-launch__group" id="sheet">
			<h2>ورقة بيانات الفروع</h2>
			<p>نزّل كل الفروع بملف واحد، عدّل العناوين وساعات الدوام والهواتف بإكسل، وارفعه — التحديث بيتم بمطابقة رقم الفرع.</p>
			<p class="qaf-launch__acts">
				<a class="button button-primary" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=qaf_branch_export' ), 'qaf_branch_sheet' ) ); ?>">⬇ تنزيل ورقة الفروع</a>
			</p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data" class="qaf-launch__upload">
				<input type="hidden" name="action" value="qaf_branch_import">
				<?php wp_nonce_field( 'qaf_branch_sheet' ); ?>
				<input type="file" name="sheet" accept=".csv,text/csv" required>
				<button type="submit" class="button">⬆ رفع الورقة وتحديث الفروع</button>
			</form>
			<?php
			$msg = get_transient( 'qaf_sheet_msg' );
			delete_transient( 'qaf_sheet_msg' );
			if ( $msg ) :
				?>
				<div class="notice notice-info inline"><p><?php echo esc_html( (string) $msg ); ?></p></div>
			<?php endif; ?>
			<p class="description">الخانات الفاضية ما بتتغيّر، وعمود «رقم الفرع» ما بينعدّل.</p>
		</section>
	</div>
	<style>
		.qaf-launch{max-width:1100px}
		.qaf-launch__lead{color:#756B5E;max-width:640px}
		.qaf-launch__meter{display:flex;align-items:center;gap:14px;margin:18px 0 22px}
		.qaf-launch__bar{flex:1;height:10px;border-radius:99px;background:#E8E2D7;overflow:hidden}
		.qaf-launch__bar span{display:block;height:100%;background:#B8912F}
		.qaf-launch__group{background:#FFFCF6;border:1px solid #D6CDBD;border-radius:16px;padding:18px 22px;margin-bottom:16px}
		.qaf-launch__group h2{margin:0 0 10px;font-size:16px;display:flex;align-items:center;gap:8px}
		.qaf-launch__group h2::before{content:"";width:7px;height:7px;background:#B8912F;transform:rotate(45deg)}
		.qaf-launch__group ul{margin:0;list-style:none}
		.qaf-launch__group li{display:flex;align-items:center;gap:12px;padding:9px 0;border-bottom:1px dashed #E8E2D7;font-size:13px}
		.qaf-launch__group li:last-child{border-bottom:0}
		.qaf-launch__mark{width:22px;height:22px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#fff;background:#8f8474;flex-shrink:0}
		li.is-ok .qaf-launch__mark{background:#2F6B3A}
		li.is-warn .qaf-launch__mark{background:#B8912F}
		li.is-todo .qaf-launch__mark{background:#A6402A}
		.qaf-launch__label{min-width:230px;font-weight:600;color:#0F0E0C}
		.qaf-launch__detail{flex:1;color:#756B5E}
		.qaf-launch__fix{font-size:12px}
		.qaf-launch__acts{margin:12px 0}
		.qaf-launch__upload{display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-bottom:8px}
	</style>
	<?php
}

/* ================================================= Branches data sheet */

/**
 * Columns: key => [ label, meta key or false for post title ].
 *
 * @return array
 */
function qaf_branch_sheet_columns() {
	return array(
		'id'      => array( 'رقم الفرع', false ),
		'name'    => array( 'اسم الفرع', false ),
		'city'    => array( 'المدينة', 'city' ),
		'address' => array( 'العنوان', 'address' ),
		'hours'   => array( 'الدوام', 'hours' ),
		'phone'   => array( 'الهاتف', 'phone' ),
		'map'     => array( 'رابط الخريطة', 'map_url' ),
	);
}

/**
 * CSV-safe cell (formula injection) — the entries screen has its own; keep ours local.
 *
 * @param string $v Value.
 * @return string
 */
function qaf_sheet_cell( $v ) {
	$v = (string) $v;
	return '' !== $v && in_array( $v[0], array( '=', '+', '-', '@' ), true ) ? "'" . $v : $v;
}

/**
 * Export.
 */
function qaf_branch_export() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Not allowed.' );
	}
	check_admin_referer( 'qaf_branch_sheet' );
	$cols = qaf_branch_sheet_columns();
	nocache_headers();
	header( 'Content-Type: text/csv; charset=UTF-8' );
	header( 'Content-Disposition: attachment; filename="qafesheh-branches-' . wp_date( 'Y-m-d' ) . '.csv"' );
	$out = fopen( 'php://output', 'w' );
	fwrite( $out, "\xEF\xBB\xBF" ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	fputcsv( $out, array_map( static fn( $c ) => $c[0], $cols ) );
	foreach ( get_posts( array( 'post_type' => 'qaf_branch', 'posts_per_page' => -1, 'orderby' => 'menu_order', 'order' => 'ASC' ) ) as $branch ) {
		$row = array();
		foreach ( $cols as $key => $col ) {
			if ( 'id' === $key ) {
				$row[] = $branch->ID;
			} elseif ( 'name' === $key ) {
				$row[] = qaf_sheet_cell( $branch->post_title );
			} else {
				$row[] = qaf_sheet_cell( (string) get_post_meta( $branch->ID, '_qaf_' . $col[1], true ) );
			}
		}
		fputcsv( $out, $row );
	}
	fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	exit;
}
add_action( 'admin_post_qaf_branch_export', 'qaf_branch_export' );

/**
 * Import (match by branch id, empty cells skipped).
 */
function qaf_branch_import() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Not allowed.' );
	}
	check_admin_referer( 'qaf_branch_sheet' );
	$file = $_FILES['sheet']['tmp_name'] ?? ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	if ( ! $file || ! is_uploaded_file( $file ) ) {
		set_transient( 'qaf_sheet_msg', 'ما وصل ملف — جرّب مرة ثانية.', 60 );
		wp_safe_redirect( admin_url( 'admin.php?page=qaf-launch#sheet' ) );
		exit;
	}
	$handle = fopen( $file, 'r' ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	$cols   = array_keys( qaf_branch_sheet_columns() );
	$defs   = qaf_branch_sheet_columns();
	$first  = true;
	$done   = 0;
	while ( false !== ( $row = fgetcsv( $handle ) ) ) {
		if ( $first ) {
			$first = false;
			continue;
		}
		$id = absint( $row[0] ?? 0 );
		if ( ! $id || 'qaf_branch' !== get_post_type( $id ) ) {
			continue;
		}
		$changed = false;
		foreach ( $cols as $i => $key ) {
			$value = isset( $row[ $i ] ) ? trim( (string) $row[ $i ] ) : '';
			if ( 'id' === $key || '' === $value ) {
				continue;
			}
			if ( 'name' === $key ) {
				if ( $value !== get_the_title( $id ) ) {
					wp_update_post( array( 'ID' => $id, 'post_title' => sanitize_text_field( $value ) ) );
					$changed = true;
				}
				continue;
			}
			$meta = '_qaf_' . $defs[ $key ][1];
			if ( $value !== (string) get_post_meta( $id, $meta, true ) ) {
				update_post_meta( $id, $meta, sanitize_text_field( $value ) );
				$changed = true;
			}
		}
		$done += $changed ? 1 : 0;
	}
	fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	set_transient( 'qaf_sheet_msg', sprintf( 'تم تحديث %d فرع.', $done ), 60 );
	wp_safe_redirect( admin_url( 'admin.php?page=qaf-launch#sheet' ) );
	exit;
}
add_action( 'admin_post_qaf_branch_import', 'qaf_branch_import' );
