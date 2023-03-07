<?php
/* Copyright (C) 2012 		Juanjo Menent				<jmenent@2byte.es>
 * Copyright (C) 2013-2017 	Ferran Marcet				<fmarcet@2byte.es>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *  \file       htdocs/labelprint/product_liste.php
 *  \ingroup    labelprint
 *  \brief      Page to list all products to label print
 */

$res = @include("../main.inc.php");                                   // For root directory
if (!$res) $res = @include("../../main.inc.php");                // For "custom" directory

require_once(DOL_DOCUMENT_ROOT . '/product/class/product.class.php');
require_once(DOL_DOCUMENT_ROOT . "/core/class/html.formother.class.php");
require_once(DOL_DOCUMENT_ROOT . "/core/lib/admin.lib.php");
dol_include_once("/labelprint/class/labelprint.class.php");
dol_include_once("/labelprint/lib/labelprint.lib.php");

if ($conf->categorie->enabled) require_once(DOL_DOCUMENT_ROOT . "/categories/class/categorie.class.php");

global $langs, $conf, $db, $user;

$langs->load("products");
$langs->load("stocks");
$langs->load("labelprint@labelprint");

$sref = GETPOST('sref', 'alpha');
$sbarcode = GETPOST('sbarcode', 'alpha');
$snom = GETPOST('snom', 'alpha');
$sall = GETPOST("sall", 'alpha');
$type = 0;
$search_sale = GETPOST('search_sale', 'int');
$search_categ = GETPOST('search_categ', 'int');
$tosell = GETPOST('tosell', 'int');
$tobuy = GETPOST('tobuy', 'int');
$line = GETPOST('lineid', 'int');

$sortfield = GETPOST("sortfield", 'alpha');
$sortorder = GETPOST("sortorder", 'alpha');
$page = GETPOST("page", 'int');
if ($page == -1) {
	$page = 0;
}
$offset = $conf->liste_limit * $page;
$pageprev = $page - 1;
$pagenext = $page + 1;
if (!$sortfield) $sortfield = "p.ref";
if (!$sortorder) $sortorder = "ASC";

$action = GETPOST('action', 'alpha');
$confirm = GETPOST('confirm', 'alpha');

$toPrint = GETPOST('toPrint');

// Security check
$result = restrictedArea($user, 'produit');
$limit = $conf->liste_limit;


/*
 * Actions
 */

// Purge search criteria
if (GETPOST('button_removefilter_x','alpha') || GETPOST('button_removefilter.x','alpha') || GETPOST('button_removefilter','alpha')) // All tests are required to be compatible with all browsers
{
	$search_categ = '';
	$sall = '';
	$sref = '';
	$sbarcode = '';
	$snom = '';
	unset($catid);
	$fourn_id = '';
	$search_categ = '';
}

if ($conf->categorie->enabled && GETPOST('catid')) {
	$catid = GETPOST('catid', 'int');
}

if ($action == 'create' && $user->rights->produit->lire && empty(GETPOST('button_search'))) {
	if (is_array($toPrint)) {
		$object = new LabelsProducts($db);
		$result = $object->multicreate($user, $toPrint);

		if ($result < 0) {
			setEventMessage($object->error, "errors");
		} else {
			setEventMessage($langs->trans("LinesAdded"));
		}
	}
}

// Action select position object
if ($action == 'confirm_position' && $confirm != 'yes') {
	$action = '';
}
if ($action == 'confirm_position' && $confirm == 'yes') {
	$position = GETPOST('position', 'int');
	$res += dolibarr_set_const($db, 'LAB_START', $position, 'chaine', 0, '', $conf->entity);

	if (GETPOST('type')=='0'){
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_0 = 1;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_1 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_2 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_3 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_4 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_5 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_6 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_7 = 0;
	}
	elseif (GETPOST('type')=='1'){
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_0 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_1 = 1;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_2 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_3 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_4 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_5 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_6 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_7 = 0;
	}
	elseif (GETPOST('type')=='2'){
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_0 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_1 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_2 = 1;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_3 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_4 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_5 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_6 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_7 = 0;
	}
	elseif (GETPOST('type')=='3'){
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_0 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_1 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_2 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_3 = 1;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_4 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_5 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_6 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_7 = 0;
	}
	elseif (GETPOST('type')=='4'){
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_0 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_1 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_2 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_3 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_4 = 1;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_5 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_6 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_7 = 0;
	}
	elseif (GETPOST('type')=='5'){
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_0 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_1 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_2 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_3 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_4 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_5 = 1;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_6 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_7 = 0;
	}
	elseif (GETPOST('type')=='6'){
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_0 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_1 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_2 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_3 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_4 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_5 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_6 = 1;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_7 = 0;
	}
	elseif (GETPOST('type')=='7'){
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_0 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_1 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_2 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_3 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_4 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_5 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_6 = 0;
		$conf->global->MAIN_MODULE_LABELPRINT_LABELS_7 = 1;
	}

	$pdf = new pdfLabelProducts();
	$url = $pdf->createPdf();
	$action = 'confirm_truncate';
}

// Truncate list to print
if ($action == "confirm_truncate" && $confirm == 'yes') {
	$object = new LabelsProducts($db);
	$result = $object->truncate();

	if ($result > 0) {
		if (empty($url)) {
			setEventMessage($langs->trans("ListTruncated"));
			Header('Location: ' . $_SERVER["PHP_SELF"]);
			exit;
		}
	} else {
		setEventMessage($object->error, "errors");
	}
}

// Add product to list
if ($action == 'updateline') {
	if (GETPOST('save', 'alpha') != '') {
		$qty = GETPOST('qty', 'int');
		$price_level = GETPOST('pricelevel', 'int');

		if ($qty <= 0) {

			$object = new LabelsProducts($db);
			$result = $object->delete($line);

		} else {

			$object = new LabelsProducts($db);
			$object->fetch($line);
			$object->qty = $qty;
			$object->price_level = $price_level;
			if($object->price_level==''){
				$object->price_level=1;
			}
			$result = $object->update();
		}

		if ($result > 0) {
			setEventMessage($langs->trans("LineUpdated"));
		} else {
			setEventMessage($object->error, "errors");
		}
	}
}

// Generate Barcode
if ($action == 'genbarcode') {
	$prod_id = GETPOST('prod_id', 'int');

	$object = new LabelsProducts($db);
	$object->fetch(0);
	$result = $object->generate_barcode($prod_id);

	if ($result > 0) {
		setEventMessage($langs->trans("BarcodeGenerated"));
	} else {
		setEventMessage($object->error, "errors");
	}
}

/*
 * View
 */

$htmlother = new FormOther($db);

$html = new Form($db);
$form = new Form($db);

$title = $langs->trans("ProductsAndServices");
$texte = $langs->trans("Module400007Desc");

$sql = 'SELECT DISTINCT p.rowid, p.ref, p.label, p.barcode, p.price, p.price_ttc, p.price_base_type,';
$sql .= ' p.fk_product_type, p.tms as datem,';
$sql .= ' p.duration, p.seuil_stock_alerte';

$sql .= ", SUM(s.reel) as stock";

$sql .= ' FROM ' . MAIN_DB_PREFIX . 'product as p';
$sql .= ", " . MAIN_DB_PREFIX . "product_stock as s";

// We'll need this table joined to the select in order to filter by categ
if ($search_categ) $sql .= ", " . MAIN_DB_PREFIX . "categorie_product as cp";
if ($_GET["fourn_id"] > 0)  // The DISTINCT is used to avoid duplicate from this link
{
	$fourn_id = $_GET["fourn_id"];
	$sql .= ", " . MAIN_DB_PREFIX . "product_fournisseur as pf";
}
$sql .= ' WHERE p.entity IN (0,' . (!empty($conf->entities['product']) ? $conf->entities['product'] : $conf->entity) . ')';
if ($search_categ) $sql .= " AND p.rowid = cp.fk_product";    // Join for the needed table to filter by categ
if ($sall) {
	$sql .= " AND (p.ref LIKE '%" . $db->escape($sall) . "%' OR p.label LIKE '%" . $db->escape($sall) . "%' OR p.description LIKE '%" . $db->escape($sall) . "%' OR p.note LIKE '%" . $db->escape($sall) . "%')";
}

$sql .= " AND p.fk_product_type <> '1'";
$sql .= " AND s.fk_product = p.rowid AND s.reel>0";

if ($sref) $sql .= " AND p.ref like '%" . $sref . "%'";
if ($sbarcode) $sql .= " AND p.barcode like '%" . $sbarcode . "%'";
if ($snom) $sql .= " AND p.label like '%" . $db->escape($snom) . "%'";

if (dol_strlen($canvas) > 0) {
	$sql .= " AND p.canvas = '" . $db->escape($canvas) . "'";
}
if ($catid) {
	$sql .= " AND cp.fk_categorie = " . $catid;
}
if ($fourn_id > 0) {
	$sql .= " AND p.rowid = pf.fk_product AND pf.fk_soc = " . $fourn_id;
}
// Insert categ filter
if ($search_categ) {
	$sql .= " AND cp.fk_categorie = " . $db->escape($search_categ);
}
$sql .= " GROUP BY p.rowid, p.ref, p.label, p.barcode, p.price, p.price_ttc, p.price_base_type,";
$sql .= " p.fk_product_type, p.tms,";
$sql .= " p.seuil_stock_alerte";
//mysql strict
$sql .= ", p.duration";
//
if (GETPOST("toolowstock")) $sql .= " HAVING SUM(s.reel) < p.seuil_stock_alerte";    // Not used yet
$sql .= $db->order($sortfield, $sortorder);
$sql .= $db->plimit($limit + 1, $offset);
$resql = $db->query($sql);

if ($resql) {
	$num = $db->num_rows($resql);

	$i = 0;

	$helpurl = 'EN:Module_Labels|FR:Module_Labels_FR|ES:M&oacute;dulo_Labels';

	llxHeader('', $title, $helpurl, '');

	if (!empty($url)) {
		print '<script language="javascript" type="text/javascript">

				window.open("' . $url . '" );
	</script>';
	}

	// Confirmation to delete invoice
	if ($action == 'truncate') {
		$text = $langs->trans('ConfirmTruncateList');
		$formconfirm = $html->formconfirm($_SERVER['PHP_SELF'] . "?id=0", $langs->trans('TruncateList'), $text, 'confirm_truncate', '', 0, 1);
		print $formconfirm;
	}

	if($action == 'confirm_position1' && $confirm == 'yes'){

		$formquestionmassinvoicing = array(
			'text' => '',
			array(
				'type' => 'radio',
				'name' => 'type',
				'label' => $langs->trans('Labels'),
				'values' => array('70x36','70x37','38x21','48x25 Avery 3657','48x25 Apli 1223','105x37','55x25','105x57'),
				'size' => 10
			)
		);

		print $form->formconfirm($_SERVER['PHP_SELF'], $langs->trans('SelectLabelType'),
			$langs->trans('ConfirmSelectLabelType'), 'confirm_position&position='.GETPOST('position'), $formquestionmassinvoicing, 'yes', 1, 250,
			420);
	}

	$formquestionposition = array(
		'text' => $langs->trans("ConfirmPosition"),
		array('type' => 'text', 'name' => 'position', 'label' => $langs->trans("HowManyPos"), 'value' => $conf->global->LAB_START, 'size' => 5)
	);

	$param = "&amp;sref=" . $sref . ($sbarcode ? "&amp;sbarcode=" . $sbarcode : "") . "&amp;snom=" . $snom . "&amp;sall=" . $sall . "&amp;tosell=" . $tosell . "&amp;tobuy=" . $tobuy;
	$param .= ($fourn_id ? "&amp;fourn_id=" . $fourn_id : "");
	$param .= isset($type) ? "&amp;type=" . $type : "";
	$param .= "&amp;search_categ=" . $search_categ;
	print_barre_liste($texte, $page, $_SERVER["PHP_SELF"], $param, $sortfield, $sortorder, '', $num);

	if ($num) {
		print "<div class=\"tabsAction\">";

		$sql = "SELECT rowid";
		$sql .= " FROM " . MAIN_DB_PREFIX . "labelprint";
		$sql .= " WHERE entity='" . $conf->entity . "'";
		$sql .= " AND typLabel=0";

		$result = $db->query($sql);
		if ($result) {
			$numrows = $db->num_rows($result);
			if ($numrows) {

				if ($user->rights->produit->lire) {
					print '<a class="butActionDelete" href="' . $_SERVER["PHP_SELF"] . '?action=truncate">' . $langs->trans("Truncate") . '</a>';
					if ($conf->global->LABELPRINT_A4) {
						print '<span id="action-position" class="butAction">' . $langs->trans('PrintLabels') . '</span>' . "\n";
						print $form->formconfirm($_SERVER["PHP_SELF"], $langs->trans('SelectPosition'), $langs->trans('ConfirmSelectPosition'), 'confirm_position1', $formquestionposition, 'yes', 'action-position', 270, 400);
					} else {
						print '<a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?action=confirm_position1&amp;confirm=yes">' . $langs->trans('PrintLabels') . '</a>';
					}
				}
			} else {
				print '<a class="butActionRefused" title="' . $langs->trans("ListTruncated") . '">' . $langs->trans("Truncate") . '</a>';
				print '<a class="butActionRefused" title="' . $langs->trans("ListTruncated") . '">' . $langs->trans("PrintLabels") . '</a>';
			}

		}
		print "</div>";
		print '<br>';
	}

	if (isset($catid)) {
		print "<div id='ways'>";
		$c = new Categorie ($db, $catid);
		$ways = $c->print_all_ways(' &gt; ', 'labelprint/product_list.php');
		print " &gt; " . $ways[0] . "<br>\n";
		print "</div><br>";
	}


	print '<form action="' . $_SERVER["PHP_SELF"] . '" method="POST">';
	print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
	print '<input type="hidden" name="action" value="create">';
	print '<input type="hidden" name="sortfield" value="' . $sortfield . '">';
	print '<input type="hidden" name="sortorder" value="' . $sortorder . '">';
	print '<input type="hidden" name="page" value="' . $page . '">';

	print '<table class="liste" width="100%">';

	// Filter on categories
	$moreforfilter = '';
	if ($conf->categorie->enabled) {
		$moreforfilter .= $langs->trans('Categories') . ': ';
		$moreforfilter .= $htmlother->select_categories(0, $search_categ, 'search_categ');
		$moreforfilter .= ' &nbsp; &nbsp; &nbsp; ';
	}
	if ($moreforfilter) {
		print '<tr class="liste_titre">';
		print '<td class="liste_titre" colspan="8">';
		print $moreforfilter;
		print '</td></tr>';
		print '</td></tr>';
	}

	print '
	<script language="javascript" type="text/javascript">
	jQuery(document).ready(function()
	{
		jQuery("#checkall").click(function()
		{
			jQuery(".checkforproduct").attr(\'checked\', true);
		});
		jQuery("#checknone").click(function()
		{
			jQuery(".checkforproduct").attr(\'checked\', false);
		});
	});
	</script>
	';

	// title lines
	print "<tr class=\"liste_titre\">";
	print_liste_field_titre($langs->trans("Ref"), $_SERVER["PHP_SELF"], "p.ref", $param, "", "", $sortfield, $sortorder);
	print_liste_field_titre($langs->trans("Label"), $_SERVER["PHP_SELF"], "p.label", $param, "", "", $sortfield, $sortorder);
	if ($conf->barcode->enabled) print_liste_field_titre($langs->trans("BarCode"), $_SERVER["PHP_SELF"], "p.barcode", $param, "", "", $sortfield, $sortorder);
	print_liste_field_titre($langs->trans("SellingPrice"), $_SERVER["PHP_SELF"], "p.price", $param, "", 'align="right"', $sortfield, $sortorder);
	if (!empty($conf->global->PRODUIT_MULTIPRICES)) print '<td align="right">' . $langs->trans("PriceLevel") . '</td>';
	if ($conf->stock->enabled && $user->rights->stock->lire && $type != 1) print '<td class="liste_titre" align="right">' . $langs->trans("PhysicalStock") . '</td>';
	print '<td class="liste_titre" align="right">' . $langs->trans("QtyToPrint") . '</td>';
	print '<td align="center" width="100px">' . $langs->trans("Select") . "<br>";
	if ($conf->use_javascript_ajax) print '<a href="#" id="checkall">' . $langs->trans("All") . '</a> / <a href="#" id="checknone">' . $langs->trans("None") . '</a>';
	print '</td>';
	print "</tr>\n";

	// Filter lines
	print '<tr class="liste_titre">';
	// Product ref
	print '<td class="liste_titre" align="left">';
	print '<input class="flat" type="text" name="sref" size="8" value="' . $sref . '">';
	print '</td>';

	// Product label
	print '<td class="liste_titre" align="left">';
	print '<input class="flat" type="text" name="snom" size="12" value="' . $snom . '">';
	print '</td>';

	// Bar code
	if ($conf->barcode->enabled) {
		print '<td class="liste_titre">';
		print '<input class="flat" type="text" name="sbarcode" size="6" value="' . $sbarcode . '">';
		print '</td>';
	}

	// Sell price
	print '<td class="liste_titre">';
	print '&nbsp;';
	print '</td>';

	// Price level
	if (!empty($conf->global->PRODUIT_MULTIPRICES)) {
		print '<td class="liste_titre">';
		print '&nbsp;';
		print '</td>';
	}

	// Stock
	if ($conf->stock->enabled && $user->rights->stock->lire && $type != 1) {
		print '<td class="liste_titre">';
		print '&nbsp;';
		print '</td>';
	}

	// To print
	print '<td class="liste_titre">';
	print '&nbsp;';
	print '</td>';

	print '<td class="liste_titre" align="right">';
	print '<input type="image" class="liste_titre" name="button_search" src="'.img_picto($langs->trans("Search"),'search.png','','',1).'" value="' . dol_escape_htmltag($langs->trans("Search")) . '" title="' . dol_escape_htmltag($langs->trans("Search")) . '">';
	print '<input type="image" class="liste_titre" name="button_removefilter" src="'.img_picto($langs->trans("Search"),'searchclear.png','','',1).'" value="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'" title="'.dol_escape_htmltag($langs->trans("RemoveFilter")).'">';
	print '</td>';
	print '</tr>';

	$product_static = new Product($db);

	$var = True;
	while ($i < min($num, $limit)) {
		$objp = $db->fetch_object($resql);

		// Multilangs
		if ($conf->global->MAIN_MULTILANGS) {
			$sql = "SELECT label";
			$sql .= " FROM " . MAIN_DB_PREFIX . "product_lang";
			$sql .= " WHERE fk_product=" . $objp->rowid;
			$sql .= " AND lang='" . $langs->getDefaultLang() . "'";
			$sql .= " LIMIT 1";

			$result = $db->query($sql);
			if ($result) {
				$objtp = $db->fetch_object($result);
				if ($objtp->label != '') $objp->label = $objtp->label;
			}
		}

		$var = !$var;
		print '<tr ' . $bc[$var ? 1 : 0] . '>';

		// Ref
		print '<td nowrap="nowrap">';
		$product_static->id = $objp->rowid;
		$product_static->ref = $objp->ref;
		$product_static->type = $objp->fk_product_type;
		print $product_static->getNomUrl(1, '', 24);
		print "</td>\n";

		// Label
		print '<td>' . dol_trunc($objp->label, 40) . '</td>';

		//Barcode
		if ($conf->barcode->enabled) {
			if ($objp->barcode) {
				print '<td align="right">' . $objp->barcode . '</td>';
			} else if ($conf->global->PRODUIT_DEFAULT_BARCODE_TYPE == 2) {
				print '<td align="right"><a href="' . $_SERVER["PHP_SELF"] . '?prod_id=' . $objp->rowid . '&action=genbarcode&page=' . $page . '&sortfield=' . $sortfield . '&sortorder=' . $sortorder . $param . '">' . $langs->trans("GenerateBarcode") . '</a></td>';
			} else {
				print '<td align="right"></td>';
			}
		}

		$sql = "SELECT rowid, qty, price_level";
		$sql .= " FROM " . MAIN_DB_PREFIX . "labelprint";
		$sql .= " WHERE fk_object=" . $objp->rowid;
		$sql .= " AND entity='" . $conf->entity . "'";
		$sql .= " AND typLabel=0";

		$result = $db->query($sql);

		if ($result) {
			$objtp = $db->fetch_object($result);

		}
		if (!$objtp) {
			$price_level = ($conf->global->LABELPRINT_LEVEL_PRICE>0?$conf->global->LABELPRINT_LEVEL_PRICE:1);
		} else $price_level = $objtp->price_level;

		// Sell price
		if (empty($conf->global->PRODUIT_MULTIPRICES)) {
			print '<td align="right">';
			if ($objp->price_base_type == 'TTC') print price($objp->price_ttc) . ' ' . $langs->trans("TTC");
			else print price($objp->price) . ' ' . $langs->trans("HT");
			print '</td>';
		} else {
			$product_static->fetch($objp->rowid);
			print '<td align="right">';
			if ($product_static->multiprices_base_type[$price_level] == 'TTC') print price($product_static->multiprices_ttc[$price_level]) . ' ' . $langs->trans("TTC");
			else print price($product_static->multiprices[$price_level]) . ' ' . $langs->trans("HT");
			print '</td>';
		}

		// Price level
		if (!empty($conf->global->PRODUIT_MULTIPRICES)) {
			if ($action == 'editline' && $user->rights->produit->creer && $line == $objtp->rowid) {
				print '<form action="' . $_SERVER["PHP_SELF"] . '?id=' . $product->id . '" method="post">';

				print '<td align="right">';

				print select_price_level($price_level);
				print '</td>';
			} else {
				print '<td align="right">';
				print $price_level;
				print '</td>';
				print '</form>';
			}
		}

		// Show stock
		if ($conf->stock->enabled && $user->rights->stock->lire && $type != 1) {
			if ($objp->fk_product_type != 1) {
				$product_static->id = $objp->rowid;
				$product_static->load_stock();
				print '<td align="right">';
				if ($product_static->stock_reel < $objp->seuil_stock_alerte) print img_warning($langs->trans("StockTooLow")) . ' ';
				print $product_static->stock_reel;
				print '</td>';
			} else {
				print '<td>&nbsp;</td>';
			}
		}

		// Qty to print
		if ($result) {
			//$objtp = $db->fetch_object($result);
			if ($action == 'editline' && $user->rights->produit->creer && $line == $objtp->rowid) {
				print '<td align="right">';
				print '<form action="' . $_SERVER["PHP_SELF"] . '?id=' . $product->id . '" method="post">';
				if (empty($conf->global->PRODUIT_MULTIPRICES)) {
					print '<input type="hidden" name="price_level" value="1"> ';

				}
				print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
				print '<input type="hidden" name="action" value="updateline">';
				print '<input type="hidden" name="id" value="' . $product->id . '">';
				print '<input type="hidden" name="lineid" value="' . $line . '">';
				print '<input type="hidden" name="page" value="' . $page . '">';

				print '<input class="flat" type="text" size="2" name="qty" value="' . $objtp->qty . '"> ';
				print '<input type="submit" class="button" name="save" value="' . $langs->trans("Save") . '">';
				print '<br><input type="submit" class="button" name="cancel" value="' . $langs->trans("Cancel") . '">';

				print '</td>';
				print '</form>';
			} else
				print '<td align="right">' . $objtp->qty . '</td>';


		} else {
			print '<td align="right">0</td>';
		}

		print '<td align="center">';
		print '<input id="' . $i . '" class="flat checkforproduct" type="checkbox" name="toPrint[]" value="' . $objp->rowid . '">';

		if ($user->rights->produit->creer && $action != 'editline' && $objtp->qty) {
			print '<a href="' . $_SERVER["PHP_SELF"] . '?action=editline&amp;page=' . $page . '&amp;lineid=' . $objtp->rowid . $param . '">';
			print img_edit();
			print '</a>';
		}
		print '</td>';
		print "</tr>\n";
		$i++;
	}

	$db->free($resql);

	print "</table>";

	/*
	 * Boutons Actions
	 */

	print '<div class="tabsAction">';

	if ($user->rights->produit->creer) {
		print '<input type="submit" class="button" value="' . $langs->trans("AddToPrint") . '">';
	}
	print "</div>";
	print '<br>';
	print '</form>';

	print "</div>";

} else {
	dol_print_error($db);
}
dol_htmloutput_events();

$db->close();

llxFooter();
