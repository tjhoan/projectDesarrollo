const express = require('express')
const bodyParser = require('body-parser')
const path = require('path')
const morgan = require('morgan')
const session = require('express-session')

// initializations
const app = express()
require('./database')

app.use(bodyParser.json())
app.use(bodyParser.urlencoded({ extended: false }))

// Importing routes
const Routes = require('./routes/index')

// settings
app.set('port', 3000)
app.set('views', path.join(__dirname, 'views'))
app.set('view engine', 'ejs')

// middlewares
app.use(morgan('dev'))
app.use(express.urlencoded({ extended: false }))

app.use(session({
  secret: 'your_secret_key_alalalal123',
  resave: false,
  saveUninitialized: true,
  cookie: { secure: false }
}))

// routes
app.use('/', Routes)

// static files
app.use('/public', express.static('public'))

// start the server
app.listen(3000, () => {
  console.log(`Server on port ${app.get('port')}`)
})
