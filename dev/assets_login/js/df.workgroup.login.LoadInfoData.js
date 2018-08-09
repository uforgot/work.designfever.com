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

            //console.log(CLASS_NAME + " xhr.readyState : ", xhr.readyState);
            //console.log(CLASS_NAME + " xhr.status : ", xhr.status);

            if (xhr.readyState == 4 && xhr.status == "200") {
                // Required use of an anonymous callback as .open will NOT return a value but simply returns undefined in asynchronous mode

                console.log(CLASS_NAME + " xhr : " , xhr);
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

    function onLoad(response) {
        var actual_JSON = JSON.parse(response);
        window.df.workgroup.GlobalVars.isLoaded = true;
        window.df.workgroup.GlobalVars.infoData = actual_JSON;

        var el_html = document.querySelector('html');
        var isDesktop = window.df.lab.Util.hasClass(el_html, 'desktop');

        var isLoggedIn = false;
        var json_data = window.df.workgroup.GlobalVars.infoData;
        if(json_data.user != undefined &&
            json_data.user.isLoggedIn != undefined ){

            if(json_data.user.isLoggedIn || json_data.user.isLoggedIn == "true") {
                isLoggedIn = true;
            }
        }

        console.log(CLASS_NAME + " isDesktop : " , isDesktop, " / isLoggedIn : ", isLoggedIn);

        if(isDesktop && isLoggedIn){
            redirectToMain();
            //return;
        }

        dispatchOnLoad();
    }

    function dispatchOnLoad(){
        var event = new CustomEvent(window.df.workgroup.Preset.eventType.ON_LOAD_JSON);
        document.dispatchEvent(event);
    }

    function onLoadJson(){
        console.log(CLASS_NAME + " onLoadJson - " , window.df.workgroup.GlobalVars.infoData);
        document.removeEventListener(window.df.workgroup.Preset.eventType.ON_LOAD_JSON, onLoadJson);
    }

    function redirectToMain(){

        var url = "";
        var json_data = window.df.workgroup.GlobalVars.infoData;
        if(json_data.preset != undefined &&
            json_data.preset.main_url != undefined){

            url = json_data.preset.main_url;
            console.log(CLASS_NAME + " go to main url (get server) : ", url);
        }else{
            url = window.df.workgroup.Preset.main_url;
            console.log(CLASS_NAME + " go to main url (get local) : ", url);
        }

        //window.location.href = url;
        return;
    }

    return {
        init: init
    }
})();


window.df.workgroup.login.LoadInfoData.init();
