window.df = window.df || {};
window.df.workgroup = window.df.workgroup || {};

window.df.workgroup.Preset = {

    "json_url": {

        "default": "/json/df_info_data.json.php",
        "default_local": "assets_login/temp/df_info_data.json",

        "login": "assets_login/temp/df_info_data_01_login.json",
        "checkin": "assets_login/temp/df_info_data_02_checkin.json",
        "checkout": "assets_login/temp/df_info_data_03_checkout.json",
        "logout": "assets_login/temp/df_info_data_04_logout.json"
    },

    "document_url": {
        "approval": "approval/approval_to_list.php",
        "approval_my": "approval/approval_my_list.php",
        "approval_cc": "approval/approval_cc_list.php"
    },

    "main_url": "main.php",

    "eventType": {
        "ON_LOAD_JSON": "onLoadJson",
        "ON_LOGIN": "onLogin",
        "ON_CHECKIN": "onCheckin",
        "ON_CHECKOUT": "onCheckout",
        "ON_LOGOUT": "onLogout",
        "ON_CHANGE_STAGE_INFO": "onChangeStageInfo",
        "ON_ERROR": "onError",
        "ON_WARNING": "onWarning",
        "ON_CLOSE_MODAL": "onCloseModal"
    },

    "class_name": {
        "showIn": "show-in",
        "hideOut": "hide-out"
    },

    "related_site": [
        {
            "title": "designfever.com",
            "url": "http://designfever.com/",
            "thumb": "thumb_mail.jpg_200x200"
        },
        {
            "title": "Facebook",
            "url": "https://www.facebook.com/feverbook",
            "thumb": "thumb_mail.jpg_200x200"
        },
        {
            "title": "Twitter",
            "url": "https://twitter.com/designfever_kr",
            "thumb": "thumb_mail.jpg_200x200"
        },
        {
            "title": "Blog",
            "url": "https://blog.naver.com/design_fever",
            "thumb": "thumb_mail.jpg_200x200"
        },
        {
            "title": "DF Mail",
            "url": "http://mail.designfever.com/",
            "thumb": "thumb_mail.jpg_200x200"
        },
        {
            "title": "DF Lab",
            "url": "http://dev.designfever.com/lab/",
            "thumb": "thumb_mail.jpg_200x200"
        },
        {
            "title": "Youtube",
            "url": "https://www.youtube.com/user/designfeverda",
            "thumb": "thumb_mail.jpg_200x200"
        }
    ],
    "isBgTest": false
};