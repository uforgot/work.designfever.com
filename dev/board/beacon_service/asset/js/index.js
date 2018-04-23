/*  ____________________________________________________________________________________________________________________

         .
     _|_|_    interactive lab
    (_| |     uforgot : 2015-07-10

 ____________________________________________________________________________________________________________________
 */
//log(';sssss');
var DFWORKOUT = {};

(function(ns, $) {

    var INDEX = (function () {

        var _isBeacon = true;;
        var _weekday = [
            "Sunday",
            "Monday",
            "Tuesday",
            "Wednesday",
            "Thursday",
            "Friday",
            "Saturday"
        ];

        var _init = function () {
                _setInterval();
                //_addEvent();
                //_setBeacon(1,0,0);
            },

            _setInterval = function() {
                _setTime();
                setTimeout(_setInterval, 1000);
            },

		/*
		   _addEvent = function() {
                $('#in-bt').bind('click', _setOut);
                $('#out-bt').bind('click', _setIn);				
            },
		*/

            _setTime = function() {
                var date = new Date();

                var dateString = date.getFullYear() + "." + (date.getMonth() +1) + "." + date.getDate();
                $('.main-wrap .top-wrap .date').html(dateString);

                var dayString = _weekday[date.getDay()];
                $('.main-wrap .top-wrap .day').html(dayString.toUpperCase());

                var hourString = _getDecimalToString(date.getHours());
                $('.main-wrap .top-wrap .time .hour').html(hourString);

                var minuteString = _getDecimalToString(date.getMinutes());
                $('.main-wrap .top-wrap .time .minute').html(minuteString);

                var secondString = _getDecimalToString(date.getSeconds());
                $('.main-wrap .top-wrap .time .second').html(secondString);
            },

            _getDecimalToString = function($number) {
                var returnStr = "";

                if ($number < 10) {
                    returnStr = "0";
                }

                returnStr += $number;
                return returnStr;
            },			
			
			// 출근처리
            _setOut = function() {
                if (_isBeacon === false) return;
				parent.goChkHouse();
				_getIn();
            },													
			
			// 출근화면
            _getIn = function() {
                if (_isBeacon === false) return;						
                $('.main-wrap .main .in-bt').css('display', 'block');
                $('.main-wrap .main .out-bt').css('display', 'none');
                $('.main-wrap').css('background-color', '#141414')								
            },
			
			// 출근버튼활성
			_onBtn_in = function(){
				$('#in-bt').bind('click', _setOut);
			},						
			
			// 출근버튼 비활성
			_offBtn_in = function(){			
				$('#in-bt').attr('onclick', '').unbind('click');
				alert('비콘이 감지되지않았습니다 체크 불가.');
			},						
						
            _setBeacon = function($beacon1, $beacon2, $beacon3) {

                var isBeacon = false;

                if ($beacon1 > 0) {
                    isBeacon = true;
                    $('#beacon-1 .activated').css('display', 'block');
                } else {
                    $('#beacon-1 .activated').css('display', 'none');
                }

                if ($beacon2 > 0) {
                    isBeacon = true;
                    $('#beacon-2 .activated').css('display', 'block');
                } else {
                    $('#beacon-2 .activated').css('display', 'none');
                }

                if ($beacon3 > 0) {
                    isBeacon = true;
                    $('#beacon-3 .activated').css('display', 'block');
                } else {
                    $('#beacon-3 .activated').css('display', 'none');
                }

                if (isBeacon === false) {
                    $('.main-wrap .main').css('opacity', 0.4);
                } else {
                    $('.main-wrap .main').css('opacity', 1);
                }

                _isBeacon = isBeacon;
            },

            _dummy = function () {

            }


        return {
            init: _init,

            setOut : _setOut,            
			
			getIn : _getIn,

			onBtn_in : _onBtn_in,
			offBtn_in : _offBtn_in,
								
            setBeacon : _setBeacon,

            dummy: _dummy
        };

    })();

    ns.INDEX = INDEX;
} (DFWORKOUT || {}, $));

$(document).ready(function() {
    DFWORKOUT.INDEX.init();        
	//DFWORKOUT.INDEX.setOut();
    DFWORKOUT.INDEX.setBeacon(0, 2, 1);	

});

