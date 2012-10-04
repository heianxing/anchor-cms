<?php defined('IN_CMS') or die('No direct access allowed.');

/**
	Theme functions for posts
*/
function has_posts() {
	if(($posts = IoC::resolve('posts')) === false) {
		$params = array(
			'status' => 'published', 
			'sortby' => 'created', 
			'sortmode' => 'desc', 
			'limit' => Config::get('metadata.posts_per_page', 10), 
			'offset' => Input::get('offset', 0)
		);
		$posts = Posts::list_all($params);
		IoC::instance('posts', $posts, true);

		$total_posts = Posts::count(array('status' => 'published'));
		IoC::instance('total_posts', $total_posts, true);
	}
	
	return $posts->length() > 0;
}

function posts() {
	if(has_posts() === false) {
		return false;
	}
	
	$posts = IoC::resolve('posts');

	if($result = $posts->valid()) {	
		// register single post
		IoC::instance('article', $posts->current(), true);
		
		// move to next
		$posts->next();
	}

	return $result;
}

function posts_next($text = 'Next', $default = '') {
	$per_page = Config::get('metadata.posts_per_page');
	$offset = post_pages_current();
	$total = IoC::resolve('total_posts');

	$pages = floor($total / $per_page);
	$page = $offset / $per_page;

	if($pages > 0 and $page < $pages) {
		return '<a href="' . current_url() . '?offset=' . ($offset + $per_page) . '">' . $text . '</a>';
	}

	return $default;
}

function posts_prev($text = 'Previous', $default = '') {
	$per_page = Config::get('metadata.posts_per_page');
	$offset = post_pages_current();
	$total = IoC::resolve('total_posts');

	$pages = ceil($total / $per_page);
	$page = $offset / $per_page;
	
	if($pages > 1 and $offset > 0) {
		return '<a href="' . current_url() . '?offset=' . ($offset - $per_page) . '">' . $text . '</a>';
	}

	return $default;
}

function post_pages_total() {
    return ceil(IoC::resolve('total_posts') / Config::get('metadata.posts_per_page'));
}

function post_pages_current() {
    return Input::get('offset', 0) + 1;
}