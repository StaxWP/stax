!function(e){var t={};function n(o){if(t[o])return t[o].exports;var r=t[o]={i:o,l:!1,exports:{}};return e[o].call(r.exports,r,r.exports,n),r.l=!0,r.exports}n.m=e,n.c=t,n.d=function(e,t,o){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:o})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,t){if(1&t&&(e=n(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var o=Object.create(null);if(n.r(o),Object.defineProperty(o,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var r in e)n.d(o,r,function(t){return e[t]}.bind(null,r));return o},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="",n(n.s=0)}([function(e,t,n){"use strict";n.r(t);const o=(e,t,n)=>{const o=e instanceof NodeList?e:[e];for(let e=0;e<o.length;e++)o[e]&&o[e].addEventListener(t,n)},r=(e,t)=>{u(e,t,"toggle")},c=(e,t)=>{u(e,t,"add")},i=(e,t)=>{u(e,t,"remove")},u=(e,t,n)=>{const o=t.split(" "),r=e instanceof NodeList?e:[e];for(let e=0;e<r.length;e++)r[e]&&r[e].classList[n].apply(r[e].classList,o)},l=["dropdown-open","active","nav-clickaway-overlay"],a=()=>{s(),document.addEventListener("click",(function(e){e.target.hash&&e.target.hash.includes("#")&&window.HFG.toggleMenuSidebar(!1)})),d(),y(),v(),window.HFG.initSearch=function(){y(),d()}},s=()=>{const e=document.querySelectorAll(".sub-menu, .minimal .stx-nav-search")||[];if(0===e.length)return;const t=window.innerWidth;e.forEach(e=>{const n=e.getBoundingClientRect(),o=n.left;o<0&&(e.style.right="auto",e.style.left=0),o+n.width>=t&&(e.style.right="100%",e.style.left="auto")}),"undefined"!=typeof menuCalcEvent&&window.dispatchEvent(menuCalcEvent)};function d(){const e=document.querySelectorAll(".caret-wrap");o(e,"click",f),o(e,"keyup",f)}function f(e){if(e.preventDefault(),e.stopPropagation(),e.keyCode&&![9,13].includes(e.keyCode))return;const t=e.currentTarget,n=t.parentNode.parentNode.querySelector(".sub-menu");r(t,l[0]),r(n,l[0]),g(document.querySelectorAll("."+l[0]),l[0])}let m={};function p(e){const t=m.elements||function(e=document){return m.elements=[...e.querySelectorAll('a[href], button, input, textarea, select, details,[tabindex]:not([tabindex="-1"])')].filter(e=>!e.hasAttribute("disabled")&&!e.getAttribute("aria-hidden")),m.elements}(m.container),n=9===e.keyCode,o=e.shiftKey,r=27===e.keyCode,c=document.activeElement,i=t[t.length-1],u=t[0];r&&(e.preventDefault(),m.container.querySelector(m.close).click(),m.backFocus.focus(),document.dispatchEvent(new CustomEvent("stx-focus-trap-end"))),!o&&n&&i===c&&(e.preventDefault(),u.focus()),o&&n&&u===c&&(e.preventDefault(),i.focus()),n&&u===i&&e.preventDefault()}function y(){const e=document.querySelectorAll(".stx-nav-search")||[],t=document.querySelectorAll(".menu-item-nav-search")||[],n=document.querySelectorAll(".close-responsive-search")||[];o(t,"click",e=>{e.preventDefault(),e.stopPropagation(),r(e.currentTarget,l[1]),g(e.currentTarget,l[1]),document.dispatchEvent(new CustomEvent("stx-focus-trap-start",{detail:{container:e.currentTarget.querySelector(".stx-nav-search"),close:".close-responsive-search",firstFocus:".search-field",backFocus:e.currentTarget}}))}),o(e,"click",e=>{e.stopPropagation()}),o(n,"click",e=>{e.preventDefault(),i(t,l[1]);const n=document.querySelector("."+l[2]);null!==n&&n.parentNode.removeChild(n)})}function v(){const e=document.querySelector(".header--row .menu-item-nav-cart");if(null===e)return;const t=e.querySelector(".stx-nav-cart");null!==t&&(t.style.left=e.getBoundingClientRect().left<350?0:null)}function g(e,t){let n=document.querySelector("."+l[2]);null!==n&&n.parentNode.removeChild(n),n=document.createElement("div"),c(n,l[2]);const o=document.querySelector("header.header");o.parentNode.insertBefore(n,o),n.addEventListener("click",()=>{i(e,t),n.parentNode.removeChild(n)})}document.addEventListener("stx-focus-trap-start",(function(e){m=e.detail,setTimeout(()=>{m.container.querySelector(m.firstFocus).focus()},100),document.addEventListener("keydown",p)})),document.addEventListener("stx-focus-trap-end",(function(){m={},document.removeEventListener("keydown",p)})),window.addEventListener("resize",v);const h=function(){this.options={menuToggleDuration:300},this.init()};function b(){s()}let S;h.prototype.init=function(e=!1){if(!1===e){const e=document.querySelectorAll(".close-sidebar-panel .navbar-toggle");o(e,"click",()=>{this.toggleMenuSidebar(!1)})}const t=document.querySelectorAll(".menu-mobile-toggle");o(t,"click",e=>{this.toggleMenuSidebar(!0,e.target)});const n=document.querySelector(".header-menu-sidebar-overlay");n&&o(n,"click",function(){this.toggleMenuSidebar(!1)}.bind(this))},h.prototype.toggleMenuSidebar=function(e,t=null){const n=document.querySelectorAll(".menu-mobile-toggle");if(i(document.body,"hiding-header-menu-sidebar"),!1===e){const e=document.querySelector(".nav-clickaway-overlay");null!==e&&e.parentNode.removeChild(e),c(document.body,"hiding-header-menu-sidebar"),i(document.body,"is-menu-sidebar"),i(n,"is-active"),setTimeout(function(){i(document.body,"hiding-header-menu-sidebar")}.bind(this),1e3)}else c(document.body,"is-menu-sidebar"),c(n,"is-active"),t&&document.dispatchEvent(new CustomEvent("stx-focus-trap-start",{detail:{container:document.getElementById("header-menu-sidebar"),close:".close-sidebar-panel button",firstFocus:"a,input",backFocus:t}}))},window.addEventListener("load",()=>{window.HFG=new h,a()}),window.addEventListener("resize",()=>{clearTimeout(S),S=setTimeout(b,500)})}]);