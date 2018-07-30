console.clear();


function setting(){

}

let mesh;
let segment;
let spacingX = 5;
let spacingY = spacingX;

//canvas size
let stageW = window.innerWidth;
let stageH = window.innerHeight;

let resizePer = 1;

let imgIndex = 0;
let imgurlArr = ['./images/df_building_00.jpg'];

let textureArr = [];

let options = {
    image: imgurlArr[imgIndex],
    pointsX: 50,
    pointsY: 50,

    pointCount: 2000,

    reset(){
        if ( segment ) { segment.reset(); }
    },

    randomPoint(){
        if ( segment ) { segment.randomize(); }
    },

    nextImage(){
        changeNextImage();
    }
};

/*////////////////////////////////////////*/

/*let gui = new dat.GUI();
let nextImage = gui.add(options, 'nextImage');*/

let stage = new PIXI.Container();

PIXI.settings.RESOLUTION = window.devicePixelRatio;
let renderer = PIXI.autoDetectRenderer(stageW, stageH, { transparent: true });

let root = document.querySelector("#root");
root.appendChild(renderer.view);
renderer.render(stage);

let isTrans = false;





window.onresize = function(event) {

    // return;
    /*let debug = document.querySelector(".debug");
    debug.innerHTML = (window.innerWidth +  " / " +  window.innerHeight)

    console.log(window.innerWidth, window.innerHeight, renderer.width, renderer.view.width);*/

    stageW = window.innerWidth;
    stageH = window.innerHeight;

    renderer.view.width = stageW;
    renderer.view.height = stageH;
    renderer.resize(stageW, stageH)

    setSize();
    if(segment) segment.resize()

};


function setSize(){
    var curTexure = textureArr[imgIndex];

    resizePer = Math.max(stageH/curTexure.height, stageW/curTexure.width);

    mesh.width = curTexure.width * resizePer;
    mesh.height = curTexure.height * resizePer;

    mesh.x = ( stageW - mesh.width ) * 0.5;
    mesh.y = ( stageH - mesh.height ) * 0.5;

    spacingX = mesh.width / (options.pointsX-1);
    spacingY = mesh.height / (options.pointsY-1);

}



/*////////////////////////////////////////*/
loadTexture(options.image);

function loadTexture() {

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
            setPlane(texture);
        });
    } else {
        setPlane(textureArr[imgIndex]);
    }


}



function setPlane(texture){
    if ( mesh ) { stage.removeChild(mesh); }

    mesh = new PIXI.mesh.Plane( texture, options.pointsX, options.pointsY);

    setSize();

    segment = new Segment(options.pointsX-1, options.pointsY-1);

    stage.addChildAt(mesh,0);

    autoRollingStart();

    isTrans = true;

    var canvas = document.querySelector("canvas");
    TweenMax.to(canvas, 1, {opacity:1})
}



/*////////////////////////////////////////*/

;(function update() {
    requestAnimationFrame(update);
    if(!isTrans) return;
    if ( segment ) { segment.update() }
    renderer.render(stage);
})(0)

/*////////////////////////////////////////*/



/* **************************************
* controller
************************************** */

let timer;
let timerIdx = 0;

function autoRollingStart(){
    clearTimeout(timer);
    timer = setTimeout(imagePointRandom, 1500);

}

function autoRollingStop(){

}


function imageChange(){

}

function imagePointRandom(){
    var random = parseInt(Math.random() * 10 + 10);
    segment.randomize(random);
    autoRollingStart()
}

function imagePointReset(){

}




function changeNextImage(){
    clearTimeout(timer);
    imgIndex = (imgIndex+1) %  imgurlArr.length;
    options.image = imgurlArr[imgIndex];
    loadTexture();
}



root.addEventListener("touchstart", function(e){
    clearTimeout(timer);
    segment.reset();
});

root.addEventListener("touchend", function(e){
    imagePointRandom();
});


document.querySelector(".btn-next").addEventListener("touchend", function(e){
    changeNextImage();
});



/* **************************************
* Class
************************************** */

class Segment {
    constructor(segmentX, segmentY){
        this.points = [];

        for (let y = 0; y <= segmentY; y++) {
            for (let x = 0; x <= segmentX; x++) {
                let point = new Point(x, y, x * spacingX, y * spacingY);
                this.points.push(point)
            }
        }
    }

    resize(){
        this.points.forEach((point, i) => {
            point.resize();
            point.reset()
        })
    }


    randomize(range){
        this.points.forEach((point,i) => {
            point.randomize(range);
        })
    }

    update(delta){
        this.points.forEach(this.calcuFunc);
    }

    calcuFunc(point , idx){
        let index = idx * 2;

        var min = (options.pointsX*2);
        var max = ((options.pointsX*options.pointsY)*2) - min;

        if(index > min && index < max){
            mesh.vertices[index] = point.x/resizePer;
            mesh.vertices[index+1] = point.y/resizePer;
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
    constructor(indexX, indexY, x, y){
        this.indexX = indexX;
        this.indexY = indexY;

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
        this.x = this.origX = this.indexX * spacingX;
        this.y = this.origY = this.indexY * spacingY;
    }

    animateTo(nx, ny, force, callback) {
        let dx = nx - this.x;
        let dy = ny - this.y;

        let dist = Math.sqrt(dx * dx + dy * dy);

        let delay = !force ? Math.random()*.1 : Math.random()*0.2;
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













