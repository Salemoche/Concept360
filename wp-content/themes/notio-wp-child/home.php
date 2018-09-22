<?php /* Template Name: Concept 360 - Home */ ?>

<?php get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>
  <?php the_title(); ?>
  <div class="home__landing">
    <div class="home__landing__video">
      <iframe width="420" height="315"
        src="<?php echo get_post_meta($post->ID, 'home_video', true); ?>">
      </iframe>
    </div>
  </div>

<?php endwhile; ?>




<div class="row">
  <div class="small-12 columns">
    <p>
      <?php // echo get_meta($page->ID, 'home_video', true); ?>
    </p>
    <div class="post-content no-vc">
      <?php the_content();?>
    </div>
  </div>
</div>

<?php


  get_footer();

?>
