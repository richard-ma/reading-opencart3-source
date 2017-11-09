<?php

class ControllerExtensionModuleRmOrderExporter extends Controller {

    public function index() {
        $data = array();

        $this->language->load('extension/module/rm_order_exporter');

        $this->document->setTitle($this->language->get('heading_title'));

        // set language data
        //$variables = array(
            //'heading_title',
            //'heading_title_version',
        //);
        //foreach($variables as $variable) $data[$variable] = $this->language->get($variable);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/rm_order_exporter', $data));
    }

    public function install() {
    }

    public function uninstall() {
    }
}
