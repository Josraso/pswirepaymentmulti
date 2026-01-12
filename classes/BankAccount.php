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

class BankAccount extends ObjectModel
{
    /** @var int */
    public $id_bank_account;

    /** @var string */
    public $bank_name;

    /** @var string */
    public $bank_logo;

    /** @var string */
    public $owner;

    /** @var string */
    public $details;

    /** @var string */
    public $address;

    /** @var bool */
    public $active;

    /** @var int */
    public $position;

    /** @var string */
    public $date_add;

    /** @var string */
    public $date_upd;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'wirepayment_multi_accounts',
        'primary' => 'id_bank_account',
        'fields' => [
            'bank_name' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 255],
            'bank_logo' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255],
            'owner' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 255],
            'details' => ['type' => self::TYPE_HTML, 'validate' => 'isCleanHtml', 'required' => true],
            'address' => ['type' => self::TYPE_HTML, 'validate' => 'isCleanHtml', 'required' => true],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'position' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
    ];

    /**
     * Get all active bank accounts ordered by position
     *
     * @return array
     */
    public static function getActiveAccounts()
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('wirepayment_multi_accounts');
        $sql->where('active = 1');
        $sql->orderBy('position ASC, id_bank_account ASC');

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Get all bank accounts ordered by position
     *
     * @return array
     */
    public static function getAllAccounts()
    {
        $sql = new DbQuery();
        $sql->select('*');
        $sql->from('wirepayment_multi_accounts');
        $sql->orderBy('position ASC, id_bank_account ASC');

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Get the highest position value
     *
     * @return int
     */
    public static function getHighestPosition()
    {
        $sql = new DbQuery();
        $sql->select('MAX(position)');
        $sql->from('wirepayment_multi_accounts');

        $result = Db::getInstance()->getValue($sql);

        return $result ? (int) $result : 0;
    }

    /**
     * Update positions
     *
     * @param array $positions
     * @return bool
     */
    public static function updatePositions($positions)
    {
        foreach ($positions as $id => $position) {
            Db::getInstance()->update(
                'wirepayment_multi_accounts',
                ['position' => (int) $position],
                'id_bank_account = ' . (int) $id
            );
        }

        return true;
    }

    /**
     * Delete logo file
     *
     * @return bool
     */
    public function deleteLogo()
    {
        if ($this->bank_logo && file_exists(_PS_MODULE_DIR_ . 'pswirepaymentmulti/views/img/banks/' . $this->bank_logo)) {
            return @unlink(_PS_MODULE_DIR_ . 'pswirepaymentmulti/views/img/banks/' . $this->bank_logo);
        }

        return true;
    }

    /**
     * Delete account and its logo
     *
     * @return bool
     */
    public function delete()
    {
        $this->deleteLogo();

        return parent::delete();
    }

    /**
     * Reordenar todas las posiciones consecutivamente (1, 2, 3, 4...)
     *
     * @return bool
     */
    public static function reorderPositions()
    {
        $accounts = self::getAllAccounts();
        $position = 1;

        foreach ($accounts as $account) {
            Db::getInstance()->update(
                'wirepayment_multi_accounts',
                ['position' => $position],
                'id_bank_account = ' . (int) $account['id_bank_account']
            );
            $position++;
        }

        return true;
    }

    /**
     * Add bank account
     *
     * @return bool
     */
    public function add($autodate = true, $nullValues = false)
    {
        // Si no tiene posición, asignarle la siguiente disponible
        if (!$this->position || $this->position == 0) {
            $this->position = self::getHighestPosition() + 1;
        }

        return parent::add($autodate, $nullValues);
    }
}
