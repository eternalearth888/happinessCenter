	
  <div id="footer">
    <div id="rtext">
      <p> Visitors since 5/2008: <?php include "./counter/counter.php"; ?><br/>
      Site last updated on <?php $d=get_lastpostmodified(); echo date("l, F d, Y", strtotime($d));?><br/>
      <a href="<?php bloginfo('rss2_url'); ?>">Entries (RSS)</a>
      and <a href="<?php bloginfo('comments_rss2_url'); ?>">Comments (RSS)</a>.<br/>
      Problems/Suggestions?  <a href="<?php get_bloginfo('url'); ?>/?page_id=12">Contact the site admin</a>.
      <br />
      Site theme based on <a href="http://themes.livingos.com/2008/03/04/livingos-upsilon/">Living OS Upsilon</a>.</p>
    </div>
    <div id="ltext">
      <p>Copyright &copy; 2008 by AskDrElia.com.  All rights reserved.</p>
    </div>
    <!-- <?php echo get_num_queries(); ?> queries. <?php timer_stop(1); ?> seconds. -->
    </p>
  </div>
</div> <!-- end of container-->
  <?php wp_footer(); ?>
<!-- 
<iframe src="http://www.askdrelia.com/wp-mail.php" name="mailiframe" width="0" height="0" frameborder="0" scrolling="no" title=""> 
-->
<iframe src="http://www.askdrelia.com/wp-content/plugins/postie/get_mail.php" name="mailiframe" width="0" height="0" frameborder="0" scrolling="no" title="">

</iframe>

</body>
</html>