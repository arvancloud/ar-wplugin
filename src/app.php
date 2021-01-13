<?php

if (!is_admin() || !defined( 'ABSPATH' ))
    exit;

function ar_main_menu() {
    add_menu_page(
        __( 'Domains', AR_TEXT_DOMAIN ),
        __( 'ArvanCloud CDN', AR_TEXT_DOMAIN ),
        'manage_options',
        'arvancloud',
        'domains_menu',
        'dashicons-cloud',
        6
    );
}

add_action( 'admin_menu', 'ar_main_menu' );

function domains_menu() {
    # show domains
    require_once('domains.php');
}