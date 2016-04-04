<?php
/**
 * Plugin Name: WP ChicWeather
 * Description: Collects data for the ChicWeather platform
 * Version: 1.0.0
 * Author: Rafael/Andre
 * Author URI: http://ChicWeather.com
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Domain Path: /fashion
 * Text Domain: Fashion
 */


/**
 * Register rewrite rules for the API.
 *
 * @global WP $wp Current WordPress environment instance.
 */
function json_api_init() {

	$args = array(
		'posts_per_page'   => 5,
		'offset'           => 0,
		'category'         => '',
		'category_name'    => '',
		'orderby'          => 'date',
		'order'            => 'DESC',
		'include'          => '',
		'exclude'          => '',
		'meta_key'         => '',
		'meta_value'       => '',
		'post_type'        => 'post',
		'post_mime_type'   => '',
		'post_parent'      => '',
		'author'	   => '',
		'post_status'      => 'publish',
		'suppress_filters' => true
	);

	$posts = get_posts( $args );

	$meta = array();
	$tags = array();


	foreach( $posts as $post ){

		// print_r(

			//  echo $post->ID . " / " . $post->post_author  . " / " . $post->post_date_gmt  . " / " . $post->post_title . "<br>";
		// );

		// $meta[ $post->ID ] = get_post_meta( $post->ID );
		$tags[ $post->ID ] = get_the_tags( $post->ID );
		$category[ $post->ID ] = get_the_category( $post->ID );

	};

	// echo json_encode( $posts );

	// print_r( wp_json_encode($category) );

	// post_to_api( $posts );


}





function post_to_api($data){

	$endpoint = "http://wp-chicweather.herokuapp.com/api";
	// $endpoint = "http://71.202.145.79:3000/api";
	$api_key = "fkvsAPaUYi3fIAa87SAcPXZskM2VmopiyGMXCsfEoQA===";

	//Stupid thing on the Nodejs side, having hard time getting the headers
	// $data['X-ChicWeather-Authkey'] = $api_key;

	$args = array(
	 'method' => 'POST',
	 'timeout' => 120,
	 'redirection' => 5,
	 'httpversion' => '1.0',
	 'blocking' => true,
	 'headers' => array(
	  'Content-Type' => 'application/json',
	  'x-chicweather-authkey' => $api_key
	 ) ,
	 'body' => json_encode( $data )
	);

	$response = wp_remote_post($endpoint, $args);

	if ($response['response']['code'] != 200)
	{
		$response_body = $response['body'];
	 	echo "Error occured!" . $response['response']['message'];
	}
	else
	{
	 		$response_body = $response['body'];
			echo $response_body;
	 			//string, associative
	 		return json_decode($response_body, true);
	}

	echo $response_body;

}



function new_post_hook($id, $post){

	post_to_api( $post );

}





add_action( 'publish_post', 'new_post_hook', 10, 2 );
add_action( 'init', 'json_api_init', 11 ); // Prioritized over core rewrites
