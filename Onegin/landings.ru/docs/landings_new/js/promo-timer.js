$(function(){
	promo.init();
});

var promo = {};

promo.timer;
promo.init = function(){
	promo.$ = $('#promo');
	promo.$days = $('.days, .d', promo.$);
	promo.$hours = $('.hours, .h', promo.$);
	promo.$minutes = $('.minutes, .i', promo.$);
	promo.$seconds = $('.seconds, .s', promo.$);

	promo.updateCounters();
	promo.timer = setInterval(promo.updateCounters, 1000);
};

promo.secLeftToMonday = function(){
	var currentDate = new Date();
	var currentDay = currentDate.getDay();

	var intervalToMo = (7 - (currentDay - 1));
	if(intervalToMo == 8) intervalToMo = 1;

	var d = currentDate.getDate() + intervalToMo;

	return promo.secLeftToTime(undefined, undefined, d, 0, 0, 0, 0);
};

promo.secLeftToTime = function(y, m, d, h, i, s, ms){
	var currentDate = new Date();
	var currentDateInMs = currentDate.getTime();
	var currentDay = currentDate.getDay();

	var intervalToMo = (7 - (currentDay - 1));
	if(intervalToMo == 8) intervalToMo = 1;

	var date = new Date();
	if(y !== undefined) date.setFullYear(y);
	if(m !== undefined) date.setMonth(m - 1);
	if(d !== undefined) date.setDate(d);
	date.setHours(h);
	date.setMinutes(i);
	date.setSeconds(s);
	date.setMilliseconds(ms);

	var dateMs = date.getTime();
	var msLeftToDate = dateMs - currentDateInMs;

	return (msLeftToDate / 1000);
};

promo.updateCounters = function(){
	var secAllLeft;

	var toTime = promo.$.data('time');
	if(toTime){
		if(toTime == '0000-00-00 00:00:00'){
			secAllLeft = 0;
		}else{
			toTime = toTime.split(' ');
			toDate = toTime[0].split('-');
			toTime = toTime[1].split(':');

			secAllLeft = promo.secLeftToTime(toDate[0], toDate[1], toDate[2], toTime[0], toTime[1], toTime[2], 0);
		}
	}else{
		secAllLeft = promo.secLeftToMonday();
	}

	if(secAllLeft < 0) secAllLeft = 0;

	var daysLeft = Math.floor(secAllLeft / 60 / 60 / 24);
	var hoursLeft = Math.floor((secAllLeft / 60 / 60) % 24);
	var minLeft = Math.floor((secAllLeft / 60) % 60);
	var secLeft = Math.floor(secAllLeft % 60);

	promo.$days.html(promo.numberFormat(daysLeft));
	promo.$hours.html(promo.numberFormat(hoursLeft));
	promo.$minutes.html(promo.numberFormat(minLeft));
	promo.$seconds.html(promo.numberFormat(secLeft));

	if(!secAllLeft){
		promo.$.addClass('stopped');
		clearInterval(promo.timer);
	}
};

promo.numberFormat = function(number){
	if(number < 10) number = ('0' + number);
	return number;
};

promo.onFinish = function(){

};