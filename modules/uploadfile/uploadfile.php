<?php
/**
* 2007-2024 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2024 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class UploadFile extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'uploadfile';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Mita';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Upload file in front');
        $this->description = $this->l('Upload file in front');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '8.0');
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('displayReassurance');
    }

    public function uninstall()
    {

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitUploadFileModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitUploadFileModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'col' => 6,
                        'type' => 'text',
                        'desc' => $this->l('Entrer la catégorie à ajouter'),
                        'name' => 'ITEK_SOUS_ORDONNANCE',
                        'label' => $this->l('Email'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'ITEK_SOUS_ORDONNANCE' => Configuration::get('ITEK_SOUS_ORDONNANCE', true),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
        $this->context->controller->addJs('https://code.jquery.com/jquery-2.2.4.min.js');
    }

    public function hookDisplayReassurance()
    {
        $this->context->smarty->assign('id_categ',Configuration::get('ITEK_SOUS_ORDONNANCE'));

        return $this->display(__FILE__,'product.tpl');
    }

    public function uploadAjax($file)
    {
        $dest = _PS_ROOT_DIR_.'/download/ordonnance/';
        $errors     = array();
        $maxsize    = 5000000;
        $acceptable = array(
            'application/pdf',
            'image/jpeg',
            'image/jpg',
            'image/png'
        );
        
        if(($file['size'] >= $maxsize) || ($file["size"] == 0)) {
            $errors[] = $this->getTranslator()->trans('Fichier trop volumineux. Le fichier doit être inférieur à 5 Mo.', array(), 'Modules.ItekUploadOrdonnance.Admin');
        }

        if((!in_array($file['type'], $acceptable)) && (!empty($file["type"]))) {
            $errors[] = $this->getTranslator()->trans('Type de fichier non valide. Seuls les types PDF, JPG et PNG sont acceptés.', array(), 'Modules.ItekUploadOrdonnance.Admin');
        }

        if(count($errors) === 0) {
            $name = md5(uniqid(rand(), true)).'.'.explode(".",$file['name'])[1];
            if(move_uploaded_file($file['tmp_name'], $dest.$name)){
                $res = ['message'=>'Fichier téléchargé avec succès', 'file'=>$name];
                
                return json_encode($res);    
            }
        } else {
            foreach($errors as $error) {
                return $error;
            }

            die(); //Ensure no more processing is done
        }
    }   
}
