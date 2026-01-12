<form method="POST" action="{$link->getModuleLink('pswirepaymentmulti', 'validation', [], true)|escape:'html':'UTF-8'}" id="wirepayment-multi-form">
  {if $bankAccounts}
    <div class="bank-accounts-selector" style="margin: 20px 0;">
      <h4>Elija una cuenta bancaria:</h4>
      <div class="bank-accounts-list" style="display: flex; flex-direction: column; gap: 15px; margin: 15px 0;">
        {foreach from=$bankAccounts item=account}
          <div class="bank-account-wrapper">
            <div style="display: flex; align-items: center; border: 2px solid #ddd; border-radius: 8px 8px 0 0; transition: all 0.3s ease;" class="bank-card-container" data-bank-id="{$account.id}">
              <label class="bank-account-card {if $account@first}selected{/if}" for="bank_form_{$account.id}" style="display: flex; align-items: center; padding: 15px; cursor: pointer; flex: 1; margin: 0;">
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
                </div>
              </label>
              <span class="bank-toggle-details" data-bank-id="{$account.id}" style="padding: 15px; color: #25b9d7; cursor: pointer; white-space: nowrap; display: flex; align-items: center; gap: 5px; user-select: none;">
                <span class="toggle-icon">▼</span> Ver datos
              </span>
            </div>

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
  .bank-card-container:hover { border-color: #25b9d7 !important; background-color: #f8f9fa !important; }
  .bank-account-card.selected { border-color: #25b9d7 !important; background-color: #e3f2fd !important; }
  .bank-card-container.has-selected { border-color: #25b9d7 !important; background-color: #e3f2fd !important; }
  .bank-toggle-details:hover { background-color: #e3f2fd; color: #1a8fa9 !important; }
</style>

<script type="text/javascript">
(function() {
  console.log('[Wire Payment Multi] Script iniciando...');

  // Verificar jQuery
  if (typeof jQuery === 'undefined') {
    console.error('[Wire Payment Multi] jQuery no está disponible!');
    return;
  }

  jQuery(document).ready(function($) {
    console.log('[Wire Payment Multi] jQuery ready, configurando...');

    // Actualizar clases visuales cuando cambia la selección
    $(document).on('change', '#wirepayment-multi-form input[type="radio"][name="selected_bank_account"]', function() {
      var selectedId = $(this).val();
      console.log('[Wire Payment Multi] ===== BANCO SELECCIONADO:', selectedId, '=====');

      // Actualizar clases visuales
      $('.bank-card-container').removeClass('has-selected');
      $(this).closest('.bank-card-container').addClass('has-selected');
    });

    // Toggle detalles de cuenta - Delegación desde document
    $(document).on('click', '.bank-toggle-details', function(e) {
      e.preventDefault();
      e.stopPropagation();

      console.log('[Wire Payment Multi] Click en Ver datos detectado!');

      var $this = $(this);
      var bankId = $this.data('bank-id');
      var detailsDiv = $('#bank-details-' + bankId);

      console.log('[Wire Payment Multi] Bank ID:', bankId);
      console.log('[Wire Payment Multi] Details div found:', detailsDiv.length > 0);

      // Cerrar todos los demás
      $('.bank-details-collapse').not(detailsDiv).slideUp(300);
      $('.bank-toggle-details').not($this).each(function() {
        $(this).html('<span class="toggle-icon">▼</span> Ver datos');
      });

      // Toggle el actual
      var isVisible = detailsDiv.is(':visible');
      console.log('[Wire Payment Multi] ¿Está visible?:', isVisible);

      detailsDiv.slideToggle(300, function() {
        console.log('[Wire Payment Multi] Toggle completado');
      });

      if (isVisible) {
        $this.html('<span class="toggle-icon">▼</span> Ver datos');
      } else {
        $this.html('<span class="toggle-icon">▲</span> Ocultar datos');
      }
    });

    // Inicializar primer banco como seleccionado
    var firstRadio = $('#wirepayment-multi-form input[type="radio"][name="selected_bank_account"]:checked');
    if (firstRadio.length > 0) {
      firstRadio.closest('.bank-card-container').addClass('has-selected');
    }

    console.log('[Wire Payment Multi] ✓ Todo configurado correctamente');
    console.log('[Wire Payment Multi] Botones encontrados:', $('.bank-toggle-details').length);
  });
})();
</script>
