<div id="navigation">
   
   <?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar(1) ) : else : ?>
	
      <h2><?php _e('Search this site'); ?></h2>
      <?php include (TEMPLATEPATH . '/searchform.php'); ?>

      <h2><?php _e('Archives'); ?></h2>
      <ul>
         <?php wp_list_categories('title_li=&sortby=name&show_count=0&hierarchical=0&hide_empty=1'); ?>
      </ul>
				
      <h2><?php _e('Links'); ?></h2>
         <?php wp_list_bookmarks('title_li=&categorize=0&show_images=1&show_description=0&orderby=rand'); ?>
	
      <h2>Feeds</h2>
      <ul class="feeds">
         <li><a href="<?php bloginfo('rss2_url'); ?>" title="<?php _e('Syndicate this site using RSS 2.0'); ?>"><?php _e('Entries <abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li>
         <li><a href="<?php bloginfo('comments_rss2_url'); ?>" title="<?php _e('The latest comments to all posts in RSS'); ?>"><?php _e('Comments <abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li>
         <li><a href="feed:<?php bloginfo('atom_url'); ?>" title="<?php _e('Syndicate this site using Atom'); ?>"><?php _e('Atom'); ?></a></li>
         <?php wp_meta(); ?>
      </ul>
   <?php endif; ?>
</div>