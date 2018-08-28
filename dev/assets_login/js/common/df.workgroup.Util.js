var date_format = require( '../bundler/node_modules/date-fns/format');
var isDate = require('../bundler/node_modules/date-fns/is_date');
var koLocale = require('../bundler/node_modules/date-fns/locale/en');

window.df = window.df || {};
window.df.workgroup = window.df.workgroup || {};

window.df.workgroup.Util = (function () {

    var now_date;

    function load_json(url, method, callback, $data) {

        now_date = new Date();
        console.log("\n----- << START LOAD >> xhr.url : ", url, "\n");

        var data = $data ? JSON.stringify($data) : null;

        var params = {
            method: method,
            action: url
        };

        // Construct an HTTP request
        var xhr = new XMLHttpRequest();
        xhr.open(params.method, params.action, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        //xhr.setRequestHeader('Accept', 'application/json; charset=UTF-8');
        //xhr.setRequestHeader('Content-Type', 'application/json; charset=UTF-8');

        xhr.onreadystatechange = function () {
            //console.log("xhr.readyState : ", xhr.readyState);
            //console.log("xhr.status : ", xhr.status);
        };

        // Callback function
        xhr.onloadend = function (response) {

            //console.log("xhr.onloadend : " , response);

            if (response.target.status === 0) {

                // Failed XmlHttpRequest should be considered an undefined error.
                console.log("xhr.onloadend (Failed) : ", xhr);

            } else if (response.target.status === 400) {

                // Bad Request
                console.log("xhr.onloadend (Bad Request) : ", xhr);
            } else if (response.target.status === 404) {

                // Bad Request
                console.log("xhr.onloadend (404 Not Found) : ", xhr);

            } else if (response.target.status === 200) {

                // Success
                console.log("\n----- << COMP LOAD >> ----- xhr.onloadend (Success) duration : ", (((new Date()).getTime() - now_date.getTime()) / 1000) + "sec\n\n");
                console.log("xhr.onloadend (Success) response : \n", response);
                console.log("xhr.onloadend (Success) responseText(JSON) : \n", JSON.parse(response.target.responseText));
                console.log("\n----- << COMP LOAD >> -----\n\n");
                //console.log("xhr.onloadend (Success) xhr : " , xhr);
                //console.log("xhr.onloadend (Success) response.target.responseText : " , JSON.parse(response.target.responseText));
                setTimeout(function () {
                    callback(response);
                }, 10);
            }
        };
        // Send the collected data as JSON
        xhr.send(data);
    }

    function addZeroNumber(num) {

        var str_num = "";
        if (num < 10) str_num = "0" + num;
        else str_num = "" + num;
        return str_num;
    }

    function addParamUniq(url) {

        if (url.indexOf('?') == -1) {
            url = url + "?uniq=" + new Date().getTime();
        } else {
            url = url + "&uniq=" + new Date().getTime();
        }
        return url;
    }

    function _getDate_format(date, format){

        // var MM_S = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
        // var MM = ['January', 'February ', 'March ', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        // var DW = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        // https://date-fns.org/v1.29.0/docs/format
        if(_getIsDate(date)) return date_format(date, format,
            {locale: koLocale});
        else return "XXXXXXXXXXX";
    }

    function _getIsDate(date){
        return isDate(date);
    }

    return {
        load_json: load_json,
        addZeroNumber: addZeroNumber,
        addParamUniq: addParamUniq,
        getDate_format: _getDate_format,
        getIsDate: _getIsDate
    }

})();