$(function(){
	promo.init();
});

var promo = {};

promo.init = function(){
	promo.$ = $('#promo');
	promo.$days = $('.days', promo.$);
	promo.$hours = $('.hours', promo.$);
	promo.$minutes = $('.minutes', promo.$);
	promo.$seconds = $('.seconds', promo.$);

	promo.updateCounters();

	var timer = setInterval(promo.updateCounters, 1000);
};

promo.secLeftToMonday = function(){
	var currentDate = new Date();
	var currentDateInMs = currentDate.getTime();
	var currentDay = currentDate.getDay();

	var intervalToMo = (7 - (currentDay - 1));
	if(intervalToMo == 8) intervalToMo = 1;

	var Monday = new Date();
	Monday.setDate(Monday.getDate() + intervalToMo);
	Monday.setHours(0);
	Monday.setMinutes(0);
	Monday.setSeconds(0);
	Monday.setMilliseconds(0);

	var mondayInMs = Monday.getTime();
	var msLeftToMonday = mondayInMs - currentDateInMs;

	return (msLeftToMonday / 1000);
};

promo.updateCounters = function(){
	var secLeftToMonday = promo.secLeftToMonday();

	var daysLeft = Math.floor(secLeftToMonday / 60 / 60 / 24);
	var hoursLeft = Math.floor((secLeftToMonday / 60 / 60) % 24);
	var minLeft = Math.floor((secLeftToMonday / 60) % 60);
	var secLeft = Math.floor(secLeftToMonday % 60);

	promo.$days.html(promo.numberFormat(daysLeft));
	promo.$hours.html(promo.numberFormat(hoursLeft));
	promo.$minutes.html(promo.numberFormat(minLeft));
	promo.$seconds.html(promo.numberFormat(secLeft));
};

promo.numberFormat = function(number){
	if(number < 10) number = ('0' + number);
	return number;
};

promo.onFinish = function(){

};