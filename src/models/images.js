const { Schema, model } = require('mongoose')

const imageSchema = new Schema({
  filename: { type: String, required: true },
  path: { type: String, required: true },
  originalname: { type: String, required: true },
  mimetype: { type: String, required: true },
  size: { type: Number, required: true },
  created_at: { type: Date, default: Date.now }
})

const Image = model('images', imageSchema)

module.exports = Image
