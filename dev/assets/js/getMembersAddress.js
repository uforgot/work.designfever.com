
var getMembersAddress = (function(){
    var isMobile = false;
    var url;// = "person_addr.json";
    var data_address = {};

    var el_container;

    function _init($url, $obj){

        url = $url;

        el_container = $obj;
        
        loadAddressList();

        checkDevice();
    }

    function loadAddressList(){

        $.ajax({
            type: 'get',
            dataType: "json",
            url: url,
            data: data_address,
            success: complete_load
        });
    }

    function complete_load(data) {
        data_address = data.address;
        setting_layout();
    }

    function setting_layout() {

        var num_group = data_address.length;

        for (var i=0; i<num_group; i++) {

            var obj_group = data_address[i];
            var obj_members = obj_group.members;

            var num_members = obj_members.length;

            var el_group = $("<ul>");
            var el_groupName = $("<p>").text(obj_group.group_name);
            $(el_group).append(el_groupName);
            el_groupName.addClass("group-name");

            for (var j=0; j<num_members; j++) {
                var obj_member = obj_members[j];

                // df members name
                var obj_member_name = "<p class='name'><span class='member-name'>"+obj_member.name+"</span><span class='member-position'>"+ obj_member.position +"</span></p>";

                // df members phone number
                var obj_member_tel;
                if(!isMobile) { obj_member_tel = "<p class='tel-phone'><span>"+ obj_member.tel + "</span></p>"; }
                else { obj_member_tel = "<p class='m-tel-phone'><a href='tel:"+ obj_member.tel + "'>" + obj_member.tel + "</a></p>"; }

                // df members email address // web & mobile
                var obj_member_email = "<p class='web-email'><a href='mailto:" + obj_member.mail_addr + "'>" + obj_member.mail_addr + "</a>"+"</p>";
                var m_email_icon = "<p class='m-email'><a class='m_icon_email' href='mailto:" + obj_member.mail_addr + "'>이메일아이콘</a></p>";

                // df members extension tel number
                var obj_member_ext_tel;
                if (!isMobile) {
                    obj_member_ext_tel = "<p class='tel-ext'><span>"+ obj_member.ext_tel + "</span></p>";
                    if (obj_member.ext_tel == undefined) { obj_member_ext_tel = "<p class='tel-ext'><span></span></p>"; }
                }
                else {
                    obj_member_ext_tel = "<p class='m-tel-ext'><a href='tel:"+ obj_member.ext_tel + "'>" + obj_member.ext_tel + "</a></p>";
                }

                var obj_member_drc_tel;
                if(obj_member.direct_tel == undefined) {
                    obj_member_drc_tel = "<p class='tel-drc'><span></span></p>";
                } else {
                    obj_member_drc_tel = "<p class='tel-drc'><span>"+ obj_member.direct_tel + "</span></p>";
                }

                // var m_email_icon = "<p class='m-email'><a href='#' class='m-email-icon' onclick='m_showEmailAddr(this);return false;'>이메일아이콘</a></p>";

                $(el_group).append("<li>" + obj_member_name + "  " + obj_member_tel + "  " + m_email_icon + " " + obj_member_email + " " + obj_member_ext_tel + " " + obj_member_drc_tel + "</li>");
            }

            el_container.append(el_group);
        }
    }

    function checkDevice() {
        var browserW = $(window).width();
        var minBroserW = 767;

        if(navigator.userAgent.match(/Android|Mobile|iP(hone|od|ad)|BlackBerry|IEMobile|Kindle|NetFront|Silk-Accelerated|(hpw|web)OS|Fennec|Minimo|Opera M(obi|ini)|Blazer|Dolfin|Dolphin|Skyfire|Zune/)){
            //console.log("VERSION : MOBILE");
            if(isMobile == false && browserW < minBroserW) {

            }
            isMobile = true;
        }

        else {
            //console.log("VERSION : PC");
            isMobile = false;
        }
    }


    return {
        init:_init
    }

})();