<div id="extra">
   <div id="myGallery">
      <div class="imageElement">
         <h3>Ask Dr. Elia</h3>
         <p>Answering your relationship questions</p>
         <a href="http://www.askdrelia.com" title="Ask Dr. Elia" class="open"></a>
         <img src="<?php bloginfo('url'); ?>/images/239556_XS.jpg" class="full" alt="Pic1" />
      </div>
      <div class="imageElement">
         <h3>Parenting...</h3>
         <p>It's not always easy!</p>
         <a href="http://www.askdrelia.com" title="Ask Dr. Elia" class="open"></a>
         <img src="<?php bloginfo('url'); ?>/images/1838085_XS.jpg" class="full" alt="Pic2" />
      </div>
      <div class="imageElement">
         <h3>Ask Dr. Elia</h3>
         <p>Finding balance in your life...</p>
         <a href="http://www.askdrelia.com" title="Ask Dr. Elia" class="open"></a>
         <img src="<?php bloginfo('url'); ?>/images/4107829_XS.jpg" class="full" alt="Pic3" />
      </div>
      <div class="imageElement">
         <h3>DTR...</h3>
         <p>Define the relationship <b>before</b> marriage</p>
         <a href="http://www.askdrelia.com" title="Ask Dr. Elia" class="open"></a>
         <img src="<?php bloginfo('url'); ?>/images/4160524_XS.jpg" class="full" alt="Pic4" />
      </div>
      <div class="imageElement">
         <h3>Marriage</h3>
         <p>Every marriage has its seasons</p>
         <a href="http://www.askdrelia.com" title="Ask Dr. Elia" class="open"></a>
         <img src="<?php bloginfo('url'); ?>/images/5230536_XS.jpg" class="full" alt="Pic5" />
      </div>
      <div class="imageElement">
         <h3>Ask Dr. Elia</h3>
         <p>How to find joy in your golden years</p>
         <a href="http://www.askdrelia.com" title="Ask Dr. Elia" class="open"></a>
         <img src="<?php bloginfo('url'); ?>/images/5230569_XS.jpg" class="full" alt="Pic6" />
      </div>
      <div class="imageElement">
         <h3>Relationships</h3>
         <p>They make life so much richer!</p>
         <a href="http://www.askdrelia.com" title="Ask Dr. Elia" class="open"></a>
         <img src="<?php bloginfo('url'); ?>/images/5230573_XS.jpg" class="full" alt="Pic7" />
      </div>


   </div>

   <div id="extra-sidebar">
      <?php if ( function_exists('dynamic_sidebar') && dynamic_sidebar(2) ) { ?>
			
      <?php } else { ?>
         <h2>What's Happening</h2>
         <?php
         /*this stuff supports the RSS Event plugin*/ 
         if (function_exists('rs_event_list')) {
				
            $rs_event_arg = array( "timespan"      => 60 * 60 * 24 * 365,
                                   "date_format"   => "jS M 'y",
                                   "time_format"   => "g:i A",
                                   "group_by_date" => true,
                                   "event_html"    => "<a href='%URL%'>%TITLE% (%TIME%)</a>",
                                   "max_events"    => 4,				
                                 );
				
            rs_event_list($rs_event_arg); 
         } else { ?>
            <ul><li>You need the RS Event plugin for this section.</li>
            <li>Or use the Sidebar Widget plugin to change the content.</li>
            </ul>
         <?php } ?>
      <?php } ?> 
   </div>
</div>