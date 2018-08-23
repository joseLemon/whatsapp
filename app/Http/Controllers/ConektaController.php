<?php

namespace App\Http\Controllers;

use Conekta\Conekta;
use Conekta\Handler;
use Conekta\Order;
use Conekta\ParameterValidationError;
use Conekta\ProcessingError;
use Illuminate\Http\Request;

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

    public function Order(Request $request)
    {
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
            "charges" =>
                [
                    "payment_method" => $payment_method,
                ]
        ];
        // If payment_method type is card, add the card token
        if($request->input('token') && $payment_method == 'card')
            $charges["charges"]["token_id"] = $request->input('token');

        // Setting the purchased item data
        $line_items = [
            "line_items" =>
                [
                    "name" => "Paquete 1",
                    "unit_price" => 500,
                    "quantity" => 1
                ]
        ];

        // Setting the customer information
        $customer_info = [
            "customer_info" =>
            [
                "name" => $request->input('c_holder_name'),
                "email" => $request->input('c_holder_email')
            ]
        ];

        try {
            $order = Order::create([
                $line_items,
                $charges,
                "currency" => $this->currency,
                $customer_info

            ]);
        } catch (ProcessingError $error) {
            echo $error->getMessage();
        } catch (ParameterValidationError $error) {
            echo $error->getMessage();
        } catch (Handler $error) {
            echo $error->getMessage();
        }
    }
}