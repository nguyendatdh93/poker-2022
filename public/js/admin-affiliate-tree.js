(self.webpackChunk=self.webpackChunk||[]).push([[5631],{55232:(t,e,n)=>{"use strict";n.d(e,{Z:()=>r});const r={props:["id","small"]}},70728:(t,e,n)=>{"use strict";n.d(e,{s:()=>r,x:()=>i});var r=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("v-menu",{attrs:{"offset-y":!0,transition:"scroll-y-transition",bottom:"",left:""},scopedSlots:t._u([{key:"activator",fn:function(e){var r=e.on;return[n("v-btn",t._g({attrs:{icon:""}},r),[n("v-icon",{attrs:{small:t.small}},[t._v("mdi-dots-vertical")])],1)]}}])},[t._v(" "),n("v-list",[n("v-list-item",{attrs:{to:{name:"admin.users.show",params:{id:t.id}},exact:""}},[n("v-list-item-icon",[n("v-icon",{attrs:{small:t.small}},[t._v("mdi-eye")])],1),t._v(" "),n("v-list-item-content",[n("v-list-item-title",[t._v(t._s(t.$t("View")))])],1)],1)],1)],1)},i=[]},22504:(t,e,n)=>{"use strict";n.r(e),n.d(e,{default:()=>Z});var r=n(87757),i=n.n(r),a=n(9669),s=n.n(a);function l(t,e,n,r,i,a,s){try{var l=t[a](s),o=l.value}catch(t){return void n(t)}l.done?e(o):Promise.resolve(o).then(r,i)}const o={components:{AffiliateTreeMenu:n(57689).Z},middleware:["auth","verified","2fa_passed","admin"],metaInfo:function(){return{title:this.$t("Affiliates tree")}},data:function(){return{tree:null}},created:function(){var t,e=this;return(t=i().mark((function t(){var n,r;return i().wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return t.next=2,s().get("/api/admin/affiliate/tree");case 2:n=t.sent,r=n.data,e.tree=r;case 5:case"end":return t.stop()}}),t)})),function(){var e=this,n=arguments;return new Promise((function(r,i){var a=t.apply(e,n);function s(t){l(a,r,i,s,o,"next",t)}function o(t){l(a,r,i,s,o,"throw",t)}s(void 0)}))})()}};var c=n(51900),u=n(43453),v=n.n(u),f=n(43776),d=n(5255),m=n(17024),_=n(66530),p=n(57894),h=n(82048),w=(0,c.Z)(o,(function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("v-container",[n("v-row",[n("v-col",{attrs:{cols:"12"}},[n("v-card",[n("v-card-title",[t._v("\n          "+t._s(t.$t("Affiliates tree"))+"\n        ")]),t._v(" "),n("v-card-text",[t.tree?n("v-treeview",{attrs:{items:t.tree},scopedSlots:t._u([{key:"label",fn:function(e){var n=e.item;return[t._v("\n              "+t._s(n.name)+t._s(n.children&&n.children.length?" ("+n.children.length+")":"")+"\n            ")]}},{key:"append",fn:function(t){var e=t.item;return[n("affiliate-tree-menu",{attrs:{id:e.id,small:""}})]}}],null,!1,2850265691)}):[n("p",[t._v("\n              "+t._s(t.$t("Loading..."))+"\n            ")])]],2)],1)],1)],1)],1)}),[],!1,null,null,null);const Z=w.exports;v()(w,{VCard:f.Z,VCardText:d.ZB,VCardTitle:d.EB,VCol:m.Z,VContainer:_.Z,VRow:p.Z,VTreeview:h.Z})}}]);
//# sourceMappingURL=admin-affiliate-tree.js.map