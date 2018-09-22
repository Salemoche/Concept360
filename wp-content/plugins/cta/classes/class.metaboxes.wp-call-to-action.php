<?php
/**
 * Class that builds the edit page of the wp-call-to-action post type.
 *
 * @package CTA
 * @subpackage EditPage
*/


class CTA_Metaboxes {

	function __construct() {
		self::load_hooks();
	}

	public static function load_hooks() {
		/* Add metaboxes */
		add_action('add_meta_boxes', array(__CLASS__, 'load_metaboxes'));

		/* Load template selector in background */
		add_action('admin_notices', array(__CLASS__, 'load_template_select_container'));

		/* Add ajax listeners for switching templates */
		add_action( 'wp_ajax_wp_cta_get_template_meta', array(__CLASS__, 'switch_templates'));

		/* Add shortcode information */
		add_action( 'edit_form_after_title', array(__CLASS__, 'add_shortcode_data'));

		/* Add variation tabs */
		add_action('edit_form_after_title', array(__CLASS__, 'add_variation_tabs'), 5);

		/* Add hidden inputs */
		add_action( 'edit_form_after_title', array(__CLASS__, 'add_hidden_inputs'));

		/* Change default title placeholder */
		add_filter( 'enter_title_here', array(__CLASS__, 'change_title_placeholder_text'), 10, 2 );

		/* Add variation notes input box */
		add_action( 'edit_form_after_title', array(__CLASS__, 'add_variation_notes'));

		/* Enqueue admin CTA styles and scripts */
		add_action( 'admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_scripts'));


		/* Saves all all incoming POST data as meta pairs */
		add_action( 'save_post', array(__CLASS__, 'save_call_to_action_data'));

		/* Remove WordPress SEO Metabox from wp-call-to-action post_type */
		add_action( 'add_meta_boxes', array(__CLASS__, 'remove_wp_seo'), 100000 );

	}

	/**
	*  		Loads Metaboxes
	*/
	public static function load_metaboxes() {
		global $post, $CTA_Variations;

		$CTAExtensions = CTA_Load_Extensions();

		if ($post->post_type!='wp-call-to-action') {
			return;
		}

		/**
		$meta = get_post_meta($post->ID);
		print_r($meta);
		/**/

		/* Loads Template Options */
		$template_id = $CTA_Variations->get_current_template( $post->ID );

		/* If new variation use historic template id */
		if ( isset($_GET['new-variation'] ) ){
			$variations = $CTA_Variations->get_variations( $post->ID, $vid = null );
			$vid = key($variations);
			$template_id = $CTA_Variations->get_current_template( $post->ID, $vid );
		}

		if ($template_id) {
			$template_name = ucwords(str_replace('-',' ',$template_id));
			add_meta_box(
				"wp_cta_template_select_meta_box", // $id
				"<small>$template_name ".__('Options:', 'inbound-pro' ). "</small>",
				array(__CLASS__, 'show_template_settings' ), // $callback
				'wp-call-to-action', // post-type
				'normal', // $context
				'high',// $priority
				array('template_id'=>$template_id , 'variation_id' => CTA_Variations::get_current_variation_id())
			); //callback args
		}


		/* render templates and extension metaboxes */
		$extension_data = $CTAExtensions->definitions;

		/* Load Extension Metaboxes */
		foreach ($extension_data as $key=>$data) {
			if (isset($data['info']['data_type']) && $data['info']['data_type'] =='metabox'){

				$id = "metabox-".$key;

				$name = (isset($data['info']['label'])) ? $data['info']['label'] : ucwords(str_replace(array('-','ext '),' ',$key). " Extension Options");
				$position = (isset($data['info']['position'])) ? $data['info']['position'] : "normal";
				$priority = (isset($data['info']['priority'])) ? $data['info']['priority'] : "default";

				//echo $key."<br>";
				add_meta_box(
					"wp_cta_{$id}_custom_meta_box", // $id
					"$name",
					array(__CLASS__, 'show_extension_metabox' ), // $callback
					'wp-call-to-action', // post-type
					$position, // $context
					$priority ,// $priority
					array( 'key' => $id )
					); //callback args

			}
		}

		/* Advanced Call to Action Options */
		add_meta_box(
			'wp_cta_tracking_metabox', // $id
			__( 'Advanced Call to Action Options', 'inbound-pro' ), // $title
			array(__CLASS__, 'show_advanced_settings' ), // $callback
			'wp-call-to-action', // $page
			'normal', // $context
			'low'
		); // $priority


		/* Custom CSS */
		add_meta_box(
			'wp_cta_3_custom_css',
			__( 'Custom CSS', 'inbound-pro' ),
			array(__CLASS__, 'show_custom_css' ),
			'wp-call-to-action',
			'normal',
			'low'
		);

		/* Custom JS */
		add_meta_box(
			'wp_cta_3_custom_js',
			__( 'Custom JS', 'inbound-pro' ),
			array(__CLASS__, 'show_custom_js' ),
			'wp-call-to-action',
			'normal',
			'low'
		);

		/* AB Testing Statistics Box */
		add_meta_box(
			'wp_cta_ab_display_stats_metabox',
			__( 'Split Testing', 'inbound-pro' ),
			array(__CLASS__, 'show_stats_metabox'),
			'wp-call-to-action' ,
			'side',
			'high'
		);
        
        /* Inbound Analytics CTA Stats Box */
		if (class_exists('Inbound_CTA_Quick_View')) {
			add_meta_box(
				'wp_cta_display_inbound_analytics_stats_metabox',
				__('Inbound Analytics', 'inbound-pro'),
				array(__CLASS__, 'show_analytics_metabox'),
				'wp-call-to-action',
				'side',
				'high'
			);
		}
	}


	/**
	* Show Template Settings Metabox
	*/
	public static function show_template_settings(	$post, $metabox_args ) {
		global $CTA_Variations, $post;

		$CTAExtensions = CTA_Load_Extensions();
		$extension_data = $CTAExtensions->definitions;

		$template_id = $metabox_args['args']['template_id'];

		if (!isset($extension_data[$template_id])) {
			_e( '<i>It looks like the template you are using is no longer available. Please check your uploads folder for your custom CTA template or visit our marketplace to download free CTA templates recently removed from this plugin.</i>' , 'inbound-pro' );
			return;
		}

		$wp_cta_custom_fields = $extension_data[$template_id]['settings'];

		$template_id = ($template_id) ? $template_id : 'blank-template';

		$template_input_name = 'wp-cta-selected-template-' . $metabox_args['args']['variation_id'];

		// Use nonce for verification
		echo "<input type='hidden' name='wp_cta_wp-cta_custom_fields_nonce' value='".wp_create_nonce('wp-cta-nonce')."' />";

		/* Display customizer launch button */
		if (
			( !isset($_GET['inbound-editor']) || $_GET['inbound-editor'] == 'false')
		&&
			$post->post_status =='publish'
		){

			$post_link = CTA_Variations::get_variation_permalink( $post->ID, $vid = null );

			echo "<a rel='".$post_link."' id='cta-launch-front' class='button-primary ' href='$post_link&inbound-customizer=on'>". __( 'Launch Visual Editor' ,'cta' ) ."</a>";
			echo "&nbsp;&nbsp;";
		}

		echo '<a class="button-primary" id="wp-cta-change-template-button">'. __( 'Choose Another Template', 'inbound-pro' ) .'</a>';
		echo '<input type="hidden" id="" name="' . $template_input_name .'" class="selected-template" value="' .$template_id .'">';

		self::render_settings( $template_id, $wp_cta_custom_fields, $post);

	}

	/**
	* Show Extension Metabox - loads & displays metaboxes built from extension settings
	*/
	public static function show_extension_metabox( $post,$key ) {
		$CTAExtensions = CTA_Load_Extensions();
		$extension_data = $CTAExtensions->definitions;

		$key = $key['args']['key'];

		$wp_cta_custom_fields = $extension_data[$key]['settings'];

		$wp_cta_custom_fields = apply_filters('wp_cta_show_metabox',$wp_cta_custom_fields, $key);

		self::render_settings('cta', $key, $wp_cta_custom_fields, $post);
	}

	/**
	* Show Custom JS Metabox
	*
	*/
	public static function show_custom_js() {
		global $post;

		$custom_js = CTA_Variations::get_variation_custom_js ( $post->ID );
		$custom_js_meta_key = apply_filters('wp_cta_prepare_input_id','wp-cta-custom-js');

		$line_count = substr_count( $custom_js, "\n" );

		$line_count = ($line_count) ? $line_count : 5;

		echo '<input type="hidden" name="wp_cta_custom_js_noncename" id="wp_cta_custom_js_noncename" value="'.wp_create_nonce(basename(__FILE__)).'" />';
		echo '<textarea name="'.$custom_js_meta_key.'" id="wp_cta_custom_js" rows="'.$line_count.'" cols="30" style="width:100%;">'.$custom_js.'</textarea>';
	}


	/*
	* Show Custom CSS Metabox
	*
	*/
	public static function show_custom_css() {
		global $post;

		echo "<em>Custom CSS may be required to customize this call to action. Insert Your CSS Below. Format: #element-id { display:none !important; }</em>";

		$custom_css = CTA_Variations::get_variation_custom_css( $post->ID );
		$custom_css_meta_key = apply_filters('wp_cta_prepare_input_id', 'wp-cta-custom-css');

		$line_count = substr_count( $custom_css, "\n" );
		$line_count = ($line_count) ? $line_count : 5;

		echo '<textarea name="'.$custom_css_meta_key.'" id="wp-cta-custom-css" rows="'. $line_count .'" cols="30" style="width:100%;">'.$custom_css .'</textarea>';
	}

	/*
	* Show Variation Statistics Metabox
	*
	*/
	public static function show_stats_metabox() {

		global $post, $CTA_Variations;

		$variations = $CTA_Variations->get_variations($post->ID);
		$next_available_variation_id = $CTA_Variations->get_next_available_variation_id( $post->ID );

		echo '<div id="cta-stat-box">';

		foreach ($variations as $vid=>$variation) {

			/* Get variation status */
			$status = $CTA_Variations->get_variation_status($post->ID, $vid);

			$variation_notes = $CTA_Variations->get_variation_notes($post->ID, $vid);
			$permalink = $CTA_Variations->get_variation_permalink($post->ID, $vid);

			$impressions = get_post_meta($post->ID,'wp-cta-ab-variation-impressions-'.$vid, true); /* get impressions */
			$conversions = get_post_meta($post->ID,'wp-cta-ab-variation-conversions-'.$vid, true);
			$conversions = ($conversions) ? $conversions : 0;

			/* get conversion rate */
			$conversion_rate = ($impressions != 0) ?  $conversions / $impressions : 0;
			$conversion_rate = round($conversion_rate,2) * 100;

			?>

				<div id="wp-cta-variation-<?php echo $CTA_Variations->vid_to_letter($post->ID, $vid); ?>" class="cta-variation-row variation-<?php echo $status; ?>">


					<!-- New UI START -->
					<div class='cta-varation-header'>
					<span class='cta-variation-name'>Variation
					<span class='cta-stat-letter'>
					<?php echo $CTA_Variations->vid_to_letter($post->ID, $vid); ?>
					</span>
						<?php
						if (isset($variation_status) && $variation_status != 1) {
							switch ($variation_status) {
								case 'paused':
									?>
									<span class='is-paused'>(<?php _e('Paused', 'inbound-pro') ?>)</span>
									<?php
									break;
								default:
									?>
									<span class='is-<?php echo $variation_status; ?>'>(<?php echo $variation_status; ?>
										)</span>
									<?php
									break;
							}

						} ?>



					</span>

					<span class="settings_icon"> </span>
					<span class="settings_wrapper">
					<span class="settings_wrapper_heading">Variation Settings</span>
						<ul class="settings_list_li">
							<li class="settings_edit">
								<span class='cta-stat-menu-edit'>
									<a title="Edit this variation" href='?post=<?php echo $post->ID; ?>&action=edit&vid=<?php echo $vid; ?>'><?php _e('Edit', 'inbound-pro'); ?></a>
								</span>
							</li>
							<li class="settings_preview">
								<span class='cta-stat-menu-preview'>
									<a title="Preview this variation" class='thickbox' href='<?php echo $permalink; ?>&inbound_popup_preview=on&post_id=<?php echo $post->ID; ?>&TB_iframe=true&width=1503&height=467' target='_blank'><?php _e('Preview', 'inbound-pro'); ?></a>
								</span>
							</li>
							<li class="settings_clone">
								<span class='cta-stat-menu-clone'>
									<a title="Clone this variation" href='?post=<?php echo $post->ID; ?>&action=edit&new-variation=1&clone=<?php echo $vid; ?>&ab-action=clone&wp-cta-variation-id=<?php echo $next_available_variation_id; ?>'><?php _e('Clone', 'inbound-pro'); ?></a>
								</span>
							</li>
							<li class="settings_delete">
								<span class='cta-stat-control-delete'>
									<a title="Delete this variation" href='?post=<?php echo $post->ID; ?>&action=edit&vid=<?php echo $vid; ?>&ab-action=delete-variation'><?php _e('Delete', 'inbound-pro'); ?></a>
								</span>
							</li>
							<li class="settings_clearstat">
								<!-- CLEAR STATS START -->
								<span class="wp-cta-delete-var-stats" data-letter='<?php echo $CTA_Variations->vid_to_letter($post->ID, $vid); ?>' data-vid='<?php echo $vid; ?>' rel='<?php echo $post->ID; ?>' title="Delete this variations stats">Clear Stats</span>
								<!-- CLEAR STAT END --></li>
						</ul>
					</span>


						<!-- PAUSE START -->
						<span class='cta-stat-control-pause'><a title="<?php _e('Pause this variation' , 'inbound-pro' ); ?>" href='?post=<?php echo $post->ID; ?>&action=edit&vid=<?php echo $vid; ?>&ab-action=pause-variation'> </a></span>
						<!-- PAUSE END -->

						<!-- PLAY START -->
						<span class='cta-stat-seperator pause-sep'>|</span>
						<span class='cta-stat-control-play'><a title="<?php _e('Turn this variation on' , 'inbound-pro' ); ?>" href='?post=<?php echo $post->ID; ?>&action=edit&vid=<?php echo $vid; ?>&ab-action=play-variation'> </a></span>
						<span class='cta-stat-control-behavioral' title="<?php  _e('This variation is behavioral.','inbound-pro'); ?>"><i class ='fa fa-lock' aria-hidden='true'></i> </span>
						<!-- PLAY END -->


					</div>
					<!-- New Ui end -->

					<div class="cta-variation-notes">
						<?php echo $variation_notes; ?>
					</div>
					<div class="cta-stat-row">
						<div class='cta-stat-stats' colspan='2'>
							<div class='cta-stat-container-impressions cta-number-box'>
								<span class="cta-stat-id"><?php _e('Views', 'inbound-pro'); ?></span>

								<span class='cta-stat-span-impressions'><?php echo $impressions; ?></span>
							</div>
							<div class='cta-stat-container-conversions cta-number-box'>
								<span class="cta-stat-id"><?php _e('Conversions', 'inbound-pro'); ?></span>
								<span class='cta-stat-span-conversions'><?php echo $conversions; ?></span>
								</span>
							</div>
							<div class='cta-stat-container-conversion_rate cta-number-box'>
								<span class="cta-stat-id cta-rate"><?php _e('Conversion Rate', 'inbound-pro'); ?></span>
								<span class='cta-stat-span-conversion_rate'><?php echo $conversion_rate; ?></span>

							</div>

						</div>
					</div>
					<div class="cta-stat-row">

						<div class='cta-stat-menu-container'>

							<?php do_action('wp_cta_ab_testing_stats_menu_post'); ?>

						</div>
					</div>
				</div>
				<?php
		}
		?>
	</div>
	<?php

	}

    /**
    * Displays a metabox of the total clicks a CTA has,
    * the top variations by clicks,
    * and the top posts by clicks of the CTA
    */
    public static function show_analytics_metabox(){

		/* sets the default quick view template */
		$template_class_name = apply_filters('inbound-ananlytics/cta/quick-view', 'Inbound_CTA_Quick_View');

		$template_class = new $template_class_name;
		$template_class->load_template(array());
    }

	/**
	* Display CTA Settings for templates AND extensions
	*/
	public static function render_settings( $settings_key, $custom_fields, $post ) {

		global $CTA_Variations;

		$CTAExtensions = CTA_Load_Extensions();
		$extension_data = $CTAExtensions->definitions;

		// Use nonce for verification
		echo "<input type='hidden' name='wp_cta_{$settings_key}_custom_fields_nonce' value='".wp_create_nonce('wp-cta-nonce')."' />";

		// Begin the field table and loop
		echo '<div class="form-table" id="inbound-meta">';

		foreach ($custom_fields as $field) {

			$field_id = apply_filters('wp_cta_prepare_input_id', $settings_key . "-" .$field['id'] );

			$label_class = $field['id'] . "-label";
			$type_class = " inbound-" . $field['type'];
			$type_class_row = " inbound-" . $field['type'] . "-row";
			$type_class_option = " inbound-" . $field['type'] . "-option";
			$option_class = (isset($field['class'])) ? $field['class'] : '';

			/* if setting does has a stored value then use default value */
			if ( metadata_exists( 'post', $post->ID, $field_id ) ) {
				$meta = get_post_meta($post->ID, $field_id, true);
			} else {
			/* else set value to stored value */
				$meta = $field['default'];
			}

			// Remove prefixes on global => true template options
			if (isset($field['global']) && $field['global'] === true) {
				$field_id = apply_filters('wp_cta_prepare_input_id', $field['id'] );
				$meta = get_post_meta($post->ID, $field_id, true);
			}

			/* Set setting value to cloned value if clone command is enabled */
			if ( isset($_GET['clone']) ) {

				if (isset($field['global']) && $field['global'] === true) {
					$meta = get_post_meta($post->ID, $field['id']. '-'. intval($_GET['clone']) , true);
				} else {
					$meta = get_post_meta($post->ID,  $settings_key . '-' . $field['id'] . '-'. intval($_GET['clone']) , true);
				}
			}

			// begin a table row with
			echo '<div class="'.$field['id'].$type_class_row.' div-'.$option_class.' wp-call-to-action-option-row inbound-meta-box-row">';
					if ($field['type'] != "description-block" && $field['type'] != "custom-css" ) {
					echo '<div id="inbound-'.$field_id.'" data-actual="'.$field_id.'" class="inbound-meta-box-label wp-call-to-action-table-header '.$label_class.$type_class.'"><label for="'.$field_id.'">'.$field['label'].'</label></div>';
					}

					echo '<div class="wp-call-to-action-option-td inbound-meta-box-option '.$type_class_option.'" data-field-type="'.$field['type'].'">';
					switch($field['type']) {
						// default content for the_content
						case 'default-content':
							echo '<span id="overwrite-content" class="button-secondary">Insert Default Content into main Content area</span><div style="display:none;"><textarea name="'.$field_id.'" id="'.$field_id.'" class="default-content" cols="106" rows="6" style="width: 75%; display:hidden;">'.$meta.'</textarea></div>';
							break;
						case 'description-block':
							echo '<div id="'.$field_id.'" class="description-block">'.$field['description'].'</div>';
							break;
						case 'custom-css':
							echo '<style type="text/css">'.$field['default'].'</style>';
							break;
						// text
						case 'colorpicker':
							if (!$meta) {
								$meta = $field['default'];
							}
							$var_id = (isset($_GET['new_meta_key'])) ? "-" . intval($_GET['new_meta_key']) : '';
							echo '<input type="text" class="jpicker" style="background-color:#'.$meta.'" name="'.$field_id.'" id="'.$field_id.'" value="'.$meta.'" size="5" /><span class="button-primary new-save-wp-cta" data-field-type="text" id="'.$field_id.$var_id.'" style="margin-left:10px; display:none;">Update</span>
									<div class="wp_cta_tooltip tool_color" title="'.$field['description'].'"></div>';
							break;
						case 'width-height':
							echo '<input type="text" class="'.$option_class.'" name="'.$field_id.'" id="'.$field_id.'" value="'.$meta.'" size="30" />
									<div class="wp_cta_tooltip" title="'.$field['description'].'"></div>';
							break;
						case 'text':
							echo '<input type="text" class="'.$option_class.'" name="'.$field_id.'" id="'.$field_id.'" value="'.$meta.'" size="30" />
									<div class="wp_cta_tooltip" title="'.$field['description'].'"></div>';
							break;
						case 'number':

							echo '<input type="number" class="'.$option_class.'" name="'.$field_id.'" id="'.$field_id.'" value="'.$meta.'" size="30" />
									<div class="wp_cta_tooltip" title="'.$field['description'].'"></div>';

							break;
						// textarea
						case 'datepicker':
							echo '<input id="datepicker" class="'.$option_class.' datepicker-'.$field['id'].'" type="text" name="' . $field_id . '" id="' . $field_id . '" value="' . $meta . '" size="8" />
									<div class="wp_cta_tooltip tool_date" title="' . $field['description'] . '"></div><p class="description">' . $field['description'] . '</p>';
							?>
							<script>
								jQuery(document).ready(function() {
									jQuery('input.datepicker-<?php echo $field['id']; ?>').Zebra_DatePicker();
								});
							</script>
							<?php
							break;
						// textarea
						case 'textarea':
							echo '<textarea name="'.$field_id.'" id="'.$field_id.'" cols="106" rows="6" style="width: 75%;">'.$meta.'</textarea>
									<div class="wp_cta_tooltip tool_textarea" title="'.$field['description'].'"></div>';
							break;
						// wysiwyg
						case 'wysiwyg':
							echo "<div class='iframe-options iframe-options-".$field_id."' id='".$field['id']."'>";
							wp_editor( $meta, $field_id, $settings = array( 'editor_class' => $field['id'] ));
							echo	'<p class="description">'.$field['description'].'</p></div>';
							break;
						// media
						case 'media':
							//echo 1; exit;
							echo '<label for="upload_image" data-field-type="text">';
							echo '<input name="'.$field_id.'"	id="'.$field_id.'" type="text" size="36" name="upload_image" value="'.$meta.'" />';
							echo '<input class="upload_image_button" id="uploader_'.$field_id.'" type="button" value="Upload Image" data-field-id="'.$field_id.'" />';
							echo '<p class="description">'.$field['description'].'</p>';
							break;
						// checkbox
						case 'checkbox':
							$i = 1;
							echo "<table class='wp_cta_check_box_table'>";
							if (!isset($meta)){$meta=array();}
							elseif (!is_array($meta)){
								$meta = array($meta);
							}
							foreach ($field['options'] as $value=>$label) {
								if ($i==5||$i==1) {
									echo "<tr>";
									$i=1;
								}
									echo '<td data-field-type="checkbox"><input type="checkbox" name="'.$field_id.'[]" id="'.$field_id.'" value="'.$value.'" ',in_array($value,$meta) ? ' checked="checked"' : '','/>';
									echo '<label for="'.$value.'">&nbsp;&nbsp;'.$label.'</label></td>';
								if ($i==4) {
									echo "</tr>";
								}
								$i++;
							}
							echo "</table>";
							echo '<div class="wp_cta_tooltip tool_checkbox" title="'.$field['description'].'"></div>';
						break;
						// radio
						case 'radio':
							foreach ($field['options'] as $value=>$label) {
								echo '<input type="radio" name="'.$field_id.'" id="'.$field_id.'" value="'.$value.'" ',$meta==$value ? ' checked="checked"' : '','/>';
								echo '<label for="'.$value.'">&nbsp;&nbsp;'.$label.'</label> &nbsp;&nbsp;&nbsp;&nbsp;';
							}
							echo '<div class="wp_cta_tooltip" title="'.$field['description'].'"></div>';
						break;
						// select
						case 'dropdown':
							echo '<select name="'.$field_id.'" id="'.$field_id.'" class="'.$field['id'].'">';
							foreach ($field['options'] as $value=>$label) {
								echo '<option', $meta == $value ? ' selected="selected"' : '', ' value="'.$value.'">'.$label.'</option>';
							}
							echo '</select><div class="wp_cta_tooltip" title="'.$field['description'].'"></div>';
						break;
						case 'image-select':
							echo '<select name="'.$field_id.'" id="'.$field_id.'" class="image-picker">';
							foreach ($field['options'] as $value=>$label) {
								echo '<option', $meta == $value ? ' selected="selected"' : '', ' value="'.$value.'" data-img-src="'.$extension_data[$settings_key]['info']['urlpath'].'assets/img/'.$value.'.'.$field['image_type'].'" >'.$label.'</option>';
							}
							echo '</select><div class="wp-cta-image-container" style="display:inline;min-height:200px;margin-top:10px;"></div><div class="wp_cta_tooltip" title="'.$field['description'].'"></div>';
						break;

					} //end switch
			echo '</div></div>';
		} // end foreach
		echo '</div>'; // end table
		//exit;
	}


	/**
	* Loads and hide the template selection grid
	*
	*/
	public static function load_template_select_container() {
		global $wp_cta_data, $post, $current_url, $CTA_Variations;

		$CTAExtensions = CTA_Load_Extensions();

		if (isset($post)&&$post->post_type!='wp-call-to-action'||!isset($post)){
			return false;
		}

		$toggle = (!strstr( $current_url, 'post-new.php')) ? "display:none" : "";

		$extension_data = $CTAExtensions->definitions;
		unset($extension_data['wp-cta']);
		unset($extension_data['wp-cta-controller']);

		$uploads = wp_upload_dir();
		$uploads_path = $uploads['basedir'];
		$extended_path = $uploads_path.'/wp-call-to-actions/templates/';

		if ( isset($_GET['new-variation'] ) ){
			$variations = $CTA_Variations->get_variations( $post->ID, $vid = null );
			$vid = key($variations);
			$template = $CTA_Variations->get_current_template( $post->ID, $vid );
		} else {
			$template = $CTA_Variations->get_current_template( $post->ID );
		}

		echo "<div class='wp-cta-template-selector-container' style='{$toggle}'>";
		echo "<div class='wp-cta-selection-heading'>";
		echo "<h1>". __( 'Select Your Call to Action Template!', 'inbound-pro' ) ."</h1>";
		echo '<a class="button-secondary" style="display:none;" id="wp-cta-cancel-selection">Cancel Template Change</a>';
		echo "</div>";
			echo '<ul id="template-filter" >';
				echo '<li class="button primary"><a href="#" data-filter=".template-item-boxes">All</a></li>';
				$categories = array();
				foreach ( $CTAExtensions->template_categories as $cat) {

					$category_slug = str_replace(' ','-',$cat['value']);
					$category_slug = strtolower($category_slug);
					$cat['value'] = ucwords($cat['value']);
					if (!in_array($cat['value'],$categories)) {
						echo '<li class="button"><a href="#" data-filter=".'.$category_slug.'">'.$cat['value'].'</a></li>';
						$categories[] = $cat['value'];
					}

				}
			echo "</ul>";
			echo '<div id="templates-container" >';

			foreach ($extension_data as $this_template=>$data) {

				if (isset($data['info']['data_type'])&&$data['info']['data_type']!='template'){
					continue;
				}

				$cats = explode( ',', $data['info']['category'] );
				foreach ($cats as $key => $cat) {
					$cat = trim($cat);
					$cat = str_replace(' ', '-', $cat);
					$cats[$key] = trim(strtolower($cat));
				}

				$cat_slug = implode(' ', $cats);

				/* Get Thumbnail */
				$thumbnail = CTA_Variations::get_template_thumbnail( $this_template );

				?>
				<div id='template-item' class="<?php echo $cat_slug; ?> template-item-boxes">
					<div id="template-box">
						<div class="wp_cta_tooltip_templates" title="<?php echo $data['info']['description']; ?>"></div>
					<a class='wp_cta_select_template' href='#' label='<?php echo $data['info']['label']; ?>' id='<?php echo $this_template; ?>'>
						<img src="<?php echo $thumbnail; ?>" class='template-thumbnail' alt="<?php echo $data['info']['label']; ?>" id='wp_cta_<?php echo $this_template; ?>'>
					</a>

						<div id="template-title" style="text-align: center;
	font-size: 14px; padding-top: 10px;"><?php echo $data['info']['label']; ?></div>
						<!-- |<a href='#' label='<?php echo $data['info']['label']; ?>' id='<?php echo $this_template; ?>' class='wp_cta_select_template'>Select</a>
						<a class='thickbox <?php echo $cat_slug;?>' href='<?php echo $data['info']['demo'];?>' id='wp_cta_preview_this_template'>Preview</a> -->
					</div>
				</div>
				<?php
			}
		echo '</div>';
		echo "<div class='clear'></div>";
		echo "</div>";
	}

	/**
	*	Listenens for template id and returns template settings
	*
	*
	*/
	public static function switch_templates( ) {

		$current_template = sanitize_text_field($_POST['selected_template']);
		$post_id = intval($_POST['post_id']);
		$vid = intval($_POST['variation_id']);
		$post = get_post($post_id);

		$key['args']['template_id'] = $current_template;
		$key['args']['variation_id'] = $vid;

		CTA_Metaboxes::show_template_settings($post,$key);
		die();
	}


	/**
	* Adds variation navigation tabs to call to action edit screen
	*
	*/
	public static function add_variation_tabs() {
		global $post, $CTA_Variations;

		if ( !$post || $post->post_type != 'wp-call-to-action' ) {
			return;
		}

		$next_available_variation_id = $CTA_Variations->get_next_available_variation_id( $post->ID );
		$current_variation_id = $CTA_Variations->get_current_variation_id();

		if ( isset($_GET['new-variation']) || isset($_GET['clone']) ) {
			$current_variation_id = $next_available_variation_id;
		}

		$variations = $CTA_Variations->get_variations($post->ID);

		echo '<h2 class="nav-tab-wrapper a_b_tabs">';

		$var_id_marker = 1;

		foreach ($variations as $vid => $variation) {

			$permalink = $CTA_Variations->get_variation_permalink( $post->ID, $vid );
			$letter = $CTA_Variations->vid_to_letter( $post->ID, $vid );

			//alert (variation.new_variation);
			if ($current_variation_id==$vid&&!isset($_GET['new-variation']) || $current_variation_id==$vid && isset($_GET['clone'])) {
				$cur_class = 'active';
			} else {
				$cur_class = 'inactive';
			}
			echo '<a href="?post='.$post->ID.'&wp-cta-variation-id='.$vid.'&action=edit" class="wp-cta-nav-tab nav-tab nav-tab-special-'.$cur_class.'" id="tab-'.$vid.'" data-permalink="'.$permalink.'" target="_parent">'.$letter.'</a>';

		}

		if (!isset($_GET['new-variation'])) {

			echo '<a href="?post='.$post->ID.'&wp-cta-variation-id='.$next_available_variation_id.'&action=edit&new-variation=1" class="wp-cta-nav-tab nav-tab nav-tab-special-inactive nav-tab-add-new-variation" id="tabs-add-variation">Add New Variation <i data-code="f132" style="vertical-align:bottom;" class="dashicons dashicons-plus"></i></a>';

		} else {

			$letter = $CTA_Variations->vid_to_letter( $post->ID, $next_available_variation_id );
			echo '<a href="?post='.$post->ID.'&wp-cta-variation-id='.$next_available_variation_id.'&action=edit" class="wp-cta-nav-tab nav-tab nav-tab-special-active" id="tabs-add-variation">'.$letter.'</a>';

		}

		echo '</h2>';

	}


	/**
	* Renders shortcode data for user to copy for user
	*/
	public static function add_shortcode_data() {
		global $post;

		if ( !$post || $post->post_type != 'wp-call-to-action' ) {
			return;
		}

		$vid = CTA_Variations::get_current_variation_id();

		echo '<span id="cta_shortcode_form" style="display:none; font-size: 13px;margin-left: 15px;">
			'. __('Variation Shortcode', 'inbound-pro' ) .': <input type="text" style="width: 200px;" class="regular-text code short-shortcode-input" readonly="readonly" id="shortcode" name="shortcode" value=\'[cta id="'.$post->ID.'" vid="'.$vid.'"]\'>
			<div class="wp_cta_tooltip" style="margin-left: 0px;" title="'. __( 'You can copy and paste this shortcode into any page or post to render this call to action. You can also insert CTAs from the WordPress editor on any given page. To enable variation rotation remove the vid= attribute.', 'inbound-pro' ) .'"></div></span>';

	}

	/**
	* Renders shortcode data for user to copy for user
	*/
	public static function add_hidden_inputs() {
		global $post, $CTA_Variations;

		if ( !$post || $post->post_type != 'wp-call-to-action' ) {
			return;
		}

		/* Moved to customizer
		Add hidden param for visual editor
		if(isset($_REQUEST['inbound-editor']) && $_REQUEST['inbound-editor'] == 'true') {
			echo '<input type="hidden" name="frontend" id="frontend-on" value="true" />';
		}*/

		/* Get current variation id */
		$vid = CTA_Variations::get_current_variation_id();

		/* Add variation status */
		$variations_status = $CTA_Variations->get_variation_status( $post->ID, $vid );
		echo '<input type="hidden" name="wp-cta-variation-status['.$vid.']" value = "'.$variations_status .'">';

		/* Add variation id */
		echo '<input type="hidden" name="wp-cta-variation-id" id="open_variation" value = "'.$vid .'">';

		/* Add call to action permalink */
	}

	/**
	* Changes the default placeholder text of wp_title when cta is being created. With CTAs, wp_title is a descriptive title.
	*
	*/
	public static function change_title_placeholder_text( $text, $post ) {
		if ($post->post_type!='wp-call-to-action') {
			return $text;
		}
		return __( 'Enter Call to Action Description', 'inbound-pro' );
	}

	/**
	* Adds variation notes below title
	*/
	public static function add_variation_notes() {
		global $post, $CTA_Variations;

		if ($post->post_type!='wp-call-to-action') {
			return;
		}

		$variation_notes = $CTA_Variations->get_variation_notes ( $post->ID );

		echo "<div id='wp-cta-notes-area' data-field-type='text'>";
		$id = apply_filters( 'wp_cta_prepare_input_id', 'wp-cta-variation-notes' );
		echo "<span id='add-wp-cta-notes'>". __( 'Notes:', 'inbound-pro' ) ."</span><input placeholder='". __( 'Add Notes to your variation. Example: This version is testing a green submit button', 'inbound-pro' ) ."' type='text' class='wp-cta-notes' name='{$id}' id='{$id}' value='{$variation_notes}' size='30'>";
		echo '</div>';

	}

	/**
	* Enqueues admin CTA styles and scripts
	*/
	public static function enqueue_admin_scripts() {
		$screen = get_current_screen();

		if (!isset($screen) || $screen->id != 'wp-call-to-action' || $screen->base !='post'){
			return;
		}
        
		wp_enqueue_style('wp-cta-ab-testing-admin-css', WP_CTA_URLPATH . 'assets/css/admin-ab-testing.css');
        
        /* enqueue the CTA Inbound Analytics metabox styles*/
		wp_enqueue_style('Zebra_Datepicker', WP_CTA_URLPATH . 'assets/lib/Zebra_Datepicker/css/default/zebra_datepicker.min.css');
		wp_enqueue_script('Zebra_Datepicker', WP_CTA_URLPATH . 'assets/lib/Zebra_Datepicker/zebra_datepicker.min.js');

		//wp_enqueue_style('wp-cta-inbound-analytics-admin-css', WP_CTA_URLPATH . 'assets/css/admin-cta-inbound-analytics-metabox.css');
	}

	/**
	* Updates call to action variation data on post save
	*
	* @param INT $cta_id of call to action id
	*
	*/
	public static function save_call_to_action_data( $cta_id ) {
		global $post;
		unset($_POST['post_content']);

		if (wp_is_post_revision($cta_id)) {
			return;
		}

		if (!isset($_POST['post_type']) || $_POST['post_type'] != 'wp-call-to-action') {
			return;
		}

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
			return;
		}

		/* Set the call to action variation into a session variable */
		$_SESSION[ $post->ID . '-variation-id'] = (isset($_POST[ 'wp-cta-variation-id'])) ? $_POST[ 'wp-cta-variation-id'] : '0';


		foreach ($_POST as $key => $value) {

			update_post_meta( $cta_id, $key, $value );

		}

	}


	/**
	 *
	 */
	public static function show_advanced_settings () {
		global $post;

		$CTAExtensions = CTA_Load_Extensions();
		$extension_data = $CTAExtensions->definitions;

		// Use nonce for verification
		//echo '<input type="hidden" name="custom_wp_cta_metaboxes_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';
		wp_nonce_field('save-custom-wp-cta-boxes','custom_wp_cta_metaboxes_nonce');
		// Begin the field table and loop
		echo '<div class="form-table">';
		echo '<div class="cta-description-box"><span class="calc button-secondary">'. __( 'Calculate height/width', 'inbound-pro' ) .'</span></div>';


		foreach ($extension_data['wp-cta']['settings'] as $key=>$field) {

			if (isset($field['region']) && $field['region'] =='advanced') {

				if ( !isset($field['global']) || !$field['global'] ) {
					$field['id'] = apply_filters( 'wp_cta_prepare_input_id', $field['id'] );
				}

				$field['id'] = "wp-cta-".$field['id'];

				CTA_Metaboxes_Global::render_setting($field);
			}
		}

		do_action( "wordpress_cta_add_meta" ); // Action for adding extra meta boxes/options

		echo '</div>'; // end table
	}

	/**
	*  Removes WordPress SEO metabox from wp-call-to-action post type.
	*  Currently disabled. This throws admin js error.
	*
	*/
	public static function remove_wp_seo() {
		//remove_meta_box( 'wpseo_meta', 'wp-call-to-action', 'normal' ); // change custom-post-type into the name of your custom post type
	}

}

new CTA_Metaboxes;
