const mongoose = require('mongoose');
const Category = require('../../../models/category');
const saveCategory = require('../../../controllers/product-category/saveCategory');
const httpMocks = require('node-mocks-http');

// Configuración inicial y final de la base de datos de pruebas
beforeAll(async () => {
  const DB_URI = process.env.DB_URI || 'mongodb://localhost:27017/project_desarrollo_test';
  if (mongoose.connection.readyState === 0) {
    await mongoose.connect(DB_URI);
  }
});

afterAll(async () => {
  await mongoose.connection.close();
});

describe('Pruebas Unitarias para saveCategory', () => {
  let req, res;

  beforeEach(async () => {
    req = httpMocks.createRequest();
    res = httpMocks.createResponse();
    req.session = {}; // Inicializa la sesión
    await Category.deleteMany(); // Limpia la colección antes de cada prueba
  });

  afterEach(async () => {
    await Category.deleteMany(); // Limpia la colección después de cada prueba
  });

  it('debería crear una categoría con un nombre y subcategorías válidos', async () => {
    req.body = { name: 'Ropa', subcategories: 'Niños,Mujeres,Hombres' }; // Simular una solicitud con nombre y subcategor

    await saveCategory(req, res);

    const categories = await Category.find({ name: 'Ropa' }); 
    expect(categories).not.toBeNull(); // Si la categoría no es nula, entonces se creó correctamente

    // Recuperar todas las subcategorías como strings y dividirlas en arrays
    const subcategoryNames = categories.flatMap(category => category.subcategories.split(',').map(sub => sub.trim()));

    // Verificar que las subcategorías recuperadas tengan exactamente los mismos elementos que las esperadas
    const expectedSubcategories = ['Niños', 'Mujeres', 'Hombres'];

    // Comparar los elementos sin importar el orden
    expect(subcategoryNames.sort()).toEqual(expectedSubcategories.sort());
  });

  it('debería fallar si el nombre contiene caracteres inválidos', async () => {
    req.body = { name: 'Ropa 123', subcategories: 'Niños,Mujeres,Hombres' };

    await saveCategory(req, res);

    expect(req.session.alert).toBe('El nombre de la categoría debe contener solo letras y no puede estar vacío ni tener espacios o comas.');
    expect(res._getRedirectUrl()).toBe('/admin'); // Verificar que se redirige a /admin
  });

  it('debería fallar si las subcategorías contienen números', async () => {
    req.body = { name: 'Ropa', subcategories: 'Niños123,Mujeres,Hombres' };

    await saveCategory(req, res);

    expect(req.session.alert).toBe('Las subcategorías deben ser una lista de texto separada por comas y no debe contener numeros.');
    expect(res._getRedirectUrl()).toBe('/admin');
  });

  it('debería evitar duplicados si la categoría y subcategoría ya existen', async () => {
    // Inserta una categoría inicial
    await Category.create({ name: 'Ropa', subcategories: 'Niños' });

    req.body = { name: 'Ropa', subcategories: 'Niños' };

    await saveCategory(req, res);

    expect(req.session.alert).toBe('La categoría Ropa con subcategoría Niños ya existe.');
    expect(res._getRedirectUrl()).toBe('/admin');

    // Verifica que no se haya creado un duplicado
    const categories = await Category.find({ name: 'Ropa', subcategories: 'Niños' });
    expect(categories.length).toBe(1);
  });
});
