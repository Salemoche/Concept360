<?php
//main output function
function ampforwp_framework_get_search_form() {
		global $redux_builder_amp;
		$action_url = '';
		$amp_query_variable = '';
		$amp_query_variable_val = '';
		$label = ampforwp_translation(isset($redux_builder_amp['ampforwp-search-label']) && $redux_builder_amp['ampforwp-search-label'], 'Type your search query and hit enter');
		$action_url = esc_url( get_bloginfo('url') );
		$action_url = preg_replace('#^http?:#', '', $action_url);
		$placeholder = ampforwp_translation($redux_builder_amp['ampforwp-search-placeholder'], 'Type Here' );
		if ( isset($redux_builder_amp['ampforwp-amp-takeover']) && !$redux_builder_amp['ampforwp-amp-takeover'] ) {
			$amp_query_variable = 'amp';
			$amp_query_variable_val = '1';
		}
	  $form = '<form role="search" method="get" id="amp-search" class="amp-search" target="_top" action="' . $action_url  .'">
				<div class="amp-search-wrapper">
					<label aria-label="Type your query" class="screen-reader-text" for="s">' . $label . '</label>
					<input type="text" placeholder="AMP" value="'.$amp_query_variable_val.'" name="'.$amp_query_variable.'" class="hidden"/>
					<input type="text" placeholder="'.$placeholder.'" value="' . get_search_query() . '" name="s" id="s" />
					<label aria-label="Submit amp search" for="amp-search-submit" >
						<input type="submit" class="icon-search" id="amp-search-submit" value="'. esc_attr_x( 'Search', 'submit button' ) .'" />
					</label>
					<div class="overlay-search">
					</div>
				</div>
				</form>';
	    echo $form;		
	    
}
ampforwp_add_scripts();
function ampforwp_add_scripts(){
	global $scriptComponent;
	if ( empty( $scriptComponent['amp-form'] ) ) {
			$scriptComponent['amp-form'] = 'https://cdn.ampproject.org/v0/amp-form-0.1.js';
		}
}