<?php
/**
 * File: rafax-click-count/include/create-table.php
 *
 * @package rafax_clickcount
 */

defined( 'ABSPATH' ) || die();

register_activation_hook( rafax_CLICKCOUNT_PLUGIN_FILE, 'rafax_clickcount_create_table' );
/**
 * Crea las tablas necesarias durante la activación del plugin
 *
 * @return void
 */
function rafax_clickcount_create_table() {
	global $wpdb;
	$sql             = array();
	$table           = $wpdb->prefix . 'rafax_clickcount';
	$charset_collate = $wpdb->get_charset_collate();
	// Consulta para crear las tablas
	// Mas adelante utiliza dbDelta, si la tabla ya existe no la crea sino que la
	// modifica con los posibles cambios y sin pérdida de datos.
	$sql[] = "CREATE TABLE $table (
		id mediumint(9) UNSIGNED NOT NULL AUTO_INCREMENT,
		link varchar(250) NOT NULL UNIQUE,
		clicks int(11) UNSIGNED,
		date_first_click datetime,
		date_last_click datetime,
		PRIMARY KEY (id)
		) $charset_collate";
	include_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );
}

//Eliminar datos de la tabla
add_action('wp_ajax_eliminardatos','rafax_clickcount_delete_data');

function rafax_clickcount_delete_data(){
	$nonce= $_POST['nonce'];
	if(!wp_verify_nonce($nonce,'clickcount-admin-nonce')){
		die('No tienes permisos para elimnar nada');
	}
	$id = $_POST['id'];
	global $wpdb;
	$sql             = array();
	$table           = $wpdb->prefix . 'rafax_clickcount';
	$wpdb->delete( $table, array( 'id' => $id ));
	wp_die();
}



// Para usuarios autenticados.
add_action( 'wp_ajax_rafax-clickcount-link', 'rafax_clickcount_procesa_click' );
// Para usuarios NO autenticados.
add_action( 'wp_ajax_nopriv_rafax-clickcount-link', 'rafax_clickcount_procesa_click' );
/**
 * Busca si el link que le llega está dado de alta en la tabla rafax_click_count
 * Crea un nuevo registro con contador = 1 o incrementa el contador si el
 * registro ya existía.
 *
 * @return void
 */
function rafax_clickcount_procesa_click() {
	global $wpdb;
	$table = $wpdb->prefix . 'rafax_clickcount';
	// phpcs:ignore WordPress.Security.NonceVerification
	if ( ! isset( $_POST['link'] ) ) {
		die();
	}
	// phpcs:ignore WordPress.Security.NonceVerification
	$link = esc_url_raw( wp_unslash( $_POST['link'] ) );
	// Muy importantes los acentos graves en el nombre de la tabla !
	// Igual de importante que no poner comillas a los nombres de campo !
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery
	$row = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT id, clicks FROM `{$wpdb->prefix}rafax_clickcount` 
				WHERE link = %s",
			array( $link )
		)
	);
	// Si el link está en la tabla incrementa contador, y si no agrégalo.
	if ( $row ) {
		$id     = $row->id;
		$clicks = $row->clicks + 1;
		$data   = array(
			'clicks'          => $clicks,
			'date_last_click' => date( 'Y-m-d H:i:s' ),
		); // actualiza esto.
		$where  = array( 'id' => $id ); // cuando se cumpla esto.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->update( $table, $data, $where );
	} else {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->insert(
			$table,
			array(
				'link'             => $link,
				'clicks'           => 1,
				'date_first_click' => date( 'Y-m-d H:i:s' ),
				'date_last_click'  => date( 'Y-m-d H:i:s' ),
			)
		);
	}
	//echo esc_url_raw( $link );
	wp_die(); // imprescindible en ajax.
}

