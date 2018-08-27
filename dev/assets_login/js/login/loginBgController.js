module.exports = function(con_iframe, json_data){

    var CLASS_NAME = "[ LoginBgController ]";

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

            console.log(CLASS_NAME + " : ",  "iframe url : ", ran_index , " / " , url);

            set_iframe(url);
        }
    }

    function setArr_bgList(json) {

        var params = df.lab.Util.getParams(); // browser params

        if (json_data){

            var json_test_bg = json.info.test.bg_contents;
            var json_today_bg = json.info.today.bg_contents;
            var json_birthday = json.info.birthday;

            if(json_test_bg != undefined && json_test_bg != null && json_test_bg.length > 0 && params.test == "true"){
                console.log(CLASS_NAME + " : ", "type : " , "test bg");
                arr_bg_list = arr_bg_list.concat(json_test_bg);

            }else if(json_today_bg != undefined && json_today_bg != null && json_today_bg.length > 0){

                console.log(CLASS_NAME + " : ", "type : " , "custom bg");
                arr_bg_list = arr_bg_list.concat(json_today_bg);

            }else if(json_birthday != undefined && json_birthday != null && json_birthday.length > 0){

                console.log(CLASS_NAME + " : ", "type : " , "Birthday bg");
                arr_bg_list = arr_bg_list.concat(json.preset.bg_contents.birthday.list);

            }else{

                console.log(CLASS_NAME + " : ", "type : " , "random bg");
                arr_bg_list = arr_bg_list.concat(json.preset.bg_contents.weather.list);
                arr_bg_list = arr_bg_list.concat(json.preset.bg_contents.artwork.list);
            }

            console.log(CLASS_NAME + " : ", "arr_bg_list : " , arr_bg_list);


            //arr_bg_list = arr_bg_list.concat(json.preset.bg_contents.weather.list);
            //arr_bg_list = arr_bg_list.concat(json.preset.bg_contents.birthday.list);
            //arr_bg_list = arr_bg_list.concat(json.preset.bg_contents.artwork.list);
            //arr_bg_list = arr_bg_list.concat(json.preset.bg_contents.custom.list);
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