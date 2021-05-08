(self.webpackChunk=self.webpackChunk||[]).push([[9294],{83016:(t,e,r)=>{"use strict";r.d(e,{Z:()=>o});const o={props:["id","small"]}},79767:(t,e,r)=>{"use strict";r.d(e,{s:()=>o,x:()=>n});var o=function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("v-menu",{attrs:{"offset-y":!0,transition:"scroll-y-transition",bottom:"",left:""},scopedSlots:t._u([{key:"activator",fn:function(e){var o=e.on;return[r("v-btn",t._g({attrs:{icon:""}},o),[r("v-icon",{attrs:{small:t.small}},[t._v("mdi-dots-vertical")])],1)]}}])},[t._v(" "),r("v-list",[r("v-list-item",{attrs:{to:{name:"admin.chat.rooms.edit",params:{id:t.id}},exact:""}},[r("v-list-item-icon",[r("v-icon",{attrs:{small:t.small}},[t._v("mdi-pencil")])],1),t._v(" "),r("v-list-item-content",[r("v-list-item-title",[t._v(t._s(t.$t("Edit")))])],1)],1),t._v(" "),r("v-list-item",{attrs:{to:{name:"admin.chat.rooms.delete",params:{id:t.id}},exact:""}},[r("v-list-item-icon",[r("v-icon",{attrs:{small:t.small}},[t._v("mdi-delete")])],1),t._v(" "),r("v-list-item-content",[r("v-list-item-title",[t._v(t._s(t.$t("Delete")))])],1)],1)],1)],1)},n=[]},22240:(t,e,r)=>{"use strict";r.r(e),r.d(e,{default:()=>F});var o=r(87757),n=r.n(o),a=r(9669),s=r.n(a),i=r(50175),l=r.n(i),c=r(93503),m=r(51905);function u(t,e,r,o,n,a,s){try{var i=t[a](s),l=i.value}catch(t){return void r(t)}i.done?e(l):Promise.resolve(l).then(o,n)}function d(t){return function(){var e=this,r=arguments;return new Promise((function(o,n){var a=t.apply(e,r);function s(t){u(a,o,n,s,i,"next",t)}function i(t){u(a,o,n,s,i,"throw",t)}s(void 0)}))}}const v={middleware:["auth","verified","2fa_passed","admin"],components:{ChatRoomMenu:r(33434).Z,Preloader:m.Z},mixins:[c.Z],props:["id"],metaInfo:function(){return{title:this.$t("Chat room {0}",[this.id])}},data:function(){return{room:null,form:new(l())({name:null,enabled:null})}},computed:{changed:function(){var t=this;return this.form.keys().some((function(e){return t.room&&t.form[e]!==t.room[e]}))}},created:function(){var t=this;return d(n().mark((function e(){var r,o;return n().wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,s().get("/api/admin/chat/rooms/".concat(t.id));case 2:r=e.sent,o=r.data,t.room=o,t.form.keys().forEach((function(e){t.form[e]=t.room[e]}));case 6:case"end":return e.stop()}}),e)})))()},methods:{update:function(){var t=this;return d(n().mark((function e(){return n().wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,t.form.patch("/api/admin/chat/rooms/".concat(t.id));case 2:t.$store.dispatch("message/success",{text:t.$t("Chat room successfully updated.")}),t.$router.push({name:"admin.chat.rooms.index"});case 4:case"end":return e.stop()}}),e)})))()}}};var f=r(51900),p=r(43453),h=r.n(p),b=r(4330),_=r(43776),x=r(5255),y=r(17024),V=r(66530),Z=r(83240),k=r(6571),w=r(57894),$=r(55515),g=r(22515),C=r(13234),E=r(54933),I=r(40961),S=r(73845),T=(0,f.Z)(v,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("v-container",[r("v-row",{attrs:{align:"center",justify:"center"}},[r("v-col",{attrs:{cols:"12",md:"6"}},[r("v-card",[r("v-toolbar",[r("v-btn",{attrs:{icon:""},on:{click:function(e){return t.$router.go(-1)}}},[r("v-icon",[t._v("mdi-arrow-left")])],1),t._v(" "),r("v-toolbar-title",[t._v("\n            "+t._s(t.$t("Chat room {0}",[t.id]))+"\n          ")]),t._v(" "),r("v-spacer"),t._v(" "),r("chat-room-menu",{attrs:{id:t.id}}),t._v(" "),r("preloader",{attrs:{active:!t.room}})],1),t._v(" "),r("v-card-text",[r("v-form",{on:{submit:function(e){return e.preventDefault(),t.update(e)}},model:{value:t.formIsValid,callback:function(e){t.formIsValid=e},expression:"formIsValid"}},[r("v-text-field",{attrs:{label:t.$t("Name"),type:"text",disabled:t.form.busy||!t.room,rules:[t.validationRequired],error:t.form.errors.has("name"),"error-messages":t.form.errors.get("name"),outlined:""},on:{keydown:function(e){return t.clearFormErrors(e,"name")}},model:{value:t.form.name,callback:function(e){t.$set(t.form,"name",e)},expression:"form.name"}}),t._v(" "),r("v-switch",{attrs:{label:t.$t("Enabled"),color:"primary",disabled:t.form.busy||!t.room,"false-value":0,"true-value":1},model:{value:t.form.enabled,callback:function(e){t.$set(t.form,"enabled",e)},expression:"form.enabled"}}),t._v(" "),r("v-skeleton-loader",{attrs:{type:"button",loading:!t.room}},[r("v-btn",{attrs:{type:"submit",color:"primary",disabled:!t.formIsValid||!t.changed||t.form.busy,loading:t.form.busy}},[t._v("\n                "+t._s(t.$t("Save"))+"\n              ")])],1)],1)],1)],1)],1)],1)],1)}),[],!1,null,null,null);const F=T.exports;h()(T,{VBtn:b.Z,VCard:_.Z,VCardText:x.ZB,VCol:y.Z,VContainer:V.Z,VForm:Z.Z,VIcon:k.Z,VRow:w.Z,VSkeletonLoader:$.Z,VSpacer:g.Z,VSwitch:C.Z,VTextField:E.Z,VToolbar:I.Z,VToolbarTitle:S.qW})}}]);
//# sourceMappingURL=admin-chat-rooms-edit.js.map