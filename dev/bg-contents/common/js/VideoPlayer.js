var VideoPlayer = function(dataObj){

    var video_url = dataObj.video_url;
    var image_url = dataObj.image_url || "../../common/img/default_bg_image.jpg";
    var only_video = dataObj.only_video || false;

    var _setting = function(){
        _setElement(only_video ? true : _checkPlayType());
    };

    var _setElement = function(isVideoAble){
        if(isVideoAble){
            // play video
            var video = document.querySelector("video");
            video.style.opacity = 1;

            var source = document.querySelector("video source");
            source.src = video_url;
            // video.src = video_url;
            // video.crossOrigin = 'anonymous';
            video.oncanplay = function() {
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

        var html = document.querySelector("html");
        var isMobile = html.classList.contains("mobile");

        var connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection || navigator.msConnection;

        if(isMobile){
            var isAndroid = html.classList.contains("android");
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