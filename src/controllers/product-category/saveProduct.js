const { ObjectId } = require('mongoose').Types;
const Category = require('../../models/category');
const Image = require('../../models/image');
const Product = require('../../models/product');

// Expresiones regulares
const esTextoNombreMarca = /^[A-Za-z\sáéíóúÁÉÍÓÚñÑ]+$/; // Permite solo letras y espacios para nombre y marca
const esTextoDescripcion = /^[A-Za-z\sáéíóúÁÉÍÓÚñÑ,.-]+$/; // Permite letras, espacios, comas y guiones para descripción
const esDecimal = /^-?\d+(\.\d+)?$/; // Permite números decimales y enteros

// Función auxiliar para validar campos
const isValidField = (value, type) => {
  if (!value) return false;

  switch (type) {
    case 'stringNombreMarca':
      return typeof value === 'string' && esTextoNombreMarca.test(value.trim()); // Validar nombre del producto y marca
    case 'stringDescripcion':
      return typeof value === 'string' && esTextoDescripcion.test(value.trim()); // Validar descripción
    case 'number':
      const numberValue = parseFloat(value);
      return esDecimal.test(value) && !isNaN(numberValue); // Verifica que el valor convertido sea un número válido
    case 'ObjectId':
      return ObjectId.isValid(value);
    default:
      return false;
  }
};

// Función para capitalizar las palabras
const capitalizeWords = str => {
  return str
    .toLowerCase()
    .split(' ')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ');
};

// Función para formatear la descripción
const formatDescription = description => {
  return description.charAt(0).toUpperCase() + description.slice(1).toLowerCase();
};

module.exports = async (req, res) => {
  try {
    const { name_product, price, quantity, brand, category, description } = req.body;
    const [categoryId, subcategory] = category.split(' - ');

    // Validaciones con mensajes específicos
    if (!isValidField(name_product, 'stringNombreMarca')) {
      req.session.alert = 'El nombre del producto es requerido.';
      return res.redirect('/admin');
    }
    if (!isValidField(price, 'number')) {
      req.session.alert = 'El precio debe ser un número válido.';
      return res.redirect('/admin');
    }
    if (!isValidField(quantity, 'number')) {
      req.session.alert = 'La cantidad debe ser un número válido.';
      return res.redirect('/admin');
    }
    if (!isValidField(brand, 'stringNombreMarca')) {
      req.session.alert = 'La marca es requerida y debe ser válida.';
      return res.redirect('/admin');
    }

    // Validar que la categoría exista en la base de datos
    const existingCategory = await Category.findById(categoryId);
    if (!existingCategory) {
      req.session.alert = 'La categoría especificada no existe.';
      return res.redirect('/admin');
    }

    if (!isValidField(subcategory, 'stringNombreMarca')) {
      req.session.alert = 'La subcategoría es requerida y debe ser válida.';
      return res.redirect('/admin');
    }
    if (!isValidField(description, 'stringDescripcion')) {
      req.session.alert = 'La descripción no es válida.';
      return res.redirect('/admin');
    }

    // Capitalizar las palabras en name_product y formatear la descripción
    const formattedNameProduct = capitalizeWords(name_product);
    const formattedDescription = formatDescription(description);

    // Verificar si el producto ya existe
    const existingProduct = await Product.findOne({
      name: formattedNameProduct,
      category: categoryId,
      brand,
      subcategory
    });

    if (existingProduct) {
      req.session.alert = 'El producto ya existe con el mismo nombre, categoría, marca y subcategoría.';
      return res.redirect('/admin');
    }

    // Manejo de imágenes
    let imageIds = [];
    if (req.files && req.files.length > 0) {
      if (req.files.length < 3 || req.files.length > 5) {
        req.session.alert = 'Debe subir entre 3 y 5 imágenes.';
        return res.redirect('/admin');
      }

      for (const file of req.files) {
        if (file.size > 10 * 1024 * 1024) {
          req.session.alert = `La imagen ${file.originalname} excede el tamaño máximo permitido de 10MB.`;
          return res.redirect('/admin');
        } else {
          const newImage = new Image({
            path: '/public/img/' + file.filename,
            original_name: file.originalname
          });
          await newImage.save();
          imageIds.push(newImage._id);
        }
      }
    } else {
      req.session.alert = 'Debe subir al menos una imagen.';
      return res.redirect('/admin');
    }

    // Crear un nuevo producto
    const newProduct = new Product({
      name: formattedNameProduct,
      price: parseFloat(price),
      quantity: parseFloat(quantity),
      brand,
      category: categoryId,
      subcategory,
      description: formattedDescription,
      imagePaths: imageIds
    });

    // Guardar el nuevo producto
    await newProduct.save();
    // console.log('Producto subido:', newProduct);

    delete req.session.alert;
    return res.redirect('/admin');
  } catch (err) {
    // Manejo del error específico de clave duplicada
    if (err.code === 11000) {
      req.session.alert = 'El producto ya existe con el mismo nombre.';
    } else {
      console.error('Error al subir imágenes o guardar el producto:', err);
      req.session.alert = 'Error al subir imágenes o guardar el producto.';
    }
    return res.redirect('/admin');
  }
};
