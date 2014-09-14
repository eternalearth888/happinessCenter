<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<title><?php bloginfo('name'); ?> <?php wp_title(); ?></title>

<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats -->
<?php if (function_exists('jkeywords_post_tags')) { ?><meta name="keywords" content="<?php jkeywords_meta_keywords();?>" /><?php } ?>
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<script src="<?php bloginfo('url'); ?>/wp-content/plugins/smoothgallery/scripts/mootools.js" type="text/javascript"></script>
<script src="<?php bloginfo('url'); ?>/wp-content/plugins/smoothgallery/scripts/jd.gallery.js" type="text/javascript"></script>
<link rel="stylesheet" href="<?php bloginfo('url'); ?>/wp-content/plugins/smoothgallery/css/jd.gallery.css" type="text/css" media="screen" />

<?php wp_head(); ?>
</head>

<body>
<div id="container">
  <div id="header">
    <h1><a href="<?php echo get_settings('home'); ?>"><?php bloginfo('name'); ?></a></h1>
    <div class="description"><?php bloginfo('description'); ?></div>
      <div id="nav">
        <ul id="navbar">
        <li <?php if (is_home()) {?>class="current_page_item"<?php } ?>><a href="<?php echo get_settings('home'); ?>/">Home</a></li>
        <li <?php if (is_category('18')) {?>class="current_page_item"<?php } ?>><a href="?cat=18">Q&A</a></li>
        <li <?php if (is_category('3')) {?>class="current_page_item"<?php } ?>><a href="?cat=3">Articles</a></li>
        <li <?php if (is_category('17')) {?>class="current_page_item"<?php } ?>><a href="?cat=17">Products</a></li>

        <?php wp_list_pages('sort_column=menu_order&depth=1&title_li=&exclude=135,136'); ?>
        </ul></div>

      </div>
      <script type="text/javascript">
        Window.onDomReady( function() { myGallery = new gallery($('myGallery'), {
          timed: true, showArrows: true, showCarousel: false, delay: 6000
          }); } );
      </script>