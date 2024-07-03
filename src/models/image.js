const { Schema, model } = require('mongoose')

const imageSchema = new Schema({
  path: { type: String, required: true },
  originalname: { type: String, required: true },
  created_at: { type: Date, default: Date.now }
}, { collection: 'image' })

const Image = model('image', imageSchema)

module.exports = Image
