// getTokenSession.js
export async function GetTokenSession(transactionId, {
    requestSource = 'ECOMMERCE',
    merchantCode = '',
    orderNumber = '',
    publicKey = '',
    amount = '',
}) {
    try {
        const response = await fetch('/api/token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'transactionId': transactionId,
            },
            body: JSON.stringify({
                requestSource,
                merchantCode,
                orderNumber,
                publicKey,
                amount,
            }),
        });

        return await response.json();
    } catch (e) {
        console.log('¡No se pudo conectar al backend de Laravel!');
        return {
            response: {
                token: undefined,
                error: '01_NODE_API'
            }
        };
    }
}
