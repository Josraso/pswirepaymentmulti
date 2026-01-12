{**
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
 *}

<section class="ps-wirepayment-multi-return">
  <p>
    Tu pedido en {$shop_name} está completo.<br/>
    Por favor, envía una transferencia bancaria a la siguiente cuenta:
  </p>

  {if $bankAccount}
    <div class="bank-account-detail-box">
      {if $bankAccount.bank_logo}
        <div class="bank-logo">
          <img src="{$bankAccount.bank_logo|escape:'htmlall':'UTF-8'}"
               alt="{$bankAccount.bank_name|escape:'htmlall':'UTF-8'}"
               onerror="this.style.display='none'">
        </div>
      {/if}
      <h3>{$bankAccount.bank_name|escape:'htmlall':'UTF-8'}</h3>

      <div class="bank-wire-details">
        <p><strong>Importe:</strong> {$total}</p>
        <p><strong>Titular de la cuenta:</strong> {$bankAccount.owner|escape:'htmlall':'UTF-8'}</p>
        <p><strong>Detalles de la cuenta:</strong><br>{$bankAccount.details nofilter}</p>
        <p><strong>Dirección del banco:</strong><br>{$bankAccount.address nofilter}</p>
      </div>
    </div>
  {/if}

  <p>
    Por favor, especifica tu referencia de pedido {$reference} en la descripción de la transferencia.<br/>
    También te hemos enviado esta información por correo electrónico.
  </p>
  <strong>Tu pedido se enviará tan pronto como recibamos el pago.</strong>
  <p>
    Si tienes preguntas, comentarios o inquietudes, por favor contacta con nuestro <a href='{$contact_url}'>equipo de atención al cliente</a>.
  </p>
</section>

<style>
  .ps-wirepayment-multi-return {
    padding: 20px 0;
  }

  .bank-account-detail-box {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #dee2e6;
    margin: 20px 0;
  }

  .bank-account-detail-box .bank-logo {
    text-align: center;
    margin-bottom: 15px;
  }

  .bank-account-detail-box .bank-logo img {
    max-width: 150px;
    max-height: 75px;
    object-fit: contain;
  }

  .bank-account-detail-box h3 {
    font-size: 1.3em;
    margin-bottom: 15px;
    color: #333;
    text-align: center;
  }

  .bank-wire-details p {
    margin-bottom: 10px;
    line-height: 1.6;
  }

  .bank-wire-details strong {
    color: #555;
  }
</style>
