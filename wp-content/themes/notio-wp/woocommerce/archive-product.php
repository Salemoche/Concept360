<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header( 'shop' ); 
$shop_full_width = isset($_GET['shop_full_width']) ? $_GET['shop_full_width'] : ot_get_option('shop_full_width', 'off');
$shop_sidebar = isset($_GET['sidebar']) ? $_GET['sidebar'] : ot_get_option('shop_sidebar', 'no');
?>
<div class="row shop-row <?php echo ($shop_full_width === 'on') ? 'full-width-row' : ''; ?>">
	<div class="small-12 columns small-order-1 large-order-2<?php if ($shop_sidebar !== 'no') { echo ' large-9'; } ?>">
		<aside class="thb_shop_bar">
			<div class="row align-middle">
		    <div class="small-12 medium-6 columns breadcrumb-column">
		      <?php if ( have_posts() ) : ?>
		      		<?php do_action( 'thb_before_shop_loop_result_count' ); ?>
		      <?php endif; ?>
		    </div>
		    <div class="small-12 medium-6 columns result-column">
		      <?php if ( have_posts() ) : ?>
		          <?php do_action( 'thb_before_shop_loop_catalog_ordering' ); ?>
		      <?php endif; ?>
		    </div>
		  </div>
		</aside>
		<?php
			/**
			 * woocommerce_archive_description hook.
			 *
			 * @hooked woocommerce_taxonomy_archive_description - 10
			 * @hooked woocommerce_product_archive_description - 10
			 */
			do_action( 'woocommerce_archive_description' );
		?>
		<?php if ( woocommerce_product_loop() ) {
		
			/**
			 * Hook: woocommerce_before_shop_loop.
			 *
			 * @hooked wc_print_notices - 10
			 * @hooked woocommerce_result_count - 20
			 * @hooked woocommerce_catalog_ordering - 30
			 */
			do_action( 'woocommerce_before_shop_loop' );
		
			woocommerce_product_loop_start();
		
			if ( wc_get_loop_prop( 'total' ) ) {
				while ( have_posts() ) {
					the_post();
		
					/**
					 * Hook: woocommerce_shop_loop.
					 *
					 * @hooked WC_Structured_Data::generate_product_data() - 10
					 */
					do_action( 'woocommerce_shop_loop' );
		
					wc_get_template_part( 'content', 'product' );
				}
			}
		
			woocommerce_product_loop_end();
		
			/**
			 * Hook: woocommerce_after_shop_loop.
			 *
			 * @hooked woocommerce_pagination - 10
			 */
			do_action( 'woocommerce_after_shop_loop' );
		} else {
			/**
			 * Hook: woocommerce_no_products_found.
			 *
			 * @hooked wc_no_products_found - 10
			 */
			do_action( 'woocommerce_no_products_found' );
		} ?>
	</div>
	<?php
		if ($shop_sidebar !== 'no') {
		/**
		 * woocommerce_sidebar hook.
		 *
		 * @hooked woocommerce_get_sidebar - 10
		 */
		do_action( 'woocommerce_sidebar' );
		}
	?>
	<?php
		/**
		 * woocommerce_after_main_content hook.
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'woocommerce_after_main_content' );
	?>
</div>
<?php get_footer( 'shop' ); ?>