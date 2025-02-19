/*! For license information please see editor.js.LICENSE.txt */
!function(){"use strict";var e={"./node_modules/react-dom/client.js":function(e,t,n){var r=n("react-dom"),o=r.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED;t.createRoot=function(e,t){o.usingClientEntryPoint=!0;try{return r.createRoot(e,t)}finally{o.usingClientEntryPoint=!1}},t.hydrateRoot=function(e,t,n){o.usingClientEntryPoint=!0;try{return r.hydrateRoot(e,t,n)}finally{o.usingClientEntryPoint=!1}}},react:function(e){e.exports=window.React},"react-dom":function(e){e.exports=window.ReactDOM},"@elementor/editor-v1-adapters":function(e){e.exports=window.elementorV2.editorV1Adapters},"@elementor/locations":function(e){e.exports=window.elementorV2.locations},"@elementor/query":function(e){e.exports=window.elementorV2.query},"@elementor/store":function(e){e.exports=window.elementorV2.store},"@elementor/ui":function(e){e.exports=window.elementorV2.ui}},t={};function n(r){var o=t[r];if(void 0!==o)return o.exports;var i=t[r]={exports:{}};return e[r](i,i.exports,n),i.exports}n.d=function(e,t){for(var r in t)n.o(t,r)&&!n.o(e,r)&&Object.defineProperty(e,r,{enumerable:!0,get:t[r]})},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})};var r={};!function(){n.r(r),n.d(r,{init:function(){return E},injectIntoLogic:function(){return f},injectIntoTop:function(){return d}});var e=n("@elementor/locations"),t=n("react"),o=n("react-dom"),i=n("./node_modules/react-dom/client.js"),c=n("@elementor/editor-v1-adapters"),l=n("@elementor/query"),u=n("@elementor/store"),a=n("@elementor/ui"),{Slot:s,inject:d}=(0,e.createLocation)(),{Slot:m,inject:f}=(0,e.createLocation)();function y(){return t.createElement(t.Fragment,null,t.createElement(s,null),t.createElement("div",{style:{display:"none"}},t.createElement(m,null)))}function p(){return window.elementor?.getPreferences?.("ui_theme")||"auto"}function _({children:e}){const n=function(){const[e,n]=(0,t.useState)((()=>p()));return(0,t.useEffect)((()=>(0,c.__privateListenTo)((0,c.v1ReadyEvent)(),(()=>n(p())))),[]),(0,t.useEffect)((()=>(0,c.__privateListenTo)((0,c.commandEndEvent)("document/elements/settings"),(e=>{const t=e;t.args?.settings&&"ui_theme"in t.args.settings&&n(p())}))),[]),e}();return t.createElement(a.ThemeProvider,{colorScheme:n},e)}function E(e){const n=(0,u.__createStore)(),r=(0,l.createQueryClient)();(0,c.__privateDispatchReadyEvent)(),function(e,t){let n;try{const r=(0,i.createRoot)(t);n=()=>{r.render(e)}}catch{n=()=>{o.render(e,t)}}n()}(t.createElement(u.__StoreProvider,{store:n},t.createElement(l.QueryClientProvider,{client:r},t.createElement(a.DirectionProvider,{rtl:"rtl"===window.document.dir},t.createElement(_,null,t.createElement(y,null))))),e)}}(),(window.elementorV2=window.elementorV2||{}).editor=r}();