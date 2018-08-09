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

window.df = window.df || {};
window.df.workgroup = window.df.workgroup || {};
window.df.workgroup.login = window.df.workgroup.login || {};

window.df.workgroup.login.LoadInfoData = (function(){

    var CLASS_NAME = "[ LoadInfoData ]";
    var url_json = window.df.workgroup.Preset.json_url.default + "?uniq=" + new Date().getTime();

    function loadJSON(url, callback) {

        var params = {
            method: "GET",
            action: url
        };

        var xhr = new XMLHttpRequest();
        xhr.overrideMimeType("application/json");
        xhr.open(params.method, params.action, true);
        xhr.onreadystatechange = function () {

            //console.log(CLASS_NAME + " xhr.readyState : ", xhr.readyState);
            //console.log(CLASS_NAME + " xhr.status : ", xhr.status);

            if (xhr.readyState == 4 && xhr.status == "200") {
                // Required use of an anonymous callback as .open will NOT return a value but simply returns undefined in asynchronous mode
                callback(xhr.responseText);
            }

/*
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

            }*/

        };
        xhr.send(null);
    }

    function init() {

        console.log(CLASS_NAME + " json_url : ", url_json);

        document.addEventListener(window.df.workgroup.Preset.eventType.ON_LOAD_JSON, onLoadJson);

        loadJSON(url_json, function(response) {
            // Parse JSON string into object
            var actual_JSON = JSON.parse(response);

            //console.log(CLASS_NAME + " ", actual_JSON);

            window.df.workgroup.GlobalVars.isLoaded = true;
            window.df.workgroup.GlobalVars.infoData = actual_JSON;

            var event = new CustomEvent(window.df.workgroup.Preset.eventType.ON_LOAD_JSON);
            document.dispatchEvent(event);
        });
    }

    function onLoadJson(){
        console.log(CLASS_NAME + " onLoadJson - " , window.df.workgroup.GlobalVars.infoData);
        document.removeEventListener(window.df.workgroup.Preset.eventType.ON_LOAD_JSON, onLoadJson);
    }

    return {
        init: init
    }
})();


window.df.workgroup.login.LoadInfoData.init();
