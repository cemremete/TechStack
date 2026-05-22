<?php
/**
 * Footer template.
 *
 * @package TechStack
 */
?>
</main>
<footer class="site-footer">
	<div class="footer-grid">
		<section>
			<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<span class="logo-mark" aria-hidden="true"><i></i><i></i><i></i></span>
				<span>TechStack</span>
			</a>
			<p><?php esc_html_e( 'European tech conferences, hackathons, meetups and workshops gathered in one practical index.', 'techstack' ); ?></p>
			<p class="footer-note"><?php esc_html_e( 'Independent community platform. Not affiliated with any event organizer.', 'techstack' ); ?></p>
		</section>
		<section>
			<h2><?php esc_html_e( 'Quick Links', 'techstack' ); ?></h2>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'tech_event' ) ); ?>"><?php esc_html_e( 'Browse Events', 'techstack' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/calendar/' ) ); ?>"><?php esc_html_e( 'Calendar', 'techstack' ); ?></a>
			<a href="<?php echo esc_url( home_url( '/submit/' ) ); ?>"><?php esc_html_e( 'Submit Event', 'techstack' ); ?></a>
			<a href="<?php echo esc_url( rest_url( 'techstack/v1/events' ) ); ?>"><?php esc_html_e( 'Events API', 'techstack' ); ?></a>
		</section>
		<section>
			<h2><?php esc_html_e( 'Popular Cities', 'techstack' ); ?></h2>
			<div class="city-cloud">
				<?php foreach ( array( 'Berlin', 'Amsterdam', 'London', 'Paris', 'Barcelona', 'Lisbon', 'Vienna', 'Istanbul' ) as $city ) : ?>
					<a href="<?php echo esc_url( add_query_arg( 'city', $city, get_post_type_archive_link( 'tech_event' ) ) ); ?>"><?php echo esc_html( $city ); ?></a>
				<?php endforeach; ?>
			</div>
		</section>
	</div>
	<div class="footer-bottom">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> TechStack.</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
