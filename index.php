<?php
get_header();
?>
<section class="section">
	<div class="section-heading">
		<p class="eyebrow"><?php esc_html_e( 'TechStack', 'techstack' ); ?></p>
		<h1><?php esc_html_e( 'European tech events', 'techstack' ); ?></h1>
	</div>
	<div class="events-grid">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<?php echo techstack_event_card( get_the_ID(), 'compact' ); ?>
			<?php endwhile; ?>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
