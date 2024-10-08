const mongoose = require('mongoose');
const Product = require('../../../models/product');
const Category = require('../../../models/category');
const Image = require('../../../models/image');
const saveProduct = require('../../../controllers/product-category/saveProduct');
const httpMocks = require('node-mocks-http');

describe('Pruebas Unitarias para saveProduct', () => {
  let category;

  // Configuración inicial de la base de datos
  beforeAll(async () => {
    const DB_URI = process.env.DB_URI || 'mongodb://localhost:27017/project_desarrollo_test';
    if (mongoose.connection.readyState === 0) {
      await mongoose.connect(DB_URI);
    }
  });

  beforeEach(async () => {
    category = new Category({ name: 'Ropa', subcategories: 'Camisas' });
    await category.save();
  });

  afterEach(async () => {
    await Product.deleteMany();
    await Category.deleteMany();
    await Image.deleteMany();
  });

  afterAll(async () => {
    await mongoose.connection.close();
  });

  it('debería crear un producto correctamente', async () => {
    const req = httpMocks.createRequest({
      method: 'POST',
      url: '/admin/product',
      body: {
        name_product: 'Camisa De Prueba',
        price: 5000,
        quantity: 10,
        brand: 'Marca X',
        category: `${category._id.toString()} - Camisas`,
        description: 'Una camisa de prueba'
      },
      files: [
        { filename: 'image1.jpg', originalname: 'image1.jpg', size: 1024 * 1024 },
        { filename: 'image2.jpg', originalname: 'image2.jpg', size: 1024 * 1024 },
        { filename: 'image3.jpg', originalname: 'image3.jpg', size: 1024 * 1024 }
      ]
    });
    const res = httpMocks.createResponse();
    req.session = {};

    await saveProduct(req, res);

    // Recargar la categoría después de la creación para garantizar que los datos se guardaron correctamente
    const categoryReloaded = await Category.findById(category._id).populate('subcategories');
    expect(categoryReloaded).not.toBeNull();

    const product = await Product.findOne({ name: 'Camisa De Prueba' }).populate('category').populate('imagePaths');
    expect(product).not.toBeNull();
    expect(product.price).toBe(5000);
    expect(product.quantity).toBe(10);
    expect(product.brand).toBe('Marca X');
    expect(product.description).toBe('Una camisa de prueba');
    expect(product.category.subcategories).toContain('Camisas');
    expect(product.imagePaths).toHaveLength(3);
  });

  it('debería devolver un error si la categoría no existe', async () => {
    const fakeCategoryId = new mongoose.Types.ObjectId();
    const req = httpMocks.createRequest({
      method: 'POST',
      url: '/admin/product',
      body: {
        name_product: 'Camisa De Prueba',
        price: 5000,
        quantity: 10,
        brand: 'Marca X',
        category: `${fakeCategoryId.toString()} - Camisas`,
        description: 'Una camisa de prueba'
      },
      files: [
        { filename: 'image1.jpg', size: 5000, originalname: 'image1.jpg' },
        { filename: 'image2.jpg', size: 5000, originalname: 'image2.jpg' },
        { filename: 'image3.jpg', size: 5000, originalname: 'image3.jpg' }
      ]
    });
    const res = httpMocks.createResponse();
    req.session = {};

    await saveProduct(req, res);

    expect(req.session.alert).toBe('La categoría especificada no existe.');
    expect(res._getRedirectUrl()).toBe('/admin');
  });

  it('debería devolver un error si no se envían imágenes', async () => {
    const req = httpMocks.createRequest({
      method: 'POST',
      url: '/admin/product',
      body: {
        name_product: 'Camisa De Prueba',
        price: 5000,
        quantity: 10,
        brand: 'Marca X',
        category: `${category._id.toString()} - Camisas`,
        description: 'Una camisa de prueba'
      },
      files: []
    });
    const res = httpMocks.createResponse();
    req.session = {};

    await saveProduct(req, res);

    expect(req.session.alert).toBe('Debe subir al menos una imagen.');
    expect(res._getRedirectUrl()).toBe('/admin');
  });

  it('debería devolver un error si se envían más de 5 imágenes', async () => {
    const req = httpMocks.createRequest({
      method: 'POST',
      url: '/admin/product',
      body: {
        name_product: 'Camisa De Prueba',
        price: 5000,
        quantity: 10,
        brand: 'Marca X',
        category: `${category._id.toString()} - Camisas`,
        description: 'Una camisa de prueba'
      },
      files: [
        { filename: 'image1.jpg', originalname: 'image1.jpg', size: 1024 * 1024 },
        { filename: 'image2.jpg', originalname: 'image2.jpg', size: 1024 * 1024 },
        { filename: 'image3.jpg', originalname: 'image3.jpg', size: 1024 * 1024 },
        { filename: 'image4.jpg', originalname: 'image4.jpg', size: 1024 * 1024 },
        { filename: 'image5.jpg', originalname: 'image5.jpg', size: 1024 * 1024 },
        { filename: 'image6.jpg', originalname: 'image6.jpg', size: 1024 * 1024 }
      ] 
    });
    const res = httpMocks.createResponse();
    req.session = {};

    await saveProduct(req, res);

    expect(req.session.alert).toBe('Debe subir entre 3 y 5 imágenes.');
    expect(res._getRedirectUrl()).toBe('/admin');
  });
});
