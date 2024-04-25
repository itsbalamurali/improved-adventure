const http = require('http');
const https = require('https');
const fs = require('file-system');
const initClient = require("./db").initClient;
const getClient = require("./db").getClient;
const getDB = require("./db").getDB;
const getMasterAuthAccountData = require("./db").getMasterAuthAccountData;
const getAuthAccountData = require("./db").getAuthAccountData;
const getMasterAuthAccountDataForRG = require("./db").getMasterAuthAccountDataForRG;
const getMasterAuthAccountDataForAC = require("./db").getMasterAuthAccountDataForAC;
const getMasterAuthAccountDataForService = require("./db").getMasterAuthAccountDataForService;
const validateLocationsForDirectionService = require("./db").validateLocationsForDirectionService;
const fetchCountryFromLocation = require("./db").fetchCountryFromLocation;
const incServiceCount = require("./db").incServiceCount;
const insertIntoAuthData = require("./db").insertIntoAuthData;
const generateASCIIString = require("./general_functions").generateASCIIString;
const formateStringURLForRG = require("./general_functions").formateStringURLForRG;
const formateStringURL = require("./general_functions").formateStringURL;
const getDataFromRemoteURL = require("./general_functions").getDataFromRemoteURL;
const fetchValuesFromAnArray = require("./general_functions").fetchValuesFromAnArray;
const fetchValuesFromAnObject = require("./general_functions").fetchValuesFromAnObject;
const isEmptyObject = require("./general_functions").isEmptyObject;
const roundToTwo = require("./general_functions").roundToTwo;
const regenerateArrayBasedOnDistance = require("./general_functions").regenerateArrayBasedOnDistance;
const generateArrFromStr = require("./general_functions").generateArrFromStr;
const insertAt = require("./general_functions").insertAt;
const decodePolyline = require('decode-google-map-polyline');
const crypto = require('crypto');
const md5 = require('md5');

const requestIp = require('request-ip');
const rateLimit = require('express-rate-limit');
const compression = require('compression');

const getAuthAccData = require("./general_functions").getAuthAccData;
const MAPS_API_PORT = process.env.MAPS_API_PORT || 1728;

const IS_USE_SSL = process.env.IS_USE_SSL || 'No';
const SSL_KEY_FILE_PATH = process.env.SSL_KEY_FILE_PATH || '';
const SSL_CERT_FILE_PATH = process.env.SSL_CERT_FILE_PATH || '';

var urldecode = require('urldecode');

process.env.TZ = 'Africa/Abidjan';
// process.env.TZ = 'Pacific/Pago_Pago';		// Do not uncomment this as its only for testing purpose

const app = require("express")();
const replaceString = require('replace-string');

const rate_limiter = rateLimit({
	windowMs: 1 * 60 * 1000, // 15 minutes
	max: 80, // Limit each IP to 100 requests per `window` (here, per 15 minutes)
	standardHeaders: false, // Return rate limit info in the `RateLimit-*` headers
	legacyHeaders: false, // Disable the `X-RateLimit-*` headers
})

if(IS_USE_SSL.toLowerCase() === "yes" && (SSL_KEY_FILE_PATH == null || SSL_KEY_FILE_PATH === "" || SSL_CERT_FILE_PATH == null || SSL_CERT_FILE_PATH === "" || !fs.existsSync(SSL_KEY_FILE_PATH) || !fs.existsSync(SSL_CERT_FILE_PATH))){
	console.log("SSL Key & Certificate file path is required. Also, make sure that key & cert files are exist in the path.");
	process.exit(1);
}

app.use(compression());
app.use(rate_limiter);
app.use(requestIp.mw())
app.use("/reversegeocode/", onDataRequested);
app.use("/direction/", onDataRequested);
app.use("/autocomplete/", onDataRequested);
app.use("/placedetails/", onDataRequested);
app.use("/fetchCountryCode/", onDataRequested);

var apacheServer = http.createServer(app);

if(IS_USE_SSL.toLowerCase() === "yes"){
	const options = {
	  key: fs.readFileSync(SSL_KEY_FILE_PATH),
	  cert: fs.readFileSync(SSL_CERT_FILE_PATH)
	};

	apacheServer = https.createServer(options, app);
}


apacheServer.listen(MAPS_API_PORT, function (err) {
	if (err) {
		throw err;
	}
	console.log("API Up and running on port " + MAPS_API_PORT);
});

function objectToQueryString(obj) {
  var str = [];
  for (var p in obj)
    if (obj.hasOwnProperty(p)) {
      str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
    }
  return str.join("&");
}

async function onDataRequested(req,res){
	
	if(Object.keys(req.query).length == 1){
		for (const [key, value] of Object.entries(req.query)) {
			// console.log(key, value);
		  
			let request_text = Buffer.from(key, 'base64').toString();
			let request_text_arr = request_text.split("!!@@!!@@@@@!!@@!!");
			
			let site_url = request_text_arr[request_text_arr.length - 1];
			let package_name = request_text_arr[request_text_arr.length - 2];
			let data_text = request_text_arr.slice(0,request_text_arr.length-2).join('');
						
			var SECRET_KEY = (Buffer.from(md5(package_name+site_url+md5(Buffer.from(generateASCIIString(Buffer.from(generateASCIIString(package_name)).toString('base64'))).toString('base64')))).toString('base64'));
			
			SECRET_KEY = SECRET_KEY.substring(SECRET_KEY.length - 16, SECRET_KEY.length);
			 // console.log(SECRET_KEY);
			
			var cipher = crypto.createCipheriv('aes-128-cbc', SECRET_KEY, SECRET_KEY);
			var decipher = crypto.createDecipheriv('aes-128-cbc', SECRET_KEY, SECRET_KEY);
			 // console.log(data_text);
			
			var decrypted = decipher.update(data_text, 'base64', 'binary');
			decrypted += decipher.final('binary');
			
			let final_data_requested = decrypted.split("&");
			
			req.query = {};
			
			for (final_data_requested_item of final_data_requested) {
				let final_data_requested_item_arr = final_data_requested_item.split("=");
				req.query[final_data_requested_item_arr[0]] = urldecode(final_data_requested_item_arr[1]);
			}
			// console.log('Decrypted: ', decrypted);
			// console.log(req.query);
			req.query['CipherObj'] = cipher;
			
			break;
		}
	}
	if(!req.query.TSITE_DB){
		var response_data = {};
		response_data['message'] = "Invalid request.";
		res.jsonp(response_data);
		return;
	}
	
	/* console.log("DBName");
	console.log(req.query.DB_NAME); */
	
	initClient(req.query.TSITE_DB, async function (client, _db, err){
		if(err){
			var response_data = {};
			response_data['message'] = "Can't start service.";
			res.jsonp(response_data);
			return;
		}
		// console.log("ClientConnected");
		
		executeRequest(req,res);
	});
}

async function executeRequest(req,res){
	
	var service_type = ""; 
	if(req.originalUrl.startsWith("/direction")){
		var locationData = [];
		locationData.push(req.query.source_latitude + "," + req.query.source_longitude);
		locationData.push(req.query.dest_latitude + "," + req.query.dest_longitude);
		
		if((req.query["waypoints"] === undefined) == false && req.query["waypoints"] != "" && req.query["waypoints"].replace(/\s+/, "")  != "[]" && req.query["waypoints"].replace(/\s+/, "")  != "null"){
			
			try {
				let waypoints_obj = JSON.parse(req.query.waypoints);
				// console.log(waypoints_obj);
				for(let waypoints_obj_item of waypoints_obj){
					if(waypoints_obj_item != ""){
						locationData.push(waypoints_obj_item.replace(/\s+/, ""));
					}
				}
			} catch (e) {
				req.query.waypoints = "";
			}
			
		}
		
		// let isDataValid = await validateLocationsForDirectionService(locationData);
		let isDataValid = true;
		if(!isDataValid){
			
			var response_data_invalid = {};
			response_data_invalid['Action'] = "0";
			response_data_invalid['message'] = "LBL_DEST_ROUTE_NOT_FOUND";
			res.jsonp(response_data_invalid);
			
			return;
		}
		service_type = "Direction";
	}else if(req.originalUrl.startsWith("/autocomplete")){
		service_type = "AutoComplete";
	}else if(req.originalUrl.startsWith("/reversegeocode")){
		service_type = "ReverseGeoCode";
	}else if(req.originalUrl.startsWith("/placedetails")){
		service_type = "PlaceDetails";
	}else if(req.originalUrl.startsWith("/fetchCountryCode")){
		var countryDataArr = await fetchCountryFromLocation({latitude: req.query.latitude, longitude: req.query.longitude});
		
		/* var response_data = {};
		response_data = Object.assign(response_data, countryDataArr); */
		
		res.jsonp(countryDataArr);
		return;
	}
	
	var vServiceAccountId = "";
	
	if(req.query.vServiceAccountId && req.query.vServiceAccountId != "null" /* && (req.query.vServiceAccountId === null) != false */){
		vServiceAccountId = parseInt(req.query.vServiceAccountId);
	}
	
	var vServiceAccountAuthKey = "";
	
	if(req.query.vServiceAccountAuthKey){
		vServiceAccountAuthKey = req.query.vServiceAccountAuthKey;
	}
	
	const db_data = await getMasterAuthAccountDataForService(service_type == "ReverseGeoCode" ? "Geocoding" : service_type, vServiceAccountId);
	
	var response_remote_data;
	var response_data = {};
	
	if(req.query.search_query){
		req.query.search_query = encodeURIComponent(req.query.search_query);
	}
	
	for(let entry of db_data) {
	
		let vServiceName = entry.vServiceName;
		let vSupportedLanguages = entry.vSupportedLanguages;
		let vServiceId = parseInt(entry.vServiceId);
		
		let obj_vServiceData = JSON.parse(JSON.stringify(entry.vServiceData));
		let obj_vServiceReturnData = JSON.parse(JSON.stringify(entry.vServiceReturnData));
		var auth_acc_data = await getAuthAccountData(vServiceId, vServiceAccountAuthKey);

		if((!auth_acc_data || auth_acc_data.length == 0) && vServiceName == "OpenMap"){
			/* Insert Account to mongoDB ***/
			
			await insertIntoAuthData({vTitle: "Service Account", vServiceId: 1, auth_key: "", EntityType: "", vUsageOrder: 1, eStatus: "Active"});
			
			auth_acc_data = await getAuthAccountData(vServiceId, vServiceAccountAuthKey);
			
		}

		/* Added by HV on 19-05-2021 for Service Account Key Usage */
		auth_acc_data = getAuthAccData(auth_acc_data);
		// res.jsonp(auth_acc_data);
		/* Added by HV on 19-05-2021 for Service Account Key Usage End */
		
		
		//console.log(req);
		
		var tURL = obj_vServiceData[service_type];
		
		var vTollAvoidValue = "";
		
		if(entry.vServiceRules && entry.vServiceRules.vTollAvoidValue){
			vTollAvoidValue = entry.vServiceRules.vTollAvoidValue;
		}
		
		response_data['vServiceName'] = vServiceName;
		response_data['vServiceId'] = vServiceId;
		
		var lang_code = req.query.language_code;
		
		if(req.query["toll_avoid"] === undefined || req.query["toll_avoid"] != "Yes"){
			req.query.toll_avoid = "";
			vTollAvoidValue = "";
		}else if(vTollAvoidValue != ""){
			tURL = tURL + vTollAvoidValue;
		}
		
		if(!req.query.latitude || req.query.latitude == ""){
			req.query.latitude = "0.0";
		}
		
		if(!req.query.longitude || req.query.longitude == ""){
			req.query.longitude = "0.0";
		}
		
		if(!req.query.source_latitude || req.query.source_latitude == ""){
			req.query.source_latitude = "0.0";
		}
		
		if(!req.query.source_longitude || req.query.source_longitude == ""){
			req.query.source_longitude = "0.0";
		}
		
		if(!req.query.source_longitude || req.query.source_longitude == ""){
			req.query.source_longitude = "0.0";
		}
		
		if(!req.query.dest_latitude || req.query.dest_latitude == ""){
			req.query.dest_latitude = "0.0";
		}
		
		if(!req.query.dest_longitude || req.query.dest_longitude == ""){
			req.query.dest_longitude = "0.0";
		}
		
		if((req.query["waypoints"] === undefined) == false && req.query["waypoints"] != "" && req.query["waypoints"].replace(/\s+/, "")  != "[]"  && req.query["waypoints"].replace(/\s+/, "")  != "null" && entry.vServiceRules && entry.vServiceRules.tWaypointsValues){
			let prefix = entry.vServiceRules.tWaypointsValues.prefix;
			let seperation_keyword = entry.vServiceRules.tWaypointsValues.seperation_keyword;
			var ADD_FIRST_SEPERATION = "No";
			var ADD_LAST_SEPERATION = "Yes";
			if(entry.vServiceRules.tWaypointsValues.ADD_FIRST_SEPERATION){
				ADD_FIRST_SEPERATION = entry.vServiceRules.tWaypointsValues.ADD_FIRST_SEPERATION;
			}
			if(entry.vServiceRules.tWaypointsValues.ADD_LAST_SEPERATION){
				ADD_LAST_SEPERATION = entry.vServiceRules.tWaypointsValues.ADD_LAST_SEPERATION;
			}
			let data_keyword = entry.vServiceRules.tWaypointsValues.data_keyword;
			let postfix_search_keyword = entry.vServiceRules.tWaypointsValues.postfix_search_keyword;
			
			var waypointsArr = regenerateArrayBasedOnDistance({source_latitude: req.query.source_latitude, source_longitude: req.query.source_longitude, waypoints: req.query.waypoints});
			let waypointsArr_orig = waypointsArr;
			
			response_data['waypoint_order'] = [];
		
			waypointsArr = [];
			for(let distance_arr_item of waypointsArr_orig){
				response_data['waypoint_order'].push(distance_arr_item['ARR_POSITION']);
				waypointsArr.push(distance_arr_item['LOCATION']);
			}
			
			// console.log(response_data);
			
			if(waypointsArr.length > 0){
				if(prefix && prefix != ""){
					tURL = tURL + prefix;
				}
				let data_keyword_arr = generateArrFromStr(data_keyword,waypointsArr.length);
				for(let i = 0; i < waypointsArr.length; i++){
					let location_tmp = waypointsArr[i].split(",");
					data_keyword_arr[i] = data_keyword_arr[i].replace(/@{latitude}/g, location_tmp[0]);
					data_keyword_arr[i] = data_keyword_arr[i].replace(/@{longitude}/g, location_tmp[1]);
					data_keyword_arr[i] = data_keyword_arr[i].replace(/@{CURRENT_POSITION}/g, ""+(i+1));
					data_keyword_arr[i] = data_keyword_arr[i].replace(/@{LAST_POSITION}/g, ""+(waypointsArr.length + 1));
				}
				
				var waypoints_data_str = data_keyword_arr.join(seperation_keyword);
				
				if(ADD_FIRST_SEPERATION == "Yes"){
					waypoints_data_str = seperation_keyword + waypoints_data_str;
				}
				
				if(prefix && prefix != ""){
					tURL = tURL + waypoints_data_str;
				}else if(postfix_search_keyword != ""){
					tURL = tURL.replace(new RegExp( postfix_search_keyword,"g" ), postfix_search_keyword+waypoints_data_str+ (ADD_LAST_SEPERATION == "Yes" ? seperation_keyword : ""));
				}
				
				tURL = tURL.replace(/@{LAST_POSITION}/g, ""+(waypointsArr.length + 1));
			}else{
				tURL = tURL.replace(/@{LAST_POSITION}/g, "1");
			}
		}else{
			tURL = tURL.replace(/@{LAST_POSITION}/g, "1");
		}
		
		if(vSupportedLanguages){
			let obj_vSupportedLanguages = JSON.parse(JSON.stringify(entry.vSupportedLanguages));
			if(lang_code && lang_code != "" && obj_vSupportedLanguages && Object.keys(obj_vSupportedLanguages).length > 0){
				lang_code = obj_vSupportedLanguages[lang_code] ? obj_vSupportedLanguages[lang_code] : obj_vSupportedLanguages['en'];
			}
		}
		
		for(let auth_acc_entry of auth_acc_data) {

			let auth_key = auth_acc_entry.auth_key;
			let idOfAccount = auth_acc_entry._id;
			
			var request_data = {URL: tURL, search_query: req.query.search_query, latitude: req.query.latitude, longitude: req.query.longitude, max_latitude: req.query.latitude, max_longitude: req.query.longitude, min_latitude: req.query.latitude, min_longitude: req.query.longitude, auth_key: auth_key, language_code: lang_code, source_latitude: req.query.source_latitude, source_longitude: req.query.source_longitude, dest_latitude: req.query.dest_latitude, dest_longitude: req.query.dest_longitude, session_token: req.query.session_token, place_id: req.query.place_id, toll_avoid: req.query.toll_avoid, vTollAvoidValue: vTollAvoidValue};
			
			 // console.log(formateStringURL(request_data));
			
			response_remote_data = await getDataFromRemoteURL(formateStringURL(request_data));
			
			var isDataFound = true;
					
			if(response_remote_data){
				if(service_type == "Direction" && vServiceName == "Google" && response_remote_data['status'] && response_remote_data['status'].toUpperCase() == "OK"){
					let routesArr = response_remote_data['routes'];
					
					for(var i_route = 0; i_route < routesArr.length; i_route++){
						let route_obj = routesArr[i_route];
						let legs_arr = route_obj['legs'];
						for(var i_legs = 0; i_legs < legs_arr.length; i_legs++){
							let legs_obj = legs_arr[i_legs];
							let stepsArr = legs_obj['steps'];
							for(var i_steps = 0; i_steps < stepsArr.length; i_steps++){
								let steps_obj = stepsArr[i_steps];
								let points_obj = steps_obj['polyline']['points'];
								steps_obj['data_locations'] = decodePolyline(points_obj);
							}
						}
					}
				}
				
				if(service_type == "Direction" && vServiceName == "OpenMap" && response_remote_data['code'] && response_remote_data['code'].toUpperCase() == "OK"){
					let routesArr = response_remote_data['routes'];
					
					for(var i_route = 0; i_route < routesArr.length; i_route++){
						let route_obj = routesArr[i_route];
						let legs_arr = route_obj['legs'];
						for(var i_legs = 0; i_legs < legs_arr.length; i_legs++){
							let legs_obj = legs_arr[i_legs];
							let stepsArr = legs_obj['steps'];
							for(var i_steps = 0; i_steps < stepsArr.length; i_steps++){
								let steps_obj = stepsArr[i_steps];
								let points_obj = steps_obj['geometry'];
								steps_obj['data_locations'] = decodePolyline(points_obj);
							}
						}
					}
				}
				
				let vServiceReturnData = obj_vServiceReturnData[service_type];
				for(let item_serviceReturnData of vServiceReturnData){
					let vKeyName_value = item_serviceReturnData.vKeyName;
					let vKeyType = item_serviceReturnData.vKeyType;
					let vReturnParam = item_serviceReturnData.vReturnParam;
					let vReturnType = item_serviceReturnData.vReturnType;
					
					
					if(vKeyType == "Array"){
						if(vKeyName_value != "" && (!response_remote_data[vKeyName_value] || response_remote_data[vKeyName_value].length == 0)){
							isDataFound = false;
							break;
						} 
						response_data = Object.assign(response_data, fetchValuesFromAnArray(response_remote_data,vKeyName_value, item_serviceReturnData.vSubKeys, vReturnType, vReturnParam));
						if(isEmptyObject(response_data)){
							isDataFound = false;
							break;
						}
					}else if(vKeyType == "Object"){
						response_data = Object.assign(response_data, fetchValuesFromAnObject(response_remote_data[vKeyName_value], item_serviceReturnData.vSubKeys, vReturnType, vReturnParam));
						
						if(isEmptyObject(response_data)){
							isDataFound = false;
							break;
						}
					}else if(vKeyType == "String"){
						let vReturnParam = item_serviceReturnData.vReturnParam;
						
						let vConversionFormula = item_serviceReturnData.vConversionFormula;
						let vConversionFormulaValue = item_serviceReturnData.vConversionFormulaValue;
						
						if(vConversionFormula && vConversionFormula != "" && vConversionFormulaValue != ""){
							response_remote_data[vKeyName_value] = roundToTwo(response_remote_data[vKeyName_value] * vConversionFormulaValue);
						}
					
						if(response_remote_data[vKeyName_value]){
							response_data[vReturnParam] = response_remote_data[vKeyName_value];
						}else{
							isDataFound = false;
							break;
						}
					}else{
						isDataFound = false;
					}
				}
			}else{
				isDataFound = false;
			}
			
			if(isDataFound){
				if(idOfAccount && idOfAccount != ""){
					await incServiceCount(req, idOfAccount, service_type);
				}
				break;
			}
		}
		
		if(isDataFound){
			break;
		}
	}
	
	if(req.query.latitude && !response_data['latitude']){
		response_data['latitude'] = req.query.latitude;
	}
	
	if(req.query.latitude && !response_data['longitude']){
		response_data['longitude'] = req.query.longitude;
	}
	
	if(response_data['data'] && service_type == "Direction"){
		let sourceLocation = {latitude: req.query.source_latitude, longitude: req.query.source_longitude};
		let destLocation = {latitude: req.query.dest_latitude, longitude: req.query.dest_longitude};
		response_data['data'].unshift(sourceLocation);
		response_data['data'].push(destLocation);
	}
	
	if((service_type == "AutoComplete" || service_type == "Direction") && !response_data['data']){
		response_data['data'] = [];
	}
	
	//res.setHeader("Content-Type", "text/plain");
	
	if((req.query["CipherObj"] === undefined) == false){
		
		var encrypted = req.query["CipherObj"].update(JSON.stringify(response_data), 'utf8', 'binary');
		encrypted += req.query["CipherObj"].final('binary');
		hexVal = Buffer.from(encrypted, 'binary');
		newEncrypted = hexVal.toString('base64');
		
		// console.log("EncText::"+newEncrypted);

		res.send(newEncrypted);
	}else{
		res.jsonp(response_data);
	}
	// res.send(req.originalUrl);
}


