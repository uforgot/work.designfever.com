var VideoPlayer = function(data){

    var videoData = data;
    var isMobile = document.querySelector("html").classList.contains("mobile");

    var _setting = function(){
        _setElement(videoData);
    };

    var _setElement = function(videoData){

        var ranNum = parseInt(Math.random() * videoData.default.length);
        var defaultData = videoData.default[ranNum];
        var lowData = videoData.low || [];

        if(isMobile && lowData.length) defaultData = lowData;

        var video = document.createElement('video');
        video.src = defaultData;
        video.autoplay = true;
        video.muted = true;
        video.loop = true;

        var container = document.querySelector(".video-wrapper");
        container.prepend(video);

    };


    var _init = function(){

    }

    return {
        setting : _setting,
        init: _init
    }
};