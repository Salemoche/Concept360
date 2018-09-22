<?php /* Template Name: Concept 360 - Home */ ?>

<?php get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>
  <div class="home row">
    <div class="home__landing columns small-12">
      <div class="home__landing__video">
        <?php the_field('home_video'); ?>
      </div>
      <div class="home__landing__title">
        <h2><?php echo get_post_meta($post->ID, 'landing_subtitle', true); ?></h2>
        <h1><?php echo get_post_meta($post->ID, 'landing_title', true); ?></h1>
      </div>
      <div class="home__landing__project-category__container row">
        <div class="home__landing_project-category home__landing_project-category--projects columns small-4">
          <h3>Projekte</h3>
          <p><?php echo get_post_meta($post->ID, 'landing_projects_text', true); ?></p>
        </div>
        <div class="home__landing_project-category home__landing_project-category--insights columns small-4">
          <h3>Insights</h3>
          <p><?php echo get_post_meta($post->ID, 'landing_insights_text', true); ?></p>
        </div>
        <div class="home__landing_project-category home__landing_project-category--showcase columns small-4">
          <h3>Showcase</h3>
          <p><?php echo get_post_meta($post->ID, 'landing_showcase_text', true); ?></p>
        </div>
      </div>
    </div>
    <div class="home__about__slider columns small-12">

    </div>
    <div class="home__ueber-uns columns small-12">
      <div class="home__ueber-uns__text">
        <h4><?php echo get_post_meta($post->ID, 'home_about-us_title', true); ?></h4>
        <p><?php echo get_post_meta($post->ID, 'home_about-us_text', true); ?></p>
      </div>
    </div>
    <div class="home__project__slider columns small-12">

    </div>
    <div class="home__customers">
      <h1>Kunden</h1>
      <?php
        //Get the images ids from the post_metadata
        $images = acf_photo_gallery('customer_logos', $post->ID);
        //Check if return array has anything in it
        if( count($images) ):
        //Cool, we got some data so now let's loop over it
        foreach($images as $image):
          $id = $image['id']; // The attachment id of the media
          $title = $image['title']; //The title
          $caption= $image['caption']; //The caption
          $full_image_url= $image['full_image_url']; //Full size image url
          // $full_image_url = acf_photo_gallery_resize_image($full_image_url, 262, 160); //Resized size to 262px width by 160px height image url
          $thumbnail_image_url= $image['thumbnail_image_url']; //Get the thumbnail size image url 150px by 150px
          $url= $image['url']; //Goto any link when clicked
          $target= $image['target']; //Open normal or new tab
          $alt = get_field('photo_gallery_alt', $id); //Get the alt which is a extra field (See below how to add extra fields)
          $class = get_field('photo_gallery_class', $id); //Get the class which is a extra field (See below how to add extra fields)
      ?>
          <div class="home__customers__item columns small-6 medium-2">
            <div class="thumbnail">
              <?php if( !empty($url) ){ ?><a href="<?php echo $url; ?>" <?php echo ($target == 'true' )? 'target="_blank"': ''; ?>><?php } ?>
                <img src="<?php echo $full_image_url; ?>" alt="<?php echo $title; ?>" title="<?php echo $title; ?>">
              <?php if( !empty($url) ){ ?></a><?php } ?>
            </div>
          </div>
      <?php endforeach; endif; ?>
      <div class="home__customers__item columns small-6 medium-2">

      </div>
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
