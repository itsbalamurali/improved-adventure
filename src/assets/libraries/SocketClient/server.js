const app = require("express")();
const client = require('socketcluster-client');
var querystring = require('querystring');

const http = require('http');
const https = require('https');
const fs = require('file-system');
const SERVICE_PORT = process.env.SERVICE_PORT || 1001;

const IS_USE_SSL = process.env.IS_USE_SSL || 'No';
const SSL_KEY_FILE_PATH = process.env.SSL_KEY_FILE_PATH || '';
const SSL_CERT_FILE_PATH = process.env.SSL_CERT_FILE_PATH || '';
const SERVICE_AUTH_KEY = process.env.SERVICE_AUTH_KEY || 'XwDSyg9Fr8u7LMERPhpj';
const SOCKET_CLS_HOST = process.env.SOCKET_CLS_HOST || '';
const SOCKET_CLS_PROTOCOL = process.env.SOCKET_CLS_PROTOCOL || 'http://';
const SOCKET_CLS_PORT = process.env.SOCKET_CLS_PORT || 1000;

if(IS_USE_SSL.toLowerCase() === "yes" && (SSL_KEY_FILE_PATH == null || SSL_KEY_FILE_PATH === "" || SSL_CERT_FILE_PATH == null || SSL_CERT_FILE_PATH === "" || !fs.existsSync(SSL_KEY_FILE_PATH) || !fs.existsSync(SSL_CERT_FILE_PATH))){
	console.log("SSL Key & Certificate file path is required. Also, make sure that key & cert files are exist in the path.");
	process.exit(1);
}

var SOCKET_CLS_CLIENT = null;

if(process.env.SERVICE_PORT == 1001 || SOCKET_CLS_PORT == 1000 || !SOCKET_CLS_HOST || !SOCKET_CLS_PROTOCOL){
	console.log("Required parameters are missing. Please contact to technical team.");
	// console.log("SERVICE_PORT is not defined.");
	process.exit(1);
}

app.use("/publish/", onDataRequested);

var apacheServer = http.createServer(app);

if(IS_USE_SSL.toLowerCase() === "yes"){
	const options = {
	  key: fs.readFileSync(SSL_KEY_FILE_PATH),
	  cert: fs.readFileSync(SSL_CERT_FILE_PATH)
	};

	apacheServer = https.createServer(options, app);
}

apacheServer.listen(SERVICE_PORT, function (err) {
	if (err) {
		throw err;
	}
	console.log("API Up and running on port " + SERVICE_PORT);
});

async function onDataRequested(req,res){

	if(req.method == 'POST') {
        processPost(req, res, function() {
			req.query = {};
			req.query['DATA_TO_PUBLISH'] = req.post.DATA_TO_PUBLISH;
			req.query['CHANNEL_NAME'] = req.post.CHANNEL_NAME;
			req.query['SERVICE_AUTH_KEY'] = req.post.SERVICE_AUTH_KEY;
			
			continueDataRequested(req,res);
        });
		
		return;
    } 
	
	continueDataRequested(req,res);
}

function continueDataRequested(req,res){
	if(req.query.DATA_TO_PUBLISH && req.query.CHANNEL_NAME /* && req.query.SERVICE_AUTH_KEY  && SERVICE_AUTH_KEY == req.query.SERVICE_AUTH_KEY */){
		initClient(req, async function (client){
			executeRequest(req,res);
		});
	}else{
		var response_data = {};
		response_data['Action'] = "0";
		response_data['message'] = "Invalid Request";
		res.jsonp(response_data);
	}
}

async function initClient(req, callback){
	if(SOCKET_CLS_CLIENT){
		return callback(SOCKET_CLS_CLIENT);
	}else{
		// console.log("Create obj");
		let socket = client.create({
			hostname: SOCKET_CLS_HOST,
			secure: SOCKET_CLS_PROTOCOL == "https://" ? true : false,
			port: SOCKET_CLS_PORT
		});
		// console.log(socket);
		SOCKET_CLS_CLIENT = socket;
		return callback(SOCKET_CLS_CLIENT);
	}
}

async function executeRequest(req,res){
	
	if(req.originalUrl.startsWith("/publish")){
		
		if(iSJsonStr(req.query.CHANNEL_NAME)){
			
			var CHANNEL_NAME = iSJsonStr(req.query.CHANNEL_NAME) ? JSON.parse(req.query.CHANNEL_NAME) : req.query.CHANNEL_NAME;
			var DATA_TO_PUBLISH = iSJsonStr(req.query.DATA_TO_PUBLISH) ? JSON.parse(req.query.DATA_TO_PUBLISH) : req.query.DATA_TO_PUBLISH;
			
			if(iSJsonStr(CHANNEL_NAME) && Array.isArray(CHANNEL_NAME) == false){
				setResponseData(res, "0", "Invalid Request");
				return;
			}
			
			if(Array.isArray(DATA_TO_PUBLISH) && DATA_TO_PUBLISH.length != CHANNEL_NAME.length ){
			   setResponseData(res, "0", "Invalid Request");
			   return;
			}else if(Array.isArray(DATA_TO_PUBLISH) && DATA_TO_PUBLISH.length == CHANNEL_NAME.length){
				for (i = 0; i < CHANNEL_NAME.length; i++) {
					let CHANNEL_NAME_tmp = CHANNEL_NAME[i];
					let DATA_TO_PUBLISH_tmp = DATA_TO_PUBLISH[i];
					SOCKET_CLS_CLIENT.transmitPublish(CHANNEL_NAME_tmp, DATA_TO_PUBLISH_tmp);
				}
			}else{
				for (i = 0; i < CHANNEL_NAME.length; i++) {
					let CHANNEL_NAME_tmp = CHANNEL_NAME[i];
					SOCKET_CLS_CLIENT.transmitPublish(CHANNEL_NAME_tmp, req.query.DATA_TO_PUBLISH);
				}
			}
		}else{
			SOCKET_CLS_CLIENT.transmitPublish(req.query.CHANNEL_NAME, req.query.DATA_TO_PUBLISH);
		}
		
		setResponseData(res, "1", "Data Sent Successfully");
	}else{
		setResponseData(res, "0", "Invalid Request");
	}
}

function setResponseData(res, action, message){
	var response_data = {};
	response_data['Action'] = action;
	response_data['message'] = message;
	
	res.jsonp(response_data);
}

function iSJsonStr(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

function processPost(request, response, callback) {
    var queryData = "";
    if(typeof callback !== 'function') return null;

    if(request.method == 'POST') {
        request.on('data', function(data) {
            queryData += data;
            if(queryData.length > 1e6) {
                queryData = "";
                response.writeHead(413, {'Content-Type': 'text/plain'}).end();
                request.connection.destroy();
            }
        });

        request.on('end', function() {
            request.post = querystring.parse(queryData);
            callback();
        });

    } else {
        response.writeHead(405, {'Content-Type': 'text/plain'});
        response.end();
    }
}