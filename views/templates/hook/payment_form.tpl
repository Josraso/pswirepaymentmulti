<form method="POST" action="{$link->getModuleLink('pswirepaymentmulti', 'validation', [], true)|escape:'html':'UTF-8'}">
  <input type="hidden" name="selected_bank_account" id="selected_bank_id" value="{if isset($bankAccounts[0])}{$bankAccounts[0].id|escape:'htmlall':'UTF-8'}{/if}">
</form>

<script type="text/javascript">
(function() {
  if (typeof jQuery === 'undefined') return;

  jQuery(document).ready(function($) {
    // Actualizar el campo oculto cuando cambia la selección
    $(document).on('change', 'input[name="selected_bank_account"]', function() {
      var selectedId = $(this).val();
      $('#selected_bank_id').val(selectedId);
      console.log('[Wire Payment Multi] Campo actualizado con banco:', selectedId);
    });

    // Inicializar con el primero
    var firstBank = $('input[name="selected_bank_account"]:first').val();
    if (firstBank) {
      $('#selected_bank_id').val(firstBank);
    }
  });
})();
</script>
