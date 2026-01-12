{**
 * 2007-2025 PrestaShop and Contributors
 *}

<section class="ps-wirepayment-multi-intro">
  <p>
    Por favor, selecciona una de nuestras cuentas bancarias para transferir el importe de la factura.
  </p>

  {if $bankAccounts}
    <div class="bank-accounts-selector">
      <h4>Elija una cuenta bancaria:</h4>

      <div class="bank-accounts-list">
        {foreach from=$bankAccounts item=account}
          <div class="bank-account-option">
            <label class="bank-account-card {if $account@first}selected{/if}" for="bank_{$account.id}">
              <input type="radio" name="selected_bank_account" id="bank_{$account.id}" value="{$account.id}" {if $account@first}checked="checked"{/if} class="bank-radio">
              <div class="bank-card-content">
                {if $account.bank_logo}
                  <div class="bank-logo">
                    <img src="{$account.bank_logo|escape:'htmlall':'UTF-8'}" alt="{$account.bank_name|escape:'htmlall':'UTF-8'}">
                  </div>
                {/if}
                <div class="bank-info">
                  <strong>{$account.bank_name|escape:'htmlall':'UTF-8'}</strong>
                  <p>Titular: {$account.owner|escape:'htmlall':'UTF-8'}</p>
                </div>
              </div>
            </label>
          </div>
        {/foreach}
      </div>

      <p class="bank-details-notice">
        <button type="button" class="btn btn-link" data-toggle="modal" data-target="#bankwire-modal-multi">
          Ver detalles de la cuenta bancaria seleccionada
        </button>
      </p>
    </div>
  {/if}

  {* Modal para mostrar detalles del banco seleccionado *}
  <div class="modal fade" id="bankwire-modal-multi" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Pago por Transferencia Bancaria - Detalles de la Cuenta</h4>
        </div>
        <div class="modal-body" id="modal-bank-content">
          {if $bankAccounts}
            {foreach from=$bankAccounts item=account}
              <div class="bank-account-detail bank-detail-{$account.id}" style="display:none;">
                {if $account.bank_logo}
                  <div class="bank-logo-large text-center">
                    <img src="{$account.bank_logo|escape:'htmlall':'UTF-8'}"
                         alt="{$account.bank_name|escape:'htmlall':'UTF-8'}"
                         style="max-width: 200px; max-height: 100px;">
                  </div>
                {/if}
                <h3 class="text-center">{$account.bank_name|escape:'htmlall':'UTF-8'}</h3>

                <div class="bank-details-info">
                  <p><strong>Importe a transferir:</strong> {$total}</p>
                  <p><strong>Titular de la cuenta:</strong> {$account.owner|escape:'htmlall':'UTF-8'}</p>
                  <p><strong>Detalles de la cuenta:</strong><br>{$account.details nofilter}</p>
                  <p><strong>Dirección del banco:</strong><br>{$account.address nofilter}</p>
                </div>
              </div>
            {/foreach}
          {/if}
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
jQuery(document).ready(function($) {
  console.log('[Wire Payment Multi] Iniciando...');

  // Función para actualizar el modal con el banco seleccionado
  function updateModalContent() {
    var selectedId = $('input[name="selected_bank_account"]:checked').val();
    console.log('[Wire Payment Multi] Actualizando modal para banco:', selectedId);
    $('.bank-account-detail').hide();
    if (selectedId) {
      $('.bank-detail-' + selectedId).show();
      console.log('[Wire Payment Multi] Modal actualizado');
    }
  }

  // Actualizar clases visuales cuando cambia la selección
  $('input[name="selected_bank_account"]').on('change', function() {
    var selectedBankId = $(this).val();
    console.log('[Wire Payment Multi] ===== BANCO SELECCIONADO:', selectedBankId, '=====');

    $('.bank-account-card').removeClass('selected');
    $(this).closest('.bank-account-card').addClass('selected');

    // BUSCAR Y ACTUALIZAR TODOS LOS CAMPOS OCULTOS POSIBLES
    var updated = false;

    // Buscar por selector completo
    $('input[name="selected_bank_account"]').each(function() {
      if ($(this).attr('type') === 'hidden') {
        $(this).val(selectedBankId);
        console.log('[Wire Payment Multi] ✓ Campo oculto actualizado:', $(this).val());
        updated = true;
      }
    });

    // Si no encontró ninguno, buscar en el formulario de pago
    if (!updated) {
      var $paymentForm = $('#payment-confirmation button[type="submit"]').closest('form');
      if ($paymentForm.length > 0) {
        // Buscar campo existente
        var $hidden = $paymentForm.find('input[name="selected_bank_account"]');
        if ($hidden.length > 0) {
          $hidden.val(selectedBankId);
          console.log('[Wire Payment Multi] ✓ Campo en form encontrado y actualizado');
        } else {
          // Crear campo si no existe
          $paymentForm.append('<input type="hidden" name="selected_bank_account" value="' + selectedBankId + '">');
          console.log('[Wire Payment Multi] ✓ Campo creado en formulario');
        }
        updated = true;
      }
    }

    if (!updated) {
      console.error('[Wire Payment Multi] ✗ NO SE PUDO ACTUALIZAR NINGÚN CAMPO OCULTO');
    }

    // Actualizar el modal
    updateModalContent();
  });

  // Cuando se abre el modal
  $('#bankwire-modal-multi').on('show.bs.modal', function() {
    updateModalContent();
  });

  // Inicializar
  updateModalContent();

  // Forzar inicialización del campo oculto con el primer banco
  setTimeout(function() {
    var firstBank = $('input[name="selected_bank_account"]:checked').val();
    if (firstBank) {
      console.log('[Wire Payment Multi] Inicializando con banco:', firstBank);
      $('input[name="selected_bank_account"]:checked').trigger('change');
    }
  }, 500);

  console.log('[Wire Payment Multi] ✓ Script cargado');
});
</script>

<style>
  .bank-accounts-selector { margin: 20px 0; }
  .bank-accounts-list { display: flex; flex-direction: column; gap: 10px; margin: 15px 0; }
  .bank-account-option { width: 100%; }
  .bank-account-card { display: flex; align-items: center; padding: 15px; border: 2px solid #ddd; border-radius: 8px; cursor: pointer; transition: all 0.3s ease; width: 100%; margin: 0; }
  .bank-account-card:hover { border-color: #25b9d7; background-color: #f8f9fa; }
  .bank-account-card.selected { border-color: #25b9d7 !important; background-color: #e3f2fd !important; }
  .bank-card-content { display: flex; align-items: center; gap: 15px; flex: 1; margin-left: 10px; }
  .bank-logo img { max-width: 100px; max-height: 50px; object-fit: contain; }
  .bank-logo-large img { max-width: 200px; max-height: 100px; object-fit: contain; margin-bottom: 15px; }
  .bank-info { flex: 1; }
  .bank-name { font-size: 1.1em; display: block; margin-bottom: 5px; }
  .bank-owner { margin: 0; color: #666; font-size: 0.9em; }
  .bank-details-notice { margin-top: 15px; text-align: center; }
  .bank-details-info p { margin-bottom: 15px; }
  .bank-radio { width: 20px; height: 20px; margin: 0; cursor: pointer; }
</style>
