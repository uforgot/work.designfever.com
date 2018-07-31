var ParticleLine = function(container, preset_arg){

    var _container = container;

    var _preset_def = {
        CAMERA_Z: 1750,
        MAX_PARTICLE_COUNT: 1000,
        R: 600,
        R_HALF: 300,
        SPEED: 1,
        SPEED_ADD: 10,
        POINT_SIZE: 3,
        POINT_COLOR: 0xFFFFFF
    };

    var _preset = df.lab.Util.combine_object_value(preset_arg, _preset_def);

    var _value = {
        stageWidth: _container.offsetWidth,
        stageHeight: _container.offsetHeight,
        isDrag: false,
        particleCount: 500,
        isHoldMode: false
    };

    var _count = 0;

    var group;

    var stats;
    var particlesData = [];
    var camera, scene, renderer;
    var positions, colors;
    var particles;
    var pointCloud;
    var particlePositions;
    var linesMesh;
    var effectController = {
        showDots: true,
        showLines: true,
        minDistance: 100,
        limitConnections: false,
        maxConnections: 20,
        particleCount: 500
    };

    var _line_bg;
    var group_2;

    var _over_callback;

    function initGUI() {
        var gui = new dat.GUI();
        gui.add( effectController, "showDots" ).onChange( function( value ) { pointCloud.visible = value; } );
        gui.add( effectController, "showLines" ).onChange( function( value ) { linesMesh.visible = value; } );
        gui.add( effectController, "minDistance", 10, 300 );
        gui.add( effectController, "limitConnections" );
        gui.add( effectController, "maxConnections", 0, 30, 1 );
        gui.add( effectController, "particleCount", 0, _preset.MAX_PARTICLE_COUNT, 1 ).onChange( function( value ) {
            _value.particleCount = parseInt( value );
            particles.setDrawRange( 0, _value.particleCount );
        });
    }
    function init(over_callback) {

        _over_callback = over_callback;

        //initGUI();
        //
        renderer = new THREE.WebGLRenderer( { antialias: true, alpha:true } );
        //renderer.setClearColor(0xeeeeee, 1);
        renderer.setPixelRatio( window.devicePixelRatio );
        renderer.setSize( _value.stageWidth, _value.stageHeight );
        renderer.gammaInput = true;
        renderer.gammaOutput = true;
        _container.appendChild( renderer.domElement );

        stats = new Stats();
        //_container.appendChild( stats.dom );

        camera = new THREE.PerspectiveCamera( 45, _value.stageWidth / _value.stageHeight, 1, 4000 );
        camera.position.z = _preset.CAMERA_Z;

        //var controls = new THREE.OrbitControls( camera, _container );
        scene = new THREE.Scene();
        group = new THREE.Group();
        group_2 = new THREE.Group();
        scene.add( group );
        scene.add( group_2 );

        // group.position.y = -200;
        // group_2.position.y = -200;

        var segments = _preset.MAX_PARTICLE_COUNT * _preset.MAX_PARTICLE_COUNT;
        positions = new Float32Array( segments * 3 );
        colors = new Float32Array( segments * 3 );
        var pMaterial = new THREE.PointsMaterial( {
            color: _preset.POINT_COLOR,
            size: _preset.POINT_SIZE,
            //blending: THREE.AdditiveBlending,
            transparent: true,
            sizeAttenuation: false
        } );

        particles = new THREE.BufferGeometry();
        particlePositions = new Float32Array( _preset.MAX_PARTICLE_COUNT * 3 );

        for ( var i = 0; i < _preset.MAX_PARTICLE_COUNT; i++ ) {
            var x = Math.random() * _preset.R - _preset.R / 2;
            var y = Math.random() * _preset.R - _preset.R / 2;
            var z = Math.random() * _preset.R - _preset.R / 2;
            particlePositions[ i * 3     ] = x;
            particlePositions[ i * 3 + 1 ] = y;
            particlePositions[ i * 3 + 2 ] = z;
            // add it to the geometry
            particlesData.push( {
                velocity: new THREE.Vector3( -(_preset.SPEED) + Math.random() * (_preset.SPEED*2), -(_preset.SPEED) + Math.random() * (_preset.SPEED*2),  -_preset.SPEED + Math.random() * (_preset.SPEED*2) ),
                pos: new THREE.Vector3( _preset.R_HALF * (Math.random()*0.1 + 0.9) , _preset.R_HALF * (Math.random()*0.1 + 0.9), _preset.R_HALF * (Math.random()*0.1 + 0.9)),
                numConnections: 0
            } );
        }
        particles.setDrawRange( 0, _value.particleCount );
        particles.addAttribute( 'position', new THREE.BufferAttribute( particlePositions, 3 ).setDynamic( true ) );
        // create the particle system
        pointCloud = new THREE.Points( particles, pMaterial );
        group.add( pointCloud );

        var geometry = new THREE.BufferGeometry();
        geometry.addAttribute( 'position', new THREE.BufferAttribute( positions, 3 ).setDynamic( true ) );
        geometry.addAttribute( 'color', new THREE.BufferAttribute( colors, 3 ).setDynamic( true ) );
        geometry.addAttribute( 'color', new THREE.BufferAttribute( colors, 3 ).setDynamic( true ) );
        geometry.computeBoundingSphere();
        geometry.setDrawRange( 0, 0 );
        var material = new THREE.LineBasicMaterial( {
            vertexColors: THREE.VertexColors,
            blending: THREE.AdditiveBlending,
            transparent: true
        } );
        linesMesh = new THREE.LineSegments( geometry, material );
        group.add( linesMesh );

        addLineBg();

        addEvent();

        animate();
    }

    function addLineBg(){
        var material_bg = new THREE.LineBasicMaterial( { color: 0xFFFFFF, opacity: 0.5, linewidth: 1 , alpha:0.5} );
        _line_bg = new THREE.LineSegments( createBgGeometry(), material_bg );
        group_2.add( _line_bg );
    }

    function createBgGeometry() {
        var r_width = 500;
        var geometry_bg = new THREE.BufferGeometry();
        var vertices = [];
        var vertex = new THREE.Vector3();
        for ( var i = 0; i < 1500; i ++ ) {
            vertex.x = Math.random() * 2 - 1;
            vertex.y = Math.random() * 2 - 1;
            vertex.z = Math.random() * 2 - 1;
            vertex.normalize();
            vertex.multiplyScalar( r_width );
            vertices.push( vertex.x, vertex.y, vertex.z );
            vertex.multiplyScalar( Math.random() * 0.1 + 1 );
            vertices.push( vertex.x, vertex.y, vertex.z );
        }
        geometry_bg.addAttribute( 'position', new THREE.Float32BufferAttribute( vertices, 3 ) );
        return geometry_bg;
    }

    function addEvent(){
        //_container.addEventListener('click', onClick, false );
        _container.addEventListener('mousedown', onMouseDown, false );
        _container.addEventListener('touchstart', onMouseDown, false );
        _container.addEventListener('mouseup', onMouseUp, false );
        _container.addEventListener('touchend', onMouseUp, false );
        window.addEventListener( 'resize', onWindowResize, false );
    }

    function onWindowResize() {

        _value.stageWidth = _container.offsetWidth;
        _value.stageHeight = _container.offsetHeight;

        camera.aspect = _value.stageWidth / _value.stageHeight;
        camera.updateProjectionMatrix();

        renderer.setSize( _value.stageWidth, _value.stageHeight );
    }

    function animate() {

            var vertexpos = 0;
            var colorpos = 0;
            var numConnected = 0;
            for ( var i = 0; i < _value.particleCount; i++ )
                particlesData[ i ].numConnections = 0;
            for ( var i = 0; i < _value.particleCount; i++ ) {
                // get the particle
                var particleData = particlesData[i];

                if(_value.isHoldMode) {

                    var tX = 0;
                    var tY = 0;
                    var tZ = 0;

                    if( i < (_value.particleCount/8) * 1){
                        tX = particleData.pos.x;
                        tY = particleData.pos.y;
                        tZ = particleData.pos.z;
                    }else if( i < (_value.particleCount/8) * 2){
                        tX = -particleData.pos.x;
                        tY = particleData.pos.y;
                        tZ = particleData.pos.z;
                    }else if( i < (_value.particleCount/8) * 3){
                        tX = -particleData.pos.x;
                        tY = -particleData.pos.y;
                        tZ = particleData.pos.z;
                    }else if( i < (_value.particleCount/8) * 4){
                        tX = particleData.pos.x;
                        tY = -particleData.pos.y;
                        tZ = particleData.pos.z;
                    }else if( i < (_value.particleCount/8) * 5){
                        tX = particleData.pos.x;
                        tY = particleData.pos.y;
                        tZ = -particleData.pos.z;
                    }else if( i < (_value.particleCount/8) * 6){
                        tX = -particleData.pos.x;
                        tY = particleData.pos.y;
                        tZ = -particleData.pos.z;
                    }else if( i < (_value.particleCount/8) * 7){
                        tX = -particleData.pos.x;
                        tY = -particleData.pos.y;
                        tZ = -particleData.pos.z;
                    }else{
                        tX = particleData.pos.x;
                        tY = -particleData.pos.y;
                        tZ = -particleData.pos.z;
                    }

                    if( i%8 > 4 ) {
                        particlePositions[i * 3] += 0.05 * (tX - particlePositions[i * 3]);
                        particlePositions[i * 3 + 1] += 0.05 * (tY - particlePositions[i * 3 + 1]);
                        particlePositions[i * 3 + 2] += 0.05 * (tZ - particlePositions[i * 3 + 2]);
                    }else{
                        particlePositions[i * 3] += (particleData.velocity.x * _preset.SPEED_ADD);
                        particlePositions[i * 3 + 1] += (particleData.velocity.y * _preset.SPEED_ADD);
                        particlePositions[i * 3 + 2] += (particleData.velocity.z * _preset.SPEED_ADD);
                    }
                }else{
                    particlePositions[i * 3] += particleData.velocity.x;
                    particlePositions[i * 3 + 1] += particleData.velocity.y;
                    particlePositions[i * 3 + 2] += particleData.velocity.z;
                }

                if ( particlePositions[ i * 3 + 1 ] < -_preset.R_HALF || particlePositions[ i * 3 + 1 ] > _preset.R_HALF )
                    particleData.velocity.y = -particleData.velocity.y;

                if ( particlePositions[ i * 3 ] < -_preset.R_HALF || particlePositions[ i * 3 ] > _preset.R_HALF )
                    particleData.velocity.x = -particleData.velocity.x;

                if ( particlePositions[ i * 3 + 2 ] < -_preset.R_HALF || particlePositions[ i * 3 + 2 ] > _preset.R_HALF )
                    particleData.velocity.z = -particleData.velocity.z;

                if ( effectController.limitConnections && particleData.numConnections >= effectController.maxConnections )
                    continue;
                // Check collision
                for ( var j = i + 1; j < _value.particleCount; j++ ) {
                    var particleDataB = particlesData[ j ];
                    if ( effectController.limitConnections && particleDataB.numConnections >= effectController.maxConnections )
                        continue;
                    var dx = particlePositions[ i * 3     ] - particlePositions[ j * 3     ];
                    var dy = particlePositions[ i * 3 + 1 ] - particlePositions[ j * 3 + 1 ];
                    var dz = particlePositions[ i * 3 + 2 ] - particlePositions[ j * 3 + 2 ];
                    var dist = Math.sqrt( dx * dx + dy * dy + dz * dz );
                    if ( dist < effectController.minDistance ) {
                        particleData.numConnections++;
                        particleDataB.numConnections++;
                        var alpha = 1.0 - dist / effectController.minDistance;
                        //var alpha = dist / effectController.minDistance;
                        positions[ vertexpos++ ] = particlePositions[ i * 3     ];
                        positions[ vertexpos++ ] = particlePositions[ i * 3 + 1 ];
                        positions[ vertexpos++ ] = particlePositions[ i * 3 + 2 ];
                        positions[ vertexpos++ ] = particlePositions[ j * 3     ];
                        positions[ vertexpos++ ] = particlePositions[ j * 3 + 1 ];
                        positions[ vertexpos++ ] = particlePositions[ j * 3 + 2 ];
                        colors[ colorpos++ ] = alpha;
                        colors[ colorpos++ ] = alpha;
                        colors[ colorpos++ ] = alpha;
                        colors[ colorpos++ ] = alpha;
                        colors[ colorpos++ ] = alpha;
                        colors[ colorpos++ ] = alpha;
                        numConnected++;
                    }else{
                        if(_value.isHoldMode) {

                            var offset_multi = 8.0;
                            if (dist > effectController.minDistance * offset_multi){
                                particleData.numConnections++;
                                particleDataB.numConnections++;
                                var alpha = (dist - (effectController.minDistance * offset_multi)) / (effectController.minDistance);
                                if (alpha > 1.0) alpha = 1.0;
                                if (alpha < 0) alpha = 0;
                                positions[vertexpos++] = particlePositions[i * 3];
                                positions[vertexpos++] = particlePositions[i * 3 + 1];
                                positions[vertexpos++] = particlePositions[i * 3 + 2];
                                positions[vertexpos++] = particlePositions[j * 3];
                                positions[vertexpos++] = particlePositions[j * 3 + 1];
                                positions[vertexpos++] = particlePositions[j * 3 + 2];
                                colors[colorpos++] = alpha;
                                colors[colorpos++] = alpha;
                                colors[colorpos++] = alpha;
                                colors[colorpos++] = alpha;
                                colors[colorpos++] = alpha;
                                colors[colorpos++] = alpha;
                                numConnected++;
                            }
                        }
                    }
                }
            }

        //console.log(numConnected, _value.particleCount);
        linesMesh.geometry.setDrawRange( 0, numConnected * 2 );
        linesMesh.geometry.attributes.position.needsUpdate = true;
        linesMesh.geometry.attributes.color.needsUpdate = true;
        pointCloud.geometry.attributes.position.needsUpdate = true;
        requestAnimationFrame( animate );
        stats.update();
        render();
    }

    function render() {

        //group.rotation.x = Math.radians(20);
        //group.rotation.z = Math.radians(20);

        if(_value.isHoldMode) {
            _count = ( _count + (1  * 0.5 ));

        }
        else                    {
            _count = ( _count + (1  * 0.1 ));

        }

        group.rotation.y = _count * 0.03;
        group_2.rotation.y = _count * 0.05;

        renderer.render( scene, camera );
    }

    function onClick(event){
        _value.isHoldMode = !_value.isHoldMode;
    }

    function onMouseDown(e){
        e.preventDefault();
        _value.isHoldMode = true;
        _over_callback.call(_value.isHoldMode);

        //TweenMax.to(scene.position, 1.2, {z:500, ease:Expo.easeOut});
        TweenMax.to(group.rotation, 1.2, {z:Math.radians(10), ease:Expo.easeOut});
        TweenMax.to(group_2.rotation, 1.2, {z:Math.radians(60), ease:Expo.easeOut});
        //TweenMax.to(group_2.position, 1.2, {z:-200, ease:Expo.easeOut})

    }

    function onMouseUp(e){
        e.preventDefault();
        _value.isHoldMode = false;
        _over_callback.call(_value.isHoldMode);

        //TweenMax.to(scene.position, 1.2, {z:0, ease:Expo.easeOut})
        TweenMax.to(group.rotation, 1.2, {z:0, ease:Expo.easeOut});
        TweenMax.to(group_2.rotation, 1.2, {z:0, ease:Expo.easeOut});
        //TweenMax.to(group_2.position, 1.2, {z:0, ease:Expo.easeOut})
    }

    return {
        init: init
    }
};