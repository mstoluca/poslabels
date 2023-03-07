<?php
/* Copyright (C) 2012-2015      Juanjo Menent        <jmenent@2byte.es>
 * Copyright (C) 2013-2017      Ferran Marcet        <fmarcet@2byte.es>
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
 *	\file       htdocs/labelprint/admin/labelprint.php
 *	\ingroup    products
 *	\brief      labels module setup page
 */

$res = @include("../../main.inc.php");                    // For root directory
if (!$res) $res = @include("../../../main.inc.php");        // For "custom" directory

require_once(DOL_DOCUMENT_ROOT . "/core/lib/admin.lib.php");
dol_include_once("/labelprint/lib/labelprint.lib.php");

global $langs, $conf, $db, $user;

$langs->load("admin");
$langs->load("labelprint@labelprint");
$langs->load("companies");

$action = GETPOST('action', 'alpha');
$value = GETPOST('value', 'int');

if (!$user->admin) accessforbidden();

/*
 * Actions
 */
if (GETPOST("save")) {
    $db->begin();

    $res = 0;

    $res += dolibarr_set_const($db, 'LAB_CONTACT_COMP', trim(GETPOST("labComp")), 'chaine', 0, '', $conf->entity);
    $res += dolibarr_set_const($db, 'LAB_CONTACT_ADDRESS', trim(GETPOST("labAddress")), 'chaine', 0, '', $conf->entity);
	$res += dolibarr_set_const($db, 'LAB_CONTACT_THIRD', trim(GETPOST("labThird")), 'chaine', 0, '', $conf->entity);
    $res += dolibarr_set_const($db, 'LAB_CONTACT_FREE_TEXT', trim(GETPOST("labFreeText")), 'chaine', 0, '', $conf->entity);
    $res += dolibarr_set_const($db, 'LAB_START', 0, 'chaine', 0, '', $conf->entity);
    $res += dolibarr_set_const($db, 'LABELPRINT_CONTACT_A4', GETPOST("labA4"), 'chaine', 0, '', $conf->entity);

    if ($res >= 5) {
        $db->commit();
        setEventMessage($langs->trans("LabSetupSaved"));
    } else {
        $db->rollback();
        setEventMessageS($langs->trans("Error"), array(), "errors");
        header("Location: " . $_SERVER["PHP_SELF"]);
        exit;
    }
}


//Activate Labels
if ($action == 'setlabel') {
    $status = GETPOST('status', 'int');

    $labelid = "MAIN_MODULE_LABELPRINT_CONTACT_" . $value;

    if ($status == 1) {

        if (dolibarr_set_const($db, $labelid, 1, 'chaine', 0, '', $conf->entity) > 0) {
            Header("Location: " . $_SERVER["PHP_SELF"]);
            exit;
        } else {
            dol_print_error($db);
        }
    } else {
        if (dolibarr_del_const($db, $labelid)) {
            Header("Location: " . $_SERVER["PHP_SELF"]);
            exit;
        } else {
            dol_print_error($db);
        }
    }

}


//Type of Labels
if ($action == 'settypelabel') {
    $status = GETPOST('status', 'int');

    $labelid = "MAIN_MODULE_LABELPRINT_LABELS_CONTACT_" . $value;

        switch ($value) {
            case (0):
                dolibarr_del_const($db, "MAIN_MODULE_LABELPRINT_LABELS_CONTACT_1");
                dolibarr_del_const($db, "MAIN_MODULE_LABELPRINT_LABELS_CONTACT_2");
                /*dolibarr_del_const($db, "MAIN_MODULE_LABELPRINT_LABELS_THIRD_3");
                dolibarr_del_const($db, "MAIN_MODULE_LABELPRINT_LABELS_THIRD_4");
                dolibarr_del_const($db, "MAIN_MODULE_LABELPRINT_LABELS_THIRD_5");*/
                break;
            case (1):
                dolibarr_del_const($db, "MAIN_MODULE_LABELPRINT_LABELS_CONTACT_0");
                dolibarr_del_const($db, "MAIN_MODULE_LABELPRINT_LABELS_CONTACT_2");
                /*dolibarr_del_const($db, "MAIN_MODULE_LABELPRINT_LABELS_THIRD_3");
                dolibarr_del_const($db, "MAIN_MODULE_LABELPRINT_LABELS_THIRD_4");
                dolibarr_del_const($db, "MAIN_MODULE_LABELPRINT_LABELS_THIRD_5");*/
                break;
            case (2):
                dolibarr_del_const($db, "MAIN_MODULE_LABELPRINT_LABELS_CONTACT_0");
                dolibarr_del_const($db, "MAIN_MODULE_LABELPRINT_LABELS_CONTACT_1");
                /*dolibarr_del_const($db, "MAIN_MODULE_LABELPRINT_LABELS_THIRD_3");
                dolibarr_del_const($db, "MAIN_MODULE_LABELPRINT_LABELS_THIRD_4");
                dolibarr_del_const($db, "MAIN_MODULE_LABELPRINT_LABELS_THIRD_5");*/
                break;
            /* case (3):
                 dolibarr_del_const($db, "MAIN_MODULE_LABELPRINT_LABELS_THIRD_0");
                 dolibarr_del_const($db, "MAIN_MODULE_LABELPRINT_LABELS_THIRD_1");
                 dolibarr_del_const($db, "MAIN_MODULE_LABELPRINT_LABELS_THIRD_2");
                 dolibarr_del_const($db, "MAIN_MODULE_LABELPRINT_LABELS_THIRD_4");
                 dolibarr_del_const($db, "MAIN_MODULE_LABELPRINT_LABELS_THIRD_5");
                 break;
             case (4):
                 dolibarr_del_const($db, "MAIN_MODULE_LABELPRINT_LABELS_THIRD_0");
                 dolibarr_del_const($db, "MAIN_MODULE_LABELPRINT_LABELS_THIRD_1");
                 dolibarr_del_const($db, "MAIN_MODULE_LABELPRINT_LABELS_THIRD_2");
                 dolibarr_del_const($db, "MAIN_MODULE_LABELPRINT_LABELS_THIRD_3");
                 dolibarr_del_const($db, "MAIN_MODULE_LABELPRINT_LABELS_THIRD_5");
                 break;
             case (5):
                 dolibarr_del_const($db, "MAIN_MODULE_LABELPRINT_LABELS_THIRD_0");
                 dolibarr_del_const($db, "MAIN_MODULE_LABELPRINT_LABELS_THIRD_1");
                 dolibarr_del_const($db, "MAIN_MODULE_LABELPRINT_LABELS_THIRD_2");
                 dolibarr_del_const($db, "MAIN_MODULE_LABELPRINT_LABELS_THIRD_3");
                 dolibarr_del_const($db, "MAIN_MODULE_LABELPRINT_LABELS_THIRD_4");
                 break;*/


        }

        if (dolibarr_set_const($db, $labelid, 1, 'chaine', 0, '', $conf->entity) > 0) {
            Header("Location: " . $_SERVER["PHP_SELF"]);
            exit;
        } else {
            dol_print_error($db);
        }
}


/*
 * 	View
 */

clearstatcache();

// read const
$labcomp = dolibarr_get_const($db, "LAB_CONTACT_COMP", $conf->entity);
$labaddress = dolibarr_get_const($db, "LAB_CONTACT_ADDRESS", $conf->entity);
$labthird = dolibarr_get_const($db, "LAB_CONTACT_THIRD", $conf->entity);


$form = new Form($db);

$helpurl = 'EN:Module_Labels|FR:Module_Labels_FR|ES:M&oacute;dulo_Labels';
llxHeader('', $langs->trans("LabelPrintSetup"), $helpurl);

$linkback = '<a href="' . DOL_URL_ROOT . '/admin/modules.php">' . $langs->trans("BackToModuleList") . '</a>';
print load_fiche_titre($langs->trans("LabelPrintSetup"), $linkback, 'title_setup');


$head = labelprintadmin_prepare_head();

dol_fiche_head($head, 'configcontacts', $langs->trans("Labels"), 0, 'barcode');

dol_htmloutput_events();

//Show in
print load_fiche_titre($langs->trans("ShowLabelsIn"));

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>' . $langs->trans("Name") . '</td>';
print '<td>' . $langs->trans("Description") . '</td>';
print '<td align="center">' . $langs->trans("Use") . '</td>';
print "</tr>\n";

$profid[0][0] = $langs->trans("PropalCustomer");
$profid[0][1] = $langs->trans('PropalCustomerDesc');
$profid[1][0] = $langs->trans("OrderCustomer");
$profid[1][1] = $langs->trans('OrderCustomerDesc');
$profid[2][0] = $langs->trans("InvoiceCustomer");
$profid[2][1] = $langs->trans('InvoiceCustomerDesc');
$profid[3][0] = $langs->trans("Contacts");
$profid[3][1] = $langs->trans('MenuContactsCardDesc');
$profid[4][0] = $langs->trans("MenuContacts");
$profid[4][1] = $langs->trans('MenuContactsDesc');

$var = true;
$i = 0;

$nbofloop = count($profid);
while ($i < $nbofloop) {
    $var = !$var;

    print '<tr ' . $bc[$var] . '>';
    print '<td>' . $profid[$i][0] . "</td><td>\n";
    print $profid[$i][1];
    print '</td>';

    switch ($i) {
        case 0:
            $verif = (!$conf->global->MAIN_MODULE_LABELPRINT_CONTACT_0 ? false : true);
            break;
        case 1:
            $verif = (!$conf->global->MAIN_MODULE_LABELPRINT_CONTACT_1 ? false : true);
            break;
        case 2:
            $verif = (!$conf->global->MAIN_MODULE_LABELPRINT_CONTACT_2 ? false : true);
            break;
        case 3:
            $verif = (!$conf->global->MAIN_MODULE_LABELPRINT_CONTACT_3 ? false : true);
            break;
    }

    print '<td align="center">';
    if (!empty($conf->use_javascript_ajax)) {
        print ajax_constantonoff('MAIN_MODULE_LABELPRINT_CONTACT_' . $i);
    } else {

        if ($verif) {
            print '<a href="' . $_SERVER['PHP_SELF'] . '?action=setlabel&amp;value=' . ($i) . '&amp;status=0">';
            print img_picto($langs->trans("Activated"), 'switch_on');
            print '</a>';
        } else {
            print '<a href="' . $_SERVER['PHP_SELF'] . '?action=setlabel&amp;value=' . ($i) . '&amp;status=1">';
            print img_picto($langs->trans("Disabled"), 'switch_off');
            print '</a>';
        }
    }
    print "</td></tr>\n";
    $i++;

}

print "</table><br>\n";
$html = new Form($db);

//Show in
print load_fiche_titre($langs->trans("TypeLabels"));

print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>' . $langs->trans("Name") . '</td>';
print '<td>' . $langs->trans("Description") . '</td>';
print '<td align="center">' . $langs->trans("Use") . '</td>';
print "</tr>\n";

$labels[0][0] = $langs->trans("70x36");
$labels[0][1] = $langs->trans('7036Desc');
$labels[1][0] = $langs->trans("70x37");
$labels[1][1] = $langs->trans('7037Desc');
/*$labels[2][0]=$langs->trans("38x21");
$labels[2][1]=$langs->trans('3821Desc');
$labels[3][0]=$langs->trans("48x25");
$labels[3][1]=$langs->trans('4825DescAvery');
$labels[4][0]=$langs->trans("48x25");
$labels[4][1]=$langs->trans('4825DescApli');*/
$labels[2][0] = $langs->trans("105x37");
$labels[2][1] = $langs->trans('10537Desc');

$var = true;
$i = 0;

$nbofloop = count($labels);
while ($i < $nbofloop) {
    $var = !$var;

    print '<tr ' . $bc[$var] . '>';
    print '<td>' . $labels[$i][0] . "</td><td>\n";
    print $labels[$i][1];
    print '</td>';

    switch ($i) {
        case 0:
            $verif = (!$conf->global->MAIN_MODULE_LABELPRINT_LABELS_CONTACT_0 ? false : true);
            break;
        case 1:
            $verif = (!$conf->global->MAIN_MODULE_LABELPRINT_LABELS_CONTACT_1 ? false : true);
            break;
        case 2:
            $verif = (!$conf->global->MAIN_MODULE_LABELPRINT_LABELS_CONTACT_2 ? false : true);
            break;
        /*case 3:
            $verif=(!$conf->global->MAIN_MODULE_LABELPRINT_LABELS_THIRD_3?false:true);
            break;
        case 4:
            $verif=(!$conf->global->MAIN_MODULE_LABELPRINT_LABELS_THIRD_4?false:true);
            break;
        case 5:
            $verif=(!$conf->global->MAIN_MODULE_LABELPRINT_LABELS_THIRD_5?false:true);
            break;*/
    }

    if ($verif) {
        print '<td align="center"><a href="' . $_SERVER['PHP_SELF'] . '?action=settypelabel&amp;value=' . ($i) . '&amp;status=0">';
        print img_picto($langs->trans("Activated"), 'switch_on');
        print '</a></td>';
    } else {
        print '<td align="center"><a href="' . $_SERVER['PHP_SELF'] . '?action=settypelabel&amp;value=' . ($i) . '&amp;status=1">';
        print img_picto($langs->trans("Disabled"), 'switch_off');
        print '</a></td>';
    }
    print "</tr>\n";
    $i++;
}
print "</table><br>\n";

print '<form name="catalogconfig" action="' . $_SERVER["PHP_SELF"] . '" method="post">';
print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';

$var = true;
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>' . $langs->trans("Parameter") . '</td>';
print '<td align="center" width="60">' . $langs->trans("Value") . '</td>';
print "</tr>\n";
print "<tr " . $bc[$var] . ">";
print '<td>' . $langs->trans("LabelsA4") . '</td>';
print '<td align="center" width="60">' . $html->selectyesno("labA4", $conf->global->LABELPRINT_CONTACT_A4, 1) . '</td>';
print '</tr></table>';
/*
 *  General Optiones
*/
print load_fiche_titre($langs->trans("ShowOptions"));
print '<table class="noborder" width="100%">';
print '<tr class="liste_titre">';
print '<td>' . $langs->trans("Parameter") . " " . $langs->trans("max2") . '</td>';
print '<td align="center" width="60">' . $langs->trans("Value") . '</td>';
print "</tr>\n";
$var = true;

// Show Company Name
$var = !$var;
print "<tr " . $bc[$var] . ">";
print "<td>" . $langs->trans("ShowContactName") . "</td>";
print '<td>';
print $html->selectyesno("labComp", $labcomp, 1);
print '</td>';
print "</tr>";

/*
// Show contact name
$var=!$var;
print "<tr ".$bc[$var].">";
print "<td>".$langs->trans("ShowContactName")."</td>";
print '<td>';
print $html->selectyesno("labContact",$labcontact,1);
print '</td>';
print "</tr>";
*/
// Show address
$var = !$var;
print "<tr " . $bc[$var] . ">";
print "<td>" . $langs->trans("ShowAdress") . "</td>";
print '<td>';
print $html->selectyesno("labAddress", $labaddress, 1);
print '</td>';
print "</tr>";

// Show third
$var = !$var;
print "<tr " . $bc[$var] . ">";
print "<td>" . $langs->trans("ShowThird") . "</td>";
print '<td>';
print $html->selectyesno("labThird", $labthird, 1);
print '</td>';
print "</tr>";

$var = !$var;
print '<tr ' . $bc[$var] . '><td colspan=2>';
print $langs->trans("FreeText") . '<br>';
print '<textarea name="labFreeText" class="flat" cols="120">' . $conf->global->LAB_CONTACT_FREE_TEXT . '</textarea>';
print '</td></tr>';

print '</table>';
dol_fiche_end();
print '<br><div style="text-align: center">';
print '<input type="submit" name="save" class="button" value="' . $langs->trans("Save") . '">';
print "</div>";
print "</form>\n";


$db->close();

llxFooter();
