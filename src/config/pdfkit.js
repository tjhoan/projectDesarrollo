const PDFDocument = require('pdfkit');

function buildPDF(dataCallback, endCallback, invoice) {
  const doc = new PDFDocument();

  doc.on('data', dataCallback);
  doc.on('end', endCallback);

  doc.fontSize(20).text('Factura', { align: 'center' });
  doc.text(`Fecha Compra: ${invoice.purchaseDate}`);

  doc.moveDown().fontSize(15).text('Datos del Cliente', { underline: true });
  doc.fontSize(12).text(`Nombre: ${invoice.customer.first_name} ${invoice.customer.last_name}`);
  doc.text(`Dirección: ${invoice.customer.address}`);
  doc.text(`Email: ${invoice.customer.email}`);
  doc.text(`C.C: ${invoice.customer.cedula}`);
  doc.text(`Banco: ${invoice.payment.banc}`);
  doc.text(`Número de Cuenta: ${invoice.payment.account_number}`);

  doc.moveDown().fontSize(15).text('Productos', { underline: true });
  invoice.cart.products.forEach((item, index) => {
    doc.fontSize(12).text(`${index + 1}. ${item.product.name} - ${item.quantity} x $${item.product.price} = $${item.product.price * item.quantity}`);
  });

  doc.moveDown().fontSize(15).text('Totales', { underline: true });
  const total = invoice.cart.products.reduce((sum, item) => sum + item.product.price * item.quantity, 0);
  doc.fontSize(12).text(`Total: $${total}`);
  doc.text(`IVA: 20%`);
  doc.text(`Total a Pagar: $${total * 1.2}`);

  doc.end();
}

module.exports = buildPDF;
