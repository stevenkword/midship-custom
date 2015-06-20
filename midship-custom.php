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

/**
 * Replace all site titles to include MR2 for SEO
 * @param  [type] $title [description]
 * @return [type]        [description]
 */
function midship_filter_wp_title( $title, $sep ) {
	//Except on the homepage
	if( is_home() || is_front_page() ) {
		return $title;
	}
	return 'MR2 ' . $title;
}
add_filter( 'wp_title', 'midship_filter_wp_title', 10, 2 );


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

/**
 * [midship_filter_the_author_posts_link description]
 * @param  [type] $link [description]
 * @return [type]       [description]
 */
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
}
add_filter( 'the_author_posts_link', 'midship_filter_the_author_posts_link' );


/**
 * [midship_singular_byline description]
 * @param  [type] $content [description]
 * @return [type]          [description]
 */
function midship_get_singular_byline() {

	global $authordata;
	if ( !is_object( $authordata ) )
		return false;
	$link = sprintf(
		'<a href="%1$s" title="%2$s" rel="author">%3$s</a>',
		esc_url( get_author_posts_url( $authordata->ID, $authordata->user_nicename ) ),
		esc_attr( sprintf( __( 'Posts by %s' ), get_the_author() ) ),
		get_the_author()
	);

	$pieces = array();

	// Article Attributioni & Credit
	$author_credit = 'Courtesy of ';
	$source_credit = midship_get_accredited_source_link();
	if( ! empty( wp_strip_all_tags( $link ) ) && ! empty( $source_credit ) ) {
		$pieces[] = $author_credit . $link . ' of ' . $source_credit;
	} elseif ( ! empty( wp_strip_all_tags( $link ) ) ) {
		$pieces[] = $author_credit . $link;
	} elseif ( ! empty( $source_credit ) ) {
		$pieces[] = $author_credit . $source_credit;
	}

	// Date
	$pieces[] =  get_the_date();

	// Print
	if( ! function_exists( 'is_print' ) || ! is_print() ) {
		global $post;
		$link = get_permalink( $post->ID );
		$pieces[] = '<a href="'.$link.'print/">Print</a>';
	}

	$new_content = implode( ' | ' , $pieces );
	return '<p class="post-byline post-info">' . $new_content . '</p>';
}

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
	return $title;
	return '<a href src="' . $link . '"">' . $title . '</a>';
}

function midship_get_banner_ad( $content ){
		$ad = '<div style="width:100%"><script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
		<!-- Midshiprunabout Banner -->
		<ins class="adsbygoogle"
		     style="display:inline-block;width:728px;height:90px"
		     data-ad-client="ca-pub-4187229811122687"
		     data-ad-slot="1168349906"></ins>
		<script>
		(adsbygoogle = window.adsbygoogle || []).push({});
		</script></div>';

		return $ad;
}

/**
 * [midship_content_disclaimer description]
 * @param  [type] $content [description]
 * @return [type]          [description]
 */
function midship_get_content_disclaimer() {

	ob_start();
	wp_link_pages(array('before' => '<div class="pagination" style="float:none;">', 'after' => '</div>', 'link_before'  => '<span class="current"><span class="currenttext">', 'link_after' => '</span></span>', 'next_or_number' => 'next_and_number', 'nextpagelink' => __('Next','mythemeshop'), 'previouspagelink' => __('Previous','mythemeshop'), 'pagelink' => '%','echo' => 1 ));
	$wp_link_pages = ob_get_clean();

	$content .= $wp_link_pages;

	$content .= '<div style="border:1px solid #ccc; background: #eee;padding:1em;"><p>This documentation in no way replaces the Toyota MR2 Repair Manuals. The purpose of this content is only to provide supplementary information to fellow MR2 enthusiasts. Midship Runabout and its contributing authors will not be held responsible for any injury or damages that may occur as the result of practicing any of the methods or procedures described within this website. Article and photo submissions are property of the contributing author.</p><!--wp-print-friendly--><div style="text-align:center;padding-bottom:1em;"><a href="print/" class="button"><button>Print this guide!</button></a></div><!--/wp-print-friendly--></div><br/>';
	return $content;
}

/**
 * [midship_pagination description]
 * @param  [type] $content [description]
 * @return [type]          [description]
 */
function midship_pagination( $content ){
	if ( is_singular() && function_exists( 'pgntn_display_pagination' ) ){
		$pagination = pgntn_display_pagination( 'multipage' );
		return $pagination . $content . $pagination;
	}
	return $content;
}
//add_filter( 'the_content', 'midship_pagination', 0 );

function midship_render_content_header( $content ){
	// Only show on single article pages, but not print pages
	if( ! is_singular() || ( function_exists( 'is_print' ) && is_print() ) ) {
		return $content;
	}

	$new_content = midship_get_singular_byline();

	// Pagination
	/*
	ob_start();
	if ( function_exists( 'wp_pagenavi' ) ){
		wp_pagenavi( array( 'type' => 'multipart' ) );
	}
	$pagination = ob_get_clean();
	$new_content .= '<p>' . $pagination . '</p>';
	*/

	return $new_content . $content;

}
add_filter( 'the_content', 'midship_render_content_header', 1 ); // needs to be early for auto links

/**
 * [midship_render_content_footer description]
 * @return [type] [description]
 */
function midship_render_content_footer( $content ){
	// Only show on single article pages, but not print pages
	if( ! is_singular() || ( function_exists( 'is_print' ) && is_print() ) ) {
		return $content;
	}

	// Pagination
	/*
	ob_start();
	if ( function_exists( 'wp_pagenavi' ) ){
		wp_pagenavi( array( 'type' => 'multipart' ) );
	}
	$pagination = ob_get_clean();
	$content .= '<p>' . $pagination . '</p>';
	*/

	$content .= midship_get_content_disclaimer();
	return $content;
}
//add_filter( 'the_content', 'midship_render_content_footer', 11 );








