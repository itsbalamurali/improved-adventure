module.exports = function (req, res) { 

	return req.query.type;
    
	/* if(req.query.type == "getDetail"){
		return "getDetail";
	}else if(req.query.type == "loadStaticInfo"){
		return "loadStaticInfo";
	}else if(req.originalUrl.startsWith("/UpdateMemberData") || req.originalUrl.endsWith("UpdateMemberData")){
		return "UpdateMemberData";
	}else if(req.originalUrl.startsWith("/UpdateStaticData") || req.originalUrl.endsWith("UpdateStaticData")){
		return "UpdateStaticData";
	}
	
	var response_data = {};
	response_data['Action'] = "0";
	response_data['message'] = "Invalid request.";
	res.jsonp(response_data);
	
	return ""; */
};


