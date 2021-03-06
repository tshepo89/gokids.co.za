<?php
/**
 * Plugin Name: face2 Two Factor Authentication
  * Description: Add <a href="https://face2.in/">face2</a> two-factor authentication to WordPress.
 * Author: face2
 * Version: 1.0.1
 * Author URI: https://www.face2.in
 * License: GPL2+
 * Text Domain: face2

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

require_once 'helpers.php';

class face2 {
    /**
     * Class variables
     */
    // Oh look, a singleton
    private static $__instance = null;

    // Some plugin info
    protected $name = 'face2 Two-Factor Authentication';

    // Parsed settings
    private $settings = null;

    // Is API ready, should plugin act?
    protected $ready = false;

    // face2 API
    protected $api = null;
    protected $api_key = null;
    protected $api_endpoint = null;

    // Interface keys
    protected $settings_page = 'face2';
    protected $users_page = 'face2-user';

    // Data storage keys
    protected $settings_key = 'face2';
    protected $users_key = 'face2_user';
    protected $signature_key = 'user_signature';
    protected $face2_data_temp_key = 'face2_data_temp';

    // Settings field placeholders
    protected $settings_fields = array();

    protected $settings_field_defaults = array(
        'label'    => null,
        'type'     => 'text',
        'sanitizer' => 'sanitize_text_field',
        'section'  => 'default',
        'class'    => null,
    );

    // Default face2 data
    protected $user_defaults = array(
        'email'        => null,
        'phone'        => null,
        'pin_code' => '0',
        'face2_id'     => null,
        
    );

    /**
     * Singleton implementation
     *
     * @uses this::setup
     * @return object
     */
    public static function instance() {
        if( ! is_a( self::$__instance, 'face2' ) ) {
            self::$__instance = new face2;
            self::$__instance->setup();
        }

        return self::$__instance;
    }

    /**
     * Silence is golden.
     */
    private function __construct() {}

    /**************************************************
     * START WORDPRESS METHODS
     **************************************************/

    /**
     * Plugin setup
     *
     * @uses this::register_settings_fields, this::prepare_api, add_action, add_filter
     * @return null
     */
    private function setup() {
        require( 'face2-api.php' );

        $this->register_settings_fields();
        $this->prepare_api();

        // Plugin settings
        add_action( 'admin_init', array( $this, 'action_admin_init' ) );
        add_action( 'admin_menu', array( $this, 'action_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'action_admin_enqueue_scripts' ) );
 

        add_filter( 'plugin_action_links', array( $this, 'filter_plugin_action_links' ), 10, 2 );

        // Anything other than plugin configuration belongs in here.
        if ( $this->ready ) {
            // User settings
            add_action( 'show_user_profile', array( $this, 'action_show_user_profile' ) );
            add_action( 'edit_user_profile', array( $this, 'action_edit_user_profile' ) );
            add_action( 'wp_ajax_' . $this->users_page, array( $this, 'get_user_modal_via_ajax' ) );

            add_action( 'personal_options_update', array( $this, 'action_personal_options_update' ) );
            add_action( 'edit_user_profile_update', array( $this, 'action_edit_user_profile_update' ) );
            add_filter( 'user_profile_update_errors', array( $this, 'register_user_and_check_errors' ), 10, 3 );

            // Authentication
            add_filter( 'authenticate', array( $this, 'authenticate_user' ), 10, 3 );

            // Disable XML-RPC
            if ( $this->get_setting( 'disable_xmlrpc' ) == "true") {
                add_filter( 'xmlrpc_enabled', '__return_false' );
            }

            // Display notices
            add_action( 'admin_notices', array( $this, 'action_admin_notices' ) );

            // Enable the user with no privileges to run action_request_sms() in AJAX
            add_action( 'wp_ajax_nopriv_request_sms_ajax', array( $this, 'request_sms_ajax' ) );
            add_action( 'wp_ajax_request_sms_ajax', array( $this, 'request_sms_ajax' ) );
        }
    }

    /**
     * Add settings fields for main plugin page
     *
     * @uses __
     * @return null
     */
    protected function register_settings_fields() {
        $this->settings_fields = array(
		  array(
                'name'      => 'api_url',
                'label'     => __( 'face2  API Host', 'face2' ),
                'type'      => 'text',
               
            ),
			
            array(
                'name'      => 'api_key_production',
                'label'     => __( 'face2  API Key', 'face2' ),
                'type'      => 'text',
                'sanitizer' => 'alphanumeric',
            ),
            array(
                'name'      => 'disable_xmlrpc',
                'label'     => __( "Disable external apps that don't support Two-factor Authentication", 'face2_wp' ),
                'type'      => 'checkbox',
                'sanitizer' => null,
            ),
			array(
                'name'      => 'match_percentage',
                'label'     => __( "Match percentage to allow logins", 'face2_wp' ),
                 'type'      => 'text',
                'sanitizer' => 'alphanumeric',
            ),
        );
    }

    /**
     * Set class variables regarding API
     * Instantiates the face2 API class into $this->api
     *
     * @uses this::get_setting, face2_WP_API::instance
     */
    protected function prepare_api() {
        $endpoints = array(
            'production'  => 'https://api.face2.in/rest/',
        );

        $api_key = $this->get_setting( 'api_key_production' );
		
        // Only prepare the API endpoint if we have all information needed.
        if ( $api_key && isset( $endpoints['production'] ) ) {
            $this->api_key = $api_key;
            $this->api_endpoint = $this->get_setting( 'api_url' );

            $this->ready = true;
        }

        // Instantiate the API class
        $this->api = face2_API::instance( $this->api_key, $this->api_endpoint );
    }

    /**
     * Register plugin's setting and validation callback
     *
     * @param action admin_init
     * @uses register_setting
     * @return null
     */
    public function action_admin_init() {
        register_setting( $this->settings_page, $this->settings_key, array( $this, 'validate_plugin_settings' ) );
        register_setting( $this->settings_page, 'face2_roles', array( $this, 'select_only_system_roles' ) );
    }

    /**
     * Register plugin settings page and page's sections
     *
     * @uses add_options_page, add_settings_section
     * @action admin_menu
     * @return null
     */
    public function action_admin_menu() {
        $show_settings = false;
        $can_admin_network = is_plugin_active_for_network( 'face2-two-factor-authentication/face2.php' ) && current_user_can( 'network_admin' );

        if ( $can_admin_network || current_user_can( 'manage_options' ) ) {
            $show_settings = true;
        }

        if ( $show_settings ) {
            add_options_page( $this->name, 'face2', 'manage_options', $this->settings_page, array( $this, 'plugin_settings_page' ) );
            add_settings_section( 'default', '', array( $this, 'register_settings_page_sections' ), $this->settings_page );
        }
    }

    /**
     * Enqueue admin script for connection modal
     *
     * @uses get_current_screen, wp_enqueue_script, plugins_url, wp_localize_script, this::get_ajax_url, wp_enqueue_style
     * @action admin_enqueue_scripts
     * @return null
     */
    public function action_admin_enqueue_scripts() {
        if ( ! $this->ready ) {
            return;
        }

        global $current_screen;

        if ( $current_screen->base === 'profile' ) {
            wp_enqueue_script( 'face2-profile', plugins_url( 'assets/face2-profile.js', __FILE__ ), array( 'jquery', 'thickbox' ), 1.01, true );
            wp_enqueue_script( 'form-face2-js', 'http://www.face2.in/form.face2.min.js', array(), false, true );
            wp_localize_script( 'face2-profile', 'face2', array(
                'ajax' => $this->get_ajax_url(),
                'th_text' => __( 'Two-Factor Authentication', 'face2' ),
                'button_text' => __( 'Enable/Disable face2', 'face2' ),
            ) );

            wp_enqueue_style( 'thickbox' );
            wp_enqueue_style( 'form-face2-css', 'http://www.face2.in/form.face2.min.css', array(), false, 'screen' );
        } elseif ( $current_screen->base === 'user-edit' ) {
            wp_enqueue_script( 'form-face2-js', 'http://www.face2.in/form.face2.min.js', array(), false, true );
            wp_enqueue_style( 'form-face2-css', 'http://www.face2.in/form.face2.min.css', array(), false, 'screen' );
        }
    }

    /**
     * Add settings link to plugin row actions
     *
     * @param array $links
     * @param string $plugin_file
     * @uses menu_page_url, __
     * @filter plugin_action_links
     * @return array
     */
    public function filter_plugin_action_links( $links, $plugin_file ) {
        if ( strpos( $plugin_file, pathinfo( __FILE__, PATHINFO_FILENAME ) ) !== false ) {
            $links['settings'] = '<a href="options-general.php?page=' . $this->settings_page . '">' . __( 'Settings', 'face2' ) . '</a>';
        }

        return $links;
    }

    /**
    * Display an admin notice when the server doesn't installed a cert bundle.
    */
    public function action_admin_notices() {
        $response = $this->api->curl_ca_certificates();
        if ( is_string( $response ) ) {
             ?><div id="message" class="error"><p><strong>Error:</strong><?php echo $response;  ?></p></div><?php
        }
    }

    /**
     * Retrieve a plugin setting
     *
     * @param string $key
     * @uses get_option, wp_parse_args, apply_filters
     * @return array or false
     */
    public function get_setting( $key ) {
        $value = false;

        if ( is_null( $this->settings ) || !is_array( $this->settings ) ) {
            $this->settings = get_option( $this->settings_key );
            $this->settings = wp_parse_args( $this->settings, array(
                'api_key_production'  => '',
                'environment'         => apply_filters( 'face2_environment', 'production' ),
                'disable_xmlrpc'      => "true",
				'match_percentage'      => "90",
            ) );
        }

        if ( isset( $this->settings[ $key ] ) ) {
            $value = $this->settings[ $key ];
        }

        return $value;
    }

    /**
     * Build Ajax URL for users' connection management
     *
     * @uses add_query_arg, wp_create_nonce, admin_url
     * @return string
     */
    protected function get_ajax_url() {
        return add_query_arg( array(
            'action' => $this->users_page,
            'nonce' => wp_create_nonce( $this->users_key . '_ajax' ),
        ), admin_url( 'admin-ajax.php' ) );
    }

    /**************************************************
     * START face2 PLUGIN METHODS
     **************************************************/

    /**
    * Check if Two factor authentication is available for role
    * @param object $user
    * @uses wp_roles, get_option
    * @return boolean
    *
    */
    public function available_face2_for_role( $user ) {
        global $wp_roles;
        $wordpress_roles = $wp_roles->get_names();
        $face2_roles = get_option( 'face2_roles', $wordpress_roles );

        foreach ( $user->roles as $role ) {
            if ( array_key_exists( $role, $face2_roles ) ) {
                return true;
            }
        }
        return false;
    }

    /**
     * GENERAL OPTIONS PAGE
     */

    /**
     * Populate settings page's sections
     *
     * @uses add_settings_field
     * @return null
     */
    public function register_settings_page_sections() {
	
	add_settings_field( 'api_url', __( 'face2 API Host', 'face2' ), array( $this, 'add_settings_api_url' ), $this->settings_page, 'default' );
	
	
        add_settings_field( 'api_key_production', __( 'face2  API Key', 'face2' ), array( $this, 'add_settings_api_key' ), $this->settings_page, 'default' );
        add_settings_field( 'face2_roles', __( 'Allow face2 for the following roles', 'face2' ), array( $this, 'add_settings_for_roles' ), $this->settings_page, 'default' );
        add_settings_field( 'disable_xmlrpc', __( "Disable external apps that don't support Two-factor Authentication", 'face2' ), array( $this, 'add_settings_disable_xmlrpc' ), $this->settings_page, 'default' );
add_settings_field( 'match_percentage', __( "Allow login if match percentage is higher than this value: ", 'face2' ), array( $this, 'add_settings_match_percentage' ), $this->settings_page, 'default' );
		}

    /**
     * Render settings api key
     *
     * @uses this::get_setting, esc_attr
     * @return string
     */
	 
	 public function add_settings_api_url() {
        $value = $this->get_setting( 'api_url' );
		if ($value=="") { $value="https://api.face2.in/rest/"; }
         ?>
            <input type="text" name="<?php echo esc_attr( $this->settings_key );  ?>[api_url]"
              class="regular-text" id="field-api_url" value="<?php echo esc_attr( $value );  ?>" />
        <?php
    }
	
	
    public function add_settings_api_key() {
        $value = $this->get_setting( 'api_key_production' );
         ?>
            <input type="text" name="<?php echo esc_attr( $this->settings_key );  ?>[api_key_production]"
              class="regular-text" id="field-api_key_production" value="<?php echo esc_attr( $value );  ?>" />
        <?php
    }

    /**
    * Render settings roles
    * @uses $wp_roles
    * @return string
    */
    public function add_settings_for_roles() {
        global $wp_roles;

        $roles = $wp_roles->get_names();
        $roles_to_list = array();

        foreach ( $roles as $key => $role ) {
            $roles_to_list[before_last_bar( $key )] = before_last_bar( $role );
        }

        $selected = get_option( 'face2_roles', $roles_to_list );

        foreach ( $wp_roles->get_names() as $role ) {
            $checked = in_array( before_last_bar( $role ), $selected );
            $role_name = before_last_bar( $role );
            // html block
             ?>
                <input name='face2_roles[<?php echo esc_attr( strtolower( $role_name ) );  ?>]' type='checkbox'
                  value='<?php echo esc_attr( $role_name );  ?>'<?php if ( $checked ) echo 'checked="checked"';  ?> /><?php echo esc_attr( $role_name );  ?></br>
            <?php
        }
    }

    /**
    * Render settings disable XMLRPC
    *
    * @return string
    */
    public function add_settings_disable_xmlrpc() {
        if ( $this->get_setting( 'disable_xmlrpc' ) == "false" ) {
            $value = false;
        } else {
            $value = true;
        }

		
         ?>
            <label for='<?php echo esc_attr( $this->settings_key );  ?>[disable_xmlrpc]'>
                <input name="<?php echo esc_attr( $this->settings_key );  ?>[disable_xmlrpc]" type="checkbox" value="true" <?php if ($value) echo 'checked="checked"';  ?> >
                <span style='color: #bc0b0b;'><?php _e( 'Ensure Two-factor authentication is always respected.' , 'face2' );  ?></span>
            </label>
            <p class ='description'><?php _e( "WordPress mobile app's don't support Two-Factor authentication. If you disable this option you will be able to use the apps but it will bypass Two-Factor Authentication.", 'face2' );  ?></p>
        <?php
    }
	
	 public function add_settings_match_percentage() {
        if ( intval($this->get_setting( 'match_percentage' ))==0  ) {
            $value = 90;
        } else {
            $value = $this->get_setting( 'match_percentage' );
        }

		
         ?>  
            <label for='<?php echo esc_attr( $this->settings_key );  ?>[match_percentage]'>
                <input name="<?php echo esc_attr( $this->settings_key );  ?>[match_percentage]" type="number" value="<?php echo $value;  ?>" >
                <span style='color: #bc0b0b;'><?php _e( 'Face match percentage required to allow login ' , 'face2' );  ?></span>
            </label>
            <p class ='description'><?php _e( "Do not set too low. Check demo on our website to get a comfortable percentage. ", 'face2' );  ?></p>
			<br>
        <?php
    }

    /**
     * Render settings page
     *
     * @uses screen_icon, esc_html, get_admin_page_title, settings_fields, do_settings_sections
     * @return string
     */

    public function plugin_settings_page() {
        $plugin_name = esc_html( get_admin_page_title() );
         ?>
            <div class="wrap">
              <?php screen_icon();  ?>
              <h2><?php echo esc_attr( $plugin_name );  ?></h2>

              <?php if ( $this->ready ) :
                  $details = $this->api->application_details();
               ?>
              <p><?php _e( 'Enter your face2 API key (get one on www.face2.in). You can select which users can enable face2 by their WordPress role. Users can then enable face2 on their individual accounts by visting their user profile pages.', 'face2' );  ?></p>
            

              <?php else :   ?>
                  <p><?php printf( __( 'To use the face2 service, you must register an account at <a href="%1$s"><strong>%1$s</strong></a> and create site to get its face2 API.', 'face2' ), 'https://www.face2.in' );  ?></p>
                  <p><?php _e( "Once you've created your site, enter your API key in the field below.", 'face2' );  ?></p>
                  <p><?php printf( __( 'Until your API keys are entered, the %s plugin cannot function.', 'face2' ), $plugin_name );  ?></p>
              <?php endif;  ?>

              <form action="options.php" method="post">
                  <?php settings_fields( $this->settings_page );  ?>
                  <?php do_settings_sections( $this->settings_page );  ?>

                  <p class="submit">
                      <input name="Submit" type="submit" value="<?php esc_attr_e( 'Save Changes' ); ?>" class="button-primary">
                  </p>
              </form>

              <?php if ( !empty( $details ) ) {  ?>
                <h2>Application Details</h2>

                <table class='widefat' style="width:400px;">
                    <tbody>
                        <tr>
                            <th><?php printf( __( 'Application name', 'face2' ) );  ?></th>
                            <td><?php print esc_attr( $details['app']->name );  ?></td>
                        </tr>
                        <tr>
                            <th><?php printf( __( 'Plan', 'face2' ) );  ?></th>
                            <td><?php print esc_attr( ucfirst( $details['app']->plan ) );  ?></td>
                        </tr>
                    </tbody>
                </table>

                <?php if ( $details['app']->plan === 'sandbox' ) {  ?>
                 
                <?php }
              } ?>
            </div>
        <?php
    }

    /**
     * Validate plugin settings
     *
     * @param array $settings
     * @uses check_admin_referer, wp_parse_args, sanitize_text_field
     * @return array
     */
    public function validate_plugin_settings( $settings ) {
        check_admin_referer( $this->settings_page . '-options' );

        $settings_validated = array();

        foreach ( $this->settings_fields as $field ) {
            $field = wp_parse_args( $field, $this->settings_field_defaults );

            if ( !isset( $settings[ $field['name'] ] ) && $field['type'] != 'checkbox' ) {
                continue;
            }

            if ( $field['type'] === "text" && $field['sanitizer'] === 'alphanumeric' ) {
                $value = preg_replace( '#[^a-z0-9]#i', '', $settings[ $field['name' ] ] );
            } elseif ( $field['type'] == "checkbox" ) {
                $value = $settings[ $field['name'] ];

                if ( $value != "true" ) {
                    $value = "false";
                }
            } else {
                $value = sanitize_text_field( $settings[ $field['name'] ] );
            }

            if ( isset( $value ) && !empty( $value ) ) {
                $settings_validated[ $field['name'] ] = $value;
            }
        }
        return $settings_validated;
    }

    /**
    * Select the system roles present in $roles
    * @param array $roles
    * @uses $wp_roles
    * @return array
    */
    public function select_only_system_roles( $roles ) {
        if ( !is_array( $roles ) || empty( $roles ) ) {
            return array();
        }

        global $wp_roles;
        $system_roles = $wp_roles->get_names();

        foreach ( $roles as $role ) {
            if ( !in_array( $roles, $system_roles ) ) {
                unset( $roles[$role] );
            }
        }

        return $roles;
    }

    /**
    * USER SETTINGS PAGES
    */

    /**
     * Non-JS connection interface
     *
     * @param object $user
     * @uses this::get_face2_data, esc_attr,
     */
    public function action_show_user_profile( $user ) {
        $meta = $this->get_face2_data( $user->ID );

        if ( $this->user_has_face2_id( $user->ID ) ) {
            if ( !$this->with_forced_by_admin( $user->ID ) ) {
                 ?>
                    <h3><?php echo esc_html( $this->name );  ?></h3>
                <?php
                echo disable_form_on_profile( $this->users_key );
            }
        } elseif ( $this->available_face2_for_role( $user ) ) {
             ?>
                <h3><?php echo esc_html( $this->name );  ?></h3>
            <?php
            echo register_form_on_profile( $this->users_key, $meta );
        }
    }

    /**
     * USER INFORMATION FUNCTIONS
     */

    public function register_face2_user( $user_params = array() ) {
	 
        foreach( array( "user_id", "email", "phone", "pin_code"  ) as $required_field ) {
            if ( !isset( $user_params[$required_field] ) ) {
                assert("Missing field : ".$required_field);
                return false;
            }
        }

        $response = $this->api->register_user( $user_params['email'], $user_params['phone'], $user_params['pin_code'] );
		
        if ( $response->userid ) {
          $user_params["face2_id"] = $response->userid;
          return $this->set_face2_data( $user_params );
        }

        return false;
    }

    /**
     * Add face2 data to a given user account
     *
     * @param int $user_id
     * @param array $face2_data
     * @uses this::user_has_face2_id, this::api::get_id, wp_parse_args, this::clear_face2_data, get_user_meta, update_user_meta
     * @return null
     */
    public function set_face2_data( $face2_data = array() ) {
        if(!isset($face2_data["user_id"])) {
            assert("Missing field : user_id");
            return;
        }

        // Retrieve user's existing face2 ID
        if ( $this->user_has_face2_id( $face2_data["user_id"] ) ) {
            $face2_data["face2_id"] = $this->get_user_face2_id( $face2_data["user_id"] );
        }

        if(!isset($face2_data["face2_id"]) ) {
            error_log("face2 id was not given when registering the user.");
            return false;
        }

        $data = get_user_meta( $face2_data['user_id'], $this->users_key, true );
        if ( ! is_array( $data ) ) {
            $data = array();
        }

        $data_sanitized = array();
        foreach ( array( 'email', 'phone', 'pin_code', 'face2_id'  ) as $attr ) {
            if ( isset( $face2_data[ $attr ] ) ) {
                $data_sanitized[ $attr ] = $face2_data[ $attr ];
            } elseif ( isset( $data[ $attr ] ) ) {
                $data_sanitized[ $attr ] = $data[ $attr ];
            }
        }

        $data_sanitized = wp_parse_args( $data_sanitized, $this->user_defaults );
        $data[ $this->api_key ] = $data_sanitized;
        update_user_meta( $face2_data['user_id'], $this->users_key, $data );
        return true;
    }

    /**
     * Retrieve a user's face2 data
     *
     * @param int $user_id
     * @uses get_user_meta, wp_parse_args
     * @return array
     */
    protected function get_face2_data( $user_id ) {
        // Bail without a valid user ID
        if ( ! $user_id ) {
            return $this->user_defaults;
        }

        // Get meta, which holds all face2 data by API key
        $data = get_user_meta( $user_id, $this->users_key, true );
        if ( ! is_array( $data ) ) {
            $data = array();
        }

        // Return data for this API, if present, otherwise return default data
        if ( array_key_exists( $this->api_key, $data ) ) {
            return wp_parse_args( $data[ $this->api_key ], $this->user_defaults );
        }

        return $this->user_defaults;
    }

    /**
     * Delete any stored face2 connections for the given user.
     * Expected usage is somewhere where clearing is the known action.
     *
     * @param int $user_id
     * @uses delete_user_meta
     * @return null
     */
    protected function clear_face2_data( $user_id ) {
        delete_user_meta( $user_id, $this->users_key );
    }

    /**
     * Check if a given user has an face2 ID set
     *
     * @param int $user_id
     * @uses this::get_user_face2_id
     * @return bool
     */
    protected function user_has_face2_id( $user_id ) {
        return (bool) $this->get_user_face2_id( $user_id );
    }

    /**
     * Retrieve a given user's face2 ID
     *
     * @param int $user_id
     * @uses this::get_face2_data
     * @return int|null
     */
    protected function get_user_face2_id( $user_id ) {
        $data = $this->get_face2_data( $user_id );

        if ( is_array( $data ) && is_numeric( $data['face2_id'] ) ) {
            return (int) $data['face2_id'];
        }

        return null;
    }

    /**
    * Check if a given user has Two factor authentication forced by admin
    * @param int $user_id
    * @uses this::get_face2_data
    * @return bool
    *
    */
    protected function with_forced_by_admin( $user_id ) {
        $data = $this->get_face2_data( $user_id );

        if ( $data['force_by_admin'] == 'true' ) {
            return true;
        }

        return false;
    }

    /**
     * Handle non-JS changes to users' own connection
     *
     * @param int $user_id
     * @uses check_admin_referer, wp_verify_nonce, get_userdata, is_wp_error, this::register_face2_user, this::clear_face2_data,
     * @return null
     */
    public function action_personal_options_update( $user_id ) {
        check_admin_referer( 'update-user_' . $user_id );

        // Check if we have data to work with
        $face2_data = isset( $_POST[ $this->users_key ] ) ? $_POST[ $this->users_key ] : false;

        // Parse for nonce and API existence
        if ( !is_array( $face2_data ) || !array_key_exists( 'nonce', $face2_data ) ) {
            return;
        }

        $is_editing = wp_verify_nonce( $face2_data['nonce'], $this->users_key . 'edit_own' );
        $is_disabling = wp_verify_nonce( $face2_data['nonce'], $this->users_key . 'disable_own' ) && isset( $face2_data['disable_own'] );

        if ( $is_editing ) {
            // Email address
            $userdata = get_userdata( $user_id );
            if ( is_object( $userdata ) && ! is_wp_error( $userdata ) ) {
                $email = $userdata->data->user_email;
            } else {
                $email = null;
            }

            // Phone number
            $phone = preg_replace( '#[^\d]#', '', $face2_data['phone'] );
            $pin_code = preg_replace( '#[^\d\+]#', '', $face2_data['pin_code'] );

            // Process information with face2
            $this->register_face2_user(array(
                "user_id" => $user_id,
                "email" => $email,
                "phone" => $phone,
                "pin_code" => $pin_code,
                "force_by_admin" => false,
            ));
        } elseif ( $is_disabling ) {
            // Delete face2 usermeta if requested
            $this->clear_face2_data( $user_id );
        }
    }

    /**
     * Allow sufficiently-priviledged users to disable another user's face2 service.
     *
     * @param object $user
     * @uses current_user_can, this::user_has_face2_id, get_user_meta, wp_parse_args, esc_attr, wp_nonce_field
     * @action edit_user_profile
     * @return string
     */
    public function action_edit_user_profile( $user ) {
        if ( !current_user_can( 'create_users' ) ) {
            return;
        }

         ?>
            <h3>face2 Two-factor Authentication</h3>

            <table class="form-table">
                <?php
                if ( $this->user_has_face2_id( $user->ID ) ) :
                    $meta = get_user_meta( get_current_user_id(), $this->users_key, true );
                    $meta = wp_parse_args( $meta, $this->user_defaults );

                    checkbox_for_admin_disable_face2( $this->users_key );
                    wp_nonce_field( $this->users_key . '_disable', "_{$this->users_key}_wpnonce" );
                else :
                    $face2_data = $this->get_face2_data( $user->ID );
                    render_admin_form_enable_face2( $this->users_key, $face2_data );
                endif;
                 ?>
            </table>
        <?php
    }

    /**
    * Add errors when editing another user's profile
    *
    */
    public function register_user_and_check_errors( &$errors, $update, &$user ) {
        if( !$update || empty( $_POST['face2_user']['phone'] ) ) {
            // ignore if it's not updating an face2 user.
            return;
			
        }

        $response = $this->api->register_user( $_POST['email'], $_POST['face2_user']['phone'], $_POST['face2_user']['pin_code'] );
        
		if ( !empty( $response->errors ) ) {
		
            foreach ( $response->errors as $attr => $message ) {
                if ( $attr == 'pin_code' ) {
                    $errors->add( 'face2_error', '<strong>Error:</strong> ' . 'face2 pin code is invalid' );
                } elseif ( $attr != 'message' ) {
                    $errors->add( 'face2_error', '<strong>Error:</strong> ' . 'face2 ' . $attr . ' ' . $message );
                }
            }
        }
    }

    /**
    * Print head element
    *
    * @uses wp_print_scripts, wp_print_styles
    * @return @string
    */
    public function ajax_head() {
         ?>
            <head>
                <?php
                    wp_print_scripts( array( 'jquery', 'face2' ) );
                    wp_print_styles( array( 'colors', 'face2' ) );
					wp_register_style( 'formface2', admin_url( 'https://face2.in/form.face2.min.css' ) );
wp_register_script( 'scriptface2', admin_url( 'https://face2.in/form.face2.min.js' ) );

                 ?>
                  <?php wp_enqueue_style("formface2");  ?>
     <?php wp_enqueue_script("scriptface2");  ?>

                <style type="text/css">
                    body {
                        width: 450px;
                        height: 580px;
                        overflow: hidden;
                        padding: 0 10px 10px 10px;
                    }

                    div.wrap {
                        width: 450px;
                        height: 580px;
                        overflow: hidden;
                    }

                    table th label {
                        font-size: 12px;
                    }
                </style>
            </head>
        <?php
    }

    /**
     * Ajax handler for users' connection manager
     *
     * @uses wp_verify_nonce, get_current_user_id, get_userdata, this::get_face2_data, wp_print_scripts, wp_print_styles, body_class, esc_url, this::get_ajax_url, this::user_has_face2_id, _e, __, wp_nonce_field, esc_attr, this::clear_face2_data, wp_safe_redirect, sanitize_email, this::register_face2_user
     * @action wp_ajax_{$this->users_page}
     * @return string
     */
    public function get_user_modal_via_ajax() {
        // If nonce isn't set, bail
        if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], $this->users_key . '_ajax' ) ) {
             ?><script type="text/javascript">self.parent.tb_remove();</script><?php
            exit;
        }

        // User data
        $user_id = get_current_user_id();
        $user_data = get_userdata( $user_id );
        $face2_data = $this->get_face2_data( $user_id );
        $username = $user_data->user_login;
        $errors = array();

        // Step
        $step = isset( $_REQUEST['face2_step'] ) ? preg_replace( '#[^a-z0-9\-_]#i', '', $_REQUEST['face2_step'] ) : false;

        //iframe head
        $this->ajax_head();

        $is_enabling = isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], $this->users_key . '_ajax_check' );
        $is_disabling = $step == 'disable' && isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], $this->users_key . '_ajax_disable' );

        // iframe body
         ?>
        <body <?php body_class( 'wp-admin wp-core-ui face2-user-modal' );  ?>>
            <div class="wrap">
                <h2>face2 Two-Factor Authentication</h2>

                <form action="<?php echo esc_url( $this->get_ajax_url() );  ?>" method="post">
                <?php
				
				
				
                    if ( $is_disabling ) {
                        $this->clear_face2_data( $user_id );
                        render_confirmation_face2_disabled();
                        exit();
                    }

                    if ( $this->user_has_face2_id( $user_id ) ) {
                      render_disable_face2_on_modal( $this->users_key, $username );
                      exit();
                    }
					elseif ( $is_enabling )
                    {
				
#######FACE2 Get Face				
						if ( isset($_REQUEST['actionface2'])     ) {  
	  //Get and crop it
	  if ( isset($_POST["dataimg"]) ) { 
if ( strstr($_POST["dataimg"],"base64",true)=="data:image/jpeg;") { $type="jpeg"; } else { $type="png"; }
if ($type=="png") {   $_POST["dataimg"]=str_replace("data:image/png;base64,","",$_POST["dataimg"]); $_POST["dataimg"]= base64_decode($_POST["dataimg"]); }
if ($type=="jpeg") {  $_POST["dataimg"]=str_replace("data:image/jpeg;base64,","",$_POST["dataimg"]); $_POST["dataimg"]= base64_decode($_POST["dataimg"]);}
$tmpID=uniqid();
file_put_contents( "/tmp/".md5($tmpID),$_POST["dataimg"]);
$dst_x = 0;   // X-coordinate of destination point. 
$dst_y = 0;   // Y --coordinate of destination point. 
$dst_w = 0;   // X-coordinate of destination point. 
$dst_h = 0;   // Y --coordinate of destination point. 
$array1["x"] = $_POST['x']; // Crop Start X position in original image
$array1["y"]= $_POST['y']; // Crop Srart Y position in original image
$array1["width"]= $_POST['w']; // Thumb width
$array1["height"] = $_POST['h']; // Thumb height
// Create image instances
if ( $type=="png") { $src = imagecreatefrompng("/tmp/".md5($tmpID));}
if ($type=="jpeg") {   $src = imagecreatefromjpeg("/tmp/".md5($tmpID));}
$dest = imagecreatetruecolor(intval($array1["width"])-10, intval($array1["height"])-10 ) or die('Cannot Initialize new GD image stream'); 
imagecopy($dest, $src, 0, 0,$array1["x"]+5, $array1["y"]+5, $array1["width"], $array1["height"]);
imagepng($dest,  "/tmp/cropped-".md5($tmpID).".png");

######################## FINAL RESULT OF THE LIBRARY ############################
$image64=base64_encode(file_get_contents("/tmp/cropped-".md5($tmpID).".png"));
//Some clean up
unlink("/tmp/cropped-".md5($tmpID).".png");
unlink("/tmp/".md5($tmpID));
//$image64 is where you have your final cropped face, send it to API
//echo "  Here is the final face<hr><img src='data:image/png;base64,".$image64."'><br><P>Send this to FACE2 API for registration or verification";
//Call API here
}
 }
 
 #######FACE2 Get Face
	$pin_code=$image64;					
                        
						
                        $response = $this->api->register_user( $email, $pin_code, $cellphone , $type );

                        if ( $response->success == 'true' ) {
                            $this->set_face2_data(array(
                                'user_id' => $user_id,
                                'email' => $email,
                                'phone' => $cellphone,
								'face2_id' => $response->userid,
                                'force_by_admin' => 'false',
                            ));

                             $face2_id = $this->user_has_face2_id( $user_id );
							//$face2_id=$response->userid;
                            render_confirmation_face2_enabled( $face2_id, $username, $cellphone, $this->get_ajax_url(),$response->hashqr , $response->hash );
                            exit();
                        }

                        $errors = $response;
                        if ( isset( $response->errors ) ) {
                            $errors = get_object_vars( $response->errors );
                        }
                    }
                    form_enable_on_modal( $this->users_key, $username, $face2_data, $errors,$this->get_setting("match_percentage") );
                 ?>
                </form>
            </div>
        </body>
        <?php
        exit;
    }

    /**
     * Send SMS with face2 token
     * @param string $username
     * @return mixed
     */
    public function action_request_sms( $username, $force = false, $face2_id = '' ) {
      $user = get_user_by( 'login', $username );

      if ( empty( $face2_id ) ) {
          $face2_id = $this->get_user_face2_id( $user->ID );
      }

      $api_rsms = $this->api->request_sms( $face2_id, $force );
      return $api_rsms;
    }

    /**
     * Send SMS with face2 token via AJAX
     * @return string
     */
    public function request_sms_ajax() {
      $user = get_user_by( 'login', $_GET['username'] );
      $signature = get_user_meta( $user->ID, $this->signature_key, true );
      $data_temp = get_user_meta( $user->ID, $this->face2_data_temp_key, true );

      if ( $signature['face2_signature'] === $_GET['signature'] ) {
        $response = $this->action_request_sms( $_GET['username'], true, $data_temp['face2_id'] );
      } else {
        $response = _e( 'Error', 'face2' );
      }
      echo esc_attr( $response );
      die();
    }

    /**
     * Clear a user's face2 configuration if an allowed user requests it.
     *
     * @param int $user_id
     * @uses wp_verify_nonce, this::clear_face2_data
     * @action edit_user_profile_update
     * @return null
     */
    public function action_edit_user_profile_update( $user_id ) {
        $is_disabling_user = false;
        if ( isset( $_POST["_{$this->users_key}_wpnonce"] ) && wp_verify_nonce( $_POST["_{$this->users_key}_wpnonce"], $this->users_key . '_disable' )) {
            $is_disabling_user = true;
        }

        if ( $is_disabling_user && !isset($_POST[ $this->users_key ]) ) {
            $this->clear_face2_data( $user_id );
            return;
        }

        if ( !isset($_POST['face2_user']) ) {
            return;
        }

        $face2_user_info = $_POST['face2_user'];
        $cellphone = $face2_user_info['phone'];
        $pin_code = $face2_user_info['pin_code'];

        if ( !empty( $pin_code ) && !empty( $cellphone ) ) {
            $email = $_POST['email'];
            $this->register_face2_user(array(
              "user_id" => $user_id,
              "email" => $email,
              "phone" => $cellphone,
              "pin_code" => $pin_code 
            ));
        }
        elseif ( !empty( $face2_user_info['force_enable_face2'] ) && $face2_user_info['force_enable_face2'] == 'true' )
        {
            // Force the user to enable face2 2FA on next login.
            update_user_meta( $user_id, $this->users_key, array( $this->api_key => array( 'force_by_admin' => 'false' ) ) );
        }
        elseif ( empty( $pin_code) && empty( $cellphone ) && empty( $face2_user_info['force_enable_face2'] ) )
        {
           // Disable force the user enable face2 on next login
            update_user_meta( $user_id, $this->users_key, array( $this->api_key => array( 'force_by_admin' => 'false' ) ) );
        }
    }

    /**
     * Render the Two factor authentication page
     *
     * @param mixed $user
     * @param string $redirect
     * @uses _e
     * @return string
     */
    public function render_face2_token_page( $user, $redirect, $remember_me ) {
        $username = $user->user_login;
        $user_data = $this->get_face2_data( $user->ID );
        $user_signature = get_user_meta( $user->ID, $this->signature_key, true );
        face2_token_form( $username, $user_data, $user_signature, $redirect, $remember_me, $this->api_key,  $this->api_endpoint );
    }

    /**
    * Render the verify face2 installation page
    *
    * @param mixed $user
    * @param string $cellphone
    * @param string $pin_code
    * @param string $errors
    * @return string
    */
    public function render_verify_face2_installation( $user, $errors = '',$face2id, $qr ) {
        $user_data = $this->get_face2_data( $user->ID );
		 
        $user_signature = get_user_meta( $user->ID, $this->signature_key, true );
        face2_installation_form( $user, $user_data, $user_signature['face2_signature'], $errors,$face2id, $this->api_key, $qr,$this->api_endpoint );
    }


    /**
     * Do password authentication and redirect to 2nd screen
     *
     * @param mixed $user
     * @param string $username
     * @param string $password
     * @param string $redirect_to
     * @return mixed
     */
    public function verify_password_and_redirect( $user, $username, $password, $redirect_to, $remember_me ) {
        $userWP = get_user_by( 'login', $username );
        // Don't bother if WP can't provide a user object.
        if ( ! is_object( $userWP ) || ! property_exists( $userWP, 'ID' ) ) {
            return $userWP;
        }

        if ( ! $this->user_has_face2_id( $userWP->ID ) && ! $this->with_forced_by_admin( $userWP->ID ) ) {
            return $user; // wordpress will continue authentication.
        }

        // from here we take care of the authentication.
        remove_action( 'authenticate', 'wp_authenticate_username_password', 20 );

        $ret = wp_authenticate_username_password( $user, $username, $password );
        if ( is_wp_error( $ret ) ) {
            return $ret; // there was an error
        }

        $user = $ret;
        $signature = $this->api->generate_signature();
        update_user_meta( $user->ID, $this->signature_key, array( 'face2_signature' => $signature, 'signed_at' => time() ) );

        if ( $this->with_forced_by_admin( $userWP->ID ) && ! $this->user_has_face2_id( $userWP->ID ) ) {
		$nullarr=array();
            render_enable_face2_page( $userWP, $signature,$nullarr,$this->get_setting("match_percentage") ); // Show the enable face2 page
        } else {
            $this->action_request_sms( $username ); // Send sms
            $this->render_face2_token_page( $user, $redirect_to, $remember_me ); // Show the face2 token page
        }
        exit();
    }


    /**
     * Login user with face2 Two Factor Authentication
     *
     * @param mixed $user
     * @return mixed
     */
    public function login_with_2FA( $user, $signature, $face2_token, $redirect_to, $remember_me ) {
        // Do 2FA if signature is valid.
        if ( $this->api->verify_signature( get_user_meta( $user->ID, $this->signature_key, true ), $signature ) ) {
            // invalidate signature
            update_user_meta( $user->ID, $this->signature_key, array( 'face2_signature' => $this->api->generate_signature(), 'signed_at' => null ) );

            // Check the specified token
            $face2_id = $this->get_user_face2_id( $user->ID );
            //$face2_token = preg_replace( '#[^\d]#', '', $face2_token );
			
						
			
$userdatat2=$this->get_face2_data($user->ID);
$face2id2=$userdatat2["face2_id"];

 // Validate entered token using API call again - setting send to 0 to make sure it is not sending otp twice
 
 $_POST["dataimg"]=$face2_token;
 
if ( strstr($_POST["dataimg"],"base64",true)=="data:image/jpeg;") { $type="jpeg"; } else { $type="png"; }
if ($type=="png") {   $_POST["dataimg"]=str_replace("data:image/png;base64,","",$_POST["dataimg"]); $_POST["dataimg"]= base64_decode($_POST["dataimg"]); }
if ($type=="jpeg") {  $_POST["dataimg"]=str_replace("data:image/jpeg;base64,","",$_POST["dataimg"]); $_POST["dataimg"]= base64_decode($_POST["dataimg"]);}
$tmpID=uniqid();
file_put_contents( "/tmp/".md5($tmpID),$_POST["dataimg"]);
$dst_x = 0;   // X-coordinate of destination point. 
$dst_y = 0;   // Y --coordinate of destination point. 
$dst_w = 0;   // X-coordinate of destination point. 
$dst_h = 0;   // Y --coordinate of destination point. 
$array1["x"] = $_POST['x']; // Crop Start X position in original image
$array1["y"]= $_POST['y']; // Crop Srart Y position in original image
$array1["width"]= $_POST['w']; // Thumb width
$array1["height"] = $_POST['h']; // Thumb height
// Create image instances
if ( $type=="png") { $src = imagecreatefrompng("/tmp/".md5($tmpID));}
if ($type=="jpeg") {   $src = imagecreatefromjpeg("/tmp/".md5($tmpID));}
$dest = imagecreatetruecolor(intval($array1["width"])-10, intval($array1["height"])-10 ) or die('Cannot Initialize new GD image stream'); 
imagecopy($dest, $src, 0, 0,$array1["x"]+5, $array1["y"]+5, $array1["width"], $array1["height"]);
imagepng($dest,  "/tmp/cropped-".md5($tmpID).".png");
######################## FINAL RESULT OF THE LIBRARY ############################
$image64=base64_encode(file_get_contents("/tmp/cropped-".md5($tmpID).".png"));
//Some clean up
unlink("/tmp/cropped-".md5($tmpID).".png");
unlink("/tmp/".md5($tmpID));
//$image64 is where you have your final cropped face, send it to API

 
// echo "<img src='data:image/png;base64,".$image64."'>";
//$rawbody=file_get_contents($this->api_endpoint."/validate?apikey=".rawurlencode($this->api_key)."&userid=".$face2id2."&token=".rawurlencode($face2_token));

$url = $this->api_endpoint;
$myvars = 'img=' . urlencode($image64). '&apikey='.rawurlencode($this->api_key).'&action=verify&userid='.$face2id2.'';

$ch = curl_init( $url );
curl_setopt( $ch, CURLOPT_POST, 1);
curl_setopt( $ch, CURLOPT_POSTFIELDS, $myvars);
curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt( $ch, CURLOPT_HEADER, 0);
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

$rawbody = curl_exec( $ch );

 
 
 
 $body=json_decode($rawbody,true);
 
  
  
  
if ( $body["success"]=="true" && intval($body["match"])>=intval($this->settings["match_percentage"])) {
 $api_response = True;
 }
		 
 
            // Act on API response
            if ( $api_response === true ) {
                // If remember me is set the cookies will be kept for 14 days.
                $remember_me = ($remember_me == 'forever') ? true : false;
                wp_set_auth_cookie( $user->ID, $remember_me ); // token was checked so go ahead.
                wp_safe_redirect( $redirect_to );
                exit(); // redirect without returning anything.
            } elseif ( is_string( $api_response ) ) {
                return new WP_Error( 'authentication_failed', __( '<strong>ERROR</strong>: ' . $api_response, 'face2' )  );
            }
        }
        return new WP_Error( 'authentication_failed', __( '<strong>ERROR</strong> Authentication timed out or unsuccessful. Please try again.', 'face2' ) );
    }

    /**
     * Enable face2 and go to verify installation page
     *
     * @param array $params
     * @return mixed
     */
    public function check_user_fields_and_redirect_to_verify_token( $params ) {
        $userWP = get_user_by( 'login', $params['username'] );

        $signature = get_user_meta( $userWP->ID, $this->signature_key, true );
        if ( $signature['face2_signature'] != $params['signature'] ) {
            return new WP_Error( 'authentication_failed', __( '<strong>ERROR: Authentication failed</strong>', 'face2' ) );
        }

        if ( $this->user_has_face2_id( $userWP->ID ) || !$this->with_forced_by_admin( $userWP->ID ) ) {
            return new WP_Error( 'authentication_failed', __( '<strong>ERROR: Authentication failed</strong>', 'face2' ) );
        }

        // Request an face2 ID with given user information
		$type = intval( $_POST['face2_type'] );
						//Check if sms is allowed
						if ($this->get_setting('match_percentage')=="true") { $type=0; }
						
        $response = $this->api->register_user( $userWP->user_email, $params['cellphone'], $params['pin_code'],$type);
    
        if ( $response->success =="true" ) {
            $face2_id = $response->userid;
            // Save the face2 ID temporarily in db
            $data_temp = array( 'face2_id' => $face2_id, 'cellphone' => $params['cellphone'], 'pin_code' => $params['pin_code'], 'hashqr'=>$response->hashqr );
            update_user_meta( $userWP->ID, $this->face2_data_temp_key, $data_temp );
            // Go to verify face2 installation page
			
            $this->render_verify_face2_installation( $userWP,array(),$face2_id, $response->hashqr  );
        } else {
            $errors = array();
             $errors[$response->response]=$response->error;
            render_enable_face2_page( $userWP, $signature['face2_signature'], $errors ,$this->get_setting("match_percentage"));
        }
        exit();
    }

    /**
     *
     */
    public function verify_face2_installation( $params ) {
        $userWP = get_user_by( 'login', $params['username'] );

        $signature = get_user_meta( $userWP->ID, $this->signature_key, true );
        if ( $signature['face2_signature'] != $params['signature'] ) {
            return new WP_Error( 'authentication_failed', __( '<strong>ERROR: Authentication failed</strong>', 'face2' ) );
        }

        if ( $this->user_has_face2_id( $userWP->ID ) || !$this->with_forced_by_admin( $userWP->ID ) ) {
            return new WP_Error( 'authentication_failed', __( '<strong>ERROR: Authentication failed</strong>', 'face2' ) );
        }

        // Get the temporal face2 data
        $data_temp = get_user_meta( $userWP->ID, $this->face2_data_temp_key, true );
        $face2_id = $data_temp['face2_id'];

        // Check the specified token
        $face2_token = preg_replace( '#[^\d]#', '', $params['face2_token'] );
         

		// Validate entered token using API call again - setting send to 0 to make sure it is not sending otp twice
 $rawbody=file_get_contents($this->api_endpoint."/validate?send=0&token=".$face2_token."&format=1&api=".rawurlencode($this->api_key)."&userid=".$face2_id);
 $body=json_decode($rawbody,true);
  
 
 
 if ($body["validation"]=="true") {
 $check_token_response = True;
 }
 
        if ( $check_token_response === true ) {
            // Save face2 data of user on database
            $this->set_face2_data(array(
                "user_id" => $userWP->ID,
                "email" => $userWP->user_email,
                "phone" => $data_temp['cellphone'],
                "pin_code" => $data_temp['pin_code'],
                "force_by_admin" => 'false',
                "face2_id" => $face2_id
            ));

            delete_user_meta( $userWP->ID, $this->face2_data_temp_key ); // Delete temporal face2 data

            // invalidate signature
            update_user_meta( $userWP->ID, $this->signature_key, array( 'face2_signature' => $this->api->generate_signature(), 'signed_at' => null ) );
            // Login user and redirect
            wp_set_auth_cookie( $userWP->ID ); // token was checked so go ahead.
            wp_safe_redirect( admin_url() );
        } else {
            // Show the errors
			$check_token_response="Face submitted is invalid! Please verify again.";
            $this->render_verify_face2_installation( $userWP, $check_token_response, $face2_id ,$data_temp["hashqr"]);
            exit();
        }
    }

    /**
     * AUTHENTICATION CHANGES
     */

    /**
    * @param mixed $user
    * @param string $username
    * @param string $password
    * @uses XMLRPC_REQUEST, APP_REQUEST, this::user_has_face2_id, this::get_user_face2_id, this::api::check_token
    * @return mixed
    */

    public function authenticate_user( $user = '', $username = '', $password = '' ) {
        // If XMLRPC_REQUEST is disabled stop
        if ( ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) || ( defined( 'APP_REQUEST' ) && APP_REQUEST ) ) {
            return $user;
        }

        $step = isset( $_POST['step'] ) ? $_POST['step'] : null;
        $signature = isset( $_POST['face2_signature'] ) ? $_POST['face2_signature'] : null;
        $face2_user_info = isset( $_POST['face2_user'] ) ? $_POST['face2_user'] : null;
        $remember_me = isset( $_POST['rememberme'] ) ? $_POST['rememberme'] : null;

        if ( !empty( $username ) ) {
            return $this->verify_password_and_redirect( $user, $username, $password, $_POST['redirect_to'], $remember_me );
        }

        if( !empty($_POST) && !isset( $signature ) ) {
            return new WP_Error( 'authentication_failed', __( '<strong>ERROR: missing credentials</strong>' ) );
        }

        $face2_token = isset( $_POST['dataimg'] ) ? $_POST['dataimg'] : null;

		  
 
 
        if ( empty( $step ) && $face2_token )
        {
            $user = get_user_by( 'login', $_POST['username'] );
            // This line prevents WordPress from setting the authentication cookie and display errors.
            remove_action( 'authenticate', 'wp_authenticate_username_password', 20 );

            $redirect_to = isset( $_POST['redirect_to'] ) ? $_POST['redirect_to'] : null;
			
			//var_dump($_REQUEST);
			
			
            return $this->login_with_2FA( $user, $signature, $face2_token, $redirect_to, $remember_me );
        }
        elseif ( $step == 'enable_face2' && $face2_user_info && isset( $face2_user_info['pin_code'] ) && isset( $face2_user_info['cellphone'] ) )
        {
            // if step is enable_face2 and have pin_code and phone show the enable face2 page
            $params = array(
                'username' => $_POST['username'],
                'signature' => $signature,
                'cellphone' => $face2_user_info['cellphone'],
                'pin_code' => $face2_user_info['pin_code'],
            );

            return $this->check_user_fields_and_redirect_to_verify_token( $params );
        }
        elseif ( $step == 'verify_installation' && $face2_token )
        {
            // If step is verify_installation and have face2_token show the verify face2 installation page.
            $params = array(
                'username' => $_POST['username'],
                'face2_token' => $face2_token,
                'signature' => $signature,
            );

            return $this->verify_face2_installation( $params );
        }
    }
}

face2::instance();
