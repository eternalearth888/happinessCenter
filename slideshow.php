<?php
$directory = 'images/slideshow'; 	
try {    	
	// Styling for images	
	echo '<div id="myslides">';	
	foreach ( new DirectoryIterator($directory) as $item ) {			
		if ($item->isFile()) {
			$path = $directory . '/' . $item;	
			echo '<img src="' . $path . '"/>';	
		}
	}	
	echo '</div>';
}	
catch(Exception $e) {
	echo 'No images found for this slideshow.<br />';	
}
?>