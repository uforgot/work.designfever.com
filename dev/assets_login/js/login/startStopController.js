module.exports = function () {

    var CLASS_NAME = "[ startStopController ]";

    var _wrapper = document.querySelector('.sec-login .wrapper-startStop');
    var _area_start = _wrapper.querySelector('.area-start.start');
    var _area_stop = _wrapper.querySelector('.area-start.stop');

    var _form = document.getElementById('id_start');
    var _form_stop = document.getElementById('id_stop');

    var _isCheckin = false;
    var _ID_INTERVAL_BAR = 0;

    var _json_user = null;

    function _init() {
        _setInfo();
        _setUrl();
        _form.addEventListener('submit', _onSubmit);
        _form_stop.addEventListener('submit', _onSubmit_stop);

        disable_input();
        disable_input_stop();
    }

    function _setInfo() {
        _json_user = window.df.workgroup.GlobalVars.infoData.user;

        var txt_name = document.getElementById("id_user_name_new");
        txt_name.textContent = _json_user.name;

        var txt_position = document.getElementById("id_user_position_new");
        txt_position.textContent = _json_user.position;
        if (_json_user.isLoggedIn) {

            if (_json_user.workInfo.isWorking) {
                var txt_time = document.getElementById("id_start_time");
                var date_start = new Date(_json_user.workInfo.startedTime);
                txt_time.textContent = date_start.getHours() + "시 " + window.df.workgroup.Util.addZeroNumber(date_start.getMinutes()) + "분";

                _isCheckin = true;
            }

            var txt_workingtime = document.getElementById("id_workingTime_forToday");
            var value_workingtime = getWorkingTimeFromMicroSEC(_json_user.workInfo.workingTime_forToday);
            txt_workingtime.textContent = value_workingtime.hours + "시간 " + window.df.workgroup.Util.addZeroNumber(value_workingtime.minutes) + "분";

            txt_workingtime = document.getElementById("id_workingTime_forThisWeek");
            value_workingtime = getWorkingTimeFromMicroSEC(_json_user.workInfo.workingTime_forThisWeek);
            txt_workingtime.textContent = value_workingtime.hours + "시간 " + window.df.workgroup.Util.addZeroNumber(value_workingtime.minutes) + "분";

            txt_workingtime = document.getElementById("id_workingTime_forThisMonth");
            value_workingtime = getWorkingTimeFromMicroSEC(_json_user.workInfo.workingTime_forThisMonth);
            txt_workingtime.textContent = value_workingtime.hours + "시간 " + window.df.workgroup.Util.addZeroNumber(value_workingtime.minutes) + "분";

            txt_workingtime = document.getElementById("id_workingTime_total_min_ofThisMonth");

            if(_json_user.workInfo.workingTime_forThisMonthMin == undefined || _json_user.workInfo.workingTime_forThisMonthMin == null){
                txt_workingtime.textContent = "-";
            }else {
                value_workingtime = getWorkingTimeFromMicroSEC(_json_user.workInfo.workingTime_forThisMonthMin);
                txt_workingtime.textContent = value_workingtime.hours + "시간";
                if (value_workingtime.minutes > 0) {
                    txt_workingtime.textContent = txt_workingtime.textContent + " " + window.df.workgroup.Util.addZeroNumber(value_workingtime.minutes) + "분";
                }
            }

            txt_workingtime = document.getElementById("id_workingTime_total_max_ofThisMonth");

            if(_json_user.workInfo.workingTime_forThisMonthMax == undefined || _json_user.workInfo.workingTime_forThisMonthMax == null){
                txt_workingtime.textContent = "-";
            }else {
                value_workingtime = getWorkingTimeFromMicroSEC(_json_user.workInfo.workingTime_forThisMonthMax);
                txt_workingtime.textContent = value_workingtime.hours + "시간";
                if (value_workingtime.minutes > 0) {
                    txt_workingtime.textContent = txt_workingtime.textContent + " " + window.df.workgroup.Util.addZeroNumber(value_workingtime.minutes) + "분";
                }
            }
        }
    }

    function getWorkingTimeFromMicroSEC(microSEC){

        var sec = (Math.floor(microSEC/1000))%60;
        var min = Math.floor(microSEC/1000/60)%60;
        var hour = Math.floor(microSEC/1000/60/60);

        return {
            hours: hour,
            minutes: min,
            seconds: sec
        }
    }

    function _setUrl() {
        var json_data = window.df.workgroup.GlobalVars.infoData;
        if (json_data.preset != undefined &&
            json_data.preset.json_url != undefined &&
            json_data.preset.json_url.start != undefined) {

            _form.action = json_data.preset.json_url.start;
        } else {
            _form.action = window.df.workgroup.Preset.json_url.start;
        }

        if (json_data.preset != undefined &&
            json_data.preset.json_url != undefined &&
            json_data.preset.json_url.stop != undefined) {

            _form_stop.action = json_data.preset.json_url.stop;
        } else {
            _form_stop.action = window.df.workgroup.Preset.json_url.stop;
        }
    }

    function _onSubmit($evt) {
        $evt.preventDefault();
        _submit();
    }

    function _submit() {
        loading();
        ajaxPost(_form, onCompSubmit);
        return false;
    }


    function _onSubmit_stop($evt) {
        $evt.preventDefault();
        _submit_stop();
    }

    function _submit_stop() {
        loading_forStop();
        ajaxPost(_form_stop, onCompSubmit_stop);
        return false;
    }

    function loading() {

        var loading = _area_start.querySelector('.ui-loading');
        df.lab.Util.addClass(loading, window.df.workgroup.Preset.class_name.showIn);

        disable_input();
    }


    function loading_forStop() {

        var loading = _area_stop.querySelector('.ui-loading');
        df.lab.Util.addClass(loading, window.df.workgroup.Preset.class_name.showIn);

        disable_input_stop();
    }

    function disable_input() {

        var inputs = _form.querySelectorAll('input');
        for (var i = 0; i < inputs.length; i++) {
            inputs[i].setAttribute("disabled", "");
        }
    }

    function able_input() {
        var inputs = _form.querySelectorAll('input');
        for (var i = 0; i < inputs.length; i++) {
            inputs[i].removeAttribute("disabled");
        }
    }

    function disable_input_stop() {
        var inputs = _form_stop.querySelectorAll('input');
        for (var i = 0; i < inputs.length; i++) {
            inputs[i].setAttribute("disabled", "");
        }
    }

    function able_input_stop() {
        var inputs = _form_stop.querySelectorAll('input');
        for (var i = 0; i < inputs.length; i++) {
            inputs[i].removeAttribute("disabled");
        }
    }

    function onCompSubmit(response) {
        var loading = _area_start.querySelector('.ui-loading');
        df.lab.Util.removeClass(loading, window.df.workgroup.Preset.class_name.showIn);
        _dispatchOnLoad(response);

        var status = getStatus(response);

        if (status.isWarning) {
            console.log("status.text : ", status.text);
            _dispatchOnWarning(status.text);
        }
    }

    function onCompSubmit_stop(response) {
        var loading = _area_stop.querySelector('.ui-loading');
        df.lab.Util.removeClass(loading, window.df.workgroup.Preset.class_name.showIn);
        _dispatchOnLoad_stop(response);

        var status = getStatus(response);

        if (status.isWarning) {
            //console.log("status.text : " , status.text);
            _dispatchOnWarning(status.text);
        }
    }

    function getStatus(response) {

        var status = {
            isWarning: false,
            text: "표시할 메세지가 없습니다."
        };
        var json = JSON.parse(response.target.responseText);
        var user_status_code = json.user.status;
        if (
            user_status_code.toLowerCase() == ("L00").toLowerCase() ||
            user_status_code.toLowerCase() == ("L01").toLowerCase() ||
            user_status_code.toLowerCase() == ("L02").toLowerCase() ||
            user_status_code.toLowerCase() == ("L03").toLowerCase() ||
            user_status_code.toLowerCase() == ("L04").toLowerCase() ||

            user_status_code.toLowerCase() == ("C10").toLowerCase() ||

            user_status_code.toLowerCase() == ("C01").toLowerCase() ||
            user_status_code.toLowerCase() == ("C02").toLowerCase() ||
            user_status_code.toLowerCase() == ("C03").toLowerCase() ||
            user_status_code.toLowerCase() == ("C04").toLowerCase() ||
            user_status_code.toLowerCase() == ("C05").toLowerCase() ||

            user_status_code.toLowerCase() == ("C11").toLowerCase() ||
            user_status_code.toLowerCase() == ("C12").toLowerCase() ||
            user_status_code.toLowerCase() == ("C13").toLowerCase() ||
            user_status_code.toLowerCase() == ("C14").toLowerCase() ||
            user_status_code.toLowerCase() == ("C15").toLowerCase() ||


            user_status_code.toLowerCase() == ("W00").toLowerCase() ||

            user_status_code.toLowerCase() == ("W01").toLowerCase() ||
            user_status_code.toLowerCase() == ("W02").toLowerCase() ||
            user_status_code.toLowerCase() == ("W03").toLowerCase() ||
            user_status_code.toLowerCase() == ("W04").toLowerCase() ||
            user_status_code.toLowerCase() == ("W05").toLowerCase() ||
            user_status_code.toLowerCase() == ("W06").toLowerCase() ||
            user_status_code.toLowerCase() == ("W07").toLowerCase() ||

            user_status_code.toLowerCase() == ("W10").toLowerCase() ||
            user_status_code.toLowerCase() == ("W11").toLowerCase() ||
            user_status_code.toLowerCase() == ("W12").toLowerCase() ||
            user_status_code.toLowerCase() == ("W13").toLowerCase() ||
            user_status_code.toLowerCase() == ("W14").toLowerCase()

        ) {
            var list = json.preset.status_list;
            for (var i = 0; i < list.length; i++) {
                var item = list[i];
                var code = item.code;

                if (code.toLowerCase() == user_status_code.toLowerCase()) {
                    status.isWarning = true;
                    status.text = item.text;
                    break;
                }
            }
        }
        return status;
    }

    function _dispatchOnWarning(txt) {
        var event = new CustomEvent(window.df.workgroup.Preset.eventType.ON_WARNING, {
            detail: {
                message: txt
            }
        });
        document.dispatchEvent(event);
    }

    function _dispatchOnLoad(response) {
        var event = new CustomEvent(window.df.workgroup.Preset.eventType.ON_START, {
            detail: {
                response: response
            }
        });
        document.dispatchEvent(event);
    }

    function _dispatchOnLoad_stop(response) {
        var event = new CustomEvent(window.df.workgroup.Preset.eventType.ON_STOP, {
            detail: {
                response: response
            }
        });
        document.dispatchEvent(event);
    }

    function _showStartBtn() {

        _setInfo();
        _setUrl();

        df.lab.Util.addClass(_wrapper, window.df.workgroup.Preset.class_name.showIn);
        able_input();
        disable_input_stop();
    }

    function _hideStartBtn() {
        _resetLayout();
    }

    function _showStopBtn() {

        _setInfo();
        _setUrl();

        df.lab.Util.addClass(_wrapper, 'checked');
        disable_input();
        able_input_stop();
    }


    function _hideStopBtn() {

        _setInfo();
        _setUrl();

        df.lab.Util.removeClass(_wrapper, 'checked');
        disable_input_stop();
        able_input();
    }

    function _resetLayout() {
        df.lab.Util.removeClass(_wrapper, window.df.workgroup.Preset.class_name.showIn);
        df.lab.Util.removeClass(_wrapper, 'checked');
        df.lab.Util.removeClass(_wrapper, 'checkedout');

        disable_input();
        disable_input_stop();
    }

    function ajaxPost(form, callback) {
        // Collect the form data while iterating over the inputs
        var data = {};
        for (var i = 0, ii = form.length; i < ii; ++i) {
            var input = form[i];
            if (input.name) {
                data[input.name] = input.value;
            }
        }

        var params = {
            method: form.method,
            action: df.workgroup.Util.addParamUniq(form.action)
        };
        //
        // if(isTest_re) {
        //     params.action = "assets_login/temp/df_info_data_03_checkout_re.json";
        // }

        df.workgroup.Util.load_json(params.action, params.method, callback, data);
    }

    return {
        init: _init,
        showStartBtn: _showStartBtn,
        hideStartBtn: _hideStartBtn,
        showStopBtn: _showStopBtn,
        hideStopBtn: _hideStopBtn
    }
};
