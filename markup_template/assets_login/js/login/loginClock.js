var DF_Clock = function(con){

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
        oH: 0
    };

    var _pixi = {
        render: {},
        app: {},
        mainContainer: {},
        graphics: {},
        txt_hh: {},
        txt_mm: {},
        txt_ss: {}
    };

    var ID_timeout = null;

    function init(){
        console.log("container : ", _con);
        _setting({
            container : _con,
            stageWidth:_con.offsetWidth,
            stageHeight:_con.offsetHeight
        });
        _addEvent();
        _start();
    }

    var _setting = function(arg){

        _opts = df.lab.Util.combine_object_value(arg, _def_opts);

        _vars.oW = _opts.stageWidth;
        _vars.oH = _opts.stageHeight;

        _setPixi();

        _pixi.mainContainer   = new PIXI.Container();
        _pixi.app.stage.addChild( _pixi.mainContainer );

        _pixi.render = new PIXI.ticker.Ticker();
        _pixi.render.autoStart = true;

        _pixi.graphics = new PIXI.Graphics();

        _settingTxt();

        _resetSize(_opts.stageWidth, _opts.stageHeight);

        _opts.container.appendChild( _pixi.app.view );
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

        _pixi.txt_hh = new PIXI.Text('8', style_hh);

        var style_mm = new PIXI.TextStyle({
            fontFamily: 'NanumSquareRound',
            fontSize: 14,
            fontWeight: '700',
            fill: ['#ffffff'],
        });
        _pixi.txt_mm = new PIXI.Text('05', style_mm);
    };

    var _resetSize = function(w, h){
        _opts.stageWidth = w || _opts.stageWidth;
        _opts.stageHeight = h || _opts.stageHeight;

        _pixi.app.view.style.width = _opts.stageWidth+"px";
        _pixi.app.view.style.height = _opts.stageHeight+"px";

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
        _pixi.mainContainer.addChild(_pixi.graphics);
        _pixi.mainContainer.addChild(_pixi.txt_hh);
        _pixi.mainContainer.addChild(_pixi.txt_mm);
        _drawCanvas();
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

        var angle_hh = Math.radians((_vars.count*0.2)%360);
        //var angle_hh = Math.radians(240 - 90);
        var angle_mm = Math.radians((_vars.count*0.5)%360);
        //var angle_mm = Math.radians(15 - 90);
        var angle_ss = Math.radians((_vars.count*1)%360);

        _pixi.graphics.clear();

        var center_x = _opts.stageWidth/2;
        var center_y = _opts.stageHeight/2;

        var half = _opts.stageWidth/2;

        var half_hh = half - 92;
        var half_mm = half - 58;

        _pixi.graphics.beginFill(0xFF00FF, 0.0);
        _pixi.graphics.drawCircle(center_x, center_y, half_mm);
        _pixi.graphics.endFill();

        _pixi.graphics.beginFill(0xFF0000, 0.0);
        _pixi.graphics.drawCircle(center_x, center_y, half_hh);
        _pixi.graphics.endFill();

        var point_hh = new PIXI.Point();
        var point_mm = new PIXI.Point();
        var point_ss = new PIXI.Point();

        point_hh.x = center_x +  Math.cos(angle_hh) * half_hh;
        point_hh.y = center_y +  Math.sin(angle_hh) * half_hh;

        point_mm.x = center_x +  Math.cos(angle_mm) * half_mm;
        point_mm.y = center_y +  Math.sin(angle_mm) * half_mm;

        point_ss.x = center_x +  Math.cos(angle_ss) * half_mm;
        point_ss.y = center_y +  Math.sin(angle_ss) * half_mm;

        _pixi.graphics.lineJoin = _pixi.graphics.lineCap = 'round';
        _pixi.graphics.lineStyle(2, 0xFF0000, 1);
        _pixi.graphics.moveTo(center_x, center_y);
        _pixi.graphics.lineTo(point_ss.x, point_ss.y);

        _pixi.graphics.lineStyle(4, 0xFFFFFF, 1);
        _pixi.graphics.moveTo(center_x, center_y);
        _pixi.graphics.lineTo(point_mm.x, point_mm.y);

        _pixi.graphics.lineStyle(4, 0xFFFFFF, 1);
        _pixi.graphics.moveTo(center_x, center_y);
        _pixi.graphics.lineTo(point_hh.x, point_hh.y);


        _pixi.txt_hh.text = Math.round(((_vars.count*0.2) + 90)%360) + "°";
        _pixi.txt_mm.text = Math.round(((_vars.count*0.5) + 90)%360) + "°";

        point_hh.x = center_x +  Math.cos(angle_hh) * (half_mm + 20) - (_pixi.txt_hh.width/2);
        point_hh.y = center_y +  Math.sin(angle_hh) * (half_mm + 20) - (_pixi.txt_hh.height/2);

        _pixi.txt_hh.x = point_hh.x;
        _pixi.txt_hh.y = point_hh.y;

        point_mm.x = center_x +  Math.cos(angle_mm) * (half_mm + 44) - (_pixi.txt_mm.width/2);
        point_mm.y = center_y +  Math.sin(angle_mm) * (half_mm + 44) - (_pixi.txt_mm.height/2);

        _pixi.txt_mm.x = point_mm.x;
        _pixi.txt_mm.y = point_mm.y;
    };

    return {
        init: init
    }
};