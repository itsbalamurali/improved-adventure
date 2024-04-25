var assert = require('assert')

let client = require('mongodb').MongoClient;

var MONGO_DB_CONN_URL = process.env.MONGO_DB_CONNECTION_URL || "mongodb://localhost:27017/";

var _db;
var dbName = "bbcsprod_development";
var master_data_collection_name = "ServiceChat";
var url = MONGO_DB_CONN_URL;

module.exports = {
    getClient,
    initClient,
	getDB,
	insertMessageData,
	getServiceMessages,
	getUserData,
	getCollectionData
};

async function initClient(callback) {
    /* if (_db) {
        console.warn("Trying to init DB again!");
        return callback(null, null);
    } */
	
	console.log("Trying to init DB!");
	
	if (_db ) {
         // console.log("Trying to init DB again!");
        return callback(client, _db, null);
    }
	
	
	await client.connect(url + dbName+"/?authSource=admin", { useNewUrlParser: true , useUnifiedTopology: true}, connected);
	
	function connected(err, client) {
		if(err){
			//console.log(err);
			return callback(null, null, err);
		}
		if (err) throw err;
         console.log("Client connected");
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

async function insertMessageData(authToken, dataArr){
	// _db.collection(master_data_collection_name).insertOne(dataArr);
	
	_db.collection(master_data_collection_name).updateOne({vAuthToken: authToken}, { $push: {"ServiceMessages": dataArr }} , {upsert: true});
}

async function getUserData(AUTH_DATA){
	assert.ok(_db, "Db has not been initialized. Please called init first.");
	/* if (masterAccountsData){
	   return masterAccountsData;
	} */
	
	var TB_NAME = "";
	
	if(AUTH_DATA.MEMBER_TYPE.toUpperCase() == "PASSENGER" ){
		TB_NAME = "register_user";
	}else if(AUTH_DATA.MEMBER_TYPE.toUpperCase() == "DRIVER" ){
		TB_NAME = "register_driver";
	}else if(AUTH_DATA.MEMBER_TYPE.toUpperCase() == "COMPANY" ){
		TB_NAME = "company";
	}else if(AUTH_DATA.MEMBER_TYPE.toUpperCase() == "TRACKING" ){
		TB_NAME = "track_service_users";
	}else if(AUTH_DATA.MEMBER_TYPE.toUpperCase() == "HOTEL" ){
		TB_NAME = "administrators";
	}
	
	if(TB_NAME == ""){
		return [];
	}
	
	let allMessages = _db.collection(TB_NAME).find({"iMemberId": AUTH_DATA.MEMBER_ID}).toArray();
	//initializeWatchOnMasterData();
	return allMessages;	
}


async function getServiceMessages(authToken){
	assert.ok(_db, "Db has not been initialized. Please called init first.");
	/* if (masterAccountsData){
	   return masterAccountsData;
	} */
	
	let allMessages = _db.collection(master_data_collection_name).find({"vAuthToken": authToken}).toArray();
	//initializeWatchOnMasterData();
	return allMessages;	
}


async function getCollectionData(TB_NAME){
	assert.ok(_db, "Db has not been initialized. Please called init first.");
	
	let allMessages = _db.collection(TB_NAME).find({}).toArray();
	//initializeWatchOnMasterData();
	return allMessages;	
}


async function getCollectionData(TB_NAME, FILTER_PARAM_OBJ){
	assert.ok(_db, "Db has not been initialized. Please called init first.");
	
	let allMessages = _db.collection(TB_NAME).find(FILTER_PARAM_OBJ).toArray();
	//initializeWatchOnMasterData();
	return allMessages;	
}