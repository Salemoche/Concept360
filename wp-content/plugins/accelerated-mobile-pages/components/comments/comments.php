<?php
function ampforwp_framework_get_comments(){
	global $redux_builder_amp;
	$display_comments_on = "";
	$display_comments_on = ampforwp_get_comments_status();
	if ( $display_comments_on ) {
		if ( $redux_builder_amp['ampforwp-facebook-comments-support']  ) { 
		 	echo ampforwp_framework_get_facebook_comments(); 
		}

		if ( $redux_builder_amp['ampforwp-disqus-comments-support'] )  {
			 ampforwp_framework_get_disqus_comments();
		}
		if ( $redux_builder_amp['ampforwp-vuukle-comments-support'] )  {
			 ampforwp_framework_get_vuukle_comments();
		}
		if ( $redux_builder_amp['ampforwp-spotim-comments-support']  )  {
			 ampforwp_framework_get_spotim_comments();
		}
	  
		if ( isset($redux_builder_amp['wordpress-comments-support']) && true == $redux_builder_amp['wordpress-comments-support'] ) {
			do_action('ampforwp_before_comment_hook'); ?>
				<div class="amp-comments">
					<?php
					// Gather comments for a specific page/post
					$postID = $comments = $max_page =  "";
					$postID = get_the_ID();
					$comments = get_comments(array(
							'post_id' => $postID,
							'status' => 'approve' //Change this to the type of comments to be displayed
					));
					
					if ( $comments ) { ?>
						<div id="comments" class="amp-comments-wrapper">
				            <h3><span><?php echo ampforwp_translation($redux_builder_amp['amp-translator-view-comments-text'], 'View Comments' )?></span></h3>
				            <ul><?php
								// Display the list of comments
								function ampforwp_custom_translated_comment($comment, $args, $depth){
									$GLOBALS['comment'] = $comment;
									global $redux_builder_amp;
									$comment_author_img_url = "";
									$comment_author_img_url = ampforwp_get_comments_gravatar( $comment ); 
									
									?>
									<li id="li-comment-<?php comment_ID() ?>"
									<?php comment_class(); ?> >
										<article id="comment-<?php comment_ID(); ?>" class="comment-body">
											<footer class="comment-meta">
											<?php if($comment_author_img_url){ ?>
			         							<amp-img src="<?php echo esc_url($comment_author_img_url); ?>" width="40" height="40" layout="fixed" class="comment-author-img"></amp-img>
			         						<?php } ?>
												<div class="comment-author vcard">
													 <?php
													 printf(__('<b class="fn">%s</b> <span class="says">'.ampforwp_translation($redux_builder_amp['amp-translator-says-text'],'says').':</span>'), get_comment_author_link()) ?>
												</div>
												<div class="comment-metadata">
													<a href="<?php echo htmlspecialchars( trailingslashit( get_comment_link( $comment->comment_ID ) ) ) ?>">
														<?php printf( ampforwp_translation( ('%1$s '. ampforwp_translation($redux_builder_amp['amp-translator-at-text'],'at').' %2$s'), '%1$s at %2$s') , get_comment_date(),  get_comment_time())?>
													</a>
													<?php edit_comment_link(  ampforwp_translation( $redux_builder_amp['amp-translator-Edit-text'], 'Edit' )  ) ?>
												</div>
											</footer>
											<div class="comment-content">
						                        <?php
						                          	$comment_content = get_comment_text();
						                        	$comment_content = wpautop( $comment_content );
						                          $sanitizer = new AMPFORWP_Content( $comment_content, array(), apply_filters( 'ampforwp_content_sanitizers', array( 
						                          		'AMP_Img_Sanitizer' => array(),
						                          		'AMP_Video_Sanitizer' => array(),
						                          		'AMP_Style_Sanitizer' => array() ) ) );
						                         $sanitized_comment_content =  $sanitizer->get_amp_content();
						                          echo make_clickable( $sanitized_comment_content );   ?>
											</div>
										<?php do_action('ampforwp_reply_comment_form', $comment, $args, $depth); ?>
										</article>
									</li>
									<?php 
								}
								wp_list_comments( array(
			                        //Allow comment pagination
			                        'per_page' 			=> AMPFORWP_COMMENTS_PER_PAGE , 
			                        'style' 			=> 'li',
			                        'type'				=> 'comment',
			                        'max_depth'   		=> 5,
			                        'avatar_size'		=> 0,
			                        'callback'			=> 'ampforwp_custom_translated_comment',
			                        'reverse_top_level' => true //Show the latest comments at the top of the list
								), $comments);  ?>
						    </ul> <?php 
							    $max_page = get_comment_pages_count($comments, AMPFORWP_COMMENTS_PER_PAGE);
							    $args = array(
									'base' 			=> add_query_arg( array('cpage' => '%#%', 'amp' => '1'), get_permalink() ),
									'format' 		=> '',
									'total' 		=> $max_page,
									'echo' 			=> false,
									'add_fragment' 	=> '#comments',		
								);
						    if ( paginate_comments_links($args) ) { ?>
								<div class="cmts-wrap">
					     			<?php echo paginate_comments_links( $args ); ?>
					     		</div>
				     		<?php } ?>
						</div> <!-- .amp-comments-wrapper -->
						<?php // if amp-comments extension is enabled then hide this button
					} // if ( $comments )
					if ( ! defined( 'AMP_COMMENTS_VERSION' ) ) { ?>
						<div class="amp-comment-button">
							<?php if ( comments_open() ) { ?>
						    	<a href="<?php echo ampforwp_comment_button_url(); ?>" rel="nofollow"><?php echo ampforwp_translation( $redux_builder_amp['amp-translator-leave-a-comment-text'], 'Leave a Comment'  ); ?></a> <?php
							} else {
								echo "<p class='nocomments'>". ampforwp_translation( $redux_builder_amp['amp-translator-comments-closed'], 'Comments are closed'  ) ." </p>";
							}?>
						</div> <?php 
					}?>
				</div>
			<?php do_action('ampforwp_after_comment_hook');
		}
	} // end $display_comments_on
}

//Facebook Comments
function ampforwp_framework_get_facebook_comments(){
global $redux_builder_amp;
	$facebook_comments_markup = $lang = $locale = '';
	$lang = $redux_builder_amp['ampforwp-fb-comments-lang'];
	$locale = 'data-locale = "'.$lang.'"';
	if ( $redux_builder_amp['ampforwp-facebook-comments-support'] ) {
	if( ampforwp_is_non_amp() && isset($redux_builder_amp['ampforwp-amp-convert-to-wp']) && $redux_builder_amp['ampforwp-amp-convert-to-wp']) {
		$facebook_comments_markup = '<div class="fb-comments" data-href="' . get_permalink() . '" data-width="800px" data-numposts="'.$redux_builder_amp['ampforwp-number-of-fb-no-of-comments'].'"></div>';
	}
	else {  
		$facebook_comments_markup = '<section class="amp-facebook-comments">';
		$facebook_comments_markup .= '<amp-facebook-comments width=486 height=357
	    		layout="responsive" '.$locale.' data-numposts=';
		$facebook_comments_markup .= '"'. $redux_builder_amp['ampforwp-number-of-fb-no-of-comments']. '"';
	    if(ampforwp_get_data_consent()){
	    	$facebook_comments_markup .= ' data-block-on-consent ';
	    }
		$facebook_comments_markup .= 'data-href="' . get_permalink() . '"';
	    $facebook_comments_markup .= '></amp-facebook-comments></section>';
	}
		return $facebook_comments_markup;
	}
}

//Disqus Comments
function ampforwp_framework_get_disqus_comments(){
	global $redux_builder_amp;
	$width = $height = 420;

	$layout = "";
	$layout = 'responsive';
	if ( isset($redux_builder_amp['ampforwp-disqus-layout']) && 'fixed' == $redux_builder_amp['ampforwp-disqus-layout'] ) {
		$layout = 'fixed';
	
		if ( isset($redux_builder_amp['ampforwp-disqus-height']) && $redux_builder_amp['ampforwp-disqus-height'] ) {
			$height = $redux_builder_amp['ampforwp-disqus-height'];
		}
	}

	if( $redux_builder_amp['ampforwp-disqus-comments-name'] !== '' ) {
		global $post; $post_slug=$post->post_name;

		$disqus_script_host_url = "https://ampforwp.appspot.com/?api=". AMPFORWP_DISQUS_URL;

		if( $redux_builder_amp['ampforwp-disqus-host-position'] == 0 ) {
			$disqus_script_host_url = esc_url( $redux_builder_amp['ampforwp-disqus-host-file'] );
		}

		$disqus_url = $disqus_script_host_url.'?disqus_title='.$post_slug.'&url='.get_permalink().'&disqus_name='. esc_url( $redux_builder_amp['ampforwp-disqus-comments-name'] ) ."/embed.js"  ;
		?>
		<section class="amp-disqus-comments">
			<amp-iframe
				height=<?php echo $height ?>
				width=<?php echo $width ?>
				layout="<?php echo $layout ?>"
				sandbox="allow-forms allow-modals allow-popups allow-popups-to-escape-sandbox allow-same-origin allow-scripts"
				frameborder="0"
				<?php if(ampforwp_get_data_consent()){?>data-block-on-consent <?php } ?>
				src="<?php echo $disqus_url ?>" >
				<div overflow tabindex="0" role="button" aria-label="Read more"><?php echo __('Disqus Comments Loading...','accelerated-mobile-pages') ?></div>
			</amp-iframe>
		</section>
	<?php
	}
}

function ampforwp_framework_get_vuukle_comments(){
	global $post, $redux_builder_amp; 
	$apiKey ='';
	if( isset($redux_builder_amp['ampforwp-vuukle-comments-apiKey']) && $redux_builder_amp['ampforwp-vuukle-comments-apiKey'] !== ""){
		$apiKey = $redux_builder_amp['ampforwp-vuukle-comments-apiKey'];
	}
	$srcUrl = 'https://cdn.vuukle.com/amp.html?';
	$srcUrl = add_query_arg('url' ,get_permalink(), $srcUrl);
	$srcUrl = add_query_arg('host' ,site_url(), $srcUrl);
	$srcUrl = add_query_arg('id' , $post->ID, $srcUrl);
	$srcUrl = add_query_arg('apiKey' , $apiKey, $srcUrl); 
	$srcUrl = add_query_arg('title' , $post->post_title, $srcUrl);  
	$vuukle_html = '<amp-iframe width="600" height="350" layout="responsive" sandbox="allow-scripts allow-same-origin allow-modals allow-popups allow-forms" resizable frameborder="0" src="'.$srcUrl.'">

		<div overflow tabindex="0" role="button" aria-label="Show comments">Show comments</div>';
	echo $vuukle_html;
}

function ampforwp_framework_get_spotim_comments(){
	global $post, $redux_builder_amp; 
	$spotId ='';
	if( isset($redux_builder_amp['ampforwp-spotim-comments-apiKey']) && $redux_builder_amp['ampforwp-spotim-comments-apiKey'] !== ""){
		$spotId = $redux_builder_amp['ampforwp-spotim-comments-apiKey'];
	}
	$srcUrl = 'https://amp.spot.im/production.html?';
	$srcUrl = add_query_arg('spotId' ,get_permalink(), $srcUrl);
	$srcUrl = add_query_arg('postId' , $post->ID, $srcUrl);
	$spotim_html = '<amp-iframe width="375" height="815" resizable sandbox="allow-scripts allow-same-origin allow-popups allow-top-navigation" layout="responsive"
	  frameborder="0" src="'.$srcUrl.'">
	  <amp-img placeholder height="815" layout="fill" src="//amp.spot.im/loader.png"></amp-img>
	  <div overflow class="spot-im-amp-overflow" tabindex="0" role="button" aria-label="Read more">Load more...</div>
	</amp-iframe>';
	echo $spotim_html;
}

// Comments Scripts
add_filter( 'amp_post_template_data', 'ampforwp_framework_comments_scripts' );
function ampforwp_framework_comments_scripts( $data ) {

	$facebook_comments_check = ampforwp_framework_get_facebook_comments();
	global $redux_builder_amp;
	$is_pb_enabled = '';
	$is_pb_enabled = checkAMPforPageBuilderStatus(get_the_ID());	
	$display_comments_on = "";
	$display_comments_on = ampforwp_get_comments_status();

	if ( $facebook_comments_check && $redux_builder_amp['ampforwp-facebook-comments-support'] && $display_comments_on && !is_front_page()  && !$is_pb_enabled ) {
			if ( empty( $data['amp_component_scripts']['amp-facebook-comments'] ) ) {
				$data['amp_component_scripts']['amp-facebook-comments'] = 'https://cdn.ampproject.org/v0/amp-facebook-comments-0.1.js';
			}
		}
	if ( $redux_builder_amp['ampforwp-disqus-comments-support'] && $display_comments_on  && comments_open() && !$is_pb_enabled ) {
		if( $redux_builder_amp['ampforwp-disqus-comments-name'] !== '' ) {
			if ( empty( $data['amp_component_scripts']['amp-iframe'] ) ) {
				$data['amp_component_scripts']['amp-iframe'] = 'https://cdn.ampproject.org/v0/amp-iframe-0.1.js';
			}
		}
	}
	if ( isset($redux_builder_amp['ampforwp-vuukle-comments-support'])
	 	&& $redux_builder_amp['ampforwp-vuukle-comments-support']
	  	&& $display_comments_on  && comments_open() && !$is_pb_enabled 
	) {
			if ( empty( $data['amp_component_scripts']['amp-iframe'] ) ) {
				$data['amp_component_scripts']['amp-iframe'] = 'https://cdn.ampproject.org/v0/amp-iframe-0.1.js';
			}
			if ($redux_builder_amp['ampforwp-vuukle-Ads-before-comments']==1 
				&& empty( $data['amp_component_scripts']['amp-ad'] ) ) {
				$data['amp_component_scripts']['amp-ad'] = 'https://cdn.ampproject.org/v0/amp-ad-0.1.js';
			}
	}
	//spotim
	if ( isset($redux_builder_amp['ampforwp-spotim-comments-support'])
	 	&& $redux_builder_amp['ampforwp-spotim-comments-support']
	 	&& $display_comments_on  && comments_open() ) {
		if ( empty( $data['amp_component_scripts']['amp-iframe'] ) ) {
			$data['amp_component_scripts']['amp-iframe'] = 'https://cdn.ampproject.org/v0/amp-iframe-0.1.js';
		}
		
	}
		return $data;
}