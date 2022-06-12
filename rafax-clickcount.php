<?php
/**
 * Plugin Name:   rafax ClickCount
 * Description:   Cuenta los clics en enlaces o botones de tu web
 * Plugin URI:   
 * Version:       1.2
 * Text Domain:   rafax-clickcount
 * Domain Path: /languages
 * Plugin Author: Juan Rafael Simarro
 * Author URI:    
 *
 * @package rafax_clickcount
 */

defined( 'ABSPATH' ) || die();

define( 'rafax_CLICKCOUNT_DIR', plugin_dir_path( __FILE__ ) );
define( 'rafax_CLICKCOUNT_PLUGIN_FILE', __FILE__ );
define( 'rafax_CLICKCOUNT_VERSION', '1.1.0' );

// Inicializa el plugin.
require_once rafax_CLICKCOUNT_DIR . 'include/plugin-init.php';
// Crea la tabla para los enlaces al activar el plugin y ademas funciones para modificar datos.
require_once rafax_CLICKCOUNT_DIR . 'include/database.php';
// Llama al fichero JavaScript que estará vigilando la pulsación de enlaces.
require_once rafax_CLICKCOUNT_DIR . 'include/enqueue-scripts.php';

// Panel administrativo para ver el contenido de la tabla en el escritorio.
require_once rafax_CLICKCOUNT_DIR . 'include/admin-click-list.php';
// Shortcode para mostrar la lista de clics en escritorio o en la web.
require_once rafax_CLICKCOUNT_DIR . 'include/public-click-list.php';
