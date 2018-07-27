

var loginBgController = function(con_iframe){

    var iframe = con_iframe;

    function init(){

        console.log("[ loadBgController.js ] : init - iframe : ", iframe);

        if(GlobalVars.infoData){

            var url = GlobalVars.infoData.info.today.bg_contents[0].url;

            console.log("[ loadBgController.js ] : ",  "loaded json : ", GlobalVars.infoData);
            console.log("[ loadBgController.js ] : ",  "iframe url : ", url);

            //set_iframe(url);

        }
    }

    function set_iframe(url){
        if(iframe) {
            iframe.src = url;
        }
    }

    return {
        init: init
    }

};