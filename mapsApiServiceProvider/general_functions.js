// var redis = require('redis');
// var client = redis.createClient(6379, "127.0.0.1");
var assert = require('assert');
const axios = require('axios');


module.exports = {
	getDataFromRemoteURL,
	formateStringURLForRG,
	formateStringURL,
	fetchValuesFromAnArray,
	fetchValuesFromAnObject,
	isEmptyObject,
	roundToTwo,
	regenerateArrayBasedOnDistance,
	generateArrFromStr,
	insertAt,
	generateASCIIString,
	getAuthAccData
};
	
async function getDataFromRemoteURL(url_str){
	var data_arr;
	
	await axios.get(url_str)
			.then(response => {
				data_arr = response.data;
			})
			.catch(error => {
				//console.log(error);
			});
	
	return data_arr;
}

function formateStringURLForRG(url_str, latitude, longitude, auth_key, language_code){
	var formatted_url_str = url_str;
	
	formatted_url_str = formatted_url_str.replace(/@{latitude}/g, latitude);
	formatted_url_str = formatted_url_str.replace(/@{longitude}/g, longitude);
	formatted_url_str = formatted_url_str.replace(/@{auth_key}/g, auth_key);
	formatted_url_str = formatted_url_str.replace(/@{language_code}/g, language_code); 
	
	return formatted_url_str;
}

function formateStringURL(dataArr){
	var formatted_url_str = dataArr['URL'];
	
	if(dataArr['toll_avoid'] || dataArr['toll_avoid'] == ""){
		formatted_url_str = formatted_url_str.replace(/@{toll_avoid}/g, dataArr['vTollAvoidValue']);
	}else{
		formatted_url_str = formatted_url_str.replace(/@{toll_avoid}/g, "");
	}
	
	if(dataArr['search_query']){
		formatted_url_str = formatted_url_str.replace(/@{search_query}/g, dataArr['search_query']);
	}else{
		formatted_url_str = formatted_url_str.replace(/@{search_query}/g, "");
	}
	
	if(dataArr['source_latitude']){
		formatted_url_str = formatted_url_str.replace(/@{source_latitude}/g, dataArr['source_latitude']);
	}else{
		formatted_url_str = formatted_url_str.replace(/@{source_latitude}/g, "");
	}
	
	if(dataArr['source_longitude']){
		formatted_url_str = formatted_url_str.replace(/@{source_longitude}/g, dataArr['source_longitude']);
	}else{
		formatted_url_str = formatted_url_str.replace(/@{source_longitude}/g, "");
	}
	
	if(dataArr['dest_latitude']){
		formatted_url_str = formatted_url_str.replace(/@{dest_latitude}/g, dataArr['dest_latitude']);
	}else{
		formatted_url_str = formatted_url_str.replace(/@{dest_latitude}/g, "");
	}
	
	if(dataArr['dest_longitude']){
		formatted_url_str = formatted_url_str.replace(/@{dest_longitude}/g, dataArr['dest_longitude']);
	}else{
		formatted_url_str = formatted_url_str.replace(/@{dest_longitude}/g, "");
	}
	
	if(dataArr['latitude']){
		formatted_url_str = formatted_url_str.replace(/@{latitude}/g, dataArr['latitude']);
	}else{
		formatted_url_str = formatted_url_str.replace(/@{latitude}/g, "");
	}
	
	if(dataArr['longitude']){
		formatted_url_str = formatted_url_str.replace(/@{longitude}/g, dataArr['longitude']);
	}else{
		formatted_url_str = formatted_url_str.replace(/@{longitude}/g, "");
	}
	
	if(dataArr['max_latitude']){
		formatted_url_str = formatted_url_str.replace(/@{max_latitude}/g, dataArr['max_latitude']);
	}else{
		formatted_url_str = formatted_url_str.replace(/@{max_latitude}/g, "");
	}
	
	if(dataArr['max_longitude']){
		formatted_url_str = formatted_url_str.replace(/@{max_longitude}/g, dataArr['max_longitude']);
	}else{
		formatted_url_str = formatted_url_str.replace(/@{max_longitude}/g, "");
	}
	
	if(dataArr['min_latitude']){
		formatted_url_str = formatted_url_str.replace(/@{min_latitude}/g, dataArr['min_latitude']);
	}else{
		formatted_url_str = formatted_url_str.replace(/@{min_latitude}/g, "");
	}
	
	if(dataArr['min_longitude']){
		formatted_url_str = formatted_url_str.replace(/@{min_longitude}/g, dataArr['min_longitude']);
	}else{
		formatted_url_str = formatted_url_str.replace(/@{min_longitude}/g, "");
	}
	
	if(dataArr['auth_key']){
		formatted_url_str = formatted_url_str.replace(/@{auth_key}/g, dataArr['auth_key']);
	}else{
		formatted_url_str = formatted_url_str.replace(/@{auth_key}/g, "");
	}
	
	if(dataArr['language_code']){
		formatted_url_str = formatted_url_str.replace(/@{language_code}/g, dataArr['language_code']);
	}else{
		formatted_url_str = formatted_url_str.replace(/@{language_code}/g, "");
	}
	
	if(dataArr['session_token']){
		formatted_url_str = formatted_url_str.replace(/@{session_token}/g, dataArr['session_token']);
	}else{
		formatted_url_str = formatted_url_str.replace(/@{session_token}/g, "");
	}
	
	if(dataArr['place_id']){
		formatted_url_str = formatted_url_str.replace(/@{place_id}/g, dataArr['place_id']);
	}else{
		formatted_url_str = formatted_url_str.replace(/@{place_id}/g, "");
	}
	
	return formatted_url_str;
}

function fetchValuesFromAnArray(response_remote_data,vArrKeyName, vSubKeys, vReturnType, vMainReturnParam){
	var dataArrObj = {};
	var dataArr = [];
	var response_remote_data_arr = [];
	
	if(vArrKeyName){
		response_remote_data_arr = response_remote_data[vArrKeyName]
	}else{
		response_remote_data_arr = response_remote_data;
	}
	
	if(response_remote_data_arr && response_remote_data_arr.length > 0){
	
		var isKeyValuePairArr = true;
		
		var countOfPosition = 0;
		
		for(let i = 0; i < response_remote_data_arr.length; i++){
		
			let item = response_remote_data_arr[i];
			
			if(!dataArr[i]){
				dataArr[i] = {};
			}
			
			if(vSubKeys.length > 1){
				countOfPosition = 0;
			}
			
			for(let item_vSubKeys of vSubKeys) {
				let vKeyName = item_vSubKeys.vKeyName;
				let vKeyType = item_vSubKeys.vKeyType;
				let vReturnParam = item_vSubKeys.vReturnParam;
				
				if(item_vSubKeys.vReturnPosition && item_vSubKeys.vReturnPosition != ""){
					let vReturnPosition = item_vSubKeys.vReturnPosition;
					if((vReturnPosition == "BEGIN" && i > 0) || (vReturnPosition == "END" && i != (response_remote_data_arr.length - 1))){
						continue;
					}
				}
				
				if(vKeyName == ""){
					
					if(vSubKeys.length > 1){
						if(dataArr[i] && typeof dataArr[i] === 'object'){
							dataArr = [];
						}
						
						dataArr[vReturnParam] = response_remote_data_arr[countOfPosition];
					}else{
						if(dataArr[i] && typeof dataArr[i] === 'object' && (dataArr.length == 1 || dataArr.length == 0)){
							dataArr = [];
						}else if(dataArr[i] && typeof dataArr[i] === 'object'){
							dataArr.pop();
						}
						dataArr.push(response_remote_data_arr[countOfPosition]);
					}
					countOfPosition++;
					//continue;
				}
				
				if(vKeyType == "Object"){
					if(!item[vKeyName]){
						continue;
					}
					let vSubKeys_obj = item[vKeyName];
					let vSubKeys_arr = item_vSubKeys.vSubKeys;
					let vReturnParam_obj = item_vSubKeys.vReturnParam;
					let vReturnType_obj = item_vSubKeys.vReturnType;
					
					var obj_data = fetchValuesFromAnObject(vSubKeys_obj, vSubKeys_arr, vReturnType_obj, vReturnParam_obj);
					
					if(vReturnParam_obj != ""){
						dataArr[i][vReturnParam_sub] = obj_data;
					}else{
						dataArr[i] = Object.assign(dataArr[i], obj_data);
					}
				}else if(vKeyType == "Array"){
					let vSubKeys_sub = item_vSubKeys.vSubKeys;
					let vReturnType_sub = item_vSubKeys.vReturnType;
					var dataOfSubArry = fetchValuesFromAnArray(response_remote_data_arr[i],vKeyName, vSubKeys_sub,vReturnType_sub, vReturnParam);
					
					if(item_vSubKeys.vSkipPositions && Array.isArray(dataOfSubArry)){
						let vSkipPositions_arr = item_vSubKeys.vSkipPositions.split(",");
						var dataOfSubArray_tmp_final = [];
						
						for(let idk = 0; idk < dataOfSubArry.length; idk++){
							if((!vSkipPositions_arr.includes(""+idk) && (idk + 1) < dataOfSubArry.length) || ((idk + 1) == dataOfSubArry.length && !vSkipPositions_arr.includes("-1"))){
								dataOfSubArray_tmp_final.push(!isNaN(dataOfSubArry[idk]) ? (parseInt(dataOfSubArry[idk]) - 1) : dataOfSubArry[idk]);
							}
						}
						dataOfSubArry = dataOfSubArray_tmp_final;
					}
					
					if(vReturnParam == ""){
					
						if(response_remote_data_arr.length > 1){
							dataArr[i] = dataOfSubArry;
						}else{
							if(dataArr && typeof dataArr[0] === 'object' && isEmptyObject(dataArr[0])){
								dataArr = [];
							}
							
							let dataOfSubArry_tmp_arr = Object.assign([],dataOfSubArry);
							
							if(vReturnType == "String" && dataOfSubArry_tmp_arr.length > 1){
								let dataOfSubArry_tmp = dataOfSubArry_tmp_arr[0];
								for(let idk = 1; idk < dataOfSubArry_tmp_arr.length; idk++){
									let keys_arr = Object.keys(dataOfSubArry_tmp_arr[idk]);
									for(let keys_arr_item of keys_arr){
										if(dataOfSubArry_tmp[keys_arr_item] && dataOfSubArry_tmp[keys_arr_item] && !isNaN(dataOfSubArry_tmp[keys_arr_item])){
											dataOfSubArry_tmp[keys_arr_item] = dataOfSubArry_tmp[keys_arr_item] + dataOfSubArry_tmp_arr[idk][keys_arr_item];
										}else if(!dataOfSubArry_tmp.hasOwnProperty(keys_arr_item)){
											dataOfSubArry_tmp[keys_arr_item] = dataOfSubArry_tmp_arr[idk][keys_arr_item];
										}
									}
								}
								dataOfSubArry = dataOfSubArry_tmp;
							}
							
							dataArr = Object.assign(dataArr, dataOfSubArry);
						}
					}else{
						dataArr[i][vReturnParam] = dataOfSubArry;
					}
					
					if(Array.isArray(dataOfSubArry) && vMainReturnParam != ""){
						dataArr = {};
						dataArr[vMainReturnParam] = dataOfSubArry;
					}
					
				}else if(vKeyType == "String"){
					let vConversionFormula = item_vSubKeys.vConversionFormula;
					let vConversionFormulaValue = item_vSubKeys.vConversionFormulaValue;
					
					if(vConversionFormula && vConversionFormula != "" && vConversionFormulaValue != ""){
						item[vKeyName] = roundToTwo(item[vKeyName] * vConversionFormulaValue);
					}
					
					if(vReturnParam && vReturnParam != "" && vKeyName != ""){
						if(!dataArr[i]){
							dataArr[i] = "";
						}
						dataArr[i][vReturnParam] = item[vKeyName];
					}else{
						isKeyValuePairArr = false;
						if(dataArr[i] && typeof dataArr[i] === 'object'&& (dataArr.length == 1 || dataArr.length == 0)){
							dataArr = [];
						}else if(dataArr[i] && typeof dataArr[i] === 'object'){
							dataArr.pop();
						}
						if(item[vKeyName]){
							dataArr.push(item[vKeyName]);
						}
						
					}
				}
			}
		}
		
		if(!isKeyValuePairArr && vSubKeys.length > 1){
			let strOfdataArr = dataArr.join(", ");
			return strOfdataArr;
		}
		
		if(vReturnType == "Array" && Array.isArray(dataArr)){
			
			var dataArr_final_1 = [];
			var count_dataArr_final_1 = 0;
			
			for(let item_dataArr_final of dataArr) {

				let keys_obj_arr = Object.keys(item_dataArr_final);
				
				for(let item_key of keys_obj_arr) {				
					if(typeof item_dataArr_final[item_key] == "object"){
						dataArr_final_1.push(item_dataArr_final[item_key]);
						count_dataArr_final_1++;
					}else{
						dataArr_final_1[count_dataArr_final_1] = item_dataArr_final;
						count_dataArr_final_1++;
						break;
					}
				}
				
			}
			
			dataArr = dataArr_final_1;
		}
					
		var dataArr_final = [];
		if(vMainReturnParam != "" && Array.isArray(dataArr)){
			dataArr_final[vMainReturnParam] = dataArr;
		}else{
			dataArr_final = dataArr;
		}
		
		dataArrObj = Object.assign(isKeyValuePairArr ? {} : [], (vReturnType == "String"  && dataArr.length > 0) ? dataArr[0] : dataArr_final);
		
		return dataArrObj;
	}else{
		return dataArrObj;
	}
}

function fetchValuesFromAnObject(response_remote_data, vSubKeys, vReturnType, vReturnParam){
	var dataArr = [];
	for(let vSubKeys_item of vSubKeys) {
		let vKeyName = vSubKeys_item.vKeyName;
		let vKeyType = vSubKeys_item.vKeyType;
		let vReturnParam_sub = vSubKeys_item.vReturnParam;
		let vReturnType_sub = vSubKeys_item.vReturnType;
		let vSubKeys = vSubKeys_item.vSubKeys;
		
		if(vKeyType == "Array"){
			let data_arr = fetchValuesFromAnArray(response_remote_data,vKeyName, vSubKeys, vReturnType_sub, vReturnParam_sub);
			
			if(vSubKeys_item.vSkipPositions && Array.isArray(data_arr)){
				let vSkipPositions_arr = vSubKeys_item.vSkipPositions.split(",");
				var dataOfSubArray_tmp_final = [];
				for(let idk = 0; idk < data_arr.length; idk++){
					if((!vSkipPositions_arr.includes(""+idk) && (idk + 1) < data_arr.length) || ((idk + 1) == data_arr.length && !vSkipPositions_arr.includes("-1"))){
						dataOfSubArray_tmp_final.push(!isNaN(data_arr[idk]) ? (parseInt(data_arr[idk])  - 1) : data_arr[idk]);
					}
				}
				data_arr = dataOfSubArray_tmp_final;
			}
			
			if(vReturnParam_sub != ""){
				dataArr[vReturnParam_sub] = data_arr;
			}else{
				dataArr = Object.assign(dataArr, data_arr);
			}
		}else if(vKeyType == "Object"){
			if(!response_remote_data[vKeyName]){
				continue;
			}
			var obj_data = fetchValuesFromAnObject(response_remote_data[vKeyName], vSubKeys, vReturnType_sub, vReturnParam_sub);
				
			if(vReturnParam_sub != ""){
				dataArr[vReturnParam_sub] = obj_data;
			}else{
				dataArr = Object.assign(dataArr, obj_data);
			}
		}else if(vKeyType == "String"){
			let vConversionFormula = vSubKeys_item.vConversionFormula;
			let vConversionFormulaValue = vSubKeys_item.vConversionFormulaValue;
			
			if(vConversionFormula && vConversionFormula != "" && vConversionFormulaValue != ""){
				response_remote_data[vKeyName] = roundToTwo(response_remote_data[vKeyName] * vConversionFormulaValue);
			}
			
			let data_str = response_remote_data[vKeyName];
			if(vReturnParam_sub != ""){
				dataArr[vReturnParam_sub] = data_str;
			}else{
				dataArr.push(data_str);
			}
		}
	}
	
	var dataArr_final = [];
	if(vReturnParam != ""){
		dataArr_final[vReturnParam] = dataArr;
	}else{
		dataArr_final = dataArr;
	}
	
	return Object.assign({}, dataArr_final);
}

function isEmptyObject(obj) {
  return !Object.keys(obj).length;
}

function roundToTwo(num) {    
    return +(Math.round(num + "e+2")  + "e-2");
}

function regenerateArrayBasedOnDistance(dataArr){
	let source_latitude = dataArr['source_latitude'];
	let source_longitude = dataArr['source_longitude'];
	
	let waypoints_obj = JSON.parse(dataArr['waypoints']);
	
	var distance_arr = [];
	var position_count = 0;
	
	for(let waypoints_obj_item of waypoints_obj){
		if(waypoints_obj_item != ""){
			let location_tmp = waypoints_obj_item.split(",");
			distance_arr[position_count] = {};
			distance_arr[position_count]['LOCATION'] = waypoints_obj_item;
			distance_arr[position_count]['ARR_POSITION'] = position_count;
			distance_arr[position_count]['DISTANCE'] = distanceBetweenLocation(source_latitude,source_longitude,location_tmp[0], location_tmp[1], "Meters");
			position_count++;
		}
	}
	
	distance_arr.sort(function(a, b) {
		return parseFloat(a.DISTANCE) - parseFloat(b.DISTANCE);
	});
	
	var locations_arr = [];
	for(let distance_arr_item of distance_arr){
		locations_arr.push(distance_arr_item['LOCATION']);
	}
	
	return distance_arr;
}

function distanceBetweenLocation(lat1, lon1, lat2, lon2, unit) {
	var radlat1 = Math.PI * lat1/180;
	var radlat2 = Math.PI * lat2/180;
	var theta = lon1-lon2;
	var radtheta = Math.PI * theta/180;
	var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
	dist = Math.acos(dist);
	dist = dist * 180/Math.PI;
	dist = dist * 60 * 1.1515; // Default = Miles
	if (unit=="K") { 
		dist = dist * 1.609344; // Default = kilometers
	}else if (unit=="N") { 
		dist = dist * 0.8684; // Default = Nautical miles 
	}else if(unit=="Meters"){
		dist = dist * 1609.34; // Default = Meters
	}
	return dist;
}

function generateArrFromStr(data_str,count){
	var data_arr = [];
	for(let i = 0; i < count; i++){
		data_arr.push(data_str);
	}
	return data_arr;
}

function deleteElement(arr, position) {
	return arr.splice(position,1);
}

function insertAt(array, index) {
    var arrayToInsert = Array.prototype.splice.apply(arguments, [2]);
    return insertArrayAt(array, index, arrayToInsert);
}

function insertArrayAt(array, index, arrayToInsert) {
    Array.prototype.splice.apply(array, [index, 0].concat(arrayToInsert));
    return array;
}

function generateASCIIString(textToConvert){
	var ascii_str = "";
	let textToConvert_arr = textToConvert.split('');
	
	for(let textToConvert_arr_item of textToConvert_arr){
		ascii_str = ascii_str + textToConvert_arr_item.charCodeAt();
	}
	return ascii_str;
}

function getAuthAccData(auth_acc_data) {
	var today  = new Date();
	var days = new Date(today.getFullYear(), today.getMonth()+1, 0).getDate();

	var dd = String(today.getDate()).padStart(2, '0');
	var current_date = parseInt(dd);

	var days_arr = [];
  	for (var i = 1; i <= days; i++) {
  		days_arr.push(i);
  	}

  	var n = auth_acc_data.length;
	if(n > days_arr.length) {
		n = days_arr.length;
	}

	var slots = [];
	for (var i = n; i > 0; i--) {
	    slots.push(days_arr.splice(0, Math.ceil(days_arr.length / i)));
	}

	var slot_index = -1;
	for (var i = 0; i < slots.length; i++) {
		if(slots[i].indexOf(current_date) != -1) {
			slot_index = i;
		}
	}

	var new_auth_acc_data = auth_acc_data;
	if(slot_index != -1) {
		new_auth_acc_data = auth_acc_data.splice(slot_index, 1);
	}

	return new_auth_acc_data;
}