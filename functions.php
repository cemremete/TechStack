<?php
/**
 * TechStack theme functions.
 *
 * @package TechStack
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TECHSTACK_VERSION', '1.0.3' );

function techstack_asset_version() {
	return TECHSTACK_VERSION;
}

function techstack_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'techstack' ),
			'footer'  => __( 'Footer Menu', 'techstack' ),
		)
	);
}
add_action( 'after_setup_theme', 'techstack_setup' );

function techstack_register_event_content() {
	$labels = array(
		'name'               => __( 'Tech Events', 'techstack' ),
		'singular_name'      => __( 'Tech Event', 'techstack' ),
		'add_new_item'       => __( 'Add New Tech Event', 'techstack' ),
		'edit_item'          => __( 'Edit Tech Event', 'techstack' ),
		'new_item'           => __( 'New Tech Event', 'techstack' ),
		'view_item'          => __( 'View Tech Event', 'techstack' ),
		'search_items'       => __( 'Search Tech Events', 'techstack' ),
		'not_found'          => __( 'No tech events found', 'techstack' ),
		'not_found_in_trash' => __( 'No tech events found in Trash', 'techstack' ),
	);

	register_post_type(
		'tech_event',
		array(
			'labels'       => $labels,
			'public'       => true,
			'has_archive'  => true,
			'menu_icon'    => 'dashicons-tickets-alt',
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => 'events' ),
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
		)
	);

	register_taxonomy(
		'event_category',
		'tech_event',
		array(
			'labels'       => array(
				'name'          => __( 'Event Categories', 'techstack' ),
				'singular_name' => __( 'Event Category', 'techstack' ),
			),
			'public'       => true,
			'hierarchical' => true,
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => 'event-category' ),
		)
	);

	register_taxonomy(
		'event_location',
		'tech_event',
		array(
			'labels'       => array(
				'name'          => __( 'Event Locations', 'techstack' ),
				'singular_name' => __( 'Event Location', 'techstack' ),
			),
			'public'       => true,
			'hierarchical' => true,
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => 'event-location' ),
		)
	);

	$categories = array( 'Conference', 'Hackathon', 'Meetup', 'Workshop', 'Summit', 'Webinar' );
	foreach ( $categories as $category ) {
		if ( ! term_exists( $category, 'event_category' ) ) {
			wp_insert_term( $category, 'event_category' );
		}
	}

	$cities = array( 'Berlin', 'Amsterdam', 'London', 'Paris', 'Barcelona', 'Lisbon', 'Vienna', 'Prague', 'Warsaw', 'Istanbul', 'Rome', 'Stockholm', 'Copenhagen', 'Zurich', 'Dublin' );
	foreach ( $cities as $city ) {
		if ( ! term_exists( $city, 'event_location' ) ) {
			wp_insert_term( $city, 'event_location' );
		}
	}
}
add_action( 'init', 'techstack_register_event_content' );

function techstack_acf_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$topic_choices = array(
		'AI/ML'               => 'AI/ML',
		'Web Dev'             => 'Web Dev',
		'DevOps'              => 'DevOps',
		'Distributed Systems' => 'Distributed Systems',
		'Cybersecurity'       => 'Cybersecurity',
		'Blockchain'          => 'Blockchain',
		'Data Science'        => 'Data Science',
		'Mobile'              => 'Mobile',
		'Cloud'               => 'Cloud',
		'Open Source'         => 'Open Source',
		'Startup'             => 'Startup',
		'Design'              => 'Design',
	);

	acf_add_local_field_group(
		array(
			'key'      => 'group_techstack_event_fields',
			'title'    => 'TechStack Event Details',
			'fields'   => array(
				array( 'key' => 'field_ts_event_name', 'label' => 'Event Name', 'name' => 'event_name', 'type' => 'text' ),
				array(
					'key'     => 'field_ts_event_type',
					'label'   => 'Event Type',
					'name'    => 'event_type',
					'type'    => 'select',
					'choices' => array( 'Conference' => 'Conference', 'Hackathon' => 'Hackathon', 'Meetup' => 'Meetup', 'Workshop' => 'Workshop', 'Summit' => 'Summit', 'Webinar' => 'Webinar' ),
				),
				array( 'key' => 'field_ts_event_date_start', 'label' => 'Start Date', 'name' => 'event_date_start', 'type' => 'date_picker', 'display_format' => 'Y-m-d', 'return_format' => 'Y-m-d' ),
				array( 'key' => 'field_ts_event_date_end', 'label' => 'End Date', 'name' => 'event_date_end', 'type' => 'date_picker', 'display_format' => 'Y-m-d', 'return_format' => 'Y-m-d' ),
				array( 'key' => 'field_ts_event_location_city', 'label' => 'City', 'name' => 'event_location_city', 'type' => 'text' ),
				array( 'key' => 'field_ts_event_location_country', 'label' => 'Country', 'name' => 'event_location_country', 'type' => 'text' ),
				array( 'key' => 'field_ts_event_location_venue', 'label' => 'Venue', 'name' => 'event_location_venue', 'type' => 'text' ),
				array( 'key' => 'field_ts_event_is_online', 'label' => 'Online', 'name' => 'event_is_online', 'type' => 'true_false', 'ui' => 1 ),
				array( 'key' => 'field_ts_event_is_hybrid', 'label' => 'Hybrid', 'name' => 'event_is_hybrid', 'type' => 'true_false', 'ui' => 1 ),
				array( 'key' => 'field_ts_event_url', 'label' => 'Event URL', 'name' => 'event_url', 'type' => 'url' ),
				array( 'key' => 'field_ts_event_registration_url', 'label' => 'Registration URL', 'name' => 'event_registration_url', 'type' => 'url' ),
				array( 'key' => 'field_ts_event_registration_deadline', 'label' => 'Registration Deadline', 'name' => 'event_registration_deadline', 'type' => 'date_picker', 'display_format' => 'Y-m-d', 'return_format' => 'Y-m-d' ),
				array( 'key' => 'field_ts_event_price', 'label' => 'Price', 'name' => 'event_price', 'type' => 'text' ),
				array( 'key' => 'field_ts_event_topics', 'label' => 'Topics', 'name' => 'event_topics', 'type' => 'checkbox', 'choices' => $topic_choices, 'return_format' => 'value' ),
				array( 'key' => 'field_ts_event_organizer', 'label' => 'Organizer', 'name' => 'event_organizer', 'type' => 'text' ),
				array( 'key' => 'field_ts_event_attendee_count', 'label' => 'Attendee Count', 'name' => 'event_attendee_count', 'type' => 'number' ),
				array( 'key' => 'field_ts_event_description', 'label' => 'Description', 'name' => 'event_description', 'type' => 'textarea', 'rows' => 6 ),
				array( 'key' => 'field_ts_event_image_url', 'label' => 'Image URL', 'name' => 'event_image_url', 'type' => 'url' ),
				array( 'key' => 'field_ts_event_cfp_deadline', 'label' => 'CFP Deadline', 'name' => 'event_cfp_deadline', 'type' => 'date_picker', 'display_format' => 'Y-m-d', 'return_format' => 'Y-m-d' ),
				array( 'key' => 'field_ts_event_cfp_url', 'label' => 'CFP URL', 'name' => 'event_cfp_url', 'type' => 'url' ),
				array( 'key' => 'field_ts_event_featured', 'label' => 'Featured', 'name' => 'event_featured', 'type' => 'true_false', 'ui' => 1 ),
				array(
					'key'        => 'field_ts_event_audience',
					'label'      => 'Audience Level',
					'name'       => 'event_audience',
					'type'       => 'select',
					'choices'    => array(
						'Beginners'     => 'Beginners',
						'Intermediate'  => 'Intermediate',
						'Advanced'      => 'Advanced',
						'All Levels'    => 'All Levels',
						'Students'      => 'Students',
						'Professionals' => 'Professionals',
						'Researchers'   => 'Researchers',
					),
					'allow_null' => 1,
				),
				array( 'key' => 'field_ts_event_certificate', 'label' => 'Certificate Offered', 'name' => 'event_certificate', 'type' => 'true_false', 'ui' => 1 ),
				array( 'key' => 'field_ts_event_certificate_type', 'label' => 'Certificate Type', 'name' => 'event_certificate_type', 'type' => 'text' ),
				array( 'key' => 'field_ts_event_attendee_profiles', 'label' => 'Attendee Profiles', 'name' => 'event_attendee_profiles', 'type' => 'textarea', 'rows' => 4 ),
				array( 'key' => 'field_ts_event_summary', 'label' => 'Expert Summary', 'name' => 'event_summary', 'type' => 'textarea', 'rows' => 4 ),
				array(
					'key'           => 'field_ts_event_languages',
					'label'         => 'Programming Languages',
					'name'          => 'event_languages',
					'type'          => 'checkbox',
					'choices'       => array(
						'JavaScript' => 'JavaScript',
						'Python'     => 'Python',
						'Rust'       => 'Rust',
						'Go'         => 'Go',
						'TypeScript' => 'TypeScript',
						'Java'       => 'Java',
						'Kotlin'     => 'Kotlin',
						'Swift'      => 'Swift',
						'C++'        => 'C++',
						'PHP'        => 'PHP',
						'Ruby'       => 'Ruby',
						'Scala'      => 'Scala',
					),
					'return_format' => 'value',
				),
				array( 'key' => 'field_ts_event_speakers_count', 'label' => 'Speakers Count', 'name' => 'event_speakers_count', 'type' => 'number' ),
				array( 'key' => 'field_ts_event_workshops_count', 'label' => 'Workshops Count', 'name' => 'event_workshops_count', 'type' => 'number' ),
				array( 'key' => 'field_ts_event_networking', 'label' => 'Networking Component', 'name' => 'event_networking', 'type' => 'true_false', 'ui' => 1 ),
				array( 'key' => 'field_ts_event_remote_friendly', 'label' => 'Remote Friendly', 'name' => 'event_remote_friendly', 'type' => 'true_false', 'ui' => 1 ),
				array( 'key' => 'field_ts_event_scholarships', 'label' => 'Scholarships Available', 'name' => 'event_scholarships', 'type' => 'true_false', 'ui' => 1 ),
				array( 'key' => 'field_ts_event_scholarship_url', 'label' => 'Scholarship URL', 'name' => 'event_scholarship_url', 'type' => 'url' ),
				array( 'key' => 'field_ts_event_past_editions', 'label' => 'Past Editions', 'name' => 'event_past_editions', 'type' => 'number' ),
				array( 'key' => 'field_ts_event_live_stream', 'label' => 'Live Stream', 'name' => 'event_live_stream', 'type' => 'true_false', 'ui' => 1 ),
			),
			'location' => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'tech_event',
					),
				),
			),
		)
	);
}
add_action( 'acf/init', 'techstack_acf_fields' );

function techstack_enqueue_assets() {
	wp_enqueue_style( 'techstack-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500;700&family=Space+Grotesk:wght@600;700&display=swap', array(), null );
	wp_enqueue_style( 'techstack-style', get_stylesheet_uri(), array( 'techstack-fonts' ), techstack_asset_version() );
	wp_enqueue_script( 'techstack-theme', get_template_directory_uri() . '/assets/js/theme.js', array(), techstack_asset_version(), true );
	wp_localize_script(
		'techstack-theme',
		'techstackData',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'techstack_events' ),
			'restUrl' => esc_url_raw( rest_url( 'techstack/v1/events' ) ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'techstack_enqueue_assets' );

function techstack_get_field( $key, $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	if ( function_exists( 'get_field' ) ) {
		$value = get_field( $key, $post_id );
		if ( null !== $value && '' !== $value ) {
			return $value;
		}
	}
	return get_post_meta( $post_id, $key, true );
}

function techstack_bool_field( $key, $post_id = null ) {
	$value = techstack_get_field( $key, $post_id );
	return true === $value || '1' === (string) $value || 1 === $value || 'true' === $value;
}

function techstack_date_value( $date ) {
	if ( empty( $date ) ) {
		return '';
	}
	if ( preg_match( '/^\d{8}$/', (string) $date ) ) {
		return $date;
	}
	$timestamp = strtotime( (string) $date );
	return $timestamp ? gmdate( 'Ymd', $timestamp ) : '';
}

function techstack_date_for_display( $date, $format = 'M j, Y' ) {
	$value = techstack_date_value( $date );
	if ( ! $value ) {
		return '';
	}
	$dt = DateTime::createFromFormat( 'Ymd', $value );
	return $dt ? $dt->format( $format ) : '';
}

function techstack_date_range( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$start   = techstack_get_field( 'event_date_start', $post_id );
	$end     = techstack_get_field( 'event_date_end', $post_id );
	if ( ! $start ) {
		return '';
	}
	$start_label = techstack_date_for_display( $start );
	$end_label   = techstack_date_for_display( $end );
	return ( $end_label && $end_label !== $start_label ) ? $start_label . ' - ' . $end_label : $start_label;
}

function techstack_topics( $post_id = null ) {
	$topics = techstack_get_field( 'event_topics', $post_id ? $post_id : get_the_ID() );
	if ( is_string( $topics ) ) {
		$maybe = maybe_unserialize( $topics );
		$topics = is_array( $maybe ) ? $maybe : array_filter( array_map( 'trim', explode( ',', $topics ) ) );
	}
	return is_array( $topics ) ? $topics : array();
}

function techstack_type_class( $type ) {
	return 'type-' . sanitize_html_class( strtolower( str_replace( '/', '-', $type ) ) );
}

function techstack_count_unique_event_meta( $key, $date_start = '', $date_end = '' ) {
	$key        = sanitize_key( $key );
	$meta_query = array();

	if ( $date_start && $date_end ) {
		$meta_query[] = array(
			'key'     => 'event_date_start',
			'value'   => array( techstack_date_value( $date_start ), techstack_date_value( $date_end ) ),
			'compare' => 'BETWEEN',
			'type'    => 'NUMERIC',
		);
	}

	$args = array(
		'post_type'      => 'tech_event',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	);
	if ( $meta_query ) {
		$args['meta_query'] = $meta_query;
	}

	$query = new WP_Query( $args );

	$values = array();
	foreach ( $query->posts as $post_id ) {
		$value = trim( (string) techstack_get_field( $key, $post_id ) );
		if ( $value ) {
			$values[ strtolower( $value ) ] = true;
		}
	}

	return count( $values );
}

function techstack_event_query_args( $request = array(), $paged = 1, $per_page = 12 ) {
	$meta_query = array( 'relation' => 'AND' );
	$tax_query  = array();
	$today      = current_time( 'Ymd' );

	$args = array(
		'post_type'      => 'tech_event',
		'post_status'    => 'publish',
		'posts_per_page' => $per_page,
		'paged'          => max( 1, (int) $paged ),
		'meta_key'       => 'event_date_start',
		'orderby'        => 'meta_value_num',
		'order'          => 'ASC',
		'meta_type'      => 'NUMERIC',
	);

	if ( empty( $request['include_past'] ) ) {
		$meta_query[] = array(
			'key'     => 'event_date_start',
			'value'   => $today,
			'compare' => '>=',
			'type'    => 'NUMERIC',
		);
	}

	if ( ! empty( $request['type'] ) && 'all' !== strtolower( sanitize_text_field( wp_unslash( $request['type'] ) ) ) ) {
		$type = sanitize_text_field( wp_unslash( $request['type'] ) );
		$meta_query[] = array( 'key' => 'event_type', 'value' => $type, 'compare' => '=' );
		$tax_query[]  = array( 'taxonomy' => 'event_category', 'field' => 'name', 'terms' => $type );
	}

	if ( ! empty( $request['city'] ) ) {
		$cities       = array_map( 'sanitize_text_field', (array) wp_unslash( $request['city'] ) );
		$meta_query[] = array( 'key' => 'event_location_city', 'value' => $cities, 'compare' => 'IN' );
	}

	if ( ! empty( $request['topics'] ) ) {
		$topics = array_map( 'sanitize_text_field', (array) wp_unslash( $request['topics'] ) );
		$topic_meta = array( 'relation' => 'OR' );
		foreach ( $topics as $topic ) {
			$topic_meta[] = array( 'key' => 'event_topics', 'value' => '"' . $topic . '"', 'compare' => 'LIKE' );
		}
		$meta_query[] = $topic_meta;
	}

	if ( ! empty( $request['date_from'] ) ) {
		$meta_query[] = array( 'key' => 'event_date_start', 'value' => techstack_date_value( sanitize_text_field( wp_unslash( $request['date_from'] ) ) ), 'compare' => '>=', 'type' => 'NUMERIC' );
	}

	if ( ! empty( $request['date_to'] ) ) {
		$meta_query[] = array( 'key' => 'event_date_start', 'value' => techstack_date_value( sanitize_text_field( wp_unslash( $request['date_to'] ) ) ), 'compare' => '<=', 'type' => 'NUMERIC' );
	}

	if ( ! empty( $request['price_free'] ) ) {
		$meta_query[] = array( 'key' => 'event_price', 'value' => 'Free', 'compare' => 'LIKE' );
	}

	if ( ! empty( $request['online_only'] ) ) {
		$meta_query[] = array( 'key' => 'event_is_online', 'value' => '1', 'compare' => '=' );
	}

	if ( ! empty( $request['s'] ) ) {
		$args['s'] = sanitize_text_field( wp_unslash( $request['s'] ) );
	}

	// CFP mode: filter to events with an open CFP deadline and sort by deadline.
	if ( ! empty( $request['cfp'] ) && in_array( (string) $request['cfp'], array( 'true', 'open', '1' ), true ) ) {
		$meta_query[] = array( 'key' => 'event_cfp_deadline', 'compare' => 'EXISTS' );
		$meta_query[] = array(
			'key'     => 'event_cfp_deadline',
			'value'   => $today,
			'compare' => '>=',
			'type'    => 'NUMERIC',
		);
		$args['meta_key'] = 'event_cfp_deadline';
		$args['orderby']  = 'meta_value_num';
		$args['order']    = 'ASC';
		unset( $args['meta_type'] );
	}

	if ( count( $meta_query ) > 1 ) {
		$args['meta_query'] = $meta_query;
	}

	if ( $tax_query ) {
		$args['tax_query'] = array_merge( array( 'relation' => 'OR' ), $tax_query );
	}

	// Sort (skip if CFP mode already set its own order).
	$sort = ! empty( $request['sort'] ) ? sanitize_text_field( wp_unslash( $request['sort'] ) ) : 'soonest';
	if ( empty( $request['cfp'] ) || ! in_array( (string) $request['cfp'], array( 'true', 'open', '1' ), true ) ) {
		if ( 'latest' === $sort ) {
			$args['orderby'] = 'date';
			$args['order']   = 'DESC';
			unset( $args['meta_key'], $args['meta_type'] );
		} elseif ( 'popular' === $sort ) {
			$args['meta_key'] = 'event_attendee_count';
			$args['orderby']  = 'meta_value_num';
			$args['order']    = 'DESC';
		} elseif ( 'alpha' === $sort ) {
			$args['orderby'] = 'title';
			$args['order']   = 'ASC';
			unset( $args['meta_key'], $args['meta_type'] );
		}
	}

	return $args;
}

function techstack_event_card( $post_id = null, $variant = 'compact', $context = '' ) {
	$post_id       = $post_id ? $post_id : get_the_ID();
	$type          = techstack_get_field( 'event_type', $post_id ) ?: 'Conference';
	$title         = techstack_get_field( 'event_name', $post_id ) ?: get_the_title( $post_id );
	$city          = techstack_get_field( 'event_location_city', $post_id );
	$country       = techstack_get_field( 'event_location_country', $post_id );
	$price         = techstack_get_field( 'event_price', $post_id ) ?: 'Free';
	$image         = techstack_get_field( 'event_image_url', $post_id );
	$is_online     = techstack_bool_field( 'event_is_online', $post_id );
	$event_start   = techstack_get_field( 'event_date_start', $post_id );
	$event_days    = null;
	if ( $event_start ) {
		$event_start_date = techstack_date_for_display( $event_start, 'Y-m-d' );
		$event_days       = $event_start_date ? (int) floor( ( strtotime( $event_start_date ) - current_time( 'timestamp' ) ) / DAY_IN_SECONDS ) : null;
	}
	$topics        = techstack_topics( $post_id );
	$classes       = array( 'event-card', 'event-card--' . sanitize_html_class( $variant ), techstack_type_class( $type ) );
	if ( null !== $event_days && $event_days >= 0 && $event_days <= 7 ) {
		$classes[] = 'event-card--starting-soon';
	}
	$permalink     = get_permalink( $post_id );
	$event_url     = techstack_get_field( 'event_url', $post_id );
	$reg_url       = techstack_get_field( 'event_registration_url', $post_id );
	$site_link     = $event_url ?: ( $reg_url ?: $permalink );

	// CFP context: resolve deadline, days remaining, badge class.
	$cfp_deadline_display = '';
	$cfp_days             = null;
	$cfp_badge_class      = 'cfp-badge--green';
	$cfp_url              = '';
	if ( 'cfp' === $context ) {
		$classes[]   = 'event-card--cfp';
		$cfp_raw     = techstack_get_field( 'event_cfp_deadline', $post_id );
		$cfp_url     = techstack_get_field( 'event_cfp_url', $post_id ) ?: '';
		if ( $cfp_raw ) {
			$cfp_deadline_display = techstack_date_for_display( $cfp_raw );
			$cfp_date_str         = techstack_date_for_display( $cfp_raw, 'Y-m-d' );
			if ( $cfp_date_str ) {
				$cfp_days = (int) floor( ( strtotime( $cfp_date_str ) - current_time( 'timestamp' ) ) / DAY_IN_SECONDS );
				if ( $cfp_days < 7 ) {
					$cfp_badge_class = 'cfp-badge--red';
				} elseif ( $cfp_days <= 14 ) {
					$cfp_badge_class = 'cfp-badge--yellow';
				}
			}
		}
	}

	// Rich data fields.
	$audience          = techstack_get_field( 'event_audience', $post_id );
	$has_certificate   = techstack_bool_field( 'event_certificate', $post_id );
	$is_networking     = techstack_bool_field( 'event_networking', $post_id );
	$is_scholarships   = techstack_bool_field( 'event_scholarships', $post_id );
	$scholarship_url   = techstack_get_field( 'event_scholarship_url', $post_id );
	$is_remote         = techstack_bool_field( 'event_remote_friendly', $post_id );
	$is_live_stream    = techstack_bool_field( 'event_live_stream', $post_id );
	$speakers_count    = techstack_get_field( 'event_speakers_count', $post_id );
	$past_editions     = (int) techstack_get_field( 'event_past_editions', $post_id );
	$attendee_profiles = techstack_get_field( 'event_attendee_profiles', $post_id );
	$event_languages   = techstack_get_field( 'event_languages', $post_id );
	if ( is_string( $event_languages ) ) {
		$maybe           = maybe_unserialize( $event_languages );
		$event_languages = is_array( $maybe ) ? $maybe : array();
	}
	$event_languages = is_array( $event_languages ) ? $event_languages : array();

	ob_start();
	?>
	<article class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-type="<?php echo esc_attr( $type ); ?>" data-city="<?php echo esc_attr( $city ); ?>" data-topics="<?php echo esc_attr( implode( ',', $topics ) ); ?>">
		<a href="<?php echo esc_url( $permalink ); ?>" class="ts-card-link" aria-label="<?php echo esc_attr( sprintf( __( 'View %s', 'techstack' ), $title ) ); ?>"></a>
		<?php if ( null !== $event_days && $event_days >= 0 && $event_days <= 7 && 'cfp' !== $context ) : ?>
			<span class="event-card__ribbon"><?php esc_html_e( 'Soon', 'techstack' ); ?></span>
		<?php endif; ?>
		<?php if ( 'featured' === $variant && $image ) : ?>
			<a class="event-card__media" href="<?php echo esc_url( $permalink ); ?>">
				<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy">
				<span class="event-card__shade"></span>
			</a>
		<?php endif; ?>
		<div class="event-card__body">
			<div class="event-card__top">
				<span class="event-badge <?php echo esc_attr( techstack_type_class( $type ) ); ?>"><?php echo esc_html( $type ); ?></span>
				<?php if ( $is_online ) : ?>
					<span class="event-online"><?php esc_html_e( 'Online', 'techstack' ); ?></span>
				<?php endif; ?>
			</div>
			<h3 class="event-card__title"><a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a></h3>
			<div class="event-card__meta event-card__date">
				<?php echo techstack_icon( 'calendar' ); ?>
				<span><?php echo esc_html( techstack_date_range( $post_id ) ); ?></span>
			</div>
			<div class="event-card__meta">
				<?php echo techstack_icon( 'pin' ); ?>
				<span><?php echo esc_html( $is_online && ! $city ? __( 'Online', 'techstack' ) : trim( $city . ( $country ? ', ' . $country : '' ) ) ); ?></span>
			</div>

			<?php if ( 'cfp' === $context ) : ?>
				<?php if ( $cfp_deadline_display ) : ?>
				<div class="event-card__meta event-card__cfp-deadline">
					<?php echo techstack_icon( 'calendar' ); ?>
					<span><?php echo esc_html( 'CFP: ' . $cfp_deadline_display ); ?></span>
					<?php if ( null !== $cfp_days && $cfp_days >= 0 ) : ?>
						<span class="cfp-badge <?php echo esc_attr( $cfp_badge_class ); ?>"><?php echo esc_html( $cfp_days . 'd left' ); ?></span>
					<?php endif; ?>
				</div>
				<?php endif; ?>
				<?php if ( $speakers_count || $past_editions ) : ?>
				<div class="event-card__cfp-meta">
					<?php if ( $speakers_count ) : ?>
						<span class="cfp-stat"><?php echo esc_html( number_format_i18n( (int) $speakers_count ) . ' ' . __( 'speakers expected', 'techstack' ) ); ?></span>
					<?php endif; ?>
					<?php if ( $past_editions ) : ?>
						<?php
						$sfx = array( 'th', 'st', 'nd', 'rd' );
						$v   = $past_editions % 100;
						$ord = $past_editions . ( isset( $sfx[ ( $v - 20 ) % 10 ] ) ? $sfx[ ( $v - 20 ) % 10 ] : ( isset( $sfx[ $v ] ) ? $sfx[ $v ] : $sfx[0] ) );
						?>
						<span class="cfp-stat"><?php echo esc_html( $ord . ' ' . __( 'edition', 'techstack' ) ); ?></span>
					<?php endif; ?>
				</div>
				<?php endif; ?>
				<?php if ( $attendee_profiles ) : ?>
				<p class="event-card__attendee-snippet"><?php echo esc_html( mb_substr( $attendee_profiles, 0, 80 ) . ( mb_strlen( $attendee_profiles ) > 80 ? '…' : '' ) ); ?></p>
				<?php endif; ?>
				<?php if ( $is_live_stream || $is_scholarships ) : ?>
				<div class="event-card__extra-badges">
					<?php if ( $is_live_stream ) : ?>
						<span class="event-mini-badge badge--live-stream"><?php esc_html_e( 'Streamed live', 'techstack' ); ?></span>
					<?php endif; ?>
					<?php if ( $is_scholarships ) : ?>
						<span class="event-mini-badge badge--scholarship"><?php esc_html_e( 'Diversity tickets available', 'techstack' ); ?></span>
					<?php endif; ?>
				</div>
				<?php endif; ?>
				<div class="event-card__footer event-card__footer--cfp">
					<span class="event-card__topics"><?php echo esc_html( implode( ' / ', array_slice( $topics, 0, 2 ) ) ); ?></span>
					<div class="event-card__actions">
						<?php if ( $cfp_url ) : ?>
							<a href="<?php echo esc_url( $cfp_url ); ?>" class="button button-primary button-cfp" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Submit Talk', 'techstack' ); ?></a>
						<?php endif; ?>
						<a href="<?php echo esc_url( $site_link ); ?>" target="_blank" rel="noopener noreferrer" class="ts-btn-secondary"><?php esc_html_e( 'See site', 'techstack' ); ?></a>
					</div>
				</div>

			<?php elseif ( 'hackathon' === $context ) : ?>
				<?php if ( $audience || $has_certificate || $is_scholarships || $is_remote ) : ?>
				<div class="event-card__badges">
					<?php if ( $audience ) : ?>
						<?php
						$ac = 'badge--audience-other';
						if ( 'Beginners' === $audience ) {
							$ac = 'badge--audience-beginners';
						} elseif ( 'Advanced' === $audience ) {
							$ac = 'badge--audience-advanced';
						} elseif ( 'All Levels' === $audience ) {
							$ac = 'badge--audience-all';
						}
						?>
						<span class="event-mini-badge <?php echo esc_attr( $ac ); ?>"><?php echo esc_html( $audience ); ?></span>
					<?php endif; ?>
					<?php if ( $has_certificate ) : ?>
						<span class="event-mini-badge badge--certificate"><?php esc_html_e( 'Certificate', 'techstack' ); ?></span>
					<?php endif; ?>
					<?php if ( $is_scholarships ) : ?>
						<span class="event-mini-badge badge--scholarship"><?php esc_html_e( 'Scholarships', 'techstack' ); ?></span>
					<?php endif; ?>
					<?php if ( $is_remote ) : ?>
						<span class="event-mini-badge badge--remote"><?php esc_html_e( 'Remote friendly', 'techstack' ); ?></span>
					<?php endif; ?>
				</div>
				<?php endif; ?>
				<?php if ( ! empty( $event_languages ) ) : ?>
				<div class="event-card__langs">
					<?php foreach ( $event_languages as $lang ) : ?>
						<span class="lang-pill"><?php echo esc_html( $lang ); ?></span>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
				<div class="event-card__footer">
					<span class="event-card__topics"><?php echo esc_html( implode( ' / ', array_slice( $topics, 0, 2 ) ) ); ?></span>
					<div class="event-card__actions">
						<?php if ( $is_networking ) : ?>
							<a href="<?php echo esc_url( $permalink ); ?>" class="button button-ghost button-cfp"><?php esc_html_e( 'Form a Team', 'techstack' ); ?></a>
						<?php endif; ?>
						<a href="<?php echo esc_url( $site_link ); ?>" target="_blank" rel="noopener noreferrer" class="ts-btn-secondary"><?php esc_html_e( 'See site', 'techstack' ); ?></a>
					</div>
				</div>

			<?php else : ?>
				<?php if ( $audience || $has_certificate || $is_networking || $is_scholarships ) : ?>
				<div class="event-card__badges">
					<?php if ( $audience ) : ?>
						<?php
						$ac = 'badge--audience-other';
						if ( 'Beginners' === $audience ) {
							$ac = 'badge--audience-beginners';
						} elseif ( 'Advanced' === $audience ) {
							$ac = 'badge--audience-advanced';
						} elseif ( 'All Levels' === $audience ) {
							$ac = 'badge--audience-all';
						}
						?>
						<span class="event-mini-badge <?php echo esc_attr( $ac ); ?>"><?php echo esc_html( $audience ); ?></span>
					<?php endif; ?>
					<?php if ( $has_certificate ) : ?>
						<span class="event-mini-badge badge--certificate"><?php esc_html_e( 'Certificate', 'techstack' ); ?></span>
					<?php endif; ?>
					<?php if ( $is_networking ) : ?>
						<span class="event-mini-badge badge--networking"><?php esc_html_e( 'Networking', 'techstack' ); ?></span>
					<?php endif; ?>
					<?php if ( $is_scholarships ) : ?>
						<span class="event-mini-badge badge--scholarship"><?php esc_html_e( 'Scholarships', 'techstack' ); ?></span>
					<?php endif; ?>
				</div>
				<?php endif; ?>
				<?php if ( ! empty( $event_languages ) ) : ?>
				<div class="event-card__langs">
					<?php foreach ( array_slice( $event_languages, 0, 3 ) as $lang ) : ?>
						<span class="lang-pill"><?php echo esc_html( $lang ); ?></span>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
				<div class="event-card__footer">
					<span class="event-card__topics"><?php echo esc_html( implode( ' / ', array_slice( $topics, 0, 2 ) ) ); ?></span>
					<div class="event-card__actions">
						<span class="price-badge <?php echo false !== stripos( $price, 'free' ) ? 'is-free' : 'is-paid'; ?>"><?php echo esc_html( $price ); ?></span>
						<a href="<?php echo esc_url( $site_link ); ?>" target="_blank" rel="noopener noreferrer" class="ts-btn-secondary"><?php esc_html_e( 'See site', 'techstack' ); ?></a>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</article>
	<?php
	return ob_get_clean();
}

function techstack_icon( $name ) {
	$icons = array(
		'calendar' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 2v3M17 2v3M4 9h16M5 5h14a1 1 0 0 1 1 1v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a1 1 0 0 1 1-1Z"/></svg>',
		'pin'      => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s7-5.1 7-12a7 7 0 1 0-14 0c0 6.9 7 12 7 12Z"/><circle cx="12" cy="9" r="2.5"/></svg>',
		'search'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>',
	);
	return '<span class="icon icon-' . esc_attr( $name ) . '">' . ( $icons[ $name ] ?? '' ) . '</span>';
}

function techstack_filter_events() {
	check_ajax_referer( 'techstack_events', 'nonce' );
	$page    = isset( $_POST['page'] ) ? max( 1, (int) $_POST['page'] ) : 1;
	$context = '';
	if ( ! empty( $_POST['cfp'] ) && 'true' === $_POST['cfp'] ) {
		$context = 'cfp';
	} elseif ( ! empty( $_POST['type'] ) && 'hackathon' === strtolower( sanitize_text_field( wp_unslash( $_POST['type'] ) ) ) ) {
		$context = 'hackathon';
	}
	$query   = new WP_Query( techstack_event_query_args( $_POST, $page, 12 ) );
	$html    = '';
	while ( $query->have_posts() ) {
		$query->the_post();
		$html .= techstack_event_card( get_the_ID(), 'compact', $context );
	}
	wp_reset_postdata();

	wp_send_json_success(
		array(
			'html'       => $html,
			'found'      => (int) $query->found_posts,
			'max_pages'  => (int) $query->max_num_pages,
			'page'       => $page,
			'showing'    => min( $query->found_posts, $page * 12 ),
		)
	);
}
add_action( 'wp_ajax_ks_filter_events', 'techstack_filter_events' );
add_action( 'wp_ajax_nopriv_ks_filter_events', 'techstack_filter_events' );

function techstack_get_events_by_month() {
	check_ajax_referer( 'techstack_events', 'nonce' );
	$year  = isset( $_POST['year'] ) ? (int) $_POST['year'] : (int) current_time( 'Y' );
	$month = isset( $_POST['month'] ) ? (int) $_POST['month'] : (int) current_time( 'n' );
	$start = sprintf( '%04d%02d01', $year, $month );
	$end   = gmdate( 'Ymt', strtotime( sprintf( '%04d-%02d-01', $year, $month ) ) );

	$query = new WP_Query(
		array(
			'post_type'      => 'tech_event',
			'post_status'    => 'publish',
			'posts_per_page' => 200,
			'meta_key'       => 'event_date_start',
			'orderby'        => 'meta_value_num',
			'order'          => 'ASC',
			'meta_query'     => array(
				array( 'key' => 'event_date_start', 'value' => array( $start, $end ), 'compare' => 'BETWEEN', 'type' => 'NUMERIC' ),
			),
		)
	);

	$events = array();
	while ( $query->have_posts() ) {
		$query->the_post();
		$post_id  = get_the_ID();
		$type     = techstack_get_field( 'event_type', $post_id ) ?: 'Conference';
		$events[] = array(
			'id'      => $post_id,
			'title'   => html_entity_decode( get_the_title(), ENT_QUOTES ),
			'type'    => $type,
			'date'    => techstack_date_for_display( techstack_get_field( 'event_date_start', $post_id ), 'Y-m-d' ),
			'city'    => techstack_get_field( 'event_location_city', $post_id ),
			'country' => techstack_get_field( 'event_location_country', $post_id ),
			'price'   => techstack_get_field( 'event_price', $post_id ) ?: 'Free',
			'register_url' => techstack_get_field( 'event_registration_url', $post_id ) ?: get_permalink( $post_id ),
			'url'     => get_permalink( $post_id ),
		);
	}
	wp_reset_postdata();
	wp_send_json_success( $events );
}
add_action( 'wp_ajax_ks_get_events_by_month', 'techstack_get_events_by_month' );
add_action( 'wp_ajax_nopriv_ks_get_events_by_month', 'techstack_get_events_by_month' );

function techstack_register_rest_routes() {
	register_rest_route(
		'techstack/v1',
		'/events',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'permission_callback' => '__return_true',
			'callback'            => function() {
				$query  = new WP_Query(
					array(
						'post_type'      => 'tech_event',
						'post_status'    => 'publish',
						'posts_per_page' => -1,
						'meta_key'       => 'event_date_start',
						'orderby'        => 'meta_value_num',
						'order'          => 'ASC',
					)
				);
				$events = array();
				while ( $query->have_posts() ) {
					$query->the_post();
					$post_id  = get_the_ID();
					$fields   = array();
					$field_keys = array( 'event_name', 'event_type', 'event_date_start', 'event_date_end', 'event_location_city', 'event_location_country', 'event_location_venue', 'event_is_online', 'event_is_hybrid', 'event_url', 'event_registration_url', 'event_registration_deadline', 'event_price', 'event_topics', 'event_organizer', 'event_attendee_count', 'event_description', 'event_image_url', 'event_cfp_deadline', 'event_cfp_url', 'event_featured' );
					foreach ( $field_keys as $key ) {
						$fields[ $key ] = techstack_get_field( $key, $post_id );
					}
					$events[] = array(
						'id'        => $post_id,
						'title'     => html_entity_decode( get_the_title(), ENT_QUOTES ),
						'permalink' => get_permalink( $post_id ),
						'fields'    => $fields,
					);
				}
				wp_reset_postdata();
				return rest_ensure_response( $events );
			},
		)
	);
}
add_action( 'rest_api_init', 'techstack_register_rest_routes' );

function techstack_handle_submit_event() {
	if ( empty( $_POST['techstack_submit_event'] ) || ! isset( $_POST['techstack_submit_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['techstack_submit_nonce'] ) ), 'techstack_submit_event' ) ) {
		return false;
	}

	$title   = sanitize_text_field( wp_unslash( $_POST['event_name'] ?? '' ) );
	$content = sanitize_textarea_field( wp_unslash( $_POST['event_description'] ?? '' ) );
	if ( ! $title ) {
		return new WP_Error( 'missing_title', __( 'Please add an event name.', 'techstack' ) );
	}

	$post_id = wp_insert_post(
		array(
			'post_type'    => 'tech_event',
			'post_status'  => 'draft',
			'post_title'   => $title,
			'post_content' => $content,
		),
		true
	);

	if ( is_wp_error( $post_id ) ) {
		return $post_id;
	}

	$fields = array( 'event_name', 'event_type', 'event_location_city', 'event_location_country', 'event_location_venue', 'event_registration_url', 'event_price', 'event_organizer', 'event_description', 'event_image_url' );
	foreach ( $fields as $field ) {
		update_post_meta( $post_id, $field, sanitize_text_field( wp_unslash( $_POST[ $field ] ?? '' ) ) );
	}
	update_post_meta( $post_id, 'event_url', esc_url_raw( wp_unslash( $_POST['event_registration_url'] ?? '' ) ) );
	update_post_meta( $post_id, 'event_date_start', techstack_date_value( sanitize_text_field( wp_unslash( $_POST['event_date_start'] ?? '' ) ) ) );
	update_post_meta( $post_id, 'event_date_end', techstack_date_value( sanitize_text_field( wp_unslash( $_POST['event_date_end'] ?? '' ) ) ) );
	update_post_meta( $post_id, 'event_is_online', ! empty( $_POST['event_is_online'] ) ? '1' : '0' );
	update_post_meta( $post_id, 'event_is_hybrid', ! empty( $_POST['event_is_hybrid'] ) ? '1' : '0' );
	update_post_meta( $post_id, 'event_topics', array_map( 'sanitize_text_field', (array) wp_unslash( $_POST['event_topics'] ?? array() ) ) );

	wp_set_object_terms( $post_id, sanitize_text_field( wp_unslash( $_POST['event_type'] ?? 'Conference' ) ), 'event_category' );
	if ( ! empty( $_POST['event_location_city'] ) ) {
		wp_set_object_terms( $post_id, sanitize_text_field( wp_unslash( $_POST['event_location_city'] ) ), 'event_location' );
	}

	return true;
}

function techstack_count_events() {
	$count = wp_count_posts( 'tech_event' );
	return isset( $count->publish ) ? (int) $count->publish : 0;
}

function techstack_count_by_meta( $key, $value ) {
	$key   = sanitize_key( $key );
	$value = sanitize_text_field( $value );
	$query = new WP_Query(
		array(
			'post_type'      => 'tech_event',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_query'     => array(
				'relation' => 'OR',
				array(
					'key'   => $key,
					'value' => $value,
				),
				array(
					'key'     => $key,
					'value'   => '"' . $value . '"',
					'compare' => 'LIKE',
				),
			),
		)
	);
	return (int) $query->found_posts;
}

function techstack_ics_payload( $post_id ) {
	$start = techstack_date_value( techstack_get_field( 'event_date_start', $post_id ) );
	$end   = techstack_date_value( techstack_get_field( 'event_date_end', $post_id ) );
	if ( ! $end ) {
		$end = $start;
	}
	$end_dt = DateTime::createFromFormat( 'Ymd', $end );
	if ( $end_dt ) {
		$end_dt->modify( '+1 day' );
		$end = $end_dt->format( 'Ymd' );
	}
	return array(
		'uid'      => 'techstack-' . $post_id . '@' . wp_parse_url( home_url(), PHP_URL_HOST ),
		'stamp'    => gmdate( 'Ymd\THis\Z' ),
		'start'    => $start,
		'end'      => $end,
		'title'    => html_entity_decode( get_the_title( $post_id ), ENT_QUOTES ),
		'location' => trim( techstack_get_field( 'event_location_venue', $post_id ) . ' ' . techstack_get_field( 'event_location_city', $post_id ) ),
		'url'      => get_permalink( $post_id ),
		'slug'     => get_post_field( 'post_name', $post_id ),
	);
}
