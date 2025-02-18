<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\EnviarTicket;

class PagoController extends Controller
{
    /**
     * Muestra el historial de pagos del usuario autenticado.
     */
    public function index()
    {
        return view('pagos.index');
    }

    public function procesarPago(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'tipo_inscripcion' => 'required|in:presencial,virtual,gratuita',
            'paypal_transaction_id' => $request->tipo_inscripcion !== 'gratuita' ? 'required|string' : 'nullable',
        ]);

        if ($request->tipo_inscripcion === 'gratuita' && !$user->es_alumno) {
            return redirect()->route('pago.index')->with('error', 'Debes ser alumno para optar por la inscripción gratuita.');
        }

        if ($request->tipo_inscripcion !== 'gratuita') {
            Pago::create([
                'usuario_id' => $user->id,
                'tipo_inscripcion' => $request->tipo_inscripcion,
                'monto' => $request->tipo_inscripcion === 'presencial' ? 20 : 10,
                'paypal_transaction_id' => $request->paypal_transaction_id,
            ]);
        }

        $user->update([
            'tipo_inscripcion' => $request->tipo_inscripcion,
            'confirmado' => true,
            'total_pagado' => $user->total_pagado + ($request->tipo_inscripcion === 'presencial' ? 20 : ($request->tipo_inscripcion === 'virtual' ? 10 : 0)),
        ]);

        // Generar ticket
        $codigo_ticket = strtoupper(uniqid('TICKET-'));
        $ticket = Ticket::create([
            'usuario_id' => $user->id,
            'codigo' => $codigo_ticket,
        ]);

        // Generar PDF del ticket
        $pdf = Pdf::loadView('tickets.pdf', compact('user', 'ticket'));

        // Enviar correo con el ticket adjunto
        Mail::to($user->email)->send(new EnviarTicket($user, $pdf));

        return response()->json(['redirect' => route('dashboard')]);
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
}
