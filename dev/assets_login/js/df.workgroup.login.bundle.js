!function(e){var t={};function o(n){if(t[n])return t[n].exports;var a=t[n]={i:n,l:!1,exports:{}};return e[n].call(a.exports,a,a.exports,o),a.l=!0,a.exports}o.m=e,o.c=t,o.d=function(e,t,n){o.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:n})},o.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},o.t=function(e,t){if(1&t&&(e=o(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var n=Object.create(null);if(o.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var a in e)o.d(n,a,function(t){return e[t]}.bind(null,a));return n},o.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return o.d(t,"a",t),t},o.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},o.p="",o(o.s=14)}([function(e,t){e.exports=function(e,t){var o=e,n={objectName:"ConectedLines_"+(new Date).getTime(),container:document.body,stageWidth:1920,stageHeight:1080},a={},r={count:0,oW:0,oH:0,clock:{hh:0,mm:0,ss:0}},i={render:{},app:{},mainContainer:{},clockContainer:{},clockGraphic:{bar_hh:{},bar_mm:{},bar_ss:{}},txt_hh:{},txt_mm:{},txt_ss:{}},s=null;var l=function(e){a=df.lab.Util.combine_object_value(e,n),r.oW=a.stageWidth,r.oH=a.stageHeight,m(),i.mainContainer=new PIXI.Container,i.clockContainer=new PIXI.Container,i.app.stage.addChild(i.mainContainer),i.render=new PIXI.ticker.Ticker,i.render.autoStart=!0,i.clockGraphic.bar_hh=new PIXI.Graphics,i.clockGraphic.bar_mm=new PIXI.Graphics,i.clockGraphic.bar_ss=new PIXI.Graphics,i.clockContainer.rotation=Math.radians(-360),i.clockGraphic.bar_hh.alpha=0,i.clockGraphic.bar_mm.alpha=0,i.clockGraphic.bar_ss.alpha=0,i.clockGraphic.bar_hh.scale.x=.1,i.clockGraphic.bar_mm.scale.x=.1,i.clockGraphic.bar_ss.scale.x=.1,u(),f(a.stageWidth,a.stageHeight),a.container.appendChild(i.app.view)},d=function(){window.onresize=function(e){c()},window.addEventListener("orientationchange",function(){clearTimeout(s),s=setTimeout(c,1e3)},!1)};function c(){r.oW==a.container.offsetWidth&&r.oH==a.container.offsetHeight||(r.oW=a.container.offsetWidth,r.oH=a.container.offsetHeight,f(r.oW,r.oH))}var u=function(){var e=new PIXI.TextStyle({fontFamily:"NanumSquareRound",fontSize:20,fontWeight:"700",fill:["#ffffff"]});i.txt_hh=new PIXI.Text("00",e);var t=new PIXI.TextStyle({fontFamily:"NanumSquareRound",fontSize:14,fontWeight:"700",fill:["#ffffff"]});i.txt_mm=new PIXI.Text("00",t);var o=new PIXI.TextStyle({fontFamily:"NanumSquareRound",fontSize:14,fontWeight:"700",fill:["#ff0000"]});i.txt_ss=new PIXI.Text("00",o),i.txt_hh.alpha=0,i.txt_mm.alpha=0,i.txt_ss.alpha=0},f=function(e,t){a.stageWidth=e||a.stageWidth,a.stageHeight=t||a.stageHeight,i.app.view.style.width=a.stageWidth+"px",i.app.view.style.height=a.stageHeight+"px",function(){var e=a.stageWidth/2,t=a.stageHeight/2;i.clockContainer.x=e,i.clockContainer.y=t;var o=Math.min(e,t),n=o-58,r=o-58,s=Math.round(.74*r);i.clockGraphic.bar_hh.clear(),i.clockGraphic.bar_hh.beginFill(16777215),i.clockGraphic.bar_hh.drawRoundedRect(-3,-3,s+3,6,3),i.clockGraphic.bar_hh.endFill(),i.clockGraphic.bar_mm.clear(),i.clockGraphic.bar_mm.beginFill(16777215),i.clockGraphic.bar_mm.drawRoundedRect(-3,-3,r+3,6,3),i.clockGraphic.bar_mm.endFill(),i.clockGraphic.bar_ss.clear(),i.clockGraphic.bar_ss.beginFill(16711680),i.clockGraphic.bar_ss.drawRoundedRect(-1,-1,n+1,2,1),i.clockGraphic.bar_ss.endFill()}(),i.app.renderer.resize(a.stageWidth,a.stageHeight)},m=function(){PIXI.settings.RESOLUTION=window.devicePixelRatio,PIXI.settings.SCALE_MODE=PIXI.SCALE_MODES.NEAREST,i.app=new PIXI.Application(a.stageWidth,a.stageHeight,{transparent:!0,antialias:!0})},w=function(){i.mainContainer.addChild(i.clockContainer),i.clockContainer.addChild(i.clockGraphic.bar_ss),i.clockContainer.addChild(i.clockGraphic.bar_mm),i.clockContainer.addChild(i.clockGraphic.bar_hh),i.mainContainer.addChild(i.txt_hh),i.mainContainer.addChild(i.txt_mm),h();TweenMax.to(i.clockContainer,5,{rotation:0,ease:Expo.easeOut,delay:0}),TweenMax.to(i.clockGraphic.bar_hh,2.2,{alpha:1,ease:Expo.easeInOut,delay:0}),TweenMax.to(i.clockGraphic.bar_mm,2.2,{alpha:1,ease:Expo.easeInOut,delay:.5}),TweenMax.to(i.clockGraphic.bar_ss,2.2,{alpha:1,ease:Expo.easeInOut,delay:1}),TweenMax.to(i.clockGraphic.bar_hh.scale,2.2,{x:1,ease:Expo.easeInOut,delay:0}),TweenMax.to(i.clockGraphic.bar_mm.scale,2.2,{x:1,ease:Expo.easeInOut,delay:.5}),TweenMax.to(i.clockGraphic.bar_ss.scale,2.2,{x:1,ease:Expo.easeInOut,delay:.5}),TweenMax.to(i.txt_hh,1.2,{alpha:1,ease:Cubic.easeOut,delay:2}),TweenMax.to(i.txt_mm,1.2,{alpha:1,ease:Cubic.easeOut,delay:2.5}),TweenMax.to(i.txt_ss,1.2,{alpha:1,ease:Cubic.easeOut,delay:3})},h=function(){i.render.add(function(e){p(),g()})},p=function(){r.count=r.count+1},g=function(){var e,t,o,n=r.clock.ss/60*360;o=Math.radians((n-90)%360);var s=r.clock.mm/60*360;t=Math.radians((s-90)%360);var l=r.clock.hh%12/12*360+r.clock.mm/60*30;e=Math.radians((l-90)%360),i.clockGraphic.bar_hh.rotation=e,i.clockGraphic.bar_mm.rotation=t,i.clockGraphic.bar_ss.rotation=o;var d=a.stageWidth/2,c=a.stageHeight/2,u=Math.min(d,c),f=u-58,m=u-58,w=new PIXI.Point,h=new PIXI.Point,p=new PIXI.Point;i.txt_hh.text=r.clock.hh%12==0?"12":r.clock.hh%12,i.txt_mm.text=window.df.workgroup.Util.addZeroNumber(r.clock.mm),i.txt_ss.text=window.df.workgroup.Util.addZeroNumber(r.clock.ss),w.x=d+Math.cos(e)*(f+20)-i.txt_hh.width/2,w.y=c+Math.sin(e)*(f+20)-i.txt_hh.height/2,i.txt_hh.x=w.x,i.txt_hh.y=w.y,h.x=d+Math.cos(t)*(f+44)-i.txt_mm.width/2,h.y=c+Math.sin(t)*(f+44)-i.txt_mm.height/2,i.txt_mm.x=h.x,i.txt_mm.y=h.y,p.x=d+Math.cos(o)*(m+44)-i.txt_ss.width/2,p.y=c+Math.sin(o)*(m+44)-i.txt_ss.height/2,i.txt_ss.x=p.x,i.txt_ss.y=p.y};function v(e){r.clock.hh=e.hh,r.clock.mm=e.mm,r.clock.ss=e.ss}return{init:function(e){l({container:o,stageWidth:o.offsetWidth,stageHeight:o.offsetHeight}),v(e),d(),w()},updateToday:v}}},function(e,t){e.exports=function(e,t){var o=e,n={txt_MM:"",txt_DD:"",txt_DW:""},a={MM:0,DD:0,DW:0},r="";function i(){df.lab.Util.removeClass(o,window.df.workgroup.Preset.class_name.showIn),clearTimeout(r),r=setTimeout(function(){df.lab.Util.addClass(o,window.df.workgroup.Preset.class_name.showIn)},1e3)}function s(e){a.MM=e.MM,a.DD=e.DD,a.DW=e.DW;n.txt_MM.textContent=["January","February ","March ","April","May","June","July","August","September","October","November","December"][a.MM],n.txt_DD.textContent=a.DD<10?"0"+a.DD:a.DD,n.txt_DW.textContent=["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"][a.DW]}return{init:function(e){n.txt_MM=document.getElementById("id_txt_MM"),n.txt_DD=document.getElementById("id_txt_DD"),n.txt_DW=document.getElementById("id_txt_DW"),i(),s(e)},updateToday:function(e){a.MM==e.MM&&a.DD==e.DD&&a.DW==e.DW||(i(),s(e))}}}},function(e,t){e.exports=function(e,t){var o,n="[ LoginBgController ]",a=(e=e,[]);return{init:function(){if(function(e){var o=df.lab.Util.getParams();if(t){var r=e.info.test.bg_contents,i=e.info.today.bg_contents,s=e.info.birthday;void 0!=r&&null!=r&&r.length>0&&"true"==o.test?(console.log(n+" : ","type : ","test bg"),a=a.concat(r)):void 0!=i&&null!=i&&i.length>0?(console.log(n+" : ","type : ","custom bg"),a=a.concat(i)):void 0!=s&&null!=s&&s.length>0?(console.log(n+" : ","type : ","Birthday bg"),a=a.concat(e.preset.bg_contents.birthday.list)):(console.log(n+" : ","type : ","random bg"),a=(a=a.concat(e.preset.bg_contents.weather.list)).concat(e.preset.bg_contents.artwork.list)),console.log(n+" : ","arr_bg_list : ",a)}}(t),e&&(o=e.querySelector(".dim")),a.length>0){var r=Math.floor(a.length*Math.random()),i=a[r].url;console.log(n+" : ","iframe url : ",r," / ",i),function(t){if(e){var n=document.createElement("iframe");n.setAttribute("src",t),n.setAttribute("name","iframe-bg"),e.appendChild(n),setTimeout(function(){o&&df.lab.Util.addClass(o,window.df.workgroup.Preset.class_name.showIn)},2e3)}}(i)}}}}},function(e,t){e.exports=function(){var e,t,o,n,a=13,r=9,i="[ LoginFieldController ]",s=document.getElementById("id_login"),l=0;function d(){void 0!=o&&null!=o||(e.focus(),e.select())}function c(){clearTimeout(l)}function u(e){switch(e.which){case a:t.focus();break;case r:console.log("ID")}}function f(e){e.preventDefault(),function(){if(s.user_id.value.length<3||s.user_id.value.length>16)return s.user_id.focus(),!1;if(s.user_pw.value.length<4||s.user_pw.value.length>16)return s.user_pw.focus(),!1;console.log(i," load json"),document.getElementById("user_pw").blur(),m(),function(e,t){for(var o={},n=0,a=e.length;n<a;++n){var r=e[n];r.name&&(o[r.name]=r.value)}var i={method:e.method,action:df.workgroup.Util.addParamUniq(e.action)};df.workgroup.Util.load_json(i.action,i.method,t,o)}(s,h)}()}function m(){var e=document.querySelector(".sec-login");df.lab.Util.addClass(e,"loading");for(var t=s.querySelectorAll("input"),o=0;o<t.length;o++)t[o].setAttribute("disabled","")}function w(){var e=document.querySelector(".sec-login");df.lab.Util.removeClass(e,"loading");for(var t=s.querySelectorAll("input"),o=0;o<t.length;o++)t[o].removeAttribute("disabled")}function h(e){w(),function(e){var t=new CustomEvent(window.df.workgroup.Preset.eventType.ON_LOGIN,{detail:{response:e}});document.dispatchEvent(t)}(e);var t=function(e){var t={isWarning:!1,text:"표시할 메세지가 없습니다.",code:null},o=JSON.parse(e.target.responseText),n=o.user.status;if(n.toLowerCase()=="L01".toLowerCase()||n.toLowerCase()=="L02".toLowerCase()||n.toLowerCase()=="L03".toLowerCase())for(var a=o.preset.status_list,r=0;r<a.length;r++){var i=a[r],s=i.code;if(s.toLowerCase()==n.toLowerCase()){t.isWarning=!0,t.text=i.text,t.code=i.code;break}}return t}(e);t.isWarning&&(!function(e){var t=new CustomEvent(window.df.workgroup.Preset.eventType.ON_WARNING,{detail:{message:e}});document.dispatchEvent(t)}(t.text),"L01"==t.code||"L03"==t.code?document.addEventListener(window.df.workgroup.Preset.eventType.ON_CLOSE_MODAL,p):"L02"==t.code&&document.addEventListener(window.df.workgroup.Preset.eventType.ON_CLOSE_MODAL,g))}function p(){document.removeEventListener(window.df.workgroup.Preset.eventType.ON_CLOSE_MODAL,p),d()}function g(){document.removeEventListener(window.df.workgroup.Preset.eventType.ON_CLOSE_MODAL,g),void 0!=n&&null!=n||(t.focus(),t.select())}return{init:function(){e=document.getElementById("user_id"),t=document.getElementById("user_pw"),function(){var e=window.df.workgroup.GlobalVars.infoData;void 0!=e.preset&&void 0!=e.preset.json_url&&void 0!=e.preset.json_url.login?s.action=e.preset.json_url.login:s.action=window.df.workgroup.Preset.json_url.login}(),e.addEventListener("keypress",u),s.addEventListener("submit",f),t.addEventListener("focusin",c),l=setTimeout(d,1e3)},hideLoginFrom:function(){m()},showLoginFrom:function(){w()}}}},function(e,t){e.exports=function(){var e=document.getElementById("id_form_logout");function t(){var t=window.df.workgroup.GlobalVars.infoData;void 0!=t.preset&&void 0!=t.preset.json_url&&void 0!=t.preset.json_url.logout?e.action=t.preset.json_url.logout:e.action=window.df.workgroup.Preset.json_url.logout}function o(t){t.preventDefault(),n(),function(e,t){for(var o={},n=0,a=e.length;n<a;++n){var r=e[n];r.name&&(o[r.name]=r.value)}var i={method:e.method,action:df.workgroup.Util.addParamUniq(e.action)};df.workgroup.Util.load_json(i.action,i.method,t,o)}(e,a)}function n(){for(var t=e.querySelectorAll("input"),o=0;o<t.length;o++)t[o].setAttribute("disabled",""),df.lab.Util.addClass(t[o],"disable")}function a(e){!function(e){var t=new CustomEvent(window.df.workgroup.Preset.eventType.ON_LOGOUT,{detail:{response:e}});document.dispatchEvent(t)}(e);var t=function(e){var t={isWarning:!1,text:"표시할 메세지가 없습니다."},o=JSON.parse(e.target.responseText),n=o.user.status;if(n.toLowerCase()=="L04".toLowerCase())for(var a=o.preset.status_list,r=0;r<a.length;r++){var i=a[r],s=i.code;if(s.toLowerCase()==n.toLowerCase()){t.isWarning=!0,t.text=i.text;break}}return t}(e);t.isWarning&&(!function(e){var t=new CustomEvent(window.df.workgroup.Preset.eventType.ON_WARNING,{detail:{message:e}});document.dispatchEvent(t)}(t.text),document.addEventListener(window.df.workgroup.Preset.eventType.ON_CLOSE_MODAL,r))}function r(){document.removeEventListener(window.df.workgroup.Preset.eventType.ON_CLOSE_MODAL,r),setTimeout(function(){window.location.reload(!0)},50)}return{init:function(){t(),e.addEventListener("submit",o)},showLogoutBtn:function(){!function(){for(var t=e.querySelectorAll("input"),o=0;o<t.length;o++)t[o].removeAttribute("disabled"),df.lab.Util.removeClass(t[o],"disable")}(),t();var o=document.querySelector("header .wrapper-logout");df.lab.Util.addClass(o,window.df.workgroup.Preset.class_name.showIn)},hideLogoutBtn:function(){n();var e=document.querySelector("header .wrapper-logout");df.lab.Util.removeClass(e,window.df.workgroup.Preset.class_name.showIn)}}}},function(e,t){e.exports=function(){var e=document.querySelector(".sec-login .wrapper-checkin"),t=e.querySelector(".area-check-inout.area-checkin"),o=e.querySelector(".area-check-inout.area-checkout"),n=document.getElementById("id_checkin"),a=document.getElementById("id_checkout"),r=document.getElementById("id_btn_checkout_re"),i=0,s=null;function l(){if(s=window.df.workgroup.GlobalVars.infoData.user,document.getElementById("id_user_name").textContent=s.name,document.getElementById("id_user_position").textContent=s.position,s.isLoggedIn)if(s.isCheckin){var e=document.getElementById("id_checkin_time"),t=new Date(s.checkin_time);e.textContent=t.getHours()+"시 "+window.df.workgroup.Util.addZeroNumber(t.getMinutes())+"분";var o=document.getElementById("id_checkout_able_time"),n=new Date(s.checkout_able_time);if(o.textContent=n.getHours()+"시 "+window.df.workgroup.Util.addZeroNumber(n.getMinutes())+"분",!0,s.isCheckout){c();var a=document.getElementById("id_checkout_time"),r=new Date(s.checkout_time),l=r.getHours();r.getDate()-t.getDate()>0&&(l=24*(r.getDate()-t.getDate())+r.getHours()),a.textContent=l+"시 "+window.df.workgroup.Util.addZeroNumber(r.getMinutes())+"분"}else c(),d(),i=setInterval(d,1e3)}else c();else c()}function d(){if(s.isCheckin){var e=s.checkout_able_time-s.checkin_time,t=(window.df.workgroup.GlobalVars.time_now-s.checkin_time)/e;t<0&&(t=0),t>1&&(t=1);var n=Math.round(100*t)+"%";document.getElementById("id_per_time").style.width=n,function(e){e?df.lab.Util.addClass(o,"checkout-able"):df.lab.Util.removeClass(o,"checkout-able")}(t>=0)}else c()}function c(){!1,clearInterval(i)}function u(){var e=window.df.workgroup.GlobalVars.infoData;void 0!=e.preset&&void 0!=e.preset.json_url&&void 0!=e.preset.json_url.checkin?n.action=e.preset.json_url.checkin:n.action=window.df.workgroup.Preset.json_url.checkin,void 0!=e.preset&&void 0!=e.preset.json_url&&void 0!=e.preset.json_url.checkout?a.action=e.preset.json_url.checkout:a.action=window.df.workgroup.Preset.json_url.checkout}function f(e){e.preventDefault(),h()}function m(e){e.preventDefault(),function(){var e=t.querySelector(".ui-loading");df.lab.Util.addClass(e,window.df.workgroup.Preset.class_name.showIn),p()}(),k(n,v)}function w(e){e.preventDefault(),h()}function h(){return function(){var e=o.querySelector(".ui-loading");df.lab.Util.addClass(e,window.df.workgroup.Preset.class_name.showIn),g()}(),k(a,_),!1}function p(){for(var e=n.querySelectorAll("input"),t=0;t<e.length;t++)e[t].setAttribute("disabled","")}function g(){for(var e=a.querySelectorAll("input"),t=0;t<e.length;t++)e[t].setAttribute("disabled","")}function v(e){var o=t.querySelector(".ui-loading");df.lab.Util.removeClass(o,window.df.workgroup.Preset.class_name.showIn),function(e){var t=new CustomEvent(window.df.workgroup.Preset.eventType.ON_CHECKIN,{detail:{response:e}});document.dispatchEvent(t)}(e);var n=b(e);n.isWarning&&(console.log("status.text : ",n.text),C(n.text))}function _(e){var t=o.querySelector(".ui-loading");df.lab.Util.removeClass(t,window.df.workgroup.Preset.class_name.showIn),function(e){var t=new CustomEvent(window.df.workgroup.Preset.eventType.ON_CHECKOUT,{detail:{response:e}});document.dispatchEvent(t)}(e);var n=b(e);n.isWarning&&C(n.text)}function b(e){var t={isWarning:!1,text:"표시할 메세지가 없습니다."},o=JSON.parse(e.target.responseText),n=o.user.status;if(n.toLowerCase()=="L00".toLowerCase()||n.toLowerCase()=="C10".toLowerCase()||n.toLowerCase()=="C01".toLowerCase()||n.toLowerCase()=="C02".toLowerCase()||n.toLowerCase()=="C03".toLowerCase()||n.toLowerCase()=="C04".toLowerCase()||n.toLowerCase()=="C05".toLowerCase()||n.toLowerCase()=="C11".toLowerCase()||n.toLowerCase()=="C12".toLowerCase()||n.toLowerCase()=="C13".toLowerCase()||n.toLowerCase()=="C14".toLowerCase()||n.toLowerCase()=="C15".toLowerCase())for(var a=o.preset.status_list,r=0;r<a.length;r++){var i=a[r];if(i.code.toLowerCase()==n.toLowerCase()){t.isWarning=!0,t.text=i.text;break}}return t}function C(e){var t=new CustomEvent(window.df.workgroup.Preset.eventType.ON_WARNING,{detail:{message:e}});document.dispatchEvent(t)}function k(e,t){for(var o={},n=0,a=e.length;n<a;++n){var r=e[n];r.name&&(o[r.name]=r.value)}var i={method:e.method,action:df.workgroup.Util.addParamUniq(e.action)};df.workgroup.Util.load_json(i.action,i.method,t,o)}return{init:function(){l(),u(),n.addEventListener("submit",m),a.addEventListener("submit",w),r.addEventListener("click",f),p(),g()},showCheckinBtn:function(){l(),u(),df.lab.Util.addClass(e,window.df.workgroup.Preset.class_name.showIn),function(){for(var e=n.querySelectorAll("input"),t=0;t<e.length;t++)e[t].removeAttribute("disabled")}(),g()},showCheckoutBtn:function(){l(),u(),df.lab.Util.addClass(e,"checked"),p(),function(){for(var e=a.querySelectorAll("input"),t=0;t<e.length;t++)e[t].removeAttribute("disabled")}()},showCheckoutText:function(){l(),u(),df.lab.Util.addClass(e,"checkedout"),p(),g()},hideCheckinBtn:function(){df.lab.Util.removeClass(e,window.df.workgroup.Preset.class_name.showIn),df.lab.Util.removeClass(e,"checked"),df.lab.Util.removeClass(e,"checkedout"),p(),g()}}}},function(e,t){e.exports=function(){var e,t,o,n,a="",r="",i="",s="",l="",d="",c=0,u={oX:0,oY:0,passX:0,passY:0},f=!1,m=!1,w=15e3;function h(a,s){M(),clearTimeout(o),function(t){if(void 0!=t&&null!=t&&void 0!=t.title&&null!=t.title&&void 0!=t.dec&&null!=t.dec){e=t,f=!1;for(var o=r.querySelector(".txt-notice"),n="",a=0;a<e.title.length;a++)null!=e.title[a]&&""!=e.title[a]&&(n=n+"<span>"+e.title[a]+"</span>",f=!0);o.innerHTML=n;var i=r.querySelector(".txt-sub");n="";for(var a=0;a<e.dec.length;a++)null!=e.dec[a]&&""!=e.dec[a]&&(n=n+"<span>"+e.dec[a]+"</span>",f=!0);f?(i.innerHTML=n,df.lab.Util.removeClass(r,"hide")):df.lab.Util.addClass(r,"hide")}else df.lab.Util.addClass(r,"hide")}(a),function(e){if(void 0!=e&&null!=e&&e.length>0){t=e,m=!0;for(var o=["오늘","생일을","축하 드려요."],n="",a=i.querySelector(".txt-notice"),r=o.length,s=0;s<r;s++)n=n+"<span>"+o[s]+"</span>";a.innerHTML=n;var l=i.querySelector(".txt-sub");r=t.length,n="";for(var s=0;s<r;s++)0==s&&(n+="<span>"),0==s?n=n+t[s].name+" "+t[s].position+"님":s>0&&(n=n+", "+t[s].name+" "+t[s].position+"님"),s==r-1&&(n+="</span>");l.innerHTML=n,df.lab.Util.removeClass(i,"hide")}else df.lab.Util.addClass(i,"hide")}(s),m||f?o=setTimeout(_,1500):b(),n=setTimeout(T,w),m||f?(df.lab.Util.addClass(d,window.df.workgroup.Preset.class_name.showIn),function(){p();for(var e=d.querySelectorAll("li.item-list"),t=0;t<e.length;t++){var o=e[t];1==t&&!f||2==t&&!m?df.lab.Util.addClass(o,"hide"):df.lab.Util.removeClass(o,"hide");var n=o.querySelector("button.btn-indi");n.setAttribute("data-index",t),n.addEventListener("click",v)}}(),g(c)):(df.lab.Util.removeClass(d,window.df.workgroup.Preset.class_name.showIn),p())}function p(){for(var e=d.querySelectorAll("li.item-list"),t=0;t<e.length;t++){var o=e[t];df.lab.Util.addClass(o,"hide"),o.querySelector("button.btn-indi").removeEventListener("click",v)}}function g(e){for(var t=d.querySelectorAll("li.item-list"),o=0;o<t.length;o++){var n=t[o];e==o?df.lab.Util.addClass(n,"active"):df.lab.Util.removeClass(n,"active")}}function v(e){var t=e.currentTarget;U(parseInt(t.getAttribute("data-index")))}function _(){b(),"desktop"!=Detectizr.device.type?l.addEventListener("touchstart",k):l.addEventListener("click",C)}function b(){clearTimeout(o),l.removeEventListener("touchstart",k),l.removeEventListener("click",C)}function C(e){e.preventDefault(),f&&m?2==c?I():T():c>0?I():f?L():m&&E()}function k(e){e.stopPropagation(),l.addEventListener("touchmove",x),document.addEventListener("touchend",y);var t=0,o=0;"mousedown"==e.type?(t=e.clientX,o=e.clientY):"touchstart"==e.type&&1===e.touches.length&&(t=e.touches[0].pageX,o=e.touches[0].pageY),u.oX=t,u.oY=o,u.passX=t,u.passY=o}function y(e){e.stopPropagation(),l.removeEventListener("touchmove",x),document.removeEventListener("touchend",y);var t=s.offsetWidth,o=u.passX-u.oX;Math.abs(o)/t>.1&&(o<0?T():function(){var e=c-1;if(!f&&!m)return;if(f&&m){if(!(e>-1))return;U(e)}else I()}())}function x(e){var t,o;e.stopPropagation(),"mousemove"==e.type?(t=e.clientX,o=e.clientY):"touchmove"==e.type&&1===e.touches.length&&(t=e.touches[0].pageX,o=e.touches[0].pageY),u.passX=t,u.passY=o}function I(){U(0)}function L(){f&&U(1)}function E(){m&&U(2)}function T(){var e=c+1;if(f||m)if(f&&m){if(!(e<3))return;U(e)}else if(0==c&&f)L();else{if(0!=c||!m)return;E()}}function U(e){if(c!=e){switch(M(),c=e){case 1:P(!0),df.lab.Util.removeClass(r,"out-left"),df.lab.Util.removeClass(r,"out-right"),df.lab.Util.addClass(r,window.df.workgroup.Preset.class_name.showIn),D(!1);break;case 2:P(!0),S(!0),df.lab.Util.removeClass(i,"out-left"),df.lab.Util.removeClass(i,"out-right"),df.lab.Util.addClass(i,window.df.workgroup.Preset.class_name.showIn);break;default:df.lab.Util.removeClass(a,"out-left"),df.lab.Util.removeClass(a,"out-right"),df.lab.Util.addClass(a,window.df.workgroup.Preset.class_name.showIn),S(!1),D(!1)}if(g(c),function(){var e=new CustomEvent(window.df.workgroup.Preset.eventType.ON_CHANGE_STAGE_INFO,{detail:{curIndex:c}});document.dispatchEvent(e)}(),!f&&!m)return;n=f&&m?c<2?setTimeout(T,w):setTimeout(I,w):0==c?setTimeout(T,w):setTimeout(I,w)}}function P(e){df.lab.Util.removeClass(a,window.df.workgroup.Preset.class_name.showIn),e?(df.lab.Util.removeClass(a,"out-right"),df.lab.Util.addClass(a,"out-left")):(df.lab.Util.addClass(a,"out-right"),df.lab.Util.removeClass(a,"out-left"))}function S(e){df.lab.Util.removeClass(r,window.df.workgroup.Preset.class_name.showIn),e?(df.lab.Util.removeClass(r,"out-right"),df.lab.Util.addClass(r,"out-left")):(df.lab.Util.addClass(r,"out-right"),df.lab.Util.removeClass(r,"out-left"))}function D(e){df.lab.Util.removeClass(i,window.df.workgroup.Preset.class_name.showIn),e?(df.lab.Util.removeClass(i,"out-right"),df.lab.Util.addClass(i,"out-left")):(df.lab.Util.addClass(i,"out-right"),df.lab.Util.removeClass(i,"out-left"))}function M(){clearTimeout(n)}return{init:function(e,t){a=document.getElementById("id_stage_clock"),r=document.getElementById("id_stage_notice"),i=document.getElementById("id_stage_birthday"),s=document.querySelector("section.sec-info"),l=s.querySelector("ul.contents_con"),d=s.querySelector(".area-indicator ul.indicator"),h(e,t)},showNotice:function(){M(),(f||m)&&(f?n=setTimeout(L,600):m&&(n=setTimeout(E,600)))},resetData:function(e,t){f||m||I(),h(e,t)}}}},function(e,t){e.exports=function(){document.querySelector(".sec-util");function e(e){var t=document.getElementById("id_link_doc_approval").querySelector(".doc-num"),o=document.getElementById("id_link_doc_approval_my").querySelector(".doc-num"),n=document.getElementById("id_link_doc_approval_cc").querySelector(".doc-num");void 0!=e&&e.isLoggedIn?(e.document_approval_num>0?t.textContent=e.document_approval_num+"":t.textContent="0",e.document_approval_my_num>0?o.textContent=e.document_approval_my_num+"":o.textContent="0",e.document_approval_cc_num>0?n.textContent=e.document_approval_cc_num+"":n.textContent="0"):(t.textContent="0",o.textContent="0",n.textContent="0")}return{init:function(t,o,n){!function(e,t){var o=document.getElementById("id_link_doc_approval").querySelector("a.btn-link"),n=document.getElementById("id_link_doc_approval_my").querySelector("a.btn-link"),a=document.getElementById("id_link_doc_approval_cc").querySelector("a.btn-link"),r=document.getElementById("id_link_main").querySelector("a.btn-link");void 0!=e.approval&&""!=e.approval?(o.href=e.approval,df.lab.Util.addClass(o,"able")):(o.href="#",df.lab.Util.removeClass(o,"able")),void 0!=e.approval_my&&""!=e.approval_my?(n.href=e.approval_my,df.lab.Util.addClass(n,"able")):(n.href="#",df.lab.Util.removeClass(n,"able")),void 0!=e.approval_cc&&""!=e.approval_cc?(a.href=e.approval_cc,df.lab.Util.addClass(a,"able")):(a.href="#",df.lab.Util.removeClass(a,"able")),void 0!=t&&""!=t?(r.href=t,df.lab.Util.addClass(r,"able")):(r.href="#",df.lab.Util.removeClass(r,"able"))}(t,o),e(n)},resetData:function(t){e(t)}}}},function(e,t){e.exports=function(){var e=document.getElementById("id_modal"),t=document.getElementById("id_modal_txt"),o=document.getElementById("id_btn_close_modal"),n=0;function a(){clearTimeout(n),df.lab.Util.removeClass(e,window.df.workgroup.Preset.class_name.showIn),e.style.display="none",function(){var e=new CustomEvent(window.df.workgroup.Preset.eventType.ON_CLOSE_MODAL);document.dispatchEvent(e)}()}return{init:function(){o.onclick=function(){a()},window.onclick=function(t){t.target==e&&a()}},showModal:function(o){clearTimeout(n),t.textContent=o,df.lab.Util.removeClass(e,window.df.workgroup.Preset.class_name.showIn),e.style.display="block",n=setTimeout(function(){df.lab.Util.addClass(e,window.df.workgroup.Preset.class_name.showIn)},100),e.setAttribute("tabindex","-1"),e.focus(),e.removeAttribute("tabindex")},closeModal:a}}},,,,,,function(e,t,o){e.exports=o(15)},function(e,t,o){"use strict";o.r(t);var n=o(0),a=o.n(n),r=o(1),i=o.n(r),s=o(2),l=o.n(s),d=o(3),c=o.n(d),u=o(4),f=o.n(u),m=o(5),w=o.n(m),h=o(6),p=o.n(h),g=o(7),v=o.n(g),_=o(8),b=o.n(_);window.df=window.df||{},window.df.workgroup=window.df.workgroup||{},window.df.workgroup.login=function(e){var t,o,n="[ login ]",r=document.getElementById("id_bg_frame"),s=document.getElementById("id_container_clock"),d=document.querySelector(".sec-date .wrapper-date"),u=e,m=0,h=new a.a(s,u),g=new i.a(d,u),_=new l.a(r,u),C=new c.a,k=new f.a,y=new w.a,x=new p.a,I=new v.a,L=new b.a,E={YY:0,MM:0,DD:0,DW:0,hh:0,mm:0,ss:0};function T(e){var t=JSON.parse(e.target.responseText);console.log(n+" << _resetData>> ",t),window.df.workgroup.GlobalVars.infoData=t,u=window.df.workgroup.GlobalVars.infoData}function U(e){T(e.detail.response),q()}function P(e){T(e.detail.response),q()}function S(e){T(e.detail.response),q()}function D(e){T(e.detail.response),q()}function M(e){L.showModal(e.detail.message)}function O(e){}function G(){var e=document.querySelector(".sec-util");df.lab.Util.removeClass(e,window.df.workgroup.Preset.class_name.showIn);var t=document.querySelector(".sec-login");df.lab.Util.removeClass(t,"logged"),k.hideLogoutBtn(),y.hideCheckinBtn(),C.showLoginFrom()}function q(){console.log(n," user : isLoggedIn - ",u.user.isLoggedIn," / isCheckin - ",u.user.isCheckin," / isCheckout",u.user.isCheckout),u.user.isLoggedIn,document.title=o,x.resetData(u.info.today.notice,u.info.birthday),I.resetData(u.user);var e=document.querySelector(".sec-login");u.user.isLoggedIn?(df.lab.Util.addClass(e,"logged"),function(){C.hideLoginFrom(),k.showLogoutBtn(),y.showCheckinBtn();var e=document.querySelector(".sec-util");df.lab.Util.addClass(e,window.df.workgroup.Preset.class_name.showIn),x.showNotice()}(),u.user.isCheckin&&(y.showCheckoutBtn(),u.user.isCheckout&&y.showCheckoutText())):(df.lab.Util.removeClass(e,"logged"),G())}function N(){(t=new Date).setTime(t.getTime()+m),E.YY=t.getFullYear(),E.MM=t.getMonth(),E.DD=t.getDate(),E.DW=t.getDay(),E.hh=t.getHours(),E.mm=t.getMinutes(),E.ss=t.getSeconds(),window.df.workgroup.GlobalVars.time_now=t.getTime(),setTimeout(N,500),h.updateToday(E),g.updateToday(E)}return{init:function(){o=document.title,void 0!=u.info&&void 0!=u.info.date&&void 0!=u.info.date.server_time&&(m=u.info.date.server_time-(new Date).getTime(),console.log(n+" [server time] : ",u.info.date.server_time," [client time] : ",(new Date).getTime()," [_offsetTime] : ",m)),N(),_.init(),h.init(E),g.init(E),C.init(),k.init(),y.init(),x.init(u.info.today.notice,u.info.birthday),I.init(u.preset.document_url,u.preset.main_url,u.user),L.init(),function(){var e=document.querySelector("header");setTimeout(function(){df.lab.Util.addClass(e,window.df.workgroup.Preset.class_name.showIn)},10);var t=document.querySelector(".sec-info");setTimeout(function(){df.lab.Util.addClass(t,window.df.workgroup.Preset.class_name.showIn),q()},0);var o=document.querySelector(".sec-login");setTimeout(function(){df.lab.Util.addClass(o,window.df.workgroup.Preset.class_name.showIn),q()},10);var n=document.querySelector("footer");setTimeout(function(){df.lab.Util.addClass(n,window.df.workgroup.Preset.class_name.showIn)},1500)}(),document.addEventListener(window.df.workgroup.Preset.eventType.ON_LOGIN,U),document.addEventListener(window.df.workgroup.Preset.eventType.ON_CHECKIN,P),document.addEventListener(window.df.workgroup.Preset.eventType.ON_CHECKOUT,S),document.addEventListener(window.df.workgroup.Preset.eventType.ON_CHANGE_STAGE_INFO,O),document.addEventListener(window.df.workgroup.Preset.eventType.ON_LOGOUT,D),document.addEventListener(window.df.workgroup.Preset.eventType.ON_WARNING,M)},setLayout_Logout:G}}}]);