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
// $Id: sitemap.php 66961 2021-05-24 13:26:45Z sdelaporte $

include("configuration.inc.php");

define('IN_SITEMAP', true);
$GLOBALS['page_name'] = 'sitemap';
$GLOBALS['DOC_TITLE'] = $GLOBALS['STR_SITEMAP'];

include($GLOBALS['repertoire_modele'] . "/haut.php");
$affiche_contenu_html = affiche_contenu_html('sitemap', true);
if (!empty($affiche_contenu_html)) {
	echo $affiche_contenu_html;
} else {
	print_alpha();
}
include($GLOBALS['repertoire_modele'] . "/bas.php");

