<?php
/**
 * Theme Options page — schema-driven, one tab at a time, per-language inputs.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;

/**
 * Menu.
 */
function qaf_options_menu() {
	add_menu_page( 'إعدادات الموقع', 'إعدادات الموقع', 'manage_options', 'qaf-options', 'qaf_render_options_page', 'dashicons-star-filled', 59 );
	add_submenu_page( 'qaf-options', 'خيارات الثيم', 'الإعدادات', 'manage_options', 'qaf-options', 'qaf_render_options_page' );
}
add_action( 'admin_menu', 'qaf_options_menu' );

/**
 * Save / reset handlers.
 */
function qaf_options_handle() {
	if ( empty( $_POST['qaf_options_action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Not allowed.', 'qafesheh' ) );
	}
	check_admin_referer( 'qaf_options_save', 'qaf_options_nonce' );

	$tab    = isset( $_POST['qaf_active_tab'] ) ? sanitize_key( wp_unslash( $_POST['qaf_active_tab'] ) ) : 'general';
	$action = sanitize_key( wp_unslash( $_POST['qaf_options_action'] ) );

	if ( 'reset' === $action ) {
		delete_option( 'qaf_options' );
		update_option( 'qaf_flush_rewrite', 1, false );
		$flag = 'reset';
	} else {
		$raw = isset( $_POST['qaf_options'] ) && is_array( $_POST['qaf_options'] ) ? wp_unslash( $_POST['qaf_options'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		update_option( 'qaf_options', qaf_sanitize_options( $raw ), true );
		$flag = 'saved';
	}

	qaf_options( true );
	do_action( 'litespeed_purge_all' );

	wp_safe_redirect( add_query_arg( array( 'page' => 'qaf-options', 'tab' => $tab, 'qaf-' . $flag => 1 ), admin_url( 'admin.php' ) ) );
	exit;
}
add_action( 'admin_init', 'qaf_options_handle' );

/**
 * Render page.
 */
function qaf_render_options_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$schema = qaf_options_schema();
	$opts   = qaf_options();
	$langs  = qaf_panel_languages();
	$def    = qaf_default_lang();
	$active = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'general'; // phpcs:ignore WordPress.Security.NonceVerification
	if ( ! isset( $schema[ $active ] ) ) {
		$active = 'general';
	}
	?>
	<div class="wrap qaf-opts" dir="rtl">
		<h1 class="screen-reader-text">إعدادات الموقع</h1>

		<div class="qaf-opts__head">
			<span class="qaf-opts__mark qaf-opts__mark--seal"><img src="<?php echo esc_url( QAF_URI . 'assets/images/logos/logo-qafesheh-seal.png' ); ?>" alt=""></span>
			<div class="qaf-opts__intro">
				<h2>إعدادات الموقع</h2>
				<p><?php echo function_exists( 'qaf_simple_admin' ) && qaf_simple_admin() ? 'الشعار، التواصل والسوشال، الفوتر، الوظائف والنماذج، ونصوص الواجهة — عدّل واضغط «حفظ التغييرات».' : 'كل إعدادات الموقع بمكان واحد — الألوان، الهيدر، شاشة التحميل، الفوتر، النصوص، الأمان والأداء.'; ?></p>
			</div>
			<span class="qaf-opts__pill"><?php echo esc_html( strtoupper( implode( ' · ', $langs ) ) ); ?></span>
			<span class="qaf-opts__ver">v<?php echo esc_html( QAF_VERSION ); ?></span>
		</div>

		<?php if ( isset( $_GET['qaf-saved'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification ?>
			<div class="notice notice-success is-dismissible"><p>تم حفظ الإعدادات.</p></div>
		<?php elseif ( isset( $_GET['qaf-reset'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification ?>
			<div class="notice notice-warning is-dismissible"><p>تمت إعادة الإعدادات للافتراضي.</p></div>
		<?php endif; ?>

		<form method="post" class="qaf-opts__body" id="qaf-opts-form">
			<?php wp_nonce_field( 'qaf_options_save', 'qaf_options_nonce' ); ?>
			<input type="hidden" name="qaf_active_tab" value="<?php echo esc_attr( $active ); ?>">

			<nav class="qaf-opts__tabs" aria-label="tabs">
				<?php foreach ( $schema as $tab_id => $tab ) : ?>
					<button type="button" class="qaf-opts__tab<?php echo $tab_id === $active ? ' is-active' : ''; ?>" data-tab="<?php echo esc_attr( $tab_id ); ?>">
						<span class="dashicons <?php echo esc_attr( $tab['icon'] ); ?>"></span><?php echo esc_html( $tab['label'] ); ?>
					</button>
				<?php endforeach; ?>
				<a class="qaf-opts__tab qaf-opts__tab--link" href="<?php echo esc_url( admin_url( 'admin.php?page=qaf-import' ) ); ?>"><span class="dashicons dashicons-download"></span>استيراد المحتوى</a>
			</nav>

			<div class="qaf-opts__panes">
				<?php foreach ( $schema as $tab_id => $tab ) : ?>
					<section class="qaf-opts__pane<?php echo $tab_id === $active ? ' is-active' : ''; ?>" data-pane="<?php echo esc_attr( $tab_id ); ?>">
						<h3 class="qaf-opts__pane-title"><?php echo esc_html( $tab['label'] ); ?></h3>
						<?php
						foreach ( $tab['fields'] as $key => $field ) {
							qaf_options_row( $key, $field, $opts, $langs, $def );
						}
						?>
					</section>
				<?php endforeach; ?>

				<div class="qaf-opts__actions">
					<button type="submit" name="qaf_options_action" value="save" class="button button-primary button-hero">حفظ التغييرات</button>
					<button type="submit" name="qaf_options_action" value="reset" class="button button-link-delete qaf-opts__reset">إعادة للافتراضي</button>
				</div>
			</div>
		</form>
	</div>
	<?php
}

/**
 * One row.
 *
 * @param string $key   Key.
 * @param array  $field Field.
 * @param array  $opts  Options.
 * @param array  $langs Languages.
 * @param string $def   Default language.
 */
function qaf_options_row( $key, $field, $opts, $langs, $def ) {
	if ( 'note' === $field['type'] || 'heading' === $field['type'] ) {
		printf(
			'<div class="qaf-opts__note"><strong>%s</strong>%s</div>',
			esc_html( $field['label'] ),
			! empty( $field['desc'] ) ? '<p>' . esc_html( $field['desc'] ) . '</p>' : ''
		);
		return;
	}
	$i18n = ! empty( $field['i18n'] ) && count( $langs ) > 1;
	?>
	<div class="qaf-opts__row">
		<div class="qaf-opts__label">
			<label><?php echo esc_html( $field['label'] ); ?></label>
			<?php if ( ! empty( $field['desc'] ) ) : ?>
				<p><?php echo esc_html( $field['desc'] ); ?></p>
			<?php endif; ?>
		</div>
		<div class="qaf-opts__ctrl">
			<?php if ( $i18n ) : ?>
				<div class="qaf-i18n">
					<div class="qaf-i18n__tabs">
						<?php foreach ( $langs as $i => $lang ) : ?>
							<button type="button" class="qaf-i18n__tab<?php echo 0 === $i ? ' is-active' : ''; ?>" data-lang="<?php echo esc_attr( $lang ); ?>"><?php echo esc_html( strtoupper( $lang ) ); ?></button>
						<?php endforeach; ?>
					</div>
					<?php foreach ( $langs as $i => $lang ) : ?>
						<?php
						$name  = $lang === $def ? $key : $key . '__' . $lang;
						$value = $opts[ $name ] ?? ( $lang === $def ? ( $field['default'] ?? '' ) : '' );
						$dir   = in_array( $lang, array( 'ar', 'he', 'fa', 'ur' ), true ) ? 'rtl' : 'ltr';
						?>
						<div class="qaf-i18n__pane<?php echo 0 === $i ? ' is-active' : ''; ?>" data-lang="<?php echo esc_attr( $lang ); ?>">
							<?php
							$field_for_input = $field;
							qaf_admin_control( $field_for_input, 'qaf_options[' . $name . ']', $value, '', array( 'dir' => $dir, 'lang' => $lang, 'placeholder' => $lang !== $def ? (string) ( $field[ 'default__' . $lang ] ?? '' ) : '' ) );
							?>
						</div>
					<?php endforeach; ?>
					<p class="qaf-i18n__hint">اترك الحقل فارغاً ليُستخدم النص الافتراضي لتلك اللغة.</p>
				</div>
			<?php else : ?>
				<?php qaf_admin_control( $field, 'qaf_options[' . $key . ']', $opts[ $key ] ?? '' ); ?>
			<?php endif; ?>
		</div>
	</div>
	<?php
}
