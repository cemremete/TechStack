<?php
/**
 * Calendar page.
 *
 * @package TechStack
 */

get_header();
?>
<section class="calendar-shell" data-calendar>
	<div class="calendar-head">
		<div>
			<p class="eyebrow"><?php esc_html_e( 'Plan ahead', 'techstack' ); ?></p>
			<h1><?php esc_html_e( 'Calendar', 'techstack' ); ?></h1>
		</div>
		<div class="calendar-actions">
			<button class="icon-button" type="button" data-cal-prev aria-label="<?php esc_attr_e( 'Previous month', 'techstack' ); ?>">&lt;</button>
			<strong data-cal-title></strong>
			<button class="icon-button" type="button" data-cal-next aria-label="<?php esc_attr_e( 'Next month', 'techstack' ); ?>">&gt;</button>
			<button class="button button-ghost calendar-today-button" type="button" data-cal-today><?php esc_html_e( 'Jump to Today', 'techstack' ); ?></button>
			<div class="segmented">
				<button class="is-active" type="button" data-view="month"><?php esc_html_e( 'Month', 'techstack' ); ?></button>
				<button type="button" data-view="list"><?php esc_html_e( 'List', 'techstack' ); ?></button>
			</div>
		</div>
	</div>
	<div class="legend">
		<?php foreach ( array( 'Conference', 'Hackathon', 'Meetup', 'Workshop', 'Summit', 'Webinar' ) as $type ) : ?>
			<span class="<?php echo esc_attr( techstack_type_class( $type ) ); ?>"><i></i><?php echo esc_html( $type ); ?></span>
		<?php endforeach; ?>
	</div>
	<div class="calendar-stats" data-calendar-stats></div>
	<div class="calendar-layout">
		<div class="calendar-main">
			<div class="calendar-grid" data-calendar-grid></div>
			<div class="calendar-list" data-calendar-list hidden></div>
		</div>
		<aside class="calendar-sidebar" aria-label="<?php esc_attr_e( 'Upcoming events this month', 'techstack' ); ?>">
			<h2><?php esc_html_e( 'This month', 'techstack' ); ?></h2>
			<div class="calendar-upcoming" data-calendar-upcoming></div>
		</aside>
	</div>
	<div class="calendar-popover" data-calendar-popover role="tooltip" hidden></div>
</section>
<?php
get_footer();
