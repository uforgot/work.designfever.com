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

    var _today = { YY:0, MM:0, DD:0, DW:0, hh:0, mm:0, ss:0 };

    var _date_now;
    var _ID_clock;

    function _init(){

        if(_json_data.info != undefined && _json_data.info.date != undefined && _json_data.info.date.server_time != undefined){
            _offsetTime = _json_data.info.date.server_time - new Date().getTime();
            console.log(CLASS_NAME + " [server time] : ", _json_data.info.date.server_time);
            console.log(CLASS_NAME + " [client time] : ", new Date().getTime());
        }

        _startTimer();

        _bgControll.init();
        _clock.init(_today);
        _date.init(_today);

        _loginController.init();
        _logoutController.init();

        startMotion();
    }

    function startMotion(){

        var con_header = document.querySelector('header');
        setTimeout(function(){df.lab.Util.addClass(con_header, "show");}, 100);

        var con_info = document.querySelector('.sec-login');
        setTimeout(function(){df.lab.Util.addClass(con_info, "show");}, 500);

        var con_footer = document.querySelector('footer');
        setTimeout(function(){df.lab.Util.addClass(con_footer, "show");}, 3000);
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