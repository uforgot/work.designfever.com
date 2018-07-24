
window.onload = function () {
    onResize();
    addEvent();
    init();
};

window.addEventListener('resize', onResize);


function onResize() {

}


//canvas
/*var canvas = document.getElementById('draw-canvas');
var ctx = canvas.getContext('2d');*/

function init() {
    animate()
}

function debugText(str){
    var debug = document.querySelector(".debug");
    debug.textContent = debug.innerHTML + " / " + str;
}

function addEvent(){
    // window.addEventListener('deviceorientation', handleOrientation);

    window.addEventListener('touchstart', onTouchStart);
    window.addEventListener('touchend', onTouchEnd);


}

function handleOrientation(event){
    var x = event.beta;  // In degree in the range [-180,180]
    var y = event.gamma;


}


var spd = 20;
var max_spd = 100;
var min_spd = 20;

var isPress = false;


function onTouchStart(e){
    isPress = true;
    // animate()

    var map = document.getElementById("color-ani");
    // map.setAttribute("dur", "1s");



}

function onTouchEnd(e){
    isPress = false;

    var map = document.getElementById("color-ani");
    // map.setAttribute("dur", "5s");
}


function animate() {
    window.requestAnimationFrame(animate);

    if (isPress) {
        spd += 5;
        if (spd > max_spd) spd = max_spd;
    } else {
        spd -= 2;
        if (spd < min_spd) spd = min_spd;
    }

    var map = document.getElementById("displacement-map");
    map.setAttribute("scale", spd);

    var time = document.getElementById("color-ani");
    // time.setAttribute("dur", (3+spd/100)+"s");


}

