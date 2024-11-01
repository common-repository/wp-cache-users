<?php
/*
Plugin Name: WP Cache Users
Description: Caches multiple users automatically on post and comment pages to radically reduce mysql queries for plugins that get_userdata.
Plugin URI:  http://wordpress.org/support/topic/224961
Author: _ck_
Author URI: http://bbShowcase.org
Version: 0.0.4
*/ 

add_filter('the_posts', 'wp_cache_users_from_posts',9);
add_filter('comments_array','wp_cache_users_from_comments',9);

function wp_cache_users_from_posts($posts) {
	if (!empty($posts)) {
		$ids=array();  foreach ($posts as $post) {if (!empty($post->post_author)) {$ids[$post->post_author]=$post->post_author;}}
		wp_cache_users($ids);
	}
	return $posts;
}

function wp_cache_users_from_comments($comments) {
	if (!empty($comments)) {
		$ids=array(); foreach ($comments as $comment) {if (!empty($comment->user_id)) {$ids[$comment->user_id]=$comment->user_id;}}
		wp_cache_users($ids);
	}
	return $comments;
}

function wp_cache_users($ids) {	
	if (empty($ids)) {return;}
	global $wpdb, $wp_object_cache; $limit=0; $in='';
	foreach ($ids as $key=>$id) {if (!isset($wp_object_cache->cache['users'][$id])) {$limit++; $in.=$id.',';}}
	if ($limit) { 
		$in=substr($in,0,-1); 
		$users=$wpdb->get_results("SELECT * FROM $wpdb->users WHERE ID IN ($in) LIMIT $limit"); 
		if (!empty($users)) { _fill_many_users($users); } 
	}
}
