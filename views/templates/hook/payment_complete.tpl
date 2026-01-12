<form method="POST" action="{$action_url|escape:'html':'UTF-8'}" id="pswirepaymentmulti-form">

  <div class="bank-selection-wrapper">
    <p>Por favor, selecciona una cuenta bancaria:</p>

    {foreach from=$bankAccounts item=account}
      <div class="bank-option">
        <label for="bank_{$account.id}">
          <input type="radio"
                 name="selected_bank_account"
                 id="bank_{$account.id}"
                 value="{$account.id}"
                 {if $account@first}checked{/if}
                 required>
          {if $account.bank_logo}
            <img src="{$account.bank_logo|escape:'html'}" alt="{$account.bank_name|escape:'html'}" style="max-width:80px; max-height:40px;">
          {/if}
          <strong>{$account.bank_name|escape:'html'}</strong>
          <span>({$account.owner|escape:'html'})</span>
        </label>
      </div>
    {/foreach}
  </div>

  <button type="submit" class="btn btn-primary" style="display:none;" id="hidden-submit-btn">Confirmar</button>
</form>

<style>
.bank-option { padding: 10px; margin: 5px 0; border: 2px solid #ddd; border-radius: 5px; cursor: pointer; }
.bank-option:has(input:checked) { border-color: #25b9d7; background: #e3f2fd; }
.bank-option label { display: flex; align-items: center; gap: 10px; cursor: pointer; width: 100%; }
.bank-option input[type="radio"] { margin: 0; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Cuando PrestaShop intente procesar el pago, submit el formulario
  var paymentForm = document.getElementById('pswirepaymentmulti-form');
  if (paymentForm) {
    // Interceptar el evento de confirmación de PrestaShop
    document.addEventListener('click', function(e) {
      var target = e.target;
      // Si es el botón de confirmar pedido de PrestaShop
      if (target && (target.matches('button[type="submit"]') || target.closest('button[type="submit"]'))) {
        // Verificar si nuestro método está seleccionado
        var ourMethod = document.querySelector('input[data-module-name="pswirepaymentmulti"]:checked');
        if (ourMethod) {
          var selected = document.querySelector('input[name="selected_bank_account"]:checked');
          if (!selected) {
            e.preventDefault();
            alert('Selecciona una cuenta bancaria');
            return false;
          }
          // Submit nuestro formulario
          setTimeout(function() {
            paymentForm.submit();
          }, 100);
        }
      }
    }, true);
  }
});
</script>
