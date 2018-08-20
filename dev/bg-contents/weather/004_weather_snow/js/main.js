
var Weather_Rain = function(args){
    var options                     = options || {};
    options.stageWidth          = args.hasOwnProperty('stageWidth') ? args.stageWidth : 1920;
    options.stageHeight         = args.hasOwnProperty('stageHeight') ? args.stageHeight : 1080;
    var container = args.container;

    var isMobile = document.querySelector("html").classList.contains("mobile");
    var ratio = Math.floor(window.devicePixelRatio);

    var renderer, camera, scene,
        light, light1;

    var rainPow = args.powerStep || 1;

    var rainLinesArr = [500, 2000, 5000];
    var rainSpdArr = [1, 1, 1];

    var rainLines = rainLinesArr[rainPow-1]; //isMobile ? 50*rainPow : 30*rainPow// 50 - 1000;
    var rainSpd = rainSpdArr[rainPow-1]//4//isMobile ? rainSpdArr[rainPow]*2 : rainSpdArr[rainPow];
    var curRainSpd = rainSpd;
    /*rainLines = 1000;
    rainSpd = 100;*/

    var dimension = 80;
    var lines = [];

    var isDown = false;



    var _setting = function(){
        _setStage();
        _setElement();
        _addEvent();
    };

    var _init = function(){
        window.requestAnimationFrame(render);
        _resetSize(window.innerWidth, window.innerHeight)
    };

    var _setStage = function(){
        // Renderer
        renderer = new THREE.WebGLRenderer({alpha: true, canvas:container, antialiasing:true});
        renderer.setClearColor( 0x000000, 0 );
        renderer.setPixelRatio( ratio );
        renderer.setSize(window.innerWidth,window.innerHeight);

        // debugTeat(options.stageWidth, options.stageHeight, "start");

        // Camera
        camera = new THREE.PerspectiveCamera( 55, options.stageWidth / options.stageHeight, 0.1, 5000 );
        camera.position.z = 400;

        // Scene
        scene = new THREE.Scene();
        scene.rotation.x = 0.8;

        // Light
        light = new THREE.AmbientLight(0xffffff,1);
        light1 = new THREE.PointLight(0xffffff,1);

        scene.add(light);
        scene.add(light1);
    }

    var _setElement = function(){
        for (var i = 0; i < rainLines; i++) {
            // var geometryLines = new THREE.BoxGeometry( 1, 1, 100 * Math.random() + 10);
            var size = Math.floor(Math.random() * 2) + 1;
            var geometryLines = new THREE.SphereGeometry( size, 4, 4 );
            var materialLines = new THREE.MeshPhongMaterial( { color:0xffffff, shininess :0} );
            var line = new THREE.Mesh( geometryLines, materialLines );
            scene.add( line );
            lines.push( line );
            line.rotation.z = Math.random() * 360;

            line.position.x = Math.random() * (dimension * 15) - (dimension / 2 * 15);
            line.position.y = Math.random() * (dimension * 15) - (dimension / 2 * 15);
            line.position.z = (-600 * Math.random()) + 150;
            line.modifier = (Math.random() * 2) + 0.5;

            line.angle = Math.random() * 0.5;
            line.direct = Math.random() > 0.5 ? 1 : -1;
        }
    };

    var _addEvent = function(){
        container.addEventListener('mousedown', onMouseDown, false );
        container.addEventListener('touchstart', onMouseDown, false );
        container.addEventListener('mouseup', onMouseUp, false );
        container.addEventListener('touchend', onMouseUp, false );
    };

    var onMouseDown = function(){
        isDown = true;
    };

    var onMouseUp = function(){
        isDown = false;
    };



    var _resetSize = function(w, h){

        options.stageWidth = w;
        options.stageHeight = h;
        // renderer.setPixelRatio( ratio );

        camera.aspect = options.stageWidth / options.stageHeight;
        camera.updateProjectionMatrix();

        renderer.setSize( options.stageWidth, options.stageHeight );
        light1.position.set( options.stageWidth/2, options.stageHeight*-1, 600 );

        for (var i = lines.length - 1; i >= 0; i--) {
            var line = lines[i];
            line.position.x = Math.random() * (dimension * 15) - (dimension / 2 * 15);
            line.position.y = Math.random() * (dimension * 15) - (dimension / 2 * 15);
            line.position.z = Math.random() * 1200 - 600;

        }
    };


    var angle = 0;
    var render = function(){
        angle += 0.005
        for (var i = lines.length - 1; i >= 0; i--) {
            var line = lines[i];
            line.position.z += curRainSpd * lines[i].modifier;
            line.position.y += Math.sin(line.angle+(angle*line.direct))
            line.position.x += Math.cos(line.angle+(angle*line.direct))
            // line.position.x += Math.cos(line.angle+(angle*line.direct))
            if(line.position.z >600){
                line.position.z = -600;

            }
        }

        // scene.rotation.y += 0.01;
        scene.rotation.z += 0.0013;
        camera.lookAt(scene.position);

        renderer.render(scene,camera);
        window.requestAnimationFrame(render);


        if(isDown){
            rainSpd*3 < curRainSpd ? curRainSpd : curRainSpd+=5
            curRainSpd *= 0
        } else {
            rainSpd >= curRainSpd ? curRainSpd = rainSpd : curRainSpd-=3
        }
    };


    _setting();

    return {
        init : _init,
        resetSize : _resetSize
    }

}







