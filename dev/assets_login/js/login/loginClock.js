var LoginClock = function(con, json_data){

    var CLASS_NAME = "[ LoginClock ]";

    var _con = con;

    var _def_opts = {
        objectName : "ConectedLines_" + new Date().getTime(),
        container : document.body,
        stageWidth : 1920,
        stageHeight : 1080,
    };

    var _opts = {};

    var _preset = {

    };

    var _vars = {
        count: 0,
        oW: 0,
        oH: 0,
        clock:{
            hh:0,
            mm:0,
            ss:0
        }
    };

    var _pixi = {
        render: {},
        app: {},
        mainContainer: {},
        clockContainer: {},
        clockGraphic: {
            bar_hh: {},
            bar_mm: {},
            bar_ss: {}
        },

        txt_hh: {},
        txt_mm: {},
        txt_ss: {}
    };

    var ID_timeout = null;

    function init(today){
        //console.log(CLASS_NAME + " container : ", _con);
        _setting({
            container : _con,
            stageWidth:_con.offsetWidth,
            stageHeight:_con.offsetHeight
        });
        _updateToday(today);
        _addEvent();
        _start();
    }

    var _setting = function(arg){

        _opts = df.lab.Util.combine_object_value(arg, _def_opts);

        _vars.oW = _opts.stageWidth;
        _vars.oH = _opts.stageHeight;

        _setPixi();

        _pixi.mainContainer   = new PIXI.Container();
        _pixi.clockContainer   = new PIXI.Container();
        //_pixi.clockContainer.alpha = 0;

        _pixi.app.stage.addChild( _pixi.mainContainer );

        _pixi.render = new PIXI.ticker.Ticker();
        _pixi.render.autoStart = true;

        _pixi.clockGraphic.bar_hh = new PIXI.Graphics();
        _pixi.clockGraphic.bar_mm = new PIXI.Graphics();
        _pixi.clockGraphic.bar_ss = new PIXI.Graphics();


        _pixi.clockContainer.rotation = Math.radians(-360);
        _pixi.clockGraphic.bar_hh.alpha = 0;
        _pixi.clockGraphic.bar_mm.alpha = 0;
        _pixi.clockGraphic.bar_ss.alpha = 0;

        _pixi.clockGraphic.bar_hh.scale.x = 0.1;
        _pixi.clockGraphic.bar_mm.scale.x = 0.1;
        _pixi.clockGraphic.bar_ss.scale.x = 0.1;

        _settingTxt();

        _resetSize(_opts.stageWidth, _opts.stageHeight);

        _opts.container.appendChild( _pixi.app.view );
    };

    var _drawBar = function(){

        var center_x = _opts.stageWidth/2;
        var center_y = _opts.stageHeight/2;

        _pixi.clockContainer.x  = center_x;
        _pixi.clockContainer.y  = center_y;

        var half = Math.min(center_x, center_y);


        var half_ss = half - 58;
        var half_mm = half - 58;
        var half_hh = Math.round(half_mm * 0.74);

        _pixi.clockGraphic.bar_hh.clear();
        _pixi.clockGraphic.bar_hh.beginFill(0xFFFFFF);
        _pixi.clockGraphic.bar_hh.drawRoundedRect(-3, -3, half_hh + 3, 6, 3);
        _pixi.clockGraphic.bar_hh.endFill();

        _pixi.clockGraphic.bar_mm.clear();
        _pixi.clockGraphic.bar_mm.beginFill(0xFFFFFF);
        _pixi.clockGraphic.bar_mm.drawRoundedRect(-3, -3, half_mm + 3, 6, 3);
        _pixi.clockGraphic.bar_mm.endFill();

        _pixi.clockGraphic.bar_ss.clear();
        _pixi.clockGraphic.bar_ss.beginFill(0xFF0000);
        _pixi.clockGraphic.bar_ss.drawRoundedRect(-1, -1, half_ss + 1, 2, 1);
        _pixi.clockGraphic.bar_ss.endFill();

    };

    var _addEvent = function(){

        window.onresize = function(event) {
            checkSize();
        };

        window.addEventListener("orientationchange", function() {
            clearTimeout(ID_timeout);
            ID_timeout = setTimeout(checkSize, 1000);
        }, false);
    };


    function checkSize(){

        if(_vars.oW != _opts.container.offsetWidth || _vars.oH != _opts.container.offsetHeight) {
            _vars.oW = _opts.container.offsetWidth;
            _vars.oH = _opts.container.offsetHeight;
            _resetSize(_vars.oW, _vars.oH);
        }
    }

    var _settingTxt = function(){
        var style_hh = new PIXI.TextStyle({
            fontFamily: 'NanumSquareRound',
            //fontFamily: 'Arial',
            fontSize: 20,
            //fontStyle: 'italic',
            fontWeight: '700',
            fill: ['#ffffff'],
            //fill: ['#ffffff', '#00ff99'], // gradient
            //stroke: '#4a1850',
            //strokeThickness: 5,
            // dropShadow: true,
            // dropShadowColor: '#000000',
            // dropShadowBlur: 4,
            // dropShadowAngle: Math.PI / 6,
            // dropShadowDistance: 6,
            // wordWrap: true,
            // wordWrapWidth: 440
        });

        _pixi.txt_hh = new PIXI.Text('00', style_hh);

        var style_mm = new PIXI.TextStyle({
            fontFamily: 'NanumSquareRound',
            fontSize: 14,
            fontWeight: '700',
            fill: ['#ffffff'],
        });

        _pixi.txt_mm = new PIXI.Text('00', style_mm);

        var style_ss = new PIXI.TextStyle({
            fontFamily: 'NanumSquareRound',
            fontSize: 14,
            fontWeight: '700',
            fill: ['#ff0000'],
        });
        _pixi.txt_ss = new PIXI.Text('00', style_ss);

        _pixi.txt_hh.alpha = 0;
        _pixi.txt_mm.alpha = 0;
        _pixi.txt_ss.alpha = 0;
    };

    var _resetSize = function(w, h){
        _opts.stageWidth = w || _opts.stageWidth;
        _opts.stageHeight = h || _opts.stageHeight;

        _pixi.app.view.style.width = _opts.stageWidth+"px";
        _pixi.app.view.style.height = _opts.stageHeight+"px";

        _drawBar();

        _pixi.app.renderer.resize(_opts.stageWidth, _opts.stageHeight);   // * PIXI.settings.RESOLUTION)
    };

    var _setPixi = function() {

        // 네이티브 윈도우 해상도를 기본 해상도로 사용
        // 렌더링 할 때 고밀도 디스플레이를 지원합니다.
        PIXI.settings.RESOLUTION = window.devicePixelRatio;

        // 크기를 조정할 때 보간을 사용하지 않고 텍스처를 픽셀 화합니다.
        PIXI.settings.SCALE_MODE = PIXI.SCALE_MODES.NEAREST;

        _pixi.app = new PIXI.Application(
            _opts.stageWidth,
            _opts.stageHeight,
            {
                transparent: true,
                antialias: true
            });
    };

    var _start = function(){

        _pixi.mainContainer.addChild(_pixi.clockContainer);
        _pixi.clockContainer.addChild(_pixi.clockGraphic.bar_ss);
        _pixi.clockContainer.addChild(_pixi.clockGraphic.bar_mm);
        _pixi.clockContainer.addChild(_pixi.clockGraphic.bar_hh);

        _pixi.mainContainer.addChild(_pixi.txt_hh);
        _pixi.mainContainer.addChild(_pixi.txt_mm);
        //_pixi.mainContainer.addChild(_pixi.txt_ss);

        _drawCanvas();

        TweenMax.to(_pixi.clockContainer, 5, {rotation:0, ease:Expo.easeOut, delay:1});
        TweenMax.to(_pixi.clockGraphic.bar_hh, 2.2, {alpha:1, ease:Expo.easeInOut, delay:1});
        TweenMax.to(_pixi.clockGraphic.bar_mm, 2.2, {alpha:1, ease:Expo.easeInOut, delay:1.5});
        TweenMax.to(_pixi.clockGraphic.bar_ss, 2.2, {alpha:1, ease:Expo.easeInOut, delay:2});

        TweenMax.to(_pixi.clockGraphic.bar_hh.scale, 2.2, {x:1, ease:Expo.easeInOut, delay:1});
        TweenMax.to(_pixi.clockGraphic.bar_mm.scale, 2.2, {x:1, ease:Expo.easeInOut, delay:1.5});
        TweenMax.to(_pixi.clockGraphic.bar_ss.scale, 2.2, {x:1, ease:Expo.easeInOut, delay:1.5});

        TweenMax.to(_pixi.txt_hh, 1.2, {alpha:1, ease:Cubic.easeOut, delay:3});
        TweenMax.to(_pixi.txt_mm, 1.2, {alpha:1, ease:Cubic.easeOut, delay:3.5});
        TweenMax.to(_pixi.txt_ss, 1.2, {alpha:1, ease:Cubic.easeOut, delay:4});
    };

    var _drawCanvas = function () {
        _pixi.render.add(function (dleta) {
            _updateValue();
            _updateTime();
        });
    };

    var _updateValue = function(){
        _vars.count = _vars.count + 1;
    };

    var _updateTime = function(){

        var angle_hh = 0; //Math.radians((_vars.count*0.2)%360);
        var angle_mm = 0; //Math.radians((_vars.count*0.5)%360);
        var angle_ss = 0; //Math.radians((_vars.count*1)%360);


        var degree_s = (_vars.clock.ss/60) * 360;
        angle_ss = Math.radians((degree_s - 90)%360);

        var degree_m = (_vars.clock.mm/60) * 360;
        angle_mm = Math.radians((degree_m - 90)%360);

        var degree_h = ((_vars.clock.hh%12)/12) * 360 + ((_vars.clock.mm/60) * (360 / 12));
        angle_hh = Math.radians((degree_h - 90)%360);

        _pixi.clockGraphic.bar_hh.rotation = angle_hh;
        _pixi.clockGraphic.bar_mm.rotation = angle_mm;
        _pixi.clockGraphic.bar_ss.rotation = angle_ss;

        var center_x = _opts.stageWidth/2;
        var center_y = _opts.stageHeight/2;

        var half = Math.min(center_x, center_y);

        //var half_hh = half - 92;
        var half_mm = half - 58;
        var half_ss = half - 58;

        var point_hh = new PIXI.Point();
        var point_mm = new PIXI.Point();
        var point_ss = new PIXI.Point();

        // txt
        _pixi.txt_hh.text = (_vars.clock.hh%12) == 0 ? "12" : (_vars.clock.hh%12);
        _pixi.txt_mm.text = _vars.clock.mm < 10 ? '0'+_vars.clock.mm : _vars.clock.mm;
        _pixi.txt_ss.text = _vars.clock.ss < 10 ? '0'+_vars.clock.ss : _vars.clock.ss;

        point_hh.x = center_x +  Math.cos(angle_hh) * (half_mm + 20) - (_pixi.txt_hh.width/2);
        point_hh.y = center_y +  Math.sin(angle_hh) * (half_mm + 20) - (_pixi.txt_hh.height/2);

        _pixi.txt_hh.x = point_hh.x;
        _pixi.txt_hh.y = point_hh.y;

        point_mm.x = center_x +  Math.cos(angle_mm) * (half_mm + 44) - (_pixi.txt_mm.width/2);
        point_mm.y = center_y +  Math.sin(angle_mm) * (half_mm + 44) - (_pixi.txt_mm.height/2);

        _pixi.txt_mm.x = point_mm.x;
        _pixi.txt_mm.y = point_mm.y;

        point_ss.x = center_x +  Math.cos(angle_ss) * (half_ss + 44) - (_pixi.txt_ss.width/2);
        point_ss.y = center_y +  Math.sin(angle_ss) * (half_ss + 44) - (_pixi.txt_ss.height/2);

        _pixi.txt_ss.x = point_ss.x;
        _pixi.txt_ss.y = point_ss.y;
    };

    function _updateToday(today){

        _vars.clock.hh = today.hh;
        _vars.clock.mm = today.mm;
        _vars.clock.ss = today.ss;

        //console.log(_vars.clock.hh, " : ", _vars.clock.mm, " : ", _vars.clock.ss);
    }

    return {
        init: init,
        updateToday: _updateToday
    }
};