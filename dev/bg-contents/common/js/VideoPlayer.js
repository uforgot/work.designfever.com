var VideoPlayer = function(defaultData, lowData){

    var videoData = defaultData;
    var videoLowData = lowData;

    var isMobile = document.querySelector("html").classList.contains("mobile");

    var _setting = function(){
        _setElement();
    };

    var _setElement = function(){

        var ranNum = parseInt(Math.random() * videoData.length);
        var defaultData = videoData[ranNum];
        var lowData = videoLowData || [];

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