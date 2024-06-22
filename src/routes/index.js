const { Router } = require('express')
const router = Router()

const controllers = require("../controllers/forms");
const upload = require('../app')

router.get("/", controllers.main)
router.get("/admin", controllers.admin)
router.get("/details", controllers.details)

router.post("/form-login-cliente", controllers.loginFormCustomer)
router.post("/form-login-admin", controllers.loginFormAdmin)
router.post("/subirProducto", controllers.subirProducto)

module.exports = router 