const controller = {};
const { ObjectId } = require("mongoose").Types;
const fs = require("fs");
const path = require("path");
const buildPDF = require("../config/pdfkit");


const Image = require("../models/image");
const Customer = require("../models/customer");
const Admin = require("../models/admin");
const Category = require("../models/category");
const Product = require("../models/product");
const Cart = require("../models/cart");
const Payment = require("../models/payment");
const LoginCustomer = require("../models/loginCustomer");



const getRandomProducts = (products, count) => {
  const shuffled = products.sort(() => 0.5 - Math.random());
  return shuffled.slice(0, count);
};
controller.main = async (req, res) => {
  const customerId = req.session.customerId || null;
  let productCart = [];

  try {
    // Obtener productos destacados
    const productShow = await Product.find().populate('imagePaths');

    if (customerId) {
      let cart = await Cart.findOne({ customer: customerId }).populate("products.product");

      if (!cart) {
        // Crear un nuevo carrito si no existe uno para el cliente
        const customerObjectId = new ObjectId(customerId);
        cart = await Cart.create({
          customer: customerObjectId,
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

    // Obtener todas las categorías
    const categories = await Category.find();

    // Crear un objeto para agrupar subcategorías por nombre de categoría
    const groupedCategories = {};
    categories.forEach((category) => {
      if (!groupedCategories[category.name]) {
        groupedCategories[category.name] = [];
      }
      // Añadir la subcategoría si no está ya en el array
      if (!groupedCategories[category.name].includes(category.subcategories)) {
        groupedCategories[category.name].push(category.subcategories);
      }
    });

    // Mapear las categorías para generar los URLs dinámicamente
    const categoryUrls = {};
    Object.keys(groupedCategories).forEach((categoryName) => {
      groupedCategories[categoryName].forEach((subcategory) => {
        const key = `${categoryName}-${subcategory}`;
        categoryUrls[key] = getCategoryUrl(categoryName, subcategory, customerId);
      });
    });

    // Convertir el objeto en un array de objetos para la vista
    const categoryData = Object.keys(groupedCategories).map(name => ({
      name,
      subcategories: groupedCategories[name]
    }));

    // Seleccionar 9 productos aleatoriamente
    const uniqueProductShow = Array.from(new Set(productShow.map(p => p._id)))
      .map(id => {
        return productShow.find(p => p._id.toString() === id.toString());
      });

    const randomProducts = getRandomProducts(uniqueProductShow, 9);

    console.log("categoryData:", categoryData);
    console.log("categoryUrls:", categoryUrls);

    res.render("main", {
      customerId,
      customerName: req.session.customerName || "No hay cliente logueado",
      productShow: randomProducts,
      productCart,
      categories: categoryData,
      categoryUrls,
      alert,
    });
  } catch (error) {
    console.error("Error en el controlador:", error);
    res.status(500).send(error.message);
  }
};



const getCategoryUrl = (categoryName, subcategory, customerId) => {
  return `/category/${categoryName}-${subcategory}?customerId=${customerId}`;
};
controller.getProductsByCategory = async (req, res) => {
  const [categoryName, subcategory] = req.params.category.split("-");
  const customerId = req.params.customerId || req.session.customerId;

  let productCart = [];

  try {
    // Buscar la categoría por nombre y subcategoría
    const category = await Category.findOne({
      name: categoryName,
      subcategories: subcategory,
    });

    if (!category) {
      return res.status(404).send("Categoría no encontrada");
    }

    // Obtener el ObjectId de la categoría encontrada
    const categoryId = category._id;

    // Buscar productos por el ObjectId de la categoría
    const productShow = await Product.find({
      category: categoryId,
    }).populate("imagePaths"); // Asegúrate de que `imagePaths` esté poblado

    let cart = [];
    if (customerId) {
      cart = await Cart.findOne({ customer: customerId }).populate("products.product");

      if (!cart) {
        // Crear un nuevo carrito si no existe uno para el cliente
        const customerObjectId = new ObjectId(customerId);
        cart = await Cart.create({
          customer: customerObjectId,
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

    const categories = await Category.find();
    // Crear un objeto para agrupar subcategorías por nombre de categoría
    const groupedCategories = {};
    categories.forEach((category) => {
      if (!groupedCategories[category.name]) {
        groupedCategories[category.name] = [];
      }
      if (!groupedCategories[category.name].includes(category.subcategories)) {
        groupedCategories[category.name].push(category.subcategories);
      }
    });

    // Mapear las categorías para generar los URLs dinámicamente
    const categoryUrls = {};
    Object.keys(groupedCategories).forEach((categoryName) => {
      groupedCategories[categoryName].forEach((subcategory) => {
        const key = `${categoryName}-${subcategory}`;
        categoryUrls[key] = getCategoryUrl(categoryName, subcategory, customerId);
      });
    });

    // Convertir el objeto en un array de objetos para la vista
    const categoryData = Object.keys(groupedCategories).map((name) => ({
      name,
      subcategories: groupedCategories[name],
    }));

    console.log("FUNCIONA");
    console.log(categoryData, categoryUrls);

    res.render("main", {
      customerId,
      customerName: req.session.customerName || "No hay cliente logueado",
      productShow,
      productCart,
      categories: categoryData,
      categoryUrls,
      alert,
    });
  } catch (error) {
    console.error("Error en controlador de categoría:", error);
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
    const product = await Product.findById(productId).populate("imagePaths"); // Pobla las imágenes del producto

    if (!product) {
      return res.status(404).send("Producto no encontrado");
    }

    res.render("detalles", {
      product,
    });
  } catch (error) {
    console.error("Error en detalles del producto:", error);
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

controller.deleteProductCart = async (req, res) => {
  const productId = req.params.productId;
  const customerId = req.session.customerId;

  try {
    let cart = await Cart.findOne({ customer: customerId });

    if (!cart) {
      req.session.alert = "Carrito no encontrado";
      return res.redirect("/");
    }

    // Filtrar el producto que se desea eliminar del carrito
    cart.products = cart.products.filter((item) => !item.product.equals(productId));

    // Guardar el carrito actualizado
    await cart.save();

    res.redirect("/");
  } catch (error) {
    console.error("Error eliminando el producto del carrito:", error);
    req.session.alert = "Error eliminando el producto del carrito";
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
  try {
    const products = await Product.find().populate("imagePaths").populate("category");
    const categories = await Category.find();

    res.render("dashboard", {
      products,
      categories,
      alert: req.session.alert,
    });

    delete req.session.alert;
  } catch (error) {
    console.error("Error al obtener productos o categorías:", error);
    res.status(500).send(error.message);
  }
};


// Función auxiliar para validar campos
const isValidField = (value, type) => {
  if (!value) return false;
  if (type === "string") return typeof value === "string" && value.trim() !== "";
  if (type === "number") return !isNaN(parseFloat(value));
  if (type === "ObjectId") return ObjectId.isValid(value);
  return false;
}
// Función para capitalizar las palabras
const capitalizeWords = (str) => {
  return str
    .toLowerCase()
    .split(" ")
    .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
    .join(" ");
}
// Función para formatear la descripción
const formatDescription = (description) => {
  return description.charAt(0).toUpperCase() + description.slice(1).toLowerCase();
}
controller.saveProduct = async (req, res) => {
  try {
    const { name_product, price, quantity, brand, category, description } = req.body;
    const [categoryId, subcategory] = category.split(" - "); // Descompón `category` en `categoryId` y `subcategory`

    // Validar campos
    if (
      !isValidField(name_product, "string") ||
      !isValidField(price, "number") ||
      !isValidField(quantity, "number") ||
      !isValidField(brand, "string") ||
      !isValidField(categoryId, "ObjectId") ||
      !isValidField(subcategory, "string") ||
      !isValidField(description, "string")
    ) {
      req.session.alert = "Todos los campos son requeridos y deben ser válidos";
      return res.redirect("/admin");
    }

    // Capitalizar las palabras en name_product
    const formattedNameProduct = capitalizeWords(name_product);

    // Formatear la descripción
    const formattedDescription = formatDescription(description);

    const existingProduct = await Product.findOne({
      name: formattedNameProduct,
      category: categoryId,
      brand,
      subcategory,
    });

    if (existingProduct) {
      req.session.alert = "El producto ya existe con el mismo nombre, categoría, marca y subcategoría.";
      return res.redirect("/admin");
    }

    const newProduct = new Product({
      name: formattedNameProduct,
      price: parseFloat(price),
      quantity: parseFloat(quantity),
      brand,
      category: categoryId,
      subcategory,
      description: formattedDescription,
    });

    let imageIds = [];
    if (req.files && req.files.length > 0) {
      for (const file of req.files) {
        if (file.size > 10 * 1024 * 1024) {
          req.session.alert = `La imagen ${file.originalname} excede el tamaño máximo permitido de 10MB`;
          return res.redirect("/admin");
        } else {
          const newImage = new Image({
            path: "/public/img/" + file.filename,
            original_name: file.originalname,
          });
          await newImage.save();
          imageIds.push(newImage._id);
        }
      }

      newProduct.imagePaths = imageIds;
      await newProduct.save();
    } else {
      req.session.alert = "Debe subir al menos una imagen";
      return res.redirect("/admin");
    }

    console.log("Producto subido:", newProduct);

    delete req.session.alert;
    return res.redirect("/admin");
  } catch (err) {
    console.error("Error al subir imágenes o guardar el producto:", err);
    req.session.alert = "Error al subir imágenes o guardar el producto";
    return res.redirect("/admin");
  }
};



controller.deleteProduct = async (req, res) => {
  try {
    const productId = req.params.id;

    // Encuentra el producto por su ID y popular las rutas de las imágenes
    const product = await Product.findById(productId).populate("imagePaths");

    if (!product) {
      req.session.alert = "Producto no encontrado";
      return res.redirect("/admin");
    }

    // Elimina las imágenes en la carpeta `public/img` y en la base de datos
    for (const imageId of product.imagePaths) {
      const image = await Image.findById(imageId);

      if (image) {
        const imagePath = path.join(__dirname, "..", "..", image.path);

        if (fs.existsSync(imagePath)) {
          fs.unlinkSync(imagePath);
        }

        await Image.deleteOne({ _id: imageId });
      }
    }

    // Elimina el producto
    await Product.deleteOne({ _id: productId });

    console.log("Producto eliminado:", product);
    return res.redirect("/admin");
  } catch (error) {
    console.error("Error al eliminar el producto:", error);
    req.session.alert = "Error al eliminar el producto";
    return res.status(500).send(error.message);
  }
};

controller.saveAdmin = async (req, res) => {
  try {
    const { name, password } = req.body;

    if (!name || typeof name !== "string" || name.trim() === "") {
      req.session.alert = "El nombre del admin no puede estar vacío";
    } else if (!password || typeof password !== "string" || password.trim() === "") {
      req.session.alert = "La contraseña del admin no puede estar vacía";
    } else if (password.length < 6) {
      req.session.alert = "La contraseña debe tener al menos 6 caracteres";
    }

    const newAdmin = new Admin({
      name,
      password,
    });

    await newAdmin.save();
    console.log("Admin creado:", newAdmin);

    res.redirect("/admin");
  } catch (err) {
    console.error("Error al crear admin:", err);
    res.status(500).send("Error al crear admin");
  }
};

controller.saveCategory = async (req, res) => {
  try {
    const { name, subcategories } = req.body;

    console.log(req.body);

    if (!name || typeof name !== "string" || name.trim() === "") {
      req.session.alert = "El nombre de la categoría no puede estar vacío";
      return res.redirect("/admin");
    }

    if (!subcategories || typeof subcategories !== "string" || subcategories.trim() === "") {
      req.session.alert = "Las subcategorías no pueden estar vacías";
      return res.redirect("/admin");
    }

    const subcategoriesArray = subcategories.split(",").map((item) => item.trim());

    for (const subcategory of subcategoriesArray) {
      // Buscar si la categoría con la subcategoría ya existe
      const existingCategory = await Category.findOne({ name: name.trim(), subcategories: subcategory });

      if (existingCategory) {
        req.session.alert = `La categoría ${name.trim()} con subcategoría ${subcategory} ya existe.`;
        continue; // Continúa con la siguiente subcategoría
      }

      // Crear una nueva categoría para cada subcategoría
      const newCategory = new Category({
        name: name.trim(),
        subcategories: subcategory,
      });

      await newCategory.save();
      console.log("Categoría creada:", newCategory);
    }

    delete req.session.alert;
    res.redirect("/admin");
  } catch (err) {
    console.error("Error al crear o mostrar la categoría:", err);
    req.session.alert = "Error al crear o mostrar la categoría";
    res.redirect("/admin");
  }
};

controller.deleteCaregory = async (req, res) => {
  console.log("lala");
  console.log("Eliminando categoría");
  try {
    const categoryId = req.params.id;
    console.log("ID de categoría para eliminar:", categoryId);

    const category = await Category.findById(categoryId);
    if (!category) {
      console.log("Categoría no encontrada");
      return res.status(404).send("Categoría no encontrada");
    }

    await Category.findByIdAndDelete(categoryId);
    console.log("Categoría eliminada: ", category);

    res.redirect("/admin");
  } catch (error) {
    console.error("Error al eliminar la categoría:", error);
    res.status(500).send("Error al eliminar la categoría");
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

      // verificar que los datos ingresador en el payment sean los mismo que los del login

      let loginCustomer = await LoginCustomer.findById(data.customer_id);
      if (loginCustomer.cedula !== data.cedula || loginCustomer.email !== data.email) {
        return res.render("formulario", {
          alert: "Los datos ingresados no coinciden con los datos de registro",
          customerId: data.customer_id,
        });
      } else {
        try {
          await customer.save();
          console.log("Datos del cliente guardados:", customer);
        } catch (error) {
          console.error("Error guardando los datos del cliente:", error);
          return res.status(500).send("Error guardando los datos del cliente");
        }
      }
    }

    // Crear una nueva instancia del modelo Payment y guardar los datos
    const paymentData = new Payment({
      customer_id: customer.login_customer_id,
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
  console.log("ID del cliente en proceso de pago: ", customerId);
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
  const customerId = req.params.customerId;
  try {
    const customer = await Customer.findOne({ login_customer_id: customerId });
    const cart = await Cart.findOne({ customer: customerId }).populate("products.product");
    const payment = await Payment.findOne({ customer_id: customerId });

    res.render("factura", {
      customer: customer.toObject(),
      cart,
      payment,
    });
  } catch (error) {
    console.error(error);
    res.status(500).send("Error al obtener los datos de la factura");
  }
};

controller.generatePDF = async (req, res) => {
  const customerId = req.params.customerId;
  try {
    const customer = await Customer.findOne({ login_customer_id: customerId }).exec();
    const cart = await Cart.findOne({ customer: customerId }).populate("products.product").exec();
    const payment = await Payment.findOne({ customer_id: customerId }).exec();

    if (!customer || !cart || !payment) {
      return res.status(404).send("Datos no encontrados");
    }

    const invoice = {
      purchaseDate: new Date().toLocaleDateString(),
      customer: customer.toObject(),
      cart: cart.toObject(),
      payment: payment.toObject(),
    };

    res.setHeader("Content-disposition", "attachment; filename=facturaUrbanStreet.pdf");
    res.setHeader("Content-type", "application/pdf");

    buildPDF(
      (chunk) => res.write(chunk),
      () => res.end(),
      invoice
    );
  } catch (error) {
    console.error(error);
    res.status(500).send("Error al obtener los datos de la factura");
  }
};

module.exports = controller;
