(self.webpackChunk=self.webpackChunk||[]).push([[9350],{70306:(t,e,r)=>{"use strict";r.d(e,{Z:()=>c});var n=r(87757),a=r.n(n),i=r(9669),s=r.n(i);function o(t,e,r,n,a,i,s){try{var o=t[i](s),c=o.value}catch(t){return void r(t)}o.done?e(c):Promise.resolve(c).then(n,a)}const c={props:{id:{type:[Number,String],required:!0}},data:function(){return{gamePackageId:null}},computed:{component:function(){var t=this;return this.gamePackageId?function(){return r(99593)("./".concat(t.gamePackageId,"/resources/js/pages/show"))}:null}},created:function(){var t,e=this;return(t=a().mark((function t(){var r,n;return a().wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return t.next=2,s().get("/api/history/games/".concat(e.id,"/package"));case 2:r=t.sent,n=r.data,e.gamePackageId=n.id;case 5:case"end":return t.stop()}}),t)})),function(){var e=this,r=arguments;return new Promise((function(n,a){var i=t.apply(e,r);function s(t){o(i,n,a,s,c,"next",t)}function c(t){o(i,n,a,s,c,"throw",t)}s(void 0)}))})()}}},92344:(t,e,r)=>{"use strict";r.d(e,{Z:()=>n});const n={props:["id","small"]}},60401:(t,e,r)=>{"use strict";r.d(e,{s:()=>n,x:()=>a});var n=function(){var t=this,e=t.$createElement;return(t._self._c||e)(t.component,{tag:"component",attrs:{id:t.id}})},a=[]},96007:(t,e,r)=>{"use strict";r.d(e,{s:()=>n,x:()=>a});var n=function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("v-menu",{attrs:{"offset-y":!0,transition:"scroll-y-transition",bottom:"",left:""},scopedSlots:t._u([{key:"activator",fn:function(e){var n=e.on;return[r("v-btn",t._g({attrs:{icon:""}},n),[r("v-icon",{attrs:{small:t.small}},[t._v("mdi-dots-vertical")])],1)]}}])},[t._v(" "),r("v-list",[r("v-list-item",{attrs:{to:{name:"history.games.show",params:{id:t.id}},exact:""}},[r("v-list-item-icon",[r("v-icon",{attrs:{small:t.small}},[t._v("mdi-eye")])],1),t._v(" "),r("v-list-item-content",[r("v-list-item-title",[t._v(t._s(t.$t("View")))])],1)],1),t._v(" "),r("v-list-item",{attrs:{to:{name:"history.games.verify",params:{id:t.id}},exact:""}},[r("v-list-item-icon",[r("v-icon",{attrs:{small:t.small}},[t._v("mdi-check-decagram")])],1),t._v(" "),r("v-list-item-content",[r("v-list-item-title",[t._v(t._s(t.$t("Verify")))])],1)],1)],1)],1)},a=[]},80515:(t,e,r)=>{"use strict";r.r(e),r.d(e,{default:()=>w});var n=r(79705);const a={components:{GameMenu:r(35275).Z,GameDetail:n.Z},middleware:["auth","verified","2fa_passed"],props:{id:{type:[Number,String],required:!0}},metaInfo:function(){return{title:this.$t("Game {0}",[this.id])}}};var i=r(51900),s=r(43453),o=r.n(s),c=r(4330),l=r(43776),v=r(5255),u=r(17024),m=r(66530),d=r(6571),f=r(57894),p=r(22515),_=r(40961),h=r(73845),g=(0,i.Z)(a,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("v-container",[r("v-row",{attrs:{align:"center",justify:"center"}},[r("v-col",{attrs:{cols:"12",lg:"8"}},[r("v-card",[r("v-toolbar",[r("v-btn",{attrs:{icon:""},on:{click:function(e){return t.$router.go(-1)}}},[r("v-icon",[t._v("mdi-arrow-left")])],1),t._v(" "),r("v-toolbar-title",[t._v("\n            "+t._s(t.$t("Game {0}",[t.id]))+"\n          ")]),t._v(" "),r("v-spacer"),t._v(" "),r("game-menu",{attrs:{id:t.id}})],1),t._v(" "),r("v-card-text",[r("game-detail",{attrs:{id:t.id}})],1)],1)],1)],1)],1)}),[],!1,null,null,null);const w=g.exports;o()(g,{VBtn:c.Z,VCard:l.Z,VCardText:v.ZB,VCol:u.Z,VContainer:m.Z,VIcon:d.Z,VRow:f.Z,VSpacer:p.Z,VToolbar:_.Z,VToolbarTitle:h.qW})},99593:(t,e,r)=>{var n={"./baccarat/resources/js/pages/show":[22041,6065,160,576]};function a(t){if(!r.o(n,t))return Promise.resolve().then((()=>{var e=new Error("Cannot find module '"+t+"'");throw e.code="MODULE_NOT_FOUND",e}));var e=n[t],a=e[0];return Promise.all(e.slice(1).map(r.e)).then((()=>r(a)))}a.keys=()=>Object.keys(n),a.id=99593,t.exports=a}}]);
//# sourceMappingURL=history-games-show.js.map