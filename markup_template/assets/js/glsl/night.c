uniform vec2    u_resolution;
uniform float   u_time;

uniform float     alpha;
uniform vec2      speed;
uniform float     shift;

#define S(a, b, t) smoothstep(a,b,t)


float N(float t) {
    return fract(sin(t*3456.)*6547.);
}

vec4 N14(float t) {
    return fract(sin(t*vec4(123., 1024., 3456., 9564.))*vec4(6547., 345., 8799., 1564.));
}

struct ray {
    vec3 o, d;
};

ray GetRay(vec2 uv, vec3 camPos, vec3 lookat, float zoom) {
    ray a;

    a.o = camPos;

    vec3 f = normalize(lookat - camPos);
    vec3 r = cross(vec3(0,1,0), f);
    vec3 u = cross(f, r);
    vec3 c = a.o + f * zoom;
    vec3 i = c + uv.x * r + uv.y * u;

    a.d = normalize(i-a.o);

    return a;
}

vec3 ClosetPoint(ray r, vec3 p) {
    return r.o +max(0., dot(p-r.o, r.d))*r.d;
}

float DistRay(ray r, vec3 p) {
    return length(p-ClosetPoint(r,p));
}

float Bokeh(ray r, vec3 p, float size, float blur) {
    float d = DistRay(r, p);

    size *= length(p);
    float c = S(size, size*(1.-blur), d);
    c *= mix(.6, 1., S(size*.8, size, d));

    return c;
}

vec3 Streetlights(ray r, float t) {
    float side = step(r.d.x, 0.);
    r.d.x = abs(r.d.x);
    const float s = 1./10.; // 0.1
    float m = 0.;

    for(float i=0.; i<1.; i+=s) {
        float ti = fract(t+i+side*s*.5);
        vec3 p = vec3(3.5, 3.5, 100.-ti*100.);
        m += Bokeh(r, p, .05, .1)*ti*ti*ti;
    }

    return vec3(1., .7, .3)*m;
}

vec3 Envlights(ray r, float t) {
    float side = step(r.d.x, 0.);
    r.d.x = abs(r.d.x);
    const float s = 1./10.; // 0.1
    float m = 0.;

    vec3 c = vec3(0.);

    for(float i=0.; i<1.; i+=s) {
        float ti = fract(t+i+side*s*.5);

        vec4 n = N14(i+side*100.);

        float fade = ti*ti*ti;

        float occlusion = sin(ti*6.28*10.*n.x)*.5+.5;
        fade = occlusion;

        float x = mix(2.5, 10., n.x);
        float y = mix(.1, 1.5, n.y);
        vec3 p = vec3(x, y, 50.-ti*50.);

        vec3 col = n.wzy;
        c += Bokeh(r, p, .05, .1)*fade*col*.5;
    }

    return c;
}

vec3 Headlights(ray r, float t) {

    t *= 2.;
    float w1 = .25;
    float w2 = w1*1.2;

    const float s = 1./30.; // 0.1
    float m = 0.;

    for(float i=0.; i<1.; i+=s) {

        float n = N(i);

        if(n>.1) continue;

        float ti = fract(t+i);
        float z = 100.-ti*100.;
        float fade = ti*ti*ti*ti*ti;
        float focus = S(.9, 1., ti);

        float size = mix(.05, .03, focus);

        m += Bokeh(r, vec3(-1.-w1,.15, z), size, .1)*fade;
        m += Bokeh(r, vec3(-1.+w1,.15, z), size, .1)*fade;

        m += Bokeh(r, vec3(-1.-w2,.15, z), size, .1)*fade;
        m += Bokeh(r, vec3(-1.+w2,.15, z), size, .1)*fade;

        float ref = 0.;
        ref += Bokeh(r, vec3(-1.-w2,-.15, z), size*3., 1.)*fade;
        ref += Bokeh(r, vec3(-1.+w2,-.15, z), size*3., 1.)*fade;

        m += ref*focus;
    }

    return vec3(.9, .9, 1.)*m;
}

vec3 Taillights(ray r, float t) {

    t *= .25;

    float w1 = .25;
    float w2 = w1*1.2;

    const float s = 1./15.; // 0.1
    float m = 0.;

    for(float i=0.; i<1.; i+=s) {

        float n = N(i);

        if(n>.5) continue;

        float lane = step(.25, n);


        float ti = fract(t+i);
        float z = 100.-ti*100.;
        float fade = ti*ti*ti*ti*ti;
        float focus = S(.9, 1., ti);

        float size = mix(.05, .03, focus);

        float laneShift = S(1., .96, ti);
        float x = 1.5 - lane * laneShift;

        float blink = step(0.,sin(t*1000.))*7.*lane*step(.96, ti);

        m += Bokeh(r, vec3(x-w1,.15, z), size, .1)*fade;
        m += Bokeh(r, vec3(x+w1,.15, z), size, .1)*fade;

        m += Bokeh(r, vec3(x-w2,.15, z), size, .1)*fade;
        m += Bokeh(r, vec3(x+w2,.15, z), size, .1)*fade*(1. +blink);

        float ref = 0.;
        ref += Bokeh(r, vec3(x-w2,-.15, z), size*3., 1.)*fade;
        ref += Bokeh(r, vec3(x+w2,-.15, z), size*3., 1.)*fade*(1. +blink*.1);

        m += ref*focus;
    }

    return vec3(1., .1, .03)*m;
}

void main() {
    vec2 uv = gl_FragCoord.xy / u_resolution.xy;
    uv -= .5;
    uv.x *= u_resolution.x / u_resolution.y;

    vec3 camPos = vec3(.5, .2, 0);
    vec3 lookat = vec3(.5, .2, 1.);

    ray r = GetRay(uv, camPos, lookat, 2.);

    float t = u_time*.1;
    vec3 col = Streetlights(r,t);
    col += Headlights(r,t);
    col += Taillights(r,t);
    col += Envlights(r,t);

    col += (r.d.y+.15)*vec3(.1,.1,.4);

    // output
    gl_FragColor = vec4(col, 1.);
}