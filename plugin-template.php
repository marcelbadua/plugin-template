<?php
/*
Plugin Name: Plugin Template
Plugin URI:
Description: Plugin Template
Version: 1.0
Author: Ballistix SPE
Author URI: http://www.ballistix.com
License: GPL2
*/
/*
Copyright 2019  Ballistix SPE  (email : design@ballistix.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


define('PLUGIN_TEMPLATE_PLUGIN_VERSION', '1.0.0')
if(!class_exists('PLUGIN_TEMPLATE_PLUGIN'))
{
	class PLUGIN_TEMPLATE_PLUGIN
	{
		/**
		 * Construct the plugin object
		 */
		public function __construct()
		{
			// Register custom post types
			require_once(sprintf("%s/inc/construct.php", dirname(__FILE__)));
			$PLUGIN_TEMPLATE_PLUGIN_CONSTRUCT = new PLUGIN_TEMPLATE_PLUGIN_CONSTRUCT();

      require_once(sprintf("%s/inc/function.php", dirname(__FILE__)));

		} // END public function __construct

		/**
		 * Activate the plugin
		 */
		public static function activate()
		{
			// Do nothing
		} // END public static function activate

		/**
		 * Deactivate the plugin
		 */
		public static function deactivate()
		{
			// Do nothing
		} // END public static function deactivate

	} // END class PLUGIN_TEMPLATE_PLUGIN
} // END if(!class_exists('PLUGIN_TEMPLATE_PLUGIN'))

if(class_exists('PLUGIN_TEMPLATE_PLUGIN'))
{
	// Installation and uninstallation hooks
	register_activation_hook(__FILE__, array('PLUGIN_TEMPLATE_PLUGIN', 'activate'));
	register_deactivation_hook(__FILE__, array('PLUGIN_TEMPLATE_PLUGIN', 'deactivate'));

	// instantiate the plugin class
	$plugin_template = new PLUGIN_TEMPLATE_PLUGIN();

}
