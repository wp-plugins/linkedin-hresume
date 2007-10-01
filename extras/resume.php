<?php
/*
Template Name: Resume
*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head profile="http://gmpg.org/xfn/1">
	<title><?php bloginfo('name'); ?><?php wp_title(); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset'); ?>" />
	<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" />
	<style type="text/css">@import url( <?php bloginfo('template_url'); ?>/resume.css );</style>
	<!--[if lt IE 7]>
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url'); ?>/resume_ie6.css" />
	<![endif]-->
	<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="alternate" type="application/rss+xml" title="Comments RSS 2.0" href="<?php bloginfo('comments_rss2_url'); ?>" />
	<link rel="alternate" type="text/xml" title="RSS 0.92" href="<?php bloginfo('rss_url'); ?>" />
	<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
	<?php wp_get_archives('type=monthly&format=link'); ?>
	<?php wp_head(); ?>
	<link rel="shortcut icon" href="favicon.ico" />
</head>

<body>

<div id="lnhr-main">

	<?php $i = 0; if (have_posts()) : while (have_posts()) : the_post(); ?>

	<?php the_content(); ?>
				
	<?php $i++; endwhile; endif; ?>

</div>

</body>
</html>
