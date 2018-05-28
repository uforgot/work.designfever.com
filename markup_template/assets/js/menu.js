/**
 * -----------------------------------------------------
 * Created by uforgot on 2018. 3. 19.
 * designsamsung_2018
 * -----------------------------------------------------
 */

var Menu = (function() {

    var winW, winH;
    var menuOpenBtEl;
    var menuCloseBtEl;
    var menuEl;


    /*=========================================================== [ event ] =====================================================================*/
    var resize = function() {
        winW = $(window).width() <= 1280 ? 1280 : $(window).width();
        winH = $(window).height();

        if(winW > 1087) {
            closeMenu();
        }
    };

    var onResize = function(){
        resize();
    };

    var openMenu = function() {
        menuEl.addClass('is-active');
    };

    var closeMenu = function() {
        menuEl.removeClass('is-active');
    };

    var addEvent = function() {
        $(window).on('resize', onResize);
        menuOpenBtEl.on('click', openMenu);
        menuCloseBtEl.on('click', closeMenu);
    };

    /*=========================================================== [ init ] =====================================================================*/

    var _init = function(){
    };

    var _load_init = function(){
        menuOpenBtEl = $('#work-menuOpenBt');
        menuCloseBtEl = $('#work-menuCloseBt');
        menuEl = $('#work-menu');

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

if (_isMarkup === true) {
    $(window).on('ready', function () {
        Menu.init();

        $(window).on('load', function () {
            $('#top').load('/include/top.html .top', function () {
                Menu.load_init();
            });

            $('#sub-menu-1').load('/include/top.html .sub-menu-1');
            $('#sub-menu-2').load('/include/top.html .sub-menu-2');
            $('#sub-menu-3').load('/include/top.html .sub-menu-3');
            $('#sub-menu-4').load('/include/top.html .sub-menu-4');
            $('#sub-menu-5').load('/include/top.html .sub-menu-5');
            $('#sub-menu-6').load('/include/top.html .sub-menu-6');
            $('#sub-menu-7').load('/include/top.html .sub-menu-7');
            $('#sub-menu-8').load('/include/top.html .sub-menu-8');
            $('#sub-menu-9').load('/include/top.html .sub-menu-9');
            $('#sub-menu-10').load('/include/top.html .sub-menu-10');
        })
    });
} else {
    $(window).on('ready',function() {
        Menu.init();
    });

    $(window).on('load',function() {
        Menu.load_init();
    });
}

