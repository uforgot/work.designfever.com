var Login = (function(){

    var container_clock = document.getElementById("id_container_clock");
    var _clock = new DF_Clock(container_clock);
    function _init(){
        LoginFieldController.init();

        _clock.init();
    }

    return {
        init: _init
    }
})();