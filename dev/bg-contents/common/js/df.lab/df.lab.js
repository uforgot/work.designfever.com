window.df = window.df || {};
window.df.lab = window.df.lab || {};

window.df.lab.Util = require("./df.lab.util");

// pollyfill

/* eslint-disable max-len */
/* Copyright (c) 2016 Tobias Buschor https://goo.gl/gl0mbf | MIT License https://goo.gl/HgajeK */
/* focusin/out event polyfill (firefox) */
(function () {
    const w = window;
    const d = w.document;

    function addPolyfill(e) {
        const type = e.type === 'focus' ? 'focusin' : 'focusout';
        const event = new CustomEvent(type, { bubbles: true, cancelable: false });
        event.c1Generated = true;
        e.target.dispatchEvent(event);
    }
    function removePolyfill(e) {
        if (!e.c1Generated) { // focus after focusin, so chrome will the first time trigger tow times focus in
            d.removeEventListener('focus', addPolyfill, true);
            d.removeEventListener('blur', addPolyfill, true);
            d.removeEventListener('focusin', removePolyfill, true);
            d.removeEventListener('focusout', removePolyfill, true);
        }
        setTimeout(() => {
            d.removeEventListener('focusin', removePolyfill, true);
            d.removeEventListener('focusout', removePolyfill, true);
        });
    }
    if (w.onfocusin === undefined) {
        d.addEventListener('focus', addPolyfill, true);
        d.addEventListener('blur', addPolyfill, true);
        d.addEventListener('focusin', removePolyfill, true);
        d.addEventListener('focusout', removePolyfill, true);
    }
}());

// requestAnimationFrame
(function () {
    let lastTime = 0;
    const vendors = ['ms', 'moz', 'webkit', 'o'];
    for (let x = 0; x < vendors.length && !window.requestAnimationFrame; ++x) {
        window.requestAnimationFrame = window[`${vendors[x]}RequestAnimationFrame`];
        window.cancelAnimationFrame = window[`${vendors[x]}CancelAnimationFrame`]
            || window[`${vendors[x]}CancelRequestAnimationFrame`];
    }

    if (!window.requestAnimationFrame) { window.requestAnimationFrame = function (callback, element) {
        var currTime = new Date().getTime();
        var timeToCall = Math.max(0, 16 - (currTime - lastTime));
        var id = window.setTimeout(
            function () { callback(currTime + timeToCall); },
            timeToCall,
        );
        lastTime = currTime + timeToCall;
        return id;
    };
    }

    if (!window.cancelAnimationFrame) { window.cancelAnimationFrame = function (id) {
        clearTimeout(id);
    };
    }
}());

