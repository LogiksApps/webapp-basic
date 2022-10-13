$(function() {
   Handlebars.registerHelper('ifneq', function(v1, v2, options) {
    	if (v1 !== v2) {
    		return options.fn(this);
    	}
    	return options.inverse(this);
    });
    Handlebars.registerHelper('ifeq', function(v1, v2, options) {
    	str1 = v1.toLowerCase();
    	str2 = v2.toLowerCase();
    	str1 = str1.replace('\-\g', '');
    	str1 = str1.replace('\ \g', '');
    	str2 = str2.replace('\-\g', '');
    	str2 = str2.replace('\ \g', '');
    	if (str1 === str2) {
    		return options.fn(this);
    	}
    	return options.inverse(this);
    });
    Handlebars.registerHelper('strconverter', function(v1, options) {
    	term = v1.replace(/ /g, '');
    	term = term.replace(/-/g, '');
    
    	return (term.toLowerCase());
    });
    
    Handlebars.registerHelper('ifgt', function(v1, min, options) {
    	if (v1 > min) {
    		return options.fn(this);
    	} else {
    		return options.inverse(this);
    	}
    });
    
    Handlebars.registerHelper('hash', function(txt) {
    	if (txt == null) txt = "";
    	return md5(txt)
    });
  
    Handlebars.registerHelper('avatar', function(txt) {
    	return _service("avatar")+"&authorid="+txt;
    });
    Handlebars.registerHelper('tags', function(tagsStr) {
      if(tagsStr==null || tagsStr.length<=0) return "";
      clz="label-info";
      
    	tagsStr=tagsStr.split(",");
      html=[];
      $.each(tagsStr, function(a,b) {
        html.push("<span class='label "+clz+" "+b.replace(/ /g,"_").replace(/'/g,"")+"'>"+b+"</span>");
      });
      
      return html.join("");
    });
    Handlebars.registerHelper('userprofile', function(useremail) {
      if(useremail==null || useremail.length<=0) return "";
	    userDetails=useremail.split("@");
      if(userDetails.length>1 && userDetails[0].length>0) {
        return "<a class='userProfile' href='#' data-email='"+useremail+"'><small>"+toTitle(userDetails[0])+"</small></a>"
      } else if(useremail.substr(0,1)=="@") {
          return "<a class='userProfile' href='#' data-email='"+useremail+"'><small>"+toTitle(useremail)+"</small></a>"
      } else if(useremail.substr(0,1)=="#") {
          return "<strong><small>"+useremail.replace("@","").toUpperCase()+" Group</small></strong>";
      } else {
        return "<strong><small>"+useremail.replace("@","")+"</small></strong>";
      }
    });
    Handlebars.registerHelper('totitle', function(str) {
      if(str==null || str.length<=0) return "";
    	return str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
            return letter.toUpperCase();
          });
    });
    
    Handlebars.registerHelper('currency', function(amount) {
    	if (amount == null || amount.length <= 0 || amount == 0) return "";
    	if(exchangeRate==null) exchangeRate=1;
    	amount = parseFloat(amount);
    	switch (exchangeCurrency) {
    		case "INR":
    			return "₹ " + (amount * exchangeRate).toFixed(2);
    			break;
    		case "GBP":
    			return "£ " + (amount * exchangeRate).toFixed(2);
    			break;
    		case "EUR":
    			return "€ " + (amount * exchangeRate).toFixed(2);
    			break;
    		case "USD":
    			return "$ " + amount.toFixed(2);
    			break;
    	}
    	return "$ " + amount.toFixed(2);
    });
    
    Handlebars.registerHelper('formatTime', function(dateStr) {
    	if(dateStr==null || dateStr.length<=0) return "";
    	return moment(dateStr).format("HH:mm");
    });
    Handlebars.registerHelper('formatDate', function(dateStr) {
    	if(dateStr==null || dateStr.length<=0) return "";
    	return moment(dateStr).format("DD/MM/YYYY");
    });
    Handlebars.registerHelper('humanDate', function(dateStr) {
        if(dateStr==null || dateStr.length<=0) return "";
    	return moment(dateStr).fromNow();
    });
    Handlebars.registerHelper('humanTime', function(time) {
    	hrs = Math.floor(time / 60);
    	min = time % 60;
    	if (hrs <= 0) {
    		return min + " min";
    	}
    	return hrs + " Hrs " + min + " min";
    });
    Handlebars.registerHelper('humanDateTime', function(dateStr) {
      return moment(dateStr).format("DD/MM/YYYY hh:mm A");
    });
    Handlebars.registerHelper('humanDateTimeStr', function(dt, type) {
    	if (dt == null) return "";
    	switch (type) {
    		case "date":
    			dt = dt.split("T");
    			dt = dt[0];
          return moment(dt).format("DD/MM/YYYY");
    			break;
    		case "time":
    			dt = dt.split("T");
    			dt = dt[1];
    			dt = dt.split("+");
    			dt = dt[0];
    			dt = dt.split("-");
    			dt = dt[0];
    
    			dt = new Date("01-01-2017 " + dt);
    			dt = dt.getHours() + ":" + dt.getMinutes();
    			break;
    		case "zone":
    			dt = dt.split("+");
    			if (dt[1] == null) {
    				dt = dt[0].split("-");
    				dt = "-" + dt[dt.length - 1];
    			} else {
    				dt = "+" + dt[1];
    			}
    			break;
    		default:
    			dt = dt.split("+").join("<br>+");
    	}
    	return dt;
    });
    Handlebars.registerHelper('humanDuration', function(dt1, dt2) {
    	dt1 = new Date(dt1);
    	dt2 = new Date(dt2);
    	diff = ((dt1 - dt2) / 1000) / 60;
    	if (diff > 60) {
    		hrs = Math.floor(diff / 60);
    		min = diff % 60;
    		return hrs + " Hrs " + min + " Mins";
    	} else {
    		return diff + " Mins";
    	}
    });
    Handlebars.registerHelper('distance', function(data) {
    	if (data == null || data == "") return "";
    	if (tjq("#distanceDrpDwn").val().toUpperCase() == "MILES") {
    		return k2m(data).toFixed(2) + " miles";
    	} else {
    		return data + " km";
    	}
    });
    
    Handlebars.registerHelper('alink', function(msg, link) {
        if(link==null || link=="#" || link.length<=1) 
            return msg;
        else
    	    return "<a href='"+link+"' target=_blank>"+msg+"</a>";
    });
    Handlebars.registerHelper('checkstatusbox', function(dataStatus, dataValue, disableOnTrue) {
        if(dataStatus==dataValue)
            if(disableOnTrue)
                return "<input type='checkbox' name='status' class='form-control checkstatusbox' checked=true disabled />";
            else
                return "<input type='checkbox' name='status' class='form-control checkstatusbox' checked=true />";
        else
            return "<input type='checkbox' name='status' class='form-control checkstatusbox' />";
    });
  
    Handlebars.registerHelper('flags', function(pValue) {
      switch(pValue.toLowerCase()) {
        case "high":
          return "red";
        case "medium":
          return "blue";
        case "low":
          return "yellow";
        default:
          return "orange";
      }
      return "black";
    });
    
    Handlebars.registerHelper('flagCombo', function(flagValue, flagArr) {
        flagArr=["","red","green","blue","yellow"];
        
        html="<select name='flag' class='form-control selector'>";
        $.each(flagArr, function(k,v) {
            if(flagValue==v)
                html+="<option value='"+v+"' selected >"+v+"</option>";
            else
                html+="<option value='"+v+"' >"+v+"</option>";
        });
        html+="</select>";
        
        return html;
    });
});