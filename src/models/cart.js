const mongoose = require('mongoose')

const CartSchema = new mongoose.Schema({
  customerId: { type: mongoose.Schema.Types.ObjectId, ref: 'Customer', required: true },
  products: [{
    productId: { type: mongoose.Schema.Types.ObjectId, ref: 'Product', required: true },
    quantity: { type: Number, default: 1 }
  }]
})

const Cart = mongoose.model('Cart', CartSchema)

module.exports = Cart
