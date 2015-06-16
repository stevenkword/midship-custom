<?php
/*
Plugin Name: Midship Runabout Custom Functions
Version: 0.1-alpha
Description: PLUGIN DESCRIPTION HERE
Author: YOUR NAME HERE
Author URI: YOUR SITE HERE
Plugin URI: PLUGIN SITE HERE
Text Domain: midship-custom
Domain Path: /languages
*/

/*
array(2) {
  ["page-templates/contributors.php"]=>
  string(16) "Contributor Page"
  ["page-templates/full-width.php"]=>
  &string(15) "Full Width Page"
}
array(3) {
  ["page-templates/contributors.php"]=>
  string(16) "Contributor Page"
  ["page-templates/full-width.php"]=>
  &string(15) "Full Width Page"
  ["page-magic-widgets.php"]=>
  string(13) "Magic Widgets"
}
*/

function midship_restrict_xmlrpc_login(){
	if( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {
		die( 'f this noise' );
	}
}
//add_action( 'init', 'midship_restrict_xmlrpc_login' );

function mynamespace_remove_xmlrpc_methods( $methods ) {
	unset( $methods['demo.addTwoNumbers'] );
	return $methods;
}
add_filter( 'xmlrpc_methods', 'mynamespace_remove_xmlrpc_methods');

function midship_add_fonts() {
	?>
	<link href='http://fonts.googleapis.com/css?family=Roboto|Lato|PT+Sans|Ubuntu|Josefin+Sans|Open+Sans|Play|Inconsolata|Oxygen' rel='stylesheet' type='text/css'>
	<?php
}
add_action( 'wp_head', 'midship_add_fonts', 99 );

function midship_login_header() {
	?><div style="text-align:center"><?php
	if( is_user_logged_in() ) {
		?>

<!--
<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
	<ul>
		<?php bp_get_displayed_user_nav(); ?>
	</ul>
</div>
-->
		<a class="header-button" style="margin-top: 1em;" href="<?php echo bp_loggedin_user_domain(); ?>">My Midship</a>
		<div><?php wp_loginout(); ?></div>
		<?php
	} else {
		?>
		<a class="header-button" href="<?php echo site_url( 'register' ); ?>">Join our Community!</a>
		<!--<a href="http://www.midshiprunabout.org/login/" class="header-button">Log in!</a>-->
		<?php
		do_action( 'wordpress_social_login' );
	}
	?></div><?php
}

/*
Header Banner

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
  ga('create', 'UA-3689671-6', 'auto');
  ga('send', 'pageview');
</script>

<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- Midshiprunabout Banner -->
<ins class="adsbygoogle"
	 style="display:inline-block;width:728px;height:90px"
	 data-ad-client="ca-pub-4187229811122687"
	 data-ad-slot="1168349906"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>
*/

/*
Beow Post Title
<div style="text-align:center">
<a href="http://www.midshiprunabout.org/login/" class="header-button">Log in!</a> <a href="http://www.midshiprunabout.org/register/">Join our Community!
</div>
 */

function midship_flush_cache() {
	wp_cache_flush();
	//wp_cache_clear_cache();
}
add_action( 'publish_post', 'midship_flush_cache' );
add_action( 'wp_logout', 'midship_flush_cache');
add_action( 'updated_option', 'midship_flush_cache' );




function midship_filter_the_author ( $authordata ) {
	$meta = get_post_meta( get_the_ID(), 'articleAuthor' );

	if( isset( $meta[0] ) && ! empty( $meta[0] ) ) {
		return esc_html( $meta[0] );
	}

	return $authordata->display_name;
}
add_filter( 'the_author', 'midship_filter_the_author' );

function midship_filter_author_link( $link, $author_id, $author_nicename ) {

	return 'http://google.com';

	//if( 'vatik' == $author_nicename ) {
		$link = esc_url( 'http://google.com' );
	//}
	return $link;
}
//add_filter( 'author_link', 'midship_filter_author_link', 10, 3 );

function midship_filter_the_author_posts_link ( $link ){

	if( strpos( $link, 'vatik' ) ) {

		global $authordata;
		$author_name = $authordata->display_name;

		$meta = get_post_meta( get_the_ID(), 'articleAuthor' );
		if( isset( $meta[0] ) && ! empty( $meta[0] ) ) {
			$author_name = esc_html( $meta[0] );
		}

		return '<a href="#/">' . $author_name . '</a>';
	}

	// BuddyPressify the author links
	return str_replace( 'author', 'members', $link );

	//return $link;
}
//add_filter( 'the_author_posts_link', 'midship_filter_the_author_posts_link' );


/**
 * [midship_singular_byline description]
 * @param  [type] $content [description]
 * @return [type]          [description]
 */
function midship_singular_byline( $content ) {
	$pieces = array();

	$pieces[] = 'Courtesy of ' . get_the_author_link() . ' of ' . midship_get_accredited_source_link();
	$pieces[] = '<a href="print/">Print</a>';
	$new_content = implode( ' | ' , $pieces );
	return $new_content . $content;
}
add_filter( 'the_content', 'midship_singular_byline', 10 );

/**
 * [midship_get_accredited_source description]
 * @param  string $post_id [description]
 * @return [type]          [description]
 */
function midship_get_accredited_source_link( $post_id = '' ) {
	if( empty( $post_id ) ) {
		global $post;
		$post_id = $post->ID;
	}
	$title = get_post_meta( $post_id, 'linkWebsiteTitle', true );
	$link  = get_post_meta( $post_id, 'linkSourceURL', true );
	return '<a href src="' . $link . '"">' . $title . '</a>';
}

/**
 * Attribute the original Source Aricle
 *
 * @param  [type] $content [description]
 * @return [type]          [description]
 */
function midship_accredit_source( $content ) {
	// Only show on single article pages
	if( ! is_singular() ) {
		return $content;
	}
	global $post;
	$title = get_post_meta( $post->ID, 'linkWebsiteTitle', true );
	$link  = get_post_meta( $post->ID, 'linkSourceURL', true );
	if( $link ) {
		$content = '<p>Special Thanks to <a target="_blank" href="' . esc_url($link) . '">' . esc_html($title) . '</a></p>' . $content;
	}
	return $content;
}
//add_filter( 'the_content', 'midship_accredit_source', 11 );

/**
 * [midship_content_disclaimer description]
 * @param  [type] $content [description]
 * @return [type]          [description]
 */
function midship_content_disclaimer( $content ) {
	// Only show on single article pages, but not print pages
	if( ! is_singular() || ( function_exists( 'is_print' ) && is_print() ) ) {
		return $content;
	}

	$content .= '<div style="border:1px solid #ccc; background: #eee;padding:1em;"><p>This documentation in no way replaces the Toyota MR2 Repair Manuals. The purpose of this content is only to provide supplementary information to fellow MR2 enthusiasts. Midship Runabout and its contributing authors will not be held responsible for any injury or damages that may occur as the result of practicing any of the methods or procedures described within this website. Article and photo submissions are property of the contributing author.</p><!--wp-print-friendly--><div style="text-align:center;padding-bottom:1em;"><a href="print/" class="button"><button>Print this guide!</button></a></div><!--/wp-print-friendly--></div><br/>';
	return $content;
}
add_filter( 'the_content', 'midship_content_disclaimer', 11 );