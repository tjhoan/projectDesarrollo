const { Schema, model } = require('mongoose')

const ProductSchema = new Schema({
  name: { type: String, required: true, unique: true },
  price: { type: Number, required: true },
  quantity: { type: Number, required: true },
  brand: { type: String, required: true },
  category: { type: String, required: true },
  description: { type: String, required: true },
  imagePaths: [{ type: String, required: true }],
}, { collection: 'product' })

const Product = model('product', ProductSchema)

module.exports = Product
