<?php



// AJAX for categories checking
add_action( 'wp_ajax_bpe_category_validaion', 'bpe_category_validaion' );
add_action( 'wp_ajax_nopriv_bpe_category_validaion', 'bpe_category_validaion' );


function bpe_category_validaion() {

    if(!isset($_POST['network_cats']) || empty($_POST['network_cats']) || !is_array($_POST['network_cats'])){
        wp_send_json_error();
        exit();
    }
    
    $sNetworkTypeCat = 'network_category';
    $sCountryCat = 'country';
    $sNetworkPostType = 'sales_network';

    $posts_ids = get_posts(array(
        'post_type' => $sNetworkPostType,
        'posts_per_page' => '-1',
        "fields" => "ids",
        "tax_query" => array(
            array(
                "taxonomy" => $sNetworkTypeCat,
                "field" => "ID",
                "terms" => $_POST['network_cats'], // Replace with a term from member_tax
            )
        )
    ));


    $aAllowedCountries = array();
    
    if (!empty($posts_ids)) {
        $terms = wp_get_object_terms($posts_ids, $sCountryCat); // replace "other_tax" with terms to retrieve from the other tax

        if (!empty($terms) && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                $aAllowedCountries[] = $term->term_id;
            }
            
            wp_send_json_success($aAllowedCountries);
            exit();
        }
    }
    wp_send_json_error();
   
}

/**
 * Admin form styles inclusion
 */
add_action('admin_enqueue_scripts', 'bpe_admin_enqueue');

function bpe_admin_enqueue() {
    wp_enqueue_style('bpe-admin-style', plugins_url('/assets/style.css', __FILE__));
    
    
    wp_enqueue_script('bpe-admin-select2', plugins_url('/assets/select2.full.min.js', __FILE__), array('jquery'));
    wp_enqueue_style('bpe-admin-select2', plugins_url('/assets/select2.min.css', __FILE__));
    
    wp_enqueue_script('bpe-admin-script', plugins_url('/assets/main.js', __FILE__), array('jquery', 'bpe-admin-select2'));
    wp_localize_script( 'bpe-admin-script', 'bpe_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' )) );
}

/**
 * Admin menu initialization
 */
add_action('admin_menu', 'bpe_setup_menu');

function bpe_setup_menu() {
    add_menu_page('PDF Exporting', 'PDF Exporting', 'manage_options', 'bpe', 'bpe_adminmenu_init', BPE_ABS_URL.'/admin/assets/pdf_16.png');
}

/**
 * Admin menu form output. 
 * Export triggering
 */
function bpe_adminmenu_init() {
    
    

    if (isset($_POST['action'])) {
        $aResult = bpe_init_export($_POST);
    }
    if (function_exists ( 'icl_register_string' )) {
        icl_register_string('bpe_strings', 'Company', 'Company');
        icl_register_string('bpe_strings', 'Industry', 'Industry');
        icl_register_string('bpe_strings', 'Agent', 'Agent');
        icl_register_string('bpe_strings', 'Address', 'Address');
        icl_register_string('bpe_strings', 'City', 'City');
        icl_register_string('bpe_strings', 'ZIP', 'ZIP');
        icl_register_string('bpe_strings', 'E-Mail', 'E-Mail');
        icl_register_string('bpe_strings', 'Website', 'Website');
        icl_register_string('bpe_strings', 'Phone', 'Phone');
        icl_register_string('bpe_strings', 'Fax', 'Fax');
    }
    ?>

    <div class="wrap">

        <?php if (isset($aResult['success'])): ?>
            <div class='bpe_result'>
                Export Successful: <a href="<?= $aResult['success']; ?>" target='_blanc'>Download File</a>
            </div>
        <?php elseif (isset($aResult['error'])) : ?>
            <div class="bpe_error"><?= $aResult['error']; ?></div>
        <?php endif; ?>
        
        <?php // __( 'Company', 'bpe' );
//        echo get_user_locale();
//        echo '<br>';
//        if (function_exists ( 'icl_register_string' )) {
//            echo icl_register_string('bpe_strings', 'Company', 'Company');
//        }
//        echo '<br>';
//        echo icl_t('bpe_strings', 'Company');
//        echo '<br>';_exists( 'icl_t' ) ) {
//            $string = false;
//            $has_translation = null;
//            $bool = false;
//            global $sitepress;
//            $lang = $sitepress->get_current_language();
//            echo icl_t('bpe_strings', 'Company', $string, $has_translation, $bool, $lang);
//
        //        if ( function}
        ?>
        <form method="POST" action="" class="bpe_form">
            <h3>Sales Network PDF Export </h3>
            <input type="hidden" name="action" value="bpe" />
            
            <div class="bpe_form_text-wrap intro">
                <p>In order to export "Sales Network" entities, please select Country (one or multiple) and Netwok Types.</p>
            </div>
            <div class="bpe_form_select-wrap bpe_form_select-network_type">
                <span class="select_label">Select Network Type</span>
                <?php wp_dropdown_categories(array('taxonomy' => 'network_category', 'name' => 'network_category[]')); ?>
            </div>
            <div class="bpe_form_select-wrap bpe_form_select-country">
                <span class="select_label">Select Countries</span>
                <?php wp_dropdown_categories(array('taxonomy' => 'country', 'name' => 'country[]')); ?>
            </div>
            
            <div class="bpe_form_text-wrap warning">
                <p><span class="red">*</span> leaving County and Network Type fields empty consider to export ALL entities</p>
            </div>
            <div class="bpe_form_btn-wrap">
                <input type="submit" class=" button button-primary" value="Export" />
            </div>
        </form>
    </div>
    <?php
}

/**
 * Wp_dropdown_cats filter with capability to multiselect
 */
add_filter('wp_dropdown_cats', 'dropdown_filter', 10, 2);

function dropdown_filter($output, $r) {
    $output = preg_replace('/<select (.*?) >/', '<select $1 size="5" multiple>', $output);
    return $output;
}

function bpe_init_export($aPost) {

    $aSettings = array();

    $aSettings['country'] = (!empty($aPost['country']) &&
            $aPost['country'] == array_filter($aPost['country'], 'is_numeric')) ? $aPost['country'] : array();

    $aSettings['network_category'] = (!empty($aPost['network_category']) &&
            $aPost['network_category'] == array_filter($aPost['network_category'], 'is_numeric')) ? $aPost['network_category'] : array();


    $args = array(
        'post_type' => 'sales_network',
        'posts_per_page' => '-1'
    );

    if (!empty($aSettings['network_category'])) {
        $args['tax_query'][] = array(
            'taxonomy' => 'network_category',
            'field' => 'id',
            'terms' => $aSettings['network_category'],
        );
    }

    if (!empty($aSettings['country']) && !empty($aSettings['network_category'])) {
        $args['tax_query']['relation'] = 'AND';
    }

    $aCountryTerms = get_terms('country');

    if (empty($aCountryTerms))
        return'';

    $aOutput = array();

    foreach ($aCountryTerms as $aCountryTerm) {
        if (empty($aSettings['country']) || (!empty($aSettings['country']) && in_array($aCountryTerm->term_id, $aSettings['country']))) {
            // Do the job
            $aCountryArray = array('country_name' => $aCountryTerm->name);


            $aInternalArgs = $args;
            $aInternalArgs['tax_query'][] = array(
                'taxonomy' => 'country',
                'field' => 'id',
                'terms' => $aCountryTerm->term_id,
            );

            $query = new WP_Query($aInternalArgs);
            $oCountryPosts = $query->posts;

            if(empty($oCountryPosts)){
                continue;
            }
            foreach ($oCountryPosts as $post) {

                $aData = array(
                    'company_name' => ' -- ',
                    'company_info' => ' -- ',
                    'company_address' => ' -- ',
                    'company_city' => ' -- ',
                    'company_zip' => ' -- ',
                    'company_website' => ' -- ',
                    'agent_name' => ' -- ',
                    'agent_mail' => ' -- ',
                    'agent_phone' => ' -- ',
                    'agent_fax' => ' -- '
                );

                $oCompanyTaxonomy = wp_get_post_terms($post->ID, 'company');

                if (!is_wp_error($oCompanyTaxonomy) && !empty($oCompanyTaxonomy)) {
                    $aData['company_name'] = $oCompanyTaxonomy[0]->name;

                    $sCompanyPostcode = get_field('company_postcode', 'term_' . $oCompanyTaxonomy[0]->term_id);
                    $sCompanyCity = get_field('company_city', 'term_' . $oCompanyTaxonomy[0]->term_id);
                    $sCompanyInfo = get_field('company_info', 'term_' . $oCompanyTaxonomy[0]->term_id);
                    $sCompanyAddress = get_field('company_address', 'term_' . $oCompanyTaxonomy[0]->term_id);
                    $sCompanyWebsite = get_field('website', 'term_' . $oCompanyTaxonomy[0]->term_id);

                    //$sCompanyPostcode = (!empty($sCompanyPostcode)) ? $sCompanyPostcode . ', ' : '';
                    //$sCompanyCity = (!empty($sCompanyCity)) ? $sCompanyCity . ', ' : '';
                    // $sCompanyAddress = (!empty($sCompanyAddress)) ? $sCompanyAddress . ', ' : ''; 
                    //$aData['company_address'] = $sCompanyPostcode . $sCompanyCity . $sCompanyAddress;
                    $aData['company_info'] = (!empty($sCompanyInfo)) ? $sCompanyInfo : $aData['company_info'];

                    $aData['company_address'] = (!empty($sCompanyAddress)) ? $sCompanyAddress : $aData['company_address'];
                    $aData['company_city'] = (!empty($sCompanyCity)) ? $sCompanyCity : $aData['company_city'];
                    $aData['company_zip'] = (!empty($sCompanyPostcode)) ? $sCompanyPostcode : $aData['company_zip'];
                    $aData['company_website'] = (!empty($sCompanyWebsite)) ? $sCompanyWebsite : $aData['company_website'];
                }

                $sAgentName = $post->post_title;
                $sAgentMail = get_field('mail', $post->ID);
                $sAgentPhone = get_field('phone', $post->ID);
                $sAgentFax = get_field('fax', $post->ID);

                $aData['agent_name'] = $sAgentName;
                $aData['agent_mail'] = (!empty($sAgentMail)) ? $sAgentMail : $aData['agent_mail'];
                $aData['agent_phone'] = (!empty($sAgentPhone)) ? $sAgentPhone : $aData['agent_phone'];
                $aData['agent_fax'] = (!empty($sAgentFax)) ? $sAgentFax : $aData['agent_fax'];



                $aCountryArray[] = $aData;
            }
            $aOutput[] = $aCountryArray;
        } else {
            continue;
        }
    }

    $fname = BPE_ABS_PATH . 'sales_network_export.pdf';
    $furl = BPE_ABS_URL . 'sales_network_export.pdf';
    
    if(empty($aOutput)){
        return array('error' => 'No records available under speficied filters');
    }
    try {
        $oGetPDF = new PDFconvertor();
        

        /* --mHTML DEBUG --
         * 
         * 
          $sHtml = $oGetPDF->buildHTML($aOutput);
          if (!file_put_contents(BPE_ABS_PATH . 'pdffile.html', $sHtml)) {
          echo 'File access error';
          } else {
          echo 'File saved successfully';
          } */

        $sFilePutRes = @file_put_contents($fname, $oGetPDF->ConvertPDF($oGetPDF->buildHTML($aOutput)));
    } catch (Exception $e) {
        $sFilePutRes = false;
    }

    if (!$sFilePutRes) {
        return array('error' => 'File access error');
    } else {
        return array('success' => $furl);
    }
}
