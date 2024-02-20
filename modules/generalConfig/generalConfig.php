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

class FraisPort extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'generalConfig';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Mita';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Configuration general');
        $this->description = $this->l('Module de configuration general');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => '8.0');
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('GENERAL_CONF_NL_TYPE', 0);
        Configuration::updateValue('GENERAL_CONF_NL_CODE', '');
        Configuration::updateValue('GENERAL_CONF_NL_NAME', '');
        Configuration::updateValue('GENERAL_CONF_NL_TYPE', 0);

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayBackOfficeHeader');
    }

    public function uninstall()
    {
        Configuration::deleteByName('GENERAL_CONF_NL_TYPE');
        Configuration::deleteByName('GENERAL_CONF_NL_NAME');
        Configuration::deleteByName('GENERAL_CONF_NL_CODE');
        Configuration::deleteByName('GENERAL_CONF_NL_START_DATE');
        Configuration::deleteByName('GENERAL_CONF_NL_END_DATE');

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
        if (((bool)Tools::isSubmit('submitFraisPortModule')) == true) {
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
        $helper->submit_action = 'submitFraisPortModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigFormNewsletter()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigFormNewsletter()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Newsletter promo settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    // array(
                    //     'type' => 'switch',
                    //     'label' => $this->l('Live mode'),
                    //     'name' => 'FRAISPORT_LIVE_MODE',
                    //     'is_bool' => true,
                    //     'desc' => $this->l('Use this module in live mode'),
                    //     'values' => array(
                    //         array(
                    //             'id' => 'active_on',
                    //             'value' => true,
                    //             'label' => $this->l('Enabled')
                    //         ),
                    //         array(
                    //             'id' => 'active_off',
                    //             'value' => false,
                    //             'label' => $this->l('Disabled')
                    //         )
                    //     ),
                    // ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Promo Type'),
                        'name' => 'GENERAL_CONF_NL_TYPE',
                        'options' => array(
                        'query' => $options = array(
                            array(
                                'id_option' => 0,
                                'name' => 'Amount',
                            ),
                            array(
                                'id_option' => 1,
                                'name' => 'Percent',
                            ),
                        ),
                        'id' => 'id_option',
                        'name' => 'name',
                       ),
                    ),
                    array(
                        'col' => 6,
                        'type' => 'text',
                        'desc' => $this->l('Enter a promo code'),
                        'name' => 'GENERAL_CONF_NL_CODE',
                        'label' => $this->l('Code promo'),
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'GENERAL_CONF_NL_NAME',
                        'desc' => $this->l('Enter a promo name'),
                        'label' => $this->l('Code promo'),
                    ),
                    array(
                        'type' => 'date',
                        'name' => 'GENERAL_CONF_NL_START_DATE',
                        'desc' => $this->l('Enter promo start date'),
                        'label' => $this->l('Start date'),
                    ),
                    array(
                        'type' => 'date',
                        'name' => 'GENERAL_CONF_NL_END_DATE',
                        'desc' => $this->l('Enter promo end date'),
                        'label' => $this->l('End date'),
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
            'GENERAL_CONF_NL_CODE' => Configuration::get('GENERAL_CONF_NL_TYPE'),
            'GENERAL_CONF_NL_CODE' => Configuration::get('GENERAL_CONF_NL_CODE'),
            'GENERAL_CONF_NL_NAME' => Configuration::get('GENERAL_CONF_NL_NAME'),
            'GENERAL_CONF_NL_START_DATE' => Configuration::get('GENERAL_CONF_NL_START_DATE'),
            'GENERAL_CONF_NL_END_DATE' => Configuration::get('GENERAL_CONF_NL_END_DATE'),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {

        Configuration::updateValue('GENERAL_CONF_NL_CODE',Tools::getValue('GENERAL_CONF_NL_CODE'));
        Configuration::updateValue('GENERAL_CONF_NL_TYPE',Tools::getValue('GENERAL_CONF_NL_TYPE'));
        Configuration::updateValue('GENERAL_CONF_NL_NAME',Tools::getValue('GENERAL_CONF_NL_NAME'));
        Configuration::updateValue('GENERAL_CONF_NL_START_DATE',Tools::getValue('GENERAL_CONF_NL_START_DATE'));
        Configuration::updateValue('GENERAL_CONF_NL_END_DATE',Tools::getValue('GENERAL_CONF_NL_END_DATE'));

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
    }


}
