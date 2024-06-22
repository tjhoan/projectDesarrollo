const express = require('express')
const router = express.Router()
const controllers = require('../controllers/forms')
const upload = require('../config/multerConfig')

router.get('/', controllers.main)
router.get('/admin', controllers.admin)
router.get('/details', controllers.details)

router.get('/addCart/:id', controllers.addCart)

router.post('/form-login-cliente', controllers.loginFormCustomer)
router.post('/form-login-admin', controllers.loginFormAdmin)
router.post('/subirProducto', upload.array('images', 10), controllers.subirProducto)

module.exports = router
