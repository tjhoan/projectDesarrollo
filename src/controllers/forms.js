const controller = {}
const Image = require('../models/images')
const Customers = require('../models/customers')
const Admin = require('../models/admins')
const Product = require('../models/products')
const Cart = require('../models/cart')

controller.main = async (req, res) => {
  const products = await Product.find()
  res.render('main', { products })
}

controller.admin = (req, res) => {
  res.render('dashboard')
}

controller.details = (req, res) => {
  res.render('detalles')
}

controller.addCart = async (req, res) => {
  const { id } = req.params
  const customerId = req.session.customerId

  if (!customerId) {
    return res.status(401).send('Usuario no autenticado')
  }

  try {
    const product = await Product.findById(id)
    if (!product) {
      return res.status(404).send('Producto no encontrado')
    }

    // Asumamos que tienes un modelo Cart que asocia productos con clientes
    let cart = await Cart.findOne({ customerId })

    if (!cart) {
      // Si no existe un carrito para el cliente, crea uno nuevo
      cart = new Cart({ customerId, products: [] })
    }

    // Agregar el producto al carrito
    cart.products.push({ productId: product._id, quantity: 1 }) // Aquí puedes ajustar la lógica según sea necesario
    await cart.save()

    // Marcar el producto como añadido al carrito (opcional)
    product.incart = true
    await product.save()

    res.redirect('/')
  } catch (error) {
    console.error(error)
    res.status(500).send('Error al agregar el producto al carrito')
  }
}

controller.loginFormCustomer = async (req, res) => {
  try {
    const data = req.body

    // Validación de cédula
    const cedulaCustomer = Number(data.cedula_Customer)
    if (isNaN(cedulaCustomer)) {
      throw new Error('INVALID_CEDULA')
    }

    // Validación de first_name
    if (typeof data.first_name !== 'string' || !isNaN(data.first_name)) {
      throw new Error('INVALID_FIRST_NAME')
    }

    // Consulta a la base de datos
    const cliente = await Customers.find({
      first_name: data.first_name,
      email: data.email_Customer,
      cedula: cedulaCustomer
    })

    // Manejo de la respuesta de la consulta
    if (!cliente.length) {
      return res.render('main', {
        alerta: 'Los datos ingresados no son correctos',
        images: await Image.find({ category: 'a' })
      })
    } else {
      console.log('El cliente ha ingresado correctamente')
      req.session.customerId = cliente._id
      req.session.customerName = cliente.first_name
      return res.render('main', {
        images: await Image.find({ category: 'a' })
      })
    }
  } catch (error) {
    // Manejo de errores
    switch (error.message) {
      case 'INVALID_CEDULA':
        return res.render('main', {
          alerta: 'El campo cédula debe ser un número',
          images: await Image.find({ category: 'a' })
        })
      case 'INVALID_FIRST_NAME':
        return res.render('main', {
          alerta: 'El campo nombre debe ser texto',
          images: await Image.find({ category: 'a' })
        })
      default:
        console.error(error)
        return res.status(500).render('main', {
          alerta: 'Ocurrió un error inesperado',
          images: await Image.find({ category: 'a' })
        })
    }
  }
}

controller.loginFormAdmin = async (req, res) => {
  try {
    const data = req.body

    // Validación de admin_name
    if (typeof data.admin_name !== 'string' || !isNaN(data.admin_name)) {
      throw new Error('INVALID_ADMIN_NAME')
    }

    // Validación de admin_password
    if (typeof data.admin_password !== 'string') {
      throw new Error('INVALID_ADMIN_PASSWORD')
    }

    // Consulta a la base de datos
    const admin = await Admin.find({
      name: data.admin_name,
      password: data.admin_password
    })

    // Manejo de la respuesta de la consulta
    if (!admin.length) {
      return res.render('main', {
        alerta: 'Los datos ingresados no son correctos',
        images: await Image.find({ category: 'a' })
      })
    } else {
      console.log('El admin ha ingresado correctamente')
      return res.redirect('/admin')
    }
  } catch (error) {
    // Manejo de errores
    switch (error.message) {
      case 'INVALID_ADMIN_NAME':
        return res.render('main', {
          alerta: 'El campo nombre de admin debe ser texto',
          images: await Image.find({ category: 'a' })
        })
      case 'INVALID_ADMIN_PASSWORD':
        return res.render('main', {
          alerta: 'El campo contraseña debe ser texto',
          images: await Image.find({ category: 'a' })
        })
      default:
        console.error(error)
        return res.status(500).render('main', {
          alerta: 'Ocurrió un error inesperado',
          images: await Image.find({ category: 'a' })
        })
    }
  }
}

controller.subirProducto = async (req, res) => {
  console.log(req.body)
  try {
    const images = req.files.map(file => {
      return {
        name_product: req.body.name_product,
        description: req.body.description,
        category: req.body.category,
        price: req.body.price,
        quantity: req.body.quantity,
        brand: req.body.brand,
        filename: file.filename,
        path: '/public/img/' + file.filename,
        originalname: file.originalname,
        mimetype: file.mimetype,
        size: file.size,
        created_at: new Date()
      }
    })

    await Image.insertMany(images)

    res.redirect('/admin')
  } catch (err) {
    console.error('Error al subir imágenes:', err)
    res.status(500).send('Error al subir imágenes')
  }
}

module.exports = controller
