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
  console.log('[Wire Payment Multi] Script iniciando (JavaScript vanilla)...');

  // Función principal que se ejecuta cuando el DOM está listo
  function initBankToggle() {
    console.log('[Wire Payment Multi] Inicializando...');

    // Actualizar clases visuales cuando cambia la selección de radio
    var radios = document.querySelectorAll('#wirepayment-multi-form input[type="radio"][name="selected_bank_account"]');
    radios.forEach(function(radio) {
      radio.addEventListener('change', function() {
        var selectedId = this.value;
        console.log('[Wire Payment Multi] ===== BANCO SELECCIONADO:', selectedId, '=====');

        // Actualizar clases visuales
        var containers = document.querySelectorAll('.bank-card-container');
        containers.forEach(function(container) {
          container.classList.remove('has-selected');
        });

        var selectedContainer = this.closest('.bank-card-container');
        if (selectedContainer) {
          selectedContainer.classList.add('has-selected');
        }
      });
    });

    // Toggle detalles de cuenta
    var toggleButtons = document.querySelectorAll('.bank-toggle-details');
    console.log('[Wire Payment Multi] Botones encontrados:', toggleButtons.length);

    toggleButtons.forEach(function(button) {
      button.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        console.log('[Wire Payment Multi] Click en Ver datos detectado!');

        var bankId = this.getAttribute('data-bank-id');
        var detailsDiv = document.getElementById('bank-details-' + bankId);

        console.log('[Wire Payment Multi] Bank ID:', bankId);
        console.log('[Wire Payment Multi] Details div found:', detailsDiv !== null);

        if (!detailsDiv) {
          console.error('[Wire Payment Multi] No se encontró el div de detalles!');
          return;
        }

        // Cerrar todos los demás
        var allDetails = document.querySelectorAll('.bank-details-collapse');
        allDetails.forEach(function(detail) {
          if (detail !== detailsDiv && detail.style.display === 'block') {
            detail.style.display = 'none';
          }
        });

        // Resetear todos los demás botones
        toggleButtons.forEach(function(btn) {
          if (btn !== button) {
            btn.innerHTML = '<span class="toggle-icon">▼</span> Ver datos';
          }
        });

        // Toggle el actual
        var isVisible = detailsDiv.style.display === 'block';
        console.log('[Wire Payment Multi] ¿Está visible?:', isVisible);

        if (isVisible) {
          detailsDiv.style.display = 'none';
          this.innerHTML = '<span class="toggle-icon">▼</span> Ver datos';
          console.log('[Wire Payment Multi] Ocultando detalles');
        } else {
          detailsDiv.style.display = 'block';
          this.innerHTML = '<span class="toggle-icon">▲</span> Ocultar datos';
          console.log('[Wire Payment Multi] Mostrando detalles');
        }
      });
    });

    // Inicializar primer banco como seleccionado
    var firstRadio = document.querySelector('#wirepayment-multi-form input[type="radio"][name="selected_bank_account"]:checked');
    if (firstRadio) {
      var container = firstRadio.closest('.bank-card-container');
      if (container) {
        container.classList.add('has-selected');
      }
    }

    console.log('[Wire Payment Multi] ✓ Todo configurado correctamente (sin jQuery)');
  }

  // Ejecutar cuando el DOM esté listo
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initBankToggle);
  } else {
    // Ya está listo, ejecutar inmediatamente
    initBankToggle();
  }
})();
</script>
