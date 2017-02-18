<?php
/*
Plugin Name: Custom Share Buttons with Floating Sidebar
Plugin URI: http://www.mrwebsolution.in/
Description: It's a very simple plugin for add to social share buttons on your site and you can publish buttons floating sidebar. Even you can edit share buttons if you wish
Author: MR Web Solution
Author URI: http://raghunathgurjar.wordpress.com
Version: 3.0
*/

/*  Copyright 2015  custom-share-buttons-with-floating-sidebar  (email : raghunath.0087@gmail.com)

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

//Admin "Custom Share Buttons with Floating Sidebar" Menu Item
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if(!class_exists('Csbwfs_Class'))
{
    class Csbwfs_Class
    {
        /**
         * Construct the plugin object
         */
        public function __construct()
        {
            // register actions
			add_action('admin_init', array(&$this, 'csbwfs_admin_init'));
			add_action('admin_menu', array(&$this, 'csbwf_sidebar_menu'));
        } // END public function __construct
		
		/**
		 * hook into WP's admin_init action hook
		 */
		public function csbwfs_admin_init()
		{
			// Set up the settings for this plugin
			$this->csbwf_sidebar_init();
			// Possibly do additional admin_init tasks
		} // END public static function activate
        /**
		 * Initialize some custom settings
		 */     
		public  function csbwf_sidebar_init()
		{
			// register the settings for this plugin
			register_setting('csbwf_sidebar_options','csbwfs_active');
			register_setting('csbwf_sidebar_options','csbwfs_position');
			register_setting('csbwf_sidebar_options','csbwfs_btn_position');
			register_setting('csbwf_sidebar_options','csbwfs_btn_text');
			register_setting('csbwf_sidebar_options','csbwfs_fb_image');
			register_setting('csbwf_sidebar_options','csbwfs_tw_image');
			register_setting('csbwf_sidebar_options','csbwfs_li_image');	
			register_setting('csbwf_sidebar_options','csbwfs_re_image');	
			register_setting('csbwf_sidebar_options','csbwfs_st_image');	
			register_setting('csbwf_sidebar_options','csbwfs_mail_image');	
			register_setting('csbwf_sidebar_options','csbwfs_gp_image');	
			register_setting('csbwf_sidebar_options','csbwfs_pin_image');
			register_setting('csbwf_sidebar_options','csbwfs_yt_image');	
			register_setting('csbwf_sidebar_options','csbwfs_fb_bg');
			register_setting('csbwf_sidebar_options','csbwfs_tw_bg');
			register_setting('csbwf_sidebar_options','csbwfs_li_bg');	
			register_setting('csbwf_sidebar_options','csbwfs_mail_bg');	
			register_setting('csbwf_sidebar_options','csbwfs_gp_bg');	
			register_setting('csbwf_sidebar_options','csbwfs_pin_bg');	
			register_setting('csbwf_sidebar_options','csbwfs_re_bg');	
			register_setting('csbwf_sidebar_options','csbwfs_st_bg');
			register_setting('csbwf_sidebar_options','csbwfs_yt_bg');	
			register_setting('csbwf_sidebar_options','csbwfs_fpublishBtn');	
			register_setting('csbwf_sidebar_options','csbwfs_tpublishBtn');	
			register_setting('csbwf_sidebar_options','csbwfs_gpublishBtn');	
			register_setting('csbwf_sidebar_options','csbwfs_ppublishBtn');	
			register_setting('csbwf_sidebar_options','csbwfs_ytpublishBtn');
			register_setting('csbwf_sidebar_options','csbwfs_republishBtn');
			register_setting('csbwf_sidebar_options','csbwfs_stpublishBtn');	
			register_setting('csbwf_sidebar_options','csbwfs_ytPath');	
			register_setting('csbwf_sidebar_options','csbwfs_lpublishBtn');	
			register_setting('csbwf_sidebar_options','csbwfs_mpublishBtn');	
			register_setting('csbwf_sidebar_options','csbwfs_mailMessage');
			register_setting('csbwf_sidebar_options','csbwfs_top_margin');
			register_setting('csbwf_sidebar_options','csbwfs_delayTimeBtn');
			register_setting('csbwf_sidebar_options','csbwfs_btn_display');
			/** Image Alt */
			register_setting('csbwf_sidebar_options','csbwfs_fb_title');
			register_setting('csbwf_sidebar_options','csbwfs_tw_title');
			register_setting('csbwf_sidebar_options','csbwfs_li_title');
			register_setting('csbwf_sidebar_options','csbwfs_pin_title');
			register_setting('csbwf_sidebar_options','csbwfs_gp_title');
			register_setting('csbwf_sidebar_options','csbwfs_mail_title');
			register_setting('csbwf_sidebar_options','csbwfs_yt_title');
			register_setting('csbwf_sidebar_options','csbwfs_re_title');
			register_setting('csbwf_sidebar_options','csbwfs_st_title');
			register_setting('csbwf_sidebar_options','csbwfs_page_fb_title');
			register_setting('csbwf_sidebar_options','csbwfs_page_tw_title');
			register_setting('csbwf_sidebar_options','csbwfs_page_li_title');
			register_setting('csbwf_sidebar_options','csbwfs_page_pin_title');
			register_setting('csbwf_sidebar_options','csbwfs_page_gp_title');
			register_setting('csbwf_sidebar_options','csbwfs_page_mail_title');
			register_setting('csbwf_sidebar_options','csbwfs_page_yt_title');
			register_setting('csbwf_sidebar_options','csbwfs_page_re_title');
			register_setting('csbwf_sidebar_options','csbwfs_page_st_title');
			register_setting('csbwf_sidebar_options','csbwfs_auto_hide');
			//Options for post/pages
			register_setting('csbwf_sidebar_options','csbwfs_buttons_active');
			register_setting('csbwf_sidebar_options','csbwfs_page_hide_home');
			register_setting('csbwf_sidebar_options','csbwfs_page_hide_post');
			register_setting('csbwf_sidebar_options','csbwfs_page_hide_page');
			register_setting('csbwf_sidebar_options','csbwfs_page_hide_archive');
			register_setting('csbwf_sidebar_options','csbwfs_hide_home');
			register_setting('csbwf_sidebar_options','csbwfs_page_fb_image');
			register_setting('csbwf_sidebar_options','csbwfs_page_tw_image');
			register_setting('csbwf_sidebar_options','csbwfs_page_li_image');	
			register_setting('csbwf_sidebar_options','csbwfs_page_mail_image');	
			register_setting('csbwf_sidebar_options','csbwfs_page_gp_image');	
			register_setting('csbwf_sidebar_options','csbwfs_page_pin_image');
			register_setting('csbwf_sidebar_options','csbwfs_page_re_image');
			register_setting('csbwf_sidebar_options','csbwfs_page_st_image');
			register_setting('csbwf_sidebar_options','csbwfs_page_yt_image');
			/** message content */	
			register_setting('csbwf_sidebar_options','csbwfs_show_btn');	
			register_setting('csbwf_sidebar_options','csbwfs_hide_btn');	
			register_setting('csbwf_sidebar_options','csbwfs_share_msg');
			register_setting('csbwf_sidebar_options','csbwfs_rmSHBtn');	
			register_setting('csbwf_sidebar_options','csbwfs_featuredshrimg');	
			register_setting('csbwf_sidebar_options','csbwfs_defaultfeaturedshrimg');
			register_setting('csbwf_sidebar_options','csbwfs_deactive_for_mob');
		} // END public function init_custom_settings()
		/**
		 * add a menu
		 */     
		public function csbwf_sidebar_menu()
		{
			add_options_page('Custom Share Buttons With Floating Sidebar','Custom Share Buttons With Floating Sidebar','manage_options','csbwfs-settings',array(&$this,'csbwf_sidebar_admin_option_page'));

		} // END public function add_menu()

		public function csbwf_sidebar_admin_option_page()
				{
					if(!current_user_can('manage_options'))
					{
						wp_die(__('You do not have sufficient permissions to access this page.'));
					}

					// Render the settings template
					include(sprintf("%s/lib/settings.php", dirname(__FILE__)));
					/** 
					 * REGISTER SCRIPT
					 * */
					 wp_enqueue_script('media-upload');
					 wp_enqueue_script('thickbox');
					 wp_register_script('csbwfs-image-upload', plugins_url('/js/csbwfs.js',__FILE__ ), array('jquery','media-upload','thickbox','wp-color-picker'));
					 wp_enqueue_script('csbwfs-image-upload');
					/** 
					 * REGISTER STYLE
					 * */
					wp_register_style( 'csbwf_admin_style', plugins_url( 'css/admin-csbwfs.css',__FILE__ ) );
					wp_enqueue_style( 'csbwf_admin_style' );
					wp_enqueue_style( 'wp-color-picker' ); 
					wp_enqueue_style('thickbox');

			 }// END public static function csbwf_sidebar_admin_option_page
        /**
		 * hook into WP's plugin_action_links_ action hook
		 */
      public static function csbwfs_add_settings_link( $links ) {
            $settings_link = '<a href="options-general.php?page=csbwfs-settings">' . __( 'Settings', 'csbwfs' ) . '</a>';
            array_unshift( $links, $settings_link );
            return $links;
        }
        /**
         * uninstall the plugin
         */
        public function csbwfs_uninstall()
        {
			delete_option('csbwfs_active');
			delete_option('csbbuttons_active');
			delete_option('csbwfs_position');
			delete_option('csbwfs_btn_position');
			delete_option('csbwfs_btn_text');
			delete_option('csbwfs_fb_image');
			delete_option('csbwfs_tw_image');
			delete_option('csbwfs_li_image');
			delete_option('csbwfs_re_image');
			delete_option('csbwfs_st_image');
			delete_option('csbwfs_mail_image');
			delete_option('csbwfs_gp_image');
			delete_option('csbwfs_pin_image');
			delete_option('csbwfs_yt_image');
			delete_option('csbwfs_re_image');
			delete_option('csbwfs_st_image');	
			delete_option('csbwfs_ytPath');
			delete_option('csbwfs_fb_bg');
			delete_option('csbwfs_tw_bg');
			delete_option('csbwfs_li_bg');
			delete_option('csbwfs_mail_bg');
			delete_option('csbwfs_gp_bg');
			delete_option('csbwfs_pin_bg');	
			delete_option('csbwfs_yt_bg');
			delete_option('csbwfs_fpublishBtn');
			delete_option('csbwfs_tpublishBtn');
			delete_option('csbwfs_gpublishBtn');	
			delete_option('csbwfs_ppublishBtn');	
			delete_option('csbwfs_lpublishBtn');	
			delete_option('csbwfs_mpublishBtn');	
			delete_option('csbwfs_republishBtn');	
			delete_option('csbwfs_stpublishBtn');
			delete_option('csbwfs_ytpublishBtn');	
			delete_option('csbwfs_mailMessage');
			delete_option('csbwfs_top_margin');
			delete_option('csbwfs_page_hide_home');
			delete_option('csbwfs_page_hide_post');
			delete_option('csbwfs_page_hide_page');
			delete_option('csbwfs_fb_title');
			delete_option('csbwfs_tw_title');
			delete_option('csbwfs_li_title');
			delete_option('csbwfs_pin_title');
			delete_option('csbwfs_gp_title');
			delete_option('csbwfs_mail_title');
			delete_option('csbwfs_yt_title');
			delete_option('csbwfs_re_title');
			delete_option('csbwfs_st_title');
			delete_option('csbwfs_page_fb_image');
			delete_option('csbwfs_page_tw_image');
			delete_option('csbwfs_page_li_image');	
			delete_option('csbwfs_page_re_image');	
			delete_option('csbwfs_page_st_image');	
			delete_option('csbwfs_page_mail_image');	
			delete_option('csbwfs_page_gp_image');	
			delete_option('csbwfs_page_pin_image');		
			delete_option('csbwfs_page_yt_image');	
			delete_option('csbwfs_rmSHBtn');
			delete_option('csbwfs_featuredshrimg');	
			delete_option('csbwfs_defaultfeaturedshrimg');
			delete_option('csbwfs_deactive_for_mob');
            // Do nothing
        } // END public static function uninstall
        /**
         * Activate the plugin
         */
        public static function csbwfs_activate()
        {
            // Do nothing
        } // END public static function activate
    
        /**
         * Deactivate the plugin
         */     
        public static function csbwfs_deactivate()
        {
            // Do nothing
        } // END public static function deactivate
		
    } // END class Csbwfs_Class
} // END if(!class_exists('Csbwfs_Class'))

if(class_exists('Csbwfs_Class'))
{
   // Installation and uninstallation hooks
   register_activation_hook(__FILE__, array('Csbwfs_Class', 'csbwfs_activate'));
   register_deactivation_hook(__FILE__, array('Csbwfs_Class', 'csbwfs_deactivate'));
   register_uninstall_hook(__FILE__, array('Csbwfs_Class', 'csbwfs_uninstall')); 
    // instantiate the plugin class
    $csbwfs_plugin_template = new Csbwfs_Class();
	// Add a link to the settings page onto the plugin page
	if(isset($csbwfs_plugin_template))
	{
		$plugin = plugin_basename(__FILE__); 
		add_filter("plugin_action_links_$plugin", array('Csbwfs_Class','csbwfs_add_settings_link'));
	    require dirname(__FILE__).'/csbwfs-class.php';
	}
	
	
}
?>
