<?php
/**
 * File: rafax-click-count/include/plugin-init.php
 *
 * @package rafax_clickcount
 */

defined( 'ABSPATH' ) || die();

add_action( 'plugins_loaded', 'rafax_clickcount_textdomain' );
/**
 * Declara donde se encuentran los ficheros de traducción del plugin
 *
 * @return void
 */
function rafax_clickcount_textdomain() {
	$translation_path = 'rafax-clickcount/languages';
	load_plugin_textdomain( 'rafax-clickcount', false, $translation_path );
}
