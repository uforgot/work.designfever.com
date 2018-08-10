var LoginInfoController = function(){

    var CLASS_NAME = "[ LoginInfoController ]";

    var _stage_clock = "";
    var _stage_notice = "";

    function _init(){
        _stage_clock =  document.getElementById('id_stage_clock');
        _stage_notice =  document.getElementById('id_stage_notice');

        setTimeout(addEvent, 1500);
    }

    function addEvent(){
        _stage_clock.addEventListener('click',  _onClick_stage_clock);
        _stage_clock.addEventListener('touchend',  _onTouchend_stage_clock);
        _stage_notice.addEventListener('click',  _onClick_stage_notice);
        _stage_notice.addEventListener('touchend',  _onTouchend_stage_notice);
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

        _showClock();
    }

    function _onTouchend_stage_notice($evt){
        $evt.preventDefault();
        console.log("_onTouchend_stage_notice");

        _showClock();
    }


    function _showClock(){
        df.lab.Util.removeClass(_stage_clock, "out-left");
        df.lab.Util.removeClass(_stage_clock, "out-right");
        df.lab.Util.addClass(_stage_clock, window.df.workgroup.Preset.class_name.showIn);

        df.lab.Util.removeClass(_stage_notice, "out-left");
        df.lab.Util.removeClass(_stage_notice, window.df.workgroup.Preset.class_name.showIn);
        df.lab.Util.addClass(_stage_notice, "out-right");
    }

    function _showNotice(){

        df.lab.Util.removeClass(_stage_clock, window.df.workgroup.Preset.class_name.showIn);
        df.lab.Util.removeClass(_stage_clock, "out-right");
        df.lab.Util.addClass(_stage_clock, "out-left");

        df.lab.Util.removeClass(_stage_notice, "out-right");
        df.lab.Util.removeClass(_stage_notice, "out-left");
        df.lab.Util.addClass(_stage_notice, window.df.workgroup.Preset.class_name.showIn);
    }

    function _showNotice_auto() {
        setTimeout(_showNotice, 600);
    }

        return {
        init: _init,
        showNotice: _showNotice_auto
    }
};