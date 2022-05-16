<?php
// Heading

$_['heading_title'] = '<span style="color: #804B95;font-weight: bold;">google sheets import/export</span> (web-systems.solution)';

// Text
$_['text_success'] = 'Success: Налаштування успішно оновлено!';

$_['entry_language_export'] = 'Яку мову експортувати (після зміни зберегти)';

$_['entry_save_danger'] = 'Перед зміною будь-якого поля потрібно зберігати налаштування модуля';

$_['text_edit'] = 'Налаштування';
$_['text_tab_setting'] = 'Налаштування';
$_['text_tab_export'] = $_['entry_export'] = 'Експорт';
$_['text_tab_import'] = $_['entry_import'] = 'Імпорт';
$_['text_tab_instruction'] = 'Інструкція';


$_['instruction_step_1'] = 'Відкрийте <code>Google Cloud Console</code> та створіть новий проект';
$_['instruction_step_2'] = 'Виберіть цей проект';
$_['instruction_step_3'] = 'Натисніть <code>Library</code>, знайдіть <code>Sheets API</code>, увімкніть службу';
$_['instruction_step_4'] = 'Натисніть <code>Credentials</code> і натисніть <code>Create Credentials</code> виберіть <code>Service Account</code>, заповніть дані';
$_['instruction_step_5'] = 'Перейдіть в цей аккаунт та додайте новий ключ (json)';
$_['instruction_step_6'] = 'Збережіть ключ в модуль';
$_['instruction_step_7'] = 'Додайте Id Вашої Google таблиці. Збережіть модуль.';
$_['instruction_step_8'] = 'Перейдіть на вкладку <code>Details</code> Вашого <code>Service Account</code> та скопіюйте <code>Email</code>';
$_['instruction_step_8'] = 'Додайте цей емайл в список користувачів які можуть редагувати таблицю або відкрийте доступ для всіх';
$_['instruction_step_9'] = 'Збережіть модуль. Приємного користування)';

$_['entry_export_danger'] = 'Дані в таблиці будуть перезатерті';

$_['entry_language_import'] = 'Яку мову імпортувати (після зміни зберегти)';
$_['entry_import_danger'] = 'Перед імпортом переконайтеся, що поле для оновлення заповнено, інакше будуть створюватися нові товари';
$_['entry_update_field'] = 'Оновлювати товари по полю';

$_['entry_attribute_group_import'] = 'До якої групи додавати нові атрибути';


$_['text_login'] = 'Для роботи через api google sheets необхідно завантажити File Credentials (.json). Див. вкладка інструкція';
$_['text_count_step'] = 'Кількість експорту товарів за один крок';

$_['login_auth_text'] = 'Для роботи через api google sheets необхідно завантажити File Credentials (.json). Див. вкладка інструкція';


// Error
$_['error_version_php'] = 'Для роботи модуля потрібна версія PHP 5.6+';

$_['error_language_field'] = 'Чи не запалена колонка мови!';
$_['error_language_code'] = 'Не вірна мова імпорту!';
$_['error_field_update'] = 'Не знайдено стовпчика для оновлення! -';
$_['error_document_title'] = 'Не знайдено заголовка документа';

$_['error_sheets_connect'] = 'Чи не вірний File Credentials або Spreadsheet Id!';
$_['error_api_login'] = 'Чи не вірний File Credentials або Spreadsheet Id!';
$_['error_permission'] = 'У Вас немає прав на зміни модуля!';
