<?php

use Dompdf\Dompdf;
use Dompdf\Options;

class PDFconvertor {

    // HTML-->PDF convert function 
    function ConvertPDF($HTMLin) {

        //option list
        $options = new Options();
        $options->set('isHtml5ParserEnabled', 'true'); //set HTML5 parser on
        $options->set('isRemoteEnabled', 'true');   // --->DEBUG OPTION!!!<---

        $pdfData = new Dompdf($options);
        // HTML convert			
        if (strlen($HTMLin) > 0) {
            $pdfData->load_html($HTMLin);
            $pdfData->render();

            $PDFoutput = $pdfData->output();
            return($PDFoutput);
        } else {
            exit("Empty input");
        }
        //$pdfData->stream("pdf_out.pdf", array("Attachment" => false));
    }

    //build data rows from data array
    function buildRowHTML($sValue, $iCounter, $iTotalCounter) {


        $sTableRight = ($iCounter % 2 == 0 ) ? 'even' : 'odd';
        $sTableSeparator = ($iCounter % 2 == 0 && $iTotalCounter-1 != $iCounter) ? '<div class="sep sep_margin"></div>' : '';


//        $first_td_string_company = translate( 'Company', 'bpe' );
//        global $sitepress;
//        $lang = $sitepress->get_current_language();
        if ( function_exists( 'icl_t' ) ) {
            $string = false;
            $has_translation = null;
            $bool = false;
            global $sitepress;
            $lang = $sitepress->get_current_language();
            $first_td_string_company = icl_t('bpe_strings', 'Company', $string, $has_translation, $bool, $lang);
            $first_td_string_industry = icl_t('bpe_strings', 'Industry', $string, $has_translation, $bool, $lang);
            $first_td_string_agent = icl_t('bpe_strings', 'Agent', $string, $has_translation, $bool, $lang);
            $first_td_string_address = icl_t('bpe_strings', 'Address', $string, $has_translation, $bool, $lang);
            $first_td_string_city = icl_t('bpe_strings', 'City', $string, $has_translation, $bool, $lang);
            $first_td_string_zip = icl_t('bpe_strings', 'ZIP', $string, $has_translation, $bool, $lang);
            $first_td_string_email = icl_t('bpe_strings', 'E-Mail', $string, $has_translation, $bool, $lang);
            $first_td_string_website = icl_t('bpe_strings', 'Website', $string, $has_translation, $bool, $lang);
            $first_td_string_phone = icl_t('bpe_strings', 'Phone', $string, $has_translation, $bool, $lang);
            $first_td_string_fax = icl_t('bpe_strings', 'Fax', $string, $has_translation, $bool, $lang);
        }
        if ($sValue['company_info'] == ' -- ') { $display = "hidden"; }
        //build table rows
        return "
            <div class='{$sTableRight}' style='height:230px; max-height:230px; border:2px solid #ccc; padding:5px 20px 15px 20px; float:left;'>
                <h4 style='font-size:18px;'>{$sValue['agent_name']}</h4>
                <div style='width:100px; height:2px; background:#f0f;'></div>    
            <table style='width:310px;' >
            <tr>
                <td class='first_td'>{$first_td_string_company}</td>
                <td>{$sValue['company_name']}</td>
            </tr>
            <tr class='{$display}'>
                <td class='first_td'>{$first_td_string_industry}</td>
                <td>{$sValue['company_info']}</td>
            </tr>
            <tr>
                <td class='first_td'>{$first_td_string_agent}</td>
                <td>{$sValue['agent_name']}</td>
            </tr>
            <tr>
                <td class='first_td'>{$first_td_string_address}</td>
                <td>{$sValue['company_address']}</td>
            </tr>
            <tr>
                <td class='first_td'>{$first_td_string_city}</td>
                <td>{$sValue['company_city']}</td>
            </tr>
            <tr>
                <td class='first_td'>{$first_td_string_zip}</td>
                <td>{$sValue['company_zip']}</td>
            </tr>
            <tr>
                <td class='first_td'>{$first_td_string_email}</td>
                <td>{$sValue['agent_mail']}</td>
            </tr>
            <tr>
                <td class='first_td'>{$first_td_string_website}</td>
                <td>{$sValue['company_website']}</td>
            </tr>
            <tr>
                <td class='first_td'>{$first_td_string_phone}</td>
                <td>{$sValue['agent_phone']}</td>
            </tr>
            <tr>
                <td class='first_td'>{$first_td_string_fax}</td>
                <td>{$sValue['agent_fax']}</td>
            </tr>
 
            </table>
            </div>
            $sTableSeparator
            ";
    }

    //build HTML for converting
    function buildHTML($data_array) {

        //HTML header layout
        $sResult = "
						<html>	
							<style>       
                                                                @page { margin: 15px; }
								body{
                                                                    margin: 20px;
                                                                    line-height: 1.3em;
                                                                    font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Oxygen-Sans, Ubuntu, Cantarell, \"Helvetica Neue\", sans-serif;
								}
                                                                img{line-height:1; margin:0px;}
                                                                    
                                                                body h3{
                                                                    text-transform:uppercase;
                                                                    padding:7px 0;
                                                                    margin:0px;
                                                                }
								table{
                                                                    font-family: Sans-Serif;
                                                                    font-size: 12px;
                                                                    margin: 0px;
                                                                    text-align: left;
                                                                    border-spacing:1px;									
								}
                                                                
                                                                .even{
                                                                    margin-left:30px;
                                                                }
								#hor-zebra .odd{
                                                                    background: #E5F3FF; 
								}
                                                               
                                                                .first_td{
                                                                    width:65px;
                                                                    font-weight:bold;                                               }
                                                                .sep{
                                                                    clear: both;
                                                                    width: 100%;
                                                                    
                                                                }
                                                                .sep_margin{
                                                                height:13px;
                                                                }
                                                                h4{
                                                                    padding:3px 0;
                                                                    margin:0px;
                                                                }
                                                                .hidden {
                                                                display: none;
                                                                }
							</style>
								
							<body>
                                                            <div class='sep'></div>
                                                            <img src='" . BPE_ABS_PATH . "admin/assets/berndorf_logo_800.gif' style='display:block; width:250px height:84px; float:right; ' />
                                                            <div class='sep'></div>		
							";

        //EOF header layout
        $GetPDF = new PDFconvertor();
        $iRowsPerPage = 0;
        foreach ($data_array as $pdf_input_data) {
            $iCounter = 0;
            $iTotalCounter = count($pdf_input_data);

            //$sResult .= $iTotalCounter;
            foreach ($pdf_input_data as $sKey => $sValue) {


                if ($sKey === 'country_name') {
                    $sResult .= "<h3 style='clear:both'>" . ($sValue) . "</h3>";
                } else {
                    $iCounter++;
                    $sResult .= $GetPDF->buildRowHTML($sValue, $iCounter, $iTotalCounter);
                    
                    
                    
                }
                if ($iCounter % 2 == 0 && $iCounter != 0 || $iTotalCounter-1 == $iCounter) {
                    $iRowsPerPage++;
                }

               /* if ($sKey !== 'country_name') {
                $sResult .= " <br>Row: $iRowsPerPage || Counter: $iCounter <br>";
                
                }*/
                
                
                if ($iRowsPerPage == 3) {
                    $sResult .= "<div class='sep'></div>"
                            . "<img src='" . BPE_ABS_PATH . "admin/assets/berndorf_logo_800.gif' style='display:block; width:250px height:84px; float:right;' />"
                            . "<div class='sep'></div>";
                    $iRowsPerPage = 0;
                }
                
                
            }
        }
        return $sResult;
    }

}
