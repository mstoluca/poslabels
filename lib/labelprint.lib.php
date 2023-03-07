<?php
/* Copyright (C) 2012      Juanjo Menent        <jmenent@2byte.es>
 * Copyright (C) 2014-2017 Ferran Marcet        <fmarcet@2byte.es>
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

function labelprintadmin_prepare_head()
{
    global $langs;
    $langs->load("labelprint@labelprint");

    $h = 0;
    $head = array();

    $head[$h][0] = dol_buildpath('/labelprint/admin/labelprint.php', 1);
    $head[$h][1] = $langs->trans("LabelPrintSetupProds");
    $head[$h][2] = 'configprods';
    $h++;

    $head[$h][0] = dol_buildpath('/labelprint/admin/labelprintthirds.php', 1);
    $head[$h][1] = $langs->trans("LabelPrintSetupThirds");
    $head[$h][2] = 'configthirds';
    $h++;

    $head[$h][0] = dol_buildpath('/labelprint/admin/labelprintcontacts.php', 1);
    $head[$h][1] = $langs->trans("LabelPrintSetupContacts");
    $head[$h][2] = 'configcontacts';
    $h++;

    return $head;
}

function select_price_level($level){
	global $conf;

	$select = '<select name="pricelevel">';
	for($i=1; $i<=$conf->global->PRODUIT_MULTIPRICES_LIMIT; $i++){
		if($level){
			if($level==$i){
				$select .= '<option value="' . $i . '" selected>' . $i . '</option>';
			}
			else {
				$select .= '<option value="' . $i . '">' . $i . '</option>';
			}
		}
		else{
			if($conf->global->LABELPRINT_LEVEL_PRICE==$i){
				$select .= '<option value="' . $i . '" selected>' . $i . '</option>';
			}
			else {
				$select .= '<option value="' . $i . '">' . $i . '</option>';
			}
		}
	}
	$select .= '</select>';
	return $select;
}