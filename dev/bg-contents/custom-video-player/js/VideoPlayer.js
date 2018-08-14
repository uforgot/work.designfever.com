var VideoPlayer = function(jsondData, dimmedOpacity){
    var json = jsondData;
    var videoData = json.items;

    var video_url = videoData[0].video_url;
    var video_low_url = videoData[0].video_low_url;

    var image_url = json.bg_thumb;
    var only_video = json.only_video_play == undefined ? true : json.only_video_play;
    var dimmedOpacity = dimmedOpacity || 0.4;//dataObj.dimmed_opacity == undefined ? 0.4 : dataObj.dimmed_opacity;

    var _html = document.querySelector("html");
    var _isMobile = _html.classList.contains("mobile");

    var _init = function(){
        _setElement(!only_video ? _checkPlayType() : true);
        _makeDimmed();
    };

    var _setElement = function(isVideoAble){
        if(isVideoAble){
            // play video
            var video = document.querySelector("video");
            video.style.opacity = 1;

            var source = document.querySelector("video source");
            source.src = _isMobile && video_low_url ? video_low_url : video_url;
            // video.crossOrigin = 'anonymous';

            video.load();
            if(video) video.play();

            video.oncanplay = function() {
                // console.log("oncanplay")
                video.play();
            };



        } else {
            //show image
            var wrapper = document.querySelector(".video-player .video-wrapper");
            wrapper.innerHTML = "";
            wrapper.style.backgroundImage = "url("+image_url+")";

        }
    };


    var _checkPlayType = function(){
        var isVideoAble = false;


        var connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection || navigator.msConnection;

        if(_isMobile){
            var isAndroid = _html.classList.contains("android");
            if(isAndroid){
                // andriod && wifi

                // if (connection && connection.type === 'wifi') {
                if (connection && connection.type != 'cellular') {
                    isVideoAble = true;
                }
            }

        } else {
            isVideoAble = true;
        }

        if(isVideoAble){
            var supportVideo = supportsVideoType("h264");
            isVideoAble = supportVideo == "probably" ? isVideoAble : false;
        }
        return isVideoAble;
    };

    var _makeDimmed = function(){
        var alpha = dimmedOpacity;
        if(alpha == 0) return;

        var dimmed = document.createElement("div");
        dimmed.classList.add("dimmed");
        dimmed.style.backgroundColor = "rgba(0,0,0,"+alpha+")";

        var container = document.querySelector(".video-wrapper");
        container.appendChild(dimmed);
    };


    var supportsVideoType = function(type) {
        let video;

        // Allow user to create shortcuts, i.e. just "webm"
        let formats = {
            ogg: 'video/ogg; codecs="theora"',
            h264: 'video/mp4; codecs="avc1.42E01E"',
            webm: 'video/webm; codecs="vp8, vorbis"',
            vp9: 'video/webm; codecs="vp9"',
            hls: 'application/x-mpegURL; codecs="avc1.42E01E"'
        };

        if(!video) {
            video = document.createElement('video')
        }

        return video.canPlayType(formats[type] || type);
    }

    _init();

    /*return {
        init : _init
    }*/
};