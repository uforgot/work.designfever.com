var GlobalVars = {
    isLoaded: false,
    eventType: {
        "ON_LOAD_JSON": "onLoadJson"
    }
};

(function () {
    if ( typeof window.CustomEvent === "function" ) return false; //If not IE

    function CustomEvent ( event, params ) {
        params = params || { bubbles: false, cancelable: false, detail: undefined };
        var evt = document.createEvent( 'CustomEvent' );
        evt.initCustomEvent( event, params.bubbles, params.cancelable, params.detail );
        return evt;
    }

    CustomEvent.prototype = window.Event.prototype;

    window.CustomEvent = CustomEvent;
})();

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

        console.log("[ loadInfoData.js ] : ", "json_url : ", url_json);

        document.addEventListener(GlobalVars.eventType.ON_LOAD_JSON, onLoadJson);

        loadJSON(url_json, function(response) {
            // Parse JSON string into object
            var actual_JSON = JSON.parse(response);

            //console.log("[ loadInfoData.js ] : ", actual_JSON);

            GlobalVars.isLoaded = true;
            GlobalVars.infoData = actual_JSON;

            var event = new CustomEvent(GlobalVars.eventType.ON_LOAD_JSON);
            document.dispatchEvent(event);
        });
    }

    function onLoadJson(){
        console.log("[ loadInfoData.js ] : ", "onLoadJson");
    }

    init();

})();
