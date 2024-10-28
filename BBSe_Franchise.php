<?php
/**
 * Plugin Name: BBS e-Franchise
 * Plugin URI: http://www.bbsetheme.com
 * Description: BBS e-Franchise는 프랜차이즈 가맹점 및 지점 목록을 표시하여 매장찾기 서비스를 지원하는 플러그인입니다. 지도에서 지역을 바로 선택하는 '지역명 검색'과 매장명을 직접 검색할 수 있는 '매장명 검색'을 지원하여 원하는 프랜차이즈 가맹점 및 지점을 쉽고 편리하게 찾을 수 있습니다.
 'BBS e-Franchise'plug-in is that supports by displaying franchise stores and branch offices. You can easily find the franchise stores and branches you want by supporting 'Local name search' to select the area directly on the map and 'Store name search' to search the store name directly.
 * Version: 1.2.5
 * Author: BBS e-Theme
 * Author URI: http://www.bbsetheme.com
 * License: GNU General Public License, v2
 * License URI: http://www.gnu.org/licenses/gpl.html
 *
 * 본 플러그인은 워드프레스와 동일한 GPL 라이센스의 플러그인입니다. 임의대로 수정, 삭제 후 이용하셔도 됩니다.
 * 단, 재배포 시 GPL 라이센스로 재배포 되어야 하며, 원 제작자의 표기를 해주시기 바랍니다.
 * 'BBS e-Franchise' WordPress Plugin, Copyright 2014 BBS e-Theme(http://www.bbsetheme.com)
 * 'BBS e-Franchise' is distributed under the terms of the GNU GPL
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define("BBSE_FRANCHISE_VER",             'v1.2.5');
define("BBSE_FRANCHISE_DB_VER",          '2017011601');
define("BBSE_FRANCHISE_URL",             site_url());
define("BBSE_FRANCHISE_PLUGIN_WEB_URL",  plugins_url().'/bbs-e-franchise/');
define("BBSE_FRANCHISE_PLUGIN_ABS_PATH", plugin_dir_path(__FILE__));
define("BBSE_FRANCHISE_TEMPLATES_URL",   BBSE_FRANCHISE_PLUGIN_WEB_URL.'templates/');
define("BBSE_FRANCHISE_TEMPLATES_PATH",  BBSE_FRANCHISE_PLUGIN_ABS_PATH.'templates/');

define("BBSE_FRANCHISE_AGENT",           "bbsetheme");

define("BBSE_FRANCHISE_SETUP_PAGE",      admin_url('/admin.php?page=bbse_franchise_setup'));
define("BBSE_FRANCHISE_WRITE_PAGE",      admin_url('/admin.php?page=bbse_franchise_write'));

require_once(BBSE_FRANCHISE_PLUGIN_ABS_PATH."lib/franchise.class.php");     // main class
$franchise = new FRANCHISE;

// Plugin Activation
register_activation_hook(__FILE__, array($franchise, 'activation'));
// Plugin Deactivation
register_deactivation_hook(__FILE__, array($franchise, 'deactivation'));

// Manager pages
add_action('admin_menu', array($franchise,  'add_admin_menu'));

// Plugin setting page
add_action( 'wp_ajax_bbse_franchise_config_save',               array($franchise, 'config_save') );
add_action( 'wp_ajax_nopriv_manage_bbse_franchise_config_save', array($franchise, 'config_save') );

// Branches manage page
add_action( 'wp_ajax_bbse_franchise_branch_save',               array($franchise, 'branch_save') );
add_action( 'wp_ajax_nopriv_manage_bbse_franchise_branch_save', array($franchise, 'branch_save') );

// Bulk managing for branches list
add_action( 'wp_ajax_bbse_franchise_branch_bulkmanage',               array($franchise, 'branch_bulkmanage') );
add_action( 'wp_ajax_nopriv_manage_bbse_franchise_branch_bulkmanage', array($franchise, 'branch_bulkmanage') );

// Backup plugin data
add_action( 'wp_ajax_bbse_franchise_data_backup',               array($franchise, 'data_backup') );
add_action( 'wp_ajax_nopriv_manage_bbse_franchise_data_backup', array($franchise, 'data_backup') );

// Restore plugin data
add_action( 'wp_ajax_bbse_franchise_data_restore',               array($franchise, 'data_restore') );
add_action( 'wp_ajax_nopriv_manage_bbse_franchise_data_restore', array($franchise, 'data_restore') );

// Delete plugin database tables
add_action( 'wp_ajax_bbse_franchise_delete_tables',               array($franchise, 'delete_tables') );
add_action( 'wp_ajax_nopriv_manage_bbse_franchise_delete_tables', array($franchise, 'delete_tables') );

// Plugin shortcode
add_shortcode('bbse_franchise', array($franchise, 'franchise_shortcode') );


function bbse_franchise_init() {
    load_plugin_textdomain( 'bbsefranchise', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'bbse_franchise_init' );
