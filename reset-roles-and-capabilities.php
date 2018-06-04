<?php
/*
 * Plugin Name: Reset Roles and Capabilities to WordPress defaults.  
 * Plugin URI: https://francescotaurino.com
 * Description: Reset Roles and Capabilities to WordPress defaults. 
 * Author: Francesco Taurino
 * Version: 0.1
 * Text Domain: rrac
 * Domain Path: languages
 * Author URI: https://www.francescotaurino.com
 * Compatibility: WordPress 2.0.0
 */
/*

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


/**
* add_action('init', 'ResetRolesAndCapabilitiesToWordPressDefault' );
*/
function ResetRolesAndCapabilitiesToWordPressDefault(){

  require_once( ABSPATH . 'wp-admin/includes/schema.php' );
  if ( !function_exists( 'populate_roles' ) ) {
    return false;
  }

  if ( !function_exists( 'wp_roles' ) ) {
    return false;
  }

  if ( !function_exists( 'get_role' ) ) {
    return false;
  }

  global $wp_roles;

  if ( ! isset( $wp_roles ) ) {

    /**
     * WP_Roles
     * @since Wp 2.0.0
     */
    $wp_roles = new WP_Roles();
  }

  /**
   * Fetch roles
   * 
   * Alternative: @ since Wp 4.3.0 $all_roles = wp_roles()->roles;
   * Alternative: @ since Wp 2.8.0 $all_roles = get_editable_roles();
   */
  $all_roles = $wp_roles->roles;
  
  foreach ( $all_roles as $role_name => $role_info ): 


    /**
     * Fetch a Role definition.
     * get_role returns an instance of WP_Role. 
     * https://codex.wordpress.org/Function_Reference/get_role
     */
    $role = get_role( $role_name );

    foreach ( $role_info['capabilities'] as $capability => $_):

      /**
       * Removes a capability from a role
       * https://codex.wordpress.org/Function_Reference/remove_cap
       */
      $role->remove_cap( $capability );

    endforeach;

    /**
     * Remove role, if it exists.
     * @since Wp 2.0.0
     */
    remove_role($role_name);

  endforeach;


  /**
   * Execute WordPress role creation for the various WordPress versions.
   * @since Wp 2.0.0
   */
  populate_roles();
  
}
