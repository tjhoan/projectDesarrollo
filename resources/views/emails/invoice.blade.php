<p>Hola {{ $invoice->customer->first_name }} {{ $invoice->customer->last_name }},</p>
<p>Gracias por tu compra. Adjuntamos tu factura con número {{ $invoice->invoice_number }}.</p>
