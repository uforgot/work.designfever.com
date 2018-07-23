var Util = {};
Util.color = (function(){

    var rgbToHex = function(r, g, b) {
        return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
    };
    var hexToRgb = function (hex) {
        // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
        var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
        hex = hex.replace(shorthandRegex, function(m, r, g, b) {
            return r + r + g + g + b + b;
        });

        var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
        } : null;
    }

    return {
        rgbToHex : rgbToHex,
        hexToRgb : hexToRgb
    }
})();

Util.combine_object_value = function(target, def){

    var obj = {};

    if(def) {
        for (var p in def) {
            obj[p] = def[p];
        }
    }

    if(target) {
        for (var p in target) {
            obj[p] = target[p];
        }
    }

    return obj;
};

Util.hasClass = function(el, className) {
    if (el.classList)
        return el.classList.contains(className);
    else
        return !!el.className.match(new RegExp('(\\s|^)' + className + '(\\s|$)'));

    return Util;
};

Util.addClass = function(el, className) {
    if (el.classList)
        el.classList.add(className);
    else if (!hasClass(el, className)) el.className += " " + className;

    return Util;
};

Util.removeClass = function(el, className) {
    if (el.classList)
        el.classList.remove(className)
    else if (hasClass(el, className)) {
        var reg = new RegExp('(\\s|^)' + className + '(\\s|$)');
        el.className=el.className.replace(reg, ' ');
    }
    return Util;
};

Util.getParams = function(){

    var _params = {};

    var queryString = window.location.search || '';
    var keyValPairs = [];
    queryString     = queryString.substr(1);
    if (queryString.length)
    {
        keyValPairs = queryString.split('&');
        for (var pairNum in keyValPairs)
        {
            var key = keyValPairs[pairNum].split('=')[0];
            if (!key.length) continue;
            if (typeof _params[key] === 'undefined')
                _params[key] = [];
            _params[key].push(keyValPairs[pairNum].split('=')[1]);
        }
    }

    return _params;
};

// Converts from degrees to radians.
Math.radians = function(degrees) {
    return degrees * Math.PI / 180;
};

// Converts from radians to degrees.
Math.degrees = function(radians) {
    return radians * 180 / Math.PI;
};

module.exports = Util;