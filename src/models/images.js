const { Schema, model } = require('mongoose');

const imageSchema = new Schema ({
  name_product: { type: String, required: true },
  description: { type: String, required: true },
  category: { type: String, required: true },
  price: { type: Number, required: true },
  quantity: { type: Number, required: true },
  brand: { type: String, required: true },
  filename: { type: String, required: true },
  path: { type: String, required: true },
  originalname: { type: String, required: true },
  mimetype: { type: String, required: true },
  size: { type: Number, required: true },
  created_at: { type: Date, default: Date.now }
})

const Image = model('images', imageSchema)

module.exports = Image