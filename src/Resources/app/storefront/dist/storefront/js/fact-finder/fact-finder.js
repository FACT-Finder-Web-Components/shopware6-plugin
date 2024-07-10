(()=>{"use strict";var e={857:e=>{var t=function(e){var t;return!!e&&"object"==typeof e&&"[object RegExp]"!==(t=Object.prototype.toString.call(e))&&"[object Date]"!==t&&e.$$typeof!==r},r="function"==typeof Symbol&&Symbol.for?Symbol.for("react.element"):60103;function i(e,t){return!1!==t.clone&&t.isMergeableObject(e)?a(Array.isArray(e)?[]:{},e,t):e}function n(e,t,r){return e.concat(t).map(function(e){return i(e,r)})}function o(e){return Object.keys(e).concat(Object.getOwnPropertySymbols?Object.getOwnPropertySymbols(e).filter(function(t){return Object.propertyIsEnumerable.call(e,t)}):[])}function s(e,t){try{return t in e}catch(e){return!1}}function a(e,r,c){(c=c||{}).arrayMerge=c.arrayMerge||n,c.isMergeableObject=c.isMergeableObject||t,c.cloneUnlessOtherwiseSpecified=i;var l,u,d=Array.isArray(r);return d!==Array.isArray(e)?i(r,c):d?c.arrayMerge(e,r,c):(u={},(l=c).isMergeableObject(e)&&o(e).forEach(function(t){u[t]=i(e[t],l)}),o(r).forEach(function(t){(!s(e,t)||Object.hasOwnProperty.call(e,t)&&Object.propertyIsEnumerable.call(e,t))&&(s(e,t)&&l.isMergeableObject(r[t])?u[t]=(function(e,t){if(!t.customMerge)return a;var r=t.customMerge(e);return"function"==typeof r?r:a})(t,l)(e[t],r[t],l):u[t]=i(r[t],l))}),u)}a.all=function(e,t){if(!Array.isArray(e))throw Error("first argument should be an array");return e.reduce(function(e,r){return a(e,r,t)},{})},e.exports=a},49:(e,t,r)=>{r.d(t,{Z:()=>n});var i=r(140);class n{static isNode(e){return"object"==typeof e&&null!==e&&(e===document||e===window||e instanceof Node)}static hasAttribute(e,t){if(!n.isNode(e))throw Error("The element must be a valid HTML Node!");return"function"==typeof e.hasAttribute&&e.hasAttribute(t)}static getAttribute(e,t){let r=!(arguments.length>2)||void 0===arguments[2]||arguments[2];if(r&&!1===n.hasAttribute(e,t))throw Error('The required property "'.concat(t,'" does not exist!'));if("function"!=typeof e.getAttribute){if(r)throw Error("This node doesn't support the getAttribute function!");return}return e.getAttribute(t)}static getDataAttribute(e,t){let r=!(arguments.length>2)||void 0===arguments[2]||arguments[2],o=t.replace(/^data(|-)/,""),s=i.Z.toLowerCamelCase(o,"-");if(!n.isNode(e)){if(r)throw Error("The passed node is not a valid HTML Node!");return}if(void 0===e.dataset){if(r)throw Error("This node doesn't support the dataset attribute!");return}let a=e.dataset[s];if(void 0===a){if(r)throw Error('The required data attribute "'.concat(t,'" does not exist on ').concat(e,"!"));return a}return i.Z.parsePrimitive(a)}static querySelector(e,t){let r=!(arguments.length>2)||void 0===arguments[2]||arguments[2];if(r&&!n.isNode(e))throw Error("The parent node is not a valid HTML Node!");let i=e.querySelector(t)||!1;if(r&&!1===i)throw Error('The required element "'.concat(t,'" does not exist in parent node!'));return i}static querySelectorAll(e,t){let r=!(arguments.length>2)||void 0===arguments[2]||arguments[2];if(r&&!n.isNode(e))throw Error("The parent node is not a valid HTML Node!");let i=e.querySelectorAll(t);if(0===i.length&&(i=!1),r&&!1===i)throw Error('At least one item of "'.concat(t,'" must exist in parent node!'));return i}}},830:(e,t,r)=>{r.d(t,{Z:()=>i});class i{publish(e){let t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{},r=arguments.length>2&&void 0!==arguments[2]&&arguments[2],i=new CustomEvent(e,{detail:t,cancelable:r});return this.el.dispatchEvent(i),i}subscribe(e,t){let r=arguments.length>2&&void 0!==arguments[2]?arguments[2]:{},i=this,n=e.split("."),o=r.scope?t.bind(r.scope):t;if(r.once&&!0===r.once){let t=o;o=function(r){i.unsubscribe(e),t(r)}}return this.el.addEventListener(n[0],o),this.listeners.push({splitEventName:n,opts:r,cb:o}),!0}unsubscribe(e){let t=e.split(".");return this.listeners=this.listeners.reduce((e,r)=>([...r.splitEventName].sort().toString()===t.sort().toString()?this.el.removeEventListener(r.splitEventName[0],r.cb):e.push(r),e),[]),!0}reset(){return this.listeners.forEach(e=>{this.el.removeEventListener(e.splitEventName[0],e.cb)}),this.listeners=[],!0}get el(){return this._el}set el(e){this._el=e}get listeners(){return this._listeners}set listeners(e){this._listeners=e}constructor(e=document){this._el=e,e.$emitter=this,this._listeners=[]}}},140:(e,t,r)=>{r.d(t,{Z:()=>i});class i{static ucFirst(e){return e.charAt(0).toUpperCase()+e.slice(1)}static lcFirst(e){return e.charAt(0).toLowerCase()+e.slice(1)}static toDashCase(e){return e.replace(/([A-Z])/g,"-$1").replace(/^-/,"").toLowerCase()}static toLowerCamelCase(e,t){let r=i.toUpperCamelCase(e,t);return i.lcFirst(r)}static toUpperCamelCase(e,t){return t?e.split(t).map(e=>i.ucFirst(e.toLowerCase())).join(""):i.ucFirst(e.toLowerCase())}static parsePrimitive(e){try{return/^\d+(.|,)\d+$/.test(e)&&(e=e.replace(",",".")),JSON.parse(e)}catch(t){return e.toString()}}}},568:(e,t,r)=>{r.d(t,{Z:()=>c});var i=r(857),n=r.n(i),o=r(49),s=r(140),a=r(830);class c{init(){throw Error('The "init" method for the plugin "'.concat(this._pluginName,'" is not defined.'))}update(){}_init(){this._initialized||(this.init(),this._initialized=!0)}_update(){this._initialized&&this.update()}_mergeOptions(e){let t=s.Z.toDashCase(this._pluginName),r=o.Z.getDataAttribute(this.el,"data-".concat(t,"-config"),!1),i=o.Z.getAttribute(this.el,"data-".concat(t,"-options"),!1),a=[this.constructor.options,this.options,e];r&&a.push(window.PluginConfigManager.get(this._pluginName,r));try{i&&a.push(JSON.parse(i))}catch(e){throw console.error(this.el),Error('The data attribute "data-'.concat(t,'-options" could not be parsed to json: ').concat(e.message))}return n().all(a.filter(e=>e instanceof Object&&!(e instanceof Array)).map(e=>e||{}))}_registerInstance(){window.PluginManager.getPluginInstancesFromElement(this.el).set(this._pluginName,this),window.PluginManager.getPlugin(this._pluginName,!1).get("instances").push(this)}_getPluginName(e){return e||(e=this.constructor.name),e}constructor(e,t={},r=!1){if(!o.Z.isNode(e))throw Error("There is no valid element given.");this.el=e,this.$emitter=new a.Z(this.el),this._pluginName=this._getPluginName(r),this.options=this._mergeOptions(t),this._initialized=!1,this._registerInstance(),this._init()}}}},t={};function r(i){var n=t[i];if(void 0!==n)return n.exports;var o=t[i]={exports:{}};return e[i](o,o.exports,r),o.exports}r.m=e,(()=>{r.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return r.d(t,{a:t}),t}})(),(()=>{r.d=(e,t)=>{for(var i in t)r.o(t,i)&&!r.o(e,i)&&Object.defineProperty(e,i,{enumerable:!0,get:t[i]})}})(),(()=>{r.f={},r.e=e=>Promise.all(Object.keys(r.f).reduce((t,i)=>(r.f[i](e,t),t),[]))})(),(()=>{r.u=e=>"./js/fact-finder/"+e+".js"})(),(()=>{r.miniCssF=e=>{}})(),(()=>{r.g=function(){if("object"==typeof globalThis)return globalThis;try{return this||Function("return this")()}catch(e){if("object"==typeof window)return window}}()})(),(()=>{r.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t)})(),(()=>{var e={};r.l=(t,i,n,o)=>{if(e[t]){e[t].push(i);return}if(void 0!==n)for(var s,a,c=document.getElementsByTagName("script"),l=0;l<c.length;l++){var u=c[l];if(u.getAttribute("src")==t){s=u;break}}s||(a=!0,(s=document.createElement("script")).charset="utf-8",s.timeout=120,r.nc&&s.setAttribute("nonce",r.nc),s.src=t),e[t]=[i];var d=(r,i)=>{s.onerror=s.onload=null,clearTimeout(h);var n=e[t];if(delete e[t],s.parentNode&&s.parentNode.removeChild(s),n&&n.forEach(e=>e(i)),r)return r(i)},h=setTimeout(d.bind(null,void 0,{type:"timeout",target:s}),12e4);s.onerror=d.bind(null,s.onerror),s.onload=d.bind(null,s.onload),a&&document.head.appendChild(s)}})(),(()=>{r.r=e=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})}})(),(()=>{r.g.importScripts&&(e=r.g.location+"");var e,t=r.g.document;if(!e&&t&&(t.currentScript&&(e=t.currentScript.src),!e)){var i=t.getElementsByTagName("script");if(i.length)for(var n=i.length-1;n>-1&&!e;)e=i[n--].src}if(!e)throw Error("Automatic publicPath is not supported in this browser");e=e.replace(/#.*$/,"").replace(/\?.*$/,"").replace(/\/[^\/]+$/,"/"),r.p=e+"../../"})(),(()=>{var e={"fact-finder":0};r.f.j=(t,i)=>{var n=r.o(e,t)?e[t]:void 0;if(0!==n){if(n)i.push(n[2]);else{var o=new Promise((r,i)=>n=e[t]=[r,i]);i.push(n[2]=o);var s=r.p+r.u(t),a=Error();r.l(s,i=>{if(r.o(e,t)&&(0!==(n=e[t])&&(e[t]=void 0),n)){var o=i&&("load"===i.type?"missing":i.type),s=i&&i.target&&i.target.src;a.message="Loading chunk "+t+" failed.\n("+o+": "+s+")",a.name="ChunkLoadError",a.type=o,a.request=s,n[1](a)}},"chunk-"+t,t)}}};var t=(t,i)=>{var n,o,[s,a,c]=i,l=0;if(s.some(t=>0!==e[t])){for(n in a)r.o(a,n)&&(r.m[n]=a[n]);c&&c(r)}for(t&&t(i);l<s.length;l++)o=s[l],r.o(e,o)&&e[o]&&e[o][0](),e[o]=0},i=self.webpackChunk=self.webpackChunk||[];i.forEach(t.bind(null,0)),i.push=t.bind(null,i.push.bind(i))})(),(()=>{var e=r(568),t=r(49);class i extends e.Z{init(){this.registerEvents()}registerEvents(){window.PluginManager.getPluginInstances("AddToCart").forEach(e=>e.$emitter.subscribe("beforeFormSubmit",this.trackAddToCart.bind(this)))}getQuantity(e){if("count_as_one"===ffTrackingSettings.addToCart.count)return 1;try{let r=t.Z.querySelector(e,'[name$="[quantity]"]');return parseInt(r.value,10)}catch(e){return 1}}async trackAddToCart(e){let r=t.Z.querySelector(e.target,'[name="product-number"]'),i=this.getQuantity(e.target);r&&new Promise(e=>{void 0!==window.factfinder?e(window.factfinder):document.addEventListener("ffReady",t=>e(t.factfinder))}).then(e=>{let t=e.communication.Util.trackingHelper;e.communication.EventAggregator.addFFEvent({type:"getRecords",recordId:r.value,idType:"productNumber",success:r=>{let[n]=r,o=e.communication.fieldRoles;e.communication.Tracking.cart({id:(e=>{let{record:t}=e;return t[o.trackingProductNumber]||t[o.productNumber]})(n),masterId:(e=>{let{record:t}=e;return t[o.masterArticleNumber]||t[o.masterId]})(n),price:t.getPrice(n),title:t.getTitle(n),count:i})}})})}}class n extends e.Z{init(){this.registerEvents()}registerEvents(){document.addEventListener("click",this._handleToggleFilter.bind(this))}_handleToggleFilter(e){let t=e=>this._eventPath(e).find(e=>"FF-ASN-GROUP"===e.tagName);t(e)||document.querySelectorAll("ff-asn-group").forEach(e=>{e.opened&&e.toggle(!0)}),t(e)&&(e=>{let t=e.target.closest("ff-asn-group");return[...document.querySelectorAll("ff-asn-group")].filter(e=>e!==t)})(e).forEach(e=>{e.opened&&e.toggle(!0)})}_eventPath(e){var t=e.composedPath&&e.composedPath()||e.path,r=e.target;return null!=t?0>t.indexOf(window)?t.concat(window):t:r===window?[window]:[r].concat(function e(t,r){r=r||[];var i=t.parentNode;return i?e(i,r.concat(i)):r}(r),window)}}let o=window.PluginManager;o.register("TrackingPlugin",i),o.register("AsnPlugin",n),o.override("OffCanvasFilter",()=>r.e("custom_plugins_FactFinder_src_Resources_app_storefront_src_plugin_offcanvas-filter_plugin_js").then(r.bind(r,600)),"[data-off-canvas-filter]")})()})();

