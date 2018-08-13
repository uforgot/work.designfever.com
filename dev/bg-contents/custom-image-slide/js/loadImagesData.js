window.df = window.df || {};
window.df.workgroup = window.df.workgroup || {};

window.df.workgroup.GlobalVars = {
    "isLoaded": false,
    "infoData": null
};

window.df.workgroup.Preset = {

    "json_url":{
        "default": "../../assets_login/temp/df_contents_image-slide.json"
    },

    "eventType": {
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

window.df.workgroup.imageslide = window.df.workgroup.imageslide || {};
window.df.workgroup.imageslide.LoadInfoData = (function(){

    var CLASS_NAME = "[ LoadInfoData ]";
    var url_json = window.df.workgroup.Preset.json_url.default + "?uniq=" + new Date().getTime();

    function init() {

        console.log(CLASS_NAME + " json_url : ", url_json);

        document.addEventListener(window.df.workgroup.Preset.eventType.ON_LOAD_JSON, onLoadJson);
        loadJSON(url_json, onLoad);
    }

    function loadJSON(url, callback) {

        var params = {
            method: "GET",
            action: url
        };

        var xhr = new XMLHttpRequest();
        xhr.overrideMimeType("application/json");
        xhr.open(params.method, params.action, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == "200") {
                callback(xhr.responseText);
            }
        };
        xhr.send(null);
    }

    function onLoad(response) {
        var actual_JSON = JSON.parse(response);
        window.df.workgroup.GlobalVars.isLoaded = true;
        window.df.workgroup.GlobalVars.infoData = actual_JSON;

        _dispatchOnLoad();
    }

    function _dispatchOnLoad(){
        var event = new CustomEvent(window.df.workgroup.Preset.eventType.ON_LOAD_JSON);
        document.dispatchEvent(event);
    }

    function onLoadJson(){
        console.log(CLASS_NAME + " onLoadJson - " , window.df.workgroup.GlobalVars.infoData);
        document.removeEventListener(window.df.workgroup.Preset.eventType.ON_LOAD_JSON, onLoadJson);
    }

    return {
        init: init
    }
})();


// window.df.workgroup.imageslide.LoadInfoData.init();
