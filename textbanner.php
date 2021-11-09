<?php
/*
* 2015-2021 vallka
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
*  @author Vallka <vallka@vallka.com>
*  @copyright  2015-2021 vallka
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class textbanner extends Module implements WidgetInterface
{
    private $templateFile;

	public function __construct()
	{
		$this->name = 'textbanner';
		$this->version = '0.1.0';
		$this->author = 'Vallka';
		$this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('TextBanner', array(), 'Modules.TextBanner.Admin');
        $this->description = $this->trans('Displays a text banner on your shop.', array(), 'Modules.TextBanner.Admin');

        $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_);

        $this->templateFile = 'module:textbanner/textbanner.tpl';
    }

    public function install()
    {

        PrestaShopLogger::addLog('Banner Install',1);

        return (parent::install() &&
            $this->registerHook('displayBanner') &&
            $this->registerHook('actionObjectLanguageAddAfter') &&
            $this->registerHook('backOfficeHeader') &&
            $this->installFixtures() 
            /*&& $this->disableDevice(Context::DEVICE_MOBILE)*/
        );
    }

    public function hookBackOfficeHeader()
    {
        $this->context->controller->addJS($this->_path.'views/js/back.js');
    }

    public function hookActionObjectLanguageAddAfter($params)
    {
        return $this->installFixture((int)$params['object']->id, Configuration::get('TEXTBANNER_IMG', (int)Configuration::get('PS_LANG_DEFAULT')));
    }

    protected function installFixtures()
    {
        $languages = Language::getLanguages(false);

        foreach ($languages as $lang) {
            $this->installFixture((int)$lang['id_lang'], 'sale70.png');
        }

        return true;
    }

    protected function installFixture($id_lang, $image = null)
    {
        $values['TEXTBANNER_LINK'][(int)$id_lang] = '';
        $values['TEXTBANNER_DESC'][(int)$id_lang] = '';

        Configuration::updateValue('TEXTBANNER_LINK', $values['TEXTBANNER_LINK']);
        Configuration::updateValue('TEXTBANNER_DESC', $values['TEXTBANNER_DESC']);
    }

    public function uninstall()
    {
        PrestaShopLogger::addLog('Banner UNInstall',1);

        Configuration::deleteByName('TEXTBANNER_LINK');
        Configuration::deleteByName('TEXTBANNER_DESC');

        return parent::uninstall();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitStoreConf')) {
            $languages = Language::getLanguages(false);
            $values = array();

            foreach ($languages as $lang) {

                $values['TEXTBANNER_LINK'][$lang['id_lang']] = Tools::getValue('TEXTBANNER_LINK_'.$lang['id_lang']);
                $values['TEXTBANNER_DESC'][$lang['id_lang']] = Tools::getValue('TEXTBANNER_DESC_'.$lang['id_lang'],true);

            }


            Configuration::updateValue('TEXTBANNER_LINK', $values['TEXTBANNER_LINK']);
            Configuration::updateValue('TEXTBANNER_DESC', $values['TEXTBANNER_DESC'],1);

            $this->_clearCache($this->templateFile);

            return $this->displayConfirmation($this->trans('The settings have been updated.', array(), 'Admin.Notifications.Success'));
        }

        return '';
    }

    public function getContent()
    {
        return $this->postProcess().$this->renderForm();
    }

    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->trans('Settings', array(), 'Admin.Global'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->trans('Banner Link', array(), 'Modules.Banner.Admin'),
                        'name' => 'TEXTBANNER_LINK',
                        'desc' => $this->trans('Enter the link associated to your banner. When clicking on the banner, the link opens in the same window. If no link is entered, it redirects to the homepage.', array(), 'Modules.TextBanner.Admin')
                    ),
                    array(
                        'type' => 'textarea',
                        'lang' => true,
                        'label' => $this->trans('Banner description', array(), 'Modules.Banner.Admin'),
                        'name' => 'TEXTBANNER_DESC',
                        'desc' => $this->trans('Please enter a short but meaningful description for the banner. HTML allowed', array(), 'Modules.TextBanner.Admin')
                    )
                ),
                'submit' => array(
                    'title' => $this->trans('Save', array(), 'Admin.Actions')
                )
            ),
        );

        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitStoreConf';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues()
    {
        $languages = Language::getLanguages(false);
        $fields = array();

        foreach ($languages as $lang) {
            $fields['TEXTBANNER_LINK'][$lang['id_lang']] = Tools::getValue('TEXTBANNER_LINK_'.$lang['id_lang'], Configuration::get('TEXTBANNER_LINK', $lang['id_lang']));
            $fields['TEXTBANNER_DESC'][$lang['id_lang']] = Tools::getValue('TEXTBANNER_DESC_'.$lang['id_lang'], Configuration::get('TEXTBANNER_DESC', $lang['id_lang']));
        }

        return $fields;
    }

    public function renderWidget($hookName, array $params)
    {
        if (!$this->isCached($this->templateFile, $this->getCacheId('textbanner'))) {
            $this->smarty->assign($this->getWidgetVariables($hookName, $params));
        }

        //PrestaShopLogger::addLog('Banner renderWidget:'.$this->fetch($this->templateFile, $this->getCacheId('textbanner')),1);

        return $this->fetch($this->templateFile, $this->getCacheId('textbanner'));
    }

    public function getWidgetVariables($hookName, array $params)
    {

        $textbanner_link = Configuration::get('TEXTBANNER_LINK', $this->context->language->id);

        //PrestaShopLogger::addLog('Banner getWidgetVariables:'.Configuration::get('TEXTBANNER_DESC', $this->context->language->id),1);

        return array(
            'textbanner_link' => $this->updateUrl($textbanner_link),
            'textbanner_desc' => Configuration::get('TEXTBANNER_DESC', $this->context->language->id)
        );
    }

    private function updateUrl($link)
    {
        if ($link) {
            if (substr($link, 0, 7) !== "http://" && substr($link, 0, 8) !== "https://") {
                $link = "http://" . $link;
            }
        }

        return $link;
    }
}
