<?php
/**
 * Plugin Name:       Reset Roles and Capabilities
 * Plugin URI:        https://wordpress.org/plugins/reset-roles-and-capabilities/
 * Description:       Reset Roles and Capabilities to WordPress defaults
 * Version:           2.6
 * Requires at least: 2.9
 * Author:            Francesco Taurino
 * Author URI:        https://profiles.wordpress.org/francescotaurino/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       reset-roles-and-capabilities
 * Domain Path:       /languages
 * @copyright Copyright (c) 2018, Francesco Taurino
 */
namespace ResetRolesAndCapabilities;
/**
 * Transient Name & Plugin Slug
 */
const TRANSIENT_NAME = 'reset-roles-and-capabilities';

/**
 * Time until expiration in seconds
 */
const TRANSIENT_TIME = 600;

/*
 * Set the activation hook
 */
register_activation_hook( __FILE__, function () 
{

	global $wp_roles;

	if (!function_exists('populate_roles')) {
		require_once (ABSPATH . 'wp-admin/includes/schema.php');
	}

	if (!isset($wp_roles)) {
		$wp_roles = new \WP_Roles();
	}

	foreach ($wp_roles->roles as $role_name => $role_info):

		/**
		 * Fetch a Role definition.
		 */
		$role = get_role($role_name);

		foreach ($role_info['capabilities'] as $capability => $_):

			/**
			 * Removes a capability from a role
			 */
			$role->remove_cap($capability);

		endforeach;

		/**
		 * Remove role, if it exists.
		 */
		remove_role($role_name);

	endforeach;

	/**
	 * Execute WordPress role creation for the various WordPress versions.
	 */
	populate_roles();

	/**
	 * Set a transient to show a success message
	 */	
	set_transient(TRANSIENT_NAME, __('Roles and Capabilities have been reset to WordPress defaults. The plugin has been deactivated.', 'reset-roles-and-capabilities'), TRANSIENT_TIME);

});

 
/**
 * Back compatibility 
 * WordPress < 4.6
 * @link https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#loading-text-domain
 */
add_action( 'plugins_loaded', function() 
{
	load_plugin_textdomain( TRANSIENT_NAME, FALSE, basename( dirname( __FILE__ ) ) . '/languages/'  );
});


/**
 * Hide default admin notice on plugin activation
 */
add_action( 'admin_head', function() 
{
	echo '<style>#message { display: none !important; }</style>';
});

/**
 * Show a message after activation and then delete the transient
 */
add_action('admin_notices', function () 
{

	if ($text = get_transient(TRANSIENT_NAME)) {
		
		printf('<div class="%1$s"><p style="font-size:28px;">%2$s</p></div>', 
			'notice notice-success is-dismissible', 
			wp_kses_post($text) 
		);
	
		delete_transient(TRANSIENT_NAME);

	}

});

/**
 * Deactivate the plugin
 */
add_action('admin_init', function () 
{
	deactivate_plugins(plugin_basename(__FILE__));
}, 0);
