<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php wp_site_icon(); ?>
	<?php
		/* Always have wp_head() just before the closing </head>
		 * tag of your theme, or you will break many plugins, which
		 * generally use this hook to add elements to <head> such
		 * as styles, scripts, and meta tags.
		 */
		wp_head();
	?>
</head>
<body <?php body_class(); ?>>
<!-- Start Loader -->
<div class="pace"></div>
<!-- End Loader -->
<div id="wrapper" class="open">

	<!-- Start Mobile Menu -->
	<?php get_template_part( 'inc/templates/header/mobile_menu'); ?>
	<!-- End Mobile Menu -->

	<div class="header">
		<menu>
			<h1>Concept 360 Header</h1>
		</menu>
	</div>

	<div role="main">
