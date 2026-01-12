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

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Upgrade to version 3.0.0 - Multi bank accounts support
 *
 * @param Pswirepaymentmulti $module
 * @return bool
 */
function upgrade_module_3_0_0($module)
{
    // Create new table for bank accounts
    $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wirepayment_multi_accounts` (
        `id_bank_account` int(11) NOT NULL AUTO_INCREMENT,
        `bank_name` varchar(255) NOT NULL,
        `bank_logo` varchar(255) DEFAULT NULL,
        `owner` varchar(255) NOT NULL,
        `details` text NOT NULL,
        `address` text NOT NULL,
        `active` tinyint(1) NOT NULL DEFAULT 1,
        `position` int(11) NOT NULL DEFAULT 0,
        `date_add` datetime NOT NULL,
        `date_upd` datetime NOT NULL,
        PRIMARY KEY (`id_bank_account`),
        KEY `active` (`active`),
        KEY `position` (`position`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

    if (!Db::getInstance()->execute($sql)) {
        return false;
    }

    // Migrate old single account data if exists
    $oldOwner = Configuration::get('BANK_WIRE_OWNER');
    $oldDetails = Configuration::get('BANK_WIRE_DETAILS');
    $oldAddress = Configuration::get('BANK_WIRE_ADDRESS');

    if ($oldOwner && $oldDetails && $oldAddress) {
        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'wirepayment_multi_accounts`
                (`bank_name`, `owner`, `details`, `address`, `active`, `position`, `date_add`, `date_upd`)
                VALUES
                ("Default Bank Account", "' . pSQL($oldOwner) . '", "' . pSQL($oldDetails) . '", "' . pSQL($oldAddress) . '", 1, 0, NOW(), NOW())';

        if (!Db::getInstance()->execute($sql)) {
            return false;
        }
    }

    // Update configuration keys
    Configuration::updateValue('BANK_WIRE_MULTI_PAYMENT_INVITE', Configuration::get('BANK_WIRE_PAYMENT_INVITE'));
    Configuration::updateValue('BANK_WIRE_MULTI_RESERVATION_DAYS', Configuration::get('BANK_WIRE_RESERVATION_DAYS') ?: 7);
    Configuration::updateValue('BANK_WIRE_MULTI_CUSTOM_TEXT', Configuration::get('BANK_WIRE_CUSTOM_TEXT'));

    // Register new hooks
    $module->registerHook('displayHeader');

    // Install admin tab
    $tab = new Tab();
    $tab->active = 1;
    $tab->class_name = 'AdminBankAccounts';
    $tab->name = [];
    foreach (Language::getLanguages(true) as $lang) {
        $tab->name[$lang['id_lang']] = 'Bank Accounts';
    }
    $tab->id_parent = (int) Tab::getIdFromClassName('AdminParentPayment');
    $tab->module = $module->name;
    $tab->add();

    return true;
}
