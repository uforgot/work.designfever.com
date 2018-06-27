var Shader = (function(){

    var xhrVertexShader;
    var xhrFragmentShader;

    var vertexShader = null;
    var fragmentShader = null;

    var width;
    var height;

    var container;
    var camera, scene, renderer;
    var uniforms;

    var _init = function ($vertexShader, $fragShader){
        $(window).on('resize',onWindowResize);
        onWindowResize();
        container = document.getElementById('shader-box');

        xhrFragmentShader = new XMLHttpRequest();
        xhrFragmentShader.open('GET', $fragShader, true);
        xhrFragmentShader.responseType = 'text';

        xhrFragmentShader.onload = function() {
            if(xhrFragmentShader.readyState === xhrFragmentShader.DONE && xhrFragmentShader.status === 200)
            {
                fragmentDone(xhrFragmentShader.responseText);
            }
        };

        xhrVertexShader = new XMLHttpRequest();
        xhrVertexShader.open('GET', $vertexShader, true);
        xhrVertexShader.responseType = 'text';

        xhrVertexShader.onload = function() {
            if(xhrVertexShader.readyState === xhrVertexShader.DONE && xhrVertexShader.status === 200)
            {
                vertexDone(xhrVertexShader.responseText);
            }
        };

        xhrVertexShader.send(null);
        xhrFragmentShader.send(null);
    };

    var fragmentDone = function(code) {
        fragmentShader = code;

        if (vertexShader !== null) {
            init3D();
        }
    };


    var vertexDone = function(code) {
        vertexShader = code;

        if (fragmentShader !== null) {
            init3D();
        }
    };


    var init3D = function() {
        camera = new THREE.Camera();
        camera.position.z = 1;

        scene = new THREE.Scene();

        var geometry = new THREE.PlaneBufferGeometry( 2, 2 );

        uniforms = {
            u_time: { type: "f", value: 1.0 },
            u_resolution: { type: "v2", value: new THREE.Vector2() }
        };

        var material = new THREE.ShaderMaterial( {
            uniforms: uniforms,
            vertexShader: vertexShader,
            fragmentShader: fragmentShader
        } );

        var mesh = new THREE.Mesh( geometry, material );
        scene.add( mesh );

        renderer = new THREE.WebGLRenderer();
        // renderer.setPixelRatio( window.devicePixelRatio );
        container.appendChild( renderer.domElement );

        // onWindowResize();
        // window.addEventListener( 'resize', onWindowResize, false );
        animate();

    };

    function onWindowResize( event ) {
        console.log('resize');
        width = $(window).width();
        height = $(window).height();

    }

    function animate() {
        render();
        requestAnimationFrame( animate );
    }

    function render() {
        renderer.setSize(width, height);
        uniforms.u_resolution.value.x = width;
        uniforms.u_resolution.value.y = height;

        uniforms.u_time.value += 0.02;
        renderer.render( scene, camera );
    }

    return{
        init:_init
    }

})();


$(window).on('load',function(){
    Shader.init(
        '/assets/js/glsl/test.vs',
        '/assets/js/glsl/drive.c'
    );
});










