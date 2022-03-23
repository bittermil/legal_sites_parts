/* global landing */

// произвести редирект на другой хеш
landing.hashRedirects = function(){
	var redirects = {
		'#1':'#structure',
		'#2':'#what_you_get',
		'#3':'#our_guarantees',
		'#4':'#reviews',
		'#5':'#workflow',
		'#6':'#feedback_2',
		'#7':'#contacts',
	};

	if(redirects[location.hash]) location.hash = redirects[location.hash];
};