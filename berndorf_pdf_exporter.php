<?php

/**
 * Plugin Name:       Berndorf PDF Exporter
 * Description:       PDF Exporter
 * Version:           1.0.0
 * Author:            Ignat Bohatur <ignat.bohatur@gmail.com>   
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bpe
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

define('BPE_VERSION', '1.0.0');
define('BPE_ABS_PATH', plugin_dir_path(__FILE__));
define('BPE_ABS_URL', plugin_dir_url(__FILE__));

/* Include PDF Converter Class */

require_once plugin_dir_path(__FILE__) . 'admin/classes/PDFconverter.php';
require_once plugin_dir_path(__FILE__) . 'admin/lib/dompdf/autoload.inc.php';

function activate_berndorf_pdf_exporter() {
    
}

function deactivate_berndorf_pdf_exporter() {
    
}

register_activation_hook(__FILE__, 'activate_berndorf_pdf_exporter');
register_deactivation_hook(__FILE__, 'deactivate_berndorf_pdf_exporter');

/**
 * Begins execution of the plugin.
 */
function run_berndorf_pdf_exporter() {
    require plugin_dir_path(__FILE__) . 'admin/admin_init.php';
}

run_berndorf_pdf_exporter();



