/* global landing */

// произвести редирект на другой хеш
landing.hashRedirects = function(){
	var redirects = {
		'#1':'#services',
		'#2':'#feedback_2',
		'#3':'#structure',
		'#4':'#reviews',
		'#5':'#contacts',
	};

	if(redirects[location.hash]) location.hash = redirects[location.hash];
};