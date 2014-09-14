<?php
/*
Template Name: About Page Template
*/
?>

<?php get_header(); ?>
<?php include(TEMPLATEPATH."/extra.php");?>
<div id="wrapper">
  <div id="content">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <?php /* Lists any subpages*/ if(wp_list_pages("child_of=".$post->ID."&echo=0")) { ?> 
    <div id="pagenav"> 
      <ul>
        <?php wp_list_pages("title_li=&child_of=".$post->ID."&depth=1&sort_column=menu_order&show_date=modified&date_format=$date_format");?> 
      </ul>
    </div>
    <?php } ?> 
    <div class="post" id="post-<?php the_ID(); ?>">
				
      <h2><?php the_title(); ?></h2>
      <div class="entry">
        <?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>
	
        <?php link_pages('<p><strong>Pages:</strong> ', '</p>', 'number'); ?>
	
      </div>
    </div>
    <?php endwhile; endif; ?>
	
    <hr />    

    <h2>Recent News and Announcements</h2>
    <ul>
    <?php
      global $post;
      query_posts('category_name=news-and-announcements');
      if (have_posts()) : while (have_posts()) : the_post(); 
    ?>

    <a href="<?php the_permalink(); ?>"><h2><?php the_title(); ?></h2></a>
    <div class="entry">
      <?php the_excerpt('<p class="serif">Read the rest of this page &raquo;</p>'); ?>
      <?php link_pages('<p><strong>Pages:</strong> ', '</p>', 'number'); ?>
      <div class="alignright">
        <a href="<?php the_permalink(); ?>">Read More &raquo;</a>
      </div>
    </div>
    <?php endwhile; ?>
    <div class="navigation">
      <div class="alignleft">
        <?php posts_nav_link('','','&laquo; Previous Entries') ?>
      </div>
      <div class="alignright">
        <?php posts_nav_link('','Next Entries &raquo;','') ?>
      </div>
    </div>

  <?php endif; ?>
    </ul> 



  </div>
</div>

<?php include(TEMPLATEPATH."/sidebar1.php");?>

<?php get_footer(); ?>