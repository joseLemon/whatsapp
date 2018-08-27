<?php

namespace App\Http\Controllers;

use Conekta\Conekta;
use Conekta\Handler;
use Conekta\Order;
use Conekta\ParameterValidationError;
use Conekta\ProcessingError;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ConektaController extends Controller
{
    protected $currency;

    public function __construct()
    {
        Conekta::setApiKey(env('CONEKTA_API_KEY'));
        Conekta::setApiVersion(env('CONEKTA_APU_VER'));
        $this->currency = env('CURRENCY');
    }

    public function test()
    {
        return view('conekta.test');
    }

    public function createOrder(Request $request)
    {
        $token = $request->input('token');
        $card = $request->input('card')['card'];
        // Defining payment method
        switch ($request->input('payment_method')) {
            case 1:
                $payment_method = 'card';
                break;
            case 2:
                $payment_method = 'oxxo_cash';
                break;
            case 3:
                $payment_method = 'spei';
                break;
            default:
                $payment_method = 'default';
                break;
        }
        $charges = [
            [
                "payment_method" => [
                    "type" => $payment_method
                ],
            ]
        ];
        // If payment_method type is card, add the card token
        if ($token && $payment_method == 'card')
            $charges[0]['payment_method']["token_id"] = $token['id'];

        // Setting the purchased item data
        $line_items = [
            [
                "name" => "Paquete 1",
                "unit_price" => 500,
                "quantity" => 1
            ]
        ];

        // Setting the customer information
        $customer_info = [
            "name" => $card['name'],
            "email" => $card['email'],
            "phone" => $card['phone'],
        ];

        try {
            $order = Order::create([
                "currency" => $this->currency,
                "customer_info" => $customer_info,
                "line_items" => $line_items,
                "charges" => $charges,
            ]);

            $response = [
                'success' => true,
                'order' => $order
            ];

            return Response::json($response);
        } catch (ProcessingError $error) {
            dd($error->getConektaMessage()->details);
        } catch (ParameterValidationError $error) {
            dd($error->getConektaMessage()->details);
        } catch (Handler $error) {
            dd($error->getConektaMessage()->details);
        }
    }
}