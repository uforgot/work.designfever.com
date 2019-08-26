module.exports = function () {

    var CLASS_NAME = "[ startStopController ]";

    var _wrapper = document.querySelector('.sec-login .wrapper-startStop');
    var _area_start = _wrapper.querySelector('.area-start');

    var _form = document.getElementById('id_start');

    var _isCheckin = false;
    var _ID_INTERVAL_BAR = 0;

    var _json_user = null;

    function _init() {
        _setInfo();
        _setUrl();
        _form.addEventListener('submit', _onSubmit);
        disable_input();
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
        }
    }

    function _setUrl() {
        var json_data = window.df.workgroup.GlobalVars.infoData;
        if (json_data.preset != undefined &&
            json_data.preset.json_url != undefined &&
            json_data.preset.json_url.start != undefined) {

            _form.action = json_data.preset.json_url.start;
            //console.log(CLASS_NAME + " action(server) : ", _form.action);
        } else {
            _form.action = window.df.workgroup.Preset.json_url.start;
            //console.log(CLASS_NAME + " action(local) : ", _form.action);
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

    function loading() {

        var loading = _area_start.querySelector('.ui-loading');
        df.lab.Util.addClass(loading, window.df.workgroup.Preset.class_name.showIn);

        disable_input();
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

    function getStatus(response) {

        var status = {
            isWarning: false,
            text: "표시할 메세지가 없습니다."
        };
        var json = JSON.parse(response.target.responseText);
        var user_status_code = json.user.status;
        if (
            //user_status_code.toLowerCase() == ("C00").toLowerCase() ||
            user_status_code.toLowerCase() == ("L00").toLowerCase() ||

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
            user_status_code.toLowerCase() == ("C15").toLowerCase()
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

    function _showStartBtn() {

        _setInfo();
        _setUrl();

        df.lab.Util.addClass(_wrapper, window.df.workgroup.Preset.class_name.showIn);
        able_input();
    }

    function _hideStartBtn() {
        _resetLayout();
    }

    function _showStopBtn() {

        _setInfo();
        _setUrl();

        df.lab.Util.addClass(_wrapper, 'checked');
        disable_input();
        //able_input_out();
    }
    function _resetLayout() {
        df.lab.Util.removeClass(_wrapper, window.df.workgroup.Preset.class_name.showIn);
        df.lab.Util.removeClass(_wrapper, 'checked');
        df.lab.Util.removeClass(_wrapper, 'checkedout');

        disable_input();
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
        showStopBtn: _showStopBtn
    }
};
