const controller = {};
const { ObjectId } = require("mongoose").Types;
const Image = require("../models/image");
const Customer = require("../models/customer");
const Admin = require("../models/admin");
const Product = require("../models/product");
const Cart = require("../models/cart");
const Payment = require("../models/payment");
const LoginCustomer = require("../models/loginCustomer");

controller.main = async (req, res) => {
  const productShow = await Product.find();
  let productCart = [];

  try {
    if (req.session.customerId) {
      let cart = await Cart.findOne({ customer: req.session.customerId }).populate("products.product");

      if (!cart) {
        const customerId = new ObjectId(req.session.customerId);
        cart = await Cart.create({
          customer: customerId,
          products: [],
        });
      }

      productCart = await Promise.all(
        cart.products.map(async (product) => {
          const productDetails = await Product.findById(product.product);
          if (productDetails) {
            return {
              details: productDetails,
              quantity: product.quantity,
            };
          }
          return null;
        })
      );
      productCart = productCart.filter((product) => product !== null);
    }

    const alert = req.session.alert;
    req.session.alert = undefined;

    res.render("main", {
      customerId: req.session.customerId || "No hay cliente logueado",
      customerName: req.session.customerName || "No hay cliente logueado",
      productShow,
      productCart,
      alert,
    });
  } catch (error) {
    console.error("Error en controlador principal:", error);
    res.status(500).send(error.message);
  }
};

controller.goToMain = (req, res) => {
  req.session.alert = undefined;
  res.redirect("/");
};

controller.detailsProduct = async (req, res) => {
  try {
    const productId = req.params.id;
    const product = await Product.findById(productId);
    if (!product) {
      return res.status(404).send("Producto no encontrado");
    }
    res.render("detalles", {
      product,
    });
  } catch (error) {
    console.error(error);
    return res.status(500).send(error.message);
  }
};

controller.addCart = async (req, res) => {
  try {
    const productId = req.params.id;
    const product = await Product.findById(productId);
    const customerId = req.session.customerId;

    if (!product) {
      req.session.alert = "Producto no encontrado";
      return res.redirect("/");
    }

    if (!customerId) {
      console.log("cliente no logueado");
      req.session.alert = "cliente no logueado";
      return res.redirect("/");
    }

    let cart = await Cart.findOne({ customer: new ObjectId(customerId) });

    if (!cart) {
      cart = new Cart({
        customer: new ObjectId(customerId),
        products: [],
      });
    }

    const existingProductIndex = cart.products.findIndex((p) => p.product.equals(new ObjectId(productId)));

    if (existingProductIndex > -1) {
      cart.products[existingProductIndex].quantity += 1;
    } else {
      cart.products.push({
        product: new ObjectId(productId),
        quantity: 1,
      });
    }

    await cart.save();

    console.log("Producto añadido al carrito: ", product.name);
    res.redirect("/");
  } catch (error) {
    console.error("Error agregando el producto al carrito: ", error);
    req.session.alert = "Error agregando el producto al carrito";
    res.redirect("/");
  }
};

controller.destroySession = (req, res) => {
  console.log("El cliente ha cerrado sesion");
  req.session.destroy((err) => {
    if (err) {
      console.error("Hubo un error cerrando sesion:", err);
      return res.status(500).send("Error cerrando sesion");
    }
    res.redirect("/");
  });
};

controller.loginFormCustomer = async (req, res) => {
  req.session.alert = undefined;
  try {
    const data = req.body;
    console.log("data: " + JSON.stringify(data, null, 2));
    console.log(typeof data);

    // Validaciones
    if (!data.cedula_customer || isNaN(data.cedula_customer)) {
      req.session.alert = "El campo cédula debe ser un número y no puede estar vacío";
      return res.redirect("/");
    }

    if (!data.first_name || typeof data.first_name !== "string" || !isNaN(data.first_name)) {
      req.session.alert = "El campo nombre debe ser texto y no puede estar vacío";
      return res.redirect("/");
    }

    if (!data.email_customer || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(data.email_customer)) {
      req.session.alert = "El campo email no puede estar vacío y debe tener un formato válido";
      return res.redirect("/");
    }

    let loginCustomer = await LoginCustomer.findOne({
      first_name: data.first_name,
      email: data.email_customer,
      cedula: data.cedula_customer,
    });

    if (!loginCustomer) {
      loginCustomer = new LoginCustomer({
        first_name: data.first_name,
        email: data.email_customer,
        cedula: data.cedula_customer,
      });
      await loginCustomer.save();
      console.log("Nuevo cliente creado");
    }

    req.session.customerId = loginCustomer._id.toString();
    req.session.customerName = loginCustomer.first_name;
    console.log("El cliente ha ingresado correctamente");

    return res.redirect("/");
  } catch (error) {
    console.error(error);
    req.session.alert = "Ocurrió un error inesperado";
    return res.redirect("/");
  }
};

controller.loginFormAdmin = async (req, res) => {
  try {
    const data = req.body;

    // Validaciones
    if (!data.admin_name || typeof data.admin_name !== "string" || !isNaN(data.admin_name)) {
      req.session.alert = "El campo nombre debe ser texto y no puede estar vacío";
      return res.redirect("/");
    }

    if (!data.admin_password || typeof data.admin_password !== "string") {
      req.session.alert = "El campo contraseña debe ser texto y no puede estar vacío";
      return res.redirect("/");
    }

    const admin = await Admin.find({
      name: data.admin_name,
      password: data.admin_password,
    });

    if (!admin.length) {
      req.session.alert = "Los datos ingresados no son correctos";
      return res.redirect("/");
    } else {
      console.log("El admin ha ingresado correctamente");
      req.session.alert = undefined;
      return res.redirect("/admin");
    }
  } catch (error) {
    console.error(error);
    req.session.alert = "Ocurrió un error inesperado";
    return res.redirect("/");
  }
};

controller.details = (req, res) => {
  res.render("detalles");
};

controller.admin = async (req, res) => {
  const product = await Product.find();
  res.render("dashboard", {
    product,
  });
};

controller.subirProducto = async (req, res) => {
  try {
    const imageDocs = await Promise.all(
      req.files.map(async (file) => {
        const newImage = new Image({
          path: "/public/img/" + file.filename,
          originalname: file.originalname,
        });
        await newImage.save();
        return {
          path: newImage.path,
        };
      })
    );

    const imagePaths = imageDocs.map((doc) => doc.path);

    const newProduct = new Product({
      name: req.body.name_product,
      price: req.body.price,
      quantity: req.body.quantity,
      brand: req.body.brand,
      category: req.body.category,
      description: req.body.description,
      imagePaths: imagePaths,
    });

    await newProduct.save();
    console.log("Producto subido:", newProduct);

    res.redirect("/admin");
  } catch (err) {
    console.error("Error al subir imágenes:", err);
    res.status(500).send("Error al subir imágenes");
  }
};

controller.crearCategoria = async (req, res) => {
  try {
    const data = req.body;
  } catch (error) {
    console.error(error);
    return res.status(500).send(error.message);
  }
};

controller.deleteProduct = async (req, res) => {
  try {
    const productId = req.params.id;
    const product = await Product.findByIdAndDelete(productId);
    if (!product) {
      return res.status(404).send("Producto no encontrado");
    }
    console.log("Producto eliminado: ", product);
    res.redirect("/admin");
  } catch (error) {
    console.error(error);
    return res.status(500).send(error.message);
  }
};

controller.formulario = (req, res) => {
  res.render("formulario", { customerId: req.params.customerId });
};

controller.paymentForm = async (req, res) => {
  const data = req.body;
  const esTexto = /^[A-Za-z\sáéíóúÁÉÍÓÚñÑ]+$/;

  // Funciones de validación auxiliares
  const validateNumericField = (value, fieldName, minLength) => {
    if (isNaN(value)) {
      return { valid: false, message: `El ${fieldName} debe ser numérico` };
    } else if (value.toString().length < minLength) {
      return { valid: false, message: `El ${fieldName} debe tener al menos ${minLength} dígitos` };
    } else if (value.trim() === "") {
      return { valid: false, message: `El ${fieldName} no puede estar vacío` };
    }
    return { valid: true };
  };

  const validateTextField = (value, fieldName, regex) => {
    if (typeof value !== "string" || !regex.test(value)) {
      return { valid: false, message: `El ${fieldName} debe ser texto y no contener números` };
    } else if (value.trim() === "") {
      return { valid: false, message: `El ${fieldName} no puede estar vacío` };
    }
    return { valid: true };
  };

  // Reglas de validación por campo
  const validationRules = {
    first_name: (value) => validateTextField(value, "primer nombre", esTexto),
    last_name: (value) => validateTextField(value, "apellido", esTexto),
    email: (value) => validateTextField(value, "email", /^[^\s@]+@[^\s@]+\.[^\s@]+$/),
    country: (value) => validateTextField(value, "país", esTexto),
    city: (value) => validateTextField(value, "ciudad", esTexto),
    neighborhood: (value) => validateTextField(value, "barrio", esTexto),
    banc: (value) => validateTextField(value, "banco", esTexto),
    payment_type: (value) => validateTextField(value, "tipo de pago", esTexto),
    cedula: (value) => validateNumericField(value, "cédula", 6),
    phone: (value) => validateNumericField(value, "teléfono", 7),
    postal_code: (value) => validateNumericField(value, "código postal", 4),
    account_number: (value) => validateNumericField(value, "número de cuenta", 10),
    second_name: (value) => {
      if (typeof value !== "string" || (value !== "" && !esTexto.test(value))) {
        return { valid: false, message: "El segundo nombre debe ser texto" };
      }
      return { valid: true };
    },
    genre: (value) => {
      if (typeof value !== "string" || (value !== "" && !esTexto.test(value))) {
        return { valid: false, message: "El género debe ser texto" };
      } else if (value.trim() === "") {
        return { valid: false, message: "El género no puede estar vacío" };
      } else if (value !== "Masculino" && value !== "Femenino" && value !== "otros") {
        return { valid: false, message: "El género debe ser Masculino, Femenino u otros" };
      }
      return { valid: true };
    },
    shipping_address: (value) => {
      if (typeof value !== "string" || value.trim() === "") {
        return { valid: false, message: "La dirección de envío no puede estar vacía" };
      }
      return { valid: true };
    },
    geo_location: (value) => {
      if (value.trim() === "") {
        return { valid: false, message: "La geolocalización no puede estar vacía" };
      }
      return { valid: true };
    },
  };

  // Función para validar los datos del pago
  const validatePaymentData = (data) => {
    for (const [key, value] of Object.entries(data)) {
      if (key === "customer_id") {
        continue; // Saltar la validación para customerId
      }

      if (validationRules[key]) {
        const validationResult = validationRules[key](value);
        if (!validationResult.valid) {
          return { valid: false, message: validationResult.message };
        }
      } else {
        return { valid: false, message: `Campo ${key} no reconocido` };
      }
    }
    return { valid: true }; // Si todos los campos son válidos, permite continuar con el guardado
  };

  // Validar los datos del pago
  const validation = validatePaymentData(data);
  if (!validation.valid) {
    // Si la validación falla, renderizar la vista del formulario con la alert correspondiente
    return res.render("formulario", {
      alert: validation.message,
      customerId: data.customer_id,
    });
  } else {
    // Verificar si el cliente ya existe
    let customer = await Customer.findOne({ cedula: data.cedula });

    if (!customer) {
      const address = `${data.shipping_address}, ${data.neighborhood}, ${data.city}, ${data.country}`;
      customer = new Customer({
        login_customer_id: data.customer_id,
        first_name: data.first_name,
        second_name: data.second_name,
        last_name: data.last_name,
        email: data.email,
        cedula: data.cedula,
        address: address,
        phone: data.phone,
        genre: data.genre,
      });

      try {
        await customer.save();
        console.log("Datos del cliente guardados:", customer);
      } catch (error) {
        console.error("Error guardando los datos del cliente:", error);
        return res.status(500).send("Error guardando los datos del cliente");
      }
    }

    // Crear una nueva instancia del modelo Payment y guardar los datos
    const paymentData = new Payment({
      customer_id: customer._id,
      country: data.country,
      city: data.city,
      neighborhood: data.neighborhood,
      shipping_address: data.shipping_address,
      geo_location: data.geo_location,
      postal_code: data.postal_code,
      banc: data.banc,
      payment_type: data.payment_type,
      account_number: data.account_number,
    });

    try {
      await paymentData.save();
      console.log("Datos de pago guardados:", paymentData);
    } catch (error) {
      let errorMessage = "Error al guardar los datos";
      if (error.code === 11000) {
        // Error de clave duplicada
        if (error.keyValue.cedula) {
          errorMessage = `La cédula ${error.keyValue.cedula} ya está registrada.`;
        } else if (error.keyValue.email) {
          errorMessage = `El email ${error.keyValue.email} ya está registrado.`;
        }
      }
      res.render("formulario", {
        alert: errorMessage,
        customerId: data.customer_id,
      });
    }
    res.redirect(`/procesoPago/${customer.login_customer_id}`);
  }
};

controller.procesoPago = async (req, res) => {
  const customerId = req.params.customerId;
  try {
    const cart = await Cart.findOne({ customer: new ObjectId(customerId) }).populate("products.product");

    const products = cart.products
      .filter((item) => item.product !== null)
      .map((item) => ({
        name: item.product.name,
        price: item.product.price,
        quantity: item.quantity,
        subtotal: item.product.price * item.quantity,
      }));

    console.log("Productos en cancelacion de compra: ", JSON.stringify(products, null, 2));

    const totalAmount = products.reduce((acc, item) => acc + item.subtotal, 0);

    res.render("procesoPago", { customerId, products, totalAmount });
  } catch (error) {
    console.error("Error al obtener el carrito:", error);
    res.status(500).send("Error al obtener el carrito");
  }
};

controller.factura = async (req, res) => {
  res.render("factura");
};

module.exports = controller;
