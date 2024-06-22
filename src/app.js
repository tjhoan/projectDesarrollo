const express = require("express")
const bodyParser = require("body-parser")
const path = require("path")
const morgan = require('morgan')
const multer = require('multer')
const { v4: uuidv4 } = require('uuid')

// initializations
const app = express()
require('./database')

// Importing routes
const Routes = require("./routes/index");
  
// settings
app.set('port', 3000)
app.set('views', path.join(__dirname, 'views'))
app.set('view engine', 'ejs') 

//middlewares
app.use(morgan('dev')) 
app.use(express.urlencoded({extended: false}))

app.use(bodyParser.json())
app.use(bodyParser.urlencoded({ extended: false })); 

const storage = multer.diskStorage({
  destination: path.join(__dirname, '../public/img'),
  filename: (req, file, cb) => {
    cb(null, uuidv4() + path.extname(file.originalname))
  }
})
app.use(multer({ storage : storage }).single('image'))
const upload = multer({ storage: storage });
module.exports = upload;

// routes
app.use("/", Routes);

// static files
// app.use(express.static(path.join(__dirname, 'public')))
app.use("/public", express.static("public")); 


// start the server
app.listen(3000, () => {
  console.log(`Server on port ${app.get('port')}`)
})