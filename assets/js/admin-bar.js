(()=>{var e={3563:function(e,n,t){var r,o,i;"undefined"!=typeof window&&window,o=[t(3563)],r=function(){var e,n,t,r=400,o="___sysend___",i=new RegExp(o),a=Math.random(),u={},c={},d=[],s=0,f=!1,l=!0,m=!1,p=0,w=0,y=N(),g=1,v=0,h={primary:[],close:[],open:[],secondary:[],message:[],visbility:[],ready:[],update:[]},_=Object.keys(h),b=H(u,"to"),x=H(u,"from"),E={id:y,broadcast:function(e,t){return n&&!m?n.postMessage({name:e,data:b(t)}):(J(e,W(t)),setTimeout((function(){U(e)}),0)),q(e,t),E},emit:function(e,n){return E.broadcast(e,n),F(e,n),E},serializer:function(e,n){if("function"!=typeof e||"function"!=typeof n)throw new Error("sysend::serializer: Invalid argument, expecting function");return u.to=e,u.from=n,E},proxy:function(...e){return e.forEach((function(e){if("string"==typeof e&&S(e)!==window.location.host){(t=t||[]).push(O(e));const n=document.createElement("iframe");n.style.width=n.style.height=0,n.style.position="absolute",n.style.top=n.style.left="-9999px",n.style.border="none";let r=e;e.match(/\.html|\.php|\?/)||(r=e.replace(/\/$/,"")+"/proxy.html"),n.addEventListener("error",(function t(){setTimeout((function(){throw new Error('html proxy file not found on "'+e+'" url')}),0),n.removeEventListener("error",t)})),n.addEventListener("load",(function e(){let t;try{t=n.contentWindow}catch(e){t=n.contentWindow}d.push({window:t,node:n}),n.removeEventListener("load",e)})),document.body.appendChild(n),n.src=r}})),!arguments.length&&V&&(f=!0),E},on:function(e,n){return c[e]||(c[e]=[]),c[e].push(n),E},off:function(e,n,t=!1){if(c[e])if(n)for(var r=c[e].length;r--;)c[e][r]==n&&c[e].splice(r,1);else(t&&C(e)||!t)&&(c[e]=[]);return E},track:function(e,n,t=!1){return t&&(n[Symbol.for(o)]=!0),_.includes(e)&&h[e].push(n),E},untrack:function(e,n,t=!1){return _.includes(e)&&h[e].length&&(h[e]=void 0===n?t?[]:h[e].filter((e=>!e[Symbol.for(o)])):h[e].filter((function(e){return e!==n}))),E},post:function(e,n){return E.broadcast(B("__message__"),{target:e,data:n,origin:y})},list:function(){const e=w++,n={target:y,id:e},t=I(E.timeout);return new Promise((function(r){const o=[];E.on(B("__window_ack__"),(function(n){n.origin.target===y&&n.origin.id===e&&o.push({id:n.id,primary:n.primary})})),E.broadcast(B("__window__"),{id:n}),t().then((function(){r(o)}))}))},channel:function(...e){return t=e.map(O),E},isPrimary:function(){return l},useLocalStorage:function(e){m="boolean"!=typeof e||e},rpc:function(e){const n=++v,t=`__${n}_rpc_request__`,r=`__${n}_rpc_response__`;let o=0;const i=1e3;function a(e,n,a=[]){const u=++o;return new Promise(((o,c)=>{E.track("message",(function n({data:t,origin:i}){if(t.type===r){const{result:r,error:a,id:s}=t;i===e&&u===s&&(a?c(a):o(r),clearTimeout(d),E.untrack("message",n))}}),!0),E.post(e,{method:n,id:u,type:t,args:a});const d=setTimeout((()=>{c(new Error("Timeout error"))}),i)}))}E.track("message",(async function({data:n,origin:o}){if(n.type==t){const{method:t,args:i,id:a}=n,u=r;if(Object.hasOwn(e,t))try{L(e[t](...i),(function(e){E.post(o,{result:e,id:a,type:u})}),(function(e){E.post(o,{error:e.message,id:a,type:u})}))}catch(e){E.post(o,{error:e.message,id:a,type:u})}else E.post(o,{error:"Method not found",id:a,type:u})}}),!0);const u="You need to specify the target window/tab";return Object.fromEntries(Object.keys(e).map((e=>[e,(n,...t)=>n?a(n,e,t):Promise.reject(new Error(u))])))}};Object.defineProperty(E,"timeout",{enumerable:!0,get:function(){return r},set:function(e){"number"!=typeof e||isNaN(e)||(r=e)}});var S=function(){if("undefined"!=typeof URL)return function(e){return e?(e=new URL(e)).host:e};var e=document.createElement("a");return function(n){return n?(e.href=n,e.host):n}}();function k(e){return e&&"object"==typeof object&&"function"==typeof object.then}function L(e,n,t=null){if(k(e)){const r=e.then(n);return null===t?r:r.catch(t)}return n(e)}function I(e){return function(){return new Promise((function(n){setTimeout(n,e)}))}}var O=function(){function e(e){return function(n){try{return e(n)}catch(e){return n}}}if(window.URL)return e((function(e){return new URL(e).origin}));var n=document.createElement("a");return e((function(e){return n.href=e,n.origin}))}(),M=[];function P(e){M.includes(e)||(M.push(e),console&&console.warn?console.warn(e):setTimeout((function(){throw new Error(e)}),0))}function j(){var e=Array.from(document.querySelectorAll("iframe"));return Promise.all(e.filter((function(e){return e.src})).map((function(e){return new Promise((function(n,t){e.addEventListener("load",n,!0),e.addEventListener("error",t,!0)}))}))).then(I(E.timeout))}function B(e){return o+e}function C(e){return e.match(i)}function T(e){return"string"==typeof e.data&&C(e.data)}function A(e){if(!t)return P("Call sysend.channel() on iframe to restrict domains that can use sysend channel"),!0;var n=t.includes(e);return n||P(e+" domain is not on the list of allowed domains use sysend.channel() on iframe to allow access to this domain"),n}function N(){var e=(new Date).getTime(),n=performance&&performance.now&&1e3*performance.now()||0;return"xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g,(function(t){var r=16*Math.random();return e>0?(r=(e+r)%16|0,e=Math.floor(e/16)):(r=(n+r)%16|0,n=Math.floor(n/16)),("x"===t?r:3&r|8).toString(16)}))}function R(e,...n){e.forEach((function(e){e.apply(null,n)}))}function z(e){return localStorage.getItem(B(e))}function J(e,n){0==p&&localStorage.setItem(B(e),a),localStorage.setItem(o+e,n)}function U(e){localStorage.removeItem(o+e)}function H(e,n){var t={from:"Unserialize",to:"Serialize"}[n]+" Error: ";return function(r){var o=e[n];try{return o?o(r):r}catch(e){P(t+e.message)}}}var V=function(){try{return window.self!==window.top}catch(e){return!0}}(),D=function(){try{return localStorage.setItem(o,1),localStorage.removeItem(o),!1}catch(e){return!0}}();function $(){return V&&f}function q(e,n){d.forEach((function(t){var r={name:o,key:e,data:n};A(O(t.node.src))&&t.window.postMessage(JSON.stringify(r),"*")}))}function W(e){var n=[p++,a];void 0!==e&&n.push(e);var t=b(n);return t===n?JSON.stringify(n):t}function Y(e){var n=x(e);return n===e?JSON.parse(e):n}function F(e,n){c[e].forEach((function(t){t(n,e)}))}function G(){var e,n;void 0!==document.hidden?(e="hidden",n="visibilitychange"):void 0!==document.msHidden?(e="msHidden",n="msvisibilitychange"):void 0!==document.webkitHidden&&(e="webkitHidden",n="webkitvisibilitychange"),"function"==typeof document.addEventListener&&e&&document.addEventListener(n,(function(){R(h.visbility,!document[e])}),!1)}function K(){l=!0,R(h.primary),E.emit(B("__primary__"))}function Q(){return-1!==["interactive","complete"].indexOf(document.readyState)}function X(){var e=new RegExp("^"+o);for(var n in localStorage)n.match(e)&&localStorage.removeItem(n);window.addEventListener("storage",(function(n){if(n.key&&n.key.match(e)&&s++%2==0){var t=n.key.replace(e,"");if(c[t]){var r=n.newValue||z(t);if(r&&r!=a){var o=Y(r);o&&o[1]!=a&&F(t,o[2])}}}}),!1)}function Z(){let e=[];function n(){R(h.update,e)}E.track("open",(t=>{t.id!==E.id&&(e.push(t),n())}),!0),E.track("close",(t=>{e=e.filter((e=>t.id!==e.id)),n()}),!0),E.track("ready",(()=>{E.list().then((t=>{e=t,n()}))}),!0)}function ee(){"function"==typeof window.BroadcastChannel?(n=new window.BroadcastChannel(o)).addEventListener("message",(function(e){if(e.target.name===o)if($()){var n={name:o,data:e.data,iframe_id:y};A(O(document.referrer))&&window.parent.postMessage(JSON.stringify(n),"*")}else{var t=e.data&&e.data.name;c[t]&&F(t,x(e.data.data))}})):D&&P('Your browser don\'t support localStorgage. In Safari this is most of the time because of "Private Browsing Mode"'),D||X(),$()?window.addEventListener("message",(function(e){if(T(e)&&A(e.origin))try{var n=JSON.parse(e.data);if(n&&n.name===o){var t=x(n.data);E.broadcast(n.key,t)}}catch(e){}})):(G(),E.track("visbility",(function(n){n&&!e&&K()}),!0),E.on(B("__primary__"),(function(){e=!0})),E.on(B("__open__"),(function(e){var n=e.id;g++,l&&E.broadcast(B("__ack__")),R(h.open,{count:g,primary:e.primary,id:e.id}),n===y&&R(h.ready)})),E.on(B("__ack__"),(function(){l||R(h.secondary)})),E.on(B("__close__"),(function(n){var t=1==--g;n.wasPrimary&&!l&&(e=!1);var r={id:n.id,count:g,primary:n.wasPrimary,self:n.id===y};t&&K(),R(h.close,r)})),E.on(B("__window__"),(function(e){E.broadcast(B("__window_ack__"),{id:y,origin:e.id,primary:l})})),E.on(B("__message__"),(function(e){("primary"===e.target&&l||e.target===y)&&R(h.message,e)})),addEventListener("beforeunload",(function(){E.emit(B("__close__"),{id:y,wasPrimary:l})}),{capture:!0}),j().then((function(){E.list().then((function(n){g=n.length,l=0===n.length,(n.find((function(e){return e.primary}))||l)&&(e=!0),E.emit(B("__open__"),{id:y,primary:l}),l&&R(h.primary)}))})))}return Q()?ee():window.addEventListener("load",(function(){setTimeout(ee,0)})),Z(),E},void 0===(i="function"==typeof r?r.apply(n,o):r)||(e.exports=i)}},n={};function t(r){var o=n[r];if(void 0!==o)return o.exports;var i=n[r]={exports:{}};return e[r].call(i.exports,i,i.exports,t),i.exports}t.n=e=>{var n=e&&e.__esModule?()=>e.default:()=>e;return t.d(n,{a:n}),n},t.d=(e,n)=>{for(var r in n)t.o(n,r)&&!t.o(e,r)&&Object.defineProperty(e,r,{enumerable:!0,get:n[r]})},t.o=(e,n)=>Object.prototype.hasOwnProperty.call(e,n),(()=>{"use strict";var e,n=t(3563),r=t.n(n);window.BuilderiusAdminBar=(e=function(){window.location.reload()},Object.freeze({init:function(){r().on("builderiusSavedAllChanges",e);var n=document.getElementById("builderius-preview-wrapper");n&&n.addEventListener("click",(function(e){e.preventDefault(),localStorage.setItem("builderius-preview-mode-changed",!0),window.location.href=builderiusAdminBar.previewModeChange.link})),window.addEventListener("storage",(function(e){"builderius-reload-page"===e.key?"true"===e.newValue&&window.location.reload():"builderius-loaded-preview-mode"===e.key&&null!==e.newValue&&builderiusAdminBar.previewModeChange.mode!==e.newValue&&window.location.reload()})),document.addEventListener("DOMContentLoaded",(function(){"true"===localStorage.getItem("builderius-preview-mode-changed")?(localStorage.setItem("builderius-reload-page",!0),localStorage.removeItem("builderius-preview-mode-changed"),localStorage.removeItem("builderius-reload-page")):(localStorage.setItem("builderius-loaded-preview-mode",builderiusAdminBar.previewModeChange.mode),localStorage.removeItem("builderius-loaded-preview-mode"))}))}})),document.addEventListener("readystatechange",(function(){"complete"===document.readyState&&window.BuilderiusAdminBar.init()}))})()})();