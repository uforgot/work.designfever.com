/**
 * -----------------------------------------------------
 * Created by uforgot on 2018. 7. 19.
 * work-df_2018
 * -----------------------------------------------------
 */

var Clock = (function() {

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
    var dayEl;

    var headerHeight = 51;
    var bodyHeight;
    var bodyWidth;

    /*=========================================================== [ event ] =====================================================================*/
    var resize = function() {
        winW = $(window).width();
        winH = $(window).height();


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

    var onResize = function(){
        resize();
    };

    var addEvent = function() {
        $(window).on('resize', onResize);
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
        dayEl = $('#df-clock-day');

        addEvent();
        onResize();
    };
    return{
        init:_init,
        load_init:_load_init
    }

})();

/*=========================================================== [ ready / load ] =======================================================================*/

// test markup ¿ë

Clock.init();
