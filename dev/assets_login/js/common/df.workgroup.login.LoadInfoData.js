(function () {
    if (typeof window.CustomEvent === "function") return false; //If not IE

    function CustomEvent(event, params) {
        params = params || {bubbles: false, cancelable: false, detail: undefined};
        var evt = document.createEvent('CustomEvent');
        evt.initCustomEvent(event, params.bubbles, params.cancelable, params.detail);
        return evt;
    }

    CustomEvent.prototype = window.Event.prototype;

    window.CustomEvent = CustomEvent;
})();

window.df = window.df || {};
window.df.workgroup = window.df.workgroup || {};
window.df.workgroup.login = window.df.workgroup.login || {};

window.df.workgroup.login.LoadInfoData = (function () {

    var CLASS_NAME = "[ LoadInfoData ]";
    var url_json = "";

    function init() {

        var _isLocalhost = false;
        if (window.location.hostname === "localhost" || window.location.hostname === "127.0.0.1") {
            _isLocalhost = true;
            url_json = window.df.workgroup.Preset.json_url.default_local;
        } else {
            _isLocalhost = false;
            url_json = window.df.workgroup.Preset.json_url.default;
        }

        //console.log(CLASS_NAME + " _isLocalhost : ", _isLocalhost + " / json_url : ", url_json);
        console.log(CLASS_NAME, " load default json data");
        document.addEventListener(window.df.workgroup.Preset.eventType.ON_LOAD_JSON, onLoadJson);
        loadJSON(url_json, onLoad);
    }

    function loadJSON(url, callback) {

        var params = {
            method: "GET",
            action: df.workgroup.Util.addParamUniq(url)
        };

        df.workgroup.Util.load_json(params.action, params.method, callback, null);
    }

    function onLoad(response) {
        var actual_JSON = JSON.parse(response.target.responseText);
        window.df.workgroup.GlobalVars.isLoaded = true;
        window.df.workgroup.GlobalVars.infoData = actual_JSON;


        var isLoggedIn = false;
        var json_data = window.df.workgroup.GlobalVars.infoData;
        if (json_data.user != undefined &&
            json_data.user.isLoggedIn != undefined) {

            if (json_data.user.isLoggedIn || json_data.user.isLoggedIn == "true") {
                isLoggedIn = true;
            }
        }

        // if is desktop

        var el_html = document.querySelector('html');
        var isDesktop = window.df.lab.Util.hasClass(el_html, 'desktop');

        if (isLoggedIn) {

            if (isDesktop && Detectizr.device.type == "desktop") {
                //redirectToMain();
                //return;

            } else if (json_data.user.isAdminAccount) {
                //redirectToMain();
                //return;
            }
        }

        // else -

        _dispatchOnLoad();
    }

    function redirectToMain() {

        var url = "";
        var json_data = window.df.workgroup.GlobalVars.infoData;
        if (json_data.preset != undefined &&
            json_data.preset.main_url != undefined) {

            url = json_data.preset.main_url;
            //console.log(CLASS_NAME + " go to main url (get server) : ", url);
        } else {
            url = window.df.workgroup.Preset.main_url;
            //console.log(CLASS_NAME + " go to main url (get local) : ", url);
        }
        console.log(CLASS_NAME + " go to main url : ", url);
        window.location.href = url;
        return;
    }

    function _dispatchOnLoad() {
        var event = new CustomEvent(window.df.workgroup.Preset.eventType.ON_LOAD_JSON);
        document.dispatchEvent(event);
    }

    function onLoadJson() {
        //console.log(CLASS_NAME + " onLoadJson - " , window.df.workgroup.GlobalVars.infoData);
        document.removeEventListener(window.df.workgroup.Preset.eventType.ON_LOAD_JSON, onLoadJson);
    }

    return {
        init: init
    }
})();


window.df.workgroup.login.LoadInfoData.init();
