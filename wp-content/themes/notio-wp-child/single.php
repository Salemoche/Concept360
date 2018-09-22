<?php get_header(); ?>
<?php
  $posttags = get_the_tags();
?>
<h1>Custom Single Page</h1>
<?php if (have_posts()) :  while (have_posts()) : the_post(); ?>
  <div class="project row">
    <div class="columns medium-8 small-12">
      <?php the_content(); ?>
    </div>
    <div class="project__info columns small-12 medium-4">
      <div class="project__info__breadcrumbs">
        <p>Breadcrumbs</p>
      </div>
      <div class="project__info__title">
        <?php the_title(); ?>
      </div>
      <div class="project__info__description">
        <p>Description</p>
      </div>
      <div class="project__info__tags">
        <p>Tags</p>
        <?php
          if ($posttags) {
            foreach($posttags as $tag) {
              echo $tag->name . ' ';
            }
          }
         ?>
      </div>
    </div>
    <div class="project__images columns small-12">

    </div>
    <div class="project__related-projects columns small-12">
      <h4>Ähnliche Projekte</h4>
    </div>
  </div>
<?php endwhile; else : endif; ?>
<?php get_footer(); ?>
