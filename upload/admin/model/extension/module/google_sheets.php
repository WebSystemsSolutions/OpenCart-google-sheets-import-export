<?php

class ModelExtensionModuleGoogleSheets extends Model
{

    public function editProduct($product_id, $data, $language_id)
    {

        // table product
        if (isset($data['product'])) {
            $fields = [];
            foreach ($data['product'] as $field => $value) {
                $fields[] = "{$field} = '" . $this->db->escape($value) . "'";
            }
            if ($fields) {
                $this->db->query('UPDATE ' . DB_PREFIX . "product SET " . implode(', ', $fields) . " , date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
            }
        }

        // table product description
        if (isset($data['description'])) {
            $fields = [];
            foreach ($data['description'] as $field => $value) {
                $fields[] = "{$field} = '" . $this->db->escape($value) . "'";
            }
            if ($fields) {
                $this->db->query('UPDATE ' . DB_PREFIX . "product_description SET " . implode(', ', $fields) . " WHERE product_id = '" . (int)$product_id . "' AND language_id = '" . (int)$language_id . "'");

            }
        }


        // table product_image
        if (isset($data['images'])) {
            $this->db->query('DELETE FROM ' . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
            if (isset($data['images'])) {
                foreach ($data['images'] as $product_image) {
                    $this->db->query('INSERT INTO ' . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image) . "'");
                }
            }
        }


        // table product_to_category
        if (isset($data['categories'])) {
            $this->db->query('DELETE FROM ' . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
            foreach ($data['categories'] as $category_id) {
                $this->db->query('INSERT INTO ' . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
            }
        }

        // table product_discount
        if (isset($data['discounts'])) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");
            foreach ($data['discounts'] as $product_discount) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_discount['customer_group_id'] . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '0', price = '" . (float)$product_discount['price'] . "'");
            }
        }

        // table product_special
        if (isset($data['specials'])) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
            foreach ($data['specials'] as $product_special) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '0', price = '" . (float)$product_special['price'] . "'");
            }
        }

        // table product_attribute
        if (!empty($data['attributes'])) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND language_id = '" . (int)$language_id . "'");
            foreach ($data['attributes'] as $product_attribute) {
                if ($product_attribute['attribute_id']) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" . $this->db->escape($product_attribute['text']) . "'");
                }
            }
        }


    }

    public function addProduct($data, $language_id)
    {

        $this->load->model('localisation/language');

        $languages = $this->model_localisation_language->getLanguages();

        // table product
        if (isset($data['product'])) {
            $fields = [];
            foreach ($data['product'] as $field => $value) {
                $fields[] = "{$field} = '" . $this->db->escape($value) . "'";
            }
            if ($fields) {
                $this->db->query('INSERT INTO ' . DB_PREFIX . "product SET " . implode(', ', $fields) . " , date_modified = NOW() , date_added = NOW()");
            }
        } else {
            return;
        }

        $product_id = $this->db->getLastId();

        // table product description
        if (isset($data['description'])) {
            $fields = [];
            foreach ($data['description'] as $field => $value) {
                $fields[] = "{$field} = '" . $this->db->escape($value) . "'";
            }
            if ($fields) {
                foreach ($languages as $language) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET " . implode(', ', $fields) . ",  product_id = '" . (int)$product_id . "', language_id = '" . (int)$language['language_id'] . "'");
                }

            }
        }


        // table product_image
        if (isset($data['images'])) {
            if (isset($data['images'])) {
                foreach ($data['images'] as $product_image) {
                    $this->db->query('INSERT INTO ' . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image) . "'");
                }
            }
        }


        // table product_to_category
        if (isset($data['categories'])) {
            foreach ($data['categories'] as $category_id) {
                $this->db->query('INSERT INTO ' . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
            }
        }

        // table product_discount
        if (isset($data['discounts'])) {
            foreach ($data['discounts'] as $product_discount) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_discount['customer_group_id'] . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '0', price = '" . (float)$product_discount['price'] . "'");
            }
        }

        // table product_special
        if (isset($data['specials'])) {
            foreach ($data['specials'] as $product_special) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '0', price = '" . (float)$product_special['price'] . "'");
            }
        }

        // table product_attribute
        if (!empty($data['attributes'])) {
            foreach ($data['attributes'] as $product_attribute) {
                if ($product_attribute['attribute_id']) {
                    foreach ($languages as $language) {
                        $this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language['language_id'] . "', text = '" . $this->db->escape($product_attribute['text']) . "'");
                    }
                }
            }
        }

    }


    public function getLanguageIdByCode($code)
    {
        $query = $this->db->query("SELECT language_id FROM " . DB_PREFIX . "language WHERE code = '" . $this->db->escape($code) . "'");

        return ($query->row) ? $query->row['language_id'] : '';
    }

    public function getProducts($data = array())
    {

        $language_id = isset($data['language_id']) ? $data['language_id'] : (int)$this->config->get('config_language_id');

        $sql = "SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)";

        $sql .= " WHERE pd.language_id = '" . (int)$language_id . "'";

        $sql .= " GROUP BY p.product_id";

        $sql .= " ORDER BY pd.name";


        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getTotalProducts($data = array())
    {

        $language_id = isset($data['language_id']) ? $data['language_id'] : (int)$this->config->get('config_language_id');

        $sql = "SELECT COUNT(DISTINCT p.product_id) AS total FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)";

        $sql .= " WHERE pd.language_id = '" . (int)$language_id . "'";


        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function getCategories($data = array())
    {

        $language_id = isset($data['language_id']) ? $data['language_id'] : (int)$this->config->get('config_language_id');

        $sql = "SELECT cp.category_id AS category_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR ' | ') AS name, c1.parent_id, c1.sort_order, c1.status  FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category c1 ON (cp.category_id = c1.category_id) LEFT JOIN " . DB_PREFIX . "category c2 ON (cp.path_id = c2.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (cp.category_id = cd2.category_id) WHERE cd1.language_id = '" . (int)$language_id . "' AND cd2.language_id = '" . (int)$language_id . "'";


        $sql .= " GROUP BY cp.category_id";

        $sql .= " ORDER BY sort_order";


        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

//            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return ($query->rows) ? array_column($query->rows, null, 'category_id') : [];
    }

    public function getProductCategories($product_id)
    {
        $product_category_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

        foreach ($query->rows as $result) {
            $product_category_data[] = $result['category_id'];
        }

        return $product_category_data;
    }

    public function getProductImages($product_id)
    {
        return $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order ASC")->rows;
    }

    public function getProductAttributes($product_id, $language_id = 0)
    {

        if (!$language_id) {
            $language_id = (int)$this->config->get('config_language_id');
        }

        $product_attribute_group_data = array();

        $product_attribute_group_query = $this->db->query("SELECT ag.attribute_group_id, agd.name FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_group ag ON (a.attribute_group_id = ag.attribute_group_id) LEFT JOIN " . DB_PREFIX . "attribute_group_description agd ON (ag.attribute_group_id = agd.attribute_group_id) WHERE pa.product_id = '" . (int)$product_id . "' AND agd.language_id = '" . (int)$language_id . "' GROUP BY ag.attribute_group_id ORDER BY ag.sort_order, agd.name");

        foreach ($product_attribute_group_query->rows as $product_attribute_group) {
            $product_attribute_data = array();

            $product_attribute_query = $this->db->query("SELECT a.attribute_id, ad.name, pa.text FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE pa.product_id = '" . (int)$product_id . "' AND a.attribute_group_id = '" . (int)$product_attribute_group['attribute_group_id'] . "' AND ad.language_id = '" . (int)$language_id . "' AND pa.language_id = '" . (int)$language_id . "' ORDER BY a.sort_order, ad.name");

            foreach ($product_attribute_query->rows as $product_attribute) {
                $product_attribute_data[] = array(
                    'attribute_id' => $product_attribute['attribute_id'],
                    'name'         => $product_attribute['name'],
                    'text'         => $product_attribute['text']
                );
            }

            $product_attribute_group_data[] = array(
                'attribute_group_id' => $product_attribute_group['attribute_group_id'],
                'name'               => $product_attribute_group['name'],
                'attribute'          => $product_attribute_data
            );
        }

        return $product_attribute_group_data;
    }

    public function getProductDiscounts($product_id)
    {
        return $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' ORDER BY quantity, priority, price")->rows;
    }

    public function getProductSpecials($product_id)
    {
        return $this->db->query("SELECT * FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' ORDER BY priority, price")->rows;
    }

    public function getManufacturerIDByName($name)
    {
        $query = $this->db->query("SELECT manufacturer_id FROM " . DB_PREFIX . "manufacturer_description  WHERE name = '" . $this->db->escape($name) . "'");

        return $query->rows ? $query->row['manufacturer_id'] : '';
    }

    public function getManufacturer($manufacturer_id, $language_id = 0)
    {

        if (!$language_id) {
            $language_id = (int)$this->config->get('config_language_id');
        }

        return $this->db->query("SELECT DISTINCT *, md.name AS name FROM " . DB_PREFIX . "manufacturer m LEFT JOIN " . DB_PREFIX . "manufacturer_description md ON (m.manufacturer_id = md.manufacturer_id) LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) WHERE md.language_id = '" . (int)$language_id . "' && m.manufacturer_id = '" . (int)$manufacturer_id . "' AND m2s.store_id = '" . (int)$this->config->get('config_store_id') . "'")->row;
    }

    public function findProductByField($val, $field)
    {
        $query = $this->db->query('SELECT product_id FROM ' . DB_PREFIX . "product WHERE {$field} = '" . $this->db->escape($val) . "' LIMIT 1");
        return $query->rows ? $query->row['product_id'] : 0;
    }

    public function addOrUpdateCategory($name, $language_id, $parent_id = 0)
    {

        $query = $this->db->query('SELECT c.category_id FROM ' . DB_PREFIX . 'category_description cd LEFT JOIN ' . DB_PREFIX . "category c ON (c.category_id = cd.category_id) WHERE cd.language_id = '" . $language_id . "' AND cd.name = '" . $this->db->escape($name) . "' AND c.parent_id = '" . $parent_id . "' LIMIT 1");

        if ($query->rows) {
            return $query->row['category_id'];
        }


        // add category
        $this->db->query('INSERT INTO ' . DB_PREFIX . "category SET parent_id = '" . (int)$parent_id . "', status = '1', date_modified = NOW(), date_added = NOW()");

        $category_id = $this->db->getLastId();

        $this->load->model('localisation/language');

        foreach ($this->model_localisation_language->getLanguages() as $language) {
            $this->db->query('INSERT INTO ' . DB_PREFIX . "category_description SET category_id = '" . (int)$category_id . "', language_id = '" . (int)$language['language_id'] . "', name = '" . $this->db->escape($name) . "', meta_title = '" . $this->db->escape($name) . "'");
        }


        // MySQL Hierarchical Data Closure Table Pattern
        $level = 0;

        $query = $this->db->query('SELECT * FROM `' . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$parent_id . "' ORDER BY `level` ASC");

        foreach ($query->rows as $result) {
            $this->db->query('INSERT INTO `' . DB_PREFIX . "category_path` SET `category_id` = '" . (int)$category_id . "', `path_id` = '" . (int)$result['path_id'] . "', `level` = '" . $level . "'");

            $level++;
        }

        $this->db->query('INSERT INTO `' . DB_PREFIX . "category_path` SET `category_id` = '" . (int)$category_id . "', `path_id` = '" . (int)$category_id . "', `level` = '" . $level . "'");

        $this->db->query('INSERT INTO ' . DB_PREFIX . "category_to_store SET category_id = '" . (int)$category_id . "', store_id = '" . (int)$this->store_id . "'");


        return $category_id;

    }


    public function findAttribute($name, $language_id, $attribute_group_id)
    {

        $query = $this->db->query('SELECT a.attribute_id FROM ' . DB_PREFIX . 'attribute_description ad LEFT JOIN ' . DB_PREFIX . "attribute a ON (a.attribute_id = ad.attribute_id) WHERE ad.language_id = '" . $language_id . "' AND ad.name = '" . $this->db->escape($name) . "' LIMIT 1");

        if ($query->rows) {
            return $query->row['attribute_id'];
        }


        $this->db->query('INSERT INTO ' . DB_PREFIX . "attribute SET attribute_group_id = '" . (int)$attribute_group_id . "'");

        $attribute_id = $this->db->getLastId();

        $this->load->model('localisation/language');

        foreach ($this->model_localisation_language->getLanguages() as $language) {
            $this->db->query('INSERT INTO ' . DB_PREFIX . "attribute_description SET attribute_id = '" . (int)$attribute_id . "', language_id = '" . (int)$language['language_id'] . "', name = '" . $this->db->escape($name) . "'");
        }


        return $attribute_id;
    }

}
