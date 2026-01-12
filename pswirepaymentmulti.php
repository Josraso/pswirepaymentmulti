<?php
/**
 * 2007-2025 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2025 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once dirname(__FILE__) . '/classes/BankAccount.php';

class Pswirepaymentmulti extends PaymentModule
{
    const FLAG_DISPLAY_PAYMENT_INVITE = 'BANK_WIRE_MULTI_PAYMENT_INVITE';

    protected $_html = '';
    protected $_postErrors = [];

    public $extra_mail_vars;
    public $is_eu_compatible;
    public $reservation_days;

    public function __construct()
    {
        $this->name = 'pswirepaymentmulti';
        $this->tab = 'payments_gateways';
        $this->version = '3.0.0';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '1.7.0.0', 'max' => _PS_VERSION_];
        $this->controllers = ['payment', 'validation'];
        $this->is_eu_compatible = 1;

        $this->currencies = true;
        $this->currencies_mode = 'checkbox';

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = 'Wire Payment Multi';
        $this->description = 'Acepta pagos por transferencia bancaria mostrando múltiples cuentas bancarias durante el checkout.';
        $this->confirmUninstall = '¿Estás seguro de que quieres desinstalar este módulo? Se eliminarán todos los datos de cuentas bancarias.';

        // Only check accounts if table exists (module is already installed)
        if ($this->active && Db::getInstance()->executeS('SHOW TABLES LIKE "' . _DB_PREFIX_ . 'wirepayment_multi_accounts"')) {
            $accounts = BankAccount::getActiveAccounts();
            if (!count($accounts)) {
                $this->warning = 'Debes configurar y activar al menos una cuenta bancaria antes de usar este módulo.';
            }
        }

        if ($this->active && !count(Currency::checkPaymentCurrencies($this->id))) {
            $this->warning = 'No se ha establecido ninguna moneda para este módulo.';
        }

        $this->reservation_days = Configuration::get('BANK_WIRE_MULTI_RESERVATION_DAYS');
    }

    public function install()
    {
        include dirname(__FILE__) . '/sql/install.php';

        Configuration::updateValue(self::FLAG_DISPLAY_PAYMENT_INVITE, true);
        Configuration::updateValue('BANK_WIRE_MULTI_RESERVATION_DAYS', 7);

        if (!parent::install()
            || !$this->registerHook('displayPaymentReturn')
            || !$this->registerHook('paymentOptions')
            || !$this->registerHook('displayHeader')
            || !$this->registerHook('actionEmailSendBefore')
            || !$this->registerHook('displayAdminOrderSide')
            || !$this->installTab()
        ) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        // PRIMERO: Eliminar todos los logos ANTES de eliminar la tabla
        try {
            $accounts = BankAccount::getAllAccounts();
            foreach ($accounts as $account) {
                $bankAccount = new BankAccount($account['id_bank_account']);
                $bankAccount->deleteLogo();
            }
        } catch (Exception $e) {
            // Si falla, continuar con la desinstalación
        }

        // Eliminar directorio de imágenes completo
        $imgDir = _PS_MODULE_DIR_ . 'pswirepaymentmulti/views/img/banks/';
        if (is_dir($imgDir)) {
            $files = glob($imgDir . '*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
        }

        // SEGUNDO: Eliminar configuraciones
        Configuration::deleteByName('BANK_WIRE_MULTI_CUSTOM_TEXT');
        Configuration::deleteByName('BANK_WIRE_MULTI_RESERVATION_DAYS');
        Configuration::deleteByName(self::FLAG_DISPLAY_PAYMENT_INVITE);

        // TERCERO: Eliminar tab
        $this->uninstallTab();

        // CUARTO: Desinstalar módulo padre
        if (!parent::uninstall()) {
            return false;
        }

        // QUINTO: Eliminar la tabla (ahora sí podemos eliminarla sin problemas)
        include dirname(__FILE__) . '/sql/uninstall.php';

        return true;
    }

    /**
     * Install admin tab
     */
    public function installTab()
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminBankAccounts';
        $tab->name = [];
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Bank Accounts';
        }
        $tab->id_parent = (int) Tab::getIdFromClassName('AdminParentPayment');
        $tab->module = $this->name;

        return $tab->add();
    }

    /**
     * Uninstall admin tab
     */
    public function uninstallTab()
    {
        $id_tab = (int) Tab::getIdFromClassName('AdminBankAccounts');
        if ($id_tab) {
            $tab = new Tab($id_tab);

            return $tab->delete();
        }

        return true;
    }

    protected function _postValidation()
    {
        if (Tools::isSubmit('btnSubmit')) {
            Configuration::updateValue(
                self::FLAG_DISPLAY_PAYMENT_INVITE,
                Tools::getValue(self::FLAG_DISPLAY_PAYMENT_INVITE)
            );

            $fieldReservationDays = Tools::getValue('BANK_WIRE_MULTI_RESERVATION_DAYS');
            if ($fieldReservationDays && !Validate::isUnsignedInt($fieldReservationDays)) {
                $this->_postErrors[] = 'El campo Período de reserva no es válido. Por favor, introduce un número entero positivo.';
            }
        }
    }

    protected function _postProcess()
    {
        if (Tools::isSubmit('btnSubmit')) {
            $custom_text = [];
            $languages = Language::getLanguages(false);
            foreach ($languages as $lang) {
                if (Tools::getIsset('BANK_WIRE_MULTI_CUSTOM_TEXT_' . $lang['id_lang'])) {
                    $custom_text[$lang['id_lang']] = Tools::getValue('BANK_WIRE_MULTI_CUSTOM_TEXT_' . $lang['id_lang']);
                }
            }
            Configuration::updateValue('BANK_WIRE_MULTI_RESERVATION_DAYS', (int) Tools::getValue('BANK_WIRE_MULTI_RESERVATION_DAYS'));
            Configuration::updateValue('BANK_WIRE_MULTI_CUSTOM_TEXT', $custom_text);
        }
        $this->_html .= $this->displayConfirmation('Configuración actualizada');
    }

    protected function _displayInfo()
    {
        return $this->display(__FILE__, 'views/templates/admin/info.tpl');
    }

    public function getContent()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/admin.css');

        if (Tools::isSubmit('btnSubmit')) {
            $this->_postValidation();
            if (!count($this->_postErrors)) {
                $this->_postProcess();
            } else {
                foreach ($this->_postErrors as $err) {
                    $this->_html .= $this->displayError($err);
                }
            }
        } else {
            $this->_html .= '<br />';
        }

        $this->_html .= $this->renderInfo();
        $this->_html .= $this->renderForm();

        return $this->_html;
    }

    public function renderInfo()
    {
        $accounts = BankAccount::getAllAccounts();
        $this->context->smarty->assign([
            'module_dir' => $this->_path,
            'accounts_count' => count($accounts),
            'link_manage' => $this->context->link->getAdminLink('AdminBankAccounts'),
        ]);

        return $this->display(__FILE__, 'views/templates/admin/info.tpl');
    }

    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/front.css');
    }

    public function hookPaymentOptions($params)
    {
        if (!$this->active) {
            return [];
        }

        if (!$this->checkCurrency($params['cart'])) {
            return [];
        }

        $accounts = BankAccount::getActiveAccounts();
        if (!count($accounts)) {
            return [];
        }

        $this->smarty->assign(
            $this->getTemplateVarInfos()
        );

        $accountCount = count($accounts);
        $paymentText = 'Pagar por transferencia bancaria' . ($accountCount > 1 ? ' - Elija entre ' . $accountCount . ' bancos' : '');

        $newOption = new PaymentOption();
        $newOption->setModuleName($this->name)
                ->setCallToActionText($paymentText)
                ->setForm($this->fetch('module:pswirepaymentmulti/views/templates/hook/payment_form.tpl'))
                ->setAdditionalInformation($this->fetch('module:pswirepaymentmulti/views/templates/hook/ps_wirepayment_intro.tpl'));

        return [
            $newOption,
        ];
    }

    public function hookDisplayPaymentReturn($params)
    {
        if (!$this->active || !Configuration::get(self::FLAG_DISPLAY_PAYMENT_INVITE)) {
            return;
        }

        // Get the selected bank ID from order messages
        $selectedBankId = null;
        $messages = Db::getInstance()->executeS('
            SELECT message
            FROM ' . _DB_PREFIX_ . 'message
            WHERE id_order = ' . (int) $params['order']->id . '
            AND message LIKE "BANK_ID:%"
            ORDER BY date_add DESC
            LIMIT 1
        ');

        if (!empty($messages)) {
            $message = $messages[0]['message'];
            if (preg_match('/BANK_ID:(\d+)/', $message, $matches)) {
                $selectedBankId = (int) $matches[1];
            }
        }

        $moduleUrl = $this->context->link->getBaseLink() . 'modules/' . $this->name . '/';

        // Get only the selected bank account
        if ($selectedBankId) {
            $selectedBank = new BankAccount($selectedBankId);
            if (!Validate::isLoadedObject($selectedBank)) {
                return;
            }

            $account = [
                'bank_name' => $selectedBank->bank_name,
                'bank_logo' => $selectedBank->bank_logo ? $moduleUrl . 'views/img/banks/' . $selectedBank->bank_logo : null,
                'owner' => $selectedBank->owner,
                'details' => Tools::nl2br($selectedBank->details),
                'address' => Tools::nl2br($selectedBank->address),
            ];
        } else {
            // Fallback: if no bank selected, show the first active one
            $accounts = BankAccount::getActiveAccounts();
            if (empty($accounts)) {
                return;
            }
            $firstAccount = $accounts[0];
            $account = [
                'bank_name' => $firstAccount['bank_name'],
                'bank_logo' => $firstAccount['bank_logo'] ? $moduleUrl . 'views/img/banks/' . $firstAccount['bank_logo'] : null,
                'owner' => $firstAccount['owner'],
                'details' => Tools::nl2br($firstAccount['details']),
                'address' => Tools::nl2br($firstAccount['address']),
            ];
        }

        $totalToPaid = $params['order']->getOrdersTotalPaid() - $params['order']->getTotalPaid();

        $this->smarty->assign([
            'shop_name' => $this->context->shop->name,
            'total' => $this->context->getCurrentLocale()->formatPrice(
                $totalToPaid,
                (new Currency($params['order']->id_currency))->iso_code
            ),
            'bankAccount' => $account,
            'status' => 'ok',
            'reference' => $params['order']->reference,
            'contact_url' => $this->context->link->getPageLink('contact', true),
        ]);

        return $this->fetch('module:pswirepaymentmulti/views/templates/hook/payment_return.tpl');
    }

    public function checkCurrency($cart)
    {
        $currency_order = new Currency($cart->id_currency);
        $currencies_module = $this->getCurrency($cart->id_currency);

        if (is_array($currencies_module)) {
            foreach ($currencies_module as $currency_module) {
                if ($currency_order->id == $currency_module['id_currency']) {
                    return true;
                }
            }
        }

        return false;
    }

    public function renderForm()
    {
        $fields_form_customization = [
            'form' => [
                'legend' => [
                    'title' => 'Personalización',
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => 'Período de reserva',
                        'desc' => 'Número de días que los artículos permanecen reservados',
                        'name' => 'BANK_WIRE_MULTI_RESERVATION_DAYS',
                    ],
                    [
                        'type' => 'textarea',
                        'label' => 'Información para el cliente',
                        'name' => 'BANK_WIRE_MULTI_CUSTOM_TEXT',
                        'desc' => 'Información sobre la transferencia bancaria (tiempo de procesamiento, inicio del envío...)',
                        'lang' => true,
                    ],
                    [
                        'type' => 'switch',
                        'label' => 'Mostrar la invitación de pago en la página de confirmación de pedido',
                        'name' => self::FLAG_DISPLAY_PAYMENT_INVITE,
                        'is_bool' => true,
                        'hint' => 'La legislación de tu país puede requerir que envíes la invitación de pago solo por correo electrónico. Desactivar esta opción ocultará la invitación en la página de confirmación.',
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => true,
                                'label' => 'Sí',
                            ],
                            [
                                'id' => 'active_off',
                                'value' => false,
                                'label' => 'No',
                            ],
                        ],
                    ],
                ],
                'submit' => [
                    'title' => 'Guardar',
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ?: 0;
        $helper->id = (int) Tools::getValue('id_carrier');
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'btnSubmit';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure='
            . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$fields_form_customization]);
    }

    public function getConfigFieldsValues()
    {
        $custom_text = [];
        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $custom_text[$lang['id_lang']] = Tools::getValue(
                'BANK_WIRE_MULTI_CUSTOM_TEXT_' . $lang['id_lang'],
                Configuration::get('BANK_WIRE_MULTI_CUSTOM_TEXT', $lang['id_lang'])
            );
        }

        return [
            'BANK_WIRE_MULTI_RESERVATION_DAYS' => Tools::getValue('BANK_WIRE_MULTI_RESERVATION_DAYS', $this->reservation_days),
            'BANK_WIRE_MULTI_CUSTOM_TEXT' => $custom_text,
            self::FLAG_DISPLAY_PAYMENT_INVITE => Tools::getValue(
                self::FLAG_DISPLAY_PAYMENT_INVITE,
                Configuration::get(self::FLAG_DISPLAY_PAYMENT_INVITE)
            ),
        ];
    }

    public function getTemplateVarInfos()
    {
        $cart = $this->context->cart;
        $total = $this->context->getCurrentLocale()->formatPrice($cart->getOrderTotal(true, Cart::BOTH), $this->context->currency->iso_code) . ' (IVA incl.)';

        $accounts = BankAccount::getActiveAccounts();
        $bankAccounts = [];

        $moduleUrl = $this->context->link->getBaseLink() . 'modules/' . $this->name . '/';

        foreach ($accounts as $account) {
            $bankAccounts[] = [
                'id' => $account['id_bank_account'],
                'bank_name' => $account['bank_name'],
                'bank_logo' => $account['bank_logo'] ? $moduleUrl . 'views/img/banks/' . $account['bank_logo'] : null,
                'owner' => $account['owner'],
                'details' => Tools::nl2br($account['details']),
                'address' => Tools::nl2br($account['address']),
            ];
        }

        $bankwireReservationDays = $this->reservation_days;
        if (false === $bankwireReservationDays) {
            $bankwireReservationDays = 7;
        }

        $bankwireCustomText = Tools::nl2br(Configuration::get('BANK_WIRE_MULTI_CUSTOM_TEXT', $this->context->language->id));
        if (empty($bankwireCustomText)) {
            $bankwireCustomText = '';
        }

        return [
            'total' => $total,
            'bankAccounts' => $bankAccounts,
            'bankwireReservationDays' => (int) $bankwireReservationDays,
            'bankwireCustomText' => $bankwireCustomText,
        ];
    }

    /**
     * Hook to block native PrestaShop emails for this payment module
     * We send our own custom email with bank details
     */
    public function hookActionEmailSendBefore($params)
    {
        // Si el correo es sobre un pedido, verificar si es de nuestro módulo
        if (isset($params['template']) && isset($params['templateVars']['{order_name}'])) {
            // Obtener el pedido
            $orderRef = $params['templateVars']['{order_name}'];
            $result = Db::getInstance()->getRow('
                SELECT id_order, module
                FROM ' . _DB_PREFIX_ . 'orders
                WHERE reference = "' . pSQL($orderRef) . '"
                LIMIT 1
            ');

            if ($result && $result['module'] == 'pswirepaymentmulti') {
                // Bloquear el correo nativo de PrestaShop
                // Nosotros enviamos nuestro propio correo personalizado desde validation.php
                return false;
            }
        }

        return true;
    }

    public function hookDisplayAdminOrderSide($params)
    {
        $orderId = (int) $params['id_order'];

        // Obtener el banco seleccionado
        $messages = Db::getInstance()->executeS('
            SELECT message
            FROM ' . _DB_PREFIX_ . 'message
            WHERE id_order = ' . $orderId . '
            AND message LIKE "BANK_ID:%"
            ORDER BY date_add DESC
            LIMIT 1
        ');

        if (empty($messages)) {
            return '';
        }

        $selectedBankId = null;
        if (preg_match('/BANK_ID:(\d+)/', $messages[0]['message'], $matches)) {
            $selectedBankId = (int) $matches[1];
        }

        if (!$selectedBankId) {
            return '';
        }

        $selectedBank = new BankAccount($selectedBankId);
        if (!Validate::isLoadedObject($selectedBank)) {
            return '';
        }

        // Extraer últimos 4 dígitos del IBAN
        $ibanLast4 = '';
        if (preg_match('/([A-Z]{2}\s?\d{2}[\s\d]+)/', $selectedBank->details, $matches)) {
            $iban = preg_replace('/\s+/', '', $matches[1]);
            $ibanLast4 = substr($iban, -4);
        }

        $moduleUrl = $this->context->link->getBaseLink() . 'modules/' . $this->name . '/';

        $this->context->smarty->assign([
            'bank_name' => $selectedBank->bank_name,
            'bank_logo' => $selectedBank->bank_logo ? $moduleUrl . 'views/img/banks/' . $selectedBank->bank_logo : null,
            'bank_owner' => $selectedBank->owner,
            'bank_iban_last4' => $ibanLast4,
            'bank_details' => $selectedBank->details,
        ]);

        return $this->display(__FILE__, 'views/templates/hook/admin_order_top.tpl');
    }
}

