<?php
/* 
ArvanCloud CDN Wordpress Plugin
 * Plugin Name:     ArvanCloud CDN Wordpress Plugin
 * Plugin URI:      https://github.com/arvancloud/ar-wplugin
 * Github URI:      https://github.com/zedium/ar-wplugin
 * Description:     WordPress Plugin for ArvanCloud CDN
 * Author:          Arvan Cloud
 * Author URI:      https://github.com/zedium/
 * Version:         1.0.0
 * Text Domain:     arvancloud-theme-textdomain
 * License:         GPLv3 or later
 * License URI:     http://www.gnu.org/licenses/gpl-3.0.txt
 * Provides:        ArvanCloud
 *
 * @package         ArvanCloudPlugin
 * @author          Arvan Cloud
 * @license         GNU General Public License, version 3
 * @copyright       2012-2020 Arvan Cloud
 */


// Block direct access

defined( 'ABSPATH' ) || exit;


class ArPluginCore{
    
    private  $opt_name = 'arvan';

    /**
    * Constructor initialize and load default values
    */
    public function __construct(){

        
        
        // Loads the optionpanel/framework.php
        $this->include_redux_core();
        
        
        
        //optionpanel/config.php
        $this->inlcude_redux_config();
        
        
        
        /**
         * This code loads the cache status from ArvanCloud
         * And set the default values in plugin option
         */
        $this->call_cache_status();

        /**
         * Called when options in plugin options saved
         */
        
         add_action('redux/options/arvan/saved', array($this, 'redux_after_saved'));
        
        
        
        /**
         * Include javascripts
        */
        add_action('admin_enqueue_scripts', array($this, 'arvan_wp_enqueue_scripts'));
        
        
        /**
         * Will be called when Total Purge button clicked in plugin option
         */
        add_action("wp_ajax_nopriv_arvan_total_purge", array($this,"arvan_total_purge_ajax_nopriv"));
        add_action("wp_ajax_arvan_total_purge", array($this,"arvan_total_purge_ajax"));
        
        
        /**
         * To purge specific post url cache
         */
        add_action("publish_post", array($this,"arvan_save_post_action"),1,3);

    }

    private function get_domain_list(){
        $domains = array();



        return $domains;
    }


    /**
     * Call cache status from remote Arvan Settings
     * And set it to plugin options
     */
    public function call_cache_status(){
        
        
        /**
         * Load Cache EndPoint URL from plugin options to make a request
         */
        $arvan_url =  Redux::get_option( $this->opt_name, 'arvan-cache-endpoint-url');
        $
        
        /**
         * Load API-Key from plugin options
         */
        $arvan_api_key =  Redux::get_option( $this->opt_name, 'arvan-api-key');
        
        
        /**
         * Make a request to server
         */
        $response = wp_remote_post( $arvan_url, array(
            'method'      => 'GET',
            'timeout'     => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array('Authorization' => $arvan_api_key),
            'cookies'     => array()
            )
        );

        
        if ( is_wp_error( $response ) ) {
  
            $error_message = $response->get_error_message();
            return "Something went wrong: $error_message";
  
        } else {
            
            if(!isset($response['body']))
                return;

            /**
             * Get cache status from JSON response
             */
            $response_object = json_decode($response['body']);
            $cache_status = $response_object->data->cache_status;
            
            /**
             * Save the remote cache status to plugin options
             */
            Redux::set_option( $this->opt_name, 'arvan-cache-status', $cache_status);
  
        }
        
    }

    /**
     * Include plugin options core file
     */
    public function include_redux_core(){

        if (!class_exists('ReduxFramework') && file_exists(plugin_dir_path(__FILE__) . '/optionpanel/framework.php'))
        {
            require_once ('optionpanel/framework.php');
        }

    }
    /**
     * Include plugin options config file
     */
    public function inlcude_redux_config(){
        if (!isset($redux_demo) && file_exists(plugin_dir_path(__FILE__) . '/optionpanel/config.php'))
        {
            require_once ('optionpanel/config.php');
        }
    }


    /**
     * Import javascript files to admin header
     */
    public function arvan_wp_enqueue_scripts(){
        
        wp_register_script('arvan-plugin-custom-script', plugin_dir_url(__FILE__) . '/js/custom.js', array('jquery'));
        wp_localize_script( 'arvan-plugin-custom-script', 'WPData', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));  
        wp_enqueue_script( 'arvan-plugin-custom-script');
        

    }

    function arvan_total_purge_ajax_nopriv()
    {
        wp_die('Oops, you don\'t have permissions.');
    }


    /**
     * These function does a request to ArvanCloud to make Total Purge
     * It will be called when Total Purge button plugin option clicked
     * It will be called via ajax request
     */

    function arvan_total_purge_ajax(){
        
        
        if (  current_user_can( 'manage_options' ) ) {
            
            /**
             * Making Purge request URL
             */
            $arvan_url =  Redux::get_option( $this->opt_name, 'arvan-cache-endpoint-url');
            
            if(empty($arvan_url)){
                wp_send_json_error(array("message"=>"Endpoint url is empty"));
                return;
            }

            $arvan_url .=  '?purge=all';
            
            $arvan_api_key =  Redux::get_option( $this->opt_name, 'arvan-api-key');
            
            if(empty($arvan_api_key)){
                wp_send_json_error(array("message"=>"API Key is empty"));
                return;
            }

            $response = wp_remote_post( $arvan_url, array(

                'method'      => 'DELETE',
                'timeout'     => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking'    => true,
                'headers'     => array('Authorization' => $arvan_api_key),
                
                )
            );
            
            if ( is_wp_error( $response ) ) {

                $error_message = $response->get_error_message();
                return "Something went wrong: $error_message";

            }else{

                if(!isset($response['body']))
                    return;
                
                $response_object = json_decode($response['body']);
                /**
                 * Send a successful response to client JS
                 */
                wp_send_json_success($response_object);
                
            }
        }

    }


    /**
     * This function called when plugin option got saved
     * Mainly used for set the cache status on ArvanCloud
     */
    public function redux_after_saved($value){
        
        
        $arvan_url =  Redux::get_option( $this->opt_name, 'arvan-cache-endpoint-url');
        $arvan_api_key =  Redux::get_option( $this->opt_name, 'arvan-api-key');
        

        
        $body = '{"cache_status":"'. $value['arvan-cache-status'] .'"}';
        $response = wp_remote_post( $arvan_url, array(
            'method'      => 'PATCH',
            'timeout'     => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array('Content-Type' => 'application/json; charset=utf-8','Authorization' => $arvan_api_key),
            'body'        => $body,
            'data_format' => 'body'
            )
        );
        
        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            return "Something went wrong: $error_message";
        }
        
       
    }

    /**
     * Function will be used for clear a webpage cache after new post or change
     * When a new post created it makes a request to ArvanCloud to purge the cached url
     */
    public function arvan_save_post_action($post_id, $post, $update ){
        
        
        $purge_after_save_status =  Redux::get_option( $this->opt_name, 'arvan-save-post-status');
        
        /**
         * Do nothin if option turned off
         */
        if(isset($purge_after_save_status) && ($purge_after_save_status == 'off'))
            return;
        


        $url = get_permalink($post_id);

        
        $arvan_url =  Redux::get_option( $this->opt_name, 'arvan-cache-endpoint-url') . '?purge=individual&purge_urls='. $url;
        $arvan_api_key =  Redux::get_option( $this->opt_name, 'arvan-api-key');
        
        $response = wp_remote_post( $arvan_url, array(
            'method'      => 'DELETE',
            'timeout'     => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            
            'headers'     => array('Authorization' => $arvan_api_key),
            
            )
        );
        
        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            return "Something went wrong: $error_message";
        }
    
        
    }

}

/**
 * Everything starts from here
 */
$arPluginCore = new ArPluginCore();