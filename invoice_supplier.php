<?php
/* Copyright (C) 2012		Juanjo Menent <jmenent@2byte.es>
 * Copyright (C) 2013		Ferran Marcet <fmarcet@2byte.es>
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
 *	\file       htdocs/labelprint/product.php
 *	\ingroup    labelprint
 *	\brief      Page to list products to print
 */
$res = @include("../main.inc.php");                    // For root directory
if (!$res) $res = @include('../../main.inc.php');    // For "custom" directory

require_once(DOL_DOCUMENT_ROOT . "/product/class/product.class.php");
require_once(DOL_DOCUMENT_ROOT . "/core/lib/product.lib.php");
require_once(DOL_DOCUMENT_ROOT . "/product/class/html.formproduct.class.php");
require_once(DOL_DOCUMENT_ROOT . '/fourn/class/fournisseur.class.php');
require_once(DOL_DOCUMENT_ROOT . "/fourn/class/fournisseur.facture.class.php");
require_once(DOL_DOCUMENT_ROOT . '/core/lib/fourn.lib.php');
dol_include_once("/labelprint/class/labelprint.class.php");
dol_include_once("/labelprint/lib/labelprint.lib.php");
require_once(DOL_DOCUMENT_ROOT . '/core/class/html.formcompany.class.php');
require_once(DOL_DOCUMENT_ROOT . "/contact/class/contact.class.php");
require_once(DOL_DOCUMENT_ROOT . "/core/lib/admin.lib.php");

global $langs, $conf, $db, $user;

$langs->load("products");
$langs->load("stocks");
$langs->load("labelprint@labelprint");

$action = GETPOST('action', 'alpha');
$confirm = GETPOST('confirm', 'alpha');
$id = GETPOST('id', 'int');
$ref = GETPOST('ref', 'alpha');
$line = GETPOST('lineid', 'int');

if ($user->socid) $socid = $user->socid;
//$result=restrictedArea($user,'produit');

/*
 *	Actions
 */

// Add product to list
if ($action == 'add') {
    $fac = new FactureFournisseur($db);
    $fac->fetch($id, $ref);
    $error = 0;
    $errorObject = 0;
    $MaxLines = count($fac->lines);
    for ($i = 0; $i < $MaxLines; $i++) {
        $object = new LabelsProducts($db);
        $object->fk_object = $fac->lines[$i]->fk_product;
        if (!empty($object->fk_object)) {
            $object->qty = $fac->lines[$i]->qty;
            $result = $object->create($user);
            if (!$result) $error++;
        } else {
            $errorObject++;
        }


    }

    if ($errorObject) {
        setEventMessage($langs->trans("LabelsWarningRefProduct"), "warnings");
    }
    if ($error) {
        setEventMessage($object->error, "errors");
    } else if ((count($fac->lines) - $errorObject) > 0) {
        setEventMessage($langs->trans("LinesAdded"));
    }
}

// Print list
if ($action == 'print') {
    /*$pdf=new pdfLabel();
    $pdf->createPdf();
    /*$res = $pdf->createPdf();
    if ($result)
    {
        Header('Location: '.$_SERVER["PHP_SELF"].'?id='.$id);
        exit;
    }*/
}


// Action select position object
if ($action == 'confirm_position' && $confirm != 'yes') {
    $action = '';
}
if ($action == 'confirm_position' && $confirm == 'yes') {
    $position = GETPOST('position', 'int');
    $res += dolibarr_set_const($db, 'LAB_START', $position, 'chaine', 0, '');

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
            Header('Location: ' . $_SERVER["PHP_SELF"] . '?id=' . $id . '&ref=' . $ref);
            exit;
        }
    } else {
        setEventMessage($object->error, "errors");
    }
}


// Add product to list
if ($action == 'delete') {
    $object = new LabelsProducts($db);
    $result = $object->delete($line);

    if ($result > 0) {
        setEventMessage($langs->trans("LineDeleted"));
    } else {
        setEventMessage($object->error, "errors");
    }
}

// Add product to list
if ($action == 'updateline') {
    if (GETPOST('save', 'alpha') != '') {
        $qty = GETPOST('qty', 'int');
        $price_level = GETPOST('price_level', 'int');

        $object = new LabelsProducts($db);
        $object->fetch($line);
        $object->qty = $qty;
        $object->price_level = $price_level;
        $result = $object->update();

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
    $object->fetch($line);
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
$helpurl = 'EN:Module_Labels|FR:Module_Labels_FR|ES:M&oacute;dulo_Labels';
llxHeader('', $langs->trans("Bill"), $helpurl);

if (!empty($url)) {
    print '<script language="javascript" type="text/javascript">
        
                window.open("' . $url . '" );
        
    </script>';
}

$html = new Form($db);
$form = new Form($db);
$formcompany = new FormCompany($db);
$contactstatic = new Contact($db);
$userstatic = new User($db);


/* *************************************************************************** */
/*                                                                             */
/* Mode vue et edition                                                         */
/*                                                                             */
/* *************************************************************************** */
if ($id > 0 || !empty($ref)) {
    $facture = new FactureFournisseur($db);
    if ($facture->fetch($id, $ref) > 0) {
        $facture->fetch_thirdparty();

        $head = facturefourn_prepare_head($facture);

        dol_fiche_head($head, 'labelprint', $langs->trans('SupplierInvoice'), 0, 'bill');

        $formconfirm = '';
        // Confirmation to delete invoice
        if ($action == 'truncate') {
            $text = $langs->trans('ConfirmTruncateList');
            $formconfirm = $html->formconfirm($_SERVER['PHP_SELF'] . '?id=' . $id . '&ref=' . $ref, $langs->trans('TruncateList'), $text, 'confirm_truncate', '', 0, 1);
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

			print $form->formconfirm($_SERVER['PHP_SELF'] . '?id=' . $id . '&ref=' . $ref . '&position=' . GETPOST('position'), $langs->trans('SelectLabelType'),
				$langs->trans('ConfirmSelectLabelType'), 'confirm_position', $formquestionmassinvoicing, 'yes', 1, 250,
				420);
		}
        print $formconfirm;

        /*
         *   Facture synthese pour rappel
         */
        print '<table class="border" width="100%">';

        // Reference du facture
        print '<tr><td width="20%">' . $langs->trans("Ref") . '</td><td colspan="3">';
        print $html->showrefnav($facture, 'ref', '', 1, 'ref');//,'id',$morehtmlref);
        print "</td></tr>";

        // Ref supplier
        print '<tr><td nowrap="nowrap">' . $langs->trans("RefSupplier") . '</td><td colspan="3">' . $facture->ref_supplier . '</td>';
        print "</tr>\n";

        // Third party
        print "<tr><td>" . $langs->trans("Supplier") . "</td>";
        print '<td colspan="3">' . $facture->thirdparty->getNomUrl(1, 'compta') . '</td></tr>';
        print "</table>";

        print '</div>';


        $formquestionposition = array(
            'text' => $langs->trans("ConfirmPosition"),
            array('type' => 'text', 'name' => 'position', 'label' => $langs->trans("HowManyPos"), 'value' => $conf->global->LAB_START, 'size' => 5)
        );

        /* ************************************************************************** */
        /*                                                                            */
        /* Barre d'action                                                             */
        /*                                                                            */
        /* ************************************************************************** */

        $sql = 'SELECT DISTINCT l.rowid id, l.qty, l.fk_user user_id, l.price_level,';
        $sql .= ' p.rowid, p.ref, p.label, p.barcode, p.price, p.price_ttc, p.price_base_type';
        $sql .= ' FROM ' . MAIN_DB_PREFIX . 'product as p';
        $sql .= ' JOIN ' . MAIN_DB_PREFIX . 'labelprint as l';
        $sql .= ' WHERE l.fk_object=p.rowid AND l.typLabel=0';

        $result = $db->query($sql);

        if ($result) {
            $num = $db->num_rows($result);
        }

        //if (empty($_GET["action"]) || $_GET["action"]=='delete')
        //{
        print "<div class=\"tabsAction\">";

        if ($user->rights->produit->creer || $user->rights->service->creer) {
            if ($num) print '<a class="butActionDelete" href="invoice_supplier.php?id=' . $id . '&amp;ref=' . $ref . '&amp;action=truncate">' . $langs->trans("Truncate") . '</a>';
            if ($facture->statut > 0) print '<a class="butAction" href="invoice_supplier.php?id=' . $id . '&amp;ref=' . $ref . '&amp;action=add">' . $langs->trans("AddToPrint") . '</a>';
            else print '<span class="butActionRefused" title="' . $langs->trans("InvoiceNotValidated") . '">' . $langs->trans('AddToPrint') . '</span>';

            if ($num) {
                if ($conf->global->LABELPRINT_A4) {
                    print '<span id="action-position" class="butAction">' . $langs->trans('PrintLabels') . '</span>' . "\n";
                    print $form->formconfirm($_SERVER["PHP_SELF"] . '?id=' . $id . '&ref=' . $ref, $langs->trans('SelectPosition'), $langs->trans('ConfirmSelectPosition'), 'confirm_position1', $formquestionposition, 'yes', 'action-position', 270, 400);
                } else {
                    print '<a class="butAction" href="' . $_SERVER['PHP_SELF'] . '?id=' . $id . '&amp;ref=' . $ref . '&amp;action=confirm_position1&amp;confirm=yes">' . $langs->trans('PrintLabels') . '</a>';
                }
            }
        }

        print "</div>";
        //}

        print '<br>';

        print '<table class="noborder" width="100%">';

        print '<tr class="liste_titre">';
        print '<td>' . $langs->trans("Ref") . '</td>';
        print '<td align="center">' . $langs->trans("Label") . '</td>';
        if ($conf->barcode->enabled) print '<td align="right">' . $langs->trans("BarCode") . '</td>';
        print '<td align="right">' . $langs->trans("SellingPrice") . '</td>';
        if (!empty($conf->global->PRODUIT_MULTIPRICES)) print '<td align="right">' . $langs->trans("PriceLevel") . '</td>';
        if ($conf->stock->enabled && $user->rights->stock->lire) print '<td align="right">' . $langs->trans("PhysicalStock") . '</td>';
        print '<td align="right">' . $langs->trans("QtyToPrint") . '</td>';
        print '<td align="right">' . $langs->trans("AddedBy") . '</td>';
        if ($user->rights->produit->creer && $action != 'editline') print '<td align="right">&nbsp;</td>';
        if ($user->rights->produit->creer && $action != 'editline') print '<td align="right">&nbsp;</td>';
        print '</tr>';

        if ($result) {
            $num = $db->num_rows($result);
            if ($num > 0) {

                $product_static = new Product($db);

                $var = True;
                $i = 0;
                while ($i < $num) {
                    $objp = $db->fetch_object($result);
                    $var = !$var;
                    print "<tr " . $bc[$var ? 1 : 0] . ">";

                    // Ref
                    print '<td nowrap="nowrap">';
                    $product_static->id = $objp->rowid;
                    $product_static->ref = $objp->ref;
                    $product_static->type = $objp->fk_product_type;
                    print $product_static->getNomUrl(1, '', 24);
                    print "</td>";

                    // Label
                    print '<td>' . dol_trunc($objp->label, 40) . '</td>';

                    // Barcode
                    if ($conf->barcode->enabled) {
                        if ($objp->barcode) {
                            print '<td align="right">' . $objp->barcode . '</td>';
                        } else if ($conf->global->PRODUIT_DEFAULT_BARCODE_TYPE == 2) {
                            print '<td align="right"><a href="' . $_SERVER["PHP_SELF"] . '?id=' . $id . '&prod_id=' . $objp->rowid . '&lineid=' . $objp->id . '&action=genbarcode">' . $langs->trans("GenerateBarcode") . '</a></td>';
                        } else {
                            print '<td align="right"></td>';
                        }
                    }

                    // Sell price
                    if (empty($conf->global->PRODUIT_MULTIPRICES)) {
                        print '<td align="right">';
                        if ($objp->price_base_type == 'TTC') print price($objp->price_ttc) . ' ' . $langs->trans("TTC");
                        else print price($objp->price) . ' ' . $langs->trans("HT");
                        print '</td>';
                    } else {
                        $product_static->fetch($objp->rowid);
                        print '<td align="right">';
                        if ($product_static->multiprices_base_type[$objp->price_level] == 'TTC') print price($product_static->multiprices_ttc[$objp->price_level]) . ' ' . $langs->trans("TTC");
                        else print price($product_static->multiprices[$objp->price_level]) . ' ' . $langs->trans("HT");
                        print '</td>';
                    }

                    // Price level
                    if (!empty($conf->global->PRODUIT_MULTIPRICES)) {
                        if ($action == 'editline' && $user->rights->produit->creer && $line == $objp->id) {
                            print '<form action="' . $_SERVER["PHP_SELF"] . '?id=' . $id . '&amp;ref=' . $ref . '" method="post">';

                            print '<td align="right">';

                            print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
                            print '<input type="hidden" name="action" value="updateline">';
                            print '<input type="hidden" name="id" value="' . $product->id . '">';
                            print '<input type="hidden" name="lineid" value="' . $line . '">';

                            print '<input class="flat" type="text" size="2" name="price_level" value="' . $objp->price_level . '"> ';
                            print '</td>';
                        } else {
                            print '<td align="right">';
                            print $objp->price_level;
                            print '</td>';
                        }
                    }

                    // Show stock
                    if ($conf->stock->enabled && $user->rights->stock->lire) {
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

                    // Qty
                    if ($action == 'editline' && $user->rights->produit->creer && $line == $objp->id) {
                        print '<td align="right">';

                        if (empty($conf->global->PRODUIT_MULTIPRICES)) {
                            print '<form action="' . $_SERVER["PHP_SELF"] . '?id=' . $id . '" method="post">';

                            print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
                            print '<input type="hidden" name="action" value="updateline">';
                            print '<input type="hidden" name="id" value="' . $product->id . '">';
                            print '<input type="hidden" name="lineid" value="' . $line . '">';

                            print '<input type="hidden" name="price_level" value="1"> ';

                        }

                        print '<input class="flat" type="text" size="2" name="qty" value="' . $objp->qty . '"> ';
                        print '<input type="submit" class="button" name="save" value="' . $langs->trans("Save") . '">';
                        print '<br><input type="submit" class="button" name="cancel" value="' . $langs->trans("Cancel") . '">';

                        print '</td>';
                        print '</form>';
                    } else
                        print '<td align="right">' . $objp->qty . '</td>';

                    // User
                    //print '<td align="right"><a href="'.DOL_URL_ROOT.'/user/fiche.php?id='.$objp->user_id.'">'.img_object($langs->trans("ShowUser"),'user').' '.$objp->login.'</a></td>';
                    //User
                    $userstatic = new User($db);
                    $userstatic->fetch($objp->user_id);
                    print '<td align="right">' . $userstatic->getNomUrl(1) . '</td>';


                    // Actions
                    if ($user->rights->produit->creer && $action != 'editline') {
                        print '<td align="right">';
                        print '<a href="' . $_SERVER["PHP_SELF"] . '?action=editline&amp;id=' . $id . '&amp;lineid=' . $objp->id . '">';
                        print img_edit();
                        print '</a>';
                        print '</td>';
                    }

                    if ($user->rights->produit->creer && $action != 'editline') {
                        print '<td align="right">';
                        print '<a href="' . $_SERVER["PHP_SELF"] . '?action=delete&amp;id=' . $id . '&amp;lineid=' . $objp->id . '">';
                        print img_delete();
                        print '</a>';
                        print '</td>';
                    }

                    print "</tr>";
                    $i++;
                }
                $db->free($result);
                print "</table>";
                print "<br>";
            }
        }
    }
}
llxFooter();
$db->close();
