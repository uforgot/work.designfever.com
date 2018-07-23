var ImageGallery = function(data){

    var imgData = data;
    var _index = 0,
        _prevIndex = -1,
        _imgArr = [],
        _timer,
        _loopTime = 2000;

    var isMobile = document.querySelector("html").classList.contains("mobile");

    var _setting = function(){
        _setElement(imgData);
    };

    var _setElement = function(imgData){
        var defaultData = imgData.default;
        var lowData = imgData.low || [];

        if(isMobile && lowData.length) defaultData = lowData;

        var wrapper = document.createElement("div");
        wrapper.classList.add("img-wrapper");

        for(var i = 0 ; i<defaultData.length ; i++){
            var img = document.createElement("div");
            img.classList.add("img-content");
            if(i==_index) img.classList.add("show");
            img.style.backgroundImage = "url("+defaultData[i]+")";
            wrapper.appendChild(img);
        }

        var container = document.querySelector(".container-wrapper");
        container.appendChild(wrapper);
    };

    var _init = function(){
        _imgArr = document.querySelectorAll(".img-content");
        if(_imgArr.length > 1) _start();
    };


    var _start = function(){
        startTimer();
    };

    var _stop = function(){

    };

    var startTimer = function(){
        _timer = setInterval(_nextImage, _loopTime)
    };

    var _nextImage = function(){
        _prevIndex = _index;
        _index = (_index + 1) % _imgArr.length;
        _controlImages(_index);
    };

    var _controlImages = function(index){
        _imgArr[index].classList.add("show");
        if(_imgArr[_prevIndex]) _imgArr[_prevIndex].classList.remove("show");
    };

    _setting();

    return {
        setting : _setting,
        init : _init,
    }


};