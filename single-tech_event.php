<?php
/**
 * Single event template.
 *
 * @package TechStack
 */

get_header();
the_post();
$post_id          = get_the_ID();
$type             = techstack_get_field( 'event_type', $post_id ) ?: 'Conference';
$title            = techstack_get_field( 'event_name', $post_id ) ?: get_the_title();
$image            = techstack_get_field( 'event_image_url', $post_id );
$price            = techstack_get_field( 'event_price', $post_id ) ?: 'Free';
$deadline         = techstack_get_field( 'event_registration_deadline', $post_id );
$registration_url = techstack_get_field( 'event_registration_url', $post_id ) ?: techstack_get_field( 'event_url', $post_id );
$days_left        = $deadline ? max( 0, (int) floor( ( strtotime( techstack_date_for_display( $deadline, 'Y-m-d' ) ) - current_time( 'timestamp' ) ) / DAY_IN_SECONDS ) ) : null;
$deadline_progress = null !== $days_left ? max( 6, min( 100, (int) round( ( $days_left / 30 ) * 100 ) ) ) : 0;
$deadline_soon    = null !== $days_left && $days_left <= 14;

// Rich data fields.
$audience          = techstack_get_field( 'event_audience', $post_id );
$has_certificate   = techstack_bool_field( 'event_certificate', $post_id );
$cert_type         = techstack_get_field( 'event_certificate_type', $post_id );
$attendee_profiles = techstack_get_field( 'event_attendee_profiles', $post_id );
$is_networking     = techstack_bool_field( 'event_networking', $post_id );
$is_remote         = techstack_bool_field( 'event_remote_friendly', $post_id );
$is_live_stream    = techstack_bool_field( 'event_live_stream', $post_id );
$is_scholarships   = techstack_bool_field( 'event_scholarships', $post_id );
$scholarship_url   = techstack_get_field( 'event_scholarship_url', $post_id );
$speakers_count    = techstack_get_field( 'event_speakers_count', $post_id );
$workshops_count   = techstack_get_field( 'event_workshops_count', $post_id );
$is_online         = techstack_bool_field( 'event_is_online', $post_id );
$is_hybrid         = techstack_bool_field( 'event_is_hybrid', $post_id );
$event_languages   = techstack_get_field( 'event_languages', $post_id );
if ( is_string( $event_languages ) ) {
	$maybe           = maybe_unserialize( $event_languages );
	$event_languages = is_array( $maybe ) ? $maybe : array();
}
$event_languages = is_array( $event_languages ) ? $event_languages : array();

$lang_colors = array(
	'JavaScript' => array( 'bg' => '#F7DF1E', 'color' => '#1a1a1a' ),
	'Python'     => array( 'bg' => '#3776AB', 'color' => '#ffffff' ),
	'Rust'       => array( 'bg' => '#CE422B', 'color' => '#ffffff' ),
	'Go'         => array( 'bg' => '#00ADD8', 'color' => '#1a1a1a' ),
	'TypeScript' => array( 'bg' => '#3178C6', 'color' => '#ffffff' ),
	'Java'       => array( 'bg' => '#F89820', 'color' => '#1a1a1a' ),
	'Kotlin'     => array( 'bg' => '#7F52FF', 'color' => '#ffffff' ),
	'Swift'      => array( 'bg' => '#FA7343', 'color' => '#ffffff' ),
	'C++'        => array( 'bg' => '#00599C', 'color' => '#ffffff' ),
	'PHP'        => array( 'bg' => '#777BB4', 'color' => '#ffffff' ),
	'Ruby'       => array( 'bg' => '#CC342D', 'color' => '#ffffff' ),
	'Scala'      => array( 'bg' => '#DC322F', 'color' => '#ffffff' ),
);
<article class="single-event">
	<div class="single-event__main">
		<?php if ( $image ) : ?>
			<img class="single-event__image" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $title ); ?>">
		<?php endif; ?>
		<div class="single-event__heading <?php echo esc_attr( techstack_type_class( $type ) ); ?>">
			<span class="event-badge <?php echo esc_attr( techstack_type_class( $type ) ); ?>"><?php echo esc_html( $type ); ?></span>
			<h1><?php echo esc_html( $title ); ?></h1>
		</div>
		<div class="single-meta">
			<span><?php echo esc_html( techstack_date_range( $post_id ) ); ?></span>
			<span><?php echo esc_html( trim( techstack_get_field( 'event_location_city', $post_id ) . ', ' . techstack_get_field( 'event_location_country', $post_id ), ', ' ) ); ?></span>
			<span><?php echo esc_html( techstack_get_field( 'event_organizer', $post_id ) ); ?></span>
			<?php if ( techstack_get_field( 'event_attendee_count', $post_id ) ) : ?>
				<span><?php echo esc_html( number_format_i18n( (int) techstack_get_field( 'event_attendee_count', $post_id ) ) ); ?> <?php esc_html_e( 'attendees', 'techstack' ); ?></span>
			<?php endif; ?>
		</div>
		<div class="event-description">
			<?php echo wp_kses_post( wpautop( techstack_get_field( 'event_description', $post_id ) ?: get_the_content() ) ); ?>
		</div>
		<div class="topic-list">
			<?php foreach ( techstack_topics( $post_id ) as $topic ) : ?>
				<span><?php echo esc_html( $topic ); ?></span>
			<?php endforeach; ?>
		</div>
		<?php if ( techstack_get_field( 'event_cfp_deadline', $post_id ) ) : ?>
			<section class="cfp-panel">
				<h2><?php printf( esc_html__( 'Call for Papers open until %s', 'techstack' ), esc_html( techstack_date_for_display( techstack_get_field( 'event_cfp_deadline', $post_id ) ) ) ); ?></h2>
				<a class="button button-primary" href="<?php echo esc_url( techstack_get_field( 'event_cfp_url', $post_id ) ?: $registration_url ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Apply', 'techstack' ); ?></a>
			</section>
		<?php endif; ?>

		<?php if ( $audience || $attendee_profiles ) : ?>
		<section class="event-rich-section who-attend-card">
			<h2><?php esc_html_e( 'Who Should Attend', 'techstack' ); ?></h2>
			<?php if ( $audience ) : ?>
				<?php
				$ac = 'badge--audience-other';
				if ( 'Beginners' === $audience ) { $ac = 'badge--audience-beginners'; }
				elseif ( 'Advanced' === $audience ) { $ac = 'badge--audience-advanced'; }
				elseif ( 'All Levels' === $audience ) { $ac = 'badge--audience-all'; }
				?>
				<span class="audience-pill event-mini-badge <?php echo esc_attr( $ac ); ?>"><?php echo esc_html( $audience ); ?></span>
			<?php endif; ?>
			<?php if ( $attendee_profiles ) : ?>
				<p class="attendee-profiles-text"><?php echo esc_html( $attendee_profiles ); ?></p>
			<?php endif; ?>
		</section>
		<?php endif; ?>

		<?php if ( ! empty( $event_languages ) ) : ?>
		<section class="event-rich-section">
			<h2><?php esc_html_e( 'What You Will Learn', 'techstack' ); ?></h2>
			<div class="lang-tags-grid">
				<?php foreach ( $event_languages as $lang ) :
					$lc    = isset( $lang_colors[ $lang ] ) ? $lang_colors[ $lang ] : array( 'bg' => '#1E2035', 'color' => '#7B8DB0' );
					$style = 'background:' . esc_attr( $lc['bg'] ) . ';color:' . esc_attr( $lc['color'] ) . ';';
				?>
					<span class="lang-tag-large" style="<?php echo $style; ?>"><?php echo esc_html( $lang ); ?></span>
				<?php endforeach; ?>
			</div>
		</section>
		<?php endif; ?>

		<section class="event-rich-section">
			<h2><?php esc_html_e( 'Event Format', 'techstack' ); ?></h2>
			<div class="event-format-grid">
				<div class="format-card">
					<span class="format-card__icon">📍</span>
					<span class="format-card__label"><?php esc_html_e( 'Format', 'techstack' ); ?></span>
					<span class="format-card__value"><?php echo esc_html( $is_hybrid ? __( 'Hybrid', 'techstack' ) : ( $is_online ? __( 'Online', 'techstack' ) : __( 'In person', 'techstack' ) ) ); ?></span>
				</div>
				<div class="format-card">
					<span class="format-card__icon">🤝</span>
					<span class="format-card__label"><?php esc_html_e( 'Networking', 'techstack' ); ?></span>
					<span class="format-card__value"><?php echo esc_html( $is_networking ? __( 'Yes', 'techstack' ) : __( 'No', 'techstack' ) ); ?></span>
				</div>
				<div class="format-card">
					<span class="format-card__icon">📺</span>
					<span class="format-card__label"><?php esc_html_e( 'Live Stream', 'techstack' ); ?></span>
					<span class="format-card__value"><?php echo esc_html( $is_live_stream ? __( 'Yes', 'techstack' ) : __( 'No', 'techstack' ) ); ?></span>
				</div>
				<div class="format-card">
					<span class="format-card__icon">🎓</span>
					<span class="format-card__label"><?php esc_html_e( 'Certificate', 'techstack' ); ?></span>
					<span class="format-card__value"><?php echo esc_html( $has_certificate ? ( $cert_type ?: __( 'Yes', 'techstack' ) ) : __( 'No', 'techstack' ) ); ?></span>
				</div>
				<div class="format-card">
					<span class="format-card__icon">🔧</span>
					<span class="format-card__label"><?php esc_html_e( 'Workshops', 'techstack' ); ?></span>
					<span class="format-card__value"><?php echo $workshops_count ? esc_html( $workshops_count ) : esc_html__( 'N/A', 'techstack' ); ?></span>
				</div>
				<div class="format-card">
					<span class="format-card__icon">🎤</span>
					<span class="format-card__label"><?php esc_html_e( 'Speakers', 'techstack' ); ?></span>
					<span class="format-card__value"><?php echo $speakers_count ? esc_html( number_format_i18n( (int) $speakers_count ) ) : esc_html__( 'N/A', 'techstack' ); ?></span>
				</div>
			</div>
		</section>

		<?php if ( $is_scholarships ) : ?>
		<section class="event-rich-section">
			<div class="scholarship-card">
				<h2><?php esc_html_e( 'Scholarship & Accessibility', 'techstack' ); ?></h2>
				<p><?php esc_html_e( 'Diversity tickets and scholarships may be available for this event. Check the organiser\'s website for eligibility and application details.', 'techstack' ); ?></p>
				<?php if ( $scholarship_url ) : ?>
					<a class="button button-primary" href="<?php echo esc_url( $scholarship_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Learn More', 'techstack' ); ?></a>
				<?php endif; ?>
			</div>
		</section>
		<?php endif; ?>
	</div>
	<aside class="single-event__sidebar">
		<div class="sticky-card">
			<?php if ( $has_certificate || $is_remote ) : ?>
			<div class="sidebar-badges">
				<?php if ( $has_certificate ) : ?>
					<span class="sidebar-badge sidebar-badge--certificate">🎓 <?php echo esc_html( $cert_type ?: __( 'Certificate', 'techstack' ) ); ?></span>
				<?php endif; ?>
				<?php if ( $is_remote ) : ?>
					<span class="sidebar-badge sidebar-badge--remote">🌐 <?php esc_html_e( 'Remote Friendly', 'techstack' ); ?></span>
				<?php endif; ?>
			</div>
			<?php endif; ?>
			<div class="price-large"><?php echo esc_html( $price ); ?></div>
			<a class="button button-primary button-wide <?php echo $deadline_soon ? 'is-deadline-soon' : ''; ?>" href="<?php echo esc_url( $registration_url ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Register Now', 'techstack' ); ?></a>
			<?php if ( null !== $days_left ) : ?>
				<div class="countdown <?php echo esc_attr( techstack_type_class( $type ) ); ?>" style="--deadline-progress: <?php echo esc_attr( $deadline_progress ); ?>%;">
					<span><?php printf( esc_html__( 'Registration deadline: %1$s · %2$d days remaining', 'techstack' ), esc_html( techstack_date_for_display( $deadline ) ), esc_html( $days_left ) ); ?></span>
					<i></i>
				</div>
			<?php endif; ?>
			<div class="detail-card">
				<h2><?php esc_html_e( 'Event Details', 'techstack' ); ?></h2>
				<p><strong><?php esc_html_e( 'Date', 'techstack' ); ?></strong><?php echo esc_html( techstack_date_range( $post_id ) ); ?></p>
				<p><strong><?php esc_html_e( 'Venue', 'techstack' ); ?></strong><?php echo esc_html( techstack_get_field( 'event_location_venue', $post_id ) ?: __( 'Online', 'techstack' ) ); ?></p>
				<p><strong><?php esc_html_e( 'Format', 'techstack' ); ?></strong><?php echo esc_html( techstack_bool_field( 'event_is_hybrid', $post_id ) ? __( 'Hybrid', 'techstack' ) : ( techstack_bool_field( 'event_is_online', $post_id ) ? __( 'Online', 'techstack' ) : __( 'In person', 'techstack' ) ) ); ?></p>
			</div>
			<div class="share-row">
				<a href="https://twitter.com/intent/tweet?url=<?php echo rawurlencode( get_permalink() ); ?>&text=<?php echo rawurlencode( $title ); ?>" target="_blank" rel="noopener">X</a>
				<a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo rawurlencode( get_permalink() ); ?>" target="_blank" rel="noopener">in</a>
				<button type="button" data-copy-link="<?php echo esc_url( get_permalink() ); ?>"><?php esc_html_e( 'Copy', 'techstack' ); ?></button>
			</div>
			<button class="button button-ghost button-wide" type="button" data-ics='<?php echo esc_attr( wp_json_encode( techstack_ics_payload( $post_id ) ) ); ?>'><?php esc_html_e( 'Add to Calendar', 'techstack' ); ?></button>
		</div>
		<section class="similar-events">
			<h2><?php esc_html_e( 'Similar Events', 'techstack' ); ?></h2>
			<?php
			$similar = new WP_Query(
				array(
					'post_type'      => 'tech_event',
					'post_status'    => 'publish',
					'posts_per_page' => 3,
					'post__not_in'   => array( $post_id ),
					'meta_query'     => array( array( 'key' => 'event_type', 'value' => $type ) ),
				)
			);
			while ( $similar->have_posts() ) :
				$similar->the_post();
				echo techstack_event_card( get_the_ID(), 'mini' );
			endwhile;
			wp_reset_postdata();
			?>
		</section>
	</aside>
</article>
<?php
get_footer();
