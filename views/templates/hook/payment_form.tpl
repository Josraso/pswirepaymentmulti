<form method="POST" action="{$link->getModuleLink('pswirepaymentmulti', 'validation', [], true)|escape:'html':'UTF-8'}" id="wirepayment-multi-form">
  {if $bankAccounts}
    <div class="bank-accounts-selector" style="margin: 20px 0;">
      <h4>Elija una cuenta bancaria:</h4>
      <div class="bank-accounts-list" style="display: flex; flex-direction: column; gap: 15px; margin: 15px 0;">
        {foreach from=$bankAccounts item=account}
          <div class="bank-account-wrapper">
            <label class="bank-account-card {if $account@first}selected{/if}" for="bank_form_{$account.id}" style="display: flex; align-items: center; padding: 15px; border: 2px solid #ddd; border-radius: 8px; cursor: pointer; transition: all 0.3s ease; margin-bottom: 0;">
              <input type="radio" name="selected_bank_account" id="bank_form_{$account.id}" value="{$account.id}" {if $account@first}checked="checked"{/if} style="width: 20px; height: 20px; margin-right: 10px; cursor: pointer;">
              <div class="bank-card-content" style="display: flex; align-items: center; gap: 15px; flex: 1;">
                {if $account.bank_logo}
                  <div class="bank-logo">
                    <img src="{$account.bank_logo|escape:'htmlall':'UTF-8'}" alt="{$account.bank_name|escape:'htmlall':'UTF-8'}" style="max-width: 100px; max-height: 50px;">
                  </div>
                {/if}
                <div class="bank-info" style="flex: 1;">
                  <strong>{$account.bank_name|escape:'htmlall':'UTF-8'}</strong>
                  <p style="margin: 5px 0 0 0; color: #666; font-size: 0.9em;">Titular: {$account.owner|escape:'htmlall':'UTF-8'}</p>
                </div>
                <button type="button" class="btn btn-link bank-toggle-details" data-bank-id="{$account.id}" style="margin-left: auto; color: #25b9d7;">
                  <i class="icon-chevron-down"></i> Ver datos cuenta
                </button>
              </div>
            </label>

            <div class="bank-details-collapse" id="bank-details-{$account.id}" style="display: none; padding: 15px; background-color: #f8f9fa; border: 2px solid #ddd; border-top: none; border-radius: 0 0 8px 8px;">
              <h5 style="margin-top: 0; color: #333;">Datos completos de la cuenta bancaria</h5>
              <p style="margin: 10px 0;"><strong>Importe a transferir:</strong> {$total}</p>
              <p style="margin: 10px 0;"><strong>Titular de la cuenta:</strong> {$account.owner|escape:'htmlall':'UTF-8'}</p>
              <p style="margin: 10px 0;"><strong>Detalles de la cuenta:</strong><br>{$account.details nofilter}</p>
              <p style="margin: 10px 0;"><strong>Dirección del banco:</strong><br>{$account.address nofilter}</p>
              <div style="margin-top: 15px; padding: 10px; background-color: #fff3cd; border-left: 4px solid #ffc107;">
                <strong>⚠️ IMPORTANTE:</strong> Recuerda indicar tu número de pedido en el concepto de la transferencia.
              </div>
            </div>
          </div>
        {/foreach}
      </div>
    </div>
  {/if}
</form>

<style>
  .bank-account-card:hover { border-color: #25b9d7 !important; background-color: #f8f9fa !important; }
  .bank-account-card.selected { border-color: #25b9d7 !important; background-color: #e3f2fd !important; }
  .bank-details-collapse { animation: slideDown 0.3s ease-out; }
  @keyframes slideDown {
    from { opacity: 0; max-height: 0; }
    to { opacity: 1; max-height: 500px; }
  }
</style>

<script type="text/javascript">
(function() {
  if (typeof jQuery === 'undefined') return;

  jQuery(document).ready(function($) {
    console.log('[Wire Payment Multi Form] Formulario con radios inicializado');

    // Actualizar clases visuales cuando cambia la selección
    $('#wirepayment-multi-form input[type="radio"][name="selected_bank_account"]').on('change', function() {
      var selectedId = $(this).val();
      console.log('[Wire Payment Multi Form] ===== BANCO SELECCIONADO:', selectedId, '=====');

      // Actualizar clases visuales
      $('#wirepayment-multi-form .bank-account-card').removeClass('selected');
      $(this).closest('.bank-account-card').addClass('selected');
    });

    // Toggle detalles de cuenta
    $('.bank-toggle-details').on('click', function(e) {
      e.preventDefault();
      e.stopPropagation();

      var bankId = $(this).data('bank-id');
      var detailsDiv = $('#bank-details-' + bankId);
      var icon = $(this).find('i');

      // Cerrar todos los demás
      $('.bank-details-collapse').not(detailsDiv).slideUp(300);
      $('.bank-toggle-details i').removeClass('icon-chevron-up').addClass('icon-chevron-down');
      $('.bank-toggle-details').each(function() {
        if ($(this).data('bank-id') != bankId) {
          $(this).html('<i class="icon-chevron-down"></i> Ver datos cuenta');
        }
      });

      // Toggle el actual
      detailsDiv.slideToggle(300);

      if (detailsDiv.is(':visible')) {
        $(this).html('<i class="icon-chevron-up"></i> Ocultar datos');
      } else {
        $(this).html('<i class="icon-chevron-down"></i> Ver datos cuenta');
      }
    });

    console.log('[Wire Payment Multi Form] ✓ Radios y desplegables configurados');
  });
})();
</script>
