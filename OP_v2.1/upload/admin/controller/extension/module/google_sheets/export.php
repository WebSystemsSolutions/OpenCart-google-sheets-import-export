<?php

class ControllerExtensionModuleGoogleSheetsExport extends Controller
{
    /**
     * @var ControllerExtensionModuleGoogleSheetsClient
     */
    private $sheetsClient;
    private $settingController;

    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->load->language('extension/module/google_sheets/setting');
        $this->load->model('extension/module/google_sheets');

        require_once __DIR__ . '/client.php';
        $this->sheetsClient = new ControllerExtensionModuleGoogleSheetsClient($this->registry);

        require_once __DIR__ . '/setting.php';
        $this->settingController = new ControllerExtensionModuleGoogleSheetsSetting($this->registry);
    }

    public function index()
    {
        $json = [];

        $page = @$this->request->post['page'];

        $language_export_code = $this->config->get($this->settingController->settingKey . '_language_export');

        $language_id = $this->model_extension_module_google_sheets->getLanguageIdByCode($language_export_code);

        try {

            $this->validateSheets();

            if ($page == 'before') {

                $this->deleteSheets();
                $this->addTitleSheet();

                $json['page'] = 1;
                $json['progress'] = 10;

                return $this->response->setOutput(json_encode($json));

            }

            $start = ($page - 1) * $this->config->get($this->settingController->settingKey . '_count_step');
            $limit = $this->config->get($this->settingController->settingKey . '_count_step');


            $filter_data = array(
                'language_id' => $language_id,
                'start'       => $start,
                'limit'       => $limit
            );


            $product_total = $this->model_extension_module_google_sheets->getTotalProducts($filter_data);
            $results = $this->model_extension_module_google_sheets->getProducts($filter_data);

            $categories_data = $this->model_extension_module_google_sheets->getCategories($filter_data);

            if ($results) {

                $rows = [];
                foreach ($results as $result) {
                    $item = [];

                    foreach ($this->settingController->loadFields() as $title) {
                        if (!$title['checked']) {
                            continue;
                        }

                        if (isset($title['table']) && ($title['table'] == 'product')) {

                            if (isset($title['type_field']) && ($title['type_field'] == 'int')) {
                                $item[] = (int)$result[$title['name_in_table']];
                            } elseif (isset($title['type_field']) && ($title['type_field'] == 'double')) {
                                $item[] = (double)$result[$title['name_in_table']];
                            } else {
                                $item[] = $result[$title['name_in_table']];
                            }


                        } elseif (isset($title['table']) && ($title['table'] == 'lang')) {

                            $item[] = $language_export_code;

                        } elseif (isset($title['table']) && ($title['table'] == 'description')) {

                            $item[] = $result[$title['name_in_table']];

                        } elseif (isset($title['table']) && ($title['table'] == 'images')) {

                            $images = $this->model_extension_module_google_sheets->getProductImages($result['product_id']);
                            $item[] = ($images) ? implode(PHP_EOL, array_column($images, 'image')) : '';

                        } elseif (isset($title['table']) && ($title['table'] == 'categories')) {

                            $categories = $this->model_extension_module_google_sheets->getProductCategories($result['product_id']);
                            $text_category = [];
                            foreach ($categories as $category) {
                                if (isset($categories_data[$category]['name'])) {
                                    $text_category[] = $categories_data[$category]['name'];
                                }
                            }
                            $item[] = implode(PHP_EOL, $text_category);

                        } elseif (isset($title['table']) && ($title['table'] == 'manufacturer')) {

                            $item[] = $this->getParsedManufacturer($result['product_id'], $language_id);

                        } elseif (isset($title['table']) && ($title['table'] == 'attributes')) {

                            $item[] = $this->getParsedAttributes($result['product_id'], $language_id);

                        } elseif (isset($title['table']) && ($title['table'] == 'discounts')) {

                            $item[] = $this->getParsedDiscounts($result['product_id']);

                        } elseif (isset($title['table']) && ($title['table'] == 'specials')) {

                            $item[] = $this->getParsedSpecials($result['product_id']);

                        } else {
                            $item[] = '';
                        }

                    }

                    $rows[] = $item;
                }

                $this->sheetsClient->addRows($rows);

                $json['page'] = $page + 1;
                $json['progress'] = (int)(($start * 100) / $product_total);
            }


            $json['status'] = true;

        } catch (\Exception $e) {
            $json['status'] = false;
            $json['error'] = $e->getMessage();
        }

        $this->response->setOutput(json_encode($json));
    }

    private function getParsedManufacturer($manufacturer_id, $language_id)
    {
        $manufacturer_name = '';
        if ($manufacturer_id) {
            $manufacturer = $this->model_extension_module_google_sheets->getManufacturer($manufacturer_id, $language_id);
            $manufacturer_name = isset($manufacturer['name']) ? $manufacturer['name'] : '';
        }
        return $manufacturer_name;
    }

    private function getParsedAttributes($product_id, $language_id)
    {
        $attributes_text = [];
        $attributes_group = $this->model_extension_module_google_sheets->getProductAttributes($product_id, $language_id);
        foreach ($attributes_group as $group) {
            foreach ($group['attribute'] as $attribute) {
                $attributes_text[] = implode(' | ', [
//                    $group['name'],
                    $attribute['name'],
                    $attribute['text']
                ]);
            }
        }

        return implode(PHP_EOL, $attributes_text);
    }

    private function getParsedSpecials($product_id)
    {

        $specials_text = [];
        $specials = $this->model_extension_module_google_sheets->getProductSpecials($product_id);

        foreach ($specials as $special) {
            $specials_text[] = implode(' | ', [
                'customer_group_id=' . $special['customer_group_id'],
                'price=' . $special['price'],
            ]);
        }

        return implode(PHP_EOL, $specials_text);
    }

    private function getParsedDiscounts($product_id)
    {
        $discounts_text = [];
        $discounts = $this->model_extension_module_google_sheets->getProductDiscounts($product_id);

        foreach ($discounts as $discount) {
            $discounts_text[] = implode(' | ', [
                'customer_group_id=' . $discount['customer_group_id'],
                'quantity=' . $discount['quantity'],
                'price=' . $discount['price'],
            ]);
        }

        return implode(PHP_EOL, $discounts_text);
    }

    private function addTitleSheet()
    {
        $row_title = [];
        foreach ($this->settingController->loadFields() as $title) {
            if ($title['checked']) {
                $row_title[] = $title['name_column'];
            }
        }

        if ($row_title) {
            $this->sheetsClient->addRows([$row_title]);
        }
    }

    private function deleteSheets()
    {

        $this->sheetsClient->deleteSheets();
        $this->sheetsClient->clearSheets();

        $this->sheetsClient->renameFirstSheet('_PRODUCTS_');

    }

    private function validateSheets()
    {
        $this->sheetsClient->getSheets();
    }


}
