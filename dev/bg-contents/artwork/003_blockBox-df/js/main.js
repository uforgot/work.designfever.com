var BomberPhysics = function(canvas){

    //canvas
    var _canvas = canvas;
    var _ctx = _canvas.getContext('2d');

    var stageW = 0;
    var stageH = 0;
    var ratio = window.devicePixelRatio;

    var Engine = Matter.Engine,
        Render = Matter.Render,
        Composites = Matter.Composites,
        Composite = Matter.Composite,
        World = Matter.World,
        Constraint = Matter.Constraint,
        MouseConstraint = Matter.MouseConstraint,
        Mouse = Matter.Mouse,
        Bodies = Matter.Bodies,
        Body = Matter.Body;

    var engine, world;
    var mouseConstraint;
    var render;

    var _init = function(tW, tH){
        _resize(tW, tH);
        _setStage();
        _setObject();
        _addEvent();
    };


    var _setStage = function(){
        // create engine
        engine = Engine.create();
        world = engine.world;
        engine.world.gravity.x = 0;
        engine.world.gravity.y = 1;

        var _mouse = Mouse.create(_canvas);
        _mouse.pixelRatio = ratio;

        mouseConstraint = MouseConstraint.create(engine, {
            mouse: _mouse,
            constraint: {
                render: {
                    visible: false
                }
            }
        });

        // create renderer
        render = Render.create({
            canvas: canvas,
            engine: engine,
            options: {
                width: stageW,
                height: stageH,
                pixelRatio: ratio,
                wireframeBackground: '#ffffff',
                wireframes: false
            }
        });

        Engine.run(engine);
        Render.run(render);
    };

    var _setObject = function(){

        // some settings
        var offset = 30, gap = 500, thickness = 30, circleSize = Math.floor(stageW/60),
            wallOptions = {
                isStatic: true
            };

// add some invisible some walls to the world
        World.add(engine.world, [
            //top
            Bodies.rectangle(stageW/2, -thickness/2-gap, stageW, thickness, wallOptions),
            //bot
            Bodies.rectangle(stageW/2, stageH+thickness/2, stageW, thickness, wallOptions),
            //right
            Bodies.rectangle(stageW+thickness/2, stageH/2-(gap/2), thickness, stageH+gap, wallOptions),
            //left
            Bodies.rectangle(-thickness/2, stageH/2-(gap/2), thickness, stageH+gap, wallOptions),

            //block circle
            Bodies.circle(stageW/4 * 1, -100, circleSize, wallOptions),
            Bodies.circle(stageW/4 * 2, -circleSize, circleSize, wallOptions),
            Bodies.circle(stageW/4 * 3, -100, circleSize, wallOptions),

            Bodies.circle(stageW/8 * 1, -400, circleSize, wallOptions),
            Bodies.circle(stageW/8 * 3, -400+90, circleSize, wallOptions),
            Bodies.circle(stageW/8 * 5, -400, circleSize, wallOptions),
            Bodies.circle(stageW/8 * 7, -400, circleSize, wallOptions),
            // Bodies.circle(stageW/5 * 4, -0+90, circleSize, wallOptions)
        ]);

        var coinMax = 200;
        var numx = Math.floor(stageW / 12);
        var numy = Math.ceil(coinMax / numx);

//create a stack
        var stack = Composites.stack(6, -gap, numx, numy, 0, 0, function(x, y, column, row) {

            if(Math.random() > 0.9 ){
                return Bodies.circle(x, y, 42, {
                    render: {
                        sprite: {
                            frictionAir: 1.01,
                            texture: './images/Icon-block.png',
                            xScale: 0.5,
                            yScale: 0.5
                        }
                    }
                });
            } else {
                return Bodies.circle(x, y, 22, {
                    render: {
                        sprite: {
                            frictionAir: 1.01,
                            texture: './images/Icon-block.png',
                            xScale: 0.25,
                            yScale: 0.25
                        }
                    }
                });
            }

        });

        World.add(world, [mouseConstraint, stack]);
    };

    var _addEvent = function(){
        if(window.DeviceMotionEvent){
            window.addEventListener("devicemotion", setGravity, false);
        }
    };

    var setGravity = function(event){
        if(!event.accelerationIncludingGravity.x) return;
        var pow = event.accelerationIncludingGravity.x * 0.3;
        if(pow < -2){
            pow = -2;
        } else if(pow > 2){
            pow = 2;
        }

        pow = Math.floor(pow * 100) / 100;

        var isIOS = document.querySelector("html").classList.contains("ios");
        if(!isIOS){
            engine.world.gravity.x = -pow;
            engine.world.gravity.y = (event.accelerationIncludingGravity.y)/10;
        } else {
            engine.world.gravity.x = pow;
            engine.world.gravity.y = (-event.accelerationIncludingGravity.y)/10;
        }

        console.log("setGravity", event.accelerationIncludingGravity)
    };


    var _resize = function(tW, tH){
        stageW = tW;
        stageH = tH;

        _canvas.style.width = tW + "px";
        _canvas.style.height = tH + "px";
        _canvas.width = stageW * ratio;
        _canvas.height = stageH * ratio;

        if(render) {
            World.clear(engine.world);
            Engine.clear(engine);

            _setStage();
            _setObject();
        }
    };


    return {
        init : _init,
        resize : _resize
    };
}


window.onload = function () {

};

function onResize() {

}



