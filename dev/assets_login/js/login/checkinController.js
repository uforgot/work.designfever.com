var CheckinController = function(){

    var CLASS_NAME = "[ CheckinController ]";
    var _form = document.getElementById('id_checkin');
    var _form_out = document.getElementById('id_checkout');
    var _btn_checkout = document.getElementById('id_btn_checkout_re');

    var _isCjeckin = false;
    var _ID_INTERVAL_BAR = 0;

    var _json_user = null;

    function _init(json_user){
        _setInfo(json_user);
        _setUrl();
        _form.addEventListener( 'submit',  _onSubmit);
        _form_out.addEventListener( 'submit',  _onSubmit_out);
        _btn_checkout.addEventListener( 'click',  _onClick_btn_checkout);
        disable_input_out();
    }

    function _setInfo(json_user){

        _json_user = json_user;

        // console.log(CLASS_NAME + " isLoggedIn : " , _json_user.isLoggedIn);
        // console.log(CLASS_NAME + " isCheckin : " , _json_user.isCheckin);
        // console.log(CLASS_NAME + " checkin_time : " , _json_user.checkin_time);
        // console.log(CLASS_NAME + " checkout_able_time : " , _json_user.checkout_able_time);
        // console.log(CLASS_NAME + " isCheckout : " , _json_user.isCheckout);
        // console.log(CLASS_NAME + " checkout_time : " , _json_user.checkout_time);

        if(_json_user.isLoggedIn){

            if(_json_user.isCheckin){
                var txt_checkin_time = document.getElementById("id_checkin_time");
                var date_checkin = new Date(_json_user.checkin_time);
                txt_checkin_time.textContent = date_checkin.getHours() + "시 " +  date_checkin.getMinutes() + "분";

                // checkout able time
                var txt_checkout_able_time = document.getElementById("id_checkout_able_time");
                var date_checkout_able = new Date(_json_user.checkout_able_time);

                txt_checkout_able_time.textContent = date_checkout_able.getHours() + "시 " +  date_checkout_able.getMinutes() + "분";

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

                    txt_checkout_time.textContent = hh + "시 " +  date_checkout.getMinutes() + "분";
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


        console.log("startSetTimeBar");

        setTimePerBar();
        //_ID_INTERVAL_BAR = setInterval(setTimePerBar, 30000);
        _ID_INTERVAL_BAR = setInterval(setTimePerBar, 1000);
    }

    function setTimePerBar(){

        console.log("setTimePerBar : ", _json_user.isCheckin);

        if(_json_user.isCheckin){
            console.log("checkin_time : " , _json_user.checkin_time);
            console.log("time_now : " , window.df.workgroup.GlobalVars.time_now);
            console.log("checkout_able_time : " , _json_user.checkout_able_time);

            var dis = _json_user.checkout_able_time - _json_user.checkin_time;
            var cur = window.df.workgroup.GlobalVars.time_now - _json_user.checkin_time;

            var per = cur / dis;

            console.log("per : " , per, dis, cur);

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
            console.log(CLASS_NAME + " action(server) : ", _form.action);
        }else{
            _form.action = window.df.workgroup.Preset.json_url.checkin;
            console.log(CLASS_NAME + " action(local) : ", _form.action);
        }

        if(json_data.preset != undefined &&
            json_data.preset.json_url != undefined &&
            json_data.preset.json_url.checkout != undefined){

            _form_out.action = json_data.preset.json_url.checkout;
            console.log(CLASS_NAME + " action(server) : ", _form_out.action);
        }else{
            _form_out.action = window.df.workgroup.Preset.json_url.checkout;
            console.log(CLASS_NAME + " action(local) : ", _form_out.action);
        }
    }

    function _onClick_btn_checkout($evt){
        $evt.preventDefault();

        console.log("_onClick_btn_checkout");
        _submit_out();
    }
    function _onSubmit( $evt ) {
        $evt.preventDefault();
        _submit();
    }

    function _submit(){
        loading();
        ajaxPost(_form, onSubmit);
        return false;
    }

    function _onSubmit_out( $evt ) {
        $evt.preventDefault();
        _submit_out();
    }

    function _submit_out(){
        loading_out();
        ajaxPost(_form_out, onSubmit_out);
        return false;
    }

    function loading(){
        disable_input();
    }

    function loading_out(){
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

    function onSubmit(response){
        able_input();
        _dispatchOnLoad();
    }

    function onSubmit_out(response){
        able_input_out();
        _dispatchOnLoad_out();
    }

    function _dispatchOnLoad(){
        var event = new CustomEvent(window.df.workgroup.Preset.eventType.ON_CHECKIN);
        document.dispatchEvent(event);
    }

    function _dispatchOnLoad_out(){
        var event = new CustomEvent(window.df.workgroup.Preset.eventType.ON_CHECKOUT);
        document.dispatchEvent(event);
    }

    function _showCheckinBtn(){
        var wrapper_checkin = document.querySelector('.sec-login .wrapper-checkin');
        df.lab.Util.addClass(wrapper_checkin, window.df.workgroup.Preset.class_name.showIn);
    }

    function _showCheckoutBtn(){
        var wrapper_checkin = document.querySelector('.sec-login .wrapper-checkin');
        df.lab.Util.addClass(wrapper_checkin, 'checked');
        disable_input();
        able_input_out();
        //_checkAbleTime();
        //setTimeout(setTimeBar, 1000);
    }

    function _showCheckoutText(){
        var wrapper_checkin = document.querySelector('.sec-login .wrapper-checkin');
        df.lab.Util.addClass(wrapper_checkin, 'checkedout');
        disable_input();
        disable_input_out();
    }

    function _checkAbleTime(isAble){

        var area_checkout = document.querySelector('.sec-login .wrapper-checkin .area-check-inout.area-checkout');

        if(isAble){
            //"checkout-able"
            df.lab.Util.addClass(area_checkout, "checkout-able");
        }else{
            df.lab.Util.removeClass(area_checkout, "checkout-able");
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
            action: form.action + "?uniq=" + new Date().getTime()
        };

        df.workgroup.Util.load_json(params.action, params.method, callback, data);
    }
    return {
        init: _init,
        showCheckinBtn: _showCheckinBtn,
        showCheckoutBtn: _showCheckoutBtn,
        showCheckoutText: _showCheckoutText
    }
};