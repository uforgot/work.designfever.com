var DF_Login = function(json_data){

    var container_iframe = document.getElementById("id_bg_frame");
    var container_clock = document.getElementById("id_container_clock");
    var container_date = document.querySelector(".sec-date .wrapper-date");

    var _json_data = json_data;
    var _offsetTime = 0;

    var _clock = new DF_Clock(container_clock, _json_data);
    var _date = new DF_Date(container_date, _json_data);
    var _bgControll = new loginBgController(container_iframe, _json_data);
    var _loginController = new LoginFieldController();

    var _today = { YY:0, MM:0, DD:0, DW:0, hh:0, mm:0, ss:0 };

    var _date_now;
    var _ID_clock;

    function _init(){

        if(_json_data.info != undefined && _json_data.info.date != undefined && _json_data.info.date.server_time != undefined){
            _offsetTime = _json_data.info.date.server_time - new Date().getTime();
            console.log("[server time] : ", _json_data.info.date.server_time);
            console.log("[client time] : ", new Date().getTime());
        }

        _startTimer();

        _bgControll.init();
        _clock.init(_today);
        _date.init(_today);
        _loginController.init();

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