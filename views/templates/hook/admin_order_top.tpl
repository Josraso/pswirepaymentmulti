<div class="card">
  <div class="card-header">
    <h3 class="card-header-title">
      <i class="material-icons">account_balance</i> Transferencia Bancaria Seleccionada
    </h3>
  </div>
  <div class="card-body">
    <div style="display: flex; align-items: flex-start; gap: 20px; padding: 15px 0;">
      {if $bank_logo}
        <div style="flex-shrink: 0;">
          <img src="{$bank_logo|escape:'htmlall':'UTF-8'}"
               alt="{$bank_name|escape:'htmlall':'UTF-8'}"
               style="max-width: 120px; max-height: 60px; object-fit: contain; border: 1px solid #e0e0e0; padding: 8px; border-radius: 4px; background: white;">
        </div>
      {/if}
      <div style="flex-grow: 1;">
        <h4 style="margin: 0 0 15px 0; color: #363a41; font-size: 16px; font-weight: 600;">
          {$bank_name|escape:'htmlall':'UTF-8'}
        </h4>

        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 4px; border: 1px solid #e9ecef; margin-bottom: 10px;">
          <p style="margin: 0 0 8px 0; font-size: 14px;">
            <strong style="color: #363a41;">Titular:</strong>
            <span style="color: #6c868e;">{$bank_owner|escape:'htmlall':'UTF-8'}</span>
          </p>
          {if $bank_iban_last4}
            <p style="margin: 0; font-size: 14px;">
              <strong style="color: #363a41;">Cuenta:</strong>
              <span style="color: #6c868e; font-family: monospace;">****{$bank_iban_last4|escape:'htmlall':'UTF-8'}</span>
            </p>
          {/if}
        </div>

        <div style="border-top: 1px solid #e9ecef; padding-top: 12px;">
          <p style="margin: 0 0 8px 0; font-size: 13px; color: #363a41; font-weight: 600;">
            Detalles completos de la cuenta:
          </p>
          <div style="font-size: 13px; color: #6c868e; line-height: 1.6;">
            {$bank_details|nl2br nofilter}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  /* Asegurar estilos consistentes con PrestaShop 1.7+ */
  .card {
    background: #fff;
    border: 1px solid #bbcdd2;
    border-radius: 2px;
    box-shadow: 0 2px 2px 0 rgba(0,0,0,0.1);
    margin-bottom: 20px;
  }
  .card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #bbcdd2;
    padding: 15px 20px;
  }
  .card-header-title {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: #363a41;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .card-body {
    padding: 20px;
  }
</style>
