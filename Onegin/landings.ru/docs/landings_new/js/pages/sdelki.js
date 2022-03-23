/* global landing */

// произвести редирект на другой хеш
landing.hashRedirects = function(){
	var redirects = {
		'#1':'#achievements',
		'#2':'#advantages',
		'#3':'#bonuses',
		'#4':'#reviews',
		'#5':'#guarantees',
		'#6':'#docs',
		'#7':'#problem-solution',
		'#8':'',
		'#9':'#workflow',
		'#10':'#geography',
		'#11':'#contacts'
	};

	if(redirects[location.hash]) location.hash = redirects[location.hash];
};

