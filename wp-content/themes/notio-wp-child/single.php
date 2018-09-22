<?php get_header(); ?>
<h1>Custom Single Page</h1>
<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
  <article itemscope itemtype="http://schema.org/BlogPosting" <?php post_class('post blog-post'); ?> id="post-<?php the_ID(); ?>" role="article">
		<div class="row max_width">
			<div class="small-12 medium-10 large-8 medium-centered columns">
				<header class="post-title">
					<h1 itemprop="headline"><?php the_title(); ?></h1>
				</header>
				<div class="post-content">
					<?php the_content(); ?>
					<?php if ( is_single()) { wp_link_pages(); } ?>
				</div>
			</div>
		</div>
		<?php do_action( 'thb_PostMeta' ); ?>
  </article>
<?php endwhile; else : endif; ?>
<?php get_footer(); ?>
