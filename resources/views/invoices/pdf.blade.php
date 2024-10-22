<!DOCTYPE html>
<html>

<head>
    <title>Factura</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
            color: #333;
        }

        h1 {
            font-size: 22px;
            color: #4CAF50;
            margin-bottom: 20px;
        }

        p {
            margin: 5px 0;
        }

        .header {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .total {
            font-size: 16px;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Factura: {{ $invoice->invoice_number }}</h1>
        <p><strong>Cliente:</strong> {{ $payment->full_name }}</p>
        <p><strong>Dirección:</strong> {{ $payment->address }}, {{ $payment->city }}, {{ $payment->state }}</p>
        <p><strong>Teléfono:</strong> {{ $payment->phone }}</p>
    </div>

    <h2>Detalles del Pedido</h2>
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->customer->cart->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ number_format($item->product->price, 2) }}</td>
                    <td>${{ number_format($item->product->price * $item->quantity, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="total">Total a Pagar: ${{ number_format($invoice->total_amount, 2) }}</p>

    <div class="footer">
        <p>Gracias por tu compra</p>
    </div>
</body>

</html>
