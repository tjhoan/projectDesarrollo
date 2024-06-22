const mongoose = require('mongoose');

mongoose.connect('mongodb://localhost/node_test')
  .then(() => console.log('DB is connected'))
  .catch(err => console.error(err));