var CheckinController = function(){

    var CLASS_NAME = "[ CheckinController ]";
    var _form = document.getElementById('id_checkin');
    var _form_out = document.getElementById('id_checkout');
    var _btn_checkout = document.getElementById('id_btn_checkout_re');

    function _init(){
        _setUrl();
        _form.addEventListener( 'submit',  _onSubmit);
        _form_out.addEventListener( 'submit',  _onSubmit_out);
        _btn_checkout.addEventListener( 'click',  _onClick_btn_checkout);
        disable_input_out();
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
        _checkAbleTime();
        setTimeout(setTimeBar, 1000);
    }

    function _showCheckoutText(){
        var wrapper_checkin = document.querySelector('.sec-login .wrapper-checkin');
        df.lab.Util.addClass(wrapper_checkin, 'checkedout');
        disable_input();
        disable_input_out();
    }

    function setTimeBar(){
        var cur_bar = document.getElementById("id_per_time");
        cur_bar.style.width = "75%";
    }

    function _checkAbleTime(){

        var area_checkout = document.querySelector('.sec-login .wrapper-checkin .area-check-inout.area-checkout');

        var isAble = true;
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
            action: form.action
        };

        // Construct an HTTP request
        var xhr = new XMLHttpRequest();
        xhr.open(params.method, params.action, true);
        xhr.setRequestHeader('Accept', 'application/json; charset=EUC-KR');
        xhr.setRequestHeader('Content-Type', 'application/json; charset=EUC-KR');

        // Send the collected data as JSON
        xhr.send(JSON.stringify(data));

        xhr.onreadystatechange = function () {

            console.log(CLASS_NAME + " xhr.readyState : ", xhr.readyState);
            console.log(CLASS_NAME + " xhr.status : ", xhr.status);
        };

        // Callback function
        xhr.onloadend = function (response) {

            console.log(CLASS_NAME + " xhr.onloadend : " , response);

            if (response.target.status === 0) {

                // Failed XmlHttpRequest should be considered an undefined error.
                //console.log(CLASS_NAME + " xhr.onloadend (Failed) : " , xhr);

            } else if (response.target.status === 400) {

                // Bad Request
                //console.log(CLASS_NAME + " xhr.onloadend (Bad Request) : " , xhr);
            } else if (response.target.status === 404) {

                // Bad Request
                //console.log(CLASS_NAME + " xhr.onloadend (404 Not Found) : " , xhr);

            } else if (response.target.status === 200) {

                // Success
                //console.log("xhr.onloadend (Success) : form.dataset.formSuccess" , form.dataset.formSuccess);
                //console.log("xhr.onloadend (Success) response : " , response);
                //console.log("xhr.onloadend (Success) xhr : " , xhr);
                console.log(CLASS_NAME + " xhr.onloadend (Success) response : " , JSON.parse(response.target.responseText));

                setTimeout(function(){
                    callback(response);
                }, 100);

                //callback(response);
            }
        };
    }
    return {
        init: _init,
        showCheckinBtn: _showCheckinBtn,
        showCheckoutBtn: _showCheckoutBtn,
        showCheckoutText: _showCheckoutText
    }
};