<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use TCPDF;
use App\Mail\InvoiceMail;

class PaymentController extends Controller
{
    public function processPayment(Request $request)
    {
        Log::info('Comenzando el procesamiento del pago');

        // Registrar todos los datos enviados en el request
        Log::info('Datos del request', ['request' => $request->all()]);

        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            Log::error('Usuario no autenticado');
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para completar la compra.');
        }

        // Obtener el carrito del usuario autenticado
        $cart = Auth::user()->cart;
        Log::info('Carrito del usuario obtenido', ['cart' => $cart]);

        // Verificar si el carrito existe y si tiene productos
        if (!$cart || $cart->items->isEmpty()) {
            Log::error('Carrito vacío o no existe', ['cart' => $cart]);
            return redirect()->back()->with('error', 'No tienes productos en tu carrito.');
        }

        // Convertir los campos checkbox a booleanos
        $request->merge([
            'pdf_invoice' => $request->has('pdf_invoice'),
            'email_invoice' => $request->has('email_invoice')
        ]);

        // Validación de los datos
        $request->validate([
            'full_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'payment_method' => 'required|in:nequi,daviplata,bancolombia',
            'pdf_invoice' => 'nullable|boolean',
            'email_invoice' => 'nullable|boolean',
        ]);

        Log::info('Validación de datos completada');

        // Crear el pago
        try {
            $payment = Payment::create([
                'customer_id' => Auth::id(),
                'full_name' => $request->full_name,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'phone' => $request->phone,
                'additional_info' => $request->additional_info,
                'payment_method' => $request->payment_method,
                'pdf_invoice' => $request->pdf_invoice,
                'email_invoice' => $request->email_invoice,
                'confirmation_code' => rand(100000, 999999),
            ]);
            Log::info('Pago creado', ['payment' => $payment]);
        } catch (\Exception $e) {
            Log::error('Error al crear el pago', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Hubo un problema al crear el pago.');
        }

        // Cálculo del total del carrito
        try {
            $total = $cart->calculateTotal();
            Log::info('Total del carrito calculado', ['total' => $total]);
        } catch (\Exception $e) {
            Log::error('Error al calcular el total del carrito', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Hubo un problema al calcular el total.');
        }

        // Crear la factura
        try {
            $invoice = Invoice::create([
                'customer_id' => Auth::id(),
                'payment_id' => $payment->id,
                'total_amount' => $total,
                'invoice_number' => 'INV-' . strtoupper(uniqid()),
            ]);

            // Cargar la relación con el cliente para usarla en la vista PDF
            $invoice->load('customer');
            Log::info('Factura creada', ['invoice' => $invoice]);
        } catch (\Exception $e) {
            Log::error('Error al crear la factura', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Hubo un problema al crear la factura.');
        }


        // Generar PDF usando TCPDF si es necesario
        if ($payment->pdf_invoice) {
            try {
                $pdf = new TCPDF();
                $pdf->SetTitle('Factura');
                $pdf->AddPage();
                $html = view('invoices.pdf', ['invoice' => $invoice])->render();
                $pdf->writeHTML($html, true, false, true, false, '');

                $pdfPath = 'invoices/' . $invoice->invoice_number . '.pdf';
                $pdf->Output(storage_path('app/public/' . $pdfPath), 'F');
                Log::info('PDF generado y guardado', ['pdfPath' => $pdfPath]);
            } catch (\Exception $e) {
                Log::error('Error al generar PDF', ['error' => $e->getMessage()]);
                return redirect()->back()->with('error', 'Hubo un problema al generar el PDF.');
            }
        }

        // Enviar la factura por correo si es necesario
        if ($payment->email_invoice) {
            try {
                Mail::to(Auth::user()->email)->send(new InvoiceMail($invoice));
                Log::info('Correo enviado correctamente');
            } catch (\Exception $e) {
                Log::error('Error al enviar el correo', ['error' => $e->getMessage()]);
                return redirect()->back()->with('error', 'Hubo un problema al enviar el correo.');
            }
        }

        // Vaciar el carrito después del pago
        try {
            $cart->items()->delete();
            Log::info('Carrito vaciado');
        } catch (\Exception $e) {
            Log::error('Error al vaciar el carrito', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Hubo un problema al vaciar el carrito.');
        }

        return response()->json([
            'success' => true,
            'message' => 'Pago procesado con éxito.',
            'invoice_number' => $invoice->invoice_number
        ]);
    }
}
