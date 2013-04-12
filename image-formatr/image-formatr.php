<?php
 /*
  * Plugin Name: Image Formatr
  * Plugin URI: http://warriorself.com/blog/about/image-formatr/
  * Description: Formats all content images on a page / post giving them borders and captions.
  * Version: 1.1
  * Author: Steven Almeroth
  * Author URI: http://warriorship.org/sma/
  * License: GPL2
  */

 /*  Copyright 2011  Steven Almeroth  (sroth77@gmail.com)
  *
  *   This program is free software; you can redistribute it and/or modify
  *   it under the terms of the GNU General Public License as published by
  *   the Free Software Foundation; either version 2 of the License, or
  *   (at your option) any later version.
  *
  *   This program is distributed in the hope that it will be useful,
  *   but WITHOUT ANY WARRANTY; without even the implied warranty of
  *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  *   GNU General Public License for more details.
  *
  *   You should have received a copy of the GNU General Public License
  *   along with this program; if not, write to the Free Software
  *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
  */
define ('IMAGEFORMATR_TEXTDOMAIN', 'image-formatr');

include_once(dirname(__FILE__) . '/class.formatr.php');

if (class_exists("ImageFormatr")) {
    $image_formatr_instance = new ImageFormatr();

    // hooks
    register_activation_hook  (__FILE__, array($image_formatr_instance, 'activate'  ));
    register_deactivation_hook(__FILE__, array($image_formatr_instance, 'deactivate'));

    // actions
    add_action('admin_init'           , array($image_formatr_instance, 'admin_init'   ));
    add_action('admin_menu'           , array($image_formatr_instance, 'admin_menu'   ));
    add_action('admin_enqueue_scripts', array($image_formatr_instance, 'enqueue'      ));
    add_action('template_redirect'    , array($image_formatr_instance, 'enqueue'      ));
    add_action('wp_footer'            , array($image_formatr_instance, 'print_scripts'));

    // filters
    add_filter('the_content'          , array($image_formatr_instance, 'filter'), 10);
}
