<?php
if ( function_exists('register_sidebars') )
    register_sidebars(2, array(
        'before_widget' => '<div class="widget">',
        'after_widget' => '</div>',
        'before_title' => '<h2>',
        'after_title' => '</h2>',
    ));
?>