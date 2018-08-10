var LoginBgController = function(con_iframe, json_data){

    var con_iframe = con_iframe;
    var arr_bg_list = [];
    var dim;

    function init(){

        setArr_bgList(json_data);

        if(con_iframe) {
            dim = con_iframe.querySelector('.dim');
            //console.log("[ loadBgController.js ] : init - dim : ", dim);
        }

        //console.log("[ loadBgController.js ] : init - iframe : ", con_iframe);
        //console.log("[ loadBgController.js ] : init - json_data : ", json_data);
        //console.log("[ loadBgController.js ] : arr_bg_list: ", arr_bg_list);

        if(arr_bg_list.length > 0){

            var ran_index = Math.floor(arr_bg_list.length * Math.random());
            var url = arr_bg_list[ran_index].url;

            console.log("[ loadBgController.js ] : ",  "iframe url : ", url);

            set_iframe(url);
        }
    }

    function setArr_bgList(json) {
        if (json_data){
            arr_bg_list = arr_bg_list.concat(json.preset.bg_contents.weather.list);
            arr_bg_list = arr_bg_list.concat(json.preset.bg_contents.birthday.list);
            arr_bg_list = arr_bg_list.concat(json.preset.bg_contents.artwork.list);
            arr_bg_list = arr_bg_list.concat(json.preset.bg_contents.custom.list);
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
                    df.lab.Util.addClass(dim, window.df.workgroup.Preset.class_name.showIn);
                }
            }, 2000)
        }
    }

    return {
        init: init
    }

};