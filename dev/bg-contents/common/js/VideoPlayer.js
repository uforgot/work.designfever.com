var VideoPlayer = function(dataObj){

    var videoData = dataObj.videodata;
    var videoLowData = dataObj.videodata_low || [];
    var videoIndex = dataObj.videoindex || parseInt(Math.random() * videoData.length);

    var isMobile = document.querySelector("html").classList.contains("mobile");

    var _setting = function(){

        _setElement();

    };

    var _setElement = function(){

        var defaultData = videoData[videoIndex] || videoData[0];
        var lowData = videoLowData[videoIndex] || videoData[0];

        if(isMobile && lowData) defaultData = lowData;

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