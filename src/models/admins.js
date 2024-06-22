const { Schema, model } = require('mongoose')

const adminSchema = new Schema({
  name: { type: String, required: true },
  password: { type: String, required: true }
})

// db.admins.insertOne({ name: "admin", password: "123" })

const Admin = model('admin', adminSchema)

module.exports = Admin
