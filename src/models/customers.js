const { Schema, model } = require('mongoose')

const customerSchema = new Schema({
  first_name: { type: String, required: true },
  second_name: { type: String, required: true },
  last_name: { type: String, required: true },
  email: { type: String, required: true },
  cedula: { type: Number, required: true, unique: true },
  password: { type: String, required: true },
  address: { type: String, required: true },
  phone: { type: Number, required: true },
  created_at: { type: Date, default: Date.now }
})

const Customers = model('customers', customerSchema)

module.exports = Customers
