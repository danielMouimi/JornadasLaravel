<?php

namespace App\Http\Controllers;

use App\Http\Requests\PagoRequest;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\EnviarTicket;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\Snappy\Facades\SnappyPdf;

class PagoController extends Controller
{
    /**
     * Muestra el historial de pagos del usuario autenticado.
     */
    public function index()
    {
        return view('pagos.index');
    }

    public function procesarPago(PagoRequest $request)
    {
        try {
            $user = Auth::user();


            if ($request->tipo_inscripcion === 'gratuita' && !$user->es_alumno) {
                return response()->json([
                    'error' => 'Debes ser alumno para optar por la inscripción gratuita.'
                ], 403);
            }

            $user->update([
                'tipo_inscripcion' => $request->tipo_inscripcion,
                'confirmado' => true,
                'total_pagado' => $user->total_pagado + ($request->tipo_inscripcion === 'presencial' ? 20 : ($request->tipo_inscripcion === 'virtual' ? 10 : 0)),
            ]);
//
            // Enviar correo
            $this->enviarCorreoConPHPMailer($user);



            return response()->json([
                'redirect' => url('/dashboard')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error interno del servidor'.$e->getMessage(),
                'message' => $e->getMessage(), // Muestra el error exacto
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }


    /**
     * Procesa un pago (simulación).
     */
    public function store(Request $request)
    {
        $request->validate([
            'monto' => 'required|numeric|min:0.01',
        ]);

        // Simulación de pago (aquí se integraría PayPal)
        Pago::create([
            'usuario_id' => Auth::id(),
            'monto' => $request->monto,
            'estado' => 'completado',
            'paypal_transaction_id' => 'TRX-' . strtoupper(uniqid()),
        ]);

        return redirect()->route('pagos.index')->with('success', 'Pago realizado correctamente.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }



    /**
     * Display the specified resource.
     */
    public function show(Pago $pago)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pago $pago)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pago $pago)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pago $pago)
    {
        //
    }



    public function enviarCorreoConPHPMailer($user)
    {
        try {
            $mail = new PHPMailer(true);

            // Configurar servidor SMTP
            $mail->isSMTP();
            $mail->Host = env('MAIL_HOST', 'smtp.gmail.com');
            $mail->SMTPAuth = true;
            $mail->Username = env('MAIL_USERNAME');
            $mail->Password = env('MAIL_PASSWORD');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = env('MAIL_PORT', 587);

            // Configurar remitente y destinatario
            $mail->setFrom('mouimidaniel@gmail.com', 'Jornadas de Videojuegos');
            $mail->addAddress($user->email, $user->name);

            // Asunto del correo
            $mail->Subject = 'Tu ticket para las Jornadas de Videojuegos';

            // Cuerpo del correo en HTML
            $mail->isHTML(true);
            $mail->Body = view('emails.ticket', ['user' => $user])->render();

//            $pdf = SnappyPdf::loadView('emails.ticket', ['user' => $user]);
//            $mail->addStringAttachment($pdf->output(), 'ticket.pdf', 'base64', 'application/pdf');
;

            // Enviar correo
            $mail->send();

        } catch (Exception $e) {
            \Log::error('Error enviando correo: ' . $mail->ErrorInfo);
        }
    }
}
