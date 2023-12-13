<?php

/**
 * Plugin Name: MV Slider
 * Plugin URI: https://gkdeveloper.com.br
 * Description: Plugin para gerenciamento de slides
 * Version: 1.0.0
 * Requires at leat: 5.6
 * Author: Gustavo Kreischer de Almeida
 * Author URI: https://gkdeveloper.com.br
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: mv-slider
 * Domain Path: /languages
 */

/*
 MV Slider is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 any later version.
 MV Slider is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU General Public License for more details.
 You should have received a copy of the GNU General Public License
 along with this program; if not, see <http://www.gnu.org/licenses/gpl-2.0.html>.
 */

// Bloqueia execução externa
if (!defined(ABSPATH)) exit;

if(!class_exists('MV_Slider')) {
    class MV_Slider {
        
        function __construct()
        {
            
        }

        public function define_constants() 
        {
            define('MV_SLIDER_PATH', plugin_dir_path(__FILE__));
            define('MV_SLIDER_URL', plugin_dir_path(__FILE__));
        }


    }
}

if(class_exists('MV_Slider')) {
    $mv_slider = new MV_Slider();
}