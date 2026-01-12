<div class="panel" style="border-left: 4px solid #25b9d7;">
  <div class="panel-heading" style="background: #25b9d7; color: white;">
    <i class="icon-credit-card"></i> Transferencia Bancaria Seleccionada
  </div>
  <div class="panel-body">
    <div style="display: flex; align-items: center; gap: 20px;">
      {if $bank_logo}
        <div style="flex-shrink: 0;">
          <img src="{$bank_logo|escape:'htmlall':'UTF-8'}"
               alt="{$bank_name|escape:'htmlall':'UTF-8'}"
               style="max-width: 120px; max-height: 60px; object-fit: contain;">
        </div>
      {/if}
      <div style="flex-grow: 1;">
        <h4 style="margin: 0 0 10px 0; color: #25b9d7;">
          <i class="icon-bank"></i> {$bank_name|escape:'htmlall':'UTF-8'}
        </h4>
        <p style="margin: 5px 0;">
          <strong>Titular:</strong> {$bank_owner|escape:'htmlall':'UTF-8'}
        </p>
        {if $bank_iban_last4}
          <p style="margin: 5px 0;">
            <strong>Cuenta:</strong> ****{$bank_iban_last4|escape:'htmlall':'UTF-8'}
          </p>
        {/if}
        <p style="margin: 10px 0 0 0; font-size: 0.9em; color: #666;">
          <strong>Detalles completos:</strong><br>
          {$bank_details|nl2br nofilter}
        </p>
      </div>
    </div>
  </div>
</div>
