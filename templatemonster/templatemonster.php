<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pladmin
 * Date: 8/20/13
 * Time: 3:20 PM
 * To change this template use File | Settings | File Templates.
 */

if (!defined('_PS_VERSION_')) {
    exit;
}
/**
 * Class TemplateMonster
 */
class TemplateMonster extends Module {

    public function __construct()
    {
        $this->version                  = 1.0;
        $this->is_configurable          = 1;
        $this->need_instance            = 1;
        $this->name                     = 'templatemonster';
        $this->tab                      = 'content_management';
        $this->author                   = 'Template Monster';
        $this->ps_versions_compliancy   = array('min' => '1.5', 'max' => '1.6');

        parent::__construct();

        $this->displayName = $this->l('Template Monster API Importer');
        $this->description = $this->l('Import templates information to PrestaShop shopping store');
    }

    public function install()
    {

        return parent::install()
            /**
             * Last catalog update. Used for retrieve actual only actual template data
             */
            && Configuration::updateValue('TEMPLATE_MONSTER_LAST_UPDATE', '')
            && Configuration::updateValue('TEMPLATE_MONSTER_API_USER'   , '')
            && Configuration::updateValue('TEMPLATE_MONSTER_API_TOKEN'  , '');
    }

    public function uninstall()
    {
        return parent::uninstall()
            && Configuration::updateValue('TEMPLATE_MONSTER_LAST_UPDATE', '')
            && Configuration::updateValue('TEMPLATE_MONSTER_API_USER'   , '')
            && Configuration::updateValue('TEMPLATE_MONSTER_API_TOKEN'  , '');
    }

    public function getContent()
    {
        /** @var  string $cont Output content*/
        $cont = '';

        if( Tools::isSubmit('run' . $this->name) ) {

        }

        if( Tools::isSubmit('submit' . $this->name) ) {
            if( ! trim(Tools::getValue('TEMPLATE_MONSTER_API_USER'))
                    || ! Tools::getValue('TEMPLATE_MONSTER_API_TOKEN')
                    || ! $this->_validateAPIAccess(Tools::getValue('TEMPLATE_MONSTER_API_USER'), Tools::getValue('TEMPLATE_MONSTER_API_TOKEN')) ) {

                $cont .= $this->displayError($this->l('<b>Something wrong!</b> Please fill all form fields or check your access details'));
            } else {

                Configuration::updateValue('TEMPLATE_MONSTER_API_USER'   , Tools::getValue('TEMPLATE_MONSTER_API_USER'));
                Configuration::updateValue('TEMPLATE_MONSTER_API_TOKEN'  , Tools::getValue('TEMPLATE_MONSTER_API_TOKEN'));

                $cont .= $this->displayConfirmation($this->l('API Access data is updated successfully!'));
            }
        }
        return $cont . $this->displayForm();

    }

    public function displayForm()
    {
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('API Setting'),
            ),
            'input' => array(
                array(
                    'type'      => 'text',
                    'label'     => $this->l('API User Name'),
                    'name'      => 'TEMPLATE_MONSTER_API_USER',
                    'size'      => 45,
                    'required'  => true
                ),
                array(
                    'type'      => 'text',
                    'label'     => $this->l('API User KEY'),
                    'name'      => 'TEMPLATE_MONSTER_API_TOKEN',
                    'size'      => 45,
                    'required'  => true
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button'
            )
        );

        $helper = new HelperForm();

        $helper->module                     = $this;
        $helper->name_controller            = $this->name;
        $helper->token                      = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex               = AdminController::$currentIndex.'&configure='.$this->name;

        $helper->default_form_language      = $default_lang;
        $helper->allow_employee_form_lang   = $default_lang;

        $helper->title = $this->displayName;
        $helper->show_toolbar               = true;
        $helper->toolbar_scroll             = true;
        $helper->submit_action              = 'submit'.$this->name;
        $helper->toolbar_btn = array(
            'run' => array(
                'desc' => $this->l('Import'),
                'class' => 'run-action',
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&run'.$this->name.
                '&token='.Tools::getAdminTokenLite('AdminModules'),
            ),
            'save' => array(
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                '&token='.Tools::getAdminTokenLite('AdminModules'),
            ),
            'back' => array(
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );

        $helper->fields_value['TEMPLATE_MONSTER_API_USER']  = Configuration::get('TEMPLATE_MONSTER_API_USER');
        $helper->fields_value['TEMPLATE_MONSTER_API_TOKEN'] = Configuration::get('TEMPLATE_MONSTER_API_TOKEN');

        return $helper->generateForm($fields_form);
    }


    /**
     * Validate Form Data Via API Connector
     * @param $apiUser
     * @param $apiToken
     * @return bool
     */
    private function _validateAPIAccess($apiUser, $apiToken)
    {
        /** @TODO Validate user and APi key */
        return true;
    }

    private function run()
    {
        $user   = Configuration::get('TEMPLATE_MONSTER_API_USER');
        $token  = Configuration::get('TEMPLATE_MONSTER_API_TOKEN');


    }
}