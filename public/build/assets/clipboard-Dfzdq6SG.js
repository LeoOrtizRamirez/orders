import{d as st,r as F,k as it,l as S,m as e,C as H,P,x as B,u as M,A as U,a2 as z,y as L,q as _}from"./vendor-BuRU_kNN.js";import{c as at,_ as j}from"./codePreview-D3lNCkU-.js";import{a as lt,c as ut}from"./quill-De1S0mUZ.js";import{S as ct}from"./sweetalert2.esm.all-5zhdP7Ax.js";import{u as dt}from"./main-C1M8ohSC.js";var Y={exports:{}};/*!
 * clipboard.js v2.0.11
 * https://clipboardjs.com/
 *
 * Licensed MIT © Zeno Rocha
 */(function(N,E){(function(g,v){N.exports=v()})(ut,function(){return function(){var k={686:function(d,s,o){o.d(s,{default:function(){return nt}});var p=o(279),a=o.n(p),t=o(370),c=o.n(t),h=o(817),y=o.n(h);function C(u){try{return document.execCommand(u)}catch{return!1}}var w=function(n){var r=y()(n);return C("cut"),r},m=w;function x(u){var n=document.documentElement.getAttribute("dir")==="rtl",r=document.createElement("textarea");r.style.fontSize="12pt",r.style.border="0",r.style.padding="0",r.style.margin="0",r.style.position="absolute",r.style[n?"right":"left"]="-9999px";var i=window.pageYOffset||document.documentElement.scrollTop;return r.style.top="".concat(i,"px"),r.setAttribute("readonly",""),r.value=u,r}var I=function(n,r){var i=x(n);r.container.appendChild(i);var l=y()(i);return C("copy"),i.remove(),l},G=function(n){var r=arguments.length>1&&arguments[1]!==void 0?arguments[1]:{container:document.body},i="";return typeof n=="string"?i=I(n,r):n instanceof HTMLInputElement&&!["text","search","url","tel","password"].includes(n==null?void 0:n.type)?i=I(n.value,r):(i=y()(n),C("copy")),i},R=G;function V(u){"@babel/helpers - typeof";return typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?V=function(r){return typeof r}:V=function(r){return r&&typeof Symbol=="function"&&r.constructor===Symbol&&r!==Symbol.prototype?"symbol":typeof r},V(u)}var X=function(){var n=arguments.length>0&&arguments[0]!==void 0?arguments[0]:{},r=n.action,i=r===void 0?"copy":r,l=n.container,f=n.target,b=n.text;if(i!=="copy"&&i!=="cut")throw new Error('Invalid "action" value, use either "copy" or "cut"');if(f!==void 0)if(f&&V(f)==="object"&&f.nodeType===1){if(i==="copy"&&f.hasAttribute("disabled"))throw new Error('Invalid "target" attribute. Please use "readonly" instead of "disabled" attribute');if(i==="cut"&&(f.hasAttribute("readonly")||f.hasAttribute("disabled")))throw new Error(`Invalid "target" attribute. You can't cut text from elements with "readonly" or "disabled" attributes`)}else throw new Error('Invalid "target" value, use a valid Element');if(b)return R(b,{container:l});if(f)return i==="cut"?m(f):R(f,{container:l})},q=X;function T(u){"@babel/helpers - typeof";return typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?T=function(r){return typeof r}:T=function(r){return r&&typeof Symbol=="function"&&r.constructor===Symbol&&r!==Symbol.prototype?"symbol":typeof r},T(u)}function J(u,n){if(!(u instanceof n))throw new TypeError("Cannot call a class as a function")}function D(u,n){for(var r=0;r<n.length;r++){var i=n[r];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(u,i.key,i)}}function K(u,n,r){return n&&D(u.prototype,n),r&&D(u,r),u}function Q(u,n){if(typeof n!="function"&&n!==null)throw new TypeError("Super expression must either be null or a function");u.prototype=Object.create(n&&n.prototype,{constructor:{value:u,writable:!0,configurable:!0}}),n&&Z(u,n)}function Z(u,n){return Z=Object.setPrototypeOf||function(i,l){return i.__proto__=l,i},Z(u,n)}function W(u){var n=ot();return function(){var i=A(u),l;if(n){var f=A(this).constructor;l=Reflect.construct(i,arguments,f)}else l=i.apply(this,arguments);return tt(this,l)}}function tt(u,n){return n&&(T(n)==="object"||typeof n=="function")?n:et(u)}function et(u){if(u===void 0)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return u}function ot(){if(typeof Reflect>"u"||!Reflect.construct||Reflect.construct.sham)return!1;if(typeof Proxy=="function")return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],function(){})),!0}catch{return!1}}function A(u){return A=Object.setPrototypeOf?Object.getPrototypeOf:function(r){return r.__proto__||Object.getPrototypeOf(r)},A(u)}function $(u,n){var r="data-clipboard-".concat(u);if(n.hasAttribute(r))return n.getAttribute(r)}var rt=function(u){Q(r,u);var n=W(r);function r(i,l){var f;return J(this,r),f=n.call(this),f.resolveOptions(l),f.listenClick(i),f}return K(r,[{key:"resolveOptions",value:function(){var l=arguments.length>0&&arguments[0]!==void 0?arguments[0]:{};this.action=typeof l.action=="function"?l.action:this.defaultAction,this.target=typeof l.target=="function"?l.target:this.defaultTarget,this.text=typeof l.text=="function"?l.text:this.defaultText,this.container=T(l.container)==="object"?l.container:document.body}},{key:"listenClick",value:function(l){var f=this;this.listener=c()(l,"click",function(b){return f.onClick(b)})}},{key:"onClick",value:function(l){var f=l.delegateTarget||l.currentTarget,b=this.action(f)||"copy",O=q({action:b,container:this.container,target:this.target(f),text:this.text(f)});this.emit(O?"success":"error",{action:b,text:O,trigger:f,clearSelection:function(){f&&f.focus(),window.getSelection().removeAllRanges()}})}},{key:"defaultAction",value:function(l){return $("action",l)}},{key:"defaultTarget",value:function(l){var f=$("target",l);if(f)return document.querySelector(f)}},{key:"defaultText",value:function(l){return $("text",l)}},{key:"destroy",value:function(){this.listener.destroy()}}],[{key:"copy",value:function(l){var f=arguments.length>1&&arguments[1]!==void 0?arguments[1]:{container:document.body};return R(l,f)}},{key:"cut",value:function(l){return m(l)}},{key:"isSupported",value:function(){var l=arguments.length>0&&arguments[0]!==void 0?arguments[0]:["copy","cut"],f=typeof l=="string"?[l]:l,b=!!document.queryCommandSupported;return f.forEach(function(O){b=b&&!!document.queryCommandSupported(O)}),b}}]),r}(a()),nt=rt},828:function(d){var s=9;if(typeof Element<"u"&&!Element.prototype.matches){var o=Element.prototype;o.matches=o.matchesSelector||o.mozMatchesSelector||o.msMatchesSelector||o.oMatchesSelector||o.webkitMatchesSelector}function p(a,t){for(;a&&a.nodeType!==s;){if(typeof a.matches=="function"&&a.matches(t))return a;a=a.parentNode}}d.exports=p},438:function(d,s,o){var p=o(828);function a(h,y,C,w,m){var x=c.apply(this,arguments);return h.addEventListener(C,x,m),{destroy:function(){h.removeEventListener(C,x,m)}}}function t(h,y,C,w,m){return typeof h.addEventListener=="function"?a.apply(null,arguments):typeof C=="function"?a.bind(null,document).apply(null,arguments):(typeof h=="string"&&(h=document.querySelectorAll(h)),Array.prototype.map.call(h,function(x){return a(x,y,C,w,m)}))}function c(h,y,C,w){return function(m){m.delegateTarget=p(m.target,y),m.delegateTarget&&w.call(h,m)}}d.exports=t},879:function(d,s){s.node=function(o){return o!==void 0&&o instanceof HTMLElement&&o.nodeType===1},s.nodeList=function(o){var p=Object.prototype.toString.call(o);return o!==void 0&&(p==="[object NodeList]"||p==="[object HTMLCollection]")&&"length"in o&&(o.length===0||s.node(o[0]))},s.string=function(o){return typeof o=="string"||o instanceof String},s.fn=function(o){var p=Object.prototype.toString.call(o);return p==="[object Function]"}},370:function(d,s,o){var p=o(879),a=o(438);function t(C,w,m){if(!C&&!w&&!m)throw new Error("Missing required arguments");if(!p.string(w))throw new TypeError("Second argument must be a String");if(!p.fn(m))throw new TypeError("Third argument must be a Function");if(p.node(C))return c(C,w,m);if(p.nodeList(C))return h(C,w,m);if(p.string(C))return y(C,w,m);throw new TypeError("First argument must be a String, HTMLElement, HTMLCollection, or NodeList")}function c(C,w,m){return C.addEventListener(w,m),{destroy:function(){C.removeEventListener(w,m)}}}function h(C,w,m){return Array.prototype.forEach.call(C,function(x){x.addEventListener(w,m)}),{destroy:function(){Array.prototype.forEach.call(C,function(x){x.removeEventListener(w,m)})}}}function y(C,w,m){return a(document.body,C,w,m)}d.exports=t},817:function(d){function s(o){var p;if(o.nodeName==="SELECT")o.focus(),p=o.value;else if(o.nodeName==="INPUT"||o.nodeName==="TEXTAREA"){var a=o.hasAttribute("readonly");a||o.setAttribute("readonly",""),o.select(),o.setSelectionRange(0,o.value.length),a||o.removeAttribute("readonly"),p=o.value}else{o.hasAttribute("contenteditable")&&o.focus();var t=window.getSelection(),c=document.createRange();c.selectNodeContents(o),t.removeAllRanges(),t.addRange(c),p=t.toString()}return p}d.exports=s},279:function(d){function s(){}s.prototype={on:function(o,p,a){var t=this.e||(this.e={});return(t[o]||(t[o]=[])).push({fn:p,ctx:a}),this},once:function(o,p,a){var t=this;function c(){t.off(o,c),p.apply(a,arguments)}return c._=p,this.on(o,c,a)},emit:function(o){var p=[].slice.call(arguments,1),a=((this.e||(this.e={}))[o]||[]).slice(),t=0,c=a.length;for(t;t<c;t++)a[t].fn.apply(a[t].ctx,p);return this},off:function(o,p){var a=this.e||(this.e={}),t=a[o],c=[];if(t&&p)for(var h=0,y=t.length;h<y;h++)t[h].fn!==p&&t[h].fn._!==p&&c.push(t[h]);return c.length?a[o]=c:delete a[o],this}},d.exports=s,d.exports.TinyEmitter=s}},g={};function v(d){if(g[d])return g[d].exports;var s=g[d]={exports:{}};return k[d](s,s.exports,v),s.exports}return function(){v.n=function(d){var s=d&&d.__esModule?function(){return d.default}:function(){return d};return v.d(s,{a:s}),s}}(),function(){v.d=function(d,s){for(var o in s)v.o(s,o)&&!v.o(d,o)&&Object.defineProperty(d,o,{enumerable:!0,get:s[o]})}}(),function(){v.o=function(d,s){return Object.prototype.hasOwnProperty.call(d,s)}}(),v(686)}().default})})(Y);var pt=Y.exports;const ft=lt(pt),Ct=N=>({toClipboard(E,k){return new Promise((g,v)=>{const d=document.createElement("button"),s=new ft(d,{text:()=>E,action:()=>"copy",container:k!==void 0?k:document.body});s.on("success",o=>{s.destroy(),g(o)}),s.on("error",o=>{s.destroy(),v(o)}),document.body.appendChild(d),d.click(),document.body.removeChild(d)})}}),mt={class:"pt-5 space-y-8"},ht={class:"grid grid-cols-1 lg:grid-cols-2 gap-6"},wt={class:"panel"},vt={class:"flex items-center justify-between mb-5"},gt={class:"mb-5"},yt={class:"bg-[#f1f2f3] p-5 rounded dark:bg-[#060818]"},bt={class:"flex flex-wrap gap-2 mt-5"},kt={class:"panel"},xt={class:"flex items-center justify-between mb-5"},Lt={class:"mb-5"},Mt={class:"bg-[#f1f2f3] p-5 rounded dark:bg-[#060818]"},Et={class:"flex flex-wrap gap-2 mt-5"},Tt={class:"panel"},St={class:"flex items-center justify-between mb-5"},Ht={class:"mb-5"},Vt={class:"bg-[#f1f2f3] p-5 rounded dark:bg-[#060818]"},At={class:"flex flex-wrap gap-2 mt-5"},Ot={class:"panel"},Pt={class:"flex items-center justify-between mb-5"},Bt={class:"mb-5"},_t={class:"bg-[#f1f2f3] p-5 rounded dark:bg-[#060818]"},jt={class:"flex flex-wrap gap-2 mt-5"},Ft=st({__name:"clipboard",setup(N){dt({title:"Clipboard"});const{codeArr:E,toggleCode:k}=at(),g=F("http://www.admin-dashboard.com"),v=F("Lorem ipsum dolor sit amet, consectetur adipiscing elit..."),{toClipboard:d}=Ct(),s=async a=>{a&&(await d(a),p("Copied successfully."))},o=async a=>{a&&(await d(a),p("Cut successfully."))},p=(a="",t="success")=>{ct.mixin({toast:!0,position:"top",showConfirmButton:!1,timer:3e3,customClass:{container:"toast"}}).fire({icon:t,title:a,padding:"10px 20px"})};return(a,t)=>(S(),it("div",null,[t[39]||(t[39]=e("ul",{class:"flex space-x-2 rtl:space-x-reverse"},[e("li",null,[e("a",{href:"javascript:;",class:"text-primary hover:underline"},"Forms")]),e("li",{class:"before:content-['/'] ltr:before:mr-2 rtl:before:ml-2"},[e("span",null,"Clipboard")])],-1)),e("div",mt,[t[38]||(t[38]=H('<div class="panel p-3 flex items-center text-primary overflow-x-auto whitespace-nowrap"><div class="ring-2 ring-primary/30 rounded-full bg-primary text-white p-1.5 ltr:mr-3 rtl:ml-3"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5"><path d="M19.0001 9.7041V9C19.0001 5.13401 15.8661 2 12.0001 2C8.13407 2 5.00006 5.13401 5.00006 9V9.7041C5.00006 10.5491 4.74995 11.3752 4.28123 12.0783L3.13263 13.8012C2.08349 15.3749 2.88442 17.5139 4.70913 18.0116C9.48258 19.3134 14.5175 19.3134 19.291 18.0116C21.1157 17.5139 21.9166 15.3749 20.8675 13.8012L19.7189 12.0783C19.2502 11.3752 19.0001 10.5491 19.0001 9.7041Z" stroke="currentColor" stroke-width="1.5"></path><path opacity="0.5" d="M7.5 19C8.15503 20.7478 9.92246 22 12 22C14.0775 22 15.845 20.7478 16.5 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg></div><span class="ltr:mr-3 rtl:ml-3">Documentation: </span><a href="https://www.npmjs.com/package/vue-clipboard3" target="_blank" class="block hover:underline">https://www.npmjs.com/package/vue-clipboard3</a></div>',1)),e("div",ht,[e("div",wt,[e("div",vt,[t[16]||(t[16]=e("h5",{class:"font-semibold text-lg dark:text-white-light"},"Copy from input",-1)),e("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:t[0]||(t[0]=c=>M(k)("code1"))},[...t[15]||(t[15]=[H('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),e("div",gt,[e("div",yt,[e("form",null,[U(e("input",{type:"text",class:"form-input","onUpdate:modelValue":t[1]||(t[1]=c=>g.value=c),id:"message1"},null,512),[[z,g.value]]),e("div",bt,[e("button",{type:"button",class:"btn btn-primary",onClick:t[2]||(t[2]=c=>s(g.value))},[...t[17]||(t[17]=[e("svg",{width:"24",height:"24",viewBox:"0 0 24 24",fill:"none",xmlns:"http://www.w3.org/2000/svg",class:"w-5 h-5 ltr:mr-2 rtl:ml-2"},[e("path",{d:"M6 11C6 8.17157 6 6.75736 6.87868 5.87868C7.75736 5 9.17157 5 12 5H15C17.8284 5 19.2426 5 20.1213 5.87868C21 6.75736 21 8.17157 21 11V16C21 18.8284 21 20.2426 20.1213 21.1213C19.2426 22 17.8284 22 15 22H12C9.17157 22 7.75736 22 6.87868 21.1213C6 20.2426 6 18.8284 6 16V11Z",stroke:"currentColor","stroke-width":"1.5"}),e("path",{opacity:"0.5",d:"M6 19C4.34315 19 3 17.6569 3 16V10C3 6.22876 3 4.34315 4.17157 3.17157C5.34315 2 7.22876 2 11 2H15C16.6569 2 18 3.34315 18 5",stroke:"currentColor","stroke-width":"1.5"})],-1),L(" Copy from Input ",-1)])]),e("button",{type:"button",class:"btn btn-dark",onClick:t[3]||(t[3]=c=>o(g.value)),onBlur:t[4]||(t[4]=c=>g.value="")},[...t[18]||(t[18]=[e("svg",{width:"24",height:"24",viewBox:"0 0 24 24",fill:"none",xmlns:"http://www.w3.org/2000/svg",class:"w-4.5 h-4.5 ltr:mr-2 rtl:ml-2"},[e("path",{d:"M15.2869 3.15178L14.3601 4.07866L5.83882 12.5999L5.83881 12.5999C5.26166 13.1771 4.97308 13.4656 4.7249 13.7838C4.43213 14.1592 4.18114 14.5653 3.97634 14.995C3.80273 15.3593 3.67368 15.7465 3.41556 16.5208L2.32181 19.8021L2.05445 20.6042C1.92743 20.9852 2.0266 21.4053 2.31063 21.6894C2.59466 21.9734 3.01478 22.0726 3.39584 21.9456L4.19792 21.6782L7.47918 20.5844L7.47919 20.5844C8.25353 20.3263 8.6407 20.1973 9.00498 20.0237C9.43469 19.8189 9.84082 19.5679 10.2162 19.2751C10.5344 19.0269 10.8229 18.7383 11.4001 18.1612L11.4001 18.1612L19.9213 9.63993L20.8482 8.71306C22.3839 7.17735 22.3839 4.68748 20.8482 3.15178C19.3125 1.61607 16.8226 1.61607 15.2869 3.15178Z",stroke:"currentColor","stroke-width":"1.5"}),e("path",{opacity:"0.5",d:"M14.36 4.07812C14.36 4.07812 14.4759 6.04774 16.2138 7.78564C17.9517 9.52354 19.9213 9.6394 19.9213 9.6394M4.19789 21.6777L2.32178 19.8015",stroke:"currentColor","stroke-width":"1.5"})],-1),L(" Cut from Input ",-1)])],32)])])])]),M(E).includes("code1")?(S(),P(j,{key:0},{default:_(()=>[...t[19]||(t[19]=[e("pre",null,`<!-- copy from input -->
<form>
  <input type="text" class="form-input" v-model="message1" id="message1" />
  <div class="flex flex-wrap gap-2 mt-5">
    <button type="button" class="btn btn-primary" @click="copy(message1)">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2">
        <path
          d="M6 11C6 8.17157 6 6.75736 6.87868 5.87868C7.75736 5 9.17157 5 12 5H15C17.8284 5 19.2426 5 20.1213 5.87868C21 6.75736 21 8.17157 21 11V16C21 18.8284 21 20.2426 20.1213 21.1213C19.2426 22 17.8284 22 15 22H12C9.17157 22 7.75736 22 6.87868 21.1213C6 20.2426 6 18.8284 6 16V11Z"
          stroke="currentColor"
          stroke-width="1.5"
        />
        <path
          opacity="0.5"
          d="M6 19C4.34315 19 3 17.6569 3 16V10C3 6.22876 3 4.34315 4.17157 3.17157C5.34315 2 7.22876 2 11 2H15C16.6569 2 18 3.34315 18 5"
          stroke="currentColor"
          stroke-width="1.5"
        />
      </svg>
      Copy from Input
    </button>
    <button type="button" class="btn btn-dark" @click="cut(message1)" @blur="message1 = ''">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 ltr:mr-2 rtl:ml-2">
        <path
          d="M15.2869 3.15178L14.3601 4.07866L5.83882 12.5999L5.83881 12.5999C5.26166 13.1771 4.97308 13.4656 4.7249 13.7838C4.43213 14.1592 4.18114 14.5653 3.97634 14.995C3.80273 15.3593 3.67368 15.7465 3.41556 16.5208L2.32181 19.8021L2.05445 20.6042C1.92743 20.9852 2.0266 21.4053 2.31063 21.6894C2.59466 21.9734 3.01478 22.0726 3.39584 21.9456L4.19792 21.6782L7.47918 20.5844L7.47919 20.5844C8.25353 20.3263 8.6407 20.1973 9.00498 20.0237C9.43469 19.8189 9.84082 19.5679 10.2162 19.2751C10.5344 19.0269 10.8229 18.7383 11.4001 18.1612L11.4001 18.1612L19.9213 9.63993L20.8482 8.71306C22.3839 7.17735 22.3839 4.68748 20.8482 3.15178C19.3125 1.61607 16.8226 1.61607 15.2869 3.15178Z"
          stroke="currentColor"
          stroke-width="1.5"
        />
        <path
          opacity="0.5"
          d="M14.36 4.07812C14.36 4.07812 14.4759 6.04774 16.2138 7.78564C17.9517 9.52354 19.9213 9.6394 19.9213 9.6394M4.19789 21.6777L2.32178 19.8015"
          stroke="currentColor"
          stroke-width="1.5"
        />
      </svg>
      Cut from Input
    </button>
  </div>
</form>

<!-- script -->
<script lang="ts" setup>
import { ref } from 'vue';
import useClipboard from 'vue-clipboard3';

const { toClipboard } = useClipboard();
const copy = async (msg) => {
  if (msg) {
    await toClipboard(msg);
    showMessage('Copied successfully.');
  }
};

const cut = async (msg) => {
  if (msg) {
    await toClipboard(msg);
    showMessage('Cut successfully.');
  }
};
<\/script>
`,-1)])]),_:1})):B("",!0)]),e("div",kt,[e("div",xt,[t[21]||(t[21]=e("h5",{class:"font-semibold text-lg dark:text-white-light"},"Copy form Textarea",-1)),e("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:t[5]||(t[5]=c=>M(k)("code2"))},[...t[20]||(t[20]=[H('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),e("div",Lt,[e("div",Mt,[e("form",null,[U(e("textarea",{rows:"3",wrap:"soft",class:"form-textarea","onUpdate:modelValue":t[6]||(t[6]=c=>v.value=c),id:"message2"},null,512),[[z,v.value]]),e("div",Et,[e("button",{type:"button",class:"btn btn-primary",onClick:t[7]||(t[7]=c=>s(v.value))},[...t[22]||(t[22]=[e("svg",{width:"24",height:"24",viewBox:"0 0 24 24",fill:"none",xmlns:"http://www.w3.org/2000/svg",class:"w-5 h-5 ltr:mr-2 rtl:ml-2"},[e("path",{d:"M6 11C6 8.17157 6 6.75736 6.87868 5.87868C7.75736 5 9.17157 5 12 5H15C17.8284 5 19.2426 5 20.1213 5.87868C21 6.75736 21 8.17157 21 11V16C21 18.8284 21 20.2426 20.1213 21.1213C19.2426 22 17.8284 22 15 22H12C9.17157 22 7.75736 22 6.87868 21.1213C6 20.2426 6 18.8284 6 16V11Z",stroke:"currentColor","stroke-width":"1.5"}),e("path",{opacity:"0.5",d:"M6 19C4.34315 19 3 17.6569 3 16V10C3 6.22876 3 4.34315 4.17157 3.17157C5.34315 2 7.22876 2 11 2H15C16.6569 2 18 3.34315 18 5",stroke:"currentColor","stroke-width":"1.5"})],-1),L(" Copy from Input ",-1)])]),e("button",{type:"button",class:"btn btn-dark",onClick:t[8]||(t[8]=c=>o(v.value)),onBlur:t[9]||(t[9]=c=>v.value="")},[...t[23]||(t[23]=[e("svg",{width:"24",height:"24",viewBox:"0 0 24 24",fill:"none",xmlns:"http://www.w3.org/2000/svg",class:"w-4.5 h-4.5 ltr:mr-2 rtl:ml-2"},[e("path",{d:"M15.2869 3.15178L14.3601 4.07866L5.83882 12.5999L5.83881 12.5999C5.26166 13.1771 4.97308 13.4656 4.7249 13.7838C4.43213 14.1592 4.18114 14.5653 3.97634 14.995C3.80273 15.3593 3.67368 15.7465 3.41556 16.5208L2.32181 19.8021L2.05445 20.6042C1.92743 20.9852 2.0266 21.4053 2.31063 21.6894C2.59466 21.9734 3.01478 22.0726 3.39584 21.9456L4.19792 21.6782L7.47918 20.5844L7.47919 20.5844C8.25353 20.3263 8.6407 20.1973 9.00498 20.0237C9.43469 19.8189 9.84082 19.5679 10.2162 19.2751C10.5344 19.0269 10.8229 18.7383 11.4001 18.1612L11.4001 18.1612L19.9213 9.63993L20.8482 8.71306C22.3839 7.17735 22.3839 4.68748 20.8482 3.15178C19.3125 1.61607 16.8226 1.61607 15.2869 3.15178Z",stroke:"currentColor","stroke-width":"1.5"}),e("path",{opacity:"0.5",d:"M14.36 4.07812C14.36 4.07812 14.4759 6.04774 16.2138 7.78564C17.9517 9.52354 19.9213 9.6394 19.9213 9.6394M4.19789 21.6777L2.32178 19.8015",stroke:"currentColor","stroke-width":"1.5"})],-1),L(" Cut from Input ",-1)])],32)])])])]),M(E).includes("code2")?(S(),P(j,{key:0},{default:_(()=>[...t[24]||(t[24]=[e("pre",null,`<!-- copy from textare -->
<form>
  <textarea rows="3" wrap="soft" class="form-textarea" v-model="message2" id="message2"></textarea>
  <div class="flex flex-wrap gap-2 mt-5">
    <button type="button" class="btn btn-primary" @click="copy(message2)">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2">
        <path
          d="M6 11C6 8.17157 6 6.75736 6.87868 5.87868C7.75736 5 9.17157 5 12 5H15C17.8284 5 19.2426 5 20.1213 5.87868C21 6.75736 21 8.17157 21 11V16C21 18.8284 21 20.2426 20.1213 21.1213C19.2426 22 17.8284 22 15 22H12C9.17157 22 7.75736 22 6.87868 21.1213C6 20.2426 6 18.8284 6 16V11Z"
          stroke="currentColor"
          stroke-width="1.5"
        />
        <path
          opacity="0.5"
          d="M6 19C4.34315 19 3 17.6569 3 16V10C3 6.22876 3 4.34315 4.17157 3.17157C5.34315 2 7.22876 2 11 2H15C16.6569 2 18 3.34315 18 5"
          stroke="currentColor"
          stroke-width="1.5"
        />
      </svg>
      Copy from Input
    </button>
    <button type="button" class="btn btn-dark" @click="cut(message2)" @blur="message2 = ''">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5 ltr:mr-2 rtl:ml-2">
        <path
          d="M15.2869 3.15178L14.3601 4.07866L5.83882 12.5999L5.83881 12.5999C5.26166 13.1771 4.97308 13.4656 4.7249 13.7838C4.43213 14.1592 4.18114 14.5653 3.97634 14.995C3.80273 15.3593 3.67368 15.7465 3.41556 16.5208L2.32181 19.8021L2.05445 20.6042C1.92743 20.9852 2.0266 21.4053 2.31063 21.6894C2.59466 21.9734 3.01478 22.0726 3.39584 21.9456L4.19792 21.6782L7.47918 20.5844L7.47919 20.5844C8.25353 20.3263 8.6407 20.1973 9.00498 20.0237C9.43469 19.8189 9.84082 19.5679 10.2162 19.2751C10.5344 19.0269 10.8229 18.7383 11.4001 18.1612L11.4001 18.1612L19.9213 9.63993L20.8482 8.71306C22.3839 7.17735 22.3839 4.68748 20.8482 3.15178C19.3125 1.61607 16.8226 1.61607 15.2869 3.15178Z"
          stroke="currentColor"
          stroke-width="1.5"
        />
        <path
          opacity="0.5"
          d="M14.36 4.07812C14.36 4.07812 14.4759 6.04774 16.2138 7.78564C17.9517 9.52354 19.9213 9.6394 19.9213 9.6394M4.19789 21.6777L2.32178 19.8015"
          stroke="currentColor"
          stroke-width="1.5"
        />
      </svg>
      Cut from Input
    </button>
  </div>
</form>

<!-- script -->
<script lang="ts" setup>
import { ref } from 'vue';
import useClipboard from 'vue-clipboard3';

const { toClipboard } = useClipboard();
const copy = async (msg) => {
  if (msg) {
    await toClipboard(msg);
    showMessage('Copied successfully.');
  }
};

const cut = async (msg) => {
  if (msg) {
    await toClipboard(msg);
    showMessage('Cut successfully.');
  }
};
<\/script>
`,-1)])]),_:1})):B("",!0)]),e("div",Tt,[e("div",St,[t[26]||(t[26]=e("h5",{class:"font-semibold text-lg dark:text-white-light"},"Copy Text from Paragraph",-1)),e("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:t[10]||(t[10]=c=>M(k)("code3"))},[...t[25]||(t[25]=[H('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),e("div",Ht,[e("div",Vt,[e("form",null,[t[28]||(t[28]=e("p",{class:"mb-3 font-semibold"},[L("Here is your OTP "),e("span",{class:"text-2xl",id:"copyOTP"},"22991"),L(".")],-1)),t[29]||(t[29]=e("p",{class:"font-semibold"},"Please do not share it to anyone",-1)),e("div",At,[e("button",{type:"button",class:"btn btn-primary",onClick:t[11]||(t[11]=c=>s("22991"))},[...t[27]||(t[27]=[e("svg",{width:"24",height:"24",viewBox:"0 0 24 24",fill:"none",xmlns:"http://www.w3.org/2000/svg",class:"w-5 h-5 ltr:mr-2 rtl:ml-2"},[e("path",{d:"M6 11C6 8.17157 6 6.75736 6.87868 5.87868C7.75736 5 9.17157 5 12 5H15C17.8284 5 19.2426 5 20.1213 5.87868C21 6.75736 21 8.17157 21 11V16C21 18.8284 21 20.2426 20.1213 21.1213C19.2426 22 17.8284 22 15 22H12C9.17157 22 7.75736 22 6.87868 21.1213C6 20.2426 6 18.8284 6 16V11Z",stroke:"currentColor","stroke-width":"1.5"}),e("path",{opacity:"0.5",d:"M6 19C4.34315 19 3 17.6569 3 16V10C3 6.22876 3 4.34315 4.17157 3.17157C5.34315 2 7.22876 2 11 2H15C16.6569 2 18 3.34315 18 5",stroke:"currentColor","stroke-width":"1.5"})],-1),L(" Copy from Paragraph ",-1)])])])])])]),M(E).includes("code3")?(S(),P(j,{key:0},{default:_(()=>[...t[30]||(t[30]=[e("pre",null,`<!-- copy from paragraph -->
<form>
  <p class="mb-3 font-semibold">Here is your OTP <span class="text-2xl" id="copyOTP">22991</span>.</p>
  <p class="font-semibold">Please do not share it to anyone</p>
  <div class="flex flex-wrap gap-2 mt-5">
    <button type="button" class="btn btn-primary" @click="copy('22991')">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2">
        <path
          d="M6 11C6 8.17157 6 6.75736 6.87868 5.87868C7.75736 5 9.17157 5 12 5H15C17.8284 5 19.2426 5 20.1213 5.87868C21 6.75736 21 8.17157 21 11V16C21 18.8284 21 20.2426 20.1213 21.1213C19.2426 22 17.8284 22 15 22H12C9.17157 22 7.75736 22 6.87868 21.1213C6 20.2426 6 18.8284 6 16V11Z"
          stroke="currentColor"
          stroke-width="1.5"
        />
        <path
          opacity="0.5"
          d="M6 19C4.34315 19 3 17.6569 3 16V10C3 6.22876 3 4.34315 4.17157 3.17157C5.34315 2 7.22876 2 11 2H15C16.6569 2 18 3.34315 18 5"
          stroke="currentColor"
          stroke-width="1.5"
        />
      </svg>
      Copy from Paragraph
    </button>
  </div>
</form>

<!-- script -->
<script lang="ts" setup>
import { ref } from 'vue';
import useClipboard from 'vue-clipboard3';

const { toClipboard } = useClipboard();
const copy = async (msg) => {
  if (msg) {
    await toClipboard(msg);
    showMessage('Copied successfully.');
  }
};
<\/script>
`,-1)])]),_:1})):B("",!0)]),e("div",Ot,[e("div",Pt,[t[32]||(t[32]=e("h5",{class:"font-semibold text-lg dark:text-white-light"},"Copy Hidden Text (Advanced)",-1)),e("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:t[12]||(t[12]=c=>M(k)("code4"))},[...t[31]||(t[31]=[H('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),e("div",Bt,[e("div",_t,[e("form",null,[t[35]||(t[35]=e("p",{class:"mb-3 font-semibold"},[e("span",null," Link -> "),L(),e("span",{id:"copyLink"}," http://www.admin-dashboard.com/code")],-1)),t[36]||(t[36]=e("span",{class:"absolute opacity-0",id:"copyHiddenCode"},"2291",-1)),e("div",jt,[e("button",{type:"button",class:"btn btn-primary",onClick:t[13]||(t[13]=c=>s("http://www.admin-dashboard.com/code"))},[...t[33]||(t[33]=[e("svg",{width:"24",height:"24",viewBox:"0 0 24 24",fill:"none",xmlns:"http://www.w3.org/2000/svg",class:"w-5 h-5 ltr:mr-2 rtl:ml-2"},[e("path",{d:"M6 11C6 8.17157 6 6.75736 6.87868 5.87868C7.75736 5 9.17157 5 12 5H15C17.8284 5 19.2426 5 20.1213 5.87868C21 6.75736 21 8.17157 21 11V16C21 18.8284 21 20.2426 20.1213 21.1213C19.2426 22 17.8284 22 15 22H12C9.17157 22 7.75736 22 6.87868 21.1213C6 20.2426 6 18.8284 6 16V11Z",stroke:"currentColor","stroke-width":"1.5"}),e("path",{opacity:"0.5",d:"M6 19C4.34315 19 3 17.6569 3 16V10C3 6.22876 3 4.34315 4.17157 3.17157C5.34315 2 7.22876 2 11 2H15C16.6569 2 18 3.34315 18 5",stroke:"currentColor","stroke-width":"1.5"})],-1),L(" Copy Link ",-1)])]),e("button",{type:"button",class:"btn btn-dark",onClick:t[14]||(t[14]=c=>s("2291"))},[...t[34]||(t[34]=[e("svg",{width:"24",height:"24",viewBox:"0 0 24 24",fill:"none",xmlns:"http://www.w3.org/2000/svg",class:"w-5 h-5 ltr:mr-2 rtl:ml-2"},[e("path",{d:"M6 11C6 8.17157 6 6.75736 6.87868 5.87868C7.75736 5 9.17157 5 12 5H15C17.8284 5 19.2426 5 20.1213 5.87868C21 6.75736 21 8.17157 21 11V16C21 18.8284 21 20.2426 20.1213 21.1213C19.2426 22 17.8284 22 15 22H12C9.17157 22 7.75736 22 6.87868 21.1213C6 20.2426 6 18.8284 6 16V11Z",stroke:"currentColor","stroke-width":"1.5"}),e("path",{opacity:"0.5",d:"M6 19C4.34315 19 3 17.6569 3 16V10C3 6.22876 3 4.34315 4.17157 3.17157C5.34315 2 7.22876 2 11 2H15C16.6569 2 18 3.34315 18 5",stroke:"currentColor","stroke-width":"1.5"})],-1),L(" Copy Hidden Code ",-1)])])])])])]),M(E).includes("code4")?(S(),P(j,{key:0},{default:_(()=>[...t[37]||(t[37]=[e("pre",null,`<!-- advanced -->
<form>
  <p class="mb-3 font-semibold"><span> Link -> </span> <span id="copyLink"> http://www.admin-dashboard.com/code</span></p>
  <span class="absolute opacity-0" id="copyHiddenCode">2291</span>
  <div class="flex flex-wrap gap-2 mt-5">
    <button type="button" class="btn btn-primary" @click="copy('http://www.admin-dashboard.com/code')">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2">
        <path
          d="M6 11C6 8.17157 6 6.75736 6.87868 5.87868C7.75736 5 9.17157 5 12 5H15C17.8284 5 19.2426 5 20.1213 5.87868C21 6.75736 21 8.17157 21 11V16C21 18.8284 21 20.2426 20.1213 21.1213C19.2426 22 17.8284 22 15 22H12C9.17157 22 7.75736 22 6.87868 21.1213C6 20.2426 6 18.8284 6 16V11Z"
          stroke="currentColor"
          stroke-width="1.5"
        />
        <path
          opacity="0.5"
          d="M6 19C4.34315 19 3 17.6569 3 16V10C3 6.22876 3 4.34315 4.17157 3.17157C5.34315 2 7.22876 2 11 2H15C16.6569 2 18 3.34315 18 5"
          stroke="currentColor"
          stroke-width="1.5"
        />
      </svg>
      Copy Link
    </button>
    <button type="button" class="btn btn-dark" @click="copy('2291')">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2">
        <path
          d="M6 11C6 8.17157 6 6.75736 6.87868 5.87868C7.75736 5 9.17157 5 12 5H15C17.8284 5 19.2426 5 20.1213 5.87868C21 6.75736 21 8.17157 21 11V16C21 18.8284 21 20.2426 20.1213 21.1213C19.2426 22 17.8284 22 15 22H12C9.17157 22 7.75736 22 6.87868 21.1213C6 20.2426 6 18.8284 6 16V11Z"
          stroke="currentColor"
          stroke-width="1.5"
        />
        <path
          opacity="0.5"
          d="M6 19C4.34315 19 3 17.6569 3 16V10C3 6.22876 3 4.34315 4.17157 3.17157C5.34315 2 7.22876 2 11 2H15C16.6569 2 18 3.34315 18 5"
          stroke="currentColor"
          stroke-width="1.5"
        />
      </svg>
      Copy Hidden Code
    </button>
  </div>
</form>

<!-- script -->
<script lang="ts" setup>
import { ref } from 'vue';
import useClipboard from 'vue-clipboard3';
const message1 = ref('http://www.admin-dashboard.com');
const message2 = ref('Lorem ipsum dolor sit amet, consectetur adipiscing elit...');

const { toClipboard } = useClipboard();
const copy = async (msg) => {
  if (msg) {
    await toClipboard(msg);
    showMessage('Copied successfully.');
  }
};
<\/script>
`,-1)])]),_:1})):B("",!0)])])])]))}});export{Ft as default};
