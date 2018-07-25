var ConnectedLines = function(arg){

    var _def_opts = {
        objectName : "ConectedLines_" + new Date().getTime(),
        container : document.body,
        stageWidth : 1920,
        stageHeight : 1080,
    };

    var _opts = {};

    var _preset = {
        NUM_CIRCLE: 60,
        LINE_H: 2,
        CIRCLE_W: 1,

        CIRCLE_DIS: [30, 150, 80],
        LINE_ALPHA: 0.3,
        LINE_COLORS: [0xFF00FF, 0x00FFFF],

        MIN_SPEED: 2,
        MAX_SPEED: 10
    };

    var _vars = {
        data: {},
        times: 0,
        isDragging: false,
        isInit: false,
        count: 0,
        offset_count: 0,
        pos:{},
        speed:_preset.MIN_SPEED,
        add_speed:0
    };

    var _pixi = {
        render: {},
        app: {},
        mainContainer: {},
        graphics: {}
    };

    var _circles_0 = [];
    var _circles_1 = [];
    var _circles_2 = [];

    var _setting = function(arg){

        _opts = df.lab.Util.combine_object_value(arg, _def_opts);

        _setPixi();

        _pixi.mainContainer   = new PIXI.Container();
        _pixi.app.stage.addChild( _pixi.mainContainer );

        _pixi.render = new PIXI.ticker.Ticker();
        _pixi.render.autoStart = true;

        _resetSize(_opts.stageWidth, _opts.stageHeight);
    };

    var _addToStage = function(){
        _opts.container.appendChild( _pixi.app.view );
    };

    var _init = function(callback_hide, callback_show){

        _vars.isInit = true;
        _addToStage();
        _setCircle();
        _addEvent();
        _drawCanvas();

        _onCheckScroll();
        window.addEventListener('scroll', _onCheckScroll);
    };

    var _isShowArea = false;
    function _onCheckScroll(){

        var top = _opts.container.offsetTop - window.scrollY;
        if(top + _opts.container.offsetHeight < 0 || top > window.innerHeight){
            _isShowArea = false;
        }else{
            _isShowArea = true;
        }
    }

    var _getGrowColor = function(idx, tot){

        var color;

        var startColor = _preset.LINE_COLORS[0];
        var lastColor = _preset.LINE_COLORS[1];

        if( idx < tot/2 )   color = ( (lastColor - startColor)/(tot/2) ) * idx + startColor;
        else                color = ( (startColor - lastColor)/(tot/2) ) * (idx - tot/2) + lastColor;

        //
        // var R = 255 - ((255/tot)*idx);
        // var G = 255 - ((255/tot)*idx);
        // var B = 255 - ((255/tot)*idx);
        //
        // color = (R*65536)+(G*256)+B;

        return color
    };

    var _setCircle= function(){

        _pixi.graphics = new PIXI.Graphics();
        _pixi.mainContainer.addChild(_pixi.graphics);

        var circle;
        var tot=  _preset.NUM_CIRCLE;
        var i;
        for(i = 0; i<tot; i++){
            circle = {};

            //circle.color = df.lab.Util.color.hexToRgb('#FFFFFF');
            //circle.color = '0x'+(Math.random()*0xFF0000<<0).toString(16);
            circle.color = _getGrowColor(i, tot);

            //circle.color = ((0xFF0000 - 0x00FF00)/tot) * i + 0x00FF00;

            circle.x = 50;
            circle.y = 150;

            circle.radius = _preset.CIRCLE_DIS[0];
            circle.rotate = (360/tot) * i;

            circle.disX = Math.cos(Math.radians(circle.rotate)) * circle.radius;
            circle.disY = Math.sin(Math.radians(circle.rotate)) * circle.radius;

            circle.x_2 = 200;
            circle.y_2 = 600;

            circle.radius_2 = _preset.CIRCLE_DIS[1];
            circle.rotate_2 = (360/tot) * i;

            circle.disX_2 = Math.cos(Math.radians(circle.rotate_2)) * circle.radius_2;
            circle.disY_2 = Math.sin(Math.radians(circle.rotate_2)) * circle.radius_2;

            circle.x_3 = 400;
            circle.y_3 = 300;

            circle.radius_3 = _preset.CIRCLE_DIS[2];
            circle.rotate_3 = (360/tot) * i;

            circle.disX_3 = Math.cos(Math.radians(circle.rotate_3)) * circle.radius_3;
            circle.disY_3 = Math.sin(Math.radians(circle.rotate_3)) * circle.radius_3;

            _circles_0.push(circle);
        }
    };

    var _resetSize = function(w, h){
        _opts.stageWidth = w || _opts.stageWidth;
        _opts.stageHeight = h || _opts.stageHeight;

        _pixi.app.view.style.width = _opts.stageWidth+"px";
        _pixi.app.view.style.height = _opts.stageHeight+"px";

        _pixi.app.renderer.resize(_opts.stageWidth, _opts.stageHeight);   // * PIXI.settings.RESOLUTION)

        //console.log("_resetSize : ", _opts.stageWidth, _opts.stageHeight );
        //if(_vars.isInit) _repositionLines();
    };

    var _drawCanvas = function () {
        _pixi.render.add(function (dleta) {

            if (_isShowArea) {
                _updateCircle();
                _drawLines();
                _vars.count = _vars.count + _vars.speed;

                _vars.speed = _vars.speed + _vars.add_speed;

                if (_vars.speed < _preset.MIN_SPEED) {
                    _vars.speed = _preset.MIN_SPEED;
                    _vars.add_speed = 0;
                } else if (_vars.speed > _preset.MAX_SPEED) {
                    _vars.speed = _preset.MAX_SPEED;
                }

                _vars.offset_count = _vars.offset_count + 0.4;
            }
        });
    };

    var _updateCircle = function(){
        var circle;
        var tot = _circles_0.length;
        var i;
        for(i = 0; i<tot; i++){
            circle = _circles_0[i];

            circle.x = (100/400) * _opts.stageWidth;
            circle.y = (100/750) * _opts.stageHeight;
            circle.rotate = (360/tot) * i + ((_vars.count*1)%360);
            circle.disX = Math.cos(Math.radians(circle.rotate)) * circle.radius;
            circle.disY = Math.sin(Math.radians(circle.rotate)) * circle.radius;

            circle.x_2 = (150/400) * _opts.stageWidth;
            circle.y_2 = (650/750) * _opts.stageHeight;
            circle.rotate_2 = (360/tot) * i + ((_vars.count*0.3)%360);

            circle.disX_2 = Math.cos(Math.radians(circle.rotate_2)) * circle.radius_2;
            circle.disY_2 = Math.sin(Math.radians(circle.rotate_2)) * circle.radius_2;

            circle.x_3 = (200/400) * _opts.stageWidth;
            circle.y_3 = (300/750) * _opts.stageHeight;
            circle.rotate_3 = (360/tot) * i + ((_vars.count*0.8)%360);

            circle.disX_3 = Math.cos(Math.radians(circle.rotate_3)) * circle.radius_3;
            circle.disY_3 = Math.sin(Math.radians(circle.rotate_3)) * circle.radius_3;
        }

        //console.log(circle.rotate);
    };

    var _drawLines = function(){

        var per = ( (Math.sin( Math.radians(_vars.offset_count%360))) +1 ) * 0.5;
        //console.log(per);

        _pixi.graphics.clear();
        _pixi.graphics.lineStyle(null);
        _pixi.graphics.endFill();

        _pixi.graphics.beginFill(0x000000);
        _pixi.graphics.drawRect(0,0,_opts.stageWidth, _opts.stageHeight);
        _pixi.graphics.endFill();

        var gab = Math.floor(_preset.NUM_CIRCLE * 0.35);

        var circle;
        var circle_2;
        var circle_3;
        var tot = _circles_0.length;
        var i;

        var line_alpha = _preset.LINE_ALPHA;

        var offset_dis = {
            dis_x_1 : -50,
            dis_y_1 : 100,

            dis_x_2 : 50,
            dis_y_2 : -150,

            dis_x_3 : 200,
            dis_y_3 : -50,
        };

        for(i = 0; i<tot; i++){
            circle = _circles_0[i];

            circle_2 = _circles_0[ Math.floor(i+gab)%_preset.NUM_CIRCLE];
            circle_3 = _circles_0[ Math.floor(i + gab + 20)%_preset.NUM_CIRCLE];

            _pixi.graphics.lineStyle(_preset.LINE_H, circle.color, line_alpha);

            _pixi.graphics.moveTo(circle.disX + circle.x + (per * offset_dis.dis_x_1), circle.disY + circle.y + (per * offset_dis.dis_y_1));
            _pixi.graphics.lineTo(circle_2.disX_2 + circle_2.x_2 + (per * offset_dis.dis_x_2), circle_2.disY_2 + circle_2.y_2 +  + (per * offset_dis.dis_y_2));

            _pixi.graphics.lineStyle(_preset.LINE_H, circle.color, line_alpha);
            _pixi.graphics.lineTo(circle_3.disX_3 + circle_3.x_3 + (per * offset_dis.dis_x_3), circle_3.disY_3 + circle_3.y_3 + (per * offset_dis.dis_y_3));

            _pixi.graphics.lineStyle(_preset.LINE_H, circle.color, line_alpha);
            _pixi.graphics.lineTo(circle.disX + circle.x + (per * offset_dis.dis_x_1), circle.disY + circle.y  + (per * offset_dis.dis_y_1) );
        }

        _pixi.graphics.lineStyle(null);
        _pixi.graphics.beginFill(0x000000, 0.4);
        _pixi.graphics.drawCircle(circle.x_2 + (per * offset_dis.dis_x_2), circle.y_2 +  (per * offset_dis.dis_y_2), _preset.CIRCLE_DIS[1]);
        _pixi.graphics.endFill();

        _pixi.graphics.lineStyle(null);
        for(i = 0; i<tot; i++){
            circle = _circles_0[i];

            circle_2 = _circles_0[ Math.floor(i+gab)%_preset.NUM_CIRCLE];
            circle_3 = _circles_0[ Math.floor(i + gab * 2)%_preset.NUM_CIRCLE];

            _pixi.graphics.beginFill(circle.color);
            _pixi.graphics.drawCircle(circle.disX + circle.x + (per * offset_dis.dis_x_1), circle.disY + circle.y + (per * offset_dis.dis_y_1), _preset.CIRCLE_W * 1);
            _pixi.graphics.drawCircle(circle_2.disX_2 + circle_2.x_2 + (per * offset_dis.dis_x_2), circle_2.disY_2 + circle_2.y_2 + (per * offset_dis.dis_y_2), _preset.CIRCLE_W * 1);
            _pixi.graphics.drawCircle(circle_3.disX_3 + circle_3.x_3 + (per * offset_dis.dis_x_3), circle_3.disY_3 + circle_3.y_3 + (per * offset_dis.dis_y_3), _preset.CIRCLE_W * 1);
        }
        _pixi.graphics.endFill();

        //_pixi.graphics.lineStyle(1, 0xFFFFFF);
    };

    var _addEvent= function(){

        //console.log("_addEvent:", _vars);

        var obj = _pixi.mainContainer;

        obj.interactive = true;
        obj.buttonMode = true;

        obj.on('mousedown', _onDragStart)
            .on('touchstart', _onDragStart)
            .on('mouseup', _onDragEnd)
            .on('mouseupoutside', _onDragEnd)
            .on('touchend', _onDragEnd)
            .on('touchendoutside', _onDragEnd)
            .on('mousemove', _onDragMove)
            .on('touchmove', _onDragMove);
    };

    var _onDragStart= function (e) {
        //console.log("_onDragStart:", _vars);
        if (!_vars.isDragging) {

            _vars.times += 1;

            _vars.data = e.data;
            _vars.isDragging = true;

            //console.log(_vars.isDragging, _opts.objectName, _vars.offset_count );

            _vars.pos = {
                x:e.data.global.x,
                y:e.data.global.y
            };

            _vars.add_speed = 0.2;
        }
    };

    var _onDragEnd= function (e) {

        //console.log("_onDragEnd:", _vars);
        if (_vars.isDragging) {
            _vars.isDragging = false;

            // set the interaction data to null
            _vars.data = null;

            //console.log(_vars.isDragging, _opts.objectName, _vars.offset_count );
            _vars.add_speed = -0.1;
        }
    };

    var _onDragMove= function (e) {
        if (_vars.isDragging) {
            //_vars newPosition = this.data.getLocalPosition(this.parent);
            //this.x = newPosition.x - this.dragPoint.x;
            //this.y = newPosition.y - this.dragPoint.y;
            //console.log(vars.isDragging);

            var tmp_pos = e.data.global;

            //console.log(tmp_pos.x, _vars.pos.x);

            var dis = tmp_pos.x - _vars.pos.x;
            var per = dis/_opts.stageWidth;
            var offset = 360 * per;

            _vars.offset_count = _vars.offset_count + (offset);

            _vars.pos.x = tmp_pos.x;
            _vars.pos.y = tmp_pos.y;

            //console.log(_vars.isDragging, _opts.objectName, _vars.offset_count );
        }
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
                transparent: false,
                antialias: true
            });
    };

    var _getCanvas = function(){
        return _pixi.app.view;
    };

    var _getPixiCon = function(){
        return _pixi.mainContainer;
    };

    _setting(arg);

    return {
        init: _init,
        resetSize: _resetSize,
        getCanvas: _getCanvas,
        getPixiCon: _getPixiCon
    }
};