<?php

class ControllerModuleWssGoogleSheetsLoader extends Controller
{
    private $error = array();

    public function index()
    {

        if (version_compare(VERSION, '3.0', '>=')) {
            $this->load->model('extension/extension');
            $extensions = $this->model_extension_extension->getInstalled('module');
        } elseif  (version_compare(VERSION, '2.3', '>=')) {
            $this->load->model('extension/extension');
            $extensions = $this->model_extension_extension->getInstalled('module');
        } else {
            $this->load->model('extension/extension');
            $extensions = $this->model_extension_extension->getInstalled('module');
        }

        if (in_array('wss_google_sheets_loader', $extensions)) {
            $this->response->redirect($this->url->link('extension/module/google_sheets/setting', 'token=' . $this->session->data['token'], 'SSL'));
        }

    }


}
