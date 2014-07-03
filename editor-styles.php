<?php
/**
 * @package   Editor_Styles
 * @author    Ram Ratan Maurya <ram@mauryaratan.me>
 * @license   GPL-2.0+
 * @link      https://mauryaratan.me
 * @copyright 2014 Ram Ratan Maurya
 *
 * Plugin Name:       Editor Styles
 * Plugin URI:        https://codestag.com/plugins/editor-styles
 * Description:       Enhance your site’s content with Editor styles. Adds button, horizonal line, alerts, lists, and serveral styles.
 * Version:           1.0.0
 * Author:            Ram Ratan Maurya
 * Author URI:        https://mauryaratan.me
 * Text Domain:       eds
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/mauryaratan/editor-styles
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Editor_Styles' ) ) :
/**
 * @package Editor_Styles
 */
final class Editor_Styles {

	/**
	 * @var Editor_Styles The single instance of the class
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main Editor_Styles Instance.
	 *
	 * Ensures only one instance of Editor_Styles is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @return Editor_Styles - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
			self::$_instance->setup_constants();
			self::$_instance->includes();
			self::$_instance->load_textdomain();
		}
		return self::$_instance;
	}

	/**
	 * Plugin Constructor.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'theme_setup' ) );

		// Add the button
		add_action( 'admin_init', array( $this, 'add_button_button' ), 11 );

		// Reorder the hr button
		add_filter( 'tiny_mce_before_init', array( $this, 'tiny_mce_before_init' ), 20, 2 );

		// Add translations for plugin
		add_filter( 'wp_mce_translation', array( $this, 'wp_mce_translation' ), 10, 2 );

		// Add styles and scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts_and_styles' ) );

		// Add the CSS for the icon
		add_action( 'admin_print_styles-post.php', array( $this, 'admin_print_styles' ) );
		add_action( 'admin_print_styles-post-new.php', array( $this, 'admin_print_styles' ) );
	}

	/**
	 * Setup plugin constants.
	 *
	 * @access private
	 * @return void
	 */
	private function setup_constants() {

		// Plugin version
		if ( ! defined( 'EDS_VERSION' ) ) {
			define( 'EDS_VERSION', '1.0.0' );
		}

		// Plugin Folder Path
		if ( ! defined( 'EDS_PLUGIN_DIR' ) ) {
			define( 'EDS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Folder URL
		if ( ! defined( 'EDS_PLUGIN_URL' ) ) {
			define( 'EDS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin Root File
		if ( ! defined( 'EDS_PLUGIN_FILE' ) ) {
			define( 'EDS_PLUGIN_FILE', __FILE__ );
		}
	}

	public function load_textdomain () {
		load_plugin_textdomain ( 'eds', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Include required files.
	 *
	 * @access private
	 * @since 1.0.0
	 * @return void
	 */
	private function includes() {

	}

	public function scripts_and_styles() {
		if ( ! is_admin() ) {
			wp_register_style( 'font-awesome', EDS_PLUGIN_URL . 'css/font-awesome.css', array(), '4.1.0', 'screen' );

			if ( ! wp_style_is( 'font-awesome', 'enqueued' ) ) {
				wp_enqueue_style( 'font-awesome' );
			}

			wp_enqueue_style( 'eds-style', EDS_PLUGIN_URL . 'css/eds-style.css', array( 'font-awesome' ), EDS_VERSION );
		}
	}

	public function theme_setup() {
		$editor_styles[] = EDS_PLUGIN_URL . 'css/font-awesome.css';
		$editor_styles[] = EDS_PLUGIN_URL . 'css/eds-style.css';

		add_editor_style( $editor_styles );
	}

	/**
	 * Implement the TinyMCE button for creating a button.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function add_button_button() {
		if ( ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) ) {
			return;
		}

		add_filter( 'mce_external_plugins', array( $this, 'add_tinymce_plugin' ) );
		add_filter( 'mce_buttons', array( $this, 'register_mce_button' ), 10, 2 );
	}

	/**
	 * Implement the TinyMCE plugin for creating a button.
	 *
	 * @since  1.0.0.
	 *
	 * @param  array    $plugins    The current array of plugins.
	 * @return array                The modified plugins array.
	 */
	public function add_tinymce_plugin( $plugins ) {
		$plugins['eds_mce_hr_button']     = EDS_PLUGIN_URL . 'js/tinymce-hr.js';
		$plugins['eds_mce_button_button'] = EDS_PLUGIN_URL . 'js/tinymce-button.js';

		return $plugins;
	}

	/**
	 * Implement the TinyMCE button for creating a button.
	 *
	 * @since  1.0.0.
	 *
	 * @param  array     $buttons      The current array of plugins.
	 * @param  string    $editor_id    The ID for the current editor.
	 * @return array                   The modified plugins array.
	 */
	public function register_mce_button( $buttons, $editor_id ) {
		$buttons[] = 'eds_mce_hr_button';
		$buttons[] = 'eds_mce_button_button';

		return $buttons;
	}

	/**
	 * Position the new hr button in the place that the old hr usually resides.
	 *
	 * @since  1.0.0.
	 *
	 * @param  array     $mceInit      The configuration for the current editor.
	 * @param  string    $editor_id    The ID for the current editor.
	 * @return array                   The modified configuration array.
	 */
	public function tiny_mce_before_init( $mceInit, $editor_id ) {
		if ( ! empty( $mceInit['toolbar1'] ) ) {
			if ( in_array( 'hr', explode( ',', $mceInit['toolbar1'] ) ) ) {
				// Remove the current positioning of the new hr button
				$mceInit['toolbar1'] = str_replace( ',hr,', ',eds_mce_hr_button,', $mceInit['toolbar1'] );

				// Remove the duplicated new hr button
				$pieces              = explode( ',', $mceInit['toolbar1'] );
				$pieces              = array_unique( $pieces );
				$mceInit['toolbar1'] = implode( ',', $pieces );
			}
		}

		return $mceInit;
	}

	/**
	 * Add translations for plugin.
	 *
	 * @since  1.0.0.
	 *
	 * @param  array     $mce_translation    Key/value pairs of strings.
	 * @param  string    $mce_locale         Locale.
	 * @return array                         The updated translation array.
	 */
	public function wp_mce_translation( $mce_translation, $mce_locale ) {
		$additional_items = array(
			'Add button'    => __( 'Add button', 'eds' ),
			'Insert Button' => __( 'Insert Button', 'eds' ),
			'Button text'   => __( 'Button text', 'eds' ),
			'Button URL'    => __( 'Button URL', 'eds' ),
			'Normal'        => __( 'Normal', 'eds' ),
			'Alert'         => __( 'Alert', 'eds' ),
			'Download'      => __( 'Download', 'eds' ),
			'Color'         => __( 'Color', 'eds' ),
			'Primary'       => __( 'Primary', 'eds' ),
			'Secondary'     => __( 'Secondary', 'eds' ),
			'Green'         => __( 'Green', 'eds' ),
			'Red'           => __( 'Red', 'eds' ),
			'Orange'        => __( 'Orange', 'eds' ),
			'Style'         => __( 'Style', 'eds' ),

			// Horizontal line
			'Dashed'        => __( 'Dashed', 'eds' ),
			'Dotted'        => __( 'Dotted', 'eds' ),
			'Double'        => __( 'Double', 'eds' ),
			'Plain'         => __( 'Plain', 'eds' ),
			'Strong'        => __( 'Strong', 'eds' ),
		);

		return array_merge( $mce_translation, $additional_items );
	}

	/**
	 * Print CSS for the buttons.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function admin_print_styles() {
	?>
		<style type="text/css">
			i.mce-i-eds-button-button {
				font: normal 20px/1 'dashicons';
				padding: 0;
				vertical-align: top;
				speak: none;
				-webkit-font-smoothing: antialiased;
				-moz-osx-font-smoothing: grayscale;
				margin-left: -2px;
				padding-right: 2px;
			}
			i.mce-i-eds-button-button:before {
				content: '\f502';
			}
		</style>
	<?php
	}
}
endif; // End if class_exists check

/**
 * The main function responsible for returning the one true Editor_Styles
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $eds = EDS(); ?>
 *
 * @since 1.0.0.
 * @return object The one true Editor_Styles Instance
 */
function EDS() {
	return Editor_Styles::instance();
}

// Get EDS Running
EDS();
