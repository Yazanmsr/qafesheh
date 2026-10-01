<?php
/**
 * Site header: top bar + masthead.
 *
 * @package Qafesheh
 */

defined( 'ABSPATH' ) || exit;
?>
<header class="qaf-header" id="qaf-header">
	<?php if ( qaf_opt( 'topbar' ) ) : ?>
		<?php get_template_part( 'template-parts/header/topbar' ); ?>
	<?php endif; ?>

	<div class="qaf-mast">
		<div class="qaf-container qaf-mast__in">
			<?php qaf_logo( 'header' ); ?>
			<?php get_template_part( 'template-parts/header/nav-desktop' ); ?>
			<?php get_template_part( 'template-parts/header/actions' ); ?>
		</div>
	</div>
</header>
