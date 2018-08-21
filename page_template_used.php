<?php
/**
 * Plugin Name: Page Template Used
 * Plugin URI: http://pleiadesservices.com	
 * Description: If your Marketing team members are having a hard time figuring out what page templates are being used on what pages, this plugin that lets authors easily see which template a page is using and also see only pages using a particular template.
 * Version: 1.0
 * Author: Nicholas Batik
 * Author URI: http://PleiadesServices.com
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume 
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package      pagetemplate
 * @version 	 1.0
 * @author       Nicholas Batik <nbatik@PleiadesServices.com>
 * @copyright    Copyright (c) 2018, Nicholas Batik
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if( !class_exists( 'Page_template_used' ) ) {
    
    class Page_template_used {

        public function __construct() {
            load_plugin_textdomain('page-template-used', false, basename(dirname(__FILE__)) );
        }

        function init() {

            // I think I need to add something here
        }

		/**
         * Show current template and template parts (if any)
         */
        function displayTheTemplateName()
        {
            // Get the actual user
            $actualUser =  wp_get_current_user();
            if($actualUser->data != null) {
            
				$current_template_name = $this->get_current_template();
				$current_child_template = $this->get_child_templates($current_template_name);
				?>
				<div id="page-template-used">
					<h3>Current template:</h3>
					<ul>
						<li>
							<?php echo basename($current_template_name); ?>
							<?php if ( count($current_child_template > 0) ) : ?>
								<ul>
									<?php foreach ($current_child_template as $current_child) : ?>
										<li><?php echo basename($current_child); ?></li>
									<?php endforeach ?>
								</ul>
							<?php endif; ?>
						</li>
					</ul>
				</div>
				<?php
            }
        }

        /**
         * Save current template name as a global variable
         */
        function retrieve_included_template( $template ) {
        
            $GLOBALS['current_theme_template'] = $template;
            return $template;
        }

        /**
         * Grab the global variable et return it
         */
        function get_current_template( $echo = false )
        {
            if( !isset( $GLOBALS['current_theme_template'] ) )
                return false;
            else
                return $GLOBALS['current_theme_template'];
        }

        /**
         * Check for template parts - get_template_part
         * @param $template_called
         * @return array
         */
        function get_child_templates($template_called) {
        
            $child_include  = array();
            $included_files = get_included_files();
            
            $stylesheet_dir = str_replace( '\\', '/', get_stylesheet_directory() );
            $template_dir   = str_replace( '\\', '/', get_template_directory() );
            $template_base  = FALSE;
            
            foreach ( $included_files as $key => $path ) {

                $path = str_replace( '\\', '/', $path );

                if ( false === strpos( $path, $stylesheet_dir ) && false === strpos( $path, $template_dir ) )
                    unset( $included_files[$key] );

                if(!strpos($path, '/wp-content/themes/') === false) {

                    if($template_base){
                        array_push($child_include, $path);
                    }

                    if($path == $template_called){
                        $template_base = TRUE;
                    }
                }
            }
            return $child_include;
        }
    }
}


if (class_exists("Page_template_used")) {
    $Page_template_used_plugin = new Page_template_used();
}

//Actions and Filters
if (isset($Page_template_used_plugin))
{
    register_activation_hook( __FILE__, array(&$Page_template_used_plugin, 'init') );

    //Actions
    add_action( 'wp_footer', array(&$Page_template_used_plugin, 'displayTheTemplateName') );
    
    // Filter
    add_filter( 'template_include', array(&$Page_template_used_plugin,'retrieve_included_template'), 1000 );
}
