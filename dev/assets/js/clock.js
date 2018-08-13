/**
 * -----------------------------------------------------
 * Created by uforgot on 2018. 7. 19.
 * work-df_2018
 * -----------------------------------------------------
 */

var Clock = (function() {
    var agent = navigator.userAgent.toLowerCase(); /*ie*/
    var winW, winH;

    var clockEl;
    var headerEl;
    var bodyEl;
    var footerEl;

    var wrapperEl;
    var hourEl;
    var minuteEl;
    var secondEl;
    var infoEl;

    var dateEl;
    var dayEl;

    var headerHeight = 51;
    var bodyHeight;
    var bodyWidth;

    var dayArray = [
        '일요일',
        '월요일',
        '화요일',
        '수요일',
        '목요일',
        '금요일',
        '토요일'
    ]

    /*=========================================================== [ event ] =====================================================================*/



    var resize = function() {
        winW = $(window).width();
        winH = $(window).height();

        /*ie*/
        if ( (navigator.appName == 'Netscape' && navigator.userAgent.search('Trident') != -1) || (agent.indexOf("msie") != -1) ) {
            clockEl.css('height', '25rem');
        }

        if (winW < 769) {
            footerEl.css('position','relative');
        } else {
            footerEl.css('position','absolute');
        }

        bodyWidth = clockEl.width();
        bodyHeight = clockEl.height() - headerHeight - footerEl.height();

        // console.log('-->' + bodyWidth);
        // 523 : 7 = w : x
        var fontsize = (bodyWidth * 7)/523 + 'rem';

        $('.clock-body .large').css('font-size', fontsize);
        $('.clock-body .small').css('font-size', fontsize);


        var marginTop = (bodyHeight - wrapperEl.height())/2;
        wrapperEl.css('margin-top',marginTop);


    };

    var setTime = function() {

        var today;
        var year;
        var month;
        var date;
        var day;
        var hours;
        var minutes;
        var seconds;

        $.ajax({
            url:'./getTime.php',
            async:false,
            success:function(data)
            {
                var _today = data;
                var year = _today.substring(0,4);
                var month = _today.substring(5,7);
                var date = _today.substring(8,10);
                var day = dayArray[_today.substring(11,12)];
                var hours = _today.substring(13,15);
                var minutes = _today.substring(16,18);
                var seconds = _today.substring(19,21);
                dateEl.html(year +'. '+ month + '. ' + date + ' <span>' + day +'</span>');

                hourEl.html(hours + ":");
                minuteEl.html(minutes + ":");
                secondEl.html(seconds);
                setTimeout(setTime, 500);
            }
        })

        /*
         today = new Date();
         year = today.getFullYear();
         month = today.getMonth() + 1;
         date = today.getDate();
         day = dayArray[today.getDay()];

         dateEl.html(year +'. '+ month + '. ' + date + ' <span>' + day +'</span>');
         //console.log(y +'.'+ month + '.'+ day +'.'+ date);

         var hours = today.getHours();
         var minutes = today.getMinutes();
         var seconds = today.getSeconds();
         minutes = checkTime(minutes);
         seconds = checkTime(seconds);

         hourEl.html(hours + ":");
         minuteEl.html(minutes + ":");
         secondEl.html(seconds);
         setTimeout(setTime, 500);
         */
    };

    var checkTime = function(i) {
        if (i < 10) {i = "0" + i}; // 숫자가 10보다 작을 경우 앞에 0을 붙여줌
        return i;
    };


    var onResize = function(){
        resize();
    };

    var addEvent = function() {
        $(window).on('resize', onResize);
        if ( (navigator.appName == 'Netscape' && navigator.userAgent.search('Trident') != -1) || (agent.indexOf("msie") != -1) ) { /*ie*/
            $(window).on('load', onResize);
        }
    };



    /*=========================================================== [ init ] =====================================================================*/

    var _init = function(){
        _load_init();
    };

    var _load_init = function(){
        clockEl = $('#df-clock');
        headerEl = $('#df-clock-header');
        bodyEl = $('#df-clock-body');
        footerEl = $('#df-clock-footer');

        wrapperEl = $('#df-clock-wrapper');
        hourEl = $('#df-clock-hour');
        minuteEl = $('#df-clock-minute');
        secondEl = $('#df-clock-second');

        infoEl = $('#df-clock-info');
        dateEl = $('#df-clock-date');
        dayEl = $('#df-clock-day');

        setTime();

        addEvent();
        onResize();
    };
    return{
        init:_init,
        load_init:_load_init
    }

})();

/*=========================================================== [ ready / load ] =======================================================================*/

// test markup 용

Clock.init();
