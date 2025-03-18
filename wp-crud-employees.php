<?php

/**
 * Plugin Name: WP CRUD Employees
 * Plugin URI: http://example.com
 * Description: This plugin performs CRUD operations with Employees Table. Also on Activation it will create a dynamic wordpress page and it will have a shortcode.
 * Version: 1.0.0
 * Author: Firstname Lastname
 * Author URI: https://github.com/brunocunha
 */

 if(!defined("ABSPATH")) {
    exit;
 }

 define("WCE_DIR_PATH", plugin_dir_path(__FILE__));
 define("WCE_DIR_URL", plugin_dir_url(__FILE__));

 include_once(WCE_DIR_PATH . "/inc/MyEmployees.php");

 // create class object

 $employeeObject = new MyEmployees();

//  create db table
register_activation_hook(__FILE__, [$employeeObject, "callPluginActivationFunction"]);

// drop db table
register_deactivation_hook(__FILE__, [$employeeObject, "dropEmployeesTable"]);

// register shortcode
add_shortcode("wp-employee-form", [$employeeObject, "createEmployeeForm"]);
// add assets
add_action("wp_enqueue_scripts", [$employeeObject, "addAssetsToPlugin"]);
