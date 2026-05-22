<?php
/**
 * Submit event page.
 *
 * @package TechStack
 */

$submitted = techstack_handle_submit_event();
$recent_events = new WP_Query(
	array(
		'post_type'      => 'tech_event',
		'post_status'    => 'publish',
		'posts_per_page' => 3,
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);
$listed_count = techstack_count_events();
$cities_count = techstack_count_unique_event_meta( 'event_location_city' );
get_header();
?>
<section class="submit-shell">
	<div class="submit-intro">
		<p class="eyebrow"><?php esc_html_e( 'Community input', 'techstack' ); ?></p>
		<h1><?php esc_html_e( 'Submit an Event', 'techstack' ); ?></h1>
		<p><?php esc_html_e( 'Send a real European tech event for editorial review. Approved events appear in the public index.', 'techstack' ); ?></p>
		<div class="submit-context-card">
			<h2><?php esc_html_e( 'Why submit?', 'techstack' ); ?></h2>
			<ul>
				<li><?php esc_html_e( 'Free to list', 'techstack' ); ?></li>
				<li><?php esc_html_e( 'Reaches thousands of European developers', 'techstack' ); ?></li>
				<li><?php esc_html_e( 'Published within 48 hours after review', 'techstack' ); ?></li>
			</ul>
		</div>
		<?php if ( $recent_events->have_posts() ) : ?>
			<div class="submit-recent">
				<h2><?php esc_html_e( 'Recently added', 'techstack' ); ?></h2>
				<?php
				while ( $recent_events->have_posts() ) :
					$recent_events->the_post();
					$recent_id   = get_the_ID();
					$recent_type = techstack_get_field( 'event_type', $recent_id ) ?: 'Conference';
					?>
					<a class="submit-mini-event <?php echo esc_attr( techstack_type_class( $recent_type ) ); ?>" href="<?php the_permalink(); ?>">
						<span class="event-badge <?php echo esc_attr( techstack_type_class( $recent_type ) ); ?>"><?php echo esc_html( $recent_type ); ?></span>
						<strong><?php echo esc_html( techstack_get_field( 'event_name', $recent_id ) ?: get_the_title() ); ?></strong>
						<small><?php echo esc_html( techstack_date_range( $recent_id ) ); ?> / <?php echo esc_html( techstack_get_field( 'event_location_city', $recent_id ) ?: __( 'Online', 'techstack' ) ); ?></small>
					</a>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		<?php endif; ?>
		<div class="submit-trust-row">
			<span><strong><?php echo esc_html( number_format_i18n( $listed_count ) ); ?></strong><?php esc_html_e( 'Events listed', 'techstack' ); ?></span>
			<span><strong><?php echo esc_html( number_format_i18n( $cities_count ) ); ?></strong><?php esc_html_e( 'Cities covered', 'techstack' ); ?></span>
			<span><strong><?php esc_html_e( 'Community', 'techstack' ); ?></strong><?php esc_html_e( 'curated', 'techstack' ); ?></span>
		</div>
	</div>
	<?php if ( true === $submitted ) : ?>
		<div class="submit-success-state">
			<div class="submit-success-state__icon">&#10003;</div>
			<h2><?php esc_html_e( 'Event Submitted', 'techstack' ); ?></h2>
			<p><?php esc_html_e( 'Our team will review your submission within 48 hours. If approved it will appear in the public index.', 'techstack' ); ?></p>
			<a class="button button-primary" href="<?php echo esc_url( get_permalink() ); ?>"><?php esc_html_e( 'Submit Another', 'techstack' ); ?></a>
		</div>
	<?php else : ?>
	<form class="submit-form" method="post" novalidate data-submit-form>
		<?php wp_nonce_field( 'techstack_submit_event', 'techstack_submit_nonce' ); ?>
		<input type="hidden" name="techstack_submit_event" value="1">
		<?php if ( is_wp_error( $submitted ) ) : ?>
			<div class="notice error"><?php echo esc_html( $submitted->get_error_message() ); ?></div>
		<?php endif; ?>
		<div class="submit-steps" aria-label="<?php esc_attr_e( 'Submission progress', 'techstack' ); ?>">
			<span class="is-active"><b>1</b><?php esc_html_e( 'Event Details', 'techstack' ); ?></span>
			<span><b>2</b><?php esc_html_e( 'Location and Format', 'techstack' ); ?></span>
			<span><b>3</b><?php esc_html_e( 'Topics and Links', 'techstack' ); ?></span>
		</div>
		<div class="form-grid">
			<label><?php esc_html_e( 'Event name', 'techstack' ); ?><input required type="text" name="event_name"></label>
			<label><?php esc_html_e( 'Type', 'techstack' ); ?><select name="event_type"><?php foreach ( array( 'Conference', 'Hackathon', 'Meetup', 'Workshop', 'Summit', 'Webinar' ) as $type ) : ?><option><?php echo esc_html( $type ); ?></option><?php endforeach; ?></select></label>
			<label><?php esc_html_e( 'Start date', 'techstack' ); ?><input required type="date" name="event_date_start"></label>
			<label><?php esc_html_e( 'End date', 'techstack' ); ?><input type="date" name="event_date_end"></label>
			<label><?php esc_html_e( 'City', 'techstack' ); ?><input type="text" name="event_location_city"></label>
			<label><?php esc_html_e( 'Country', 'techstack' ); ?><input type="text" name="event_location_country"></label>
			<label class="form-full"><?php esc_html_e( 'Venue', 'techstack' ); ?><input type="text" name="event_location_venue"></label>
			<label><?php esc_html_e( 'Registration URL', 'techstack' ); ?><input required type="url" name="event_registration_url"></label>
			<label><?php esc_html_e( 'Price', 'techstack' ); ?><input type="text" name="event_price" placeholder="<?php esc_attr_e( 'Free or EUR 299', 'techstack' ); ?>"></label>
			<label><?php esc_html_e( 'Organizer name', 'techstack' ); ?><input type="text" name="event_organizer"></label>
			<label><?php esc_html_e( 'Image URL', 'techstack' ); ?><input type="url" name="event_image_url"></label>
			<label class="submit-toggle-card">
				<input type="checkbox" name="event_is_online" value="1">
				<span class="submit-toggle-card__icon"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c3 3.2 3 14.8 0 18M12 3c-3 3.2-3 14.8 0 18"/></svg></span>
				<span><strong><?php esc_html_e( 'Online Event', 'techstack' ); ?></strong><small><?php esc_html_e( 'Accessible from anywhere in the world', 'techstack' ); ?></small></span>
			</label>
			<label class="submit-toggle-card submit-toggle-card--hybrid">
				<input type="checkbox" name="event_is_hybrid" value="1">
				<span class="submit-toggle-card__icon"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 3v18M12 3a9 9 0 0 1 0 18"/></svg></span>
				<span><strong><?php esc_html_e( 'Hybrid Event', 'techstack' ); ?></strong><small><?php esc_html_e( 'Both in-person and online attendance', 'techstack' ); ?></small></span>
			</label>
			<fieldset class="form-full topic-fieldset">
				<legend><?php esc_html_e( 'Topics', 'techstack' ); ?></legend>
				<?php foreach ( array( 'AI/ML', 'Web Dev', 'DevOps', 'Distributed Systems', 'Cybersecurity', 'Blockchain', 'Data Science', 'Mobile', 'Cloud', 'Open Source', 'Startup', 'Design' ) as $topic ) : ?>
					<label class="submit-topic-pill"><input type="checkbox" name="event_topics[]" value="<?php echo esc_attr( $topic ); ?>"> <span><?php echo esc_html( $topic ); ?></span></label>
				<?php endforeach; ?>
			</fieldset>
			<label class="form-full"><?php esc_html_e( 'Description', 'techstack' ); ?><textarea required name="event_description" rows="7"></textarea></label>
		</div>
		<button class="button button-primary" type="submit"><?php esc_html_e( 'Submit Event', 'techstack' ); ?></button>
	</form>
	<?php endif; ?>
</section>
<?php
get_footer();
