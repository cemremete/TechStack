<?php
/**
 * Front page template.
 *
 * @package TechStack
 */

get_header();
$event_count_obj = wp_count_posts( 'tech_event' );
$event_count     = isset( $event_count_obj->publish ) ? (int) $event_count_obj->publish : 0;
$event_query     = new WP_Query(
	array(
		'post_type'      => 'tech_event',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);
$cities    = array();
$countries = array();
if ( $event_query->have_posts() ) {
	foreach ( $event_query->posts as $event_id ) {
		$city    = trim( (string) techstack_get_field( 'event_location_city', $event_id ) );
		$country = trim( (string) techstack_get_field( 'event_location_country', $event_id ) );
		if ( $city ) {
			$cities[ strtolower( $city ) ] = $city;
		}
		if ( $country ) {
			$countries[ strtolower( $country ) ] = $country;
		}
	}
}
$hero_events_query = new WP_Query(
	array(
		'post_type'      => 'tech_event',
		'post_status'    => 'publish',
		'posts_per_page' => 8,
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);
$hero_events = array();
while ( $hero_events_query->have_posts() ) {
	$hero_events_query->the_post();
	$hero_event_id   = get_the_ID();
	$hero_event_city = techstack_get_field( 'event_location_city', $hero_event_id );
	$hero_events[]   = array(
		'title'            => html_entity_decode( get_the_title(), ENT_QUOTES, get_bloginfo( 'charset' ) ),
		'city'             => $hero_event_city,
		'country'          => techstack_get_field( 'event_location_country', $hero_event_id ),
		'date'             => techstack_date_for_display( techstack_get_field( 'event_date_start', $hero_event_id ), 'M d' ),
		'topics'           => array_values( array_slice( techstack_topics( $hero_event_id ), 0, 3 ) ),
		'type'             => techstack_get_field( 'event_type', $hero_event_id ) ?: __( 'Event', 'techstack' ),
		'registration_url' => techstack_get_field( 'event_registration_url', $hero_event_id ) ?: get_permalink( $hero_event_id ),
	);
}
wp_reset_postdata();
$hero_front = $hero_events[0] ?? array(
	'title'            => __( 'Explore TechStack Events', 'techstack' ),
	'city'             => __( 'Europe', 'techstack' ),
	'country'          => '',
	'date'             => '',
	'topics'           => array(),
	'type'             => __( 'Event', 'techstack' ),
	'registration_url' => get_post_type_archive_link( 'tech_event' ),
);
$hero_back = $hero_events[1] ?? $hero_front;
$topics = array( 'AI/ML', 'Web Dev', 'DevOps', 'Distributed Systems', 'Cybersecurity', 'Cloud', 'Data Science', 'Open Source' );
$topic_icons = array(
	'AI/ML'               => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9.5 2a2.5 2.5 0 0 1 0 5H9a7 7 0 0 0-7 7 2 2 0 0 0 2 2 2 2 0 0 0 2-2 3 3 0 0 1 3-3h.5a2.5 2.5 0 0 1 0 5 2.5 2.5 0 0 1 0 5H8a2 2 0 0 0-2 2"/><path d="M14.5 2a2.5 2.5 0 0 0 0 5H15a7 7 0 0 1 7 7 2 2 0 0 1-2 2 2 2 0 0 1-2-2 3 3 0 0 0-3-3h-.5a2.5 2.5 0 0 0 0 5 2.5 2.5 0 0 0 0 5H16a2 2 0 0 1 2 2"/></svg>',
	'Web Dev'             => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="3" width="20" height="18" rx="2"/><path d="M2 8h20"/><circle cx="6" cy="5.5" r="0.8" fill="currentColor"/><circle cx="9" cy="5.5" r="0.8" fill="currentColor"/><circle cx="12" cy="5.5" r="0.8" fill="currentColor"/><path d="M6 13h4M6 17h8"/></svg>',
	'DevOps'              => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2a10 10 0 1 0 10 10"/><path d="M22 2v8h-8"/><path d="M7 12h10"/><path d="M15 8l4 4-4 4"/></svg>',
	'Distributed Systems' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="2"/><circle cx="4" cy="6" r="2"/><circle cx="20" cy="6" r="2"/><circle cx="4" cy="18" r="2"/><circle cx="20" cy="18" r="2"/><path d="M6 6l4 4.5M18 6l-4 4.5M6 18l4-4.5M18 18l-4-4.5"/></svg>',
	'Cybersecurity'       => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2L3 7v6c0 5 4 9.3 9 10.3C17 22.3 21 18 21 13V7l-9-5z"/><rect x="9" y="11" width="6" height="5" rx="1"/><circle cx="12" cy="10" r="1.5"/><path d="M12 10v1"/></svg>',
	'Cloud'               => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9z"/><path d="M12 13v4M10 15h4"/></svg>',
	'Data Science'        => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 3v18h18"/><rect x="7" y="10" width="3" height="8" rx="0.5"/><rect x="12" y="6" width="3" height="12" rx="0.5"/><rect x="17" y="13" width="3" height="5" rx="0.5"/><path d="M5 8l4-3 4 4 4-5"/></svg>',
	'Open Source'         => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="6" cy="18" r="2"/><circle cx="6" cy="6" r="2"/><circle cx="18" cy="6" r="2"/><path d="M6 8v8M8 6h6a4 4 0 0 1 4 4v0"/></svg>',
);
$topic_icon_colors = array(
	'AI/ML'               => '108,71,255',
	'Web Dev'             => '0,212,255',
	'DevOps'              => '255,149,0',
	'Distributed Systems' => '0,200,150',
	'Cybersecurity'       => '255,77,109',
	'Cloud'               => '88,166,255',
	'Data Science'        => '245,166,35',
	'Open Source'         => '167,139,250',
);
?>
<section class="hero">
	<div class="orb orb-purple"></div>
	<div class="orb orb-purple orb-purple--soft"></div>
	<div class="orb orb-cyan"></div>
	<div class="hero__inner">
		<div class="hero__content">
			<p class="eyebrow"><?php esc_html_e( 'Updated European event intelligence', 'techstack' ); ?></p>
			<h1><?php esc_html_e( 'Discover Tech Events Across Europe', 'techstack' ); ?></h1>
			<p><?php esc_html_e( 'Conferences, hackathons, meetups and workshops - all in one place.', 'techstack' ); ?></p>
			<div class="hero__actions">
				<a class="button button-primary" href="<?php echo esc_url( get_post_type_archive_link( 'tech_event' ) ); ?>"><?php esc_html_e( 'Browse Events', 'techstack' ); ?></a>
				<a class="button button-ghost" href="<?php echo esc_url( home_url( '/submit/' ) ); ?>"><?php esc_html_e( 'Submit an Event', 'techstack' ); ?></a>
			</div>
			<div class="stats-bar">
				<a href="<?php echo esc_url( get_post_type_archive_link( 'tech_event' ) ); ?>"><i aria-hidden="true"></i><strong><?php echo esc_html( $event_count ); ?></strong><?php esc_html_e( 'Events', 'techstack' ); ?></a>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'tech_event' ) ); ?>"><i aria-hidden="true"></i><strong><?php echo esc_html( count( $cities ) ); ?></strong><?php esc_html_e( 'Cities', 'techstack' ); ?></a>
				<a href="<?php echo esc_url( get_post_type_archive_link( 'tech_event' ) ); ?>"><i aria-hidden="true"></i><strong><?php echo esc_html( count( $countries ) ); ?></strong><?php esc_html_e( 'Countries', 'techstack' ); ?></a>
			</div>
		</div>
		<div class="hero__visual">
			<div class="event-mock event-mock--back" data-hero-back-card>
				<div class="event-mock__header" data-hero-back-type><?php echo esc_html( $hero_back['type'] ); ?></div>
				<strong data-hero-back-title><?php echo esc_html( $hero_back['title'] ); ?></strong>
				<span data-hero-back-meta><?php echo esc_html( trim( $hero_back['date'] . ( $hero_back['city'] ? ' · ' . $hero_back['city'] : '' ), ' ·' ) ); ?></span>
				<div class="event-mock__footer"><b data-hero-back-topic><?php echo esc_html( $hero_back['topics'][0] ?? $hero_back['type'] ); ?></b><i></i></div>
			</div>
			<div class="event-mock event-mock--front" data-hero-front-card>
				<div class="event-mock__header" data-hero-type><?php echo esc_html( $hero_front['type'] ); ?></div>
				<strong data-hero-title><?php echo esc_html( $hero_front['title'] ); ?></strong>
				<span data-hero-meta><?php echo esc_html( trim( $hero_front['date'] . ( $hero_front['city'] ? ' · ' . $hero_front['city'] : '' ), ' ·' ) ); ?></span>
				<div class="event-mock__score" data-hero-topics>
					<?php foreach ( $hero_front['topics'] as $hero_topic ) : ?>
						<b><?php echo esc_html( $hero_topic ); ?></b>
					<?php endforeach; ?>
				</div>
				<div class="event-mock__footer">
					<b data-hero-footer-topic><?php echo esc_html( $hero_front['topics'][0] ?? $hero_front['type'] ); ?></b>
					<a href="<?php echo esc_url( $hero_front['registration_url'] ); ?>" data-hero-register><?php esc_html_e( 'Register', 'techstack' ); ?></a>
				</div>
			</div>
			<div class="event-mock__dots" data-hero-dots>
				<?php foreach ( $hero_events as $index => $hero_event ) : ?>
					<button type="button" class="<?php echo 0 === $index ? 'is-active' : ''; ?>" data-hero-dot="<?php echo esc_attr( $index ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Show event %d', 'techstack' ), $index + 1 ) ); ?>"></button>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
<script>
const tsEvents = <?php echo wp_json_encode( $hero_events, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT ); ?>;
window.tsEvents = tsEvents;
</script>

<section class="section this-week-section">
	<div class="section-heading section-heading--row">
		<div>
			<p class="eyebrow"><?php esc_html_e( 'This week', 'techstack' ); ?></p>
			<h2><?php esc_html_e( 'Happening in the next 7 days', 'techstack' ); ?></h2>
		</div>
	</div>
	<div class="week-strip">
		<?php
		$this_week = new WP_Query(
			array(
				'post_type'      => 'tech_event',
				'post_status'    => 'publish',
				'posts_per_page' => 8,
				'meta_key'       => 'event_date_start',
				'orderby'        => 'meta_value_num',
				'order'          => 'ASC',
				'meta_query'     => array(
					array(
						'key'     => 'event_date_start',
						'value'   => array( current_time( 'Ymd' ), gmdate( 'Ymd', current_time( 'timestamp' ) + ( 7 * DAY_IN_SECONDS ) ) ),
						'compare' => 'BETWEEN',
						'type'    => 'NUMERIC',
					),
				),
			)
		);
		if ( $this_week->have_posts() ) :
			while ( $this_week->have_posts() ) :
				$this_week->the_post();
				$day_label = techstack_date_for_display( techstack_get_field( 'event_date_start', get_the_ID() ), 'D j' );
				?>
				<a class="week-card <?php echo esc_attr( techstack_type_class( techstack_get_field( 'event_type', get_the_ID() ) ?: 'Conference' ) ); ?>" href="<?php the_permalink(); ?>">
					<span><?php echo esc_html( $day_label ); ?></span>
					<strong><?php echo esc_html( techstack_get_field( 'event_name', get_the_ID() ) ?: get_the_title() ); ?></strong>
					<small><?php echo esc_html( techstack_get_field( 'event_location_city', get_the_ID() ) ?: __( 'Online', 'techstack' ) ); ?></small>
				</a>
				<?php
			endwhile;
			wp_reset_postdata();
		else :
			?>
			<p class="empty-state"><?php esc_html_e( 'No events are scheduled in the next week yet.', 'techstack' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<section class="section featured-section">
	<div class="section-heading">
		<p class="eyebrow"><?php esc_html_e( 'Featured', 'techstack' ); ?></p>
		<h2><?php esc_html_e( 'Featured Events', 'techstack' ); ?></h2>
	</div>
	<div class="featured-grid">
		<?php
		$featured = new WP_Query(
			array(
				'post_type'      => 'tech_event',
				'post_status'    => 'publish',
				'posts_per_page' => 3,
				'meta_key'       => 'event_featured',
				'meta_value'     => '1',
				'orderby'        => 'meta_value_num',
				'order'          => 'DESC',
			)
		);
		while ( $featured->have_posts() ) :
			$featured->the_post();
			echo techstack_event_card( get_the_ID(), 'featured' );
		endwhile;
		wp_reset_postdata();
		?>
	</div>
</section>

<section class="section upcoming-section">
	<div class="section-heading section-heading--row">
		<div>
			<p class="eyebrow"><?php esc_html_e( 'Next up', 'techstack' ); ?></p>
			<h2><?php esc_html_e( 'Upcoming Events', 'techstack' ); ?></h2>
		</div>
		<a class="text-link" href="<?php echo esc_url( get_post_type_archive_link( 'tech_event' ) ); ?>"><?php esc_html_e( 'View All Events', 'techstack' ); ?> &rarr;</a>
	</div>

	<div class="home-filter event-filter" data-local-filter>
		<div class="pill-group" role="group" aria-label="<?php esc_attr_e( 'Event type filters', 'techstack' ); ?>">
			<button class="pill is-active" type="button" data-type="All"><?php esc_html_e( 'All', 'techstack' ); ?></button>
			<?php foreach ( array( 'Conference', 'Hackathon', 'Meetup', 'Workshop' ) as $type ) : ?>
				<button class="pill" type="button" data-type="<?php echo esc_attr( $type ); ?>"><?php echo esc_html( $type ); ?></button>
			<?php endforeach; ?>
		</div>
		<select data-city aria-label="<?php esc_attr_e( 'Filter by city', 'techstack' ); ?>">
			<option value=""><?php esc_html_e( 'All cities', 'techstack' ); ?></option>
			<?php foreach ( array( 'Berlin', 'Amsterdam', 'London', 'Paris', 'Barcelona', 'Lisbon', 'Vienna', 'Prague', 'Warsaw', 'Istanbul', 'Rome', 'Stockholm', 'Copenhagen', 'Zurich', 'Dublin' ) as $city ) : ?>
				<option value="<?php echo esc_attr( $city ); ?>"><?php echo esc_html( $city ); ?></option>
			<?php endforeach; ?>
		</select>
		<select data-topic aria-label="<?php esc_attr_e( 'Filter by topic', 'techstack' ); ?>">
			<option value=""><?php esc_html_e( 'All topics', 'techstack' ); ?></option>
			<?php foreach ( array( 'AI/ML', 'Web Dev', 'DevOps', 'Distributed Systems', 'Cybersecurity', 'Blockchain', 'Data Science', 'Mobile', 'Cloud', 'Open Source', 'Startup', 'Design' ) as $topic ) : ?>
				<option value="<?php echo esc_attr( $topic ); ?>"><?php echo esc_html( $topic ); ?></option>
			<?php endforeach; ?>
		</select>
	</div>

	<div class="events-grid" data-card-grid>
		<?php
		$upcoming = new WP_Query( techstack_event_query_args( array(), 1, 8 ) );
		while ( $upcoming->have_posts() ) :
			$upcoming->the_post();
			echo techstack_event_card( get_the_ID(), 'compact' );
		endwhile;
		wp_reset_postdata();
		?>
	</div>
</section>

<section class="section topic-section">
	<div class="section-heading">
		<p class="eyebrow"><?php esc_html_e( 'Browse by topic', 'techstack' ); ?></p>
		<h2><?php esc_html_e( 'Find your track', 'techstack' ); ?></h2>
	</div>
	<div class="topic-pills">
		<?php foreach ( $topics as $topic ) : ?>
			<a class="topic-pill ts-topic-card" href="<?php echo esc_url( add_query_arg( 'topics[]', $topic, get_post_type_archive_link( 'tech_event' ) ) ); ?>">
				<span class="ts-topic-icon" style="--topic-rgb: <?php echo esc_attr( $topic_icon_colors[ $topic ] ?? '108,71,255' ); ?>;" aria-hidden="true"><?php echo $topic_icons[ $topic ] ?? ''; ?></span>
				<strong><?php echo esc_html( $topic ); ?></strong>
				<span><?php echo esc_html( techstack_count_by_meta( 'event_topics', $topic ) ); ?></span>
			</a>
		<?php endforeach; ?>
	</div>
</section>
<?php
get_footer();
