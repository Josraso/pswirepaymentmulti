<form method="POST" action="{$link->getModuleLink('pswirepaymentmulti', 'validation', [], true)|escape:'html':'UTF-8'}" id="wirepayment-multi-form">
  <input type="hidden" name="selected_bank_account" id="selected_bank_account_input" value="{if isset($bankAccounts[0])}{$bankAccounts[0].id|escape:'htmlall':'UTF-8'}{/if}">
</form>

<script type="text/javascript">
(function() {
  if (typeof jQuery === 'undefined') return;

  jQuery(document).ready(function($) {
    console.log('[Wire Payment Multi Form] Formulario inicializado');

    // Función para actualizar el campo hidden
    function updateHiddenField() {
      var selectedRadio = $('input[type="radio"][name="selected_bank_account"]:checked');
      if (selectedRadio.length > 0) {
        var selectedId = selectedRadio.val();
        $('#selected_bank_account_input').val(selectedId);
        console.log('[Wire Payment Multi Form] Campo actualizado a banco ID:', selectedId);
        return selectedId;
      }
      return null;
    }

    // Escuchar cambios en los radios de selección de banco
    $(document).on('change', 'input[type="radio"][name="selected_bank_account"]', function() {
      updateHiddenField();
    });

    // Antes de enviar el formulario de pago, asegurar que el valor es el correcto
    $(document).on('submit', 'form[id*="payment"]', function(e) {
      console.log('[Wire Payment Multi Form] Detectado envío de formulario de pago');
      updateHiddenField();
    });

    // También capturar clics en el botón de confirmar pedido
    $(document).on('click', 'button[type="submit"]', function() {
      var $form = $(this).closest('form');
      if ($form.attr('id') && $form.attr('id').indexOf('payment') !== -1) {
        console.log('[Wire Payment Multi Form] Detectado clic en botón de pago');
        updateHiddenField();
      }
    });

    // Inicializar con el primer banco
    setTimeout(function() {
      updateHiddenField();
    }, 100);

    console.log('[Wire Payment Multi Form] ✓ Listeners configurados');
  });
})();
</script>
