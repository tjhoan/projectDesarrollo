const fs = require('fs');
const path = require('path');
const Image = require('../../models/image');
const Product = require('../../models/product');

module.exports = async (req, res) => {
  try {
    const productId = req.params.id;
    const product = await Product.findById(productId).populate('imagePaths');

    if (!product) {
      req.session.alert = 'Producto no encontrado';
      return res.status(404).json({ message: 'Producto no encontrado' });
    }

    for (const imageId of product.imagePaths) {
      const image = await Image.findById(imageId);

      if (image) {
        const imagePath = path.join(__dirname, '..', '..', image.path);

        try {
          if (fs.existsSync(imagePath)) {
            fs.unlinkSync(imagePath);
          }
          await Image.deleteOne({ _id: imageId });
        } catch (fileError) {
          console.error('Error al eliminar el archivo:', fileError);
          req.session.alert = 'Error al eliminar la imagen del producto';
          return res.status(500).json({ message: 'Error al eliminar la imagen del producto' });
        }
      }
    }

    await Product.deleteOne({ _id: productId });
    delete req.session.alert;
    return res.status(200).json({ message: 'Producto eliminado correctamente' });
  } catch (error) {
    console.error('Error al eliminar el producto:', error);
    req.session.alert = 'Error al eliminar el producto';
    return res.status(500).json({ message: 'Error al eliminar el producto' });
  }
};
