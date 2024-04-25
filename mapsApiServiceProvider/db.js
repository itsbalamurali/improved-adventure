// var redis = require('redis');
// var client = redis.createClient(6379, "127.0.0.1");
var assert = require('assert');
var client = require('mongodb').MongoClient;
var replicaClient = require('mongodb').MongoClient;
var dateFormat = require('dateformat');

var cloneObj = require('clone');

var dbName = "";

var MONGO_DB_CONN_URL = process.env.MONGO_DB_CONNECTION_URL || "mongodb://localhost:27017/";

var url = MONGO_DB_CONN_URL;
var replicaUrl = "mongodb://localhost:27017?replicaSet=mongo-repl";

var master_data_collection_name = "auth_master_accounts_places";
var account_data_collection_name = "auth_accounts_places";
var auth_report_accounts_places_name = "auth_report_accounts_places";
var request_log_collection_name = "request_log_data";

var masterAccountsData;
var countryData;
var accountsDataAsPerService = {};
var authAccServiceObj = {};
var requestLogCountObjNew = {};
var requestLogData;

var _db;
var _replicaDb;
module.exports = {
    getClient,
    initClient,
	getDB,
	getMasterAuthAccountData,
	getMasterAuthAccountDataForRG,
	getMasterAuthAccountDataForAC,
	getAuthAccountData,
	getMasterAuthAccountDataForService,
	validateLocationsForDirectionService,
	fetchCountryFromLocation,
	incServiceCount,
	insertIntoAuthData
};

function initClient(TSITE_DB, callback) {
    /* if (_db) {
        console.warn("Trying to init DB again!");
        return callback(null, null);
    } */
	
	if (_db && (dbName != "" && dbName == TSITE_DB)) {
         // console.log("Trying to init DB again!");
        return callback(client, _db, null);
    }
	
	dbName = TSITE_DB;
	masterAccountsData = null;
	
	client.connect(url + dbName+"/?authSource=admin", { useNewUrlParser: true , useUnifiedTopology: true}, connected);
	
	function connected(err, client) {
		if(err){
			//console.log(err);
			return callback(null, null, err);
		}
		if (err) throw err;
        // console.log("Client connected");
        _db = client.db(dbName);
        return callback(client, _db, err);
    }
}

function getClient() {
    assert.ok(_db, "Db has not been initialized. Please called init first.");
    return client;
}

function getDB() {
    assert.ok(_db, "Db has not been initialized. Please called init first.");
    return _db;
}

function getMasterAuthAccountData(){
	assert.ok(_db, "Db has not been initialized. Please called init first.");
	/* if (masterAccountsData){
	   return masterAccountsData;
	} */
	
	masterAccountsData = _db.collection(master_data_collection_name).find({}).sort({vUsageOrder: 1}).toArray();
	//initializeWatchOnMasterData();
	return masterAccountsData;	
}

function getCountryData(){
	assert.ok(_db, "Db has not been initialized. Please called init first.");
	if (countryData){
	   return countryData;
	}
	
	countryData = _db.collection("country_boundaries");
	//initializeWatchOnMasterData();
	return countryData;	
}

async function fetchCountryFromLocation(dataArr){
	var isDataValid = true;
	var country_data = await getCountryData();
	
	if(country_data){
		var currentCountryCode = "";
		
		
		var dataCountryArr = await country_data.find({"tBoundary": {"$geoIntersects": {"$geometry": {"type" : "Point", "coordinates": [ parseFloat(dataArr['longitude']), parseFloat(dataArr['latitude']) ]}}}}).toArray();
		
		if(dataArr === undefined || !dataArr || dataArr.length == 0){
			return [];
		}
		
		var dataReturnObj = {latitude: dataArr['latitude'], longitude: dataArr['longitude'], iCountryId: dataCountryArr[0].iCountryId, vCountry: dataCountryArr[0].vCountry, vCountryCode: dataCountryArr[0].vCountryCode, vPhoneCode: dataCountryArr[0].vPhoneCode};
		
		return dataReturnObj;
		
	}
	
	return [];
}

async function validateLocationsForDirectionService(dataOfLocations){
	var isDataValid = true;
	var country_data = await getCountryData();
	if(country_data){
		var currentCountryCode = "";
		
		for(var i = 0; i < dataOfLocations.length; i++){
			let location_tmp = dataOfLocations[i].split(",");
			
			var dataArr = await country_data.find({"tBoundary": {"$geoIntersects": {"$geometry": {"type" : "Point", "coordinates": [ parseFloat(location_tmp[1]), parseFloat(location_tmp[0]) ]}}}}).toArray();
			
			if(dataArr === undefined || !dataArr || dataArr.length == 0){
				continue;
			}
		
			let countryCode_tmp = dataArr[0].vCountryCode;		
			
			if(currentCountryCode == ""){
				currentCountryCode = countryCode_tmp;
				continue;
			}else if((currentCountryCode.toUpperCase() === countryCode_tmp.toUpperCase()) == false){
				isDataValid = false;
				break;
			}
		}
	}
	
	return isDataValid;
}

function getAuthAccountData(vServiceId, vServiceAccountAuthKey){
	assert.ok(_db, "Db has not been initialized. Please called init first.");
	
	if(!vServiceId){
		return [];
	}
	
	if(vServiceAccountAuthKey != ""){
		var account_data = {auth_key: vServiceAccountAuthKey, _id: ""}
		var account_data_arr = [];
		account_data_arr.push({auth_key: vServiceAccountAuthKey, _id: ''}); 
		return account_data_arr;
	}

	/* if (accountsDataAsPerService[vServiceId]){
	   return accountsDataAsPerService[vServiceId];
	} */
	
	accountsDataAsPerService[vServiceId] = _db.collection(account_data_collection_name).find({"eStatus": 'Active', "vServiceId": vServiceId}).sort({vUsageOrder: 1}).toArray();
	//initializeWatchOnMasterData();
	return accountsDataAsPerService[vServiceId];	
}
	
async function getMasterAuthAccountDataForService(vServiceName, vServiceAccountId){
	var master_data = await getMasterAuthAccountData();
	
	var data_arr = [];
	
	master_data.forEach(function(entry) {
		var vActiveServices = entry.vActiveServices;
		var eStatus = entry.eStatus;
		var vServiceId = parseInt(entry.vServiceId);
		
		if(eStatus == "Active") {
			if(vServiceName && vServiceName != ""){
				if((new RegExp("\\b"+vServiceName+"\\b").test(vActiveServices) && vServiceAccountId == "") || (new RegExp("\\b"+vServiceName+"\\b").test(vActiveServices) && vServiceAccountId != "" && vServiceId == vServiceAccountId)){
					data_arr.push(entry);
				}
			}else{
				data_arr.push(entry);
			}
		}
		
	});
	
	return data_arr;
}
	
async function getMasterAuthAccountDataForRG(){
	var master_data = await getMasterAuthAccountData();
	
	var data_arr = [];
	
	master_data.forEach(function(entry) {
		var vActiveServices = entry.vActiveServices;
		
		if(new RegExp("\\b"+"Geocoding"+"\\b").test(vActiveServices)){
			data_arr.push(entry);
		}
	});
	
	return data_arr;
}
	
async function getMasterAuthAccountDataForAC(){
	var master_data = await getMasterAuthAccountData();
	
	var data_arr = [];
	
	master_data.forEach(function(entry) {
		var vActiveServices = entry.vActiveServices;
		
		if(new RegExp("\\b"+"AutoComplete"+"\\b").test(vActiveServices)){
			data_arr.push(entry);
		}
	});
	
	return data_arr;
}

async function incServiceCount(req, accId, vServiceName){
	// var currDate = dateFormat(new Date(new Date().toISOString()), "yyyy-mm-dd");
	// var currDate = dateFormat(new Date().toISOString(), "yyyy-mm-dd");
	// currDate = currDate + " 00:00:00";
	// currDate = currDate.toLocaleString('en-US', {
	//   	timeZone: 'UTC'
	// });
	// var currDate_obj = new Date(currDate);
	
	// var service_obj = {};
	// service_obj[accId + "." + vServiceName] = 1;

	if(authAccServiceObj.hasOwnProperty([accId + "." + vServiceName])) {
        authAccServiceObj[accId + "." + vServiceName] = authAccServiceObj[accId + "." + vServiceName] + 1;
    }
    else {
        authAccServiceObj[accId + "." + vServiceName] = 1;
    }

	let ip = req.clientIp.toString().replace('::ffff:', '').replaceAll('.', '-');

	if(!requestLogCountObjNew.hasOwnProperty([ip])){
		requestLogCountObjNew[ip] = {}
	}

	if(!requestLogCountObjNew[ip][vServiceName]){
		requestLogCountObjNew[ip][vServiceName] = {}
		requestLogCountObjNew[ip][vServiceName]['TotalCount'] = 0
		requestLogCountObjNew[ip][vServiceName]['RequestParameters'] = []
	}
	

	requestLogCountObjNew[ip][vServiceName]['TotalCount'] = requestLogCountObjNew[ip][vServiceName]['TotalCount'] +1;
	requestLogCountObjNew[ip][vServiceName]['RequestParameters'].push(req.query);
}

async function insertIntoAuthData(dataArr){
	_db.collection(account_data_collection_name).insertOne({vTitle: dataArr["vTitle"], vServiceId: dataArr["vServiceId"], auth_key: dataArr["auth_key"], EntityType: dataArr["EntityType"], vUsageOrder: dataArr["vUsageOrder"], eStatus: dataArr["eStatus"]});
}


function initializeWatchOnMasterData(){
	
    console.log("ReplicaClient connecting");
		
	replicaClient.connect(replicaUrl, { useNewUrlParser: true , useUnifiedTopology: true}, replicaConnected);
	
	
	function replicaConnected(err, client) {
		if (err) throw err;
        console.log("ReplicaClient connected");
       
	   _replicaDb = client.db(dbName);
	   _replicaDb.collection(master_data_collection_name).watch().on("change", function(change) {
		  console.log("DataChange in Master");
		  console.log(change);
		});
		
		return callback(client, _replicaDb);
    }
	
	/* console.log("DataChange in Master Wach initializeWatchOnMasterData");
	console.log("MasterDBName:"+master_data_collection_name);
	
	const pipeline = [{$project: { documentKey: false }}];

	const changeStream = _db.collection(master_data_collection_name).watch();
	changeStream.on("change", function(change) {
      console.log("DataChange in Master");
      console.log(change);
    }); */
}

async function incServiceCountExecute() {
	var currDate = dateFormat(new Date().toISOString(), "yyyy-mm-dd");
	currDate = currDate + " 00:00:00";
	currDate = currDate.toLocaleString('en-US', {
	  	timeZone: 'UTC'
	});
	var currDate_obj = new Date(currDate);
	
	var authAccUpdateObj = {};
    for (let key in authAccServiceObj) {
        authAccUpdateObj[key] = authAccServiceObj[key];
    }

    // console.log(authAccServiceObj);

    var isEmpty = Object.keys(authAccServiceObj).length === 0;

    if(!isEmpty) {
    	await _db.collection(auth_report_accounts_places_name).updateOne({vUsageDate: currDate_obj, vDateInMilli: currDate_obj.valueOf(), iYear: currDate_obj.getUTCFullYear(), iMonth: (currDate_obj.getUTCMonth() + 1), iDay: currDate_obj.getUTCDate()}, { $inc: authAccServiceObj} , {upsert: true});
    }
	
	for (let key in authAccServiceObj) {
        authAccServiceObj[key] = authAccServiceObj[key] - authAccUpdateObj[key];
    }
}

async function incServiceLogExecute() {
	var currDate = dateFormat(new Date().toISOString(), "yyyy-mm-dd");
	currDate = currDate + " 00:00:00";
	currDate = currDate.toLocaleString('en-US', {
	  	timeZone: 'UTC'
	});
	var currDate_obj = new Date(currDate);
	
	let requestLogCountObjNew_clone = await cloneObj(requestLogCountObjNew);

    var isEmptyLogCount = Object.keys(requestLogCountObjNew_clone).length === 0;

    if(!isEmptyLogCount) {

    	for (const parentKey in requestLogCountObjNew_clone) {
			const parentUpdateData = requestLogCountObjNew_clone[parentKey];
			for (const nestedKey in parentUpdateData) {
				const nestedUpdateData = parentUpdateData[nestedKey];

				if(requestLogCountObjNew[parentKey][nestedKey]['TotalCount'] > 0) {
					requestLogCountObjNew[parentKey][nestedKey]['TotalCount'] -= requestLogCountObjNew_clone[parentKey][nestedKey]['TotalCount'];

					for (const nestedKeyParams in nestedUpdateData['RequestParameters']) {
						delete requestLogCountObjNew[parentKey][nestedKey]['RequestParameters'][nestedKeyParams];
					}
				}
				
				requestLogCountObjNew[parentKey][nestedKey]['RequestParameters'] = requestLogCountObjNew[parentKey][nestedKey]['RequestParameters'].filter(function(e){return e});
			}
		}
		
		
		const bulkOperations = [];

		for (const parentKey in requestLogCountObjNew_clone) {
		    const parentUpdateData = requestLogCountObjNew_clone[parentKey];

		    for (const nestedKey in parentUpdateData) {
		        const filter = {vUsageDate: currDate_obj, vDateInMilli: currDate_obj.valueOf(), iYear: currDate_obj.getUTCFullYear(), iMonth: (currDate_obj.getUTCMonth() + 1), iDay: currDate_obj.getUTCDate()
		        };

		        if(parentUpdateData[nestedKey]['TotalCount'] > 0) {
		        	const update = {
			            $inc: {
			                ['Data.' + parentKey + '.' + nestedKey + '.TotalCount']: parentUpdateData[nestedKey]['TotalCount']
			            },
			            $push: {
			                ['Data.' + parentKey + '.' + nestedKey + '.RequestParameters']: {
			                    $each: parentUpdateData[nestedKey]['RequestParameters']
			                }
			            }
			        };

			        bulkOperations.push({
			            updateOne: {
			                filter,
			                update,
			                upsert: true
			            }
			        });
		        }
		    }
		}

		var isEmptybulkOperations = Object.keys(bulkOperations).length === 0;
		// Execute bulk write operations
		if(!isEmptybulkOperations) {
			await _db.collection(request_log_collection_name).bulkWrite(bulkOperations);	
		}
		
    }	
}

setInterval(function(){
    incServiceCountExecute()
}, 20000);

setInterval(function(){
    incServiceLogExecute()
}, 30000); 