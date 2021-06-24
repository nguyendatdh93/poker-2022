(self.webpackChunk=self.webpackChunk||[]).push([[8949],{41092:(t,e,a)=>{"use strict";a.d(e,{Z:()=>i});const i={props:["id","small"]}},69567:(t,e,a)=>{"use strict";a.d(e,{Z:()=>u});var i=a(87757),r=a.n(i),s=a(9669),n=a.n(s),l=a(74155),o=a(68843);function v(t,e,a,i,r,s,n){try{var l=t[s](n),o=l.value}catch(t){return void a(t)}l.done?e(o):Promise.resolve(o).then(i,r)}const u={name:"KeyValueTable",props:["name","api","headers"],data:function(){return{data:null}},watch:{api:function(){this.loadData()}},created:function(){this.loadData()},methods:{value:function(t){var e=t.lookup?this.data[t.lookup][this.data[this.name][t.value]]:(0,o.U2)(this.data[this.name],t.value);return"boolean"==typeof e?e?this.$t("Yes"):this.$t("No"):t.format?this.format(t.format,e):e},format:function(t,e){return"function"==typeof l[t]?l[t](e):e},loadData:function(){var t,e=this;return(t=r().mark((function t(){var a,i;return r().wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return e.data=null,t.next=3,n().get(e.api);case 3:a=t.sent,i=a.data,e.data=i,i[e.name]&&e.$emit("load",{data:i[e.name]});case 7:case"end":return t.stop()}}),t)})),function(){var e=this,a=arguments;return new Promise((function(i,r){var s=t.apply(e,a);function n(t){v(s,i,r,n,l,"next",t)}function l(t){v(s,i,r,n,l,"throw",t)}n(void 0)}))})()}}}},96992:(t,e,a)=>{"use strict";a.d(e,{s:()=>i,x:()=>r});var i=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("v-menu",{attrs:{"offset-y":!0,transition:"scroll-y-transition",bottom:"",left:""},scopedSlots:t._u([{key:"activator",fn:function(e){var i=e.on;return[a("v-btn",t._g({attrs:{icon:""}},i),[a("v-icon",{attrs:{small:t.small}},[t._v("mdi-dots-vertical")])],1)]}}])},[t._v(" "),a("v-list",[a("v-list-item",{attrs:{to:{name:"users.show",params:{id:t.id}},exact:""}},[a("v-list-item-icon",[a("v-icon",{attrs:{small:t.small}},[t._v("mdi-account")])],1),t._v(" "),a("v-list-item-content",[a("v-list-item-title",[t._v(t._s(t.$t("Profile")))])],1)],1),t._v(" "),a("v-list-item",{attrs:{to:{name:"admin.users.show",params:{id:t.id}},exact:""}},[a("v-list-item-icon",[a("v-icon",{attrs:{small:t.small}},[t._v("mdi-eye")])],1),t._v(" "),a("v-list-item-content",[a("v-list-item-title",[t._v(t._s(t.$t("View")))])],1)],1),t._v(" "),a("v-list-item",{attrs:{to:{name:"admin.users.edit",params:{id:t.id}},exact:""}},[a("v-list-item-icon",[a("v-icon",{attrs:{small:t.small}},[t._v("mdi-pencil")])],1),t._v(" "),a("v-list-item-content",[a("v-list-item-title",[t._v(t._s(t.$t("Edit")))])],1)],1),t._v(" "),a("v-list-item",{attrs:{to:{name:"admin.users.delete",params:{id:t.id}},exact:""}},[a("v-list-item-icon",[a("v-icon",{attrs:{small:t.small}},[t._v("mdi-delete")])],1),t._v(" "),a("v-list-item-content",[a("v-list-item-title",[t._v(t._s(t.$t("Delete")))])],1)],1),t._v(" "),a("v-list-item",{attrs:{to:{name:"admin.users.mail",params:{id:t.id}},exact:""}},[a("v-list-item-icon",[a("v-icon",{attrs:{small:t.small}},[t._v("mdi-email-outline")])],1),t._v(" "),a("v-list-item-content",[a("v-list-item-title",[t._v(t._s(t.$t("E-mail")))])],1)],1)],1)],1)},r=[]},85579:(t,e,a)=>{"use strict";a.d(e,{s:()=>i,x:()=>r});var i=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("v-simple-table",{scopedSlots:t._u([{key:"default",fn:function(){return[a("tbody",[t._l(t.headers,(function(e){return a("tr",{key:e.value},[a("td",[t._v(t._s(e.text))]),t._v(" "),a("td",{staticClass:"text-right"},[t.data?t._t(e.value,[t._v("\n            "+t._s(t.value(e))+"\n          ")],{item:t.data[t.name]}):a("v-skeleton-loader",{attrs:{type:"text","min-width":"60"}})],2)])})),t._v(" "),t.data?t._t("after",null,{item:t.data}):t._e()],2)]},proxy:!0}],null,!0)})},r=[]},44951:(t,e,a)=>{"use strict";a.r(e),a.d(e,{default:()=>y});var i=a(45335),r=a(26200);const s={components:{UserAvatar:a(60083).Z,KeyValueTable:i.Z,UserMenu:r.Z},middleware:["auth","verified","2fa_passed","admin"],props:["id"],metaInfo:function(){return{title:this.$t("User {0}",[this.id])}},computed:{headers:function(){return[{text:this.$t("ID"),value:"id"},{text:this.$t("Referrer"),value:"referrer"},{text:this.$t("Avatar"),value:"avatar"},{text:this.$t("Name"),value:"name"},{text:this.$t("Email"),value:"email"},{text:this.$t("Role"),value:"role_title"},{text:this.$t("Status"),value:"status_title"},{text:this.$t("Banned from chat"),value:"banned_from_chat"},{text:this.$t("Created at"),value:"created_at"},{text:this.$t("Updated at"),value:"updated_at"},{text:this.$t("Email verified at"),value:"email_verified_at"},{text:this.$t("Last login at"),value:"last_login_at"},{text:this.$t("Last login from"),value:"last_login_from"},{text:this.$t("Last seen at"),value:"last_seen_at"},{text:this.$t("Two-factor authentication"),value:"two_factor_auth_enabled"}]}}};var n=a(51900),l=a(43453),o=a.n(l),v=a(4330),u=a(43776),m=a(5255),c=a(17024),d=a(66530),_=a(6571),f=a(57894),h=a(22515),p=a(40961),x=a(73845),$=(0,n.Z)(s,(function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("v-container",[a("v-row",{attrs:{align:"center",justify:"center"}},[a("v-col",{attrs:{cols:"12",md:"6"}},[a("v-card",[a("v-toolbar",[a("v-btn",{attrs:{icon:""},on:{click:function(e){return t.$router.go(-1)}}},[a("v-icon",[t._v("mdi-arrow-left")])],1),t._v(" "),a("v-toolbar-title",[t._v("\n            "+t._s(t.$t("User {0}",[t.id]))+"\n          ")]),t._v(" "),a("v-spacer"),t._v(" "),a("user-menu",{attrs:{id:t.id}})],1),t._v(" "),a("v-card-text",[a("key-value-table",{attrs:{name:"user",api:"/api/admin/users/"+t.id,headers:t.headers},scopedSlots:t._u([{key:"referrer",fn:function(e){var i=e.item;return[i.referrer?a("router-link",{attrs:{to:{name:"admin.users.show",params:{id:i.referrer.id}}}},[t._v(t._s(i.referrer.name))]):t._e()]}},{key:"avatar",fn:function(t){var e=t.item;return[a("user-avatar",{attrs:{user:e}})]}}])})],1)],1)],1)],1)],1)}),[],!1,null,null,null);const y=$.exports;o()($,{VBtn:v.Z,VCard:u.Z,VCardText:m.ZB,VCol:c.Z,VContainer:d.Z,VIcon:_.Z,VRow:f.Z,VSpacer:h.Z,VToolbar:p.Z,VToolbarTitle:x.qW})}}]);
//# sourceMappingURL=admin-users-show.js.map