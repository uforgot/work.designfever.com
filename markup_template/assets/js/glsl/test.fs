uniform vec2    u_resolution;
uniform float   u_time;

uniform float     alpha;
uniform vec2      speed;
uniform float     shift;

void main() {
    vec2 st = gl_FragCoord.xy/u_resolution.xy;

    vec3 c[4];
    //219 223 233
    c[0] = vec3(155.0/255.0, 182.0/255.0, 230.0/255.0);
    c[1] = vec3(187.0/255.0, 200.0/255.0, 225.0/255.0);
    c[2] = vec3(74.0/255.0, 95.0/255.0, 156.0/255.0);;
    c[3] = vec3(230.0/222.0, 204.0/255.0, 198.0/255.0);

    vec2 p[4];
    p[0] = vec2(0.3, 0.4);
    p[1] = vec2(0.6, 0.4);
    p[2] = vec2(0.5, -0.3);
    p[3] = vec2(cos(u_time), sin(u_time)) * 0.4 + vec2(0.5, 0.4);

    float blend = 2.0;

    float w[4];
    vec3 sum = vec3(0.0);
    float valence = 0.0;

    for (int i = 0; i < 4; i++) {
        float distance = length( st - p[i]);
        if (distance == 0.0) { distance = 1.0; }
        float w =  1.0 / pow(distance, blend);
        sum += w * c[i];
        valence += w;
    }

    sum /= valence;
    sum = pow(sum, vec3(1.0/2.2));

    // output
    gl_FragColor = vec4(sum.xyz, 1.0);
    //gl_FragColor=vec4(st.x,st.y,0.0,1.0);
}