var VideoPlayer = function(dataObj){

    var video_url = dataObj.video_url;
    var video_low_url = dataObj.video_low_url;
    var image_url = dataObj.image_url || "../../common/img/default_bg_image.jpg";
    var only_video = dataObj.only_video == undefined ? true : dataObj.only_video;
    var dimmedOpacity = dataObj.dimmed_opacity == undefined ? 0.4 : dataObj.dimmed_opacity;

    var _html = document.querySelector("html");
    var _isMobile = _html.classList.contains("mobile");
    var _checkTimer;

    var _setting = function(){
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

            // video.src = video_url;
            // video.crossOrigin = 'anonymous';
            video.load();
            video.oncanplay = function() {
                video.play();
            };
            _checkTimer = setInterval(function(){videoStatus(video)}, 1000);


        } else {
            //show image
            var wrapper = document.querySelector(".video-player .video-wrapper");
            wrapper.innerHTML = "";
            wrapper.style.backgroundImage = "url("+image_url+")";
        }
    };


    var videoStatus = function(video){
        if(video.readyState == 0){
            console.log(video.paused, video.readyState)
            /*video.play();
            video.style.opacity = 0.5*/
        } else {
            clearInterval(_checkTimer);
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

    var _init = function(){

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

    return {
        setting : _setting
    }
};