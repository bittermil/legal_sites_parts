/* global landing */

// произвести редирект на другой хеш
landing.hashRedirects = function(){
	var redirects = {
		'#to_services':'#services',
		'#to_price':'#prices',
		'#to_reviews':'#reviews',
		'#to_qwestions':'#feedback_2',
		'#to_contacts':'#contacts'
	};

	if(redirects[location.hash]) location.hash = redirects[location.hash];
};