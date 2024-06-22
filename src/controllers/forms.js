const controller = {}
const Image = require('../models/images')
const Customers = require('../models/customers')
const Admin = require('../models/admins')

controller.main = async (req, res) => {
  const images = await Image.find({ category: 'dw' })
  console.log(images)
  res.render('main', { images })
}

controller.admin = (req, res) => {
  res.render('dashboard')
}
  
controller.details = (req, res) => {
  res.render('detalles')
}

controller.loginFormCustomer = async (req, res) => {
  try {
    const data = req.body;

    // Validación de cédula
    const cedulaCustomer = Number(data.cedula_Customer);
    if (isNaN(cedulaCustomer)) {
      throw new Error("INVALID_CEDULA");
    }

    // Validación de first_name
    if (typeof data.first_name !== 'string' || !isNaN(data.first_name)) {
      throw new Error("INVALID_FIRST_NAME");
    }

    // Consulta a la base de datos
    const cliente = await Customers.find({
      first_name: data.first_name,
      email: data.email_Customer,
      cedula: cedulaCustomer
    });

    // Manejo de la respuesta de la consulta
    if (!cliente.length) {
      return res.render('main', {
        alerta: "Los datos ingresados no son correctos",
        images: await Image.find({ category: 'a' })
      });
    } else {
      console.log("El cliente ha ingresado correctamente");
      return res.render('main', {
        images: await Image.find({ category: 'a' })
      });
    }
  } catch (error) {
    // Manejo de errores
    switch (error.message) {
      case "INVALID_CEDULA":
        return res.render('main', {
          alerta: "El campo cédula debe ser un número",
          images: await Image.find({ category: 'a' })
        });
      case "INVALID_FIRST_NAME":
        return res.render('main', {
          alerta: "El campo nombre debe ser texto",
          images: await Image.find({ category: 'a' })
        });
      default:
        console.error(error);
        return res.status(500).render('main', {
          alerta: "Ocurrió un error inesperado",
          images: await Image.find({ category: 'a' })
        });
    }
  }
};


controller.loginFormAdmin = async (req, res) => {
  try {
    const data = req.body;

    // Validación de admin_name
    if (typeof data.admin_name !== 'string' || !isNaN(data.admin_name)) {
      throw new Error("INVALID_ADMIN_NAME");
    }

    // Validación de admin_password
    if (typeof data.admin_password !== 'string') {
      throw new Error("INVALID_ADMIN_PASSWORD");
    }

    // Consulta a la base de datos
    const admin = await Admin.find({
      name: data.admin_name,
      password: data.admin_password
    });

    // Manejo de la respuesta de la consulta
    if (!admin.length) {
      return res.render('main', {
        alerta: "Los datos ingresados no son correctos",
        images: await Image.find({ category: 'a' })
      });
    } else {
      console.log("El admin ha ingresado correctamente");
      return res.redirect('/admin');
    }
  } catch (error) {
    // Manejo de errores
    switch (error.message) {
      case "INVALID_ADMIN_NAME":
        return res.render('main', {
          alerta: "El campo nombre de admin debe ser texto",
          images: await Image.find({ category: 'a' })
        });
      case "INVALID_ADMIN_PASSWORD":
        return res.render('main', {
          alerta: "El campo contraseña debe ser texto",
          images: await Image.find({ category: 'a' })
        });
      default:
        console.error(error);
        return res.status(500).render('main', {
          alerta: "Ocurrió un error inesperado",
          images: await Image.find({ category: 'a' })
        });
    }
  }
};

controller.subirProducto = async (req, res) => {
  console.log(req.body);
  const image = new Image(req.body);
  image.name_product = req.body.name_product;
  image.description = req.body.description;
  image.category = req.body.category;
  image.price = req.body.price;
  image.quantity = req.body.quantity;
  image.brand = req.body.brand;
  image.filename = req.file.filename;
  image.path = '/public/img/' + req.file.filename;
  image.originalname = req.file.originalname;
  image.mimetype = req.file.mimetype;
  image.size = req.file.size;

  await image.save();

  res.redirect('/admin');
}

module.exports = controller