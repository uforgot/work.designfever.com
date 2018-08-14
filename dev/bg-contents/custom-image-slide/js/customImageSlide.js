var CustomImageSlide = function(jsondData, dimmedOpacity){

    var json = jsondData;
    var imgData = json.items;

    var _index = -1,
        _prevIndex = -1,
        _imgArr = [],
        _imgUrlArr = [],
        _timer,
        _dimmedOpacity = dimmedOpacity,
        _transTime = json.transition_time/1000 || 3,
        _loopTime = 5;

    var isMobile = document.querySelector("html").classList.contains("mobile");

    var _setting = function(){
        _loadImage();
    };

    var _setTransitionTime = function(){
        var content = document.querySelectorAll(".slide-show .img-wrapper .img-content");
        content.forEach(function(el) {
            el.style.transition = "opacity "+(_transTime)+"s linear, transform 10s linear "+_transTime+"s";
        });

        var style = document.styleSheets[0];
        style.insertRule(".slide-show .img-wrapper .img-content.show {transition: opacity "+(_transTime)+"s linear, transform 10s linear!important;}", 4)
    };

    var _setElement = function(){
        var defaultData = imgData;

        var wrapper = document.createElement("div");
        wrapper.classList.add("img-wrapper");

        for(var i = 0 ; i<defaultData.length ; i++){
            var img = document.createElement("div");
            var dataObj = defaultData[i];
            img.classList.add("img-content");
            var url = _imgUrlArr[i];

            img.style.backgroundImage = "url("+url+")";
            wrapper.appendChild(img);
        }

        var container = document.querySelector(".container-wrapper");
        container.appendChild(wrapper);
    };

    var _makeDimmed = function(){
        var alpha = _dimmedOpacity || 0;
        if(alpha == 0) return;

        var dimmed = document.createElement("div");
        dimmed.classList.add("dimmed");
        dimmed.style.backgroundColor = "rgba(0,0,0,"+alpha+")";

        var container = document.querySelector(".container-wrapper");
        container.appendChild(dimmed);
    };


    var _init = function(){
        _setElement();
        _setTransitionTime();
        _makeDimmed();

        _imgArr = document.querySelectorAll(".img-content");
        if(_imgArr.length > 1) {
            _start();
        } else {
            var curImg = document.querySelectorAll(".img-content")[0];
            curImg.classList.add("show");
        }


    };

    var _loadImage = function(){
        var imageNumber = 0;
        for (let i = 0; i < imgData.length; i++) {
            const img = new Image();

            var imgObj = imgData[i];
            var url = isMobile && imgObj.bg_url_low ? imgObj.bg_url_low : imgObj.bg_url;
            _imgUrlArr.push(url);
            // imageArr.push(img);
            img.onload = function () {
                imageNumber++;
                // console.log("image Load Complete", imageNumber, imgData.length)
                if(imageNumber == imgData.length){
                    _init();
                }
            };

            img.onerror = function () {
                _imgUrlArr[i] = json.bg_thumb;
                // console.log("image error!!!!!!!!!!!!!!!!!", i, this.src)
                this.src = json.bg_thumb;
            }

            img.src = url;
        }
    };

    var _start = function(){
        setTimeout(function(){
            _nextImage()
        }, 10);
    };

    var _nextImage = function(){
        _prevIndex = _index;
        _index = (_index + 1) % _imgArr.length;
        _controlImages(_index);

        var delay = imgData[_index].duration || _loopTime*1000;
        _timer = setTimeout(_nextImage, delay);
    };

    var _controlImages = function(index){
        _imgArr[index].classList.add("show");
        if(_imgArr[_prevIndex]) _imgArr[_prevIndex].classList.remove("show");
    };

    _setting();

    /*return {
        setting : _setting,
        init : _init,
    }*/


};








