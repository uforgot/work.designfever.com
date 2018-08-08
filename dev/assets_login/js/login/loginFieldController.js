var LoginFieldController = function(){

    var KEYBOARD_ENTER = 13;
    var KEYBOARD_TAB = 9;
    var id, pwd;
    var storageId, storagePw;

    var _init = function(){
        _inputKeyController();
    };

    function _inputKeyController(){
        id = document.getElementById('user_id');
        pwd = document.getElementById('user_pw');

        id.addEventListener( 'keypress', _keypressId );
        //pwd.addEventListener( 'keypress', _keypressPwd );

        if(storageId == null || storageId == undefined){
            id.focus();
        }

        var frm = document.getElementById('id_login');
        frm.addEventListener( 'submit',  _onSubmit);
    }

    function _keypressId( $evt ) {
        switch( $evt.which ) {
            case KEYBOARD_ENTER :
                pwd.focus();
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
        var frm = document.getElementById('id_login');

         if( frm.user_id.value.length < 3 || frm.user_id.value.length > 16 ) {
         alert("아이디가 존재하지 않습니다.");
         frm.user_id.focus();
         return false;
         }

         if( frm.user_pw.value.length < 4 || frm.user_pw.value.length > 16) {
         alert("잘못된 패스워드입니다. (4-16자리 가능)");
         frm.user_pw.focus();
         return false;
         }

        _submit();
    }

    function _submit(){
        var frm = document.getElementById('id_login');

        console.log("action : ", frm.action);
        console.log("target : ", frm.target);

        //alert("action : " + frm.action + "\ntarget : " + frm.target);

        var btn = document.getElementById('user_pw');
        btn.blur();

        //frm.submit();
        loading();
        ajaxPost(frm, onSubmit);
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

        // Construct an HTTP request
        var xhr = new XMLHttpRequest();
        xhr.open(form.method, form.action, true);
        xhr.setRequestHeader('Accept', 'application/json; charset=utf-8');
        xhr.setRequestHeader('Content-Type', 'application/json; charset=UTF-8');

        // Send the collected data as JSON
        xhr.send(JSON.stringify(data));

        // Callback function
        xhr.onloadend = function (response) {

            console.log("xhr.onloadend : " , response);

            if (response.target.status === 0) {

                // Failed XmlHttpRequest should be considered an undefined error.
                console.log("xhr.onloadend (Failed) : " , xhr);

            } else if (response.target.status === 400) {

                // Bad Request
                console.log("xhr.onloadend (Bad Request) : " , xhr);
            } else if (response.target.status === 404) {

                // Bad Request
                console.log("xhr.onloadend (404 Not Found) : " , xhr);

            } else if (response.target.status === 200) {

                // Success
                //console.log("xhr.onloadend (Success) : form.dataset.formSuccess" , form.dataset.formSuccess);
                //console.log("xhr.onloadend (Success) response : " , response);
                //console.log("xhr.onloadend (Success) xhr : " , xhr);
                console.log("xhr.onloadend (Success) response : " , JSON.parse(response.target.responseText));

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