const mongoose = require('mongoose');
const Category = require('../../../models/category'); // Importa tu modelo

describe('Operaciones de eliminación en la colección Category', () => {
  let categoryId;
  let category;

  beforeAll(async () => {
    const DB_URI = process.env.DB_URI || 'mongodb://localhost:27017/project_desarrollo_test';
    if (mongoose.connection.readyState === 0) {
      await mongoose.connect(DB_URI);
    }
  });

  beforeEach(async () => {
    // Crea una categoría de prueba
    category = new Category({ name: 'Prueba', subcategories: 'Subcategoria1' });
    await category.save();
    categoryId = category._id.toString();
  });

  afterEach(async () => {
    await Category.deleteMany();
  });

  afterAll(async () => {
    await mongoose.connection.close();
  });

  it('debería eliminar una categoría existente y verificar que ya no existe en la base de datos', async () => {
    await Category.findByIdAndDelete(categoryId);
    const deletedCategory = await Category.findById(categoryId);

    // Verifica que la categoría ya no exista en la base de datos
    expect(deletedCategory).toBeNull();
  });

  it('debería devolver null al buscar una categoría con un ID que no existe en la base de datos', async () => {
    const fakeId = new mongoose.Types.ObjectId();

    const category = await Category.findById(fakeId);

    // Verifica que el resultado sea null
    expect(category).toBeNull();
  });

  it('debería confirmar que la categoría fue eliminada de la base de datos', async () => {
    // Elimina la categoría con el método deleteOne
    await Category.deleteOne({ _id: categoryId });

    // Verifica que la categoría ya no exista en la base de datos
    const deletedCategory = await Category.findOne({ _id: categoryId });

    expect(deletedCategory).toBeNull();
  });
});
