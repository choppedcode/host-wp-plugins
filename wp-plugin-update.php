<?php
// Define HOST_WP_PLUGINS_BASE_URL to match the URL you defined in your BASE_URL.
if (!defined('HOST_WP_PLUGINS_BASE_URL')) define('HOST_WP_PLUGINS_BASE_URL','.../');

//Define your plugin main file name here, without the .php extension
define('PLUGIN_FILENAME','plugin-filename');

// Take over the update check
add_filter('pre_set_site_transient_update_plugins', 'my_hosted_plugin_check_for_plugin_update');

// Activate the following 2 lines to force WP to check for a new version, otherwise the check is done every 12 hours or so
//delete_option('_site_transient_update_plugins');
//wp_update_plugins();

function my_hosted_plugin_check_for_plugin_update($checked_data) {
	$plugin_dir = basename(dirname(__FILE__));
	
	if (empty($checked_data->checked))
		return $checked_data;
	
	$request_args = array(
		'slug' => $plugin_dir,
		'version' => $checked_data->checked[$plugin_dir .'/'. 'my_hosted_plugin_filename' .'.php'],
	);
	$request_string = my_hosted_plugin_prepare_request('basic_check', $request_args);
	
	// Start checking for an update
	$raw_response = wp_remote_post(HOST_WP_PLUGINS_BASE_URL, $request_string);
	
	if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200))
		$response = unserialize($raw_response['body']);
	
	if (is_object($response) && !empty($response) && $response->new_version) // Feed the update data into WP updater
		$checked_data->response[$plugin_dir .'/'. 'my_hosted_plugin_filename' .'.php'] = $response;
	
	return $checked_data;
}

// Take over the Plugin info screen
add_filter('plugins_api', 'my_hosted_plugin_api_call', 10, 3);

function my_hosted_plugin_api_call($def, $action, $args) {
	$plugin_dir = basename(dirname(__FILE__));
	
	if (!isset($args->slug) || ($args->slug != $plugin_dir)) return false;
	
	// Get the current version
	$plugin_info = get_site_transient('update_plugins');
	$current_version = $plugin_info->checked[$plugin_dir .'/'. 'my_hosted_plugin_filename' .'.php'];
	$args->version = $current_version;
	
	$request_string = my_hosted_plugin_prepare_request($action, $args);
	
	$request = wp_remote_post(HOST_WP_PLUGINS_BASE_URL, $request_string);
	
	if (is_wp_error($request)) {
		$res = new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>'), $request->get_error_message());
	} else {
		$res = unserialize($request['body']);
		
		if ($res === false)
			$res = new WP_Error('plugins_api_failed', __('An unknown error occurred'), $request['body']);
	}
	
	return $res;
}


function my_hosted_plugin_prepare_request($action, $args) {
	global $wp_version;
	
	return array(
		'body' => array(
			'action' => $action, 
			'request' => serialize($args),
			'api-key' => md5(get_bloginfo('url'))
		),
		'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
	);	
}

