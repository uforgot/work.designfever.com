var DF_Login = function(json_data){

    var container_iframe = document.getElementById("id_bg_frame");
    var container_clock = document.getElementById("id_container_clock");


    var _clock = new DF_Clock(container_clock, json_data);
    var _bgControll = new loginBgController(container_iframe, json_data);

    var _today = {
        YY:0,
        MM:0,
        DD:0,
        DW:0,
        hh:0,
        mm:0,
        ss:0
    };

    var _date_now;
    var _ID_clock;

    var _el ={
        txt_MM:"",
        txt_DD:"",
        txt_DW:""
    };

    function _init(){

        _el.txt_MM = document.getElementById("id_txt_MM");
        _el.txt_DD = document.getElementById("id_txt_DD");
        _el.txt_DW = document.getElementById("id_txt_DW");

        _startTimer();

        _bgControll.init();
        _clock.init(_today);

        LoginFieldController.init();
    }

    function _startTimer(){

        _date_now = new Date();
        _today.YY = _date_now.getFullYear();
        _today.MM = _date_now.getMonth();
        _today.DD = _date_now.getDate();
        _today.DW = _date_now.getDay();
        _today.hh = _date_now.getHours();
        _today.mm = _date_now.getMinutes();
        _today.ss = _date_now.getSeconds();

        _ID_clock = setTimeout(_updateTimeer, 500);
    }

    function _updateTimeer(){

        _startTimer();

        // console.log(_today.YY, " / ", _today.MM, " / ", _today.DD, " / ", _today.DW);
        // console.log(_today.hh, " : ", _today.mm, " : ", _today.ss);

        var MM = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
        var DW = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        _el.txt_MM.textContent = MM[_today.MM];
        _el.txt_DD.textContent = _today.DD < 10 ? '0'+_today.DD : _today.DD;
        _el.txt_DW.textContent = DW[_today.DW];

        _clock.updateToday(_today);
    }

    return {
        init: _init
    }
};