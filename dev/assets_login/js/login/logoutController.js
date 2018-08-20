var LogoutController = function(){

    var CLASS_NAME = "[ LogoutController ]";
    var _form = document.getElementById('id_form_logout');

    function _init(){
        _setUrl();
        _form.addEventListener( 'submit',  _onSubmit);
    }

    function _setUrl(){
        var json_data = window.df.workgroup.GlobalVars.infoData;
        if(json_data.preset != undefined &&
            json_data.preset.json_url != undefined &&
            json_data.preset.json_url.logout != undefined){

            _form.action = json_data.preset.json_url.logout;
            //console.log(CLASS_NAME + " action(server) : ", _form.action);
        }else{
            _form.action = window.df.workgroup.Preset.json_url.logout;
            //console.log(CLASS_NAME + " action(local) : ", _form.action);
        }
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

    function loading(){
        disable_input();

    }

    function disable_input(){

        var inputs = _form.querySelectorAll('input');
        for (var i = 0; i < inputs.length; i++) {
            inputs[i].setAttribute("disabled", "");
            df.lab.Util.addClass(inputs[i],"disable");
        }
    }

    function able_input(){
        var inputs = _form.querySelectorAll('input');
        for (var i = 0; i < inputs.length; i++) {
            inputs[i].removeAttribute("disabled");
            df.lab.Util.removeClass(inputs[i],"disable");
        }
    }

    function onSubmit(response){
        //console.log(response);
        _dispatchOnLoad(response);
        setTimeout(function(){    window.location.reload (true);}, 100);
    }

    function _showLogoutBtn(){

        able_input();
        _setUrl();

        var btn_logout = document.querySelector('header .wrapper-logout');
        df.lab.Util.addClass(btn_logout, window.df.workgroup.Preset.class_name.showIn);
    }

    function _hideLogoutBtn(){

        disable_input();
        var btn_logout = document.querySelector('header .wrapper-logout');
        df.lab.Util.removeClass(btn_logout, window.df.workgroup.Preset.class_name.showIn);
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

    function _dispatchOnLoad(response){
        var event = new CustomEvent(window.df.workgroup.Preset.eventType.ON_LOGOUT, {
            detail: {
                response: response
            }});
        document.dispatchEvent(event);
    }

    return {
        init: _init,
        showLogoutBtn: _showLogoutBtn,
        hideLogoutBtn: _hideLogoutBtn
    }
};