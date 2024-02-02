"use strict";(self.webpackChunk=self.webpackChunk||[]).push([["fact-finder"],{5025:(e,t,n)=>{var s=n(6285),a=n(3206);class r extends s.Z{init(){this.registerEvents()}registerEvents(){window.PluginManager.getPluginInstances("AddToCart").forEach((e=>e.$emitter.subscribe("beforeFormSubmit",this.trackAddToCart.bind(this))))}getQuantity(e){if("count_as_one"===ffTrackingSettings.addToCart.count)return 1;try{const t=a.Z.querySelector(e,'[name$="[quantity]"]');return parseInt(t.value,10)}catch(e){return 1}}async trackAddToCart(e){const t=a.Z.querySelector(e.target,'[name="product-number"]'),n=this.getQuantity(e.target);t&&new Promise((e=>{void 0!==window.factfinder?e(window.factfinder):document.addEventListener("ffReady",(t=>e(t.factfinder)))})).then((e=>{const s=e.communication.Util.trackingHelper;e.communication.EventAggregator.addFFEvent({type:"getRecords",recordId:t.value,idType:"productNumber",success:([t])=>{const a=e.communication.fieldRoles;e.communication.Tracking.cart({id:(({record:e})=>e[a.trackingProductNumber]||e[a.productNumber])(t),masterId:(({record:e})=>e[a.masterArticleNumber]||e[a.masterId])(t),price:s.getPrice(t),title:s.getTitle(t),count:n})}})}))}}class o extends s.Z{init(){this.registerEvents()}registerEvents(){document.addEventListener("click",this._handleToggleFilter.bind(this))}_handleToggleFilter(e){const t=e=>this._eventPath(e).find((e=>e.tagName==="ff-asn-group".toUpperCase()));t(e)||document.querySelectorAll("ff-asn-group").forEach((e=>{e.opened&&e.toggle(!0)})),t(e)&&(e=>{const t=e.target.closest("ff-asn-group");return[...document.querySelectorAll("ff-asn-group")].filter((e=>e!==t))})(e).forEach((e=>{e.opened&&e.toggle(!0)}))}_eventPath(e){var t=e.composedPath&&e.composedPath()||e.path,n=e.target;if(null!=t)return t.indexOf(window)<0?t.concat(window):t;if(n===window)return[window];return[n].concat(function e(t,n){n=n||[];var s=t.parentNode;return s?e(s,n.concat(s)):n}(n),window)}}var i=n(9206),c=n(3637);function f(e,t,n){return(t=function(e){var t=function(e,t){if("object"!=typeof e||null===e)return e;var n=e[Symbol.toPrimitive];if(void 0!==n){var s=n.call(e,t||"default");if("object"!=typeof s)return s;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===t?String:Number)(e)}(e,"string");return"symbol"==typeof t?t:String(t)}(t))in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}class l extends i.Z{constructor(...e){super(...e),f(this,"ASNMobileClass","ffw-asn-vertical"),f(this,"ASNGroupMobileClass","ffw-asn-group-vertical"),f(this,"ASNGroupElementMobileClass","ffw-asn-group-element-vertical")}_onCloseOffCanvas(e){setTimeout((()=>{const t=e.detail.offCanvasContent&&e.detail.offCanvasContent[0];if(!t)throw Error("There was nothing passed as `event.detail.offCanvasContent` in the `onCloseOffcanvas` event");this._toggleASNMobileMode(t.querySelector("ff-asn"));document.querySelector("#filtersOrigin").appendChild(t)})),document.$emitter.unsubscribe("onCloseOffcanvas",this._onCloseOffCanvas.bind(this))}_onClickOffCanvasFilter(e){e.preventDefault();const t=document.querySelector('[data-offcanvas-filter-content="true"]');if(this._toggleASNMobileMode(t.querySelector("ff-asn")),!t)throw Error('There was no DOM element with the data attribute "data-offcanvas-filter-content".');c.Z.open("",(()=>{}),"bottom",!0,c.Z.REMOVE_OFF_CANVAS_DELAY(),!0,"offcanvas-filter"),setTimeout((()=>{document.querySelector(".offcanvas").appendChild(t)})),document.$emitter.subscribe("onCloseOffcanvas",this._onCloseOffCanvas.bind(this)),this.$emitter.publish("onClickOffCanvasFilter")}_toggleASNMobileMode(e){const t=e=>t=>n=>t.classList[e](...n),n=t("add"),s=t("remove");e.querySelectorAll("ff-asn-group").forEach((e=>{const a=e.querySelector('[slot="groupCaption"]'),r=e.querySelectorAll("ff-asn-group-element"),o=[this.ASNGroupMobileClass,"btn-block"];e.classList.contains(this.ASNGroupMobileClass)?t(e)(o):n(e)(o),a.classList.contains("btn-block")?s(a)(["btn-block"]):n(a)(["btn-block"]),r.forEach((e=>{e.classList.contains(this.ASNGroupElementMobileClass)?s(e)([this.ASNGroupElementMobileClass]):n(e)([this.ASNGroupElementMobileClass])}))}))}}const d=window.PluginManager;d.register("TrackingPlugin",r),d.register("AsnPlugin",o),d.override("OffCanvasFilter",l,"[data-offcanvas-filter]")},9206:(e,t,n)=>{n.d(t,{Z:()=>i});var s=n(3637),a=n(6285),r=n(3206),o=n(4432);class i extends a.Z{init(){this._registerEventListeners()}_registerEventListeners(){this.el.addEventListener("click",this._onClickOffCanvasFilter.bind(this))}_onCloseOffCanvas(e){const t=e.detail.offCanvasContent[0];(o.Z.isActive("v6.6.0.0")?document.querySelector('[data-off-canvas-filter-content="true"]'):document.querySelector('[data-offcanvas-filter-content="true"]')).innerHTML=t.innerHTML,document.$emitter.unsubscribe("onCloseOffcanvas",this._onCloseOffCanvas.bind(this)),window.PluginManager.getPluginInstances("Listing")[0].refreshRegistry()}_onClickOffCanvasFilter(e){e.preventDefault();const t=o.Z.isActive("v6.6.0.0")?document.querySelector('[data-off-canvas-filter-content="true"]'):document.querySelector('[data-offcanvas-filter-content="true"]');if(!t)throw Error('There was no DOM element with the data attribute "data-offcanvas-filter-content".');s.Z.open(t.innerHTML,(()=>{}),"bottom",!0,s.Z.REMOVE_OFF_CANVAS_DELAY(),!0,"offcanvas-filter");r.Z.querySelector(t,".filter-panel").remove(),window.PluginManager.getPluginInstances("Listing")[0].refreshRegistry(),document.$emitter.subscribe("onCloseOffcanvas",this._onCloseOffCanvas.bind(this)),this.$emitter.publish("onClickOffCanvasFilter")}}},3637:(e,t,n)=>{n.d(t,{Z:()=>l,r:()=>f});var s=n(9658),a=n(2005),r=n(1966);const o="offcanvas",i=350;class c{constructor(){this.$emitter=new a.Z}open(e,t,n,s,a,r,o){this._removeExistingOffCanvas();const i=this._createOffCanvas(n,r,o,s);this.setContent(e,s,a),this._openOffcanvas(i,t)}setContent(e,t,n){const s=this.getOffCanvas();s[0]&&(s[0].innerHTML=e,this._registerEvents(n))}setAdditionalClassName(e){this.getOffCanvas()[0].classList.add(e)}getOffCanvas(){return document.querySelectorAll(`.${o}`)}close(e){const t=this.getOffCanvas();r.Z.iterate(t,(e=>{bootstrap.Offcanvas.getInstance(e).hide()})),setTimeout((()=>{this.$emitter.publish("onCloseOffcanvas",{offCanvasContent:t})}),e)}goBackInHistory(){window.history.back()}exists(){return this.getOffCanvas().length>0}_openOffcanvas(e,t){c.bsOffcanvas.show(),window.history.pushState("offcanvas-open",""),"function"==typeof t&&t()}_registerEvents(e){const t=s.Z.isTouchDevice()?"touchend":"click",n=this.getOffCanvas();r.Z.iterate(n,(t=>{const s=()=>{setTimeout((()=>{t.remove(),this.$emitter.publish("onCloseOffcanvas",{offCanvasContent:n})}),e),t.removeEventListener("hide.bs.offcanvas",s)};t.addEventListener("hide.bs.offcanvas",s)})),window.addEventListener("popstate",this.close.bind(this,e),{once:!0});const a=document.querySelectorAll(".js-offcanvas-close");r.Z.iterate(a,(n=>n.addEventListener(t,this.close.bind(this,e))))}_removeExistingOffCanvas(){c.bsOffcanvas=null;const e=this.getOffCanvas();return r.Z.iterate(e,(e=>e.remove()))}_getPositionClass(e){return"left"===e?"offcanvas-start":"right"===e?"offcanvas-end":`offcanvas-${e}`}_createOffCanvas(e,t,n,s){const a=document.createElement("div");if(a.classList.add(o),a.classList.add(this._getPositionClass(e)),!0===t&&a.classList.add("is-fullwidth"),n){const e=typeof n;if("string"===e)a.classList.add(n);else{if(!Array.isArray(n))throw new Error(`The type "${e}" is not supported. Please pass an array or a string.`);n.forEach((e=>{a.classList.add(e)}))}}return document.body.appendChild(a),c.bsOffcanvas=new bootstrap.Offcanvas(a,{backdrop:!1!==s||"static"}),a}}const f=Object.freeze(new c);class l{static open(e,t=null,n="left",s=!0,a=350,r=!1,o=""){f.open(e,t,n,s,a,r,o)}static setContent(e,t=!0,n=350){f.setContent(e,t,n)}static setAdditionalClassName(e){f.setAdditionalClassName(e)}static close(e=350){f.close(e)}static exists(){return f.exists()}static getOffCanvas(){return f.getOffCanvas()}static REMOVE_OFF_CANVAS_DELAY(){return i}}}},e=>{e.O(0,["vendor-node","vendor-shared"],(()=>{return t=5025,e(e.s=t);var t}));e.O()}]);