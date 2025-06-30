<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
    use App\Models\Payment;

class TokenController extends Controller
{
    public function getTokenSession(Request $request)
    {
        $transactionId = $request->header('transactionid');
        $body = $request->all();

        $client = new Client([
            'base_uri' => 'https://sandbox-checkout.izipay.pe',
            'timeout'  => 5.0,
        ]);

        try {
            $response = $client->post('/apidemo/v1/Token/Generate', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'transactionId' => $transactionId,
                ],
                'json' => $body,
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true);

            return response()->json($responseBody);

        } catch (\Exception $e) {
            Log::error('Error al llamar a Izipay Token API: ' . $e->getMessage());

            return response()->json([
                'response' => [
                    'token' => null,
                    'error' => '01_PHP_API'
                ]
            ], 500);
        }
    }

    public function storePaymentResponse(Request $request)
    {
        $data = $request->all();
        if (($data['code'] ?? null) === '021') {
            return response()->json([
                'success' => false,
                'message' => 'Código 021 no se guarda.',
            ], 200); 
        }
        try {
            $payment = Payment::create([
                'code' => $data['code'] ?? null,
                'message' => $data['message'] ?? null,
                'pay_method' => $data['response']['payMethod'] ?? null,
                'amount' => $data['response']['order'][0]['amount'] ?? null,
                'currency' => $data['response']['order'][0]['currency'] ?? null,
                'order_number' => $data['response']['order'][0]['orderNumber'] ?? null,
                'authorization_code' => $data['response']['order'][0]['codeAuth'] ?? null,
                'transaction_id' => $data['transactionId'] ?? null,
                'state_message' => $data['response']['order'][0]['stateMessage'] ?? null,
                'transaction_date' => $data['response']['order'][0]['dateTransaction'] ?? null,
                'transaction_time' => $data['response']['order'][0]['timeTransaction'] ?? null,
                'unique_id' => $data['response']['order'][0]['uniqueId'] ?? null,
                'reference_number' => $data['response']['order'][0]['referenceNumber'] ?? null,
                'merchant_code' => $data['response']['merchant']['merchantCode'] ?? null,
                'buyer_id' => $data['response']['token']['merchantBuyerId'] ?? null,

                'card_brand' => $data['response']['card']['brand'] ?? null,
                'card_pan_masked' => $data['response']['card']['pan'] ?? null,

                'customer_first_name' => $data['response']['billing']['firstName'] ?? null,
                'customer_last_name' => $data['response']['billing']['lastName'] ?? null,
                'customer_email' => $data['response']['billing']['email'] ?? null,
                'customer_document_type' => $data['response']['billing']['documentType'] ?? null,
                'customer_document' => $data['response']['billing']['document'] ?? null,
                'customer_address' => $data['response']['billing']['street'] ?? null,
                'customer_city' => $data['response']['billing']['city'] ?? null,
                'customer_country' => $data['response']['billing']['country'] ?? null,

                'full_response' => $data,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transacción guardada correctamente.',
                'payment_id' => $payment->id
            ]);
        } catch (\Exception $e) {
            Log::error('Error al guardar la transacción: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la transacción.'
            ], 500);
        }
    }

}
