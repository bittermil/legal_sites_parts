/* global landing */

// произвести редирект на другой хеш
landing.hashRedirects = function(){
	var redirects = {
		'#what_you_get':'#advantages',
		'#why_we':'#why-us',
		'#fear_solution':'#problem-solution',
		'#review':'#reviews',
		'#question':'#feedback_2',
		'#contact':'#contacts'
	};

	if(redirects[location.hash]) location.hash = redirects[location.hash];
};