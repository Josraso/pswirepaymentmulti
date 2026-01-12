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

require_once _PS_MODULE_DIR_ . 'pswirepaymentmulti/classes/BankAccount.php';

class AdminBankAccountsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'wirepayment_multi_accounts';
        $this->className = 'BankAccount';
        $this->identifier = 'id_bank_account';
        $this->lang = false;

        parent::__construct();

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->bulk_actions = [
            'delete' => [
                'text' => 'Eliminar seleccionados',
                'icon' => 'icon-trash',
                'confirm' => '¿Eliminar elementos seleccionados?',
            ],
        ];

        $this->fields_list = [
            'id_bank_account' => [
                'title' => 'ID',
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'bank_logo' => [
                'title' => 'Logo',
                'align' => 'center',
                'image' => 'banks',
                'orderby' => false,
                'search' => false,
                'callback' => 'displayLogo',
            ],
            'bank_name' => [
                'title' => 'Nombre del Banco',
                'filter_key' => 'a!bank_name',
            ],
            'owner' => [
                'title' => 'Titular de la Cuenta',
                'filter_key' => 'a!owner',
            ],
            'active' => [
                'title' => 'Activo',
                'active' => 'status',
                'type' => 'bool',
                'align' => 'center',
                'class' => 'fixed-width-sm',
                'orderby' => false,
            ],
            'position' => [
                'title' => 'Posición',
                'filter_key' => 'a!position',
                'position' => 'position',
                'align' => 'center',
            ],
        ];
    }

    public function renderList()
    {
        // ARREGLO AUTOMÁTICO: Reordenar posiciones cada vez que se muestra la lista
        require_once _PS_MODULE_DIR_ . 'pswirepaymentmulti/classes/BankAccount.php';
        BankAccount::reorderPositions();

        $this->addRowActionSkipList('delete', []);

        return parent::renderList();
    }

    public function renderForm()
    {
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $image = null;
        if (isset($obj->bank_logo) && $obj->bank_logo) {
            $image = __PS_BASE_URI__ . 'modules/pswirepaymentmulti/views/img/banks/' . $obj->bank_logo;
            $image_size = @getimagesize(_PS_MODULE_DIR_ . 'pswirepaymentmulti/views/img/banks/' . $obj->bank_logo);
        }

        $this->fields_form = [
            'legend' => [
                'title' => 'Cuenta Bancaria',
                'icon' => 'icon-bank',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => 'Nombre del Banco',
                    'name' => 'bank_name',
                    'required' => true,
                    'hint' => 'Nombre del banco (ej., "Santander", "BBVA", etc.)',
                ],
                [
                    'type' => 'file',
                    'label' => 'Logo del Banco',
                    'name' => 'bank_logo',
                    'image' => $image,
                    'size' => isset($image_size[3]) ? $image_size[3] : null,
                    'hint' => 'Sube el logo del banco (recomendado: 200x100px, PNG o JPG)',
                ],
                [
                    'type' => 'text',
                    'label' => 'Titular de la Cuenta',
                    'name' => 'owner',
                    'required' => true,
                    'hint' => 'Nombre del titular de la cuenta',
                ],
                [
                    'type' => 'textarea',
                    'label' => 'Detalles de la Cuenta',
                    'name' => 'details',
                    'rows' => 4,
                    'cols' => 60,
                    'required' => true,
                    'hint' => 'Detalles de la cuenta bancaria: IBAN, BIC/SWIFT, número de cuenta, etc.',
                    'desc' => 'Ejemplo: IBAN: ES12 1234 5678 9012 3456 7890 | BIC: SABADELL',
                ],
                [
                    'type' => 'textarea',
                    'label' => 'Dirección del Banco',
                    'name' => 'address',
                    'rows' => 3,
                    'cols' => 60,
                    'required' => true,
                    'hint' => 'Dirección completa de la sucursal bancaria',
                ],
                [
                    'type' => 'switch',
                    'label' => 'Activo',
                    'name' => 'active',
                    'required' => false,
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => 'Sí',
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => 'No',
                        ],
                    ],
                ],
            ],
            'submit' => [
                'title' => 'Guardar',
            ],
        ];

        return parent::renderForm();
    }

    public function postProcess()
    {
        // Handle logo upload BEFORE parent processing
        if (Tools::isSubmit('submitAdd' . $this->table)) {
            $id = (int) Tools::getValue('id_bank_account');

            // Handle logo upload before the object is saved
            if (isset($_FILES['bank_logo']) && $_FILES['bank_logo']['tmp_name'] && $_FILES['bank_logo']['size'] > 0) {
                $uploadedFilename = $this->processLogoUpload();
                if ($uploadedFilename) {
                    $_POST['bank_logo'] = $uploadedFilename;
                }
            }

            // Set position for new accounts
            if (!$id) {
                $_POST['position'] = BankAccount::getHighestPosition() + 1;
            }
        }

        return parent::postProcess();
    }

    protected function processLogoUpload()
    {
        $uploadDir = _PS_MODULE_DIR_ . 'pswirepaymentmulti/views/img/banks/';

        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                $this->errors[] = 'No se puede crear el directorio de subida';
                return false;
            }
        }

        $file = $_FILES['bank_logo'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions)) {
            $this->errors[] = 'Formato de archivo no válido. Formatos permitidos: JPG, PNG, GIF';
            return false;
        }

        // Check file size (max 2MB)
        if ($file['size'] > 2097152) {
            $this->errors[] = 'El archivo es demasiado grande. Tamaño máximo: 2MB';
            return false;
        }

        // Delete old logo if exists
        $id = (int) Tools::getValue('id_bank_account');
        if ($id) {
            $oldObject = new BankAccount($id);
            if ($oldObject->bank_logo && file_exists($uploadDir . $oldObject->bank_logo)) {
                @unlink($uploadDir . $oldObject->bank_logo);
            }
        }

        // Generate unique filename
        $filename = uniqid('bank_') . '.' . $extension;
        $destination = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Set proper permissions
            @chmod($destination, 0644);
            return $filename;
        } else {
            $this->errors[] = 'Error al subir el archivo';
            return false;
        }
    }

    public function displayLogo($value, $row)
    {
        if (!$value) {
            return '<span class="label">-</span>';
        }

        // Use media server path
        $imagePath = __PS_BASE_URI__ . 'modules/pswirepaymentmulti/views/img/banks/' . $value;

        return '<img src="' . $imagePath . '" alt="Bank Logo" style="max-width: 100px; max-height: 50px;" onerror="this.src=\'' . __PS_BASE_URI__ . 'img/admin/unknown.gif\'" />';
    }

    public function ajaxProcessUpdatePositions()
    {
        $way = (int) Tools::getValue('way');
        $id = (int) Tools::getValue('id');
        $positions = Tools::getValue($this->table);

        if (is_array($positions)) {
            foreach ($positions as $position => $value) {
                $pos = explode('_', $value);
                if (isset($pos[2])) {
                    BankAccount::updatePositions([$pos[2] => $position]);
                }
            }
        }

        die(true);
    }
}
