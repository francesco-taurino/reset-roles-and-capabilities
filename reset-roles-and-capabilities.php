<?php
/*
 * Plugin Name: Reset Roles and Capabilities  
 * Plugin URI: https://francescotaurino.com
 * Description: Reset Roles and Capabilities to WordPress defaults
 * Author: Francesco Taurino
 * Domain Path: languages
 * Author URI: https://www.francescotaurino.com
 * License: GPL v2 or later
 *
 * Version: 1.0
 * Requires PHP: 5.2
 * Requires at least: 2.8.0
 * Tested up to: 4.9.6
 */

/*
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 
	2 of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	with this program. If not, visit: https://www.gnu.org/licenses/

	Copyright 2018 Monzilla Media. All rights reserved.
*/


define('RRAC_TRANSIENT_TIME', 5 );
define('RRAC_TRANSIENT_ERROR','rrac-failed');
define('RRAC_TRANSIENT_SUCCESS','rrac-success');


register_activation_hook( __FILE__, function () {

	global $wp_roles;

  require_once( ABSPATH . 'wp-admin/includes/schema.php' );
  
  if ( !function_exists( 'populate_roles' ) ) {
  	// Error
  	set_transient( RRAC_TRANSIENT_ERROR, 'RRAC: populate_roles() is required', RRAC_TRANSIENT_TIME );
    return false;
  }

  if ( ! isset( $wp_roles ) ) {

    $wp_roles = new WP_Roles();
  }

  foreach ( $wp_roles->roles as $role_name => $role_info ): 

    /**
     * Fetch a Role definition.
     */
    $role = get_role( $role_name );

    foreach ( $role_info['capabilities'] as $capability => $_):

      /**
       * Removes a capability from a role
       */
      $role->remove_cap( $capability );

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
  
  // Success
  set_transient( RRAC_TRANSIENT_SUCCESS, __('RRAC: Plugin deactivated. Roles and Capabilities reset to WordPress defaults.', 'rrac'), RRAC_TRANSIENT_TIME );

});


/**
 * Show a Message
 */
add_action( 'admin_notices', function () {

	if( $text = get_transient( 	RRAC_TRANSIENT_ERROR ) ){
	 	$class = 'notice notice-error is-dismissible';
		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $text ) ); 
	  delete_transient( RRAC_TRANSIENT_ERROR );
	} elseif( $text = get_transient( 	RRAC_TRANSIENT_SUCCESS ) ){
	 	$class = 'notice notice-success is-dismissible';
		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $text ) ); 
	  delete_transient( RRAC_TRANSIENT_SUCCESS );
	}

} );


/**
 * Deactivate
 */
add_action( 'admin_init', function () { 
	deactivate_plugins( plugin_basename( __FILE__ ) ); 
},0 );
