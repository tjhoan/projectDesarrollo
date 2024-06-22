const { Schema, model } = require('mongoose');

const customerSchema = new Schema({
  first_name: { type: String, required: true },
  second_name: { type: String, required: true },
  last_name: { type: String, required: true },
  email: { type: String, required: true},
  cedula: { type: Number, required: true, unique: true  },
  password: { type: String, required: true },
  address: { type: String, required: true },
  phone: { type: Number, required: true },
  created_at: { type: Date, default: Date.now }
})

// db.customers.insertOne({ first_name: "Juan", second_name: "Pablo", last_name: "Pérez", email: "juanperez@example.com", cedula: 1234567890, password: "clave_segura", address: "Calle Principal #123", phone: 1122334455, created_at: new Date() })

const Customers = model('customers', customerSchema)

module.exports = Customers