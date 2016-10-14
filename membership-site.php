<?php

/*
Plugin Name: WP Membership Site
Plugin URI: http://kelabim.com/
Description: Enables admin to make a member-only page. Members will only see that content only when they signed in.
Author: Abdul Rahman
Version: 0.1
Author URI: http://kelabim.com/
*/
/*
need to clean up the code
need fully functional admin interface

how to use it.
1. create new post at members page
2. create new user with members role
3. if user doesn't have view-member as capability, it will automatically go back to the index.php

*/

// add new menu to admin
add_action( 'admin_menu', 'wp_membership_menu' );

// new custom post type
add_action( 'init', 'create_post_type' );
function create_post_type() {
	register_post_type( 'members-page',
		array(
			'labels' => array(
				'name' => __( 'Members Page' ),
				'singular_name' => __( 'Members Page' ),
				),
			'description' => 'This is members page',
			'public' => true,
			'exclude_from_search' => false,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_nav_menus' => true,
			'show_in_menu' => true,
			'show_in_admin_bar' => true,
			'menu_position' => 5,
			'hierarchical' => true,
			
			)
		);
}

//add member role to wp
add_action( 'admin_init', 'add_new_role' );
function add_new_role() {
	$members = add_role( 'members', 'Members', array(
		'read' => true,
		'manage_links', 
		'manage_categories', 
		'moderate_comments',
		'view-member'
		));

	if( null !== $members ) {
		$role = get_role( 'administrator' );
		$role->add_cap( 'view-member' );
	}
}

//add menu to wp-admin

function wp_membership_menu() {
	add_options_page( 'WP Membership options', 'WP Membership', 'manage_options', 'members-admin','wp_membership_options' );
}

function wp_membership_options() {
	if ( !current_user_can( 'view-member' )) {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ));
	}
	echo '<div class="wrap">';
	?>
	
	<ul>
		<p>Below is list of members</p>
		<?php
		$blogmembers = get_users('blog_id=1&orderby=nicename&role=members');
		foreach ($blogmembers as $user) {
			echo '<li>' . $user->first_name . ' ' . $user->last_name . '</li>';
			echo '<li>' . $user->user_email . '</li>';
		};


		?>
	</ul>

	<?php $loop = new WP_Query( array( 'post_type' => 'members-page', 'posts_per_page' => 10 ) );

	while ( $loop->have_posts() ) : $loop->the_post(); ?>

	<?php the_title( '<h2 class="entry-title"><a href="' . get_permalink() . '" title="' . the_title_attribute( 'echo=0' ) . '" rel="bookmark">', '</a></h2>' ); ?>

	<div class="entry-content">
		<?php the_content(); ?>
	</div>
<?php endwhile; ?>

<?php


echo '</div>';
}



function check_member_status() {

	$posttype = get_post_type( get_the_ID() );

	if ( $posttype == 'members-page' ) {
		if ( !current_user_can( 'view-member' )) {
			header('Location: index.php');
		}
	}

}
add_action('wp_footer', 'check_member_status');


?>