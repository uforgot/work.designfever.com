var LoginUtilController = function(){

    var sec_con = document.querySelector(".sec-util");

    var CLASS_NAME = "[ LoginUtilController ]";

    function _init(json_document_url, json_main_url, json_user){
        _setUrl(json_document_url, json_main_url);
        _setDocNum(json_user);
    }

    function _setDocNum(json_user){

        // console.log(CLASS_NAME + " " + "json_document_url : "  ,  json_document_url);
        // console.log(CLASS_NAME + " " + "json_main_url : "  ,  json_main_url);

        var num_doc_approval = document.getElementById("id_link_doc_approval").querySelector(".doc-num");
        var num_doc_approval_my = document.getElementById("id_link_doc_approval_my").querySelector(".doc-num");
        var num_doc_approval_cc = document.getElementById("id_link_doc_approval_cc").querySelector(".doc-num");

        if(json_user != undefined && json_user.isLoggedIn){


            if(json_user.document_approval_num > 0) num_doc_approval.textContent = json_user.document_approval_num + "";
            else                                    num_doc_approval.textContent = "0";

            if(json_user.document_approval_my_num > 0) num_doc_approval_my.textContent = json_user.document_approval_my_num + "";
            else                                    num_doc_approval_my.textContent = "0";

            if(json_user.document_approval_cc_num > 0) num_doc_approval_cc.textContent = json_user.document_approval_cc_num + "";
            else                                    num_doc_approval_cc.textContent = "0";

        }else{
            num_doc_approval.textContent = "0";
            num_doc_approval_my.textContent = "0";
            num_doc_approval_cc.textContent = "0";
        }
    }

    function _setUrl(json_document_url, json_main_url){

        // console.log(CLASS_NAME + " " + "json_document_url : "  ,  json_document_url);
        // console.log(CLASS_NAME + " " + "json_main_url : "  ,  json_main_url);

        var link_doc_approval = document.getElementById("id_link_doc_approval").querySelector("a.btn-link");
        var link_doc_approval_my = document.getElementById("id_link_doc_approval_my").querySelector("a.btn-link");
        var link_doc_approval_cc = document.getElementById("id_link_doc_approval_cc").querySelector("a.btn-link");
        var link_main = document.getElementById("id_link_main").querySelector("a.btn-link");


        if(json_document_url.approval != undefined && json_document_url.approval != ""){
            //console.log(json_document_url.approval);

            link_doc_approval.href = json_document_url.approval;
            df.lab.Util.addClass(link_doc_approval, "able");
        }else{
            link_doc_approval.href = "#";
            df.lab.Util.removeClass(link_doc_approval, "able");
        }

        if(json_document_url.approval_my != undefined && json_document_url.approval_my != ""){
            //console.log(json_document_url.approval_my);

            link_doc_approval_my.href = json_document_url.approval_my;
            df.lab.Util.addClass(link_doc_approval_my, "able");
        }else{
            link_doc_approval_my.href = "#";
            df.lab.Util.removeClass(link_doc_approval_my, "able");
        }

        if(json_document_url.approval_cc != undefined && json_document_url.approval_cc != ""){
            //console.log(json_document_url.approval_cc);

            link_doc_approval_cc.href = json_document_url.approval_cc;
            df.lab.Util.addClass(link_doc_approval_cc, "able");
        }else{
            link_doc_approval_cc.href = "#";
            df.lab.Util.removeClass(link_doc_approval_cc, "able");
        }

        if(json_main_url != undefined && json_main_url != ""){
            //console.log(json_main_url);

            link_main.href = json_main_url;
            df.lab.Util.addClass(link_main, "able");
        }else {
            link_main.href = "#";
            df.lab.Util.removeClass(link_main, "able");
        }
    }

    function _resetData(json_user){
        _setDocNum(json_user)
    }

    return {
        init: _init,
        resetData: _resetData
    }
};