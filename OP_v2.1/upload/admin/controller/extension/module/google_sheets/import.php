<?php

class ControllerExtensionModuleGoogleSheetsImport extends Controller
{
    /**
     * @var ControllerExtensionModuleGoogleSheetsClient
     */
    private $sheetsClient;
    private $settingController;
    private $language_import_code;
    private $language_id;

    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->load->language('extension/module/google_sheets/setting');
        $this->load->model('extension/module/google_sheets');

        require_once __DIR__ . '/client.php';
        $this->sheetsClient = new ControllerExtensionModuleGoogleSheetsClient($this->registry);

        require_once __DIR__ . '/setting.php';
        $this->settingController = new ControllerExtensionModuleGoogleSheetsSetting($this->registry);

        $this->language_import_code = $this->config->get($this->settingController->settingKey . '_language_import');
        $this->language_id = $this->model_extension_module_google_sheets->getLanguageIdByCode($this->language_import_code);
    }

    public function index()
    {
        $json = [];

        $page = @$this->request->post['page'];

        $load_fields = $this->settingController->loadFields();


        try {

            $this->validateSheets();

            $start = ($page - 1) * $this->config->get($this->settingController->settingKey . '_count_step');
            $to = $start + $this->config->get($this->settingController->settingKey . '_count_step');

            $start = ($start == 0) ? 2 : $start + 1;

            $total_rows = $this->getTotalRows();

            $document_titles = $this->getDocumentTitles();

            $this->findUpdateFieldInDocument($document_titles);

            $update_field_key = $this->getUpdateFieldKey();

            $rows = $this->getRows($start, $to);

            if ($rows) {

                $products_table = $this->addKeyToFieldsProducts($rows, $document_titles);

                foreach ($products_table as $product_table) {

                    $product = $this->createProductFromFields($product_table, $load_fields);

                    if (isset($product['categories'])) {
                        $product['categories'] = $this->parseCategories($product['categories'], $product['language_id']);
                    }

                    if (isset($product['attributes'])) {
                        $product['attributes'] = $this->parseAttributes($product['attributes'], $product['language_id']);
                    }

                    $product_id = 0;

                    if (isset($product['product'][$update_field_key])) {
                        $product_id = $this->model_extension_module_google_sheets->findProductByField($product['product'][$update_field_key], $update_field_key);
                    }

                    unset($product['product']['product_id']);


                    if ($product_id) {
                        $this->model_extension_module_google_sheets->editProduct($product_id, $product, $product['language_id']);
                    } else {
                        $this->model_extension_module_google_sheets->addProduct($product, $product['language_id']);
                    }

                }


                $json['page'] = $page + 1;
                $json['progress'] = (int)(($start * 100) / $total_rows);
            }

            $json['status'] = true;

        } catch (\Exception $e) {
            $json['status'] = false;
            $json['error'] = $e->getMessage();
        }

        $this->response->setOutput(json_encode($json));
    }


    private function getUpdateFieldKey()
    {

        return array_search($this->config->get($this->settingController->settingKey . '_import_update_field'),
            array_column($this->settingController->loadFields(), 'name_column', 'name_in_table'));
    }

    private function parseAttributes($rows_attributes, $language_id)
    {
        if (!$language_id) throw new \Exception($this->language->get('error_language_field'));
        if ($language_id != $this->language_id) throw new \Exception($this->language->get('error_language_code'));

        $attributes = [];

        $attribute_group_id = $this->config->get($this->settingController->settingKey . '_import_attribute_group_id');

        foreach ($rows_attributes as $row_attribute) {
            $attr = explode('|', $row_attribute);
            if (isset($attr[0]) && isset($attr[1]) && !empty($attr[0]) && !empty($attr[1])) {
                $attribute_id = $this->model_extension_module_google_sheets->findAttribute($attr[0], $language_id, $attribute_group_id);

                if ($attribute_id) {
                    $attributes[] = [
                        'attribute_id' => $attribute_id,
                        'text'         => $attr[1],
                    ];
                }
            }

        }

        return $attributes;

    }

    private function parseCategories($rows_categories, $language_id)
    {
        if (!$language_id) throw new \Exception($this->language->get('error_language_field'));
        if ($language_id != $this->language_id) throw new \Exception($this->language->get('error_language_code'));

        $category_ids = [];

        foreach ($rows_categories as $row_category) {

            $categories = explode('|', $row_category);
            $category_id = 0;

            foreach ($categories as $category) {
                $category_id = $this->model_extension_module_google_sheets->addOrUpdateCategory(trim($category), $language_id, $category_id);
            }
            if ($category_id) {
                $category_ids[] = $category_id;
            }
        }

        return $category_ids;

    }

    private function createProductFromFields($product_table, $load_fields)
    {
        $product = [];

        foreach ($load_fields as $field) {

            if (isset($field['table']) && isset($product_table[$field['name_column']])) {

                if ($field['table'] == 'product') {

                    $product['product'][$field['name_in_table']] = $product_table[$field['name_column']];

                } elseif ($field['table'] == 'lang') {

                    $product['language_id'] = $this->model_extension_module_google_sheets->getLanguageIdByCode($product_table[$field['name_column']]);

                } elseif ($field['table'] == 'description') {

                    $product['description'][$field['name_in_table']] = $product_table[$field['name_column']];

                } elseif ($field['table'] == 'images') {

                    $product['images'] = explode(PHP_EOL, $product_table[$field['name_column']]);

                } elseif ($field['table'] == 'manufacturer') {

                    $product['product']['manufacturer_id'] = $this->model_extension_module_google_sheets->getManufacturerIDByName($product_table[$field['name_column']]);

                } elseif ($field['table'] == 'categories') {

                    $product['categories'] = explode(PHP_EOL, $product_table[$field['name_column']]);

                } elseif ($field['table'] == 'attributes') {

                    $product['attributes'] = explode(PHP_EOL, $product_table[$field['name_column']]);

                } elseif ($field['table'] == 'discounts') {

                    $product['discounts'] = $this->parseDiscounts($product_table[$field['name_column']]);

                } elseif ($field['table'] == 'specials') {

                    $product['specials'] = $this->parseSpecials($product_table[$field['name_column']]);

                }

            }

        }

        return $product;
    }

    private function parseSpecials($row_specials)
    {
        $specials = [];

        foreach (explode(PHP_EOL, $row_specials) as $special) {
            $item = $this->string2KeyedArray($special);
            if ($item
                && array_key_exists('customer_group_id', $item)
                && array_key_exists('price', $item)
            ) {
                $specials[] = $item;
            }
        }

        return $specials;
    }

    private function parseDiscounts($row_discounts)
    {
        $discounts = [];

        foreach (explode(PHP_EOL, $row_discounts) as $discount) {
            $item = $this->string2KeyedArray($discount);
            if ($item
                && array_key_exists('customer_group_id', $item)
                && array_key_exists('quantity', $item)
                && array_key_exists('price', $item)
            ) {
                $discounts[] = $item;
            }
        }

        return $discounts;
    }


    private function string2KeyedArray($string, $delimiter = '|', $kv = '=')
    {
        $return = [];

        if ($a = explode($delimiter, $string)) { // create parts
            foreach ($a as $s) {
                if ($s) {
                    if ($pos = strpos($s, $kv)) { // key/value delimiter
                        $return[trim(substr($s, 0, $pos))] = trim(substr($s, $pos + strlen($kv)));
                    } else {
                        $return[] = trim($s);
                    }
                }
            }
            return $return;
        }
    }

    private function addKeyToFieldsProducts($rows, $document_titles)
    {
        return array_map(function ($element) use ($document_titles) {
            if (count($document_titles) > count($element)) {
                $element = array_pad($element, count($document_titles), '');
            } elseif (count($document_titles) < count($element)) {
                $document_titles = array_pad($document_titles, count($element), '');
            }
            return array_combine($document_titles, $element);
        }, $rows);
    }

    private function findUpdateFieldInDocument($document_titles)
    {
        if (!in_array($this->config->get($this->settingController->settingKey . '_import_update_field'), $document_titles)) {
            throw new \Exception($this->language->get('error_field_update') . $this->config->get($this->settingController->settingKey . '_import_update_field'));
        }
    }

    private function getDocumentTitles()
    {
        $row = $this->sheetsClient->getRow(1);
        if ($row) {
            return $row;
        } else throw new \Exception($this->language->get('error_document_title'));

    }

    private function getRows($row_from = 1, $row_to = 1)
    {
        return $this->sheetsClient->getRows($row_from, $row_to);
    }

    private function getTotalRows()
    {
        return $this->sheetsClient->getTotalRows();
    }

    private function validateSheets()
    {
        $this->sheetsClient->getSheets();
    }


}
