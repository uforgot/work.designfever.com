var date_format = require( '../bundler/node_modules/date-fns/format');

module.exports = function (con, json_data) {

    var CLASS_NAME = "[ LoginDate ]";

    var _con = con;

    var _el = {
        txt_MM: "",
        txt_DD: "",
        txt_DW: ""
    };

    var _today = {
        MM: "",
        DD: "",
        DW: ""
    };

    var _ID_TIMEOUT = '';

    function init(date) {
        //console.log(CLASS_NAME + " container : ", _con);

        _el.txt_MM = document.getElementById("id_txt_MM");
        _el.txt_DD = document.getElementById("id_txt_DD");
        _el.txt_DW = document.getElementById("id_txt_DW");

        _updateToday(date);
    }

    function show() {

        df.lab.Util.removeClass(_con, window.df.workgroup.Preset.class_name.showIn);

        clearTimeout(_ID_TIMEOUT);
        _ID_TIMEOUT = setTimeout(function () {
            df.lab.Util.addClass(_con, window.df.workgroup.Preset.class_name.showIn);
        }, 1000);
    }

    function _updateToday(date) {

        var MM = window.df.workgroup.Util.getDate_format(date,'MMMM');
        var DD = window.df.workgroup.Util.getDate_format(date,'DD');
        var DW = window.df.workgroup.Util.getDate_format(date,'dddd');

        if (_today.MM != MM || _today.DD != DD || _today.DW != DW) {

            _today.MM = MM;
            _today.DD = DD;
            _today.DW = DW;

            show();
            setDateTxt();
        }
    }

    function setDateTxt() {

        _el.txt_MM.textContent = _today.MM;
        _el.txt_DD.textContent = _today.DD;
        _el.txt_DW.textContent = _today.DW;
    }

    return {
        init: init,
        updateToday: _updateToday
    }
};