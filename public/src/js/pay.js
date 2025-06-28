//script del cotizador sin el modal del resumen (solo con el modal de datos para izipay)
import { GetTokenSession } from './getTokenSession.js';
import { getDataOrderDynamic } from './util.js';

document.addEventListener('DOMContentLoaded', () => {
  const { transactionId, orderNumber } = getDataOrderDynamic();

  const MERCHANT_CODE = '4004353';
  const PUBLIC_KEY = 'VErethUtraQuxas57wuMuquprADrAHAb';
  let ORDER_AMOUNT = '0.00';
  const ORDER_CURRENCY = 'PEN';

  window.addEventListener('totalUpdated', function (e) {
    ORDER_AMOUNT = e.detail.amount;
    const buttonPay = document.querySelector('#btnPayNow');
    if (buttonPay) {
      buttonPay.innerHTML = `Pagar S/ ${ORDER_AMOUNT} →`;
    }
  });

  GetTokenSession(transactionId, {
    requestSource: 'ECOMMERCE',
    merchantCode: MERCHANT_CODE,
    orderNumber: orderNumber,
    publicKey: PUBLIC_KEY,
    amount: ORDER_AMOUNT,
  }).then((authorization) => {
    const {
      response: { token = undefined, error },
    } = authorization || { response: {}, error: 'NODE_API' };

    if (!!token) {
      const buttonPay = document.querySelector('#btnPayNow');
      buttonPay.disabled = false;
      buttonPay.innerHTML = `Pagar S/ ${ORDER_AMOUNT} →`;

      buttonPay.addEventListener('click', (event) => {

        event.preventDefault();
        const modalElement = document.getElementById('billingModal');
        const modal = new bootstrap.Modal(modalElement);
        $('#billingModal').modal('show');
      });

      const billingForm = document.getElementById('billingForm');

      billingForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        if (!billingForm.checkValidity()) {
          billingForm.classList.add('was-validated');
          return;
        }

        const formData = new FormData(billingForm);
        const billingData = Object.fromEntries(formData.entries());

       $('#billingModal').modal('hide'); 


        const currentAmount = ORDER_AMOUNT;
        const { transactionId, orderNumber } = getDataOrderDynamic();

        const newAuthorization = await GetTokenSession(transactionId, {
          requestSource: 'ECOMMERCE',
          merchantCode: MERCHANT_CODE,
          orderNumber: orderNumber,
          publicKey: PUBLIC_KEY,
          amount: currentAmount,
        });

        const {
          response: { token = undefined, error },
        } = newAuthorization || {};

        if (token) {
          const iziConfig = {
            config: {
              transactionId: transactionId,
              action: Izipay.enums.payActions.PAY,
              merchantCode: MERCHANT_CODE,
              order: {
                orderNumber: orderNumber,
                currency: ORDER_CURRENCY,
                amount: currentAmount,
                processType: Izipay.enums.processType.AUTHORIZATION,
                merchantBuyerId: window.currentClienteId ?? 'cliente-anonimo',
                dateTimeTransaction: `${Date.now()}000`,
                payMethod: "CARD, QR, YAPE_CODE, PAGO_PUSH",
              },
              billing: {
                ...billingData,
                documentType: Izipay.enums.documentType.DNI,
              },
              render: {
                typeForm: Izipay.enums.typeForm.POP_UP,
                container: '#your-iframe-payment',
                showButtonProcessForm: false,
              },
              urlRedirect: 'https://server.punto-web.com/comercio/creceivedemo.asp?p=h1',
              appearance: {
                logo: 'https://logowik.com/content/uploads/images/shopping-cart5929.jpg',
              },
            },
          };
        console.log(iziConfig?.config);

          const checkout = new Izipay({ config: iziConfig.config });

          checkout.LoadForm({
            authorization: token,
            keyRSA: 'RSA',
            callbackResponse: (response) => {
              console.log('Respuesta del pago:', response);
              const code = response.code;
              const message = response.message;
              let finalMessage = '';
              let iconType = 'info';

              if (code === '00') {
                console.log(`Transacción exitosa: ${message}`);
              } else if (code === '021') {
                finalMessage = `${message}. Si tiene inconvenientes, por favor recargue la página e intente nuevamente.`;
                iconType = 'error';
              } else {
                finalMessage = 'Hubo un problema con la transacción. Intente nuevamente.';
                iconType = 'error';
              }
              if (code !== '00') {
              Swal.fire({
                position: 'top-end',
                icon: iconType,
                html: finalMessage,
                showConfirmButton: false,
                timer: 3000,
              });}

              fetch('/api/payment/store', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                },
                body: JSON.stringify(response),
              })
                .then((res) => res.json())
                .then((data) => {
                  //console.log('Respuesta guardada en backend:', data);
                  if (code === '00') {
                    window.parent.postMessage(
                      {
                        action: 'habilitar-boton',
                        orderNumber: response.response.order[0].orderNumber,
                      },
                      '*'
                    );
                  }
                })
                .catch((err) => {
                  console.error('Error al guardar en backend:', err);
                });
            },
          });
        } else {
          console.error('Error al generar token:', newAuthorization);
          alert('❌ No se pudo generar el token. Verifica el monto y vuelve a intentar.');
        }
      });
    } else if (error) {
      console.log('error-->', error);
    }
  });
});
