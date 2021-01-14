<?php
/*
Plugin Name: ArvanCloud CDN
Plugin URI: https://github.com/arvancloud/ar-wplugin
Description: ArvanCloud CDN Wordpress Plugin
Version: 1.0.0
Author: See all contributors
Author URI: https://github.com/arvancloud/ar-wplugin/graphs/contributors
License: GPLv2 or later
Text Domain: ar-wplugin
*/
if (!is_admin() || !defined( 'ABSPATH' ))
    return;

define('AR_TEXT_DOMAIN', 'ar-wplugin');

require_once('src/app.php');