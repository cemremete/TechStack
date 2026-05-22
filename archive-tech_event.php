<?php
/**
 * Tech event archive.
 *
 * @package TechStack
 */

get_header();

// Page mode detection.
$page_mode = 'all';
if ( isset( $_GET['cfp'] ) && 'open' === $_GET['cfp'] ) {
	$page_mode = 'cfp';
}
if ( isset( $_GET['type'] ) ) {
	$page_mode = sanitize_text_field( wp_unslash( $_GET['type'] ) );
}

$paged           = max( 1, get_query_var( 'paged' ) ? get_query_var( 'paged' ) : (int) ( $_GET['page'] ?? 1 ) );
$archive_request = $_GET;
$archive_request['include_past'] = '1';

// Normalise pre-filters so the PHP query matches the page mode.
if ( 'cfp' === $page_mode ) {
	$archive_request['cfp'] = 'true';
}
if ( 'hackathon' === strtolower( $page_mode ) ) {
	$archive_request['type'] = 'Hackathon';
}

$events          = new WP_Query( techstack_event_query_args( $archive_request, $paged, 12 ) );
$total_published = techstack_count_events();
$active_type     = isset( $_GET['type'] ) ? sanitize_text_field( wp_unslash( $_GET['type'] ) ) : '';
$today_value     = current_time( 'Ymd' );
$month_start     = current_time( 'Ym' ) . '01';
$month_end       = gmdate( 'Ymt', current_time( 'timestamp' ) );
$week_end        = gmdate( 'Ymd', strtotime( '+7 days', current_time( 'timestamp' ) ) );
$shown_count     = min( $events->found_posts, 12 );
$found_count     = $events->found_posts ?: $total_published;
$shown_percent   = $found_count ? min( 100, round( ( $shown_count / $found_count ) * 100 ) ) : 0;

$month_events = new WP_Query(
	array(
		'post_type'      => 'tech_event',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'meta_query'     => array(
			array( 'key' => 'event_date_start', 'value' => array( $month_start, $month_end ), 'compare' => 'BETWEEN', 'type' => 'NUMERIC' ),
		),
	)
);
$cities_this_month = techstack_count_unique_event_meta( 'event_location_city', $month_start, $month_end );
$next_event        = new WP_Query(
	array(
		'post_type'      => 'tech_event',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'meta_key'       => 'event_date_start',
		'orderby'        => 'meta_value_num',
		'order'          => 'ASC',
		'meta_query'     => array(
			array( 'key' => 'event_date_start', 'value' => $today_value, 'compare' => '>=', 'type' => 'NUMERIC' ),
		),
	)
);
$next_event_title = __( 'No upcoming event', 'techstack' );
$next_event_days  = '';
if ( $next_event->have_posts() ) {
	$next_event->the_post();
	$next_id          = get_the_ID();
	$next_event_title = wp_html_excerpt( techstack_get_field( 'event_name', $next_id ) ?: get_the_title( $next_id ), 30, '...' );
	$next_start       = techstack_date_for_display( techstack_get_field( 'event_date_start', $next_id ), 'Y-m-d' );
	$next_event_days  = $next_start ? max( 0, (int) floor( ( strtotime( $next_start ) - current_time( 'timestamp' ) ) / DAY_IN_SECONDS ) ) : '';
	wp_reset_postdata();
}

$week_events = new WP_Query(
	array(
		'post_type'      => 'tech_event',
		'post_status'    => 'publish',
		'posts_per_page' => 12,
		'meta_key'       => 'event_date_start',
		'orderby'        => 'meta_value_num',
		'order'          => 'ASC',
		'meta_query'     => array(
			array( 'key' => 'event_date_start', 'value' => array( $today_value, $week_end ), 'compare' => 'BETWEEN', 'type' => 'NUMERIC' ),
		),
	)
);
?>

<?php if ( 'cfp' === $page_mode ) : ?>
<section class="archive-hero archive-hero--cfp">
	<p class="eyebrow"><?php esc_html_e( 'SPEAK AT A CONFERENCE', 'techstack' ); ?></p>
	<h1 class="gradient-text gradient-text--cfp"><?php esc_html_e( 'Call for Papers', 'techstack' ); ?></h1>
	<p><?php esc_html_e( 'Submit your talk proposal before these deadlines close.', 'techstack' ); ?></p>
</section>
<?php elseif ( 'hackathon' === strtolower( $page_mode ) ) : ?>
<section class="archive-hero archive-hero--hackathon">
	<p class="eyebrow"><?php esc_html_e( 'BUILD · COMPETE · WIN', 'techstack' ); ?></p>
	<h1 class="gradient-text gradient-text--hackathon"><?php esc_html_e( 'Hackathons', 'techstack' ); ?></h1>
	<p><?php esc_html_e( 'Compete in the best hackathons across Europe. Build something extraordinary.', 'techstack' ); ?></p>
</section>
<?php
// Hackathon stats bar.
$hack_total  = techstack_count_by_meta( 'event_type', 'Hackathon' );
$hack_cert   = ( new WP_Query( array(
	'post_type'      => 'tech_event',
	'post_status'    => 'publish',
	'posts_per_page' => 1,
	'fields'         => 'ids',
	'meta_query'     => array(
		array( 'key' => 'event_type', 'value' => 'Hackathon' ),
		array( 'key' => 'event_certificate', 'value' => '1' ),
	),
) ) )->found_posts;
$hack_remote = ( new WP_Query( array(
	'post_type'      => 'tech_event',
	'post_status'    => 'publish',
	'posts_per_page' => 1,
	'fields'         => 'ids',
	'meta_query'     => array(
		array( 'key' => 'event_type', 'value' => 'Hackathon' ),
		array( 'key' => 'event_remote_friendly', 'value' => '1' ),
	),
) ) )->found_posts;
?>
<div class="hackathon-stats">
	<span><strong><?php echo esc_html( $hack_total ); ?></strong> <?php esc_html_e( 'hackathons listed', 'techstack' ); ?></span>
	<span><strong><?php echo esc_html( $hack_cert ); ?></strong> <?php esc_html_e( 'offer a certificate', 'techstack' ); ?></span>
	<span><strong><?php echo esc_html( $hack_remote ); ?></strong> <?php esc_html_e( 'remote-friendly', 'techstack' ); ?></span>
</div>
<?php else : ?>
<section class="archive-hero">
	<span class="archive-hero__orb archive-hero__orb--purple"></span>
	<span class="archive-hero__orb archive-hero__orb--cyan"></span>
	<div class="archive-hero__copy">
		<p class="eyebrow"><?php esc_html_e( 'BROWSE EUROPE', 'techstack' ); ?></p>
		<h1><?php esc_html_e( 'Events', 'techstack' ); ?></h1>
		<p><?php esc_html_e( 'Filter conferences, hackathons, meetups, workshops, summits and webinars across the continent.', 'techstack' ); ?></p>
	</div>
	<div class="archive-hero-stats" aria-label="<?php esc_attr_e( 'Events archive stats', 'techstack' ); ?>">
		<div>
			<strong><?php echo esc_html( number_format_i18n( (int) $month_events->found_posts ) ); ?></strong>
			<span><?php esc_html_e( 'Total events this month', 'techstack' ); ?></span>
		</div>
		<div>
			<strong><?php echo esc_html( number_format_i18n( $cities_this_month ) ); ?></strong>
			<span><?php esc_html_e( 'Cities represented', 'techstack' ); ?></span>
		</div>
		<div>
			<strong><?php echo '' !== $next_event_days ? esc_html( $next_event_days . 'd' ) : esc_html__( '--', 'techstack' ); ?></strong>
			<span><?php echo esc_html( $next_event_title ); ?></span>
		</div>
	</div>
</section>
<?php endif; ?>

<?php if ( 'all' === $page_mode && $week_events->have_posts() ) : ?>
<section class="this-week-section" aria-label="<?php esc_attr_e( 'Events this week', 'techstack' ); ?>">
	<h2><span></span><?php esc_html_e( 'This Week', 'techstack' ); ?></h2>
	<div class="this-week-strip">
		<?php
		while ( $week_events->have_posts() ) :
			$week_events->the_post();
			$week_id      = get_the_ID();
			$week_type    = techstack_get_field( 'event_type', $week_id ) ?: 'Conference';
			$week_title   = techstack_get_field( 'event_name', $week_id ) ?: get_the_title( $week_id );
			$week_city    = techstack_get_field( 'event_location_city', $week_id );
			$week_date    = techstack_date_for_display( techstack_get_field( 'event_date_start', $week_id ), 'Y-m-d' );
			$week_days    = $week_date ? max( 0, (int) floor( ( strtotime( $week_date ) - current_time( 'timestamp' ) ) / DAY_IN_SECONDS ) ) : 0;
			$week_urgency = sprintf( __( 'In %d days', 'techstack' ), $week_days );
			if ( 0 === $week_days ) {
				$week_urgency = __( 'Today', 'techstack' );
			} elseif ( 1 === $week_days ) {
				$week_urgency = __( 'Tomorrow', 'techstack' );
			}
			?>
			<a class="this-week-card <?php echo esc_attr( techstack_type_class( $week_type ) ); ?>" href="<?php the_permalink(); ?>">
				<span class="event-badge <?php echo esc_attr( techstack_type_class( $week_type ) ); ?>"><?php echo esc_html( $week_type ); ?></span>
				<strong><?php echo esc_html( $week_title ); ?></strong>
				<time datetime="<?php echo esc_attr( $week_date ); ?>"><?php echo esc_html( techstack_date_for_display( techstack_get_field( 'event_date_start', $week_id ), 'M j' ) ); ?></time>
				<small><?php echo esc_html( $week_city ?: __( 'Online', 'techstack' ) ); ?></small>
				<em class="<?php echo $week_days <= 3 ? 'is-urgent' : ''; ?>"><?php echo esc_html( $week_urgency ); ?></em>
			</a>
			<?php
		endwhile;
		wp_reset_postdata();
		?>
	</div>
</section>
<?php endif; ?>

<button class="filters-button" type="button" data-filter-open><?php esc_html_e( 'Filters', 'techstack' ); ?></button>
<div class="filter-backdrop" data-filter-backdrop hidden></div>
<section class="archive-layout">
	<aside class="filter-sidebar" data-filter-drawer>
		<form class="ajax-filter-form">
			<input type="hidden" name="include_past" value="1">
			<?php if ( 'cfp' === $page_mode ) : ?>
			<input type="hidden" name="cfp" value="true" data-permanent>
			<?php endif; ?>
			<?php if ( 'hackathon' === strtolower( $page_mode ) ) : ?>
			<input type="hidden" name="type" value="Hackathon" data-permanent>
			<?php endif; ?>
			<div class="filter-sidebar__head">
				<strong><?php esc_html_e( 'Filters', 'techstack' ); ?></strong>
				<button type="button" class="icon-button filter-close" data-filter-close aria-label="<?php esc_attr_e( 'Close filters', 'techstack' ); ?>">&times;</button>
			</div>
			<label><?php esc_html_e( 'Search', 'techstack' ); ?><input type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Keyword or organizer', 'techstack' ); ?>"></label>
			<?php if ( 'cfp' !== $page_mode && 'hackathon' !== strtolower( $page_mode ) ) : ?>
			<div class="filter-block">
				<h2><?php esc_html_e( 'Event Type', 'techstack' ); ?></h2>
				<div class="type-toggle-group">
					<?php foreach ( array( 'Conference', 'Hackathon', 'Meetup', 'Workshop', 'Summit', 'Webinar' ) as $type ) : ?>
						<label class="type-toggle ts-type-pill <?php echo esc_attr( techstack_type_class( $type ) ); ?>">
							<input type="checkbox" name="type" value="<?php echo esc_attr( $type ); ?>" <?php checked( $active_type, $type ); ?>>
							<span><?php echo esc_html( $type ); ?></span>
							<small><?php echo esc_html( techstack_count_by_meta( 'event_type', $type ) ); ?></small>
						</label>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endif; ?>
			<div class="filter-block">
				<h2><?php esc_html_e( 'Cities', 'techstack' ); ?></h2>
				<?php $selected_cities = array_map( 'sanitize_text_field', (array) wp_unslash( $_GET['city'] ?? array() ) ); ?>
				<div class="pill-filter-group" data-pill-filter="city[]">
					<?php foreach ( array( 'Berlin', 'Amsterdam', 'London', 'Paris', 'Barcelona', 'Lisbon', 'Vienna', 'Prague', 'Warsaw', 'Istanbul', 'Rome', 'Stockholm', 'Copenhagen', 'Zurich', 'Dublin' ) as $city ) : ?>
						<button type="button"
							class="filter-pill<?php echo in_array( $city, $selected_cities, true ) ? ' is-active' : ''; ?>"
							data-filter-name="city[]"
							data-filter-value="<?php echo esc_attr( $city ); ?>"
						><?php echo esc_html( $city ); ?></button>
					<?php endforeach; ?>
				</div>
				<div class="pill-filter-inputs" data-pill-inputs="city[]">
					<?php foreach ( $selected_cities as $city ) : ?>
						<input type="hidden" name="city[]" value="<?php echo esc_attr( $city ); ?>">
					<?php endforeach; ?>
				</div>
			</div>
			<div class="filter-block">
				<h2><?php esc_html_e( 'Topics', 'techstack' ); ?></h2>
				<?php $selected_topics = array_map( 'sanitize_text_field', (array) wp_unslash( $_GET['topics'] ?? array() ) ); ?>
				<div class="pill-filter-group" data-pill-filter="topics[]">
					<?php foreach ( array( 'AI/ML', 'Web Dev', 'DevOps', 'Distributed Systems', 'Cybersecurity', 'Blockchain', 'Data Science', 'Mobile', 'Cloud', 'Open Source', 'Startup', 'Design' ) as $topic ) : ?>
						<button type="button"
							class="filter-pill<?php echo in_array( $topic, $selected_topics, true ) ? ' is-active' : ''; ?>"
							data-filter-name="topics[]"
							data-filter-value="<?php echo esc_attr( $topic ); ?>"
						><?php echo esc_html( $topic ); ?></button>
					<?php endforeach; ?>
				</div>
				<div class="pill-filter-inputs" data-pill-inputs="topics[]">
					<?php foreach ( $selected_topics as $topic ) : ?>
						<input type="hidden" name="topics[]" value="<?php echo esc_attr( $topic ); ?>">
					<?php endforeach; ?>
				</div>
			</div>
			<div class="filter-row">
				<label><?php esc_html_e( 'From', 'techstack' ); ?><input type="date" name="date_from"></label>
				<label><?php esc_html_e( 'To', 'techstack' ); ?><input type="date" name="date_to"></label>
			</div>
			<div class="filter-block">
				<h2><?php esc_html_e( 'More Filters', 'techstack' ); ?></h2>
				<div class="pill-filter-group" data-toggle-pills>
					<button type="button"
						class="filter-pill<?php echo ! empty( $_GET['price_free'] ) ? ' is-active' : ''; ?>"
						data-toggle-input="price_free"
					><?php esc_html_e( 'Free only', 'techstack' ); ?></button>
					<button type="button"
						class="filter-pill<?php echo ! empty( $_GET['online_only'] ) ? ' is-active' : ''; ?>"
						data-toggle-input="online_only"
					><?php esc_html_e( 'Online only', 'techstack' ); ?></button>
				</div>
				<?php if ( ! empty( $_GET['price_free'] ) ) : ?>
					<input type="hidden" name="price_free" value="1" data-toggle-hidden="price_free">
				<?php endif; ?>
				<?php if ( ! empty( $_GET['online_only'] ) ) : ?>
					<input type="hidden" name="online_only" value="1" data-toggle-hidden="online_only">
				<?php endif; ?>
			</div>
			<button class="button button-primary" type="submit"><?php esc_html_e( 'Apply Filters', 'techstack' ); ?></button>
			<a class="reset-link" href="<?php echo esc_url( get_post_type_archive_link( 'tech_event' ) ); ?>"><?php esc_html_e( 'Reset', 'techstack' ); ?></a>
		</form>
		<div class="active-filters" data-active-filters aria-live="polite"></div>
	</aside>
	<div class="archive-content">
		<div class="archive-counter-bar" data-counter-bar>
			<div class="archive-counter-bar__top">
				<p class="result-count" data-result-count data-showing="<?php echo esc_attr( $shown_count ); ?>" data-found="<?php echo esc_attr( $found_count ); ?>">
					<?php
					printf(
						wp_kses_post( __( 'Showing <strong>%1$d</strong> of %2$d events', 'techstack' ) ),
						(int) $shown_count,
						(int) $found_count
					);
					?>
				</p>
			<label><?php esc_html_e( 'Sort', 'techstack' ); ?>
				<select name="sort" form="none" data-sort-select>
					<option value="soonest"><?php esc_html_e( 'Soonest first', 'techstack' ); ?></option>
					<option value="latest"><?php esc_html_e( 'Latest added', 'techstack' ); ?></option>
					<option value="popular"><?php esc_html_e( 'Most popular', 'techstack' ); ?></option>
					<option value="alpha"><?php esc_html_e( 'Alphabetical', 'techstack' ); ?></option>
				</select>
			</label>
			</div>
			<div class="archive-counter-progress" aria-hidden="true"><span data-result-progress style="width: <?php echo esc_attr( $shown_percent ); ?>%;"></span></div>
		</div>
		<div class="events-grid archive-grid" data-ajax-results>
			<?php
			if ( 'cfp' === $page_mode ) {
				$card_context = 'cfp';
			} elseif ( 'hackathon' === strtolower( $page_mode ) ) {
				$card_context = 'hackathon';
			} else {
				$card_context = '';
			}
			while ( $events->have_posts() ) :
				$events->the_post();
				echo techstack_event_card( get_the_ID(), 'compact', $card_context );
			endwhile;
			wp_reset_postdata();
			?>
		</div>
		<div class="pagination">
			<?php
			echo paginate_links(
				array(
					'total'   => $events->max_num_pages,
					'current' => $paged,
				)
			);
			?>
		</div>
	</div>
</section>
<script>
const tsPageMode = '<?php echo esc_js( $page_mode ); ?>';
const tsPreFilter = <?php
	if ( 'hackathon' === strtolower( $page_mode ) ) {
		echo '{"type":"Hackathon"}';
	} elseif ( 'cfp' === $page_mode ) {
		echo '{"cfp":true}';
	} else {
		echo '{}';
	}
?>;
</script>
<?php
get_footer();
