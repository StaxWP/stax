!function(e){var t={};function r(i){if(t[i])return t[i].exports;var o=t[i]={i:i,l:!1,exports:{}};return e[i].call(o.exports,o,o.exports,r),o.l=!0,o.exports}r.m=e,r.c=t,r.d=function(e,t,i){r.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:i})},r.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},r.t=function(e,t){if(1&t&&(e=r(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var i=Object.create(null);if(r.r(i),Object.defineProperty(i,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var o in e)r.d(i,o,function(t){return e[t]}.bind(null,o));return i},r.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return r.d(t,"a",t),t},r.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},r.p="",r(r.s=0)}([function(e,t,r){r(2),e.exports=r(1)},function(e,t){var r;(r=jQuery).staxCustomizeUtilities={setLiveCss:function(e,t){"use strict";var i="",o=r("."+e.styleClass);if("object"!=typeof t)return r(e.selectors).css(e.cssProperty,t.toString()+e.propertyUnit),!1;r.each(t,(function(t,r){var o;if("suffix"===t)return!0;var n=e.propertyUnit;switch("object"==typeof e.propertyUnit&&(n=e.propertyUnit[t]),o=e.selectors+"{ "+e.cssProperty+":"+r+n+"}",t){default:case"mobile":i+=o;break;case"desktop":i+="@media(min-width: 960px) {"+o+"}";break;case"tablet":i+="@media (min-width: 576px){"+o+"}"}})),o.length>0?o.text(i):r("head").append('<style type="text/css" class="'+e.styleClass+'">'+i+"</style>")}}},function(e,t,r){"use strict";r.r(t);const i=(e,t,r)=>{const i=e instanceof NodeList?e:[e];for(let e=0;e<i.length;e++)i[e]&&i[e].addEventListener(t,r)},o=(e,t)=>{s(e,t,"toggle")},n=(e,t)=>{s(e,t,"add")},a=(e,t)=>{s(e,t,"remove")},s=(e,t,r)=>{const i=t.split(" "),o=e instanceof NodeList?e:[e];for(let e=0;e<o.length;e++)o[e]&&o[e].classList[r].apply(o[e].classList,i)},l=["dropdown-open","active","nav-clickaway-overlay"],c=()=>{u(),document.addEventListener("click",(function(e){e.target.hash&&e.target.hash.includes("#")&&window.HFG.toggleMenuSidebar(!1)})),d(),m(),v(),window.HFG.initSearch=function(){m(),d()}},u=()=>{const e=document.querySelectorAll(".sub-menu, .minimal .stx-nav-search")||[];if(0===e.length)return;const t=window.innerWidth;e.forEach(e=>{const r=e.getBoundingClientRect(),i=r.left;i<0&&(e.style.right="auto",e.style.left=0),i+r.width>=t&&(e.style.right="100%",e.style.left="auto")}),"undefined"!=typeof menuCalcEvent&&window.dispatchEvent(menuCalcEvent)};function d(){const e=document.querySelectorAll(".caret-wrap");i(e,"click",f),i(e,"keyup",f)}function f(e){if(e.preventDefault(),e.stopPropagation(),e.keyCode&&![9,13].includes(e.keyCode))return;const t=e.currentTarget,r=t.parentNode.parentNode.querySelector(".sub-menu");o(t,l[0]),o(r,l[0]),b(document.querySelectorAll("."+l[0]),l[0])}let p={};function h(e){const t=p.elements||function(e=document){return p.elements=[...e.querySelectorAll('a[href], button, input, textarea, select, details,[tabindex]:not([tabindex="-1"])')].filter(e=>!e.hasAttribute("disabled")&&!e.getAttribute("aria-hidden")),p.elements}(p.container),r=9===e.keyCode,i=e.shiftKey,o=27===e.keyCode,n=document.activeElement,a=t[t.length-1],s=t[0];o&&(e.preventDefault(),p.container.querySelector(p.close).click(),p.backFocus.focus(),document.dispatchEvent(new CustomEvent("stx-focus-trap-end"))),!i&&r&&a===n&&(e.preventDefault(),s.focus()),i&&r&&s===n&&(e.preventDefault(),a.focus()),r&&s===a&&e.preventDefault()}function m(){const e=document.querySelectorAll(".stx-nav-search")||[],t=document.querySelectorAll(".menu-item-nav-search")||[],r=document.querySelectorAll(".close-responsive-search")||[];i(t,"click",e=>{e.preventDefault(),e.stopPropagation(),o(e.currentTarget,l[1]),b(e.currentTarget,l[1]),document.dispatchEvent(new CustomEvent("stx-focus-trap-start",{detail:{container:e.currentTarget.querySelector(".stx-nav-search"),close:".close-responsive-search",firstFocus:".search-field",backFocus:e.currentTarget}}))}),i(e,"click",e=>{e.stopPropagation()}),i(r,"click",e=>{e.preventDefault(),a(t,l[1]);const r=document.querySelector("."+l[2]);null!==r&&r.parentNode.removeChild(r)})}function v(){const e=document.querySelector(".header--row .menu-item-nav-cart");if(null===e)return;const t=e.querySelector(".stx-nav-cart");null!==t&&(t.style.left=e.getBoundingClientRect().left<350?0:null)}function b(e,t){let r=document.querySelector("."+l[2]);null!==r&&r.parentNode.removeChild(r),r=document.createElement("div"),n(r,l[2]);const i=document.querySelector("header.header");i.parentNode.insertBefore(r,i),r.addEventListener("click",()=>{a(e,t),r.parentNode.removeChild(r)})}document.addEventListener("stx-focus-trap-start",(function(e){p=e.detail,setTimeout(()=>{p.container.querySelector(p.firstFocus).focus()},100),document.addEventListener("keydown",h)})),document.addEventListener("stx-focus-trap-end",(function(){p={},document.removeEventListener("keydown",h)})),window.addEventListener("resize",v);const g=e=>{if(e.indexOf(",")<0)return y(e);return e.split(",").map(e=>y(e)).join(",")},y=e=>(e=e.trim()).indexOf(" ")<0?e:`"${e}"`,x=["top","right","bottom","left"],S=["mobile","tablet","desktop"],$={mobile:!1,tablet:"min-width: 576px",desktop:"min-width: 961px"};class C{run(e,t,r,i){const{selector:o,vars:n,responsive:a=!1,suffix:s="",fallback:l="",dispatchWindowResize:c=!1,valueRemap:u}=i;if(!o||!n)return!1;this.id=e,this.settingType=t,this.value=r,this.vars=n,this.selector=""+o,this.suffix=s,this.responsive=a,this.fallback=l,this.timeout=null,this.valueRemap=u;const d=this.getStyle();w(e,d,""+o),c&&(clearTimeout(this.timeout),this.timeout=setTimeout(()=>{window.dispatchEvent(new Event("resize"))},200))}getStyle(){const{vars:e,responsive:t}=this;if("backgroundControl"===e)return this.getBackgroundControlVars();if(this.isDirectionalValue(this.value))return this.getDirectionalNonResponsive();if(t)return this.getResponsiveVarCSS(this.selector,e,this.value,this.suffix,this.fallback);if(Array.isArray(e)){let t="";return e.forEach(e=>{this.vars=e,t+=this.getStringVarCSS()}),t}switch(typeof e){case"string":return this.getStringVarCSS();case"object":return this.getComposedVarCSS()}}getBackgroundControlVars(){const{value:e,selector:t}=this,{type:r,imageUrl:i,focusPoint:o,colorValue:n,overlayColorValue:a,overlayOpacity:s,useFeatured:l,fixed:c}=e,u={"--bgImage":"unset","--overlayColor":"unset","--bgOverlayOpacity":"unset","--bgAttachment":"unset","--bgPosition":"unset"};if("color"===r)u["--bgColor"]=n;else{const{currentFeaturedImage:e}=window.staxCustomizePreview;let t=i;l&&(t=e||i);const r=""!==t,{x:n,y:d}=o,f=`${(100*n).toFixed(0)}% ${(100*d).toFixed(0)}%`;u["--bgImage"]=r?`url("${t}")`:"none",u["--overlayColor"]=a||"transparent",u["--bgOverlayOpacity"]=""+s/100,u["--bgAttachment"]=c?"fixed":"unset",u["--bgPosition"]=f}return`${t}{${Object.entries(u).map(([e,t])=>`${e}:${t}`).join(";")}}`}getDirectionalNonResponsive(){const{selector:e,value:t,vars:r,suffix:i}=this,o=this.maybeParseJson(t);let n=i||"";void 0!==o.unit&&(n=o.unit);const a=this.parseDirectionalValue(o,n);return""!==a?`${e}{${r}:${a};}`:""}getResponsiveVarCSS(e,t,r,i,o){const n=this.maybeParseJson(r);if(void 0===n)return"";let a="";return S.forEach(r=>{let s=!1,l=i;void 0!==n[r+"-unit"]&&(l=n[r+"-unit"]),n.suffix&&n.suffix[r]&&(l=n.suffix[r]),n[r]||(s=!0);let c=n[r];c=s?o:this.parseDirectionalValue(c,l);let u="";"string"==typeof t&&""!==c&&(u=`${t}:${c};`),Array.isArray(t)&&t.forEach(e=>{let t=c;this.valueRemap&&this.valueRemap[e]&&this.valueRemap[e][c]&&(t=this.valueRemap[e][c]),u+=`${e}:${t};`}),$[r]&&(a+=`@media(${$[r]}) {`),a+=e+"{",a+=u,a+="}",$[r]&&(a+="}")}),a}getStringVarCSS(){const{selector:e,vars:t,value:r,suffix:i,fallback:o,validateSuffix:n,settingType:a}=this;return r||""===o?"\\Stax\\Customizer\\Core\\Controls\\React\\Font_Family"===a?`${e} {${t}:${g(r)};}`:`${e} {${t}:${r}${n(i)};}`:`${e} {${t}:${o};}`}getComposedVarCSS(){const{selector:e,vars:t,settingType:r,value:i,suffix:o,fallback:n}=this,a=this.isButtonSetting(r);let s="";return Object.keys(t).forEach(r=>{let l=o,c=t[r];if("object"==typeof c){if(c.suffix&&(l=c.suffix),c.responsive)return s+=this.getResponsiveVarCSS(e,r,i[c.key],l,n),!1;c=c.key}s+=e+" {";let u=i[c]||null;r.toLowerCase().includes("borderwidth")&&a&&"outline"!==i.type?s+=r+": 0;":(u=u?this.parseDirectionalValue(u,l):n,""!==u&&(s+=`${r}:${u};`),s+="}")}),s}isButtonSetting(e){return["\\Stax\\Customizer\\Core\\Controls\\React\\Button_Appearance","stax_button_appearance"].includes(e)}parseDirectionalValue(e,t){if("object"!=typeof e)return e+this.validateSuffix(t);if(!this.isDirectionalValue(e))return e;let r="",i=!0,o=0;return x.forEach(t=>{""===e[t]&&o++,4===o&&(i=!1)}),i&&x.forEach(i=>{""===e[i]?r+="0 ":r+=`${e[i]}${this.validateSuffix(t)} `}),r=r.trim(),r}isDirectionalValue(e){return void 0!==e.top&&void 0!==e.right&&void 0!==e.bottom&&void 0!==e.left}maybeParseJson(e){if("string"!=typeof e)return e;try{JSON.parse(e)}catch(t){return e}return JSON.parse(e)}validateSuffix(e){return["px","em","%","vh","vw"].includes(e)?e:""}}const w=(e,t="",r="")=>{let i=document.querySelector("#"+e+"-css-style");i||(i=document.createElement("style"),i.setAttribute("id",e+"-css-style"),i.setAttribute("type","text/css"),document.querySelector("body").appendChild(i)),i.innerHTML=t};function k(){let e=document.querySelector("#stax-css-vars-inline-css"),t={};return e=e.innerHTML.replace(":root{","").replace("}","").trim().split(";"),_.each(e,(function(e){(e=e.split(":"))[0]&&e[1]&&(t[e[0]]=e[1])})),t}function z(e){let t=document.querySelector("#stax-css-vars-inline-css"),r="";_.each(e,(function(e,t){r+=t+":"+e+";"})),r&&(t.innerHTML=":root{"+r+"}")}function R(e,t,r,i=""){var o=r;return i&&(o+=i),r?e["--"+t]=o:delete e["--"+t],e}var E;window.addEventListener("load",(function(){document.addEventListener("header_builder_panel_changed",(function(e){return"hfg_header_layout_partial"===e.detail.partial_id?(window.HFG.init(),window.HFG.initSearch(),u(),!1):"primary-menu_partial"===e.detail.partial_id?(c(),u(),!1):void 0})),document.addEventListener("customize_control_sidebar",function(e){"open"===e.detail&&window.HFG.toggleMenuSidebar(!0),"close"===e.detail&&window.HFG.toggleMenuSidebar(!1)}.bind(this)),document.addEventListener("customize_section_opened",function(e){"header_sidebar"===e.detail&&window.HFG.toggleMenuSidebar(!0)}.bind(this));const e={mobile:"max-width: 576px",tablet:"min-width: 576px",desktop:"min-width: 961px"},t=new C;_.each(staxCustomizePreview,(function(r,i){_.each(r,(function(r,o){wp.customize(o,(function(s){s.bind((function(s){if(r.additional&&r.additional.cssVar)return t.run(o,i,s,r.additional.cssVar),!1;if(r.additional&&r.additional.template)return((e,t,r,i)=>{const o={mobile:"max-width: 576px",tablet:"min-width: 576px",desktop:"min-width: 960px"};let n="";if(i.directional){if(i.responsive)for(const e in o){let t=i.template;const a=r[e+"-unit"];_.each(r[e],(function(e,r){const i=new RegExp(`{{value.${r}}}`,"g");t=t.replace(i,e+a)})),n+=`@media (${o[e]}) {${t}}`}else n=i.template,_.each(x,(function(e){const t=new RegExp(`{{value.${e}}}`,"g");n=n.replace(t,r[e]+r.unit)}));return w(t,n),!1}const a=new RegExp("{{value}}","g");if(i.responsive){const e=i.template,t=JSON.parse(r);for(const r in o){let s="";i.suffix&&(s=i.suffix[r]);let l=s;if(t[r+"-unit"]&&(l=t[r+"-unit"]),t.suffix&&t.suffix[r]&&(l=t.suffix[r]),0===t[r]||"0"===t[r])n+=`@media (${o[r]}) {${e.replace(a,"0"+l)}}`;else{const i=t[r]+l||"";""!==i&&(n+=`@media (${o[r]}) {${e.replace(a,i)}}`)}}}else if(0===r||"0"===r)n+=i.template.replace(a,"0");else{const e=r||i.fallback||"";""!==e&&(n+=i.template.replace(a,e.toString()))}w(t,n)})(0,o,s,r.additional,r.responsive,r.directional),R(i,o,s,r.additional),!1;let l="";switch(i){case"stax_color_control":if(r.additional.partial)return wp.customize.selectiveRefresh.partial(r.additional.partial).refresh(),!1;_.each(r.additional,e=>{if(!e.selector)return!1;s=s||e.fallback,l+=`body ${e.selector} { ${e.prop}: ${s} !important; }`}),w(o,l);break;case"stax_background_control":if("color"===s.type){!s.colorValue&&r.additional.partial&&wp.customize.selectiveRefresh.partial(r.additional.partial).refresh(),l+=`body ${r.selector}{background-image: none !important;}`;const e="undefined"!==s.colorValue?s.colorValue:"inherit";return l+=r.selector+":before{ content: none !important;}",l+=`body ${r.selector}, body ${r.selector} .primary-menu-ul .sub-menu {background-color: ${e}!important;}`,l+=`${r.selector} .primary-menu-ul .sub-menu, ${r.selector} .primary-menu-ul .sub-menu li {border-color: ${e}!important;}`,w(o,l),!1}s.useFeatured&&staxCustomizePreview.currentFeaturedImage&&(s.imageUrl=staxCustomizePreview.currentFeaturedImage),l+=r.selector+"{",l+=s.imageUrl?`background-image: url("${s.imageUrl}") !important;`:"background-image: none !important;",l+=!0===s.fixed?"background-attachment: fixed !important;":"background-attachment: initial !important;",s.focusPoint&&s.focusPoint.x&&s.focusPoint.y&&(l+="background-position:"+(100*s.focusPoint.x).toFixed(2)+"% "+(100*s.focusPoint.y).toFixed(2)+"% !important;"),l+="background-size: cover !important;",l+='top: 0; bottom: 0; width: 100%; content:"";',l+="}";const t=s.overlayColorValue||"unset",i=0===s.overlayOpacity?0:s.overlayOpacity||50;l+=`body ${r.selector}, body ${r.selector} .primary-menu-ul .sub-menu {background-color: ${t}!important;}`,l+=`${r.selector} .primary-menu-ul .sub-menu, ${r.selector} .primary-menu-ul .sub-menu li {border-color: ${t}!important;}`,l+=r.selector+':before { content: "";position: absolute; top: 0; bottom: 0; width: 100%;'+`background-color: ${t}!important;opacity: `+i/100+"!important;}",l+=r.selector+"{ background-color: transparent !important; }",w(o,l);break;case"\\Stax\\Customizer\\Core\\Controls\\React\\Responsive_Radio_Buttons":!function(e,t){if(!e.additional)return!1;const r=document.querySelectorAll(e.selector),i=[];Object.keys(t).map(e=>(i.push(`${e}-${t[e]}`),e)),_.each(r,(function(t){t.parentNode.classList.remove(...e.additional.remove_classes),t.parentNode.classList.add(...i)}))}(r,s);break;case"\\Stax\\Customizer\\Core\\Controls\\React\\Radio_Buttons":if(!r.additional)return!1;const c="hfg-item-v-top hfg-item-v-middle hfg-item-v-bottom",u="hfg-item-v-"+s,d=document.querySelectorAll(r.selector);_.each(d,(function(e){a(e.parentNode.parentNode,c),n(e.parentNode.parentNode,u)}));break;case"\\Stax\\Customizer\\Core\\Controls\\Radio_Image":break;case"\\Stax\\Customizer\\Core\\Controls\\Range":case"\\Stax\\Customizer\\Core\\Controls\\React\\Responsive_Range":const f=JSON.parse(s);f.mobile>0?l+="@media (max-width: 576px) { body "+r.selector+"{ "+r.additional.prop+":"+f.mobile+r.additional.unit+";}}":l+="@media (max-width: 576px) { body "+r.selector+"{ "+r.additional.prop+":unset;}}",f.tablet>0?l+="@media (min-width: 576px) { body "+r.selector+"{ "+r.additional.prop+":"+f.tablet+r.additional.unit+";}}":l+="@media (min-width: 576px) { body "+r.selector+"{ "+r.additional.prop+":unset;}}",f.desktop>0?l+="@media (min-width: 961px) { body "+r.selector+"{ "+r.additional.prop+":"+f.desktop+r.additional.unit+";}}":l+="@media (min-width: 961px) { body "+r.selector+"{ "+r.additional.prop+":unset;}}",w(o,l);break;case"\\Stax\\Customizer\\Core\\Controls\\React\\Spacing":for(const t in e){l+="@media ("+e[t]+") { body "+r.selector+"{";for(const e in s[t])if(""!==s[t][e]){let i=r.additional.prop+"-"+e;if("border-width"===r.additional.prop&&(i=`border-${e}-width`),"border-radius"===r.additional.prop){i=`border-${{top:"top-left",right:"top-right",bottom:"bottom-right",left:"bottom-left"}[e]}-radius`}l+=i+":"+s[t][e]+s[t+"-unit"]+";"}l+="}}"}w(o,l);break;case"\\Stax\\Customizer\\Core\\Controls\\React\\Typography":if(!0!==r.selector){l+=`html ${r.selector}{`,s.textTransform&&(l+=`text-transform: ${s.textTransform};`),"none"!==s.fontWeight&&(l+=`font-weight: ${s.fontWeight};`),l+="}";for(const t in e)l+=`@media (${e[t]}) {\n\t\t\t\t\t\t\t\t\t\t\thtml ${r.selector} {`,r.live_refresh_default&&r.live_refresh_default.size&&(l+=`font-size:${r.live_refresh_default.size[t]}${r.live_refresh_default.size.suffix[t]};`),s.fontSize&&s.fontSize[t]&&(l+=`font-size:${s.fontSize[t]}${s.fontSize.suffix[t]};`),r.live_refresh_default&&r.live_refresh_default.letter_spacing&&(l+=`letter-spacing:${r.live_refresh_default.letter_spacing[t]}px;`),s.letterSpacing&&s.letterSpacing[t]&&(l+=`letter-spacing:${s.letterSpacing[t]}px;`),r.live_refresh_default&&r.live_refresh_default.line_height&&(l+=`line-height:${r.live_refresh_default.line_height[t]}${r.live_refresh_default.line_height.suffix&&r.live_refresh_default.line_height.suffix[t]?r.live_refresh_default.line_height.suffix[t]:""};`),s.lineHeight&&s.lineHeight[t]&&(l+=`line-height:${s.lineHeight[t]}${s.lineHeight.suffix[t]||""};`),l+="}}";w(o,l)}else{let e=k();r.live_refresh_default&&r.live_refresh_default.size.vars&&r.live_refresh_default.size.vars.length&&(e=R(e,r.live_refresh_default.size.vars[0],r.live_refresh_default.size.mobile,r.live_refresh_default.size.suffix.mobile),e=R(e,r.live_refresh_default.size.vars[1],r.live_refresh_default.size.tablet,r.live_refresh_default.size.suffix.tablet),e=R(e,r.live_refresh_default.size.vars[2],r.live_refresh_default.size.desktop,r.live_refresh_default.size.suffix.desktop)),r.live_refresh_default&&r.live_refresh_default.letter_spacing.vars&&r.live_refresh_default.letter_spacing.vars.length&&(e=R(e,r.live_refresh_default.letter_spacing.vars[0],r.live_refresh_default.letter_spacing.mobile,r.live_refresh_default.letter_spacing.suffix.mobile),e=R(e,r.live_refresh_default.letter_spacing.vars[1],r.live_refresh_default.letter_spacing.tablet,r.live_refresh_default.letter_spacing.suffix.tablet),e=R(e,r.live_refresh_default.letter_spacing.vars[2],r.live_refresh_default.letter_spacing.desktop,r.live_refresh_default.letter_spacing.suffix.desktop)),r.live_refresh_default&&r.live_refresh_default.line_height.vars&&r.live_refresh_default.line_height.vars.length&&(e=R(e,r.live_refresh_default.line_height.vars[0],r.live_refresh_default.line_height.mobile,r.live_refresh_default.line_height.suffix.mobile),e=R(e,r.live_refresh_default.line_height.vars[1],r.live_refresh_default.line_height.tablet,r.live_refresh_default.line_height.suffix.tablet),e=R(e,r.live_refresh_default.line_height.vars[2],r.live_refresh_default.line_height.desktop,r.live_refresh_default.line_height.suffix.desktop)),s.fontSize&&s.fontSize.vars&&s.fontSize.vars.length&&(e=R(e,s.fontSize.vars[0],s.fontSize.mobile,s.fontSize.suffix.mobile),e=R(e,s.fontSize.vars[1],s.fontSize.tablet,s.fontSize.suffix.tablet),e=R(e,s.fontSize.vars[2],s.fontSize.desktop,s.fontSize.suffix.desktop)),s.letterSpacing&&s.letterSpacing.vars&&s.letterSpacing.vars.length&&(e=R(e,s.letterSpacing.vars[0],s.letterSpacing.mobile,s.letterSpacing.suffix.mobile),e=R(e,s.letterSpacing.vars[1],s.letterSpacing.tablet,s.letterSpacing.suffix.tablet),e=R(e,s.letterSpacing.vars[2],s.letterSpacing.desktop,s.letterSpacing.suffix.desktop)),s.lineHeight&&s.lineHeight.vars&&s.lineHeight.vars.length&&(e=R(e,s.lineHeight.vars[0],s.lineHeight.mobile,s.lineHeight.suffix.mobile),e=R(e,s.lineHeight.vars[1],s.lineHeight.tablet,s.lineHeight.suffix.tablet),e=R(e,s.lineHeight.vars[2],s.lineHeight.desktop,s.lineHeight.suffix.desktop)),z(e)}break;case"\\Stax\\Customizer\\Core\\Controls\\React\\Button_Appearance":const p=s.background||"unset",h=s.text||"currentColor",m=s.text||"";let v="html "+r.selector,b=`html ${r.selector} .icon-bar`;r.additional&&r.additional.additional_buttons&&_.each(r.additional.additional_buttons,e=>{v+=",html "+e.button,b+=",html "+e.button+" "+e.text}),l+=`${v} {\n\t\t\t\t\t\t\t\t\t\tbackground-color: ${p};\n\t\t\t\t\t\t\t\t\t\tcolor: ${h};`,"object"!=typeof s.borderRadius?l+=`border-radius: ${s.borderRadius}px;`:l+=`\n                                        border-top-left-radius: ${s.borderRadius.top}px;\n                                        border-top-right-radius: ${s.borderRadius.right}px;\n                                        border-bottom-right-radius: ${s.borderRadius.bottom}px;\n                                        border-bottom-left-radius: ${s.borderRadius.left}px;\n                                        `,"outline"===s.type&&("object"!=typeof s.borderWidth?l+=`border: ${s.borderWidth}px solid ${m};`:l+=`\n                                            border-style: solid;\n                                            border-color: ${m};\n                                            border-top-width: ${s.borderWidth.top}px;\n                                            border-right-width: ${s.borderWidth.right}px;\n                                            border-bottom-width: ${s.borderWidth.bottom}px;\n                                            border-left-width: ${s.borderWidth.left}px;\n                                            `),"fill"===s.type&&(l+="border: none;"),l+="}",l+=`${b} {\n\t\t\t\t\t\t\t\t\t\tbackground-color: ${h};\n\t\t\t\t\t\t\t\t\t\tcolor: ${h};\n\t\t\t\t\t\t\t\t\t}`,w(o,l);break;case"text":const g=document.querySelector(r.selector);if(""===s)return g.parentNode.removeChild(g),!1;if(null===g){const e=document.createElement(r.additional.html_tag);e.classList.add(r.additional.wrap_class),document.querySelector(r.additional.parent).prepend(e)}document.querySelector(r.selector).innerHTML=s;break;case"stax_range_control":case"\\Stax\\Customizer\\Core\\Controls\\React\\Range":if("svg-icon-size"===r.additional.type)return l+=`html ${r.selector} {\n\t\t\t\t\t\t\t\t\t\t\twidth: ${s}px;\n\t\t\t\t\t\t\t\t\t\t\theight: ${s}px;\n\t\t\t\t\t\t\t\t\t\t}`,w(o,l),!1;if("default"===r.additional.type)return w(o,l),!1;console.log(r,r.selector,s),l+=`html ${r.selector} {\n\t\t\t\t\t\t\t\t\t\t\t${r.additional.type}: ${s}px;\n\t\t\t\t\t\t\t\t\t\t}`,w(o,l);break;case"\\Stax\\Customizer\\Core\\Controls\\React\\Color":const y=""===s?"unset":s;l+=`html ${r.selector} {\n\t\t\t\t\t\t\t\t\t\t${r.additional.prop}: ${y};\n\t\t\t\t\t\t\t\t\t}`,w(o,l);break;case"\\Stax\\Customizer\\Core\\Controls\\React\\Font_Family":break;case"\\Stax\\Customizer\\Core\\Controls\\React\\Global_Colors":let x=k();const{palettes:S,activePalette:$}=s,{colors:C}=S[$];Object.keys(C).map(e=>{if("hsl"===C[e].output){var t=function(e){if(null==e||""==e)return null;var t,r,i=/^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(e),o=parseInt(i[1],16)/255,n=parseInt(i[2],16)/255,a=parseInt(i[3],16)/255,s=Math.max(o,n,a),l=Math.min(o,n,a),c=(s+l)/2;if(s===l)t=r=0;else{var u=s-l;switch(r=c>.5?u/(2-s-l):u/(s+l),s){case o:t=(n-a)/u+(n<a?6:0);break;case n:t=(a-o)/u+2;break;case a:t=(o-n)/u+4}t/=6}return[Math.ceil(360*t),Math.round(100*r)+"%",Math.round(100*c)+"%"]}(C[e].color);C[e].vars.map((e,r)=>({var:e,value:t[r]})).forEach(e=>{x=R(x,e.var,e.value)})}else x=R(x,C[e].vars,C[e].color);return!1}),z(x)}}))}))}))})),wp.customize.preview.bind("font-selection",(function(e){const t=staxCustomizePreview[e.type][e.controlId];let r=t.selector;const i=e.source,o=e.controlId+"_font_family";if("google"===i.toLowerCase()){const t=document.querySelector("#"+o),r="//fonts.googleapis.com/css?family="+e.value.replace(" ","+")+'%3A100%2C200%2C300%2C400%2C500%2C600%2C700%2C800&display=swap"';if(null!==t)t.setAttribute("href",r);else{const e=document.createElement("link");e.setAttribute("rel","stylesheet"),e.setAttribute("id",o),e.setAttribute("href",r),e.setAttribute("type","text/css"),e.setAttribute("media","all"),document.querySelector("head").appendChild(e)}}const{additional:n=!1}=t;if(!1!==n&&void 0!==n.cssVar)return!1;const a=e.inherit?"inherit":'-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif';if(r=r.split(","),r=r.map((function(e){return"html "+e})).join(","),!1===e.value)return w(e.controlId,r+"{font-family: "+a+";}"),!1;const s=g(e.value);w(e.controlId,r+"{font-family: "+s+" ;}")})),wp.customize("background_image",(function(e){e.bind((function(e){e||document.querySelector("body").classList.remove("custom-background")}))}))})),(E=jQuery).staxCustomizeUtilities={setLiveCss(e,t){let r="";if("object"!=typeof t)return E(e.selectors).css(e.cssProperty,t.toString()+e.propertyUnit),!1;E.each(t,(function(t,i){if("suffix"===t)return!0;let o=e.propertyUnit;"object"==typeof e.propertyUnit&&(o=e.propertyUnit[t]);const n=e.selectors+"{ "+e.cssProperty+":"+i+o+"}";switch(t){default:case"mobile":r+=n;break;case"desktop":r+="@media(min-width: 960px) {"+n+"}";break;case"tablet":r+="@media (min-width: 576px){"+n+"}"}}));const i=E("."+e.styleClass);i.length>0?i.text(r):E("head").append('<style type="text/css" class="'+e.styleClass+'">'+r+"</style>")}}}]);