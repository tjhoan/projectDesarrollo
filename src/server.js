const app = require('./app');

// Inicia el servidor
const PORT = app.get('port');
app.listen(PORT, () => {
  console.log(`Server on port ${PORT}`);
});
