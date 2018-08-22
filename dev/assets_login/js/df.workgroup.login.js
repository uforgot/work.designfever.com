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
    var _loginInfoController = new LoginInfoController();

    var _loginUtilController = new LoginUtilController();
    var _modalController = new ModalController();

    var _today = { YY:0, MM:0, DD:0, DW:0, hh:0, mm:0, ss:0 };

    var _date_now;
    var _ID_clock;

    var _title_origin;

    function _init(){

        _title_origin = document.title;

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
        _loginInfoController.init(_json_data.info.today.notice, _json_data.info.birthday);

        _loginUtilController.init(_json_data.preset.document_url, _json_data.preset.main_url, _json_data.user);

        _modalController.init();

        startMotion();

        addEvent();
    }

    function addEvent(){
        document.addEventListener(window.df.workgroup.Preset.eventType.ON_LOGIN, _onLogin);
        document.addEventListener(window.df.workgroup.Preset.eventType.ON_CHECKIN, _onCheckin);
        document.addEventListener(window.df.workgroup.Preset.eventType.ON_CHECKOUT, _onCheckout);
        document.addEventListener(window.df.workgroup.Preset.eventType.ON_CHANGE_STAGE_INFO, _onChange_stage_info);
        document.addEventListener(window.df.workgroup.Preset.eventType.ON_LOGOUT, _onLogout);
        document.addEventListener(window.df.workgroup.Preset.eventType.ON_WARNING, _onWarning);
    }

    function _resetData(response){

        var actual_JSON = JSON.parse(response.target.responseText);
        console.log(CLASS_NAME + " << _resetData>> ", actual_JSON);
        window.df.workgroup.GlobalVars.infoData = actual_JSON;
        _json_data = window.df.workgroup.GlobalVars.infoData;
    }

    function _onLogin(evt){
        _resetData(evt.detail.response);
        _updateStatus();
    }

    function _onCheckin(evt) {
        _resetData(evt.detail.response);
        _updateStatus();
    }

    function _onCheckout(evt) {
        _resetData(evt.detail.response);
        _updateStatus();
    }

    function _onLogout(evt){
        _resetData(evt.detail.response);
        _updateStatus();
    }

    function _onWarning(evt){
        //console.log(evt.detail.message);
        _modalController.showModal(evt.detail.message);
    }

    function _onChange_stage_info(event){
        //console.log("_onChange_stage_info : stage_index - ", event.detail.curIndex);
    }

    // set layout
    function _setLayout_Logout(){

        var sec_util = document.querySelector('.sec-util');
        df.lab.Util.removeClass(sec_util, window.df.workgroup.Preset.class_name.showIn);

        var sec_login = document.querySelector('.sec-login');
        df.lab.Util.removeClass(sec_login, 'logged');

        _logoutController.hideLogoutBtn();
        _checkinController.hideCheckinBtn();
        _loginController.showLoginFrom();
    }

    function _setLayout_Login(){
        _loginController.hideLoginFrom();

        _logoutController.showLogoutBtn();
        _checkinController.showCheckinBtn();

        var sec_util = document.querySelector('.sec-util');
        df.lab.Util.addClass(sec_util, window.df.workgroup.Preset.class_name.showIn);

        _loginInfoController.showNotice();
    }

    function _setLayout_Checkin(){
        _checkinController.showCheckoutBtn();
    }

    function _setLayout_Checkout(){
        _checkinController.showCheckoutText();
    }

    function startMotion(){

        var con_header = document.querySelector('header');
        setTimeout(function(){df.lab.Util.addClass(con_header, window.df.workgroup.Preset.class_name.showIn);}, 10);

        var con_info = document.querySelector('.sec-info');
        setTimeout(function(){
            df.lab.Util.addClass(con_info, window.df.workgroup.Preset.class_name.showIn);
            _updateStatus();
        }, 0);

        var con_login = document.querySelector('.sec-login');
        setTimeout(function(){
            df.lab.Util.addClass(con_login, window.df.workgroup.Preset.class_name.showIn);
            _updateStatus();
        }, 10);

        var con_footer = document.querySelector('footer');
        setTimeout(function(){df.lab.Util.addClass(con_footer, window.df.workgroup.Preset.class_name.showIn);}, 1500);
    }

    function _updateStatus(){

        console.log(CLASS_NAME , " user : isLoggedIn - ", _json_data.user.isLoggedIn, " / isCheckin - ", _json_data.user.isCheckin , " / isCheckout", _json_data.user.isCheckout);

        _resetBrowserTitle();

        _loginInfoController.resetData(_json_data.info.today.notice, _json_data.info.birthday);
        _loginUtilController.resetData(_json_data.user);

        var sec_login = document.querySelector('.sec-login');

        if(_json_data.user.isLoggedIn){

            df.lab.Util.addClass(sec_login, 'logged');

            _setLayout_Login();

            if(_json_data.user.isCheckin){
                _setLayout_Checkin();

                if(_json_data.user.isCheckout){
                    _setLayout_Checkout();
                }
            }
        }else{
            df.lab.Util.removeClass(sec_login, 'logged');
            _setLayout_Logout();
        }

    }

    function _resetBrowserTitle(){

        if(_json_data.user.isLoggedIn){
            //document.title = _json_data.user.name + " " + _json_data.user.position+"ดิ.";
            document.title = _title_origin;
        }else{
            document.title = _title_origin;
        }
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

        window.df.workgroup.GlobalVars.time_now = _date_now.getTime();

        _ID_clock = setTimeout(_updateTimer, 500);
    }

    return {
        init: _init,
        setLayout_Logout: _setLayout_Logout
    }
};