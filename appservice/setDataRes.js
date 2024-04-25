module.exports = function (data, req, res) { 
	if (typeof data === 'string' || data instanceof String){
		res.send(data);
	}else{
		res.jsonp(data);
	}
};


