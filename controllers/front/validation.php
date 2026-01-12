<?php
/**
 * 2007-2020 PrestaShop and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * @since 1.5.0
 *
 * @property Pswirepaymentmulti $module
 */
class PswirepaymentmultiValidationModuleFrontController extends ModuleFrontController
{
    /**
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        $cart = $this->context->cart;
        if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        // Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
        $authorized = false;
        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] == 'pswirepaymentmulti') {
                $authorized = true;
                break;
            }
        }
        if (!$authorized) {
            exit($this->module->getTranslator()->trans('This payment method is not available.', [], 'Modules.Wirepaymentmulti.Shop'));
        }

        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer)) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        require_once _PS_MODULE_DIR_ . 'pswirepaymentmulti/classes/BankAccount.php';

        // DEBUGGING: Ver qué está llegando
        $allParams = Tools::getAllValues();
        error_log('=== WIRE PAYMENT MULTI DEBUG ===');
        error_log('GET params: ' . print_r($_GET, true));
        error_log('POST params: ' . print_r($_POST, true));
        error_log('All Tools::getAllValues(): ' . print_r($allParams, true));

        // Get the selected bank account ID from GET or POST parameters
        $selectedBankId = (int) Tools::getValue('selected_bank_account');

        if (!$selectedBankId) {
            // If no bank selected, try to get the first active one as fallback
            $accounts = BankAccount::getActiveAccounts();
            if (!empty($accounts)) {
                $selectedBankId = (int) $accounts[0]['id_bank_account'];
            } else {
                Tools::redirect('index.php?controller=order&step=1');
            }
        }

        // Get only the selected bank account
        $selectedBank = new BankAccount($selectedBankId);

        if (!Validate::isLoadedObject($selectedBank) || !$selectedBank->active) {
            // If bank is invalid or not active, redirect back
            Tools::redirect('index.php?controller=order&step=1');
        }

        $currency = $this->context->currency;
        $total = (float) $cart->getOrderTotal(true, Cart::BOTH);

        $this->module->validateOrder(
            $cart->id,
            (int) Configuration::get('PS_OS_BANKWIRE'),
            $total,
            $this->module->displayName,
            null,
            [],
            (int) $currency->id,
            false,
            $customer->secure_key
        );

        // Save and send email
        if ($this->module->currentOrder) {
            $orderId = (int) $this->module->currentOrder;

            // Guardar el ID del banco seleccionado
            $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'message`
                    (`id_cart`, `id_order`, `message`, `private`, `date_add`)
                    VALUES
                    (' . (int) $cart->id . ', ' . $orderId . ',
                    "BANK_ID:' . (int) $selectedBankId . '", 1, NOW())';
            Db::getInstance()->execute($sql);

            // Get order for order reference
            $order = new Order($orderId);

            // Build bank details for email
            $bankDetails = '<div style="background-color:#f8f8f8; padding:15px; border-radius:5px; margin:15px 0;">';
            $bankDetails .= '<h3 style="margin-top:0; color:#333;">' . $selectedBank->bank_name . '</h3>';
            $bankDetails .= '<p style="margin:10px 0;"><strong>Titular de la cuenta:</strong><br>' . $selectedBank->owner . '</p>';
            $bankDetails .= '<p style="margin:10px 0;"><strong>Datos de la cuenta:</strong><br>' . nl2br($selectedBank->details) . '</p>';
            $bankDetails .= '<p style="margin:10px 0;"><strong>Dirección bancaria:</strong><br>' . nl2br($selectedBank->address) . '</p>';
            $bankDetails .= '</div>';

            // Email variables
            $templateVars = [
                '{bankwire_accounts}' => $bankDetails,
                '{firstname}' => $customer->firstname,
                '{lastname}' => $customer->lastname,
                '{shop_name}' => Configuration::get('PS_SHOP_NAME'),
                '{shop_url}' => $this->context->link->getPageLink('index', true),
                '{order_name}' => $order->reference,
                '{total_paid}' => Tools::displayPrice($total, $currency),
            ];

            // Send email
            $langId = (int) $this->context->language->id;

            Mail::Send(
                $langId,
                'bankwire',
                Mail::l('Confirmación de pedido - Transferencia bancaria', $langId),
                $templateVars,
                $customer->email,
                $customer->firstname . ' ' . $customer->lastname,
                Configuration::get('PS_SHOP_EMAIL'),
                Configuration::get('PS_SHOP_NAME'),
                null,
                null,
                dirname(__FILE__) . '/../../mails/',
                false,
                null,
                null
            );
        }

        Tools::redirect('index.php?controller=order-confirmation&id_cart=' . $cart->id . '&id_module=' . $this->module->id . '&id_order=' . $this->module->currentOrder . '&key=' . $customer->secure_key);
    }
}
