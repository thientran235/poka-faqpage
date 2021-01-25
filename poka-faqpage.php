<?php

/**
 * @link              https://pokamedia.com
 * @since             1.0.0
 * @package           Poka_Faqpage
 * @author Thien Tran <thientran2359@gmail.com>
 * 
 * @wordpress-plugin
 * Plugin Name:       Poka Frequently Asked Question (FAQ)
 * Plugin URI:        https://pokamedia.com/poka-faqpage
 * Description:       Mark up your FAQs with structured data
 * Version:           1.0.0
 * Author:            PokaMedia
 * Author URI:        https://pokamedia.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       poka-faqpage
 * Domain Path:       /languages
 */
// Block direct access to file
defined('ABSPATH') or die('Not Authorized!');

// Plugin Defines
define("POKA_FAQPAGE_FILE", __FILE__);
define("POKA_FAQPAGE_DIRECTORY", dirname(__FILE__));
define("POKA_FAQPAGE_DIRECTORY_BASENAME", plugin_basename(POKA_FAQPAGE_FILE));
define("POKA_FAQPAGE_TEXT_DOMAIN", "poka-faqpage");
define("POKA_FAQPAGE_DIRECTORY_PATH", plugin_dir_path(POKA_FAQPAGE_FILE));
define("POKA_FAQPAGE_DIRECTORY_URL", plugins_url(null, POKA_FAQPAGE_FILE));

// Require the main class file
require_once( POKA_FAQPAGE_DIRECTORY . '/include/main-class.php' );