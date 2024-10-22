<!DOCTYPE html>
<html>

<head>
    <title>Factura</title>
</head>

<body>
    <h1>Factura: {{ $invoice->invoice_number }}</h1>
    <p>Nombre: {{ $invoice->customer->first_name }} {{ $invoice->customer->last_name }}</p>
    <p>Dirección: {{ $invoice->customer->address }}</p>
    <p>Teléfono: {{ $invoice->customer->phone }}</p>
    <p>Total: ${{ $invoice->total_amount }}</p>
</body>

</html>
