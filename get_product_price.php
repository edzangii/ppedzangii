<?php
// This file should be in UTF8 without BOM - Accents examples: éèê
// +----------------------------------------------------------------------+
// | Copyright (c) 2004-2021 Advisto SAS, service PEEL - contact@peel.fr  |
// +----------------------------------------------------------------------+
// | This file is part of PEEL Shopping 9.4.0, which is subject to an	  |
// | opensource GPL license: you are allowed to customize the code		  |
// | for your own needs, but must keep your changes under GPL			  |
// | More information: https://www.peel.fr/lire/licence-gpl-70.html		  |
// +----------------------------------------------------------------------+
// | Author: Advisto SAS, RCS 479 205 452, France, https://www.peel.fr/	  |
// +----------------------------------------------------------------------+
// $Id: get_product_price.php 66961 2021-05-24 13:26:45Z sdelaporte $
include("configuration.inc.php");

if (empty($_POST) || empty($_POST['product_id']) || vb($_POST['hash']) != sha256('HFhza8462naf' . $_POST['product_id'])) {
	die();
}
if (!empty($_GET['encoding'])) {
	$page_encoding = $_GET['encoding'];
} else {
	$page_encoding = 'utf-8';
}
output_general_http_header($page_encoding);
$output = '';
$product_id = intval(vn($_POST['product_id']));
$attribut_list = vb($_POST['attribut_list']);
if (!empty($_SESSION['session_attributs_step'])) {
	foreach(vb($_SESSION['session_attributs_step'], array()) as $this_attribut_list) {
		// On recompose la liste des attributs pour la passer en paramètre de affiche_attributs_form_part
		$attribut_list_array[] = $this_attribut_list;
	}
	$attribut_list .= '§'.implode('§', $attribut_list_array);
}
$size_id = intval(vn($_POST['size_id']));
$color_id = intval(vn($_POST['color_id']));
$product_object = new Product($product_id, null, false, null, true, !is_user_tva_intracom_for_no_vat() && !check_if_module_active('micro_entreprise'));
$product_object->set_configuration($color_id, $size_id, $attribut_list, check_if_module_active('reseller') && is_reseller());

$product_id = intval(vn($_POST['product_id']));
$quantite_total = 0;
if(!empty($_POST['qte_array'])){
	foreach($_POST['qte_array'] as $key => $value) {
		$quantite_total += $value;
	}
}
if (!empty($_POST['size_id_array']) && !empty($quantite_total)) {
	$product_object = new Product($product_id, null, false, null, true, !is_user_tva_intracom_for_no_vat() && !is_micro_entreprise_module_active());
	$prix = $product_object->get_final_price(get_current_user_promotion_percentage(), display_prices_with_taxes_active(), check_if_module_active('reseller') && is_reseller(), false, false, 1, true, true, true);
	foreach ($_POST['size_id_array'] as $key => $this_size_id) {
		$product_object->set_configuration(null, $this_size_id, $attribut_list, check_if_module_active('reseller') && is_reseller(), false, vn($_POST['qte_array'][$key]));
		$size_array = $product_object->get_size('infos', 0, false, check_if_module_active('reseller') && is_reseller(), false, false);
		$prix += $size_array['row_final_price']*vn($_POST['qte_array'][$key]);
	}
} else {
	$prix = $product_object->get_final_price(get_current_user_promotion_percentage(), display_prices_with_taxes_active(), check_if_module_active('reseller') && is_reseller(), false, false, 1, true, true, true);
}

$threshold_not_reached = false;
if (!empty($GLOBALS['site_parameters']['product_minimal_price_threshold']) &&  $prix < $GLOBALS['site_parameters']['product_minimal_price_threshold'] && $product_object->technical_code == 'surface') {
	$prix = $GLOBALS['site_parameters']['product_minimal_price_apply'];
	$threshold_not_reached = true;
}
if(!empty($_POST['product2_id'])) {
	$product2_id = intval(vn($_POST['product2_id']));
	$product_object2 = new Product($product2_id, null, false, null, true, !is_user_tva_intracom_for_no_vat() && !check_if_module_active('micro_entreprise'));
	$product_object2->set_configuration(null, $size_id, $attribut_list, check_if_module_active('reseller') && is_reseller());
	$prix += $product_object2->get_final_price(get_current_user_promotion_percentage(), display_prices_with_taxes_active(), check_if_module_active('reseller') && is_reseller(), false, false, vn($_POST['quantite'], 1), true, true, true);
}
$output .= fprix($prix, true, null, true, null, false, true, ',', true); 
if (!display_prices_with_taxes_active() || !empty($GLOBALS['site_parameters']['price_force_tax_display_on_product_and_category_pages'])) {
	// !display_prices_with_taxes_active() : On n'affiche pas d'info sur la taxe sur le site si il est configuré en TTC, pour une présentation plus agréable.
	// !empty($GLOBALS['site_parameters']['price_force_tax_display_on_product_and_category_pages']): L'admin a configuré l'affichage.
	$output .= ' ' . (display_prices_with_taxes_active()?$GLOBALS['STR_TTC']:$GLOBALS['STR_HT']);
}
echo StringMb::convert_encoding($output, $page_encoding, GENERAL_ENCODING);

