const express = require('express')
const router = express.Router()
const controllers = require('../controllers/forms')
const upload = require('../config/multerConfig')

router.get('/', controllers.main)
router.get('/go-to-main', controllers.goToMain)
router.get('/detalles/:id', controllers.detailsProduct)
router.get('/logout', controllers.destroySession);
router.get('/admin', controllers.admin)
router.get('/details', controllers.details)
router.get('/factura', controllers.factura)

router.get('/addCart/:id', controllers.addCart)
router.get('/product/delete/:id', controllers.deleteProduct)
router.get('/formulario/:customerId', controllers.formulario)
router.get('/procesoPago/:customerId', controllers.procesoPago)

router.post('/form-login-cliente', controllers.loginFormCustomer)
router.post('/form-login-admin', controllers.loginFormAdmin)
router.post('/subirProducto', upload.array('images', 5), controllers.subirProducto)
router.post('/crearCategoria', controllers.crearCategoria)
router.post('/paymentForm', controllers.paymentForm)

module.exports = router