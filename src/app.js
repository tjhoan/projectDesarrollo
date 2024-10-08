const express = require('express');
const path = require('path');
const morgan = require('morgan');
const session = require('express-session');
const MongoStore = require('connect-mongo'); // Importa connect-mongo
const mongoose = require('mongoose');
require('dotenv').config();

// initializations
const app = express();
require('./database');

// Importing routes
const Routes = require('./routes/index');

// settings
app.set('port', process.env.PORT || 3000);
app.set('views', path.join(__dirname, 'views'));
app.set('view engine', 'ejs');

// middlewares
app.use(morgan('dev'));
app.use(express.json());
app.use(express.urlencoded({ extended: false }));

// Session middleware configuration
app.use(
  session({
    secret: process.env.SESSION_SECRET || 'your_secret_key',
    resave: false,
    saveUninitialized: false,
    store: MongoStore.create({
      mongoUrl: process.env.MONGODB_URI || 'mongodb://localhost:27017/project',
      mongooseConnection: mongoose.connection
    }),
    cookie: {
      secure: process.env.NODE_ENV === 'production'
    }
  })
);

// routes
app.use('/', Routes);

// static files
app.use('/public', express.static('public'));

module.exports = app;
