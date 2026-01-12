<?php

global $_MODULE;
$_MODULE = array();

// Admin
$_MODULE['<{pswirepaymentmulti}prestashop>pswirepaymentmulti_b02c0b74e5ff8061e6e937c5ec62028a'] = 'Wire Payment Multi';
$_MODULE['<{pswirepaymentmulti}prestashop>pswirepaymentmulti_2faf0b4c207091f829e9231e0fd39416'] = 'Acepta pagos por transferencia bancaria mostrando múltiples cuentas bancarias durante el checkout.';
$_MODULE['<{pswirepaymentmulti}prestashop>pswirepaymentmulti_876f23178c29fbb0e80b3b17e4f86c35'] = '¿Estás seguro de que quieres desinstalar este módulo? Se eliminarán todos los datos de cuentas bancarias.';
$_MODULE['<{pswirepaymentmulti}prestashop>pswirepaymentmulti_20015706a8cbd57fc76933e4cc5d03f9'] = 'Debes configurar y activar al menos una cuenta bancaria antes de usar este módulo.';
$_MODULE['<{pswirepaymentmulti}prestashop>pswirepaymentmulti_c888438d14855d7d96a2724ee9c306bd'] = 'No se ha establecido ninguna moneda para este módulo.';

// Configuration
$_MODULE['<{pswirepaymentmulti}prestashop>pswirepaymentmulti_3ec365dd533ddb7ef3d1c111186ce872'] = 'Personalización';
$_MODULE['<{pswirepaymentmulti}prestashop>pswirepaymentmulti_f4f70727dc34561dfde1a3c529b6205c'] = 'Configuración';
$_MODULE['<{pswirepaymentmulti}prestashop>pswirepaymentmulti_0881a11f7af33bc1b43e437391129d66'] = 'Período de reserva';
$_MODULE['<{pswirepaymentmulti}prestashop>pswirepaymentmulti_7cc92687130ea12abb80556681538001'] = 'Número de días que los artículos permanecen reservados';
$_MODULE['<{pswirepaymentmulti}prestashop>pswirepaymentmulti_a713711bf09e913005692cf61d3eb64e'] = 'Información para el cliente';
$_MODULE['<{pswirepaymentmulti}prestashop>pswirepaymentmulti_bf2957d7706c8fdd712c8e4a91e8d435'] = 'Información sobre la transferencia bancaria (tiempo de procesamiento, inicio del envío...)';
$_MODULE['<{pswirepaymentmulti}prestashop>pswirepaymentmulti_8c40a45ecfb22ed9d8803ce8e14b7e3a'] = 'Mostrar la invitación de pago en la página de confirmación de pedido';
$_MODULE['<{pswirepaymentmulti}prestashop>pswirepaymentmulti_71f303bb090c2a4c75eaf42f66341fe3'] = 'La legislación de tu país puede requerir que envíes la invitación de pago solo por correo electrónico. Desactivar esta opción ocultará la invitación en la página de confirmación.';

// Bank Accounts Admin
$_MODULE['<{pswirepaymentmulti}prestashop>adminbankaccountscontroller_b5a7adde1af5c87d7fd797b6245c2a39'] = 'Descripción';
$_MODULE['<{pswirepaymentmulti}prestashop>adminbankaccountscontroller_1e6d2f06688b16d363022c90d2f7bc71'] = 'Cuenta Bancaria';
$_MODULE['<{pswirepaymentmulti}prestashop>adminbankaccountscontroller_91a11556aa18dc872417b36e8f5c9e8f'] = 'Nombre del Banco';
$_MODULE['<{pswirepaymentmulti}prestashop>adminbankaccountscontroller_5a80eb5e35fa8fb7287c1a5de5

0f49db'] = 'Nombre del banco (ej., "Santander", "BBVA", etc.)';
$_MODULE['<{pswirepaymentmulti}prestashop>adminbankaccountscontroller_4c1f76824dca8d92e4a50c43f5e7d7b8'] = 'Logo del Banco';
$_MODULE['<{pswirepaymentmulti}prestashop>adminbankaccountscontroller_0cfe98bb830d2310ed2e0fa90b7df5c2'] = 'Sube el logo del banco (recomendado: 200x100px, PNG o JPG)';
$_MODULE['<{pswirepaymentmulti}prestashop>adminbankaccountscontroller_0dbd1fb012ba2d0de90c6e2c64f70a9d'] = 'Titular de la Cuenta';
$_MODULE['<{pswirepaymentmulti}prestashop>adminbankaccountscontroller_ac3e7f7641f6b81be02d8c9b914a3f31'] = 'Nombre del titular de la cuenta';
$_MODULE['<{pswirepaymentmulti}prestashop>adminbankaccountscontroller_7a93c4d1fee9f1015212f9dd4ad59aa8'] = 'Detalles de la Cuenta';
$_MODULE['<{pswirepaymentmulti}prestashop>adminbankaccountscontroller_1e6bfe8fb351bf7a5c31a2de7645b7f0'] = 'Detalles de la cuenta bancaria: IBAN, BIC/SWIFT, número de cuenta, etc.';
$_MODULE['<{pswirepaymentmulti}prestashop>adminbankaccountscontroller_example'] = 'Ejemplo: IBAN: ES12 1234 5678 9012 3456 7890 | BIC: SABADELL';
$_MODULE['<{pswirepaymentmulti}prestashop>adminbankaccountscontroller_ad9e2b4527cb0bc63e11e26a6c4ec9f6'] = 'Dirección del Banco';
$_MODULE['<{pswirepaymentmulti}prestashop>adminbankaccountscontroller_ccf10cbd9b830cdb149e8df51301f0d3'] = 'Dirección completa de la sucursal bancaria';

// Shop (Frontend)
$_MODULE['<{pswirepaymentmulti}prestashop>payment_5e1695822fc5af98f6b749ea3cbc9b4c'] = 'Pagar por transferencia bancaria';
$_MODULE['<{pswirepaymentmulti}prestashop>payment_choice'] = 'Pagar por transferencia bancaria - Elija entre %count% bancos';
$_MODULE['<{pswirepaymentmulti}prestashop>payment_intro_select'] = 'Por favor, selecciona una de nuestras cuentas bancarias para transferir el importe de la factura. Recibirás nuestro confirmación de pedido por correo electrónico con los detalles bancarios y el número de pedido.';
$_MODULE['<{pswirepaymentmulti}prestashop>payment_intro_reserved_plural'] = 'Los productos se reservarán durante %s días y procesaremos el pedido inmediatamente después de recibir el pago.';
$_MODULE['<{pswirepaymentmulti}prestashop>payment_intro_reserved_single'] = 'Los productos se reservarán durante %s día y procesaremos el pedido inmediatamente después de recibir el pago.';
$_MODULE['<{pswirepaymentmulti}prestashop>payment_choose'] = 'Elija una cuenta bancaria (%d disponibles):';
$_MODULE['<{pswirepaymentmulti}prestashop>payment_account_holder'] = 'Titular de la cuenta:';
$_MODULE['<{pswirepaymentmulti}prestashop>payment_view_details'] = 'Ver detalles de la cuenta bancaria seleccionada';
$_MODULE['<{pswirepaymentmulti}prestashop>payment_modal_title'] = 'Pago por Transferencia Bancaria - Detalles de la Cuenta';
$_MODULE['<{pswirepaymentmulti}prestashop>payment_amount'] = 'Importe a transferir:';
$_MODULE['<{pswirepaymentmulti}prestashop>payment_details'] = 'Detalles de la cuenta:';
$_MODULE['<{pswirepaymentmulti}prestashop>payment_address'] = 'Dirección del banco:';

// Payment return
$_MODULE['<{pswirepaymentmulti}prestashop>payment_return_complete'] = 'Tu pedido en %s está completo.';
$_MODULE['<{pswirepaymentmulti}prestashop>payment_return_send'] = 'Por favor, envía una transferencia bancaria a la siguiente cuenta:';
$_MODULE['<{pswirepaymentmulti}prestashop>payment_return_amount'] = 'Importe:';
$_MODULE['<{pswirepaymentmulti}prestashop>payment_return_reference'] = 'Por favor, especifica tu referencia de pedido %s en la descripción de la transferencia.';
$_MODULE['<{pswirepaymentmulti}prestashop>payment_return_email'] = 'También te hemos enviado esta información por correo electrónico.';
$_MODULE['<{pswirepaymentmulti}prestashop>payment_return_sent'] = 'Tu pedido se enviará tan pronto como recibamos el pago.';
$_MODULE['<{pswirepaymentmulti}prestashop>payment_return_contact'] = 'Si tienes preguntas, comentarios o inquietudes, por favor contacta con nuestro [1]equipo de atención al cliente[/1].';

return $_MODULE;
