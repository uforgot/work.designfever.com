var LoginUtilController = function(){

    var CLASS_NAME = "[ LoginUtilController ]";


    function _init(){
        _setUrl();
    }

    function _setUrl(){
        var json_data = window.df.workgroup.GlobalVars.infoData;
        if(json_data.preset != undefined &&
            json_data.preset.json_url != undefined &&
            json_data.preset.json_url.logout != undefined){

            //_form.action = json_data.preset.json_url.logout;
            //console.log(CLASS_NAME + " action(server) : ", _form.action);
        }else{
            //_form.action = window.df.workgroup.Preset.json_url.logout;
            //console.log(CLASS_NAME + " action(local) : ", _form.action);
        }
    }
    return {
        init: _init
    }
};