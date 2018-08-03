

var loginBgController = function(con_iframe, json_data){

    var con_iframe = con_iframe;

    var dim;

    function init(){

        if(con_iframe) {
            dim = con_iframe.querySelector('.dim');
            console.log("[ loadBgController.js ] : init - dim : ", dim);
        }

        console.log("[ loadBgController.js ] : init - iframe : ", con_iframe);
        console.log("[ loadBgController.js ] : init - json_data : ", json_data);

        if(json_data){

            //var ran_index = 0;//Math.floor(json_data.info.today.bg_contents.length * Math.random());
            //var url = json_data.info.today.bg_contents[ran_index].url;

            var ran_index = Math.floor(json_data.info.test.bg_contents.length * Math.random());
            var url = json_data.info.test.bg_contents[ran_index].url;

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

            setTimeout(function(){
                if(dim){
                    df.lab.Util.addClass(dim, "show");
                }
            }, 2000)
        }
    }

    return {
        init: init
    }

};