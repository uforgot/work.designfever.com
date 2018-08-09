var LoginDate = function(con, json_data){

    var CLASS_NAME = "[ LoginDate ]";

    var _con = con;

    var _el ={
        txt_MM:"",
        txt_DD:"",
        txt_DW:""
    };

    var _today = {
      MM:0,
      DD:0,
      DW:0
    };

    var _ID_TIMEOUT = '';

    function init(today){
        //console.log(CLASS_NAME + " container : ", _con);

        _el.txt_MM = document.getElementById("id_txt_MM");
        _el.txt_DD = document.getElementById("id_txt_DD");
        _el.txt_DW = document.getElementById("id_txt_DW");

        show();
        setDateTxt(today);
    }

    function show(){

        df.lab.Util.removeClass(_con, window.df.workgroup.Preset.class_name.showIn);

        clearTimeout(_ID_TIMEOUT);
        _ID_TIMEOUT = setTimeout(function(){
            df.lab.Util.addClass(_con, window.df.workgroup.Preset.class_name.showIn);
        }, 2000);
    }

    function _updateToday(today){
        if(_today.MM != today.MM || _today.DD != today.DD || _today.DW != today.DW){
            show();
            setDateTxt(today);
        }
    }

    function setDateTxt(today){
        //console.log(today);

        _today.MM = today.MM;
        _today.DD = today.DD;
        _today.DW = today.DW;

        var MM_S = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
        var MM = ['January', 'February ', 'March ', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        var DW = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        _el.txt_MM.textContent = MM[_today.MM];
        _el.txt_DD.textContent = _today.DD < 10 ? '0'+_today.DD : _today.DD;
        _el.txt_DW.textContent = DW[_today.DW];
    }

    return {
        init: init,
        updateToday: _updateToday
    }
};