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
        disable_input();
    }

    function disable_input(){
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

    function able_input(){
        var sec_login = document.querySelector('.sec-login');
        df.lab.Util.removeClass(sec_login, 'loading');

        var inputs = sec_login.querySelectorAll('input');
        for (var i = 0; i < inputs.length; i++) {
            inputs[i].removeAttribute("disabled");
        }
    }

    function onSubmit(response){
        able_input();
        _dispatchOnLoad();
    }

    function _dispatchOnLoad(){
        var event = new CustomEvent(window.df.workgroup.Preset.eventType.ON_LOGIN);
        document.dispatchEvent(event);
    }

    function _hideLoginFrom(){

    }

    function _showLoginFrom(){

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
        init : _init,
        hideLoginFrom: _hideLoginFrom
    }
};