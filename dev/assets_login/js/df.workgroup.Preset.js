window.df = window.df || {};
window.df.workgroup = window.df.workgroup || {};

window.df.workgroup.Preset = {

    "json_url":{
        "default": "http://dev3.designfever.com/assets_login/temp/df_info_data.json.php",
        "login": "http://dev3.designfever.com/assets_login/temp/df_info_data.json.php",
        "checkin": "http://dev3.designfever.com/assets_login/temp/df_info_data.json.php",
        "checkout": "http://dev3.designfever.com/assets_login/temp/df_info_data.json.php",
        "logout": "http://dev3.designfever.com/assets_login/temp/df_info_data.json.php",
    },

    "document_url":{
        "approval": "approval/approval_to_list.php",
        "approval_my": "approval/approval_my_list.php",
        "approval_cc": "approval/approval_cc_list.php"
    },

    "main_url":"main.php",

    "eventType": {
        "ON_LOAD_JSON": "onLoadJson"
    },

    "isBgTest": false
};