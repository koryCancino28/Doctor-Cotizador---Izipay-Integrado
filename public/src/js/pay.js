import { GetTokenSession } from './getTokenSession.js';
import { getDataOrderDynamic } from './util.js';

/************** función de apoyo para simular el order y transactionId de manera dinámica **************/
const { transactionId, orderNumber } = getDataOrderDynamic();

/* Inicio datos del comercio */
const MERCHANT_CODE = '4001834';
const PUBLIC_KEY = 'VErethUtraQuxas57wuMuquprADrAHAb';
/* Fin datos del comercio */

/************* Inicio datos de la transacción **************/
const TRANSACTION_ID = transactionId;
const ORDER_NUMBER = orderNumber;
const ORDER_AMOUNT = '1.99';
const ORDER_CURRENCY = 'PEN';
/************* Fin datos de la transacción **************/

/********************************************************
 - Obteniendo el código de /autorización o token de sessión/ para inicializar el formulario de pago
 - El comercio debe llamar a su backend con sus datos para poder generar el token
 *********************************************************/
GetTokenSession(TRANSACTION_ID, {
    requestSource: 'ECOMMERCE',
    merchantCode: MERCHANT_CODE,
    orderNumber: ORDER_NUMBER,
    publicKey: PUBLIC_KEY,
    amount: ORDER_AMOUNT,
}).then(authorization => {

    /********* Obteniendo el token de la respuesta  **********/
    const { response: { token = undefined, error } = { response: undefined, error: 'NODE_API' } } = authorization;
    
    if (!!token) {

        const buttonPay = document.querySelector('#btnPayNow');

        buttonPay.disabled = false;
        buttonPay.innerHTML = `${ORDER_CURRENCY} ${ORDER_AMOUNT} →`;

        //Datos de configuración para cargar el Checkout(form) de izipay

        console.log('ENUMS-->', Izipay.enums);

        const iziConfig = {
            config: {
                transactionId: TRANSACTION_ID,
                action: Izipay.enums.payActions.PAY,
                merchantCode: MERCHANT_CODE,
                order: {
                    orderNumber: ORDER_NUMBER,
                    currency: ORDER_CURRENCY,
                    amount: ORDER_AMOUNT,
                    processType: Izipay.enums.processType.AUTHORIZATION,
                    merchantBuyerId: 'mc1768',
                    dateTimeTransaction: '1670258741603000', //currentTimeUnix
                    payMethod: Izipay.enums.showMethods.ALL, //
                },
                billing: {
                    firstName: 'Juan',
                    lastName: 'Wick',
                    email: 'jwick@izipay.pe',
                    phoneNumber: '989339999',
                    street: 'calle el demo',
                    city: 'lima',
                    state: 'lima',
                    country: 'PE',
                    postalCode: '00001',
                    document: '12345678',
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
                    /*customize: {
                        visibility: {
                            hideOrderNumber: true,
                            hideSuccessPage: false,
                            hideErrorPage: false,
                            hideIconCloseCheckout: true,
                            hideLogo: true,
                            hideMessageActivateOnlinePurchases: true,
                            hideTestCards: true,
                            hideShakeValidation: true,
                        },
                    }*/
                },
                /*originEntry:{
                    originCode: ''
                }*/
            },
        };

        const callbackResponsePayment = response => document.querySelector('#payment-message').innerHTML = JSON.stringify(response, null, 2);

        const handleLoadForm = () => {
            try {
                const checkout = new Izipay({ config: iziConfig?.config });

                checkout &&
                    checkout.LoadForm({
                        authorization: token,
                        keyRSA: 'RSA',
                        callbackResponse: callbackResponsePayment,
                    });

            } catch (error) {
                console.log(error.message, error.Errors, error.date);
            }
        };

        /************** Botón para llamar al formulario *************/

        document.querySelector('#btnPayNow').addEventListener('click', async (event) => {
            event.preventDefault();
            handleLoadForm();
        });

        //handleLoadForm();

    }else if(error) {
        console.log('error-->', error);
    }

});

