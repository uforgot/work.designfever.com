var LoginFieldController = function(){

    var KEYBOARD_ENTER = 13;
    var KEYBOARD_TAB = 9;
    var input_user_id, input_user_pw;
    var storageId, storagePw;

    var CLASS_NAME = "[ LoginFieldController ]";
    var _form = document.getElementById('id_login');

    function _init(){
        _inputKeyController();
        _setUrl();
    }

    function _setUrl(){
        var json_data = window.df.workgroup.GlobalVars.infoData;
        if(json_data.preset != undefined &&
            json_data.preset.json_url != undefined &&
            json_data.preset.json_url.login != undefined){

            _form.action = json_data.preset.json_url.login;
            console.log(CLASS_NAME + " action(server) : ", _form.action);
        }else{
            _form.action = window.df.workgroup.Preset.json_url.login;
            console.log(CLASS_NAME + " action(local) : ", _form.action);
        }
    }

    function _inputKeyController(){
        input_user_id = document.getElementById('user_id');
        input_user_pw = document.getElementById('user_pw');

        input_user_id.addEventListener( 'keypress', _keypressId );
        //input_user_pw.addEventListener( 'keypress', _keypressPwd );

        _form.addEventListener( 'submit',  _onSubmit);

        setTimeout(function(){
            setFocus();
        }, 500);
    }

    function setFocus(){

        if(storageId == null || storageId == undefined){
            input_user_id.focus();
            //console.log(CLASS_NAME + " focus: ", input_user_id);
        }
    }

    function _keypressId( $evt ) {
        switch( $evt.which ) {
            case KEYBOARD_ENTER :
                input_user_pw.focus();
                break;
            case KEYBOARD_TAB :
                console.log("ID");
                break;
        }
    }

    function _keypressPwd( $evt ) {
        switch( $evt.which ) {
            case KEYBOARD_ENTER :
                //_loginCheck();
                break;
            case KEYBOARD_TAB :
                console.log("PW");
                break;
        }
    }

    function _onSubmit( $evt ) {
        $evt.preventDefault();
        console.log("_onSubmit");
        _loginCheck();
    }

    function _loginCheck() {

         if( _form.user_id.value.length < 3 || _form.user_id.value.length > 16 ) {
         alert("아이디가 존재하지 않습니다.");
         _form.user_id.focus();
         return false;
         }

         if( _form.user_pw.value.length < 4 || _form.user_pw.value.length > 16) {
         alert("잘못된 패스워드입니다. (4-16자리 가능)");
         _form.user_pw.focus();
         return false;
         }

        _submit();
    }

    function _submit(){

        console.log("action : ", _form.action);
        console.log("target : ", _form.target);

        //alert("action : " + _form.action + "\ntarget : " + _form.target);

        var btn = document.getElementById('user_pw');
        btn.blur();

        //_form.submit();
        loading();
        ajaxPost(_form, onSubmit);
        return false;
    }

    function loading(){
        var sec_login = document.querySelector('.sec-login');
        df.lab.Util.addClass(sec_login, 'loading');

        var inputs = sec_login.querySelectorAll('input');

        // inputs.forEach(function(el, index){
        //     el.setAttribute("disabled", "");
        // });

        for (var i = 0; i < inputs.length; i++) {
            inputs[i].setAttribute("disabled", "");
        }

    }

    function onSubmit(response){
        console.log(response);
        onLog();
    }

    function onLog(){
        var sec_login = document.querySelector('.sec-login');
        df.lab.Util.removeClass(sec_login, 'loading');
        df.lab.Util.addClass(sec_login, 'logged');

        var inputs = sec_login.querySelectorAll('input');
        // inputs.forEach(function(el, index){
        //     el.removeAttribute("disabled");
        // });

        for (var i = 0; i < inputs.length; i++) {
            inputs[i].removeAttribute("disabled");
        }


        var sec_util = document.querySelector('.sec-util');
        df.lab.Util.addClass(sec_util, 'logged');

        var btn_logout = document.querySelector('header .wrapper-logout');
        df.lab.Util.addClass(btn_logout, 'show');
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
        xhr.setRequestHeader('Accept', 'application/json; charset=utf-8');
        xhr.setRequestHeader('Content-Type', 'application/json; charset=UTF-8');

        // Send the collected data as JSON
        xhr.send(JSON.stringify(data));

        xhr.onreadystatechange = function () {

            console.log("[LoginFieldController ] xhr.readyState : ", xhr.readyState);
            console.log("[LoginFieldController ] xhr.status : ", xhr.status);
        };

        // Callback function
        xhr.onloadend = function (response) {

            console.log("[LoginFieldController ] xhr.onloadend : " , response);

            if (response.target.status === 0) {

                // Failed XmlHttpRequest should be considered an undefined error.
                console.log("[LoginFieldController ] xhr.onloadend (Failed) : " , xhr);

            } else if (response.target.status === 400) {

                // Bad Request
                console.log("[LoginFieldController ] xhr.onloadend (Bad Request) : " , xhr);
            } else if (response.target.status === 404) {

                // Bad Request
                console.log("[LoginFieldController ] xhr.onloadend (404 Not Found) : " , xhr);

            } else if (response.target.status === 200) {

                // Success
                //console.log("xhr.onloadend (Success) : form.dataset.formSuccess" , form.dataset.formSuccess);
                //console.log("xhr.onloadend (Success) response : " , response);
                //console.log("xhr.onloadend (Success) xhr : " , xhr);
                console.log("[LoginFieldController ] xhr.onloadend (Success) response : " , JSON.parse(response.target.responseText));

                setTimeout(function(){
                    callback(response);
                }, 1000);

                //callback(response);
            }
        };
    }

    return {
        init : _init
    }
};