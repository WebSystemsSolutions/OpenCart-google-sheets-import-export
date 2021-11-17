<?php
// Heading

$_['heading_title'] = '<span style="color: #804B95;font-weight: bold;">google sheets import/export</span> (web-systems.solution)';

// Text
$_['text_success'] = 'Success: Настройки успешно обновлены!';

$_['entry_language_export'] = 'Какой язык експортировать (после изменения сохранить)';

$_['entry_save_danger'] = 'Перед изменением любого поля нужно сохранять настройки модуля';

$_['text_edit'] = 'Настройки';
$_['text_tab_setting'] = 'Настройки';
$_['text_tab_export'] = $_['entry_export'] = 'Експорт';
$_['text_tab_import'] = $_['entry_import'] = 'Импорт';
$_['text_tab_instruction'] = 'Инструкция';


$_['instruction_step_1'] = 'Перейти на страницу апи на вкладку "Учетные данные"';
$_['instruction_step_2'] = 'Кликнуть на "+ Создать учетные данные" и выбрать - Идентификатор клиента OAuth ';
$_['instruction_step_3'] = 'Создать идентификатор клиента OAuth (выбрать Тип приложения - Другие типы )';
$_['instruction_step_4'] = 'Скопировать " Идентификатор клиента (ClientId)" и " Секретный код клиента (ClientSecret)" в настройки модуля';
$_['instruction_step_5'] = 'Скопировать в настройки модуля " sheet id " - это ИД Гугл таблицы';
$_['instruction_step_6'] = 'Сохранить модуль';
$_['instruction_step_7'] = 'Кликнуть на ссылку авторизации (Для работы через api google sheets нужно авторизоваться )';
$_['instruction_step_8'] = 'Ввести код в новом появившемся окне';
$_['instruction_step_9'] = 'Сохранить модуль';

$_['entry_export_danger'] = 'Данные в таблице будут перезатерты';

$_['entry_language_import'] = 'Какой язык импортировать (после изменения сохранить)';
$_['entry_import_danger'] = 'Перед импортом убедитесь что поле для обновления заполнено, иначе будут создаватся новые товары';
$_['entry_update_field'] = 'Обновлять товары по полю';

$_['entry_attribute_group_import'] = 'В какую группу добавлять новые атрибуты';


$_['text_login'] = 'Для работы через api google sheets нужно авторизоваться';
$_['text_count_step'] = 'Количество експорта товаров за один шаг';

$_['login_auth_text'] = 'Перейдите сначала по ссылке, авторизуйтесь, получите код, введите его сюда, сохраните, потом сохраните модуль';


// Error
$_['error_version_php'] = 'Для работы модуля нужна версия PHP 5.6+ ';

$_['error_language_field'] = 'Не заполена колонка языка!';
$_['error_language_code'] = 'Не верный язык импорта!';
$_['error_field_update'] = 'Не найдена колонка для обновления! - ';
$_['error_document_title'] = 'Не найден заголовок документа';

$_['error_sheets_connect'] = 'Не верный ClientId, ClientSecret или sheet id!';
$_['error_api_login'] = 'Не верный ClientId, ClientSecret или или sheet id!';
$_['error_permission'] = 'У Вас нет прав на изменения модуля!';
