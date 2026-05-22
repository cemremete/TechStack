<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label>
		<span class="screen-reader-text"><?php esc_html_e( 'Search for events', 'techstack' ); ?></span>
		<input type="search" class="search-field" placeholder="<?php esc_attr_e( 'Search tech events', 'techstack' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s">
	</label>
	<input type="hidden" name="post_type" value="tech_event">
	<button type="submit"><?php esc_html_e( 'Search', 'techstack' ); ?></button>
</form>
