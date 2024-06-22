const { Schema, model } = require('mongoose')

const ProductSchema = new Schema({
  name: { type: String, required: true, unique: true },
  img: { type: String, required: true },
  incart: { type: Boolean, default: false },
  price: { type: Number, required: true }
})

const Product = model('products', ProductSchema)

module.exports = Product
