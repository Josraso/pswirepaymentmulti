{**
 * 2007-2025 PrestaShop and Contributors
 *}

<section class="ps-wirepayment-multi-intro">
  <p style="margin-bottom: 15px;">
    <strong>Por favor, selecciona una cuenta bancaria arriba para continuar con la transferencia.</strong>
  </p>

  {if $bankAccounts}
    <p class="bank-details-notice" style="text-align: center; margin: 15px 0;">
      <button type="button" class="btn btn-link" data-toggle="modal" data-target="#bankwire-modal-multi">
        Ver detalles completos de la cuenta bancaria seleccionada
      </button>
    </p>

    {* Modal para mostrar detalles del banco seleccionado *}
    <div class="modal fade" id="bankwire-modal-multi" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Pago por Transferencia Bancaria - Detalles de la Cuenta</h4>
          </div>
          <div class="modal-body" id="modal-bank-content">
            {foreach from=$bankAccounts item=account}
              <div class="bank-account-detail bank-detail-{$account.id}" style="display:none;">
                {if $account.bank_logo}
                  <div class="bank-logo-large text-center">
                    <img src="{$account.bank_logo|escape:'htmlall':'UTF-8'}"
                         alt="{$account.bank_name|escape:'htmlall':'UTF-8'}"
                         style="max-width: 200px; max-height: 100px; margin-bottom: 15px;">
                  </div>
                {/if}
                <h3 class="text-center">{$account.bank_name|escape:'htmlall':'UTF-8'}</h3>

                <div class="bank-details-info" style="margin-top: 20px;">
                  <p style="margin-bottom: 15px;"><strong>Importe a transferir:</strong> {$total}</p>
                  <p style="margin-bottom: 15px;"><strong>Titular de la cuenta:</strong> {$account.owner|escape:'htmlall':'UTF-8'}</p>
                  <p style="margin-bottom: 15px;"><strong>Detalles de la cuenta:</strong><br>{$account.details nofilter}</p>
                  <p style="margin-bottom: 15px;"><strong>Dirección del banco:</strong><br>{$account.address nofilter}</p>
                </div>
              </div>
            {/foreach}
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>
  {/if}
</section>

<script>
jQuery(document).ready(function($) {
  console.log('[Wire Payment Multi] Modal de detalles inicializado');

  // Función para actualizar el modal con el banco seleccionado
  function updateModalContent() {
    var selectedId = $('input[name="selected_bank_account"]:checked').val();
    console.log('[Wire Payment Multi] Actualizando modal para banco:', selectedId);
    $('.bank-account-detail').hide();
    if (selectedId) {
      $('.bank-detail-' + selectedId).show();
    }
  }

  // Escuchar cambios en la selección para actualizar el modal
  $(document).on('change', 'input[name="selected_bank_account"]', function() {
    updateModalContent();
  });

  // Cuando se abre el modal
  $('#bankwire-modal-multi').on('show.bs.modal', function() {
    updateModalContent();
  });

  // Inicializar el modal con el primer banco seleccionado
  setTimeout(function() {
    updateModalContent();
  }, 300);

  console.log('[Wire Payment Multi] ✓ Modal configurado');
});
</script>
