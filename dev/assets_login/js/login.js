var Login = (function(){



    var container_iframe = document.querySelector("#bg_frame iframe");
    var container_clock = document.getElementById("id_container_clock");


    var _clock = new DF_Clock(container_clock);
    var _bgControll = new loginBgController(container_iframe);

    function _init(){

        _bgControll.init();
        _clock.init();

        LoginFieldController.init();
    }

    return {
        init: _init
    }
})();