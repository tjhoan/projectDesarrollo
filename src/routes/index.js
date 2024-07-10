const express = require('express');
const router = express.Router();
const controllers = require('../controllers/controller');
const upload = require('../config/multerConfig');

// Rutas GET para productos y categorías
router.get('/category/:category/:customerId?', controllers.getProductsByCategory);
router.get('/detalles/:id', controllers.detailsProduct);

// Rutas GET para eliminar productos y categorías
router.get('/product/delete/:id', controllers.deleteProduct);
router.get('/category/delete/:id', controllers.deleteCaregory);

// Rutas GET para el carrito de compras
router.get('/addCart/:id', controllers.addCart);
router.get('/delete/productCart/:productId', controllers.deleteProductCart);

// Rutas GET para la sesión y la navegación principal
router.get('/go-to-main', controllers.goToMain);
router.get('/logout', controllers.destroySession);

// Rutas GET para formularios y procesos relacionados
router.get('/formulario/:customerId', controllers.formulario);
router.get('/procesoPago/:customerId', controllers.procesoPago);

// Rutas GET para la generación de PDFs y facturas
router.get('/generate-invoice-pdf/:customerId', controllers.generatePDF);
router.get('/factura/:customerId', controllers.factura);

// Rutas POST para formularios de login y guardado
router.post('/form-login-cliente', controllers.loginFormCustomer);
router.post('/form-login-admin', controllers.loginFormAdmin);
router.post('/saveProduct', upload.array('images', 5), controllers.saveProduct);
router.post('/saveAdmin', controllers.saveAdmin);
router.post('/saveCategory', controllers.saveCategory);
router.post('/paymentForm', controllers.paymentForm);

// Rutas GET para vistas principales y administrativas
router.get('/', controllers.main);
router.get('/admin', controllers.admin);
router.get('/details', controllers.details);

module.exports = router;
