<?php


/**
 * Class adds lead id cookie when user logs in
 *
 * @package Leads
 * @subpackage WPLogin
 */

if (!class_exists('Inbound_Login')) {

	class Inbound_Login {

		public function __construct() {

			add_action('wp_login', array( __CLASS__ , 'load_tracking_cookies' ) , 10, 2);

		}

		/**
		 *	Loads correct lead UID during a login
		*/
		public static function load_tracking_cookies( $user_login, $user) {

			if (!isset($user->data->user_email)) {
				return;
			}

			global $wp_query;

			/* search leads cpt for record containing email & get UID */
			$results = new WP_Query( array( 'post_type' => 'wp-lead' , 's' => $user->data->user_email ) );

			if (!$results) {
				return;
			}

			if ( $results->have_posts() ) {
				while ( $results->have_posts() ) {

					$uid = get_post_meta( $results->post->ID , 'wp_leads_uid' , true );

					if ($uid) {
						setcookie( 'wp_lead_uid' , $uid , time() + (20 * 365 * 24 * 60 * 60),'/');
					}

					setcookie( 'wp_lead_id' , $results->post->ID , (int) (time() + (20 * 365 * 24 * 60 * 60)),'/');

					/* load lead lists into cookies */
					if (class_exists('Leads_Tracking')) {
						Leads_Tracking::cookie_lead_lists($results->post->ID);
					}
					return;
				}
			}
		}

	}

	new Inbound_Login;
}