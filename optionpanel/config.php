<?php


if ( ! class_exists( 'Redux' ) ) {
    return;
}

$opt_name = 'arvan';




$theme = wp_get_theme(); // For use with some settings. Not necessary.

$args = array(
    'display_name'         => $theme->get( 'Arvan Cloud Options' ),
    'display_version'      => $theme->get( '1.0' ),
    'menu_title'           => esc_html__( 'Arvan Cloud Options', 'arcloud-theme-textdomain' ),
    'customizer'           => false,
    'dev_mode'             => false,
    'show_import_export'   => false,
    'hide_reset'           => true
);

Redux::setArgs( $opt_name, $args );

Redux::setSection( $opt_name,
    array(
        'title'  => esc_html__( 'Basic Options', 'arvancloud-theme-textdomain' ),
        'id'     => 'arvancloud-basic',
        'desc'   => esc_html__( 'Basic options.', 'arvancloud-theme-textdomain' ),
        'icon'   => 'el el-home',

        'fields' => array(
           
            array(
                'id'       => 'arvan-api-key',
                'type'     => 'text',
                'title'    => esc_html__( 'API Key', 'arvancloud-theme-textdomain' ),
                'desc'     => esc_html__( '', 'arvancloud-theme-textdomain' ),
                'subtitle' => esc_html__( '', 'arvancloud-theme-textdomain' ),
                'hint'     => array(
                                'content' => 'API Key',
                ),
                
                
            ),
            array(
                'id'       => 'arvan-cache-endpoint-url',
                'type'     => 'text',
                'title'    => esc_html__( 'Cache Endpoint URL', 'arvancloud-theme-textdomain' ),
                'desc'     => esc_html__( 'https://napi.arvancloud.com/cdn/4.0/domains/{domain}/caching', 'arvancloud-theme-textdomain' ),
                'subtitle' => esc_html__( 'Change the {domain} to your specific domain', 'arvancloud-theme-textdomain' ),
                'hint'     => array(
                                'content' => 'Cache Endpoint URL',
                ),
                
                
            ),
            
        )
    )
);

Redux::setSection( $opt_name,
    array(
        'title'  => esc_html__( 'Requests', 'arvancloud-theme-textdomain' ),
        'id'     => 'arvancloud-requests',
        'desc'   => esc_html__( 'Requests.', 'arvancloud-theme-textdomain' ),
        'icon'   => 'el el-home',

        'fields' => array(
           
            array(
                'id'       => 'arvan-cache-status',
                'type'     => 'button_set',
                'title'    => esc_html__( 'Cache Status', 'arvancloud-theme-textdomain' ),
                'desc'     => esc_html__( 'Set Cache On/Off', 'arvancloud-theme-textdomain' ),
                'subtitle' => esc_html__( 'This option used for global setting for caching', 'arvancloud-theme-textdomain' ),
                'hint'     => array(
                                'content' => 'Cache status',
                ),
                'options'  => array(
					'off' => 'Off',
                    'uri' => 'URI',
                    'query_string'=>'Query String',
                    'advance'=>'Advance'
					
				),
				'default'  => 'on',
                
            ),
            array(
                'id'       => 'arvan-total-purge',
                'type'     => 'raw',
                'full_width'=>'false',
                'title'    => esc_html__( 'Total Purge', 'arvancloud-theme-textdomain' ),
                'desc'     => esc_html__( 'Total purge', 'arvancloud-theme-textdomain' ),
                'subtitle' =>  'Use this option very carefully<br /> It will <b>TOTALLY PURGE</b> the cache' ,
                'content'     => '<button id="arvan-total-purge" type="button">Total Purge</button>',

                
            ),
            array(
                'id'       => 'arvan-save-post-status',
                'type'     => 'button_set',
                'title'    => esc_html__( 'Purge after save', 'arvancloud-theme-textdomain' ),
                'desc'     => esc_html__( 'Turn On/Off', 'arvancloud-theme-textdomain' ),
                'subtitle' => esc_html__( 'This option used for Purge the individual post cache when it saved.', 'arvancloud-theme-textdomain' ),
                
                'options'  => array(
					'on' => 'On',
                    'off' => 'Off',
                    
					
				),
				'default'  => 'on',
                
            ),
           
        )
    )
);
