<?php
//    MyDMS. Document Management System
//    Copyright (C) 2010 Matteo Lucarelli
//
//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or
//    (at your option) any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.

include("../inc/inc.Settings.php");
include("../inc/inc.Utils.php");
include("../inc/inc.Language.php");
include("../inc/inc.Init.php");
include("../inc/inc.Extension.php");
include("../inc/inc.DBInit.php");
include("../inc/inc.ClassUI.php");
include("../inc/inc.ClassAccessOperation.php");
include("../inc/inc.Authentication.php");

if (!$user->isAdmin()) {
    UI::exitError(getMLText("admin_tools"),getMLText("access_denied"));
}

if (!isset($_GET["documentid"]) || !is_numeric($_GET["documentid"]) || intval($_GET["documentid"])<1) {
	UI::exitError(getMLText("document_title", array("documentname" => getMLText("invalid_doc_id"))),getMLText("invalid_doc_id"));
}

$document = $dms->getDocument($_GET["documentid"]);

if (!is_object($document)) {
	UI::exitError(getMLText("document_title", array("documentname" => getMLText("invalid_doc_id"))),getMLText("invalid_doc_id"));
}

if (!isset($_GET["fileid"]) || !is_numeric($_GET["fileid"]) || intval($_GET["fileid"])<1) {
	UI::exitError(getMLText("document_title", array("documentname" => htmlspecialchars($document->getName()))),getMLText("invalid_file_id"));
}

$file = $document->getDocumentFile($_GET["fileid"]);

if (!is_object($file)) {
	UI::exitError(getMLText("document_title", array("documentname" => htmlspecialchars($document->getName()))),getMLText("invalid_file_id"));
}

if (($document->getAccessMode($user) < M_ALL)&&($user->getID()!=$file->getUserID())) {
	UI::exitError(getMLText("document_title", array("documentname" => htmlspecialchars($document->getName()))),getMLText("access_denied"));
}

$folder = $document->getFolder();

/* Create object for checking access to certain operations */
$accessop = new SeedDMS_AccessOperation($dms, $document, $user, $settings);

$tmp = explode('.', basename($_SERVER['SCRIPT_FILENAME']));
$view = UI::factory($theme, $tmp[1], array('dms'=>$dms, 'user'=>$user, 'folder'=>$folder, 'document'=>$document, 'file'=>$file));
if($view) {
	$view->setParam('accessobject', $accessop);
	$view->show();
	exit;
}

?>
