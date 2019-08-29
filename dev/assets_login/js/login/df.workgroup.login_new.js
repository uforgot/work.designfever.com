import LoginClock from './loginClock';
import LoginDate from './loginDate';
import LoginBgController from './loginBgController';
import LoginFieldController from './loginFieldController';
import LogoutController from './logoutController';
import CheckinController from './checkinController';
import StartStopController from './startStopController';
import LoginInfoController from './loginInfoController';
import LoginUtilController from './loginUtilController';
import ModalController from './modalController';

window.df = window.df || {};
window.df.workgroup = window.df.workgroup || {};

window.df.workgroup.login = function (json_data) {

    var CLASS_NAME = "[ login ]";

    var container_iframe = document.getElementById("id_bg_frame");
    var container_clock = document.getElementById("id_container_clock");
    var container_date = document.querySelector(".sec-date .wrapper-date");

    var _json_data = json_data;
    var _offsetTime = 0;

    var _clock = new LoginClock(container_clock, _json_data);
    var _dateInfo = new LoginDate(container_date, _json_data);

    var _bgControll = new LoginBgController(container_iframe, _json_data);

    var _loginController = new LoginFieldController();
    var _logoutController = new LogoutController();
    var _checkinController = new CheckinController();
    var _startStopController = new StartStopController();
    var _loginInfoController = new LoginInfoController();

    var _loginUtilController = new LoginUtilController();
    var _modalController = new ModalController();

    var _save_DD = null;
    var _isChangeToTomorow = false;

    var _date_now;
    var _ID_clock = 0;
    var _ID_refresh = 0;

    var _title_origin;

    function _init() {


        console.log("NEW NEW NEW NEW NEW NEW NEW NEW NEW NEW NEW NEW NEW NEW NEW NEW NEW");

        _title_origin = document.title;

        _setOffsetTime();
        _setNow();

        _bgControll.init();
        _clock.init(_date_now);
        _dateInfo.init(_date_now);

        _loginController.init();
        _logoutController.init();
        _checkinController.init();
        _startStopController.init();
        _loginInfoController.init(_json_data.info.today.notice, _json_data.info.birthday);

        _loginUtilController.init(_json_data.preset.document_url, _json_data.preset.main_url, _json_data.user);

        _modalController.init();

        startMotion();
        _startTimer();

        addEvent();
    }

    function _setOffsetTime() {
        if (_json_data.info != undefined &&
            _json_data.info.date != undefined &&
            _json_data.info.date.server_time != undefined) {

            _offsetTime = _json_data.info.date.server_time - new Date().getTime();

            console.log(CLASS_NAME + " [server time] : ", _json_data.info.date.server_time, " [client time] : ", new Date().getTime(), " [_offsetTime] : ", _offsetTime);
        }
    }

    function addEvent() {
        document.addEventListener(window.df.workgroup.Preset.eventType.ON_LOGIN, _onLogin);
        document.addEventListener(window.df.workgroup.Preset.eventType.ON_CHECKIN, _onCheckin);
        document.addEventListener(window.df.workgroup.Preset.eventType.ON_CHECKOUT, _onCheckout);
        document.addEventListener(window.df.workgroup.Preset.eventType.ON_START, _onStart);
        document.addEventListener(window.df.workgroup.Preset.eventType.ON_STOP, _onStop);
        document.addEventListener(window.df.workgroup.Preset.eventType.ON_CHANGE_STAGE_INFO, _onChange_stage_info);
        document.addEventListener(window.df.workgroup.Preset.eventType.ON_LOGOUT, _onLogout);
        document.addEventListener(window.df.workgroup.Preset.eventType.ON_WARNING, _onWarning);
    }

    function _resetData(response) {

        var actual_JSON = JSON.parse(response.target.responseText);
        console.log(CLASS_NAME + " << _resetData>> ", actual_JSON);
        window.df.workgroup.GlobalVars.infoData = actual_JSON;
        _json_data = window.df.workgroup.GlobalVars.infoData;

        _setOffsetTime();
    }

    function _onLogin(evt) {
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

    function _onStart(evt) {
        _resetData(evt.detail.response);
        _updateStatus();
    }

    function _onStop(evt) {
        _resetData(evt.detail.response);
        _updateStatus();
    }

    function _onLogout(evt) {
        _resetData(evt.detail.response);
        _updateStatus();
    }

    function _onWarning(evt) {
        //console.log(evt.detail.message);
        _modalController.showModal(evt.detail.message);
    }

    function _onChange_stage_info(event) {
        //console.log("_onChange_stage_info : stage_index - ", event.detail.curIndex);
    }

    // set layout
    function _setLayout_Logout() {

        var sec_util = document.querySelector('.sec-util');
        df.lab.Util.removeClass(sec_util, window.df.workgroup.Preset.class_name.showIn);

        var sec_login = document.querySelector('.sec-login');
        df.lab.Util.removeClass(sec_login, 'logged');

        _logoutController.hideLogoutBtn();
        _checkinController.hideCheckinBtn();
        _startStopController.hideStartBtn();
        _loginController.showLoginFrom();
    }

    function _setLayout_Login() {
        _loginController.hideLoginFrom();

        _logoutController.showLogoutBtn();
        //_checkinController.showCheckinBtn();
        _startStopController.showStartBtn();

        var sec_util = document.querySelector('.sec-util');
        df.lab.Util.addClass(sec_util, window.df.workgroup.Preset.class_name.showIn);

        _loginInfoController.showNotice();
    }

    function _setLayout_Checkin() {
        _checkinController.showCheckoutBtn();
    }

    function _setLayout_Checkout() {
        _checkinController.showCheckoutText();
    }

    function _setLayout_started() {
        _startStopController.showStopBtn();
    }

    function _setLayout_stop() {
        _startStopController.hideStopBtn();
    }

    function startMotion() {

        _updateStatus();

        var con_header = document.querySelector('header');
        setTimeout(function () {
            df.lab.Util.addClass(con_header, window.df.workgroup.Preset.class_name.showIn);
        }, 10);

        var con_info = document.querySelector('.sec-info');
        setTimeout(function () {
            df.lab.Util.addClass(con_info, window.df.workgroup.Preset.class_name.showIn);
        }, 0);

        var con_login = document.querySelector('.sec-login');
        setTimeout(function () {
            df.lab.Util.addClass(con_login, window.df.workgroup.Preset.class_name.showIn);
        }, 10);

        var con_footer = document.querySelector('footer');
        setTimeout(function () {
            df.lab.Util.addClass(con_footer, window.df.workgroup.Preset.class_name.showIn);
        }, 1500);
    }

    function _updateStatus() {

        var params = window.df.workgroup.GlobalVars.params; // browser params

        console.log(CLASS_NAME, " user : isLoggedIn - ", _json_data.user.isLoggedIn, " / isCheckin - ", _json_data.user.isCheckin, " / isCheckout", _json_data.user.isCheckout);

        _resetChangeDateCheck();

        var el_html = document.querySelector('html');
        var isDesktop = window.df.lab.Util.hasClass(el_html, 'desktop');

        // redirect
        if(_json_data.user.isLoggedIn && params.retUrl != undefined && params.retUrl !="" && params.retUrl.length > 0){
            console.log("--------------------------------------- go redirect : ", params.retUrl[0]);
            window.location.href = decodeURIComponent(params.retUrl[0]);
        }

        if (_json_data.user.isLoggedIn) {

            if (isDesktop && Detectizr.device.type == "desktop") {
                //redirectToMain();
                //return;

            } else if (json_data.user.isAdminAccount) {
                //redirectToMain();
                //return;
            }
        }

        // else -

        _resetBrowserTitle();

        if (params.test == "true") {
            _loginInfoController.resetData(_json_data.info.test.notice, _json_data.info.birthday);
        }else{
            _loginInfoController.resetData(_json_data.info.today.notice, _json_data.info.birthday);
        }

        _loginUtilController.resetData(_json_data.user);

        var sec_login = document.querySelector('.sec-login');

        if (_json_data.user.isLoggedIn) {

            df.lab.Util.addClass(sec_login, 'logged');

            _setLayout_Login();
            //
            // if (_json_data.user.isCheckin) {
            //     _setLayout_Checkin();
            //
            //     if (_json_data.user.isCheckout) {
            //         _setLayout_Checkout();
            //     }
            // }

            if (_json_data.user.workInfo.isWorking) {
                _setLayout_started();
            }else{
                _setLayout_stop();
            }
        }
        else {
            df.lab.Util.removeClass(sec_login, 'logged');
            _setLayout_Logout();
        }

    }

    function redirectToMain() {

        var url = "";
        if (_json_data.preset != undefined &&
            _json_data.preset.main_url != undefined) {

            url = _json_data.preset.main_url;
            //console.log(CLASS_NAME + " go to main url (get server) : ", url);
        } else {
            url = window.df.workgroup.Preset.main_url;
            //console.log(CLASS_NAME + " go to main url (get local) : ", url);
        }
        console.log(CLASS_NAME + " go to main url : ", url);
        window.location.href = decodeURIComponent(url);
        return;
    }

    function _resetBrowserTitle() {

        if (_json_data.user.isLoggedIn) {
            //document.title = _json_data.user.name + " " + _json_data.user.position+"님.";
            document.title = _title_origin;
        } else {
            document.title = _title_origin;
        }
    }

    function _startTimer() {
        _updateTimer();
    }

    function _updateTimer() {
        _setTimer();
        _clock.updateToday(_date_now);
        _dateInfo.updateToday(_date_now);
    }

    function _setNow(){
        _date_now = new Date();
        _date_now.setTime(_date_now.getTime() + _offsetTime);
        window.df.workgroup.GlobalVars.time_now = _date_now.getTime();
    }

    function _setTimer() {
        _setNow();
        if (_isChangeToTomorow != true && _save_DD != _date_now.getDate() && _save_DD != null) {
            _startChangeDateCheck();
        }
        _save_DD = _date_now.getDate();
        _ID_clock = setTimeout(_updateTimer, 500);
    }

    function _startChangeDateCheck() {

        console.log(">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> Change Date ");

        _isChangeToTomorow = true;

        document.removeEventListener("mousemove", onMouseMove_changeDate);
        document.addEventListener("mousemove", onMouseMove_changeDate);
        document.removeEventListener("touchstart", onMouseMove_changeDate);
        document.addEventListener("touchstart", onMouseMove_changeDate);
        document.removeEventListener("touchmove", onMouseMove_changeDate);
        document.addEventListener("touchmove", onMouseMove_changeDate);

        _delayAutoRefresh();
    }

    function _delayAutoRefresh() {

        var DELAY_TIME = 10000;
        console.log(">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> _delayAutoRefresh : ", DELAY_TIME);

        clearTimeout(_ID_refresh);
        _ID_refresh = setTimeout(function () {
            console.log("_Refresh");
            window.location.reload (true);
        }, DELAY_TIME);
    }

    function _resetChangeDateCheck() {

        if (_isChangeToTomorow) {
            console.log(">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> _resetChangeDateCheck");
            clearTimeout(_ID_refresh);
            document.removeEventListener("mousemove", onMouseMove_changeDate);
            document.removeEventListener("touchstart", onMouseMove_changeDate);
            document.removeEventListener("touchmove", onMouseMove_changeDate);
        }

        _isChangeToTomorow = false;
    }

    function onMouseMove_changeDate(evt) {
        _delayAutoRefresh();
    }

    return {
        init: _init
    }
};
