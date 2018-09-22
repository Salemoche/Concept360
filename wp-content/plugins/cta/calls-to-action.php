<?php
/*
Plugin Name: Calls to Action
Plugin URI: http://www.inboundnow.com/cta/
Description: Display Targeted Calls to Action on your WordPress site.
Version: 3.3.8
Author: Inbound Now
Author URI: https://www.inboundnow.com/
Text Domain: cta
Domain Path: assets/lang
*/

if (!class_exists('Inbound_Calls_To_Action_Plugin')) {

	/**
	 * Class Inbound_Automation_Plugin
	 * @package CTA
	 */
	final class Inbound_Calls_To_Action_Plugin {

		/**
		* Main Inbound_Calls_To_Action_Plugin Instance
		*/
		public function __construct() {
			self::define_constants();
			self::includes();
			self::load_text_domain_init();
			self::load_shared_files();
		}

		/*
		* Setup plugin constants
		*
		*/
		private static function define_constants() {

			define('WP_CTA_CURRENT_VERSION', '3.3.7' );
			define('WP_CTA_URLPATH', plugins_url( '/' , __FILE__ ) );
			define('WP_CTA_PATH', plugin_dir_path( __FILE__ ) );
			define('WP_CTA_SLUG', 'cta' );
			define('WP_CTA_FILE', __FILE__ );
			$uploads = wp_upload_dir();
			define('WP_CTA_UPLOADS_PATH', $uploads['basedir'].'/calls-to-action/templates/' );
			define('WP_CTA_UPLOADS_URLPATH', $uploads['baseurl'].'/calls-to-action/templates/' );
			define('WP_CTA_THEME_TEMPLATES_PATH' , get_template_directory(). '/calls-to-action/' );
			define('WP_CTA_THEME_TEMPLATES_URLPATH' , get_template_directory_uri(). '/calls-to-action/' );
			define('WP_CTA_STORE_URL', 'http://www.inboundnow.com/market/' );

		}

		/* Include required plugin files */
		private static function includes() {

			switch (is_admin()) :
				case true :
					/* loads admin files */
					include_once( WP_CTA_PATH . 'classes/class.settings.php');
					include_once( WP_CTA_PATH . 'classes/class.activation.php');
					include_once( WP_CTA_PATH . 'classes/class.activation.database-routines.php');
					include_once( WP_CTA_PATH . 'classes/class.post-type.wp-call-to-action.php');
					include_once( WP_CTA_PATH . 'classes/class.extension.wp-lead.php');
					include_once( WP_CTA_PATH . 'classes/class.extension.wordpress-seo.php');
					include_once( WP_CTA_PATH . 'classes/class.metaboxes.wp-call-to-action.php');
					include_once( WP_CTA_PATH . 'classes/class.menus.php');
					include_once( WP_CTA_PATH . 'classes/class.ajax.listeners.php');
					include_once( WP_CTA_PATH . 'classes/class.enqueues.php');
					include_once( WP_CTA_PATH . 'classes/class.clone-post.php');
					include_once( WP_CTA_PATH . 'classes/class.cta.variations.php');
					include_once( WP_CTA_PATH . 'classes/class.widget.static.php');
					include_once( WP_CTA_PATH . 'classes/class.widget.dynamic.php');
					include_once( WP_CTA_PATH . 'classes/class.load-extensions.php');
					include_once( WP_CTA_PATH . 'classes/class.cta.render.php');
					include_once( WP_CTA_PATH . 'classes/class.metaboxes.global.php');
					include_once( WP_CTA_PATH . 'classes/class.templates.list-table.php');
					include_once( WP_CTA_PATH . 'classes/class.templates.manage.php');
					include_once( WP_CTA_PATH . 'modules/module.utils.php');
					include_once( WP_CTA_PATH . 'classes/class.tracking.php');
					include_once( WP_CTA_PATH . 'classes/class.admin.notices.php');

					BREAK;

				case false :
					/* load front-end files */
					include_once( WP_CTA_PATH . 'classes/class.settings.php');
					include_once( WP_CTA_PATH . 'classes/class.load-extensions.php');
					include_once( WP_CTA_PATH . 'classes/class.post-type.wp-call-to-action.php');
					include_once( WP_CTA_PATH . 'classes/class.extension.wp-lead.php');
					include_once( WP_CTA_PATH . 'classes/class.extension.wordpress-seo.php');
					include_once( WP_CTA_PATH . 'classes/class.enqueues.php');
					include_once( WP_CTA_PATH . 'classes/class.tracking.php');
					include_once( WP_CTA_PATH . 'classes/class.ajax.listeners.php');
					include_once( WP_CTA_PATH . 'classes/class.widget.static.php');
					include_once( WP_CTA_PATH . 'classes/class.widget.dynamic.php');
					include_once( WP_CTA_PATH . 'classes/class.cta.variations.php');
					include_once( WP_CTA_PATH . 'classes/class.cta.render.php');
					include_once( WP_CTA_PATH . 'modules/module.utils.php');


					BREAK;
			endswitch;
		}

		/**
		 *  Loads components shared between Inbound Now plugins
		 */
		private static function load_shared_files() {
			if (!defined('INBOUND_PRO_PATH')) {
				add_action( 'plugins_loaded', array( 'Inbound_Load_Shared' , 'init') , 1 );
				include_once( WP_CTA_PATH . 'shared/classes/class.load-shared.php');
			}
		}

		/**
		*  Loads the correct .mo file for this plugin
		*/
		private static function load_text_domain_init() {
			add_action( 'init' , array(__CLASS__, 'load_text_domain' ) );
		}

		public static function load_text_domain() {
			load_plugin_textdomain( 'cta' , false , WP_CTA_SLUG . 'assets/lang/' );
		}

		/* START PHP VERSION CHECKS */
		/**
		 * Admin notices, collected and displayed on proper action
		 *
		 * @var array
		 */
		public static $notices = array();

		/**
		 * Whether the current PHP version meets the minimum requirements
		 *
		 * @return bool
		 */
		public static function is_valid_php_version() {
			return version_compare( PHP_VERSION, '5.3', '>=' );
		}

		/**
		 * Invoked when the PHP version check fails. Load up the translations and
		 * add the error message to the admin notices
		 */
		static function fail_php_version() {
			//add_action( 'plugins_loaded', array( __CLASS__, 'load_text_domain_init' ) );
			$plugin_url = admin_url( 'plugins.php' );
			self::notice( __( 'Calls to Action requires PHP version 5.3+ to run. Your version '.PHP_VERSION.' is not high enough.<br><u>Please contact your hosting provider</u> to upgrade your PHP Version.<br>The plugin is NOT Running. You can disable this warning message by <a href="'.$plugin_url.'">deactivating the plugin</a>', 'inbound-pro' ) );
		}

		/**
		 * Handle notice messages according to the appropriate context (WP-CLI or the WP Admin)
		 *
		 * @param string $message
		 * @param bool $is_error
		 * @return void
		 */
		public static function notice( $message, $is_error = true ) {
			if ( defined( 'WP_CLI' ) ) {
				$message = strip_tags( $message );
				if ( $is_error ) {
					WP_CLI::warning( $message );
				} else {
					WP_CLI::success( $message );
				}
			} else {
				// Trigger admin notices
				add_action( 'all_admin_notices', array( __CLASS__, 'admin_notices' ) );

				self::$notices[] = compact( 'message', 'is_error' );
			}
		}

		/**
		 * Show an error or other message in the WP Admin
		 *
		 * @action all_admin_notices
		 * @return void
		 */
		public static function admin_notices() {
			foreach ( self::$notices as $notice ) {
				$class_name   = empty( $notice['is_error'] ) ? 'updated' : 'error';
				$html_message = sprintf( '<div class="%s">%s</div>', esc_attr( $class_name ), wpautop( $notice['message'] ) );
				echo wp_kses_post( $html_message );
			}
		}
		/* END PHP VERSION CHECKS */


	}

	/* Initiate Plugin */
	if ( Inbound_Calls_To_Action_Plugin::is_valid_php_version() ) {
		new Inbound_Calls_To_Action_Plugin;

	} else {
		// Show Fail
		Inbound_Calls_To_Action_Plugin::fail_php_version();
	}


	/**
	 * Checks if Calls to Action plugin is active
	 * @package CTA
	 */
	function cta_check_active() {
		return 1;
	}

}
