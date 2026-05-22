<?php
/**
 * Header template.
 *
 * @package TechStack
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="site-header" data-search-nav>
	<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'TechStack home', 'techstack' ); ?>">
		<span class="logo-mark" aria-hidden="true"><i></i><i></i><i></i></span>
		<span>TechStack</span>
	</a>
	<button class="nav-toggle" type="button" aria-label="<?php esc_attr_e( 'Open menu', 'techstack' ); ?>" aria-expanded="false"><span></span><span></span><span></span></button>
	<nav class="primary-nav" aria-label="<?php esc_attr_e( 'Primary navigation', 'techstack' ); ?>">
		<a href="<?php echo esc_url( get_post_type_archive_link( 'tech_event' ) ); ?>"><?php esc_html_e( 'Events', 'techstack' ); ?> <span class="count-badge"><?php echo esc_html( techstack_count_events() ); ?></span></a>
		<a href="<?php echo esc_url( home_url( '/calendar/' ) ); ?>"><?php esc_html_e( 'Calendar', 'techstack' ); ?></a>
		<a href="<?php echo esc_url( add_query_arg( 'type', 'Hackathon', get_post_type_archive_link( 'tech_event' ) ) ); ?>"><?php esc_html_e( 'Hackathons', 'techstack' ); ?></a>
		<a href="<?php echo esc_url( add_query_arg( 'cfp', 'open', get_post_type_archive_link( 'tech_event' ) ) ); ?>"><?php esc_html_e( 'CFP Open', 'techstack' ); ?></a>
		<a href="<?php echo esc_url( home_url( '/submit/' ) ); ?>"><?php esc_html_e( 'Submit Event', 'techstack' ); ?></a>
	</nav>
	<form class="header-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<input type="hidden" name="post_type" value="tech_event">
		<input type="search" name="s" placeholder="<?php esc_attr_e( 'Search events', 'techstack' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>">
		<button class="search-toggle" type="button" aria-label="<?php esc_attr_e( 'Search events', 'techstack' ); ?>" aria-expanded="false"><?php echo techstack_icon( 'search' ); ?></button>
	</form>
</header>
<main id="content" class="site-main">
