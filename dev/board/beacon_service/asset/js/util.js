/*  ____________________________________________________________________________________________________________________

         .
     _|_|_    interactive lab
    (_| |     uforgot : 2015-07-10

 ____________________________________________________________________________________________________________________
 */
(function(ns, $) {

    var UTIL = (function () {

        var _getDegreeToMSC = function(degree) {
                var d, m, s;

                d = parseInt(degree);
                m = parseInt((degree - d) * 60);
                s = parseInt((degree - d - m / 60) * 3600);

                r = 1;

                if (degree < 0){
                    r = -1;
                }

                return {degree:d, minute:m, second:s, direction:r};
            },

            _getMSCToDegree = function(msc) {
                var d = msc.degree;
                var m = msc.minute;
                var s = msc.second;

                return d + (m/60) + (s/3600);
            },

            _getRgbTohsl = function(rgbArr) {
                var r1 = rgbArr[0] / 255;
                var g1 = rgbArr[1] / 255;
                var b1 = rgbArr[2] / 255;

                var maxColor = Math.max(r1, g1, b1);
                var minColor = Math.min(r1, g1, b1);
                //Calculate L:
                var L = (maxColor + minColor) / 2;
                var S = 0;
                var H = 0;
                if (maxColor != minColor) {
                    //Calculate S:
                    if (L < 0.5) {
                        S = (maxColor - minColor) / (maxColor + minColor);
                    } else {
                        S = (maxColor - minColor) / (2.0 - maxColor - minColor);
                    }
                    //Calculate H:
                    if (r1 == maxColor) {
                        H = (g1 - b1) / (maxColor - minColor);
                    } else if (g1 == maxColor) {
                        H = 2.0 + (b1 - r1) / (maxColor - minColor);
                    } else {
                        H = 4.0 + (r1 - g1) / (maxColor - minColor);
                    }
                }

                L = L * 100;
                S = S * 100;
                H = H * 60;
                if (H < 0) {
                    H += 360;
                }
                var result = {hue: H, situation: S, level: L};
                return result;
            },

            _gethslToRGB = function (h, s, l)
            {
                if( h=="" ) h=0;
                if( s=="" ) s=0;
                if( l=="" ) l=0;
                h = parseFloat(h);
                s = parseFloat(s);
                l = parseFloat(l);
                if( h<0 ) h=0;
                if( s<0 ) s=0;
                if( l<0 ) l=0;
                if( h>=360 ) h=359;
                if( s>100 ) s=100;
                if( l>100 ) l=100;
                s/=100;
                l/=100;
                C = (1-Math.abs(2*l-1))*s;
                hh = h/60;
                X = C*(1-Math.abs(hh%2-1));
                r = g = b = 0;
                if( hh>=0 && hh<1 )
                {
                    r = C;
                    g = X;
                }
                else if( hh>=1 && hh<2 )
                {
                    r = X;
                    g = C;
                }
                else if( hh>=2 && hh<3 )
                {
                    g = C;
                    b = X;
                }
                else if( hh>=3 && hh<4 )
                {
                    g = X;
                    b = C;
                }
                else if( hh>=4 && hh<5 )
                {
                    r = X;
                    b = C;
                }
                else
                {
                    r = C;
                    b = X;
                }
                m = l-C/2;
                r += m;
                g += m;
                b += m;
                r *= 255;
                g *= 255;
                b *= 255;
                r = Math.floor(r);
                g = Math.floor(g);
                b = Math.floor(b);
                hex = r*65536+g*256+b;
                hex = hex.toString(16,6);
                len = hex.length;
                if( len<6 )
                    for(i=0; i<6-len; i++)
                        hex = '0'+hex;

                return {red:r, green:g, blue:b};
            },

            _getRgbToCMYK = function (r,g,b) {
                var computedC = 0;
                var computedM = 0;
                var computedY = 0;
                var computedK = 0;

                //remove spaces from input RGB values, convert to int
                var r = parseInt( (''+r).replace(/\s/g,''),10 );
                var g = parseInt( (''+g).replace(/\s/g,''),10 );
                var b = parseInt( (''+b).replace(/\s/g,''),10 );

                if ( r==null || g==null || b==null ||
                    isNaN(r) || isNaN(g)|| isNaN(b) )
                {
                    alert ('Please enter numeric RGB values!');
                    return;
                }
                if (r<0 || g<0 || b<0 || r>255 || g>255 || b>255) {
                    alert ('RGB values must be in the range 0 to 255.');
                    return;
                }

                // BLACK
                if (r==0 && g==0 && b==0) {
                    computedK = 1;
                    return [0,0,0,1];
                }

                computedC = 1 - (r/255);
                computedM = 1 - (g/255);
                computedY = 1 - (b/255);

                var minCMY = Math.min(computedC,
                    Math.min(computedM,computedY));
                computedC = (computedC - minCMY) / (1 - minCMY) ;
                computedM = (computedM - minCMY) / (1 - minCMY) ;
                computedY = (computedY - minCMY) / (1 - minCMY) ;
                computedK = minCMY;


                computedC = _getIntPercent(computedC);
                computedM = _getIntPercent(computedM);
                computedY = _getIntPercent(computedY);
                computedK = _getIntPercent(computedK);

                return {c:computedC,m:computedM,y:computedY,k:computedK};
            },

            _getIntPercent = function(number)
            {
                return parseInt(number * 100);
            },

            _getGradedValue = function(beginColor, endColor, percent) {
                var r = beginColor.red + parseInt(percent * (endColor.red - beginColor.red));
                var g = beginColor.green + parseInt(percent * (endColor.green - beginColor.green));
                var b = beginColor.blue + parseInt(percent * (endColor.blue - beginColor.blue));

                return {red:r, green:g, blue:b};
            },


            _getZeroDecimal = function(number, counter) {
                var value = String(Math.abs(number));
                var returnValue = "";

                var zeroCounter = counter - value.length;

                for (var i=0;i<zeroCounter; i++ ){
                    returnValue += "0";
                }

                returnValue += value;

                return returnValue;
            },

            _getHue = function(color) {
               return (color+195-126)%360;
            },

            _getRGBA = function(color) {
                return "rgba(" + color.red + ", " + color.green + ", " + color.blue + ", 1.0)";
            },

            _getFixedDegree = function(degree) {
                while(degree<0) {
                    degree += 360;
                }

                while(degree>360) {
                    degree -= 360;
                }

                return degree;
            },

            _rotateCanvas = function(el, degrees, speed, delay, direction) {
                degrees = SMARTCITY.UTIL.getFixedDegree(degrees);

                TweenLite.killTweensOf($(el));
                //TweenLite.to($(el), speed, {rotation:"'" + degrees +"_short'", delay:delay, ease:Cubic.easeOut});
                //TweenLite.to($(el), speed, {rotation:"'" + degrees +"_cw'", delay:delay, ease:Cubic.easeOut});

                TweenLite.to($(el), speed, {rotation:"'" + degrees + "_" + direction + "'", delay:delay, ease:Cubic.easeOut});

            },

            _dummy = function () {

            }

        return {
            getDegreeToMSC : _getDegreeToMSC,
            getMSCToDegree : _getMSCToDegree,
            getRgbTohsl : _getRgbTohsl,
            gethslToRgb : _gethslToRGB,
            getRgbToCMYK : _getRgbToCMYK,
            getGradedValue : _getGradedValue,
            getZeroDecimal : _getZeroDecimal,
            getHue : _getHue,
            getRGBA : _getRGBA,
            getFixedDegree : _getFixedDegree,
            rotateCanvas: _rotateCanvas,

            dummy: _dummy
        };

    })();

    ns.UTIL = UTIL;
}(SMARTCITY || {}, $));

