var GlobalVars = {};

(function(){
    var url_json = "assets_login/temp/df_info_data.json" + "?uniq=" + new Date().getTime();

    function loadJSON(url, callback) {

        var xobj = new XMLHttpRequest();
        xobj.overrideMimeType("application/json");
        xobj.open('GET', url, true);
        xobj.onreadystatechange = function () {
            if (xobj.readyState == 4 && xobj.status == "200") {
                // Required use of an anonymous callback as .open will NOT return a value but simply returns undefined in asynchronous mode
                callback(xobj.responseText);
            }
        };
        xobj.send(null);
    }

    function init() {

        console.log("[ loadInfoData.js ]");

        loadJSON(url_json, function(response) {
            // Parse JSON string into object
            var actual_JSON = JSON.parse(response);

            console.log("[ loadInfoData.js ] : ", "json_url : ", url_json);
            console.log("[ loadInfoData.js ] : ", actual_JSON);

            GlobalVars.infoData = actual_JSON;
        });
    }

    init();

})();