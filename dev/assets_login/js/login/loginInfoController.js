var LoginInfoController = function(){

    var CLASS_NAME = "[ LoginInfoController ]";

    var _stage_clock = "";
    var _stage_notice = "";
    var _stage_birthday = "";

    var _stage_con = "";

    var _curIndex = 0;
    var _pos = {
        oX: 0,
        oY: 0,
        passX: 0,
        passY: 0,
    };

    var _isHasNotice = false;
    var _isHasBirthday = false;

    var _json_notice;
    var _json_birthday;


    function _init(json_notice, json_birthday){

        _stage_clock =  document.getElementById('id_stage_clock');
        _stage_notice =  document.getElementById('id_stage_notice');
        _stage_birthday =  document.getElementById('id_stage_birthday');
        _stage_con =  document.querySelector('section.sec-info');

        if(json_notice != undefined && json_notice != null && json_notice.title != undefined && json_notice.title != null && json_notice.title.length > 0){
            _json_notice = json_notice;
            _isHasNotice = true;

            setNotice();
            df.lab.Util.removeClass(_stage_notice, "hide");
        }else{
            df.lab.Util.addClass(_stage_notice, "hide");
        }

        if(json_birthday != undefined && json_birthday != null && json_birthday.length > 0){
            _json_birthday = json_birthday;
            _isHasBirthday = true;

            setBirthday();
            df.lab.Util.removeClass(_stage_birthday, "hide");
        }else{
            df.lab.Util.addClass(_stage_birthday, "hide");
        }

        if(_isHasBirthday || _isHasNotice) setTimeout(addEvent, 1500);
    }

    function setNotice(){

        var txt_con = _stage_notice.querySelector(".txt-notice");
        var inner = "";
        for(var i=0; i<_json_notice.title.length; i++){
            inner = inner + "<span>" + _json_notice.title[i] + "</span>";
        }
        txt_con.innerHTML = inner;

        var txt_con_sub = _stage_notice.querySelector(".txt-sub");
        inner = "";
        for(var i=0; i<_json_notice.dec.length; i++){
            inner = inner + "<span>" + _json_notice.dec[i] + "</span>";
        }
        txt_con_sub.innerHTML = inner;
    }

    function setBirthday(){

        var str_notice = ["ø¿¥√", "ª˝¿œ¿ª", "√‡«œ µÂ∑¡ø‰."];

        var inner = "";

        var txt_con = _stage_birthday.querySelector(".txt-notice");
        var tot = str_notice.length;
        for(var i=0; i<tot; i++){
            inner = inner + "<span>" + str_notice[i] + "</span>";
        }
        txt_con.innerHTML = inner;


        var txt_con_sub = _stage_birthday.querySelector(".txt-sub");
        tot = _json_birthday.length;
        inner = "";
        for(var i=0; i<tot; i++){

            if(i==0) inner = inner + "<span>";

            if(i == 0) inner = inner + _json_birthday[i].name + " " + _json_birthday[i].position + "¥‘";
            else if(i > 0) inner = inner + ", " + _json_birthday[i].name + " " + _json_birthday[i].position + "¥‘";

            if(i == tot-1) inner = inner + "</span>";
        }
        txt_con_sub.innerHTML = inner;
    }

    function addEvent(){

        if(Detectizr.device.type != "desktop"){
            _stage_con.addEventListener('touchstart',  onTouchStart_stage);
        }else{
            _stage_con.addEventListener('click',  onClick_stage);
        }

        //console.log("Detectizr.device.type : ", Detectizr.device.type);
        //console.log("Detectizr : ", Detectizr);

    }

    function onClick_stage($evt){
        $evt.preventDefault();

        if(_isHasNotice && _isHasBirthday) {
            if(_curIndex == 2) _showClock();
            else _nextStage();
        }else{
            if(_curIndex > 0) _showClock();
            else{
                if(_isHasNotice) _showNotice();
                else if(_isHasBirthday) _showBirthday();
            }
        }
    }

    function onTouchStart_stage($evt){

        console.log("onTouchStart_stage");

        //$evt.preventDefault();
        $evt.stopPropagation();

        _stage_con.addEventListener('touchmove',  onTouchMove_stage);
        document.addEventListener('touchend',  onTouchEnd_stage);

        var pointX = 0;
        var pointY = 0;

        if($evt.type == 'mousedown') {
            pointX = $evt.clientX;
            pointY = $evt.clientY;
        }else if($evt.type == 'touchstart') {
            if ( $evt.touches.length === 1 ) {
                pointX = $evt.touches[ 0 ].pageX;
                pointY = $evt.touches[ 0 ].pageY;
            }
        }

        _pos.oX = pointX;
        _pos.oY = pointY;
        _pos.passX = pointX;
        _pos.passY = pointY;

        //console.log(pointX);
    }

    function onTouchEnd_stage($evt){

        console.log("onTouchEnd_stage (document)");

        //$evt.preventDefault();
        $evt.stopPropagation();

        _stage_con.removeEventListener('touchmove',  onTouchMove_stage);
        document.removeEventListener('touchend',  onTouchEnd_stage);

        var oW = _stage_con.offsetWidth;

        var disX = _pos.passX - _pos.oX;
        var per = Math.abs(disX)/oW;

        if(per > 0.1){
            if(disX < 0) _nextStage();
            else         _prevStage();
        }

        //console.log(_pos.passX);
        //console.log(oW, disX, per, disX > 0);
    }

    function onTouchMove_stage($evt){
        //$evt.preventDefault();
        $evt.stopPropagation();
        //console.log("onTouchMove_stage");

        var pointX, pointY;

        if($evt.type == 'mousemove') {
            pointX = $evt.clientX;
            pointY = $evt.clientY;
        }else if($evt.type == 'touchmove') {
            if ( $evt.touches.length === 1 ) {
                pointX = $evt.touches[ 0 ].pageX;
                pointY = $evt.touches[ 0 ].pageY;
            }
        }

        _pos.passX = pointX;
        _pos.passY = pointY;
    }

    function _showClock(){
        _changeStage(0);
    }

    function _showNotice(){

        if(_isHasNotice) _changeStage(1);
    }

    function _showBirthday(){

        if(_isHasBirthday) _changeStage(2);
    }

    function _nextStage(){

        console.log("_nextStage : ",_curIndex,_isHasNotice,_isHasBirthday);

        var index = (_curIndex + 1);
        if(_isHasNotice && _isHasBirthday) {
            if(index < 3) _changeStage(index);
        }else {
            if(_curIndex == 0 && _isHasNotice) _showNotice();
            if(_curIndex == 0 && _isHasBirthday) _showBirthday();
        }
    }

    function _prevStage(){

        var index = (_curIndex - 1);
        if(_isHasNotice && _isHasBirthday) {
            if(index > -1) _changeStage(index);
        }else{
            _showClock();
        }
    }

    function _changeStage(index){

        if(_curIndex != index){

            _curIndex = index;

            switch (_curIndex) {
                case 1 :
                    setHide_clock(true);
                    setShow_notice();
                    setHide_birthday(false);
                    break;
                case 2 :
                    setHide_clock(true);
                    setHide_notice(true);
                    setShow_birthday();
                    break;
                default :
                    setShow_clock();
                    setHide_notice(false);
                    setHide_birthday(false);

                    break;
            }

            _dispatchEvent();
        }
    }

    function setShow_clock(){
        df.lab.Util.removeClass(_stage_clock, "out-left");
        df.lab.Util.removeClass(_stage_clock, "out-right");
        df.lab.Util.addClass(_stage_clock, window.df.workgroup.Preset.class_name.showIn);
    }

    function setHide_clock(isToLeft){

        df.lab.Util.removeClass(_stage_clock, window.df.workgroup.Preset.class_name.showIn);
        if(isToLeft) {
            df.lab.Util.removeClass(_stage_clock, "out-right");
            df.lab.Util.addClass(_stage_clock, "out-left");
        }else{
            df.lab.Util.addClass(_stage_clock, "out-right");
            df.lab.Util.removeClass(_stage_clock, "out-left");
        }
    }

    function setShow_notice(){
        df.lab.Util.removeClass(_stage_notice, "out-left");
        df.lab.Util.removeClass(_stage_notice, "out-right");
        df.lab.Util.addClass(_stage_notice, window.df.workgroup.Preset.class_name.showIn);
    }

    function setHide_notice(isToLeft){

        df.lab.Util.removeClass(_stage_notice, window.df.workgroup.Preset.class_name.showIn);
        if(isToLeft) {
            df.lab.Util.removeClass(_stage_notice, "out-right");
            df.lab.Util.addClass(_stage_notice, "out-left");
        }else{
            df.lab.Util.addClass(_stage_notice, "out-right");
            df.lab.Util.removeClass(_stage_notice, "out-left");
        }
    }

    function setShow_birthday(){
        df.lab.Util.removeClass(_stage_birthday, "out-left");
        df.lab.Util.removeClass(_stage_birthday, "out-right");
        df.lab.Util.addClass(_stage_birthday, window.df.workgroup.Preset.class_name.showIn);
    }

    function setHide_birthday(isToLeft){

        df.lab.Util.removeClass(_stage_birthday, window.df.workgroup.Preset.class_name.showIn);
        if(isToLeft) {
            df.lab.Util.removeClass(_stage_birthday, "out-right");
            df.lab.Util.addClass(_stage_birthday, "out-left");
        }else{
            df.lab.Util.addClass(_stage_birthday, "out-right");
            df.lab.Util.removeClass(_stage_birthday, "out-left");
        }
    }

    function _showNotice_auto() {
        if(_isHasNotice) setTimeout(_showNotice, 600);
    }

    function _dispatchEvent(){
        var event = new CustomEvent(window.df.workgroup.Preset.eventType.ON_CHANGE_STAGE_INFO, {
            detail: {
                curIndex: _curIndex
            }});
        document.dispatchEvent(event);
    }

    return {
        init: _init,
        showNotice: _showNotice_auto
    }
};