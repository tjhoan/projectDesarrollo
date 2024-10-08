const mongoose = require('mongoose');
const fs = require('fs');
const path = require('path');
const Product = require('../../../models/product');
const Image = require('../../../models/image');
const Category = require('../../../models/category'); // Importar el modelo de categoría
const deleteProduct = require('../../../controllers/product-category/deleteProduct');
const httpMocks = require('node-mocks-http');

beforeAll(async () => {
  const DB_URI = process.env.DB_URI || 'mongodb://localhost:27017/project_desarrollo_test';
  if (mongoose.connection.readyState === 0) {
    await mongoose.connect(DB_URI);
  }
});

afterAll(async () => {
  await mongoose.connection.close();
});

describe('Pruebas Unitarias para deleteProduct', () => {
  let req, res, category;

  beforeEach(async () => {
    req = httpMocks.createRequest();
    res = httpMocks.createResponse();
    req.session = {};

    category = await Category.create({ name: 'Categoría Prueba', subcategories: 'Camisas' });
  });

  afterEach(async () => {
    await Product.deleteMany();
    await Image.deleteMany();
    await Category.deleteMany(); // Limpiar categorías después de cada prueba
  });

  it('debería eliminar un producto existente y sus imágenes', async () => {
    const image1 = await Image.create({ path: 'public/img/image1.jpg', original_name: 'image1.jpg' });
    const image2 = await Image.create({ path: 'public/img/image2.jpg', original_name: 'image2.jpg' });
    const product = await Product.create({
      name: 'Camisa',
      price: 5000,
      quantity: 10,
      brand: 'Marca X',
      category: category._id, // Usar la categoría creada en el beforeEach
      description: 'Una camisa de prueba',
      imagePaths: [image1._id, image2._id]
    });

    req.params.id = product._id;

    jest.spyOn(fs, 'existsSync').mockReturnValue(true);
    jest.spyOn(fs, 'unlinkSync').mockImplementation(() => {});

    await deleteProduct(req, res);

    const deletedProduct = await Product.findById(product._id);
    const deletedImage1 = await Image.findById(image1._id);
    const deletedImage2 = await Image.findById(image2._id);

    expect(deletedProduct).toBeNull();
    expect(deletedImage1).toBeNull();
    expect(deletedImage2).toBeNull();
    expect(res.statusCode).toBe(200);
    expect(res._getJSONData().message).toBe('Producto eliminado correctamente');
  });

  it('debería manejar el caso cuando el producto no existe', async () => {
    req.params.id = new mongoose.Types.ObjectId();

    await deleteProduct(req, res);

    expect(req.session.alert).toBe('Producto no encontrado');
    expect(res.statusCode).toBe(404);
    expect(res._getJSONData().message).toBe('Producto no encontrado');
  });

  it('debería manejar errores del sistema de archivos durante la eliminación', async () => {
    const image1 = await Image.create({ path: 'public/img/image1.jpg', original_name: 'image1.jpg' });
    const product = await Product.create({
      name: 'Camisa',
      price: 5000,
      quantity: 10,
      brand: 'Marca X',
      category: category._id, // Usar la categoría creada en el beforeEach
      description: 'Una camisa de prueba',
      imagePaths: [image1._id]
    });

    req.params.id = product._id;

    jest.spyOn(fs, 'existsSync').mockReturnValue(true);
    jest.spyOn(fs, 'unlinkSync').mockImplementation(() => {
      throw new Error('Error al eliminar el archivo');
    });

    await deleteProduct(req, res);

    expect(req.session.alert).toBe('Error al eliminar la imagen del producto');
    expect(res.statusCode).toBe(500);
    expect(res._getJSONData().message).toBe('Error al eliminar la imagen del producto');
  });

  it('debería manejar el caso cuando la imagen no existe en el sistema de archivos', async () => {
    const image = await Image.create({ path: 'public/img/missing_image.jpg', original_name: 'missing_image.jpg' });
    const product = await Product.create({
      name: 'Camisa',
      price: 5000,
      quantity: 10,
      brand: 'Marca X',
      category: category._id, // Usar la categoría creada en el beforeEach
      description: 'Una camisa de prueba',
      imagePaths: [image._id]
    });

    req.params.id = product._id;

    jest.spyOn(fs, 'existsSync').mockReturnValue(false);

    await deleteProduct(req, res);

    const deletedProduct = await Product.findById(product._id);
    const deletedImage = await Image.findById(image._id);

    expect(deletedProduct).toBeNull();
    expect(deletedImage).toBeNull();
    expect(res.statusCode).toBe(200);
    expect(res._getJSONData().message).toBe('Producto eliminado correctamente');
  });
});
