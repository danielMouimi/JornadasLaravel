<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketMail;
use Srmklive\PayPal\Services\PayPal as PayPalClient;


class PayPalController extends Controller
{
    // Crear orden
    public function createOrder(Request $request)
    {
        $user = auth()->user();
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('services.paypal'));
        $paypalToken = $provider->getAccessToken();

        $total = $this->getInscriptionPrice($user);

        $order = $provider->createOrder([
            "intent" => "CAPTURE",
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => config('services.paypal.currency'),
                        "value" => number_format($total, 2, '.', '')
                    ]
                ]
            ]
        ]);

        return response()->json($order);
    }

    // Capturar pago
    public function captureOrder(Request $request, $orderID)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('services.paypal'));
        $provider->getAccessToken();

        $capture = $provider->capturePaymentOrder($orderID);

        if ($capture['status'] === 'COMPLETED') {
            $user = auth()->user();
            $user->confirmado = true;
            $user->total_pagado = $capture['purchase_units'][0]['payments']['captures'][0]['amount']['value'];
            $user->paypal_transaction_id = $orderID;
            $user->save();

            // Enviar ticket por correo
            Mail::to($user->email)->send(new TicketMail($user));

            return response()->json([
                "message" => "Pago exitoso y usuario confirmado",
                "order" => $capture
            ]);
        }

        return response()->json(["error" => "No se pudo capturar el pago"], 500);
    }

    private function getInscriptionPrice($user)
    {
        if ($user->tipo_inscripcion === 'gratuita') {
            return 0.00;
        }
        return $user->tipo_inscripcion === 'presencial' ? 50.00 : 30.00;
    }
}
