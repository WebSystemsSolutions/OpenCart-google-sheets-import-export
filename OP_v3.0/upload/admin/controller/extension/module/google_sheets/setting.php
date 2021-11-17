<?php

class ControllerExtensionModuleGoogleSheetsSetting extends Controller
{
    public $settingKey = 'google_sheets_setting';
    private $error;

    public function index()
    {

        $this->load->language('extension/module/google_sheets/setting');
        $this->load->model('setting/setting');

        if (version_compare(phpversion(), '5.6', '<') == true) {
            $this->error['warning'] = $this->language->get('error_version_php');
        }

        $data['user_token'] = $this->session->data['user_token'];

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting($this->settingKey, $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/module/google_sheets/setting', 'user_token=' . $this->session->data['user_token'], true));
        }

        $this->document->setTitle($this->language->get('heading_title'));

        $this->loadTabSetting($data);

        $this->loadTabExport($data);

        $this->loadTabImport($data);

        $this->loadModuleInfoData($data);

        $this->response->setOutput($this->load->view('extension/module/google_sheets/setting', $data));

    }

    private function loadTabExport(&$data)
    {
        $this->load->model('localisation/language');

        $data['entry_language_export'] = $this->language->get('entry_language_export');
        $data['entry_export'] = $this->language->get('entry_export');
        $data['entry_export_danger'] = $this->language->get('entry_export_danger');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        $data['language_export'] = $this->config->get($this->settingKey . '_language_export');
        $data['language_export_field_name'] = $this->settingKey . '_language_export';

        $data['tab_export'] = $this->load->view('extension/module/google_sheets/tab_export', $data);

       // $data['tab_export'] = $this->load->view('extension/module/google_sheets/tab_export', $data);

    }

    private function loadTabImport(&$data)
    {
        $this->load->model('localisation/language');

        $data['entry_language_import'] = $this->language->get('entry_language_import');
        $data['entry_import'] = $this->language->get('entry_import');
        $data['entry_import_danger'] = $this->language->get('entry_import_danger');
        $data['entry_update_field'] = $this->language->get('entry_update_field');
        $data['entry_attribute_group_import'] = $this->language->get('entry_attribute_group_import');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        $data['language_import'] = $this->config->get($this->settingKey . '_language_import');
        $data['language_import_field_name'] = $this->settingKey . '_language_import';

        $data['import_update_field'] = $this->config->get($this->settingKey . '_import_update_field');
        $data['import_update_field_name'] = $this->settingKey . '_import_update_field';


        $this->load->model('catalog/attribute_group');
        $data['attribute_groups'] = $this->model_catalog_attribute_group->getAttributeGroups();

        $data['import_attribute_group_id'] = $this->config->get($this->settingKey . '_import_attribute_group_id');
        $data['import_attribute_group_id_name'] = $this->settingKey . '_import_attribute_group_id';

        $data['fields_to_update'] = [
            '_ID_',
            '_MODEL_',
            '_SKU_',
        ];

        $data['tab_import'] = $this->load->view('extension/module/google_sheets/tab_import', $data);
    }

    private function loadTabSetting(&$data)
    {
        require_once __DIR__ . '/client.php';
        $sheetsClient = new ControllerExtensionModuleGoogleSheetsClient($this->registry);

        $data['login_url'] = false;

        try {
            if (!$sheetsClient->getClient()) {
                $data['login_url'] = $this->url->link('extension/module/google_sheets/client/getClientLogin', 'user_token=' . $this->session->data['user_token'], true);

                $this->error['warning'] = $this->language->get('error_api_login');
            }

            $sheetsClient->getSheets();

        } catch (\Exception $e) {
            $this->error['warning'] = $this->language->get('error_sheets_connect');
        }

        $data['fields'][$this->settingKey . '_ClientId'] = [
            'label' => 'ClientId',
            'type'  => 'input',
            'value' => $this->config->get($this->settingKey . '_ClientId'),
        ];

        $data['fields'][$this->settingKey . '_ClientSecret'] = [
            'label' => 'ClientSecret',
            'type'  => 'input',
            'value' => $this->config->get($this->settingKey . '_ClientSecret'),
        ];

        $data['fields'][$this->settingKey . '_sheet_id'] = [
            'label' => 'sheet id',
            'type'  => 'input',
            'value' => $this->config->get($this->settingKey . '_sheet_id'),
        ];


        $data['fields'][$this->settingKey . '_count_step'] = [
            'label' => $this->language->get('text_count_step'),
            'type'  => 'input',
            'value' => ($this->config->get($this->settingKey . '_count_step')) ? $this->config->get($this->settingKey . '_count_step') : 10,
        ];

        $data['parser_fields'] = $this->loadFields();



        $data['tab_setting'] = $this->load->view('extension/module/google_sheets/tab_setting', $data);
    }

    public function loadFields()
    {

        $parser_fields = $this->config->get($this->settingKey . '_parser_fields');

        $fields[] = [
            'name'          => $this->settingKey . '_parser_fields[product_id]',
            'name_column'   => '_ID_',
            'label'         => 'ID товара',
            'disabled'      => true,
            'checked'       => true,
            'table'         => 'product',
            'name_in_table' => 'product_id',
            'type_field'    => 'int',
        ];

        $fields[] = [
            'name'        => $this->settingKey . '_parser_fields[lang]',
            'name_column' => '_LANG_',
            'label'       => 'Язык',
            'disabled'    => true,
            'checked'     => true,
            'table'       => 'lang',
        ];

        $fields[] = [
            'name'        => $this->settingKey . '_parser_fields[categories]',
            'name_column' => '_CATEGORIES_',
            'label'       => 'Категории',
            'disabled'    => true,
            'checked'     => true,
            'table'       => 'categories',
        ];

        $fields[] = [
            'name'          => $this->settingKey . '_parser_fields[name]',
            'name_column'   => '_NAME_',
            'label'         => 'Наименование',
            'disabled'      => true,
            'checked'       => true,
            'table'         => 'description',
            'name_in_table' => 'name',
        ];

        $fields[] = [
            'name'          => $this->settingKey . '_parser_fields[model]',
            'name_column'   => '_MODEL_',
            'label'         => 'Модель',
            'disabled'      => false,
            'checked'       => isset($parser_fields['model']),
            'table'         => 'product',
            'name_in_table' => 'model',
        ];

        $fields[] = [
            'name'          => $this->settingKey . '_parser_fields[sku]',
            'name_column'   => '_SKU_',
            'label'         => 'SKU',
            'disabled'      => false,
            'checked'       => isset($parser_fields['sku']),
            'table'         => 'product',
            'name_in_table' => 'sku',
        ];

        $fields[] = [
            'name'          => $this->settingKey . '_parser_fields[ean]',
            'name_column'   => '_EAN_',
            'label'         => 'Европейский артикул',
            'disabled'      => false,
            'checked'       => isset($parser_fields['ean']),
            'table'         => 'product',
            'name_in_table' => 'ean',
        ];

        $fields[] = [
            'name'          => $this->settingKey . '_parser_fields[jan]',
            'name_column'   => '_JAN_',
            'label'         => 'Японский артикул ',
            'disabled'      => false,
            'checked'       => isset($parser_fields['jan']),
            'table'         => 'product',
            'name_in_table' => 'jan',
        ];

        $fields[] = [
            'name'          => $this->settingKey . '_parser_fields[isbn]',
            'name_column'   => '_ISBN_',
            'label'         => 'Международный стандарт номера книги',
            'disabled'      => false,
            'checked'       => isset($parser_fields['isbn']),
            'table'         => 'product',
            'name_in_table' => 'isbn',
        ];

        $fields[] = [
            'name'          => $this->settingKey . '_parser_fields[mpn]',
            'name_column'   => '_MPN_',
            'label'         => 'Номер производителя',
            'disabled'      => false,
            'checked'       => isset($parser_fields['mpn']),
            'table'         => 'product',
            'name_in_table' => 'mpn',
        ];

        $fields[] = [
            'name'          => $this->settingKey . '_parser_fields[upc]',
            'name_column'   => '_UPC_',
            'label'         => 'UPC',
            'disabled'      => false,
            'checked'       => isset($parser_fields['upc']),
            'table'         => 'product',
            'name_in_table' => 'upc',
        ];

        $fields[] = [
            'name'        => $this->settingKey . '_parser_fields[manufacturer]',
            'name_column' => '_MANUFACTURER_',
            'label'       => 'Производитель',
            'disabled'    => false,
            'checked'     => isset($parser_fields['manufacturer']),
            'table'       => 'manufacturer',
        ];

        $fields[] = [
            'name'          => $this->settingKey . '_parser_fields[location]',
            'name_column'   => '_LOCATION_',
            'label'         => 'Расположение',
            'disabled'      => false,
            'checked'       => isset($parser_fields['location']),
            'table'         => 'product',
            'name_in_table' => 'location',
        ];

        $fields[] = [
            'name'          => $this->settingKey . '_parser_fields[price]',
            'name_column'   => '_PRICE_',
            'label'         => 'Цена',
            'disabled'      => false,
            'checked'       => isset($parser_fields['price']),
            'table'         => 'product',
            'name_in_table' => 'price',
            'type_field'    => 'double',
        ];

        $fields[] = [
            'name'          => $this->settingKey . '_parser_fields[quantity]',
            'name_column'   => '_QUANTITY_',
            'label'         => 'Количество',
            'disabled'      => false,
            'checked'       => isset($parser_fields['quantity']),
            'table'         => 'product',
            'name_in_table' => 'quantity',
            'type_field'    => 'int',
        ];

        /*$fields[] = [
            'name'          => $this->settingKey . '_parser_fields[meta_h1]',
            'name_column'   => '_META_H1_',
            'label'         => 'HTML-тег H1',
            'disabled'      => false,
            'checked'       => isset($parser_fields['meta_h1']),
            'table'         => 'description',
            'name_in_table' => 'meta_h1',
        ];*/

        $fields[] = [
            'name'          => $this->settingKey . '_parser_fields[meta_title]',
            'name_column'   => '_META_TITLE_',
            'label'         => 'Meta-тег Title',
            'disabled'      => false,
            'checked'       => isset($parser_fields['meta_title']),
            'table'         => 'description',
            'name_in_table' => 'meta_title',
        ];

        $fields[] = [
            'name'          => $this->settingKey . '_parser_fields[meta_keywords]',
            'name_column'   => '_META_KEYWORDS_',
            'label'         => 'Мета-тег Keywords',
            'disabled'      => false,
            'checked'       => isset($parser_fields['meta_keywords']),
            'table'         => 'description',
            'name_in_table' => 'meta_keyword',
        ];

        $fields[] = [
            'name'          => $this->settingKey . '_parser_fields[meta_description]',
            'name_column'   => '_META_DESCRIPTION_',
            'label'         => 'Мета-тег Description',
            'disabled'      => false,
            'checked'       => isset($parser_fields['meta_description']),
            'table'         => 'description',
            'name_in_table' => 'meta_description',
        ];

        $fields[] = [
            'name'          => $this->settingKey . '_parser_fields[description]',
            'name_column'   => '_DESCRIPTION_',
            'label'         => 'Текст с описанием',
            'disabled'      => false,
            'checked'       => isset($parser_fields['description']),
            'table'         => 'description',
            'name_in_table' => 'description',
        ];

      /*  $fields[] = [
            'name'          => $this->settingKey . '_parser_fields[image]',
            'name_column'   => '_IMAGE_',
            'label'         => 'Изображение',
            'disabled'      => false,
            'checked'       => isset($parser_fields['image']),
            'table'         => 'product',
            'name_in_table' => 'image',
        ];*/

        $fields[] = [
            'name'        => $this->settingKey . '_parser_fields[images]',
            'name_column' => '_IMAGES_',
            'label'       => 'Доп. изображения',
            'disabled'    => false,
            'checked'     => isset($parser_fields['images']),
            'table'       => 'images',
        ];

        $fields[] = [
            'name'          => $this->settingKey . '_parser_fields[status]',
            'name_column'   => '_STATUS_',
            'label'         => 'Статус',
            'disabled'      => false,
            'checked'       => isset($parser_fields['status']),
            'table'         => 'product',
            'name_in_table' => 'status',
            'type_field'    => 'int',
        ];

        $fields[] = [
            'name'        => $this->settingKey . '_parser_fields[attributes]',
            'name_column' => '_ATTRIBUTES_',
            'label'       => 'Атрибуты',
            'disabled'    => false,
            'checked'     => isset($parser_fields['attributes']),
            'table'       => 'attributes',
        ];

        $fields[] = [
            'name'        => $this->settingKey . '_parser_fields[discount]',
            'name_column' => '_DISCOUNT_',
            'label'       => 'Скидки',
            'disabled'    => false,
            'checked'     => isset($parser_fields['discount']),
            'table'       => 'discounts',
        ];
        $fields[] = [
            'name'        => $this->settingKey . '_parser_fields[special]',
            'name_column' => '_SPECIAL_',
            'label'       => 'Акции',
            'disabled'    => false,
            'checked'     => isset($parser_fields['special']),
            'table'       => 'specials',
        ];

        return $fields;

    }

    private function loadModuleInfoData(&$data)
    {


        $data['action'] = $this->url->link('extension/module/google_sheets/setting', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('extension/module/', 'user_token=' . $this->session->data['user_token'], true);

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/google_sheets/setting', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['entry_save_danger'] = $this->language->get('entry_save_danger');
        $data['text_tab_instruction'] = $this->language->get('text_tab_instruction');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_login'] = $this->language->get('text_login');

        $data['entry_status'] = $this->language->get('entry_status');

        for ($i = 1; $i < 10; $i++) {
            $data['instruction_steps'][] = $this->language->get('instruction_step_' . $i);
        }


        $data['text_tab_setting'] = $this->language->get('text_tab_setting');
        $data['text_tab_import'] = $this->language->get('text_tab_import');
        $data['text_tab_export'] = $this->language->get('text_tab_export');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $data['heading_title'] = $this->language->get('heading_title');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/module/google_sheets/setting')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}
