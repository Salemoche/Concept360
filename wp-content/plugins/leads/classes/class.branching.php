<?php

/**
 * Experimental Class for assisting with quickly switching to legacy, stable or development versions. Not active at the moment.
 *
 * @package Leads
 * @subpackage BranchControl
 */

class Leads_Branching {

	public function __construct() {
		self::load_hooks();
	}

	/**
	*  Load hooks and filters
	*/
	public static function load_hooks() {

		/* adds branching capabilities to branching API for landing pages */
		add_filter( 'inbound_plugin_branches' , array( __CLASS__ ,  'add_branches' )  , 10 , 1 );

		/* reset active branch during svn update */
		//add_action( 'activate_landing_pages' , array( __CLASS__ , 'reset_branch_status' ) , 10 , 1 );
	}

	/**
	* Add branches to branching api for landing pages plugin
	*/
	public static function add_branches( $branches ) {
		$branches['leads'] = array(
			'git' => 'https://codeload.github.com/inboundnow/leads/zip/master',
			'svn' => 'https://downloads.wordpress.org/plugin/leads.'.WPL_CURRENT_VERSION.'.zip'
		);

		return $branches;
	}


}

new Leads_Branching;
