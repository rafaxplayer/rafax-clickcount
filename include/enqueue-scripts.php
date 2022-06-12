<?php
/**
 * File: rafax-clickcount/include/enqueue-scripts.php
 *
 * @package rafax_clickcount
 */

// Agrega el código JavaScript que estará vigilando nuestra web.
add_action( 'wp_enqueue_scripts', 'rafax_clickcount_enqueue_scripts' );
// Agrega el código JavaScript que estará vigilando tabla del plugin.
add_action( 'admin_enqueue_scripts', 'rafax_clickcount_enqueue_admin_scripts' );
/**
 * Agrega el fichero JavaScript a la cola
 *
 * @return void
 */
function rafax_clickcount_enqueue_scripts() {
	wp_enqueue_script(
		'rafax-clickcount',
		plugins_url( 'js/clickcount.js', rafax_CLICKCOUNT_PLUGIN_FILE ),
		array( 'jquery' ),
		rafax_CLICKCOUNT_VERSION,
		true
	);
	wp_localize_script(
		'rafax-clickcount',
		'AjaxParams',
		array(
			'adminAjaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'        => wp_create_nonce( 'clickcount-nonce' ),
		)
	);
}
function rafax_clickcount_enqueue_admin_scripts($hook){
	
	if($hook != 'toplevel_page_rafax_clickcount_menu'){
		return;
	}
	wp_enqueue_script(
		'admin-rafax-clickcount',
		plugins_url( 'js/admin.js', rafax_CLICKCOUNT_PLUGIN_FILE ),
		array( 'jquery' ),
		rafax_CLICKCOUNT_VERSION,
		true
	);
	wp_localize_script(
		'admin-rafax-clickcount',
		'AjaxParams',
		array(
			'adminAjaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'        => wp_create_nonce( 'clickcount-admin-nonce' ),
		)
	);
}
