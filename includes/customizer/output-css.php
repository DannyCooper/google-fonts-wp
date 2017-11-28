<?php
/**
 * Output the Google Fonts CSS.
 *
 * @package     olympus-google-fonts
 * @copyright   Copyright (c) 2017, Danny Cooper
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

/**
 * Output the font CSS to wp_head.
 */
function ogf_output_css() {
	?>
	<!-- Olympus Google Fonts CSS -->
	<style type="text/css">

		<?php ogf_generate_css( 'body', 'ogf_body_font' ); ?>
		<?php ogf_generate_css( '.site-title, h1, h2, h3, h4, h5, h6', 'ogf_headings_font' ); ?>
		<?php ogf_generate_css( 'button, input, select, textarea', 'ogf_inputs_font' ); ?>

		/* Advanced Settings */

		<?php ogf_generate_css( '.site-title', 'ogf_site_title_font' ); ?>
		<?php ogf_generate_css( '.site-description', 'ogf_site_description_font' ); ?>
		<?php ogf_generate_css( '.menu', 'ogf_navigation_font' ); ?>
		<?php ogf_generate_css( 'article h1, article h2, article h3, article h4, article h5, article h6', 'ogf_post_page_headings_font' ); ?>
		<?php ogf_generate_css( 'article', 'ogf_post_page_content_font' ); ?>
		<?php ogf_generate_css( '.widget-area h1, .widget-area h2, .widget-area h3, .widget-area h4, .widgets-area h5, .widget-area h6', 'ogf_sidebar_headings_font' ); ?>
		<?php ogf_generate_css( '.widget-area', 'ogf_sidebar_content_font' ); ?>
		<?php ogf_generate_css( 'footer h1, footer h2, footer h3, footer h4, .widgets-area h5, footer h6', 'ogf_footer_headings_font' ); ?>
		<?php ogf_generate_css( 'footer', 'ogf_footer_content_font' ); ?>

	</style>
	<!--/ Olympus Google Fonts CSS -->
	<?php
}

// Output custom CSS to live site.
add_action( 'wp_head' , 'ogf_output_css' );

/**
 * Helper function to build the CSS styles.
 *
 * @param string $selector The CSS selector to apply the styles to.
 * @param string $option_name The option name to pull from the database.
 */
function ogf_generate_css( $selector, $option_name ) {
	$return = '';

	if ( false !== get_theme_mod( $option_name ) ) {

		$stack = ogf_build_font_stack( get_theme_mod( $option_name ) );

		if ( ! empty( $stack ) && 'default' !== $stack ) {
			$return = sprintf('%s { font-family: %s; }',
				$selector,
				$stack . ogf_is_forced()
			);
		}
		echo wp_kses_post( $return );

	}
}

/**
 * Check if the styles should be forced.
 */
function ogf_is_forced() {

	if ( '1' === get_theme_mod( 'ogf_force_styles' ) ) {
		return ' !important';
	}

}
