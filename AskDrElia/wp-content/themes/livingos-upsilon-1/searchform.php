<?php if (!is_search()) {
		$search_text = "search the archives";
	} else {
		$search_text = "$s";
	}
?>
		<form method="get" id="searchform" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<input type="text" value="<?php echo wp_specialchars($search_text, 1); ?>" name="s" id="s" onfocus="if (this.value == 'search the archives') {this.value = '';}" onblur="if (this.value == '') {this.value = 'search the archives';}" />
			<input type="submit" id="searchsubmit" value="go" />
		</form>


