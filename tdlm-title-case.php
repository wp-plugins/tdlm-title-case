<?php
/**
 * @package Tdlm_TitleCase
 * @version 1.0.0
 */

/*
Plugin Name: TDLM Title Case
Plugin URI: http://scodub.com
Description: Change your post title to correct title case or all lowercase or all uppercase.
Author: TDLM
Version: 1.0.0
Author URI: http://scodub.com
*/

defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );

/**
 * Filter Callback: get_sample_permalink_html
 *
 * @param $return
 * @param $id
 * @param $new_title
 * @param $new_slug
 *
 * @return string
 */
function tdlm_get_sample_permalink_html( $return, $id, $new_title, $new_slug ) {

	$translate = '__';

	$return .= <<<END
<select name="tdlm_case" class="button button-small button-tdlm-change-case">
<option value="">{$translate( '-- Change Case --', 'tdlm_titlecase' )}</option>
<option value="title">{$translate( 'Title Case', 'tdlm_titlecase' )}</option>
<option value="lower">{$translate( 'Lowercase', 'tdlm_titlecase' )}</option>
<option value="upper">{$translate( 'Uppercase', 'tdlm_titlecase' )}</option>
</select>
END;

	return $return;
}

/**
 * Filter Callback: load-post.php
 */
function tdlm_load_post() {
	add_action( 'in_admin_footer', 'tdlm_in_admin_footer' );
}

/**
 * Filter Callback: in_admin_footer
 */
function tdlm_in_admin_footer() { ?>
	<script type="text/javascript">
		(function($) {
			$.fn.toTitleCase = function() {
				var original = $(this).val(),
					lowers = [<?php echo tdlm_title_case_lowercase_get_js(); ?>],
					uppers = [<?php echo tdlm_title_case_uppercase_get_js(); ?>],
					title, i, j;

				title = original.replace(/([^\W_]+[^\s-]*) */g, function(txt) {
					return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
				});

				for (i = 0, j = lowers.length; i < j; i++) {
					title = title.replace(new RegExp('\\s' + lowers[i] + '\\s', 'g'),
						function(txt) {
							return txt.toLowerCase();
						});
				}

				for (i = 0, j = uppers.length; i < j; i++) {
					title = title.replace(new RegExp('\\b' + uppers[i] + '\\b', 'g'),
						uppers[i].toUpperCase());
				}

				$(this).val(title);

				return this;
			};
		})(jQuery);

		(function($) {
			$('select.button-tdlm-change-case').change(function(e) {

				switch ($(this).val()) {
					case 'title':
						$('#title').toTitleCase();
						break;
					case 'lower':
						$('#title').val($('#title').val().toLowerCase());
						break;
					case 'upper':
						$('#title').val($('#title').val().toUpperCase());
						break;
					default:
					case '':
						break;
				}

			});

			$('#title').keyup(function(e) {
				$('select.button-tdlm-change-case').val('');
			});

		})(jQuery);
	</script>
<?php
}

/**
 * Title Case Options Page
 */
function tdlm_title_case_options_page() {
	?>
	<div>
		<form action="options.php" method="post">
			<?php settings_fields( 'tdlm-title-case' ); ?>
			<?php do_settings_sections( 'tdlm-title-case' ); ?>
			<?php submit_button(); ?>
		</form>
	</div>
<?php

}

/**
 * Title Case Admin Menu
 */
function tdlm_title_case_admin_menu() {
	add_options_page(
		__( 'TDLM Title Case', 'tdlm_titlecase' ),
		__( 'TDLM Title Case', 'tdlm_titlecase' ),
		'manage_options',
		'tdlm-title-case.php',
		'tdlm_title_case_options_page'
	);
}

/**
 * Lowercase Word Output for JS
 *
 * @return string
 */
function tdlm_title_case_lowercase_get_js() {
	$lowercase_options = tdlm_title_case_lowercase_get_array();

	return implode( ",", array_map( function ( $val ) {
		return "'$val'";
	}, $lowercase_options ) );
}

/**
 * Lowercase Word Output to Array
 *
 * @return array
 */
function tdlm_title_case_lowercase_get_array() {
	$lowercase_options = tdlm_title_case_lowercase_get();
	$lowercase_options = explode( ',', $lowercase_options );

	return array_map( 'trim', $lowercase_options );
}

/**
 * Lowercase Word Output
 *
 * @return mixed
 */
function tdlm_title_case_lowercase_get() {
	return get_option( 'tdlm_titlecase_lowercase', tdlm_title_case_lowercase_default() );
}

/**
 * Default for Lowercase Words
 *
 * @return string
 */
function tdlm_title_case_lowercase_default() {
	return 'A, An, The, And, But, Or, For, Nor, As, At, By, For, From, In, Into, Near, Of, On, Onto, To, With';
}

/**
 * Uppercase Word Output for JS
 *
 * @return string
 */
function tdlm_title_case_uppercase_get_js() {
	$uppercase_options = tdlm_title_case_uppercase_get_array();

	return implode( ",", array_map( function ( $val ) {
		return "'$val'";
	}, $uppercase_options ) );
}

/**
 * Uppercase Word Output to Array
 *
 * @return array
 */
function tdlm_title_case_uppercase_get_array() {
	$uppercase_options = tdlm_title_case_uppercase_get();
	$uppercase_options = explode( ',', $uppercase_options );

	return array_map( 'trim', $uppercase_options );
}

/**
 * Uppercase Word Output
 *
 * @return mixed
 */
function tdlm_title_case_uppercase_get() {
	return get_option( 'tdlm_titlecase_uppercase', tdlm_title_case_uppercase_default() );
}

/**
 * Default for Uppercase Words
 *
 * @return string
 */
function tdlm_title_case_uppercase_default() {
	return 'Id, Tv';
}

/**
 * Admin Header (blank for now)
 */
function tdlm_title_case_admin_header() {
	echo '';
}

/**
 * Callback for lowercase textarea on options page
 *
 * @param $args
 */
function tdlm_title_case_lowercase_callback( $args ) {
	$html = '<p><label for="tdlm_titlecase_lowercase">' . __( "When the Title Case action encounters any of the words below, it will keep them lowercase. This is a comma-separated list.", "tdlm_titlecase" ) . '</label></p>';
	$html .= '<textarea name="tdlm_titlecase_lowercase" id="tdlm_titlecase_lowercase" rows="10" cols="50" class="large-text code">' . tdlm_title_case_lowercase_get() . '</textarea>';
	echo $html;
}

/**
 * Callback for uppercase textarea on options page
 *
 * @param $args
 */
function tdlm_title_case_uppercase_callback( $args ) {
	$html = '<p><label for="tdlm_titlecase_uppercase">' . __( "When the Title Case action encounters any of the words below, it will keep them uppercase. This is a comma-separated list.", "tdlm_titlecase" ) . '</label></p>';
	$html .= '<textarea name="tdlm_titlecase_uppercase" id="tdlm_titlecase_uppercase" rows="10" cols="50" class="large-text code">' . tdlm_title_case_uppercase_get() . '</textarea>';
	echo $html;
}

/**
 * Title Case: Admin Init
 */
function tdlm_title_case_admin_init() {

	register_setting(
		'tdlm-title-case',
		'tdlm_titlecase_lowercase'
	);

	register_setting(
		'tdlm-title-case',
		'tdlm_titlecase_uppercase'
	);

	add_settings_section(
		'tdlm_titlecase_section',
		__( 'TDLM Title Case Settings', 'tdlm_titlecase' ),
		'tdlm_title_case_admin_header',
		'tdlm-title-case'
	);

	add_settings_field(
		'tdlm_titlecase_lowercase',
		__( 'Lowercase Words', 'tdlm_titlecase' ),
		'tdlm_title_case_lowercase_callback',
		'tdlm-title-case',
		'tdlm_titlecase_section'
	);

	add_settings_field(
		'tdlm_titlecase_uppercase',
		__( 'Uppercase Words', 'tdlm_titlecase' ),
		'tdlm_title_case_uppercase_callback',
		'tdlm-title-case',
		'tdlm_titlecase_section'
	);
}

/**
 * Title Case: Init
 */
function tdlm_title_case_init() {
	if ( ! is_admin() ) {
		return;
	}

	add_filter( 'get_sample_permalink_html', 'tdlm_get_sample_permalink_html' );
	add_filter( 'load-post.php', 'tdlm_load_post' );

	add_action( 'admin_menu', 'tdlm_title_case_admin_menu' );
	add_action( 'admin_init', 'tdlm_title_case_admin_init' );
}

add_action( 'init', 'tdlm_title_case_init' );