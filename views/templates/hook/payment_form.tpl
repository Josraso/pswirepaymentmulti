<form method="POST" action="{$link->getModuleLink('pswirepaymentmulti', 'validation', [], true)|escape:'html':'UTF-8'}" id="wirepayment-multi-form">
  {if $bankAccounts}
    <div class="bank-accounts-selector" style="margin: 20px 0;">
      <h4>Elija una cuenta bancaria:</h4>
      <div class="bank-accounts-list" style="display: flex; flex-direction: column; gap: 10px; margin: 15px 0;">
        {foreach from=$bankAccounts item=account}
          <label class="bank-account-card {if $account@first}selected{/if}" for="bank_form_{$account.id}" style="display: flex; align-items: center; padding: 15px; border: 2px solid #ddd; border-radius: 8px; cursor: pointer; transition: all 0.3s ease;">
            <input type="radio" name="selected_bank_account" id="bank_form_{$account.id}" value="{$account.id}" {if $account@first}checked="checked"{/if} style="width: 20px; height: 20px; margin-right: 10px; cursor: pointer;">
            <div class="bank-card-content" style="display: flex; align-items: center; gap: 15px; flex: 1;">
              {if $account.bank_logo}
                <div class="bank-logo">
                  <img src="{$account.bank_logo|escape:'htmlall':'UTF-8'}" alt="{$account.bank_name|escape:'htmlall':'UTF-8'}" style="max-width: 100px; max-height: 50px;">
                </div>
              {/if}
              <div class="bank-info">
                <strong>{$account.bank_name|escape:'htmlall':'UTF-8'}</strong>
                <p style="margin: 0; color: #666; font-size: 0.9em;">Titular: {$account.owner|escape:'htmlall':'UTF-8'}</p>
              </div>
            </div>
          </label>
        {/foreach}
      </div>
    </div>
  {/if}
</form>

<style>
  .bank-account-card:hover { border-color: #25b9d7 !important; background-color: #f8f9fa !important; }
  .bank-account-card.selected { border-color: #25b9d7 !important; background-color: #e3f2fd !important; }
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

    console.log('[Wire Payment Multi Form] ✓ Radios configurados en el formulario');
  });
})();
</script>
