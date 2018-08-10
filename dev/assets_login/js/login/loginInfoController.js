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

    function _init(){
        _stage_clock =  document.getElementById('id_stage_clock');
        _stage_notice =  document.getElementById('id_stage_notice');
        _stage_birthday =  document.getElementById('id_stage_birthday');
        _stage_con =  document.querySelector('section.sec-info');

        setTimeout(addEvent, 1500);
    }

    function addEvent(){
        // _stage_clock.addEventListener('click',  _onClick_stage_clock);
        // _stage_clock.addEventListener('touchend',  _onTouchend_stage_clock);
        // _stage_notice.addEventListener('click',  _onClick_stage_notice);
        // _stage_notice.addEventListener('touchend',  _onTouchend_stage_notice);
        // _stage_birthday.addEventListener('click',  _onClick_stage_birthday);
        // _stage_birthday.addEventListener('touchend',  _onTouchend_stage_birthday);

        _stage_con.addEventListener('click',  onClick_stage);
        if(Detectizr.device.type != "desktop"){
            _stage_con.addEventListener('touchstart',  onTouchStart_stage);
        }

        console.log("Detectizr.device.type : ", Detectizr.device.type);
        console.log("Detectizr : ", Detectizr);

    }

    function onClick_stage($evt){
        $evt.preventDefault();

        var index = (_curIndex + 1)%3;
        _changeStage(index);
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

    function _onClick_stage_clock($evt){
        $evt.preventDefault();
        console.log("_onClick_stage_clock");
        _showNotice();
    }

    function _onTouchend_stage_clock($evt){
        $evt.preventDefault();
        console.log("_onTouchend_stage_clock");
        _showNotice();
    }

    function _onClick_stage_notice($evt){
        $evt.preventDefault();
        console.log("_onClick_stage_notice");

        _showBirthday();
    }

    function _onTouchend_stage_notice($evt){
        $evt.preventDefault();
        console.log("_onTouchend_stage_notice");

        _showBirthday();
    }


    function _onClick_stage_birthday($evt){
        $evt.preventDefault();
        console.log("_onClick_stage_notice");

        _showClock();
    }

    function _onTouchend_stage_birthday($evt){
        $evt.preventDefault();
        console.log("_onTouchend_stage_notice");

        _showClock();
    }


    function _showClock(){
        _changeStage(0);
    }

    function _showNotice(){

        _changeStage(1);
    }

    function _showBirthday(){

        _changeStage(2);
    }

    function _nextStage(){

        var index = (_curIndex + 1);
        if(index < 3) _changeStage(index);
    }

    function _prevStage(){

        var index = (_curIndex - 1);
        if(index > -1) _changeStage(index);
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
        setTimeout(_showNotice, 600);
    }

        return {
        init: _init,
        showNotice: _showNotice_auto
    }
};