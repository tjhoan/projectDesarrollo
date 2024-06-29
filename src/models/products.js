const { Schema, model } = require('mongoose')

const ProductSchema = new Schema({
  name: { type: String, required: true, unique: true },
  price: { type: Number, required: true },
  quantity: { type: Number, required: true },
  brand: { type: String, required: true },
  category: { type: String, required: true },
  description: { type: String, required: true },
  imgs: [{ type: Schema.Types.ObjectId, ref: 'images' }],
  imagePaths: [{ type: String }]
})

const Product = model('products', ProductSchema)

module.exports = Product
