<?php
// Heading

$_['heading_title'] = '<span style="color: #804B95;font-weight: bold;">google sheets import/export</span> (web-systems.solution)';

// Text
$_['text_success'] = 'Success: Settings updated successfully!';

$_['entry_language_export'] = 'Which language to export (save after changing)';

$_['entry_save_danger'] = 'Before changing any field, you need to save the module settings';

$_['text_edit'] = 'Settings';
$_['text_tab_setting'] = 'Settings';
$_['text_tab_export'] = $_['entry_export'] = 'Export';
$_['text_tab_import'] = $_['entry_import'] = 'Import';
$_['text_tab_instruction'] = 'Instruction';


$_['instruction_step_1'] = 'Open the <code> Google Cloud Console </code> and create a new project';
$_['instruction_step_2'] = 'Select this project';
$_['instruction_step_3'] = 'Click on <code>Library</code>, search for <code>Sheets API</code>, enable the service:';
$_['instruction_step_4'] = 'Click <code>Credentials</code> and click <code>Create Credentials</code> select <code>Service Account</code>, fill in the details';
$_['instruction_step_5'] = 'Go to this account and add a new key (json)';
$_['instruction_step_6'] = 'Save the key to the module';
$_['instruction_step_7'] = 'Add your Google Spreadsheet Id. Save the module.';
$_['instruction_step_8'] = 'Click the <code> Details </code> tab of your <code> Service Account </code> and copy the <code> Email </code>';
$_['instruction_step_9'] = 'Add this email to the list of users who can edit the spreadsheet or share it with everyone';
$_['instruction_step_10'] = 'Save the module. Enjoy)';

$_['entry_export_danger'] = 'The data in the table will be overwritten';

$_['entry_language_import'] = 'Which language to import (save after change)';
$_['entry_import_danger'] = 'Before importing, make sure that the update field is filled, otherwise new products will be created';
$_['entry_update_field'] = 'Update products in the field';

$_['entry_attribute_group_import'] = 'In which group to add new attributes';


$_['text_login'] = 'To work through api google sheets you need upload File Credentials (.json). Go to tab Instruction';
$_['text_count_step'] = 'The number of exports of goods in one step';

$_['login_auth_text'] = 'Follow the link first, log in, get the code, enter it here, save, then save the module';


// Error
$_['error_version_php'] = 'PHP version 5.6+ is required for the module to work';

$_['error_language_field'] = 'The tongue column is not lit!';
$_['error_language_code'] = 'Not the correct language of import!';
$_['error_field_update'] = 'No update column found! -';
$_['error_document_title'] = 'No document title found';

$_['error_sheets_connect'] = 'Not a valid File Credentials or Spreadsheet Id!';
$_['error_api_login'] = 'Not a valid Clientid, Clientsecret or shet id!';
$_['error_permission'] = 'You do not have the right to change the module !!';
