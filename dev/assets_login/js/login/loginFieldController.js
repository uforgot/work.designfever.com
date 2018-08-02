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
        pwd.addEventListener( 'keypress', _keypressPwd );

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

         if( frm.user_id.value.length < 4 || frm.user_id.value.length > 16 ) {
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

        alert("action : " + frm.action + "\ntarget : " + frm.target);

        frm.submit();

        return false;
    }

    return {
        init : _init
    }
};