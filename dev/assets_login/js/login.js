var DF_Login = function(json_data){

    var container_iframe = document.getElementById("id_bg_frame");
    var container_clock = document.getElementById("id_container_clock");
    var container_date = document.querySelector(".sec-date .wrapper-date");

    var _clock = new DF_Clock(container_clock, json_data);
    var _date = new DF_Date(container_date, json_data);
    var _bgControll = new loginBgController(container_iframe, json_data);
    var _loginController = new LoginFieldController();

    var _today = { YY:0, MM:0, DD:0, DW:0, hh:0, mm:0, ss:0 };

    var _date_now;
    var _ID_clock;

    function _init(){
        _startTimer();

        _bgControll.init();
        _clock.init(_today);
        _date.init(_today);
        _loginController.init();

        startMotion();

        setTimeout(function(){
            setFocus();
        }, 1000);
    }

    function setFocus(){
        var input_user_id = document.getElementById('user_id');
        input_user_id.focus();

        console.log("focus: ", input_user_id);
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
        _clock.updateToday(_today);
        _date.updateToday(_today);
    }

    return {
        init: _init
    }
};

/*

function getList_tmp(){
var arr_list = document.querySelectorAll('table.work3 tbody tr td a'); var str_tmp=""; arr_list.forEach(function(element, index) {
    if(index%3 == 0) str_tmp = str_tmp + '\n{"group": "04", "seq":"0005", "cat": "001", "url": "' + element.href + '", "color_mode": 0},';
});console.log(str_tmp);}

getList_tmp();

*/


