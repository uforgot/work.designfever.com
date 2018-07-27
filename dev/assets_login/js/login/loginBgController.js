

var loginBgController = function(con_iframe, json_data){

    var con_iframe = con_iframe;

    function init(){

        console.log("[ loadBgController.js ] : init - iframe : ", con_iframe);
        console.log("[ loadBgController.js ] : init - json_data : ", json_data);

        if(json_data){

            var url = json_data.info.today.bg_contents[0].url;

            console.log("[ loadBgController.js ] : ",  "loaded json : ", json_data);
            console.log("[ loadBgController.js ] : ",  "iframe url : ", url);

            set_iframe(url);

        }
    }

    function set_iframe(url){

        if(con_iframe) {
            var ifrm = document.createElement("iframe");
            ifrm.setAttribute("src", url);
            ifrm.setAttribute("name", "iframe-bg");
            con_iframe.appendChild(ifrm);
        }
    }

    return {
        init: init
    }

};