#!/usr/bin/env node
//var WebSocketServer = require('websocket').server;
const app = require("express")();
var http = require('http');
var https = require('https');
const fs = require('file-system');

const initClient = require("./db").initClient;
const insertMessageData = require("./db").insertMessageData;
const getServiceMessages = require("./db").getServiceMessages;

const SERVICE_PORT = process.env.SERVICE_PORT || 1001;

const IS_USE_SSL = process.env.IS_USE_SSL || 'No';
const SSL_KEY_FILE_PATH = process.env.SSL_KEY_FILE_PATH || '';
const SSL_CERT_FILE_PATH = process.env.SSL_CERT_FILE_PATH || '';

var clients = {};

var clients_messages = {};
var client_connections = {};

var apacheServer = http.createServer(app);


if(IS_USE_SSL.toLowerCase() === "yes"){
	const options = {
	  key: fs.readFileSync(SSL_KEY_FILE_PATH),
	  cert: fs.readFileSync(SSL_CERT_FILE_PATH)
	};

	apacheServer = https.createServer(options, app);
}

function isJsonStr(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}


const SocketIO = require('socket.io')(apacheServer, {
cors: {
    origin: "*"
    },
});

	/* console.log("=============Client Connection Initiaing =================="); */

initClient(function (client, _db, err){
	
		if(err){
			var response_data = {};
			response_data['message'] = "Can't start service.";
			res.jsonp(response_data);
			return;
		}
		
		/* console.log("=============ClientConnected=================="); */
		
		
		/* let wsServer = new WebSocketServer({
			httpServer: apacheServer,
			autoAcceptConnections: true,
			keepaliveInterval: 4000,
			keepaliveGracePeriod: 4000,
			closeTimeout: 4000
		}); */
		
		
		executeAuth();
		
		apacheServer.listen(SERVICE_PORT, function() {
			console.log((new Date()) + ' Server is listening on port '+ SERVICE_PORT);
			
		});
		

		/* wsServer.on('connect', function(connection) {
			
			 executeRequest(connection);
			
		});
		
		
		wsServer.on('close', function(connection) {
			if(connection.authToken && clients[connection.authToken]){	
			
			}
		}); */

		//executeRequest(connection);
	}); 


async function executeAuth(){
		console.log("Trying to initiate");
	
	/* SocketIO.on('connection', client => {
		console.log("Connection Accepted");
		executeRequest(client);
	}); */
	
	 /* SocketIO.use(function(socket, next){
		console.log("Query: ", socket.handshake.query);
		// return the result of next() to accept the connection.
		if (socket.handshake.query.foo == "bar") {
			return next();
		}
		// call next() with an Error if you need to reject the connection.
		next(new Error('Authentication error'));
	}); */ 
	
    SocketIO.on('connection', client => {
		console.log("Connection Accepted");
		executeRequest(client);
	}); 
}

async function executeRequest(client){
	
	
	client.on('event', (data, callback) => { 



	});
	
	
	
}

