const apiTypes = require("./apiTypes")
var cloneObj = require('clone');

const getUserData = require("./db").getUserData;
const getCollectionData = require("./db").getCollectionData;
const setDataRes = require("./setDataRes");

var memberData = {};
var staticData = {};
var staticPages = {};
var FAQsData = {};
var CancelReasonData = {};
var appImagesData = "";

var sysCacheData = {};

/* module.exports = function (req, res) { 
    
	let api_func_name = apiTypes(req, res);
		
	handleMemberDataProperty(req.AUTH_DATA);
	
	if(api_func_name != ""){
		eval(api_func_name)(req, res);
	}
}; */

module.exports = {exeDataRequest, exeClientRequest, UpdateStaticData, UpdateStaticPages, UpdateFAQsData, UpdateCancelReasons, UpdateAppImages, UpdateCabRequestData, UpdateGeneralConfigData};

function handleMemberDataProperty(AUTH_DATA){
	if(!memberData[AUTH_DATA.MEMBER_TYPE]){
		memberData[AUTH_DATA.MEMBER_TYPE] = {};
	}
	
	if(!memberData[AUTH_DATA.MEMBER_TYPE][AUTH_DATA.MEMBER_ID]){
		memberData[AUTH_DATA.MEMBER_TYPE][AUTH_DATA.MEMBER_ID] = "";
	}
}

function exeDataRequest(req, res) { 
    
	let api_func_name = apiTypes(req, res);
	
	handleMemberDataProperty(req.AUTH_DATA);
	
	if (eval(`typeof ${api_func_name}`) == "function") {
		eval(api_func_name)(req, res);
	}else{
		setDataRes({Action: "1", message: "Invalid path request."}, req, res);
	}

	/* if(api_func_name != ""){
		
	} */
}

async function exeClientRequest(client){
	handleMemberDataProperty(client.AUTH_DATA);
	if(memberData[client.AUTH_DATA.MEMBER_TYPE][client.AUTH_DATA.MEMBER_ID] == ""){
		console.log("Fetching");
		let origData = await getUserData(client.AUTH_DATA);
		memberData[client.AUTH_DATA.MEMBER_TYPE][client.AUTH_DATA.MEMBER_ID] = JSON.stringify(origData[0].MEMBER_DATA);
	}
	
	if(memberData[client.AUTH_DATA.MEMBER_TYPE][client.AUTH_DATA.MEMBER_ID] == ""){
		client.send(JSON.stringify({Action: 0, message: "Invalid request."}));
		
		return;
	}
	console.log("Sending");
		 
	// client.send({MEMBER_PROFILE_DATA: memberData[client.AUTH_DATA.MEMBER_TYPE][client.AUTH_DATA.MEMBER_ID], DATA_TYPE: "APP_SERVICE"});
	client.send(memberData[client.AUTH_DATA.MEMBER_TYPE][client.AUTH_DATA.MEMBER_ID]);
		 
	 
	
}

async function getDetail(req, res){
	
	// console.log("Memdata");
	// console.log(memberData[req.AUTH_DATA.MEMBER_TYPE][req.AUTH_DATA.MEMBER_ID]);
	if(memberData[req.AUTH_DATA.MEMBER_TYPE][req.AUTH_DATA.MEMBER_ID] == ""){
		let origData = await getUserData(req.AUTH_DATA);
		memberData[req.AUTH_DATA.MEMBER_TYPE][req.AUTH_DATA.MEMBER_ID] = JSON.stringify(origData[0].MEMBER_DATA);
	}
	
	if(memberData[req.AUTH_DATA.MEMBER_TYPE][req.AUTH_DATA.MEMBER_ID] == ""){
		setDataRes({Action: "1", message: "Invalid request."}, req, res);
		return;
	}
	
	res.send(memberData[req.AUTH_DATA.MEMBER_TYPE][req.AUTH_DATA.MEMBER_ID]);
}


async function UpdateMemberData(req, res){
	
	let origData = await getUserData(req.AUTH_DATA);
	memberData[req.AUTH_DATA.MEMBER_TYPE][req.AUTH_DATA.MEMBER_ID] = JSON.stringify(origData[0].MEMBER_DATA);
	
	setDataRes({Action: "1"}, req, res);
}

async function UpdateStaticData(req, res){
	
	let static_data = (await getCollectionData("static_data"))[0];
	
	for (const lng_item of Object.keys(static_data)) {
		if(lng_item == "_id"){
			continue;
		}
		staticData[lng_item] = {};
		for (const currency_item of Object.keys(static_data[lng_item]['message']['GIFT_CARD_DATA']['GIFT_CARD_MAX_AMOUNT_WITH_SYMBOL'])) {
			staticData[lng_item][currency_item] = await cloneObj(static_data[lng_item]);			
			staticData[lng_item][currency_item]['message']['GIFT_CARD_DATA']['GIFT_CARD_MAX_AMOUNT_WITH_SYMBOL']=staticData[lng_item][currency_item]['message']['GIFT_CARD_DATA']['GIFT_CARD_MAX_AMOUNT_WITH_SYMBOL'][currency_item];
			staticData[lng_item][currency_item] = JSON.stringify(staticData[lng_item][currency_item]);
		}		
	}
	
	if((Object.keys(req).length < 1) == false){
		setDataRes({Action: "1"}, req, res);
	}
	
}

async function loadStaticInfo(req, res){
	
	if(Object.keys(staticData).length < 1){
		await UpdateStaticData({},{});
	}
	
	if(!staticData[req.AUTH_DATA.MEMBER_LANGUAGE] || !staticData[req.AUTH_DATA.MEMBER_LANGUAGE][req.AUTH_DATA.MEMBER_CURRENCY]){
		setDataRes({Action: "0", "message": "Invalid Request Parameters."}, req, res);
		return;
	}
	 
	let static_data_user_obj = staticData[req.AUTH_DATA.MEMBER_LANGUAGE][req.AUTH_DATA.MEMBER_CURRENCY];
	 
	
	if((Object.keys(req).length < 1) == false){
		setDataRes(static_data_user_obj, req, res);
	}
}

async function UpdateStaticPages(req, res){
	
	let static_pages = (await getCollectionData("static_pages"));
	
	for (const page_item of static_pages) {
		//staticPages[page_item['iPageId']] = page_item['ALL_DATA'];
		
			staticPages[page_item['iPageId']] = {};
		
		for (const lng_item of Object.keys(page_item['ALL_DATA'])) {
			staticPages[page_item['iPageId']][lng_item] = {};
			staticPages[page_item['iPageId']][lng_item] = page_item['ALL_DATA'][lng_item];
			staticPages[page_item['iPageId']][lng_item] = JSON.stringify(staticPages[page_item['iPageId']][lng_item]);
		}
		
	}
		
	if((Object.keys(req).length < 1) == false){
		setDataRes({Action: "1"}, req, res);
	}
}

async function staticPage(req, res){
	
	if(Object.keys(staticPages).length < 1){
		await UpdateStaticPages({},{});
	}
	
	if(!staticPages[req.query.iPageId]){
		setDataRes({Action: "0", "message": "Invalid Page ID."}, req, res);
		return;
	}
	
	let static_page_user_obj = staticPages[req.query.iPageId][req.AUTH_DATA.MEMBER_LANGUAGE];
	
	if((Object.keys(req).length < 1) == false){
		setDataRes(static_page_user_obj, req, res);
	}
}

async function UpdateFAQsData(req, res){
	let faqs_data = (await getCollectionData("faqs"))[0];
	
	for (const lng_item of Object.keys(faqs_data)) {
		if(lng_item == "_id"){
			continue;
		}
		FAQsData[lng_item] = {};
		
		for (const member_type of Object.keys(faqs_data[lng_item])) {
			FAQsData[lng_item][member_type] = JSON.stringify(faqs_data[lng_item][member_type]);
		}
	}
	
	if((Object.keys(req).length < 1) == false){
		setDataRes({Action: "1"}, req, res);
	}
}

async function getFAQ(req, res){
	if(Object.keys(FAQsData).length < 1){
		await UpdateFAQsData({},{});
	}
	
	if(!FAQsData[req.AUTH_DATA.MEMBER_LANGUAGE] || !FAQsData[req.AUTH_DATA.MEMBER_LANGUAGE][req.AUTH_DATA.MEMBER_TYPE]){
		setDataRes({Action: "0", "message": "Invalid Request Parameters."}, req, res);
		return;
	}
	
	let faqs_data_obj = FAQsData[req.AUTH_DATA.MEMBER_LANGUAGE][req.AUTH_DATA.MEMBER_TYPE];
	
	if((Object.keys(req).length < 1) == false){
		setDataRes(faqs_data_obj, req, res);
	}
}

async function UpdateCancelReasons(req, res){
	let cancel_reason_data = (await getCollectionData("cancel_reason"))[0];
	
	for (const lng_item of Object.keys(cancel_reason_data)) {
		if(lng_item == "_id"){
			continue;
		}
		CancelReasonData[lng_item] = {};
		
		for (const member_type of Object.keys(cancel_reason_data[lng_item])) {
			
			CancelReasonData[lng_item][member_type] = {};
			
			for (const job_type of Object.keys(cancel_reason_data[lng_item][member_type])) {
			
				CancelReasonData[lng_item][member_type][job_type] = cancel_reason_data[lng_item][member_type][job_type];
				
				CancelReasonData[lng_item][member_type][job_type] = JSON.stringify(CancelReasonData[lng_item][member_type][job_type]);
			}
			
		}
		
	}
	
	if((Object.keys(req).length < 1) == false){
		setDataRes({Action: "1"}, req, res);
	}
}

async function GetCancelReasons(req, res){
	if(Object.keys(CancelReasonData).length < 1){
		await UpdateCancelReasons({},{});
	}
	
	if(!CancelReasonData[req.AUTH_DATA.MEMBER_LANGUAGE] || !CancelReasonData[req.AUTH_DATA.MEMBER_LANGUAGE][req.AUTH_DATA.MEMBER_TYPE] || !CancelReasonData[req.AUTH_DATA.MEMBER_LANGUAGE][req.AUTH_DATA.MEMBER_TYPE][req.AUTH_DATA.JOB_TYPE]){
		setDataRes({Action: "0", "message": "Invalid Request Parameters."}, req, res);
		return;
	}
	
	let cancelReasonData_obj = CancelReasonData[req.AUTH_DATA.MEMBER_LANGUAGE][req.AUTH_DATA.MEMBER_TYPE][req.AUTH_DATA.JOB_TYPE];
	
	if((Object.keys(req).length < 1) == false){
		setDataRes(cancelReasonData_obj, req, res);
	}
}

async function UpdateAppImages(req, res){
	let app_images_data = (await getCollectionData("app_images"))[0];
	
	appImagesData = JSON.stringify(app_images_data);
	
	if((Object.keys(req).length < 1) == false){
		setDataRes({Action: "1"}, req, res);
	}
}

async function getAppImages(req, res){
	if(Object.keys(appImagesData).length < 1){
		await UpdateAppImages({},{});
	}
	
	setDataRes(appImagesData, req, res);
}

async function UpdateCabRequestData(req, res){
	
	let service_request_now_data = await getCollectionData("cab_request_now");
	
	sysCacheData['ServiceReqData'] = {};
	sysCacheData['ServiceReqData']['General'] = {};
	sysCacheData['ServiceReqData']['DeliverAll'] = {};

	for (const service_request of service_request_now_data) {
		let eSystemType = service_request['eSystemType'];
		let DRIVER_DATA = service_request['DRIVER_DATA'];
		let DRIVER_ID_ARR = Object.keys(DRIVER_DATA);
		
		let service_id = "";
		if(eSystemType == "General"){
			service_id= service_request['iCabRequestId'];
		}else{
			service_id= service_request['iOrderId'];
		}
		
		sysCacheData['ServiceReqData'][eSystemType][service_id] = {};
		
		for (const driver_id of DRIVER_ID_ARR) {
			sysCacheData['ServiceReqData'][eSystemType][service_id][driver_id]=JSON.stringify(DRIVER_DATA[driver_id]);
		}
	}
	
	if((Object.keys(req).length < 1) == false){
		setDataRes({Action: "1"}, req, res);
	}
}

async function getCabRequestAddress(req, res){
	if(!sysCacheData['ServiceReqData'] || Object.keys(sysCacheData['ServiceReqData']).length < 1){
		await UpdateCabRequestData({},{});
	}
	
	let eSystemType = "";
	let service_id = "";
	
	if(!req.query.iOrderId || req.query.iOrderId == ""){
		service_id = req.query.iCabRequestId;
		eSystemType = "General";
	}else{
		service_id = req.query.iOrderId;
		eSystemType = "DeliverAll";
	}
	
	
	if(sysCacheData['ServiceReqData'][eSystemType][service_id] && sysCacheData['ServiceReqData'][eSystemType][service_id][req.query.GeneralMemberId]){
		setDataRes(sysCacheData['ServiceReqData'][eSystemType][service_id][req.query.GeneralMemberId], req, res);
	}else{
		setDataRes({Action: "0", message: "Invalid Parameters"}, req, res);
	}	
}

async function uploadImage(req, res){
	setDataRes({Action: "1"}, req, res);
}

async function UpdateGeneralConfigData(req, res){
	
	// let general_config_data = await getCollectionData("GeneralConfigData", { eUserType: req.query.UserType, eDeviceType: { $regex : new RegExp(req.query.Platform, "i") } });
	let general_config_data = await getCollectionData("GeneralConfigData");
	
	sysCacheData['GeneralConfigData'] = {};
	
		sysCacheData['GeneralConfigData']["ANDROID"] = {};
		sysCacheData['GeneralConfigData']["IOS"] = {};
	
	for (const item of general_config_data) {
		sysCacheData['GeneralConfigData'][item['eDeviceType'].toUpperCase()][item['eUserType'].toUpperCase()] = JSON.stringify(item['ALL_DATA']);
	}
	
	if((Object.keys(req).length < 1) == false){
		setDataRes({Action: "1"}, req, res);
	}
	
}

async function generalConfigData(req, res){
	if(!sysCacheData['GeneralConfigData'] || Object.keys(sysCacheData['GeneralConfigData']).length < 1){
		await UpdateGeneralConfigData({},{});
	}
	
	if(!req.query.Platform || !req.query.UserType){
		setDataRes({Action: "0", message: "Invalid Parameters"}, req, res);
		return;
	}
	
	let eDeviceType = req.query.Platform.toUpperCase();
	let eUserType = req.query.UserType.toUpperCase();
	
	// console.log(sysCacheData['GeneralConfigData'][eDeviceType][eUserType]);
	
	if(sysCacheData['GeneralConfigData'][eDeviceType][eUserType]){
		setDataRes(sysCacheData['GeneralConfigData'][eDeviceType][eUserType], req, res);
	}else{
		setDataRes({Action: "0", message: "Invalid Parameters"}, req, res);
	}
	
}