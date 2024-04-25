#!/usr/bin/env node
var WebSocketServer = require('websocket').server;
const app = require("express")();
const rateLimit = require('express-rate-limit');

var http = require('http');
var https = require('https');
const fs = require('file-system');
var qs = require('querystring');
const bodyParser = require("body-parser");
const compression = require('compression');
const setDataRes = require("./setDataRes");

const multer = require('multer');


const initClient = require("./db").initClient;

const dataReqObj = require("./exeDataRequest");
const exeDataRequest = dataReqObj.exeDataRequest;
const exeClientRequest = dataReqObj.exeClientRequest;

const SERVICE_PORT = process.env.SERVICE_PORT || 1001;
const HTTP_SERVICE_PORT = process.env.HTTP_SERVICE_PORT || 1001;

const IS_USE_SSL = process.env.IS_USE_SSL || 'No';
const SSL_KEY_FILE_PATH = process.env.SSL_KEY_FILE_PATH || '';
const SSL_CERT_FILE_PATH = process.env.SSL_CERT_FILE_PATH || '';

var clients = {};

var clients_messages = {};
var client_connections = {};

/* const rate_limiter = rateLimit({
	windowMs: 1 * 60 * 1000, // 15 minutes
	max: 10, // Limit each IP to 100 requests per `window` (here, per 15 minutes)
	standardHeaders: false, // Return rate limit info in the `RateLimit-*` headers
	legacyHeaders: false, // Disable the `X-RateLimit-*` headers
}) */

app.use(compression());
// app.use(rate_limiter);

app.use(bodyParser.urlencoded({
    extended: true,
	limit: '50mb',
	parameterLimit: 1000000
}));

app.use(bodyParser.json());
app.use(bodyParser.urlencoded({extended: true}))
app.use(multer({ dest: 'uploads/' }).any());

// app.use("/memberdata/", onDataRequested);
app.use("/", onDataRequested);

/* var httpApacheServer = http.createServer(app); */
var apacheServer = http.createServer(app);
// var localApacheServer = http.createServer(app);

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
		
		
		
		
		apacheServer.listen(SERVICE_PORT, function() {
			console.log((new Date()) + ' Server is listening on port '+ SERVICE_PORT);
			
			executeAuth(apacheServer);
		});
		
		/* httpApacheServer.listen(HTTP_SERVICE_PORT, function() {
			console.log((new Date()) + ' Server is listening on port '+ HTTP_SERVICE_PORT);
			
			executeAuth(httpApacheServer);
		}); */
		
		/* localApacheServer.listen(8470, function() {
			console.log((new Date()) + ' localApacheServer is listening on port 8470');
			
			executeAuth(localApacheServer);
		}); */
		

		/* wsServer.on('connect', function(connection) {
			
			 executeRequest(connection);
			
		});
		
		
		wsServer.on('close', function(connection) {
			if(connection.authToken && clients[connection.authToken]){	
			
			}
		}); */

		//executeRequest(connection);
	}); 


async function executeAuth(apacheServer){
		console.log("Trying to initiate");
	
	let wsServer = new WebSocketServer({
		httpServer: apacheServer,
		autoAcceptConnections: true,
		keepaliveInterval: 4000,
		keepaliveGracePeriod: 4000,
		closeTimeout: 4000
	});
	
	wsServer.on('connect', function(connection) {
		connection.AUTH_DATA = {};
		executeRequest(connection);
		
	});
	
	await preloadData();
}

async function preloadData(){
	await dataReqObj.UpdateStaticData({},{});
	await dataReqObj.UpdateStaticPages({},{});
	await dataReqObj.UpdateFAQsData({},{});
	await dataReqObj.UpdateCancelReasons({},{});
	await dataReqObj.UpdateAppImages({},{});
	await dataReqObj.UpdateCabRequestData({},{});
	await dataReqObj.UpdateGeneralConfigData({},{});
}

function random (min, max) {
    return Math.floor(Math.random() * (max - min) + min);
}

function isJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

async function executeRequest(client){
	
	
	client.on('message', async function(message) {
				
		if (message.type === 'utf8') {
			
			if(message.utf8Data == "RETRIEVE_MEMBER_DATA" && client.AUTH_DATA){
				await sendMemberData(client);
			}else if(message.utf8Data == "SEND_MEMBER_DATA"){
				console.log(message.utf8Data);
			}else{
				let jsonData = JSON.parse(message.utf8Data);
			
				if(jsonData.event == "#handshake" || jsonData.event == "RETRIEVE_MEMBER_DATA"){
					
					let AUTH_DATA_ARR = jsonData.data.authToken.split("&");
					  
					AUTH_DATA_ARR.forEach(element => {
						if(element.startsWith("authToken=")){
							client.AUTH_DATA.AUTH_TOKEN = element.split("=")[1];
						}else if(element.startsWith("iMemberId=")){
							client.AUTH_DATA.MEMBER_ID = element.split("=")[1];
						}else if(element.startsWith("iMemberType=")){
							client.AUTH_DATA.MEMBER_TYPE = element.split("=")[1];
						}
					});
							
							
					await sendMemberData(client);
					
							// client.send(JSON.stringify(origData),()=>{});
							
					/* setTimeout(function() {
							let origData = {data: "123456789"};
							
							console.log(origData);
						
							client.send(JSON.stringify(origData),()=>{});
						}, 1200); */
				}else if(jsonData.event == "*"){
					
					/* let timeNum = random(500, 1200);
					console.log(timeNum);
					
					setTimeout(function() {
							
							
							let origData = {data: jsonData.data, cid: jsonData.cid, rid: jsonData.cid};
							console.log(origData);
						
							client.send(JSON.stringify(origData),()=>{});
						}, timeNum); */
				}
			}
		}
		
	});
}

async function onDataRequested(req,res){
	
	// if(req.originalUrl == "/" || req.originalUrl == "/loaderio-fb6f5f767ec813b46ef6582e528f5a8b.txt"){
		// res.send("loaderio-fb6f5f767ec813b46ef6582e528f5a8b");
	// }else{
		
		// console.log(req);
		
		if(req.method == 'POST') {
			req.query = req.body;
		}
		
		if(Object.keys(req.query).length < 1){
			setDataRes({Action: "1", message: "Invalid request."}, req, res);
			return;
		}
		
		// console.log(req.originalUrl);
		
		  // console.log(req.query);
		
		let AUTH_DATA = {};
		
		AUTH_DATA.AUTH_TOKEN = req.query.tSessionId;
		AUTH_DATA.MEMBER_ID = req.query.GeneralMemberId;
		AUTH_DATA.MEMBER_TYPE = "PASSENGER";
		AUTH_DATA.MEMBER_LANGUAGE = "EN";
		AUTH_DATA.MEMBER_CURRENCY = "USD";
		AUTH_DATA.JOB_TYPE = "RIDE";
		if(req.query.GeneralUserType){
			AUTH_DATA.MEMBER_TYPE = req.query.GeneralUserType.toUpperCase();
		}
		
		if(req.query.vGeneralLang){
			AUTH_DATA.MEMBER_LANGUAGE = req.query.vGeneralLang.toUpperCase();
		}
		if(req.query.vGeneralCurrency){
			AUTH_DATA.MEMBER_CURRENCY = req.query.vGeneralCurrency.toUpperCase();
		}
		if(req.query.eJobType){
			AUTH_DATA.JOB_TYPE = req.query.eJobType.toUpperCase();
		}
		
		req.AUTH_DATA = AUTH_DATA;

		exeDataRequest(req, res);
	// }
	// res.send("loaderio-fb6f5f767ec813b46ef6582e528f5a8b");
	
	
}


async function sendMemberData(client){
	
	if(!client.AUTH_DATA){
		return;
	}
	
	
	await exeClientRequest(client);
	
	// let origData = await getUserData(client.AUTH_DATA);
	

	// if(origData && origData[0].MEMBER_DATA){
		// client.send(JSON.stringify({MEMBER_PROFILE_DATA: origData[0].MEMBER_DATA, DATA_TYPE: "APP_SERVICE"}));
	// }
	
	console.log("USER Data Sent");
}
