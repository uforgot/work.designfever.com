
var AnimateSprite = function(args){

    var opts = args || {};
    var _this = this;
    var autoTimerNum = 0;

    var _ratio = Math.floor(window.devicePixelRatio);

    this.setting(opts, _ratio);
    this.setLayout();

    this.init = function(){
        _this.addToStage();
        gifLoader();
    };



    var gifLoader = function(){
        PIXI.loaders.Resource.setExtensionLoadType('gif', PIXI.loaders.Resource.LOAD_TYPE.XHR);
        PIXI.loaders.Resource.setExtensionXhrType('gif', PIXI.loaders.Resource.XHR_RESPONSE_TYPE.BUFFER);
        PIXI.loader.add([
            './images/gif_test.gif'
        ]).load(onLoad);
    };

    var characterArr = [];
    var animatedFrames = [];
    var onLoad = function(loader, res){
        Object.keys(res).forEach(function(k){
            res[k].data && animatedFrames.push(getFrames(res[k]));
        });

        var max = 0;
        for(var i=0; i < max ; i++){
            var frames = animatedFrames[Math.round(Math.random() * (animatedFrames.length - 1))];
            var character = new CharacterSprite(frames, _ratio);
            var ran = (Math.random() * 20 - 10);
            character.x = (i % 10) * 40 - 30 + ran;
            character.y = Math.ceil(i/10) * 20 + window.innerHeight;
            characterArr.push(character);
            _this.pixi.mainContainer.addChild(character);

        }

        drawCanvas();
        addEvent();
        addCharecter(0, true);
    };


    var getFrames = function(r){
        var frames           = [];
        var gif              = new GIF(new Uint8Array(r.data));
        var gifFrames        = gif.decompressFrames(true);
        var gifWidth         = gifFrames[0].dims.width;
        var gifHeight        = gifFrames[0].dims.height;
        var gifCanvas        = document.createElement('canvas');
        var gifCtx           = gifCanvas.getContext('2d');
        var gifImageData     = gifCtx.createImageData(gifWidth, gifHeight);

        gifCanvas.width  = gifFrames.length * (gifWidth * _ratio);
        gifCanvas.height = gifHeight * _ratio;

        var gifSpriteSheet = new PIXI.BaseTexture.fromCanvas(gifCanvas);

        gifFrames.map(
            function (f, i) {
                gifImageData.data.set(f.patch);
                gifCtx.putImageData(gifImageData, i * gifWidth*_ratio, 0);

                //console.log("f.patch : " , f.patch);
            }
        ).map(function (f, i) {
            frames.push(new PIXI.Texture(gifSpriteSheet, new PIXI.Rectangle(i * gifWidth, 0, gifWidth, gifHeight)));
        });
        return frames;
    };





    var autoAddCharacter = function(){

    };





    // render
    var drawCanvas = function(){
        _this.pixi.render.add(function( dleta ) {

            autoTimerNum++;
            if(autoTimerNum > 5){
                addCharecter(0, true);
                autoTimerNum = 0;
                console.log("auto")
            }


            if(isTouch) {
                touchTimerNum = touchTimerNum + 1;
            } else {

            }

            if(touchTimerNum > 80){

            }

            /*if(isTouchLong) {
                _this.pixi.mainContainer.x = Math.random() * 8 - 4;
                _this.pixi.mainContainer.y = Math.random() * 8 - 4;
            }*/

            for(var i=0; i < characterArr.length ; i++){
                var character = characterArr[i];
                character.render(isTouch)

            }
        });
    };




    var addEvent = function(){
        var container = document.querySelector(".content");
        container.addEventListener("touchstart", onTouchStart);
        container.addEventListener("touchmove", onTouchMove);
        container.addEventListener("touchend", onTouchEnd);

    };



    // touch event
    var isTouch = false;
    var isTouchLong = false;
    var touchTimerNum = 0;
    var onTouchStart = function(e){
        isTouch = true;
        // isTouchLong = false;
    };

    var onTouchMove = function(e){};

    var onTouchEnd = function(e){

        isTouch = false;
        touchTimerNum = 0;

        if(touchTimerNum < 15){
            addCharecter(0, true);
        }

        autoTimerNum = 0;
    };



    // add stage Charecter
    var addCharecter = function(index, isSolo){
        var character;
        var isNew = true;
        for(var i=0; i < characterArr.length ; i++){
            character = characterArr[i];
            if(!character.onStage){
                isNew = false;
                character.init();
                break;
            }
        }

        if(isNew){
            var frames = animatedFrames[Math.round(Math.random() * (animatedFrames.length - 1))];
            character = new CharacterSprite(frames, _ratio);
            characterArr.push(character);
            _this.pixi.mainContainer.addChild(character);
        }

        var ran = (Math.random() * 20 - 10);

        if(isSolo){
            character.x = Math.random() * (window.innerWidth);
            character.y = window.innerHeight;
        } else {
            character.x = (index % 10) * 40 - 30 + ran;
            character.y = Math.ceil(index/10) * 30 + window.innerHeight  + ran;

        }
    }




};



AnimateSprite.prototype = {
    options: {},
    pixi:{
        render: {},
        app: {},
        mainContainer: {},
        graphics: {}
    },

    setting: function(options, ratio){

        // 네이티브 윈도우 해상도를 기본 해상도로 사용
        // 렌더링 할 때 고밀도 디스플레이를 지원합니다.
        PIXI.settings.RESOLUTION = ratio;

        // 크기를 조정할 때 보간을 사용하지 않고 텍스처를 픽셀 화합니다.
        PIXI.settings.SCALE_MODE = PIXI.SCALE_MODES.NEAREST;

        //  OPTIONS
        /// ---------------------------
        this.options                     = options || {};
        this.options.objectName          = options.hasOwnProperty('objectName') ? options.objectName : new Date().getTime();
        this.options.stageWidth          = options.hasOwnProperty('stageWidth') ? options.stageWidth : 1920;
        this.options.stageHeight         = options.hasOwnProperty('stageHeight') ? options.stageHeight : 1080;
        this.options.pixiSprites         = options.hasOwnProperty('sprites') ? options.sprites : [];
        this.options.centerSprites       = options.hasOwnProperty('centerSprites') ? options.centerSprites : false;
        this.options.displaceAutoFit     = options.hasOwnProperty('displaceAutoFit')  ?  options.displaceAutoFit : false;
        this.options.container           = options.hasOwnProperty('container') ? options.container : document.body;

        this.pixi.app            = new PIXI.Application( this.options.stageWidth, this.options.stageHeight, { transparent: false, antialias: false, backgroundColor:0xeeeeee  });

        this.resetSize(this.options.stageWidth, this.options.stageHeight);

        //this.pixi.app.stage.interactive = true;
        //this.pixi.app.stage.buttonMode = true;

        this.pixi.mainContainer   = new PIXI.Container();
        //this.pixi.mainContainer.interactive = true;
        //this.pixi.mainContainer.buttonMode = true;

        this.pixi.render = new PIXI.ticker.Ticker();
        this.pixi.render.autoStart = true;

        this.pixi.graphics = new PIXI.Graphics();

    },

    setLayout : function() {
        // Add child container to the main container
        this.pixi.app.stage.addChild( this.pixi.mainContainer );
        this.pixi.mainContainer.addChild(this.pixi.graphics);
    },

    addToStage : function(){
        // Add canvas to the HTML
        //document.body.appendChild( renderer.view );
        this.options.container.appendChild( this.pixi.app.view );
        this.resetSize(this.options.stageWidth, this.options.stageHeight);

    },

    val: {},
    setValue : function (val){
        this.val = val;
    },

    getValue : function(){
        return this.val;
    },

    resetSize : function(w, h){
        this.options.stageWidth = w || this.options.stageWidth;
        this.options.stageHeight = h || this.options.stageHeight;

        this.pixi.app.view.style.width = this.options.stageWidth+"px";
        this.pixi.app.view.style.height = this.options.stageHeight+"px";

        this.pixi.app.renderer.resize(this.options.stageWidth, this.options.stageHeight);   // * PIXI.settings.RESOLUTION)

        this.resetValue();

    },

    resetValue : function(){

    }
};








/*
* image Thumbnail
* */
class CharacterSprite extends PIXI.extras.AnimatedSprite {
    constructor(frame, ratio){
        super(frame);
        this.ratio = ratio;
        this.setting(frame);
    }

    setting(frame){
        var random = Math.round(Math.random() * frame.length - 1);
        this.gotoAndPlay(random);
        var ran = Math.round(Math.random()*9) + 1;
        ran = 0.15 + ran/100;
        this.scale.set(ran * this.ratio);
        this.animationSpeed = 0.05;
        this.originSpd = Math.random() * 2 + 1;
        this.spd = this.originSpd

        this.onStage = true;
    }


    init(){
        this.animationSpeed = 0.05;
        this.y = window.innerHeight;
        this.spd = this.originSpd;
        this.onStage = true;
        this.gotoAndPlay(1);

        var ran = Math.round(Math.random()*9) + 1;
        ran = 0.15 + ran/100;
        this.scale.set(ran * this.ratio);
    }

    render(isTouch){
        if(!this.onStage) return;
        var upspd = this.spd;
        if(isTouch) {
            upspd = this.spd = this.spd + 0.4;
            if(upspd > 10) upspd = 10;
            this.animationSpeed = 0.2;
        } else {
            upspd = this.spd = this.spd * 0.8;
            if(this.originSpd > upspd) upspd = this.originSpd;
            this.animationSpeed = 0.05;
        }
        this.y = this.y - upspd;

        if(this.y < -120) {
            // this.y = window.innerHeight
            this.onStage = false;
            this.gotoAndStop(1);
        }
    }

}

















/*
* image Thumbnail
* */
/*class ChracterSprite extends PIXI.Sprite {
    constructor(img){
        super();
        this.setting(img);
    }

    setting(img){
        this.texture = new PIXI.Texture( new PIXI.BaseTexture(img) );

        this.interactive = true;
        this.buttonMode = true;
    }


    init(){

    }

    render(){

    }
}*/
















