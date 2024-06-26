const controller = {}
const { ObjectId } = require('mongoose').Types
const Image = require('../models/images')
const Customers = require('../models/customers')
const Admin = require('../models/admins')
const Product = require('../models/products')
const Cart = require('../models/cart')

controller.main = async (req, res) => {
  let alerta = req.session.alerta
  const productShow = await Product.find()
  let productCart = []
  try {
    if (req.session.customerId) {
      try {
        let cart = await Cart.findOne({ customer: req.session.customerId }).populate('products.product')

        if (!cart) {
          const customerId = new ObjectId(req.session.customerId)
          cart = await Cart.create({
            customer: customerId,
            products: []
          })
        }

        if (cart.products.length > 0) {
          for (const product of cart.products) {
            const productDetails = await Product.findById(product.product)
            if (productDetails) {
              productCart.push({
                details: productDetails,
                quantity: product.quantity
              })
            }
          }
        }
      } catch (error) {
        console.error('Error fetching cart or products:', error)
        return res.status(500).send(error.message)
      }
    }

    res.render('main', {
      customerId: req.session.customerId || 'No customer id',
      customerName: req.session.customerName || 'No customer name',
      productShow,
      productCart,
      alerta
    })
  } catch (error) {
    res.status(500).send(error.message)
  }
}

controller.addCart = async (req, res) => {
  try {
    const productId = req.params.id
    const product = await Product.findById(productId)
    const customerId = req.session.customerId

    if (!product) {
      return res.status(404).send('Product not found')
    }

    if (!customerId) {
      console.log('Customer not logged in')
      req.session.alerta = 'Customer not logged in'
      return res.redirect('/')
    }

    let cart = await Cart.findOne({ customer: new ObjectId(customerId) })

    if (!cart) {
      cart = new Cart({
        customer: new ObjectId(customerId),
        products: []
      })
    }

    const existingProductIndex = cart.products.findIndex(p => p.product.equals(new ObjectId(productId)))

    if (existingProductIndex > -1) {
      cart.products[existingProductIndex].quantity += 1
    } else {
      cart.products.push({
        product: new ObjectId(productId),
        quantity: 1
      })
    }

    await cart.save()

    res.redirect('/')
  } catch (error) {
    console.error('Error adding product to cart:', error)
    res.status(500).send(error.message)
  }
}


controller.destroySession = (req, res) => {
  console.log('El cliente ha cerrado sesión')
  req.session.destroy(err => {
    if (err) {
      console.error('Error destroying session:', err);
      return res.status(500).send('Error destroying session');
    }
    res.redirect('/');
  });
};

controller.admin = (req, res) => {
  res.render('dashboard')
}

controller.details = (req, res) => {
  res.render('detalles')
}

controller.loginFormCustomer = async (req, res) => {
  try {
    const data = req.body

    const cedulaCustomer = Number(data.cedula_Customer)
    if (isNaN(cedulaCustomer)) {
      req.session.alerta = 'El campo cédula debe ser un número'
      return res.redirect('/')
    }

    if (typeof data.first_name !== 'string' || !isNaN(data.first_name)) {
      req.session.alerta = 'El campo nombre debe ser texto'
      return res.redirect('/')
    }

    const cliente = await Customers.find({
      first_name: data.first_name,
      email: data.email_Customer,
      cedula: cedulaCustomer
    })

    if (!cliente.length) {
      req.session.alerta = 'Los datos ingresados no son correctos'
      return res.redirect('/')
    } else {
      req.session.customerId = cliente[0]._id.toString()
      req.session.customerName = cliente[0].first_name
      console.log('El cliente ha ingresado correctamente')
      return res.redirect('/')
    }
  } catch (error) {
    console.error(error)
    req.session.alerta = 'Ocurrió un error inesperado'
    return res.redirect('/')
  }
}

controller.loginFormAdmin = async (req, res) => {
  try {
    const data = req.body

    if (typeof data.admin_name !== 'string' || !isNaN(data.admin_name)) {
      throw new Error('INVALID_ADMIN_NAME')
    }

    if (typeof data.admin_password !== 'string') {
      throw new Error('INVALID_ADMIN_PASSWORD')
    }

    const admin = await Admin.find({
      name: data.admin_name,
      password: data.admin_password
    })

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
