<?php

/**
 * Class for defining and executing activation routines
 * @package CTA
 * @subpackage Activation
 */

if ( !class_exists('CTA_Activation') ) {

    class CTA_Activation {

        static $version_wp;
        static $version_php;
        static $version_lp;
        static $version_leads;

        /**
         * Initiate class
         */
        public function __construct() {
            self::load_hooks();
        }

        /**
         * load supporting hooks and filters
         */
        public static function load_hooks() {
            if (!is_admin()) {
                return;
            }

            /* Add listener for unset permalinks  */
            add_action('admin_notices', array(__CLASS__, 'permastruct_check'));

            /** add listener for permlaink flush command  */
            add_action('admin_init', array(__CLASS__, 'flush_permalinks'), 11 );

            /* Add listener for uncompleted upgrade routines */
            add_action( 'admin_init', array( 'CTA_Activation', 'run_upgrade_routine_checks'));
        }

        /**
         * activate method
         */
        public static function activate() {
            self::load_static_vars();
            self::run_version_checks();
            self::activate_plugin();
            self::run_updates();
        }

        /**
         * deactivate method
         */
        public static function deactivate() {

        }

        /**
         * load static vars
         */
        public static function load_static_vars() {

            self::$version_wp = '3.6';
            self::$version_php = '5.3';
            self::$version_lp = '1.3.6';
            self::$version_leads = '1.2.1';
        }

        public static function activate_plugin() {
            global $wpdb;

            /* Update DB Markers for Plugin */
            self::store_version_data();

            /* Set Default Settings */
            self::set_default_settings();

            /* Activate shared components */
            self::activate_shared();

            /* Flush permalinks */
            update_option( 'wp_cta_flush_permalinks', true, true);
        }

        /**
         *This method loads public methods from the CTA_Activation_Update_Routines class and automatically runs them if they have not been run yet.
         * We use transients to store the data, which may not be the best way but I don't have to worry about save/update/create option and the auto load process
         */

        public static function run_updates() {


            /* Get list of updaters from CTA_Activation_Update_Routines class */
            $updaters = get_class_methods('CTA_Activation_Update_Routines');

            /* Get transient list of completed update processes */
            $completed = ( get_option( 'cta_completed_upgrade_routines' ) ) ?  get_option( 'cta_completed_upgrade_routines' ) : array();

            /* Get the difference between the two arrays */
            $remaining = array_diff( $updaters, $completed );

            /* Loop through updaters and run updaters that have not been ran */
            foreach ( $remaining as $updater ) {

                CTA_Activation_Update_Routines::$updater();
                $completed[] = $updater;

            }

            /* Update this transient value with list of completed upgrade processes */
            update_option( 'cta_completed_upgrade_routines', $completed );

        }

        /**
         *  This method checks if there are upgrade routines that have not been executed yet and notifies the administror if there are
         *
         */
        public static function run_upgrade_routine_checks() {

            /* Listen for a manual upgrade call */
            if (isset($_GET['plugin_action']) && $_GET['plugin_action'] == 'upgrade_routines' && $_GET['plugin'] =='cta' ) {
                self::run_updates();
                wp_redirect(wp_get_referer());
                exit;
            }

            /* Get list of updaters from CTA_Activation_Update_Routines class */
            $updaters = get_class_methods('CTA_Activation_Update_Routines');

            /* Get transient list of completed update processes */
            $completed = ( get_option( 'cta_completed_upgrade_routines' ) ) ?  get_option( 'cta_completed_upgrade_routines' ) : array();

            /* Get the difference between the two arrays */
            $remaining = array_diff( $updaters, $completed );

            if (count($remaining)>0) {
                add_action( 'admin_notices', array(__CLASS__, 'display_upgrade_routine_notice'));
            }
        }

        public static function display_upgrade_routine_notice() {
            ?>
            <div class="error">
                <p><?php _e( 'Calls to Action plugin requires  a database upgrade:', 'inbound-pro' ); ?> <a href='?plugin=cta&plugin_action=upgrade_routines'><?php _e('Upgrade Database Now', 'inbound-pro' ); ?></a></p>
            </div>
        <?php
        }


        /* Creates transient records of past and current version data */
        public static function store_version_data() {

            $old = get_transient('cta_current_version');
            set_transient( 'cta_previous_version', $old );
            set_transient( 'cta_current_version', WP_CTA_CURRENT_VERSION );

        }

        public static function set_default_settings() {
            add_option( 'wp_cta_global_record_admin_actions', '1', '', 'no' );
            add_option( 'wp_cta_global_wp_cta_slug', 'cta', '', 'no' );
            update_option( 'wp_cta_activate_rewrite_check', '1');
        }

        /**
         *  Tells Inbound Shared to run activation commands
         */
        public static function activate_shared() {
            update_option( 'Inbound_Activate', true );
        }

        /* Aborts activation and details
        * @param ARRAY $args array of message details
        */
        public static function abort_activation( $args ) {
            echo $args['title'] . '<br>';
            echo $args['message'] . '<br>';
            echo 'Details:<br>';
            print_r ($args['details']);
            echo '<br>';
            echo $args['solution'];

            deactivate_plugins( WP_CTA_FILE );
            exit;
        }


        /* Checks if plugin is compatible with current server PHP version */
        public static function run_version_checks() {

            global $wp_version;

            /* Check PHP Version */
            if ( version_compare( phpversion(), self::$version_php, '<' ) ) {
                self::abort_activation(
                    array(
                        'title' => 'Installation aborted',
                        'message' => __('Calls to Action plugin could not be installed', 'inbound-pro'),
                        'details' => array(
                            __( 'Server PHP Version', 'inbound-pro' ) => phpversion(),
                            __( 'Required PHP Version', 'inbound-pro' ) => self::$version_php
                        ),
                        'solution' => sprintf( __( 'Please contact your hosting provider to upgrade PHP to %s or greater', 'inbound-pro'), self::$version_php )
                    )
                );
            }

            /* Check WP Version */
            if ( version_compare( $wp_version, self::$version_wp, '<' ) ) {
                self::abort_activation( array(
                        'title' => 'Installation aborted',
                        'message' => __('Calls to Action plugin could not be installed', 'inbound-pro'),
                        'details' => array(
                            __( 'WordPress Version', 'inbound-pro' ) => $wp_version,
                            __( 'Required WordPress Version', 'inbound-pro' ) => self::$version_wp
                        ),
                        'solution' => sprintf( __( 'Please update landing pages to version %s or greater.', 'inbound-pro'), self::$version_wp )
                    )
                );
            }

            /* Check Landing Pages Version */
            if ( defined('LANDINGPAGES_CURRENT_VERSION') && version_compare( LANDINGPAGES_CURRENT_VERSION, self::$version_lp, '<' ) ) {
                self::abort_activation( array(
                        'title' => 'Installation aborted',
                        'message' => __('Calls to Action plugin could not be installed', 'inbound-pro'),
                        'details' => array(
                            __( 'Calls to Action Version', 'inbound-pro' ) => LANDINGPAGES_CURRENT_VERSION,
                            __( 'Required Calls to Action Version', 'inbound-pro' ) => self::$version_lp
                        ),
                        'solution' => sprintf( __( 'Please update Calls to Action to version %s or greater.', 'inbound-pro'), self::$version_lp )
                    )
                );
            }

            /* Check Leads Version */
            if ( defined('WPL_CURRENT_VERSION') && version_compare( WPL_CURRENT_VERSION, self::$version_leads, '<' ) ) {
                self::abort_activation( array(
                        'title' => 'Installation aborted',
                        'message' => __('Calls to Action plugin could not be installed', 'inbound-pro'),
                        'details' => array(
                            __( 'Leads Version', 'inbound-pro' ) => WPL_CURRENT_VERSION,
                            __( 'Required Leads Version', 'inbound-pro' ) => self::$version_leads
                        ),
                        'solution' => sprintf( __( 'Please update Leads to version %s or greater.', 'inbound-pro'), self::$version_leads )
                    )
                );
            }


        }

        /**
         * flush permalinks
         */
        public static function flush_permalinks() {

            if ( !get_option( 'wp_cta_flush_permalinks' ) ) {
                return;
            }

            flush_rewrite_rules( true );
            delete_option( 'wp_cta_flush_permalinks' );
        }

        /**
         *  check for 'default' permalinks and warn
         */
        public static function permastruct_check() {
            if ( '' == get_option( 'permalink_structure' ) ) {
                ?>
                <div class="error">
                    <p><?php _e( 'Calls To Action plugin requires you to use a non default permlaink structure. Please head into your pemalink settings and choose an option besides \'default\'.', 'cta'); ?></p>
                </div>
            <?php
            }
        }
    }

    /* Add Activation Hook */
    register_activation_hook( WP_CTA_FILE, array( 'CTA_Activation', 'activate'));
    register_deactivation_hook( WP_CTA_FILE, array( 'CTA_Activation', 'deactivate'));

    new CTA_Activation;
}