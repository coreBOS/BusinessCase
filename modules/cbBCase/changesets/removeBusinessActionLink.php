<?php
/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
* Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
* file except in compliance with the License. You can redistribute it and/or modify it
* under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
* granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
* the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
* warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
* applicable law or agreed to in writing, software distributed under the License is
* distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
* either express or implied. See the License for the specific language governing
* permissions and limitations under the License. You may obtain a copy of the License
* at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
*************************************************************************************************/
include_once 'modules/BusinessActions/BusinessActions.php';

class removeBusinessActionLink extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$mods = array('Accounts', 'Contacts', 'Leads', 'Users', 'Vendors', 'Potentials', 'Quotes', 'SalesOrder', 'Invoice', 'PurchaseOrder');
			foreach ($mods as $mod) {
			$modInstance = Vtiger_Module::getInstance($mod);
			if ($mod=='Accounts') {
				$relfield = 'accid=$RECORD$';
			} elseif ($mod=='Contacts') {
				$relfield = 'ctoid=$RECORD$';
			} else {
				$relfield = 'accid=$related_to&ctoid=$related_to';
			}
				 $modInstance->deleteLink(
					'DETAILVIEWBASIC',
					'Create Business Case',
					'index.php?module=cbBCase&action=EditView&return_module=cbBCase&return_action=DetailView&return_id=$RECORD$&'.$relfield.'&RLparent_id=$RECORD$&createmode=link',
					'{"library":"standard", "icon":"case_transcript"}',
					'1'
				);
			}
			$moduleInstance = Vtiger_Module::getInstance('cbBCase');
			$moduleInstance->deleteLink('HEADERSCRIPT', 'MailJS', 'include/js/Mail.js', '', 1, null, true);
			$moduleInstance->deleteLink('DETAILVIEWWIDGET', 'QuickRelatedList', 'module=Utilities&action=UtilitiesAjax&file=QuickRelatedList&formodule=$MODULE$&forrecord=$RECORD$');
			$moduleInstance->deleteLink('DETAILVIEWWIDGET', 'DetailViewBlockCommentWidget', 'block://ModComments:modules/ModComments/ModComments.php');
			$action = array(
				'menutype' => 'item',
				'title' => 'Recalculate',
				'href' => 'javascript:cbbcrecalculate($RECORD$);',
				'icon' => '{"library":"utility", "icon":"formula"}',
			);
			BusinessActions::deleteLink($moduleInstance->id, 'DETAILVIEWBASIC', $action['title'], $action['href'], $action['icon'], 0, null, true, 0);
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}

	public function undoChange() {
		if ($this->isBlocked()) {
			return true;
		}
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			
		} else {
			$this->sendMsg('Changeset '.get_class($this).' not applied!');
		}
		$this->finishExecution();
	}
}

