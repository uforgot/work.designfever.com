window.df = window.df || {};
window.df.workgroup = window.df.workgroup || {};

window.df.workgroup.login = function(json_data){

    var CLASS_NAME = "[ login ]";

    var container_iframe = document.getElementById("id_bg_frame");
    var container_clock = document.getElementById("id_container_clock");
    var container_date = document.querySelector(".sec-date .wrapper-date");

    var _json_data = json_data;
    var _offsetTime = 0;

    var _clock = new LoginClock(container_clock, _json_data);
    var _date = new LoginDate(container_date, _json_data);

    var _bgControll = new LoginBgController(container_iframe, _json_data);

    var _loginController = new LoginFieldController();
    var _logoutController = new LogoutController();
    var _checkinController = new CheckinController();

    var _today = { YY:0, MM:0, DD:0, DW:0, hh:0, mm:0, ss:0 };

    var _date_now;
    var _ID_clock;

    function _init(){

        if(_json_data.info != undefined && _json_data.info.date != undefined && _json_data.info.date.server_time != undefined){

            _offsetTime = _json_data.info.date.server_time - new Date().getTime();

            console.log(CLASS_NAME + " [server time] : ", _json_data.info.date.server_time, " [client time] : ", new Date().getTime(), " [_offsetTime] : ", _offsetTime);
        }

        _startTimer();

        _bgControll.init();
        _clock.init(_today);
        _date.init(_today);

        _loginController.init();
        _logoutController.init();
        _checkinController.init();

        startMotion();

        addEvent();
    }

    function addEvent(){
        document.addEventListener(window.df.workgroup.Preset.eventType.ON_LOGIN, _onLogin);
        document.addEventListener(window.df.workgroup.Preset.eventType.ON_CHECKIN, _onCheckin);
    }

    function _onLogin(){

        _loginController.hideLoginFrom();

        var sec_login = document.querySelector('.sec-login');
        df.lab.Util.addClass(sec_login, 'logged');

        _logoutController.showLogoutBtn();
        _checkinController.showCheckinBtn();

        var sec_util = document.querySelector('.sec-util');
        df.lab.Util.addClass(sec_util, window.df.workgroup.Preset.class_name.showIn);
    }

    function _onCheckin(){
        _checkinController.showCheckoutBtn();
    }

    function startMotion(){

        var con_header = document.querySelector('header');
        setTimeout(function(){df.lab.Util.addClass(con_header, window.df.workgroup.Preset.class_name.showIn);}, 100);

        var con_info = document.querySelector('.sec-login');
        setTimeout(function(){df.lab.Util.addClass(con_info, window.df.workgroup.Preset.class_name.showIn);}, 500);

        var con_footer = document.querySelector('footer');
        setTimeout(function(){df.lab.Util.addClass(con_footer, window.df.workgroup.Preset.class_name.showIn);}, 3000);
    }

    function _startTimer(){
        _updateTimer();
    }

    function _updateTimer(){
        _setTimer();
        _clock.updateToday(_today);
        _date.updateToday(_today);
    }

    function _setTimer(){

        _date_now = new Date();
        _date_now.setTime(_date_now.getTime() + _offsetTime);

        _today.YY = _date_now.getFullYear();
        _today.MM = _date_now.getMonth();
        _today.DD = _date_now.getDate();
        _today.DW = _date_now.getDay();
        _today.hh = _date_now.getHours();
        _today.mm = _date_now.getMinutes();
        _today.ss = _date_now.getSeconds();

        _ID_clock = setTimeout(_updateTimer, 500);
    }

    return {
        init: _init
    }
};