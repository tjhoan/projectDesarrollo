<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use TCPDF;

class PaymentController extends Controller
{
    public function processPayment(Request $request)
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para completar la compra.');
        }

        // Obtener el carrito del usuario autenticado
        $cart = Auth::user()->cart;

        // Verificar si el carrito existe y si tiene productos
        if (!$cart || $cart->items->isEmpty()) {
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
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Hubo un problema al crear el pago.');
        }

        // Cálculo del total del carrito
        try {
            $total = $cart->calculateTotal();
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Hubo un problema al crear la factura.');
        }

        // Generar PDF usando TCPDF si es necesario
        if ($payment->pdf_invoice) {
            try {
                $pdf = new TCPDF();
                $pdf->SetTitle('Factura');
                $pdf->AddPage();
                $html = view('invoices.pdf', ['invoice' => $invoice, 'payment' => $payment])->render();
                $pdf->writeHTML($html, true, false, true, false, '');

                $pdfPath = 'invoices/' . $invoice->invoice_number . '.pdf';
                $pdf->Output(storage_path('app/public/' . $pdfPath), 'F');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Hubo un problema al generar el PDF.');
            }
        }

        // Vaciar el carrito después del pago
        try {
            $cart->items()->delete();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Hubo un problema al vaciar el carrito.');
        }

        // Enviar respuesta JSON para manejar la descarga del PDF y mostrar el modal
        return response()->json([
            'success' => true,
            'message' => 'Pago procesado con éxito.',
            'invoice_number' => $invoice->invoice_number
        ]);
    }
}
