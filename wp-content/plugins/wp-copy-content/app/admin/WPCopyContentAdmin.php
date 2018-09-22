<?php
/**
 * Functions of WP Copy Content's admin functions.
 *
 * @package WPCopyContent
 */

/**
 * Class for admin.
 *
 * @package WPCopyContent
 */
class WPCopyContentAdmin {

	/**
	 * Cunstructor for admin class.
	 */
	public function __construct() {

		// Add metaboxes.
		add_action( 'add_meta_boxes', array( $this, 'wpcc_add_custom_meta_boxes' ), 10, 2 );

		// Add scripts.
		add_action( 'admin_enqueue_scripts', array( $this, 'wpcc_enqueue_scripts' ) );

		// Get post suggestions.
		add_action( 'wp_ajax_wpcc_find_posts', array( $this, 'wpcc_get_posts_suggestion' ) );

		// Fetch requested post's content.
		add_action( 'wp_ajax_wpcc_copy_from_post', array( $this, 'wpcc_fetch_content' ) );
	}

	/**
	 * Fetch the content from requested post.
	 */
	public function wpcc_fetch_content() {

		// Validate ajax by nounce.
		check_ajax_referer( 'wpcc-find-posts' );

		$post_id = isset( $_POST['pid'] ) ? sanitize_text_field( wp_unslash( $_POST['pid'] ) ) : 0;

		// If post is not available, then throw error.
		if ( 0 === $post_id ) {
			wp_send_json_error( esc_html__( 'Post is not available.', 'wpcc' ) );
		}

		// Get the content from post ID.
		$content = get_post_field( 'post_content', $post_id );

		// Send json response.
		if ( $content ) {
			wp_send_json_success( $content );
		} else {
			wp_send_json_error( esc_html__( 'Content is not available.', 'wpcc' ) );
		}

		wp_die();
	}

	/**
	 * Get result from typed title.
	 */
	public function wpcc_get_posts_suggestion() {

		// Validate nounce field.
		check_ajax_referer( 'wpcc-find-posts' );

		$s = isset( $_POST['ps'] ) ? sanitize_text_field( wp_unslash( $_POST['ps'] ) ) : '';

		// If no search keyword is added, then don't search.
		if ( empty( $s ) ) {
			return;
		}

		// Get post types.
		$post_types = get_post_types(
			array(
				'public' => true,
			),
			'objects'
		);
		unset( $post_types['attachment'] );

		// Filter for search only with titles.
		add_filter( 'posts_where', array( $this, 'wpcc_posts_where' ), 10, 2 );

		// Set query for posts.
		$posts = new WP_Query( array(
			'post_type' => array_keys( $post_types ),
			'post_status' => 'any',
			'posts_per_page' => 50,
			'no_found_rows' => true,
		));

		// Remove filter after query build.
		remove_filter( 'posts_where', array( $this, 'wpcc_posts_where' ), 10, 2 );

		// Get results.
		$posts = $posts->posts;

		// Send error message of no posts found.
		if ( ! $posts ) {
			wp_send_json_error( esc_html__( 'No items found.', 'wpcc' ) );
		}

		// Send json response.
		wp_send_json_success( $posts );

		wp_die();
	}

	/**
	 * Filter for search only from post tile.
	 *
	 * @param string $where Default where query.
	 * @param object $wp_query Reference by wp_query.
	 * @return string $where Modified where statement.
	 */
	function wpcc_posts_where( $where, &$wp_query ) {

		check_ajax_referer( 'wpcc-find-posts' );

		global $wpdb;

		$s = isset( $_POST['ps'] ) ? sanitize_text_field( wp_unslash( $_POST['ps'] ) ) : '';

		// Search string.
		$s = sanitize_text_field( wp_unslash( $s ) );

		// Build query for search from post title.
		if ( ! empty( $s ) ) {
			$where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'' . esc_sql( $wpdb->esc_like( $s ) ) . '%\'';
		}

		return $where;
	}

	/**
	 * Add JS and styles.
	 */
	public function wpcc_enqueue_scripts() {

		// Add admin.js.
		wp_enqueue_script( 'wpcc-jscript', WPCC_URL . 'app/assets/js/admin.min.js', array(), '1.0.0', true );
	}

	/**
	 * Add metabox
	 *
	 * @param string $post_type Post type.
	 * @param object $post Post object.
	 */
	public function wpcc_add_custom_meta_boxes( $post_type, $post ) {

		// Get post types.
		$post_types = get_post_types(
			array(
				'public' => true,
			),
			'objects'
		);
		unset( $post_types['attachment'] );

		add_meta_box(
			'my-meta-box',
			esc_html__( 'WP Copy Content', 'wcc' ),
			array( $this, 'wpcc_meta_box' ),
			array_keys( $post_types ),
			'side',
			'high'
		);
	}

	/**
	 * Render metabox.
	 */
	public function wpcc_meta_box() {
		?>
		<label for="wcc_post_title"><?php echo esc_html__( 'Enter post/page title:', 'wcc' ); ?></label>
		<input type="text" name="wcc_post_title" id="wcc_post_title" />
		<input type="hidden" name="wcc_post_id" id="wcc_post_id" />
		<?php wp_nonce_field( 'wpcc-find-posts', '_ajax_nonce', false ); ?>
		<input class="button" id="wcc_fetch_button" value="<?php echo esc_html__( 'Fetch Content', 'wpcc' ); ?>" type="button" style="display:none;">
		<div id="wpcc-spiner" class="spinner"></div>
		<?php
	}
}
