var CheckinController = function(){

    var CLASS_NAME = "[ CheckinController ]";

    var _wrapper = document.querySelector('.sec-login .wrapper-checkin');
    var _area_checkin = _wrapper.querySelector('.area-check-inout.area-checkin');
    var _area_checkout = _wrapper.querySelector('.area-check-inout.area-checkout');

    var _form = document.getElementById('id_checkin');
    var _form_out = document.getElementById('id_checkout');
    var _btn_checkout = document.getElementById('id_btn_checkout_re');

    var _isCjeckin = false;
    var _ID_INTERVAL_BAR = 0;

    var _json_user = null;

    function _init(){
        _setInfo();
        _setUrl();
        _form.addEventListener( 'submit',  _onSubmit);
        _form_out.addEventListener( 'submit',  _onSubmit_out);
        _btn_checkout.addEventListener( 'click',  _onClick_btn_checkout);
        disable_input();
        disable_input_out();
    }

    function _setInfo(){
        _json_user = window.df.workgroup.GlobalVars.infoData.user;


        var txt_name = document.getElementById("id_user_name");
        txt_name.textContent = _json_user.name;

        var txt_position = document.getElementById("id_user_position");
        txt_position.textContent = _json_user.position;

        if(_json_user.isLoggedIn){

            if(_json_user.isCheckin){
                var txt_checkin_time = document.getElementById("id_checkin_time");
                var date_checkin = new Date(_json_user.checkin_time);
                txt_checkin_time.textContent = date_checkin.getHours() + "시 " +  window.df.workgroup.Util.addZeroNumber(date_checkin.getMinutes()) + "분";

                // checkout able time
                var txt_checkout_able_time = document.getElementById("id_checkout_able_time");
                var date_checkout_able = new Date(_json_user.checkout_able_time);

                txt_checkout_able_time.textContent = date_checkout_able.getHours() + "시 " +  window.df.workgroup.Util.addZeroNumber(date_checkout_able.getMinutes()) + "분";

                _isCjeckin = true;

                if(!_json_user.isCheckout){
                    startSetTimeBar();
                }else{
                    stopSetTimeBar();

                    var txt_checkout_time = document.getElementById("id_checkout_time");
                    var date_checkout = new Date(_json_user.checkout_time);

                    var hh = date_checkout.getHours();
                    if(date_checkout.getDate() - date_checkin.getDate() > 0){
                        hh = ((date_checkout.getDate() - date_checkin.getDate()) * 24 ) + date_checkout.getHours();
                    }

                    txt_checkout_time.textContent = hh + "시 " +  window.df.workgroup.Util.addZeroNumber(date_checkout.getMinutes()) + "분";
                }
            }else{
                stopSetTimeBar();
            }
        }else{
            stopSetTimeBar();
        }
    }

    function startSetTimeBar(){
        stopSetTimeBar();
        setTimePerBar();
        //_ID_INTERVAL_BAR = setInterval(setTimePerBar, 30000);
        _ID_INTERVAL_BAR = setInterval(setTimePerBar, 1000);
    }

    function setTimePerBar(){

        //console.log("setTimePerBar : ", _json_user.isCheckin);

        if(_json_user.isCheckin){
            // console.log("checkin_time : " , _json_user.checkin_time);
            // console.log("time_now : " , window.df.workgroup.GlobalVars.time_now);
            // console.log("checkout_able_time : " , _json_user.checkout_able_time);

            var dis = _json_user.checkout_able_time - _json_user.checkin_time;
            var cur = window.df.workgroup.GlobalVars.time_now - _json_user.checkin_time;

            var per = cur / dis;

            //console.log("per : " , per, dis, cur);

            if(per < 0) per = 0;
            if(per > 1) per = 1;

            var per_str = Math.round(per*100) + "%";
            var cur_bar = document.getElementById("id_per_time");
            cur_bar.style.width = per_str;

            _checkAbleTime(per >= 0);

        }else{
            stopSetTimeBar();
        }
    }

    function stopSetTimeBar(){
        _isCjeckin = false;
        clearInterval(_ID_INTERVAL_BAR);
    }

    function _setUrl(){
        var json_data = window.df.workgroup.GlobalVars.infoData;
        if(json_data.preset != undefined &&
            json_data.preset.json_url != undefined &&
            json_data.preset.json_url.checkin != undefined){

            _form.action = json_data.preset.json_url.checkin;
            //console.log(CLASS_NAME + " action(server) : ", _form.action);
        }else{
            _form.action = window.df.workgroup.Preset.json_url.checkin;
            //console.log(CLASS_NAME + " action(local) : ", _form.action);
        }

        if(json_data.preset != undefined &&
            json_data.preset.json_url != undefined &&
            json_data.preset.json_url.checkout != undefined){

            _form_out.action = json_data.preset.json_url.checkout;
            //console.log(CLASS_NAME + " action(server) : ", _form_out.action);
        }else{
            _form_out.action = window.df.workgroup.Preset.json_url.checkout;
            //console.log(CLASS_NAME + " action(local) : ", _form_out.action);
        }
    }

    function _onClick_btn_checkout($evt){
        $evt.preventDefault();
        _submit_out_re();
    }
    function _onSubmit( $evt ) {
        $evt.preventDefault();
        _submit();
    }

    function _submit(){
        loading();
        ajaxPost(_form, onCompSubmit);
        return false;
    }

    function _onSubmit_out( $evt ) {
        $evt.preventDefault();
        _submit_out();
    }

    function _submit_out(){
        loading_out();
        ajaxPost(_form_out, onCompSubmit_out);
        return false;
    }

    function _submit_out_re(){
        _submit_out();
    }

    function loading(){

        var loading = _area_checkin.querySelector('.ui-loading');
        df.lab.Util.addClass(loading, window.df.workgroup.Preset.class_name.showIn);

        disable_input();
    }

    function loading_out(){

        var loading = _area_checkout.querySelector('.ui-loading');
        df.lab.Util.addClass(loading, window.df.workgroup.Preset.class_name.showIn);

        disable_input_out();
    }

    function disable_input(){

        var inputs = _form.querySelectorAll('input');
        for (var i = 0; i < inputs.length; i++) {
            inputs[i].setAttribute("disabled", "");
        }
    }

    function able_input(){
        var inputs = _form.querySelectorAll('input');
        for (var i = 0; i < inputs.length; i++) {
            inputs[i].removeAttribute("disabled");
        }
    }

    function disable_input_out(){
        var inputs = _form_out.querySelectorAll('input');
        for (var i = 0; i < inputs.length; i++) {
            inputs[i].setAttribute("disabled", "");
        }
    }

    function able_input_out(){
        var inputs = _form_out.querySelectorAll('input');
        for (var i = 0; i < inputs.length; i++) {
            inputs[i].removeAttribute("disabled");
        }
    }

    function onCompSubmit(response){
        var loading = _area_checkin.querySelector('.ui-loading');
        df.lab.Util.removeClass(loading, window.df.workgroup.Preset.class_name.showIn);
        _dispatchOnLoad(response);

        var status = getStatus(response);

        if(status.isWarning) {
            console.log("status.text : " , status.text);
            _dispatchOnWarning(status.text);
        }
    }

    function onCompSubmit_out(response){
        var loading = _area_checkout.querySelector('.ui-loading');
        df.lab.Util.removeClass(loading, window.df.workgroup.Preset.class_name.showIn);
        _dispatchOnLoad_out(response);

        var status = getStatus(response);

        if(status.isWarning) {
            console.log("status.text : " , status.text);
            _dispatchOnWarning(status.text);
        }
    }

    function getStatus(response){

        var status = {
            isWarning : false,
            text: "표시할 메세지가 없습니다."
        };
        var json = JSON.parse(response.target.responseText);
        var user_status_code = json.user.status;
        if(
            user_status_code.toLowerCase() == ("C00").toLowerCase() ||
            user_status_code.toLowerCase() == ("C10").toLowerCase() ||

            user_status_code.toLowerCase() == ("C01").toLowerCase() ||
            user_status_code.toLowerCase() == ("C02").toLowerCase() ||
            user_status_code.toLowerCase() == ("C03").toLowerCase() ||
            user_status_code.toLowerCase() == ("C04").toLowerCase() ||
            user_status_code.toLowerCase() == ("C05").toLowerCase() ||

            user_status_code.toLowerCase() == ("C11").toLowerCase() ||
            user_status_code.toLowerCase() == ("C12").toLowerCase() ||
            user_status_code.toLowerCase() == ("C13").toLowerCase() ||
            user_status_code.toLowerCase() == ("C14").toLowerCase()
        ){
            var list = json.preset.status_list;
            for(var i=0; i<list.length; i++){
                var item =  list[i];
                var code = item.code;

                if(code.toLowerCase() == user_status_code.toLowerCase()){
                    status.isWarning = true;
                    status.text = item.text;
                    break;
                }
            }
        }
        return status;
    }

    function _dispatchOnWarning(txt){
        var event = new CustomEvent(window.df.workgroup.Preset.eventType.ON_WARNING, {
            detail: {
                message: txt
            }});
        document.dispatchEvent(event);
    }

    function _dispatchOnLoad(response){
        var event = new CustomEvent(window.df.workgroup.Preset.eventType.ON_CHECKIN, {
            detail: {
                response: response
            }});
        document.dispatchEvent(event);
    }

    function _dispatchOnLoad_out(response){
        var event = new CustomEvent(window.df.workgroup.Preset.eventType.ON_CHECKOUT, {
            detail: {
                response: response
            }});
        document.dispatchEvent(event);
    }

    function _showCheckinBtn(){

        _setInfo();
        _setUrl();

        df.lab.Util.addClass(_wrapper, window.df.workgroup.Preset.class_name.showIn);
        able_input();
        disable_input_out();
    }

    function _showCheckoutBtn(){

        _setInfo();
        _setUrl();

        df.lab.Util.addClass(_wrapper, 'checked');
        disable_input();
        able_input_out();
        //_checkAbleTime();
        //setTimeout(setTimeBar, 1000);
    }

    function _showCheckoutText(){

        _setInfo();
        _setUrl();

        df.lab.Util.addClass(_wrapper, 'checkedout');
        disable_input();
        disable_input_out();
    }

    function _hideCheckinBtn(){
        _resetLayout();
    }

    function _resetLayout(){
        df.lab.Util.removeClass(_wrapper, window.df.workgroup.Preset.class_name.showIn);
        df.lab.Util.removeClass(_wrapper, 'checked');
        df.lab.Util.removeClass(_wrapper, 'checkedout');

        disable_input();
        disable_input_out();
    }

    function _checkAbleTime(isAble){
        if(isAble){
            //"checkout-able"
            df.lab.Util.addClass(_area_checkout, "checkout-able");
        }else{
            df.lab.Util.removeClass(_area_checkout, "checkout-able");
        }
    }

    function ajaxPost (form, callback) {
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
        showCheckinBtn: _showCheckinBtn,
        showCheckoutBtn: _showCheckoutBtn,
        showCheckoutText: _showCheckoutText,
        hideCheckinBtn: _hideCheckinBtn
    }
};