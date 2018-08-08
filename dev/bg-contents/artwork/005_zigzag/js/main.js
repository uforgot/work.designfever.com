


var ZigZag = function(args){

    let mesh;
    let segment;
    let resizePer = 1;

    let imgIndex = 0;
    let imgurlArr = [args.imageurl];
    let contentOpacity = args.opacity || 1;

    let textureArr = [];

    let options = {
        image: imgurlArr[imgIndex],
        pointsX: 50,
        pointsY: 50,
        spacingX : 5,
        spacingY : 5,

        pointCount: 2000
    };


    let stage = new PIXI.Container();
    PIXI.settings.RESOLUTION = window.devicePixelRatio;
    let renderer = PIXI.autoDetectRenderer(stageW, stageH, { transparent: true });

    let root = document.querySelector("#root");
    let isTrans = false;
    let timer;

    var _setting = function(){
        root.appendChild(renderer.view);
        renderer.render(stage);

        _loadImage(options.image)
    };

    var _init = function(){

    };

    var _resize = function(){
        renderer.view.width = stageW;
        renderer.view.height = stageH;
        renderer.resize(stageW, stageH);

        _resizeContents();
        if(segment) segment.resize(resizePer);
    };

    var _resizeContents = function(){
        var curTexure = textureArr[imgIndex];

        resizePer = Math.max(stageH/curTexure.height, stageW/curTexure.width);

        mesh.width = curTexure.width * resizePer;
        mesh.height = curTexure.height * resizePer;

        mesh.x = ( stageW - mesh.width ) * 0.5;
        mesh.y = ( stageH - mesh.height ) * 0.5;

        options.spacingX = mesh.width / (options.pointsX-1);
        options.spacingY = mesh.height / (options.pointsY-1);
    };



    var _loadImage = function(){
        isTrans = false;
        document.body.className = 'loading';

        if(!textureArr[imgIndex]){
            let texture = new PIXI.Texture.fromImage(options.image);
            if ( !texture.requiresUpdate ) { texture.update(); }

            texture.on('error', function(){ this.off('error'); console.error('AGH!'); });

            texture.on('update',function(texture){
                document.body.className = '';
                textureArr.push(texture)
                texture.off('update');
                _setPlane(texture);
            });
        } else {
            _setPlane(textureArr[imgIndex]);
        }
    };

    var _setPlane = function(texture){
        if ( mesh ) { stage.removeChild(mesh); }
        mesh = new PIXI.mesh.Plane( texture, options.pointsX, options.pointsY);
        // setSize();
        _resizeContents();
        // segment = new Segment(options.pointsX-1, options.pointsY-1);
        segment = new Segment(options, mesh, resizePer);
        stage.addChildAt(mesh,0);
        autoRollingStart();
        isTrans = true;

        var canvas = document.querySelector("canvas");
        TweenMax.to(canvas, 1, {opacity:contentOpacity});
        _update();
    };

    var _update = function(){
        requestAnimationFrame(_update);
        if(!isTrans) return;
        if ( segment ) { segment.update() }
        renderer.render(stage);
    };






    var autoRollingStart = function(){
        clearTimeout(timer);
        timer = setTimeout(imagePointRandom, options.pointCount);

    };

    var imagePointRandom = function(){
        var random = parseInt(Math.random() * 30 + 5);
        segment.randomize(random);
        autoRollingStart()
    };


    //spacingX, resizePer, mesh

    return {
        setting : _setting,
        resize : _resize
    }
}








/* **************************************
* Class
************************************** */

class Segment {
    constructor(options, mesh, resizePer){
        this.points = [];
        this.resizePer = 1;
        this.options = options;
        this.mesh = mesh;
        this.resizePer = resizePer;

        for (let y = 0; y <= this.options.pointsY-1; y++) {
            for (let x = 0; x <= this.options.pointsX-1; x++) {
                let point = new Point(x, y, x * this.options.spacingX, y * this.options.spacingY, this.options);
                this.points.push(point)
            }
        }
    }


    resize(resizePer){
        this.resizePer = resizePer;

        this.points.forEach((point, i) => {
            point.resize();
            point.reset();
        })
    }


    randomize(range){
        this.points.forEach((point,i) => {
            point.randomize(range);
        })
    }

    update(delta){
        var _this = this;
        this.points.forEach((point,i) => {
            _this.calcuFunc(point, i);
        })
    }

    calcuFunc(point , idx){

        let index = idx * 2;

        var min = (this.options.pointsX*2);
        var max = ((this.options.pointsX * this.options.pointsY)*2) - min;

        if(index > min && index < max){
            this.mesh.vertices[index] = point.x/this.resizePer;
            this.mesh.vertices[index+1] = point.y/this.resizePer;
        }
    }

    reset(){
        this.points.forEach((point) => {
            point.reset()
        })
    }


}


const ease = Elastic.easeOut.config(2, 0.4);

class Point {
    constructor(indexX, indexY, x, y, options){
        this.indexX = indexX;
        this.indexY = indexY;
        this.options = options;

        this.init(x, y);
    }

    init(x, y){
        this.x = this.origX = x;
        this.y = this.origY = y;
        this.randomize();

        var randomGap = 300;
        this.x = this.origX + (Math.random() * randomGap - (randomGap/2));
        this.y = this.origY + (Math.random() * randomGap - (randomGap/2));
    }

    resize(){
        // this.options = options;
        // this.resizePer = this.options.resizePer;

        this.x = this.origX = this.indexX * this.options.spacingX;
        this.y = this.origY = this.indexY * this.options.spacingY;
    }

    animateTo(nx, ny, force, callback) {
        let dx = nx - this.x;
        let dy = ny - this.y;

        let dist = Math.sqrt(dx * dx + dy * dy);

        let delay = !force ? Math.random()*.2 : Math.random()*0.2;
        let time = Math.min(1.25, Math.max(0.4, dist / 40) );

        TweenMax.killTweensOf(this);
        TweenMax.to(this, time*2, {x: nx, y: ny, ease:ease, delay:delay});
    }


    randomize(range){
        let gap = 10;
        if(range) gap = range;

        let nx = this.origX + ((Math.random() * gap) - gap*0.5);
        let ny = this.origY + ((Math.random() * gap) - gap*0.5);

        this.animateTo(nx, ny);
    }

    reset(){
        this.animateTo(this.origX, this.origY, true);
    }

}













