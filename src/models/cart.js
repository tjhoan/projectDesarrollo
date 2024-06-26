const { Schema, model } = require('mongoose')

const cartSchema = new Schema({
  customer: { type: Schema.Types.ObjectId, ref: 'customers', required: true },
  products: [
    {
      product: { type: Schema.Types.ObjectId, ref: 'products', required: true },
      quantity: { type: Number, required: true, default: 1 }
    }
  ],
  created_at: { type: Date, default: Date.now }
})

const Cart = model('cart', cartSchema)

module.exports = Cart
