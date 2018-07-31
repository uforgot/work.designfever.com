
var Sphere3D_line = function(container, isMore, preset_arg) {
    if (!Detector.webgl) Detector.addGetWebGLMessage();

    var _preset_def = {
        NUM_LAT: isMore ? 50 : 50,
        NUM_LNG: isMore ? 100 : 50,
        NUM_LINES_CULLED: 0,
        CAMERA_Z: 4
    };

    var _preset = df.lab.Util.combine_object_value(preset_arg, _preset_def);

    var _container = container;
    var camera, scene, renderer;
    var geometry, mesh;

    var _value = {
        stageWidth: _container.offsetWidth,
        stageHeight: _container.offsetHeight,
        count_x: 0,
        count_y: 0,
        passPos: {x:0, y:0},
        curPos: {x:0, y:0},
        isDrag: false
    };

    var targetRotation = 0;
    var targetRotationOnMouseDown = 0;
    var mouseX = 0;
    var mouseXOnMouseDown = 0;

    function init() {

        setStage();
        setLayout();
        addEvent();
        start();
    }

    function setStage(){
        renderer = new THREE.WebGLRenderer({antialias: true, alpha: false});
        renderer.setClearColor(0x000000, 1);
        renderer.setPixelRatio(window.devicePixelRatio);
        renderer.setSize(_value.stageWidth, _value.stageHeight);

        camera = new THREE.PerspectiveCamera(45, _value.stageWidth / _value.stageHeight, 0.01, 10);
        camera.position.z = _preset.CAMERA_Z;

        container.appendChild(renderer.domElement);
        scene = new THREE.Scene();
    }

    function addEvent(){
        window.addEventListener('resize', onWindowResize, false);

        _container.addEventListener( 'mousedown', onDragStart);
        _container.addEventListener( 'touchstart', onDragStart);
    }

    function setLayout(){
        scene.add(new THREE.AmbientLight(0x444444));
        addLines(1.0);
    }

    function start(){
        animate();
    }

    function addLines(radius) {
        geometry = new THREE.BufferGeometry();
        var linePositions = new Float32Array(_preset.NUM_LAT * _preset.NUM_LNG * 3 * 2);
        var lineColors = new Float32Array(_preset.NUM_LAT * _preset.NUM_LNG * 3 * 2);
        var visible = new Float32Array(_preset.NUM_LAT * _preset.NUM_LNG * 2);
        for (var i = 0; i < _preset.NUM_LAT; ++i) {
            for (var j = 0; j < _preset.NUM_LNG; ++j) {
                var lat = (Math.random() * Math.PI) / 50.0 + i / _preset.NUM_LAT * Math.PI;
                var lng = (Math.random() * Math.PI) / 50.0 + j / _preset.NUM_LNG * 2 * Math.PI;
                var index = i * _preset.NUM_LNG + j;
                var ran_s = radius + (Math.random()*0.2 - 0.1);
                linePositions[index * 6 + 0] = 0;
                linePositions[index * 6 + 1] = 0;
                linePositions[index * 6 + 2] = 0;
                linePositions[index * 6 + 3] = ran_s * Math.sin(lat) * Math.cos(lng);
                linePositions[index * 6 + 4] = ran_s * Math.cos(lat);
                linePositions[index * 6 + 5] = ran_s * Math.sin(lat) * Math.sin(lng);
                var color = new THREE.Color(0xffffff);
                //var color = new THREE.Color(0x00ff00);
                var h_per = lat;//(lat*0.5 + (Math.PI * 0.65));
                h_per = (h_per%Math.PI) / (Math.PI);

                color.setHSL(h_per, 1.0, 0.2);
                lineColors[index * 6 + 0] = color.r;
                lineColors[index * 6 + 1] = color.g;
                lineColors[index * 6 + 2] = color.b;
                color.setHSL(h_per, 1.0, 0.7);
                lineColors[index * 6 + 3] = color.r;
                lineColors[index * 6 + 4] = color.g;
                lineColors[index * 6 + 5] = color.b;
                // non-0 is visible
                visible[index * 2 + 0] = 1.0;
                visible[index * 2 + 1] = 1.0;
                //if(index%100 == 0) console.log(h_per);
            }
        }
        geometry.addAttribute('position', new THREE.BufferAttribute(linePositions, 3));
        geometry.addAttribute('vertColor', new THREE.BufferAttribute(lineColors, 3));
        geometry.addAttribute('visible', new THREE.BufferAttribute(visible, 1));
        geometry.computeBoundingSphere();
        var shaderMaterial = new THREE.ShaderMaterial({
            vertexShader: document.getElementById('vertexshader').textContent,
            fragmentShader: document.getElementById('fragmentshader').textContent
        });
        mesh = new THREE.LineSegments(geometry, shaderMaterial);
        scene.add(mesh);
        updateCount();
    }

    function updateCount() {
        //var str = '1 draw call, ' + _preset.NUM_LAT * _preset.NUM_LNG + ' lines, ' + _preset.NUM_LINES_CULLED + ' culled (<a target="_blank" href="http://callum.com">author</a>)';
        //document.getElementById( 'title' ).innerHTML = str.replace( /\B(?=(\d{3})+(?!\d))/g, "," );
    }

    function hideLines() {
        for (var i = 0; i < geometry.attributes.visible.array.length; i += 2) {
            if (Math.random() > 0.75) {
                if (geometry.attributes.visible.array[i + 0]) {
                    ++_preset.NUM_LINES_CULLED;
                }
                geometry.attributes.visible.array[i + 0] = 0;
                geometry.attributes.visible.array[i + 1] = 0;
            }
        }
        geometry.attributes.visible.needsUpdate = true;
        updateCount();
    }

    function showAllLines() {
        _preset.NUM_LINES_CULLED = 0;
        for (var i = 0; i < geometry.attributes.visible.array.length; i += 2) {
            geometry.attributes.visible.array[i + 0] = 1;
            geometry.attributes.visible.array[i + 1] = 1;
        }
        geometry.attributes.visible.needsUpdate = true;
        updateCount();
    }

    function onWindowResize() {

        _value.stageWidth = _container.offsetWidth;
        _value.stageHeight = _container.offsetHeight;

        camera.aspect = _value.stageWidth / _value.stageHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(_value.stageWidth, _value.stageHeight);
    }

    function animate(time) {
        requestAnimationFrame(animate);
        if(!_value.isDrag ) {
            var time = Date.now() * 0.001;
            _value.count_x = (time * 50) % 360;
            _value.count_y = (time * 100) % 360;
        }
        mesh.rotation.x = Math.radians(_value.count_x);
        mesh.rotation.z = Math.radians(_value.count_y);
        renderer.render(scene, camera);
    }

    var _drag_count = 0;
    function onDragStart( event ) {
        event.preventDefault();
        event.stopPropagation();

        _value.isDrag = true;

        var pointX, pointY;

        if(event.type == 'mousedown') {
            _container.addEventListener('mousemove', onDragMove);
            _container.addEventListener('mouseup', onDragEnd);
            _container.addEventListener('mouseout', onDragEnd);

            pointX = event.clientX;
            pointY = event.clientY;

        }else if(event.type == 'touchstart') {
            if ( event.touches.length === 1 ) {

                _container.addEventListener( 'touchmove', onDragMove);
                _container.addEventListener( 'touchend', onDragEnd);

                pointX = event.touches[ 0 ].pageX;
                pointY = event.touches[ 0 ].pageY;
            }
        }

        mouseXOnMouseDown = pointX - _value.stageWidth / 2;
        targetRotationOnMouseDown = targetRotation;

        _value.curPos.x = pointX;
        _value.curPos.y = pointY;
        _value.passPos.x = _value.curPos.x;
        _value.passPos.y = _value.curPos.y;

        _drag_count = 0;
    }
    function onDragMove( event ) {
        event.preventDefault();
        event.stopPropagation();

        var pointX, pointY;

        if(event.type == 'mousemove') {
            pointX = event.clientX;
            pointY = event.clientY;
        }else if(event.type == 'touchmove') {
            if ( event.touches.length === 1 ) {
                pointX = event.touches[ 0 ].pageX;
                pointY = event.touches[ 0 ].pageY;
            }
        }

        mouseX = pointX - _value.stageWidth/2;
        targetRotation = targetRotationOnMouseDown + ( mouseX - mouseXOnMouseDown ) * 0.05;

        _value.passPos.x = _value.curPos.x;
        _value.passPos.y = _value.curPos.y;
        _value.curPos.x = pointX;
        _value.curPos.y = pointY;

        var dis_x = _value.curPos.x - _value.passPos.x;
        var dis_y = _value.curPos.y - _value.passPos.y;
        carcPos(dis_x, dis_y);

        _drag_count = _drag_count + 1;
        if (_drag_count < 200) {
            if (_drag_count % 10 == 0) hideLines();
        }
    }

    function onDragEnd( event ) {
        _container.removeEventListener('mousemove', onDragMove);
        _container.removeEventListener('mouseup', onDragEnd);
        _container.removeEventListener('mouseout', onDragEnd);

        _container.removeEventListener( 'touchmove', onDragMove);
        _container.removeEventListener( 'touchend', onDragEnd);

        _value.isDrag = false;
        showAllLines();
    }

    function carcPos(dis_x, dis_y){
        _value.count_x = (_value.count_x + (dis_y*1))%360;
        _value.count_y = (_value.count_y + (dis_x*0.5))%360;
    }


    return {
        init: init
    }
};