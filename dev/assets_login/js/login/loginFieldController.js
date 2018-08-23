var LoginFieldController = function(){

    var KEYBOARD_ENTER = 13;
    var KEYBOARD_TAB = 9;
    var input_user_id, input_user_pw;
    var storageId, storagePw;

    var CLASS_NAME = "[ LoginFieldController ]";
    var _form = document.getElementById('id_login');

    function _init(){

        input_user_id = document.getElementById('user_id');
        input_user_pw = document.getElementById('user_pw');

        _setUrl();

        _addEvent();
        //setFocus_id();
        setTimeout(function(){
             setFocus_id();
         }, 500);
    }

    function _setUrl(){
        var json_data = window.df.workgroup.GlobalVars.infoData;
        if(json_data.preset != undefined &&
            json_data.preset.json_url != undefined &&
            json_data.preset.json_url.login != undefined){

            _form.action = json_data.preset.json_url.login;
            //console.log(CLASS_NAME + " action(server) : ", _form.action);
        }else{
            _form.action = window.df.workgroup.Preset.json_url.login;
            //console.log(CLASS_NAME + " action(local) : ", _form.action);
        }
    }

    function _addEvent(){
        input_user_id.addEventListener( 'keypress', _keypressId );
        //input_user_pw.addEventListener( 'keypress', _keypressPwd );

        _form.addEventListener( 'submit',  _onSubmit);
    }

    function setFocus_id(){

        if(storageId == undefined || storageId == null){
            input_user_id.focus();
            input_user_id.select();
        }
    }

    function setFocus_pw(){
        if(storagePw == undefined || storagePw == null){
            input_user_pw.focus();
            input_user_pw.select();
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
        _loginCheck();
    }

    function _loginCheck() {

         if( _form.user_id.value.length < 3 || _form.user_id.value.length > 16 ) {
         //alert("아이디가 존재하지 않습니다.");
         _form.user_id.focus();
         return false;
         }

         if( _form.user_pw.value.length < 4 || _form.user_pw.value.length > 16) {
         //alert("잘못된 패스워드입니다. (4-16자리 가능)");
         _form.user_pw.focus();
         return false;
         }

        _submit();
    }

    function _submit(){

        console.log(CLASS_NAME, " load json" );

        var btn = document.getElementById('user_pw');
        btn.blur();

        //_form.submit();
        loading();
        ajaxPost(_form, onCompSubmit);
        return false;
    }

    function loading(){
        disable_input();
    }

    function disable_input(){
        var sec_login = document.querySelector('.sec-login');
        df.lab.Util.addClass(sec_login, 'loading');

        var inputs = _form.querySelectorAll('input');
        for (var i = 0; i < inputs.length; i++) {
            inputs[i].setAttribute("disabled", "");
        }
    }

    function able_input(){
        var sec_login = document.querySelector('.sec-login');
        df.lab.Util.removeClass(sec_login, 'loading');

        var inputs = _form.querySelectorAll('input');
        for (var i = 0; i < inputs.length; i++) {
            inputs[i].removeAttribute("disabled");
        }
    }

    function onCompSubmit(response){
        able_input();
        _dispatchOnLoad(response);

        var status = getStatus(response);

        if(status.isWarning) {
            //console.log("status.text : " , status.text);
            _dispatchOnWarning(status.text);

            if(status.code == "L01" || status.code == "L03") {
                document.addEventListener(window.df.workgroup.Preset.eventType.ON_CLOSE_MODAL, _onClose_modal_forID);
            }else if(status.code == "L02") {
                document.addEventListener(window.df.workgroup.Preset.eventType.ON_CLOSE_MODAL, _onClose_modal_forPW);
            }
        }
    }

    function getStatus(response){

        var status = {
            isWarning : false,
            text: "표시할 메세지가 없습니다.",
            code: null
        };
        var json = JSON.parse(response.target.responseText);
        var user_status_code = json.user.status;
        if(
            user_status_code.toLowerCase() == ("L01").toLowerCase() ||
            user_status_code.toLowerCase() == ("L02").toLowerCase() ||
            user_status_code.toLowerCase() == ("L03").toLowerCase()
        ){
            var list = json.preset.status_list;
            for(var i=0; i<list.length; i++){
                var item =  list[i];
                var code = item.code;

                if(code.toLowerCase() == user_status_code.toLowerCase()){
                    status.isWarning = true;
                    status.text = item.text;
                    status.code = item.code;
                    break;
                }
            }
        }
        return status;
    }

    function _onClose_modal_forID(){
        document.removeEventListener(window.df.workgroup.Preset.eventType.ON_CLOSE_MODAL, _onClose_modal_forID);
        setFocus_id();
    }

    function _onClose_modal_forPW(){
        document.removeEventListener(window.df.workgroup.Preset.eventType.ON_CLOSE_MODAL, _onClose_modal_forPW);
        setFocus_pw();
    }

    function _dispatchOnWarning(txt){
        var event = new CustomEvent(window.df.workgroup.Preset.eventType.ON_WARNING, {
            detail: {
                message: txt
            }});
        document.dispatchEvent(event);
    }


    function _dispatchOnLoad(response){
        var event = new CustomEvent(window.df.workgroup.Preset.eventType.ON_LOGIN, {
            detail: {
                response: response
            }});
        document.dispatchEvent(event);
    }

    function _hideLoginFrom(){
        disable_input();
    }

    function _showLoginFrom(){
        able_input();
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

        df.workgroup.Util.load_json(params.action, params.method, callback, data);
    }

    return {
        init : _init,
        hideLoginFrom: _hideLoginFrom,
        showLoginFrom: _showLoginFrom
    }
};