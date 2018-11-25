'use strict';

const express = require('express'),
CORS = require('./middleware/cors.js'),
bodyParser = require('body-parser'),
accountRouter = require('./routes/account-route.js');

let ekkele = require('./controllers/account-controller.js');
// Constants
const PORT = 80,
HOST = 'localhost';

//ekkele.getAccounts();
// App
const app = express();

//Body Parser
app.use(bodyParser.json()); 
app.use(bodyParser.urlencoded({ extended: true }));

//CORS
app.use(CORS);

//Routes
app.use('/', accountRouter);

app.listen(PORT, () => {
    console.log(`Running on http://${HOST}:${PORT}`);
});
