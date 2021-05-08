(self.webpackChunk=self.webpackChunk||[]).push([[8753],{1692:(e,t,r)=>{"use strict";r.d(t,{Z:()=>a});const a={props:["id","small"]}},26964:(e,t,r)=>{"use strict";r.d(t,{Z:()=>n});var a=r(15566);const n={name:"UserLink",components:{UserAvatar:r(60083).Z},props:["user"],computed:{providers:function(){return(0,a.v)("oauth")}}}},67834:(e,t,r)=>{"use strict";r.d(t,{Z:()=>h});var a=r(87757),n=r.n(a),s=r(9669),i=r.n(s),o=r(20629),l=r(68843),u=r(74155),c=r(19043);function d(e,t,r,a,n,s,i){try{var o=e[s](i),l=o.value}catch(e){return void r(e)}o.done?t(l):Promise.resolve(l).then(a,n)}function v(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var a=Object.getOwnPropertySymbols(e);t&&(a=a.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,a)}return r}function p(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{};t%2?v(Object(r),!0).forEach((function(t){f(e,t,r[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):v(Object(r)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))}))}return e}function f(e,t,r){return t in e?Object.defineProperty(e,t,{value:r,enumerable:!0,configurable:!0,writable:!0}):e[t]=r,e}const h={name:"DataTable",components:{SearchMenu:r(52515).Z,FilterMenu:c.Z},props:{title:{type:String,required:!0},api:{type:String,required:!0},filters:{type:Array,required:!1,default:function(){return[]}},headers:{type:Array,required:!0},sortBy:{type:String,required:!1,default:"id"},sortDesc:{type:Boolean,required:!1,default:!0},search:{type:Boolean,required:!1,default:!1},searchPlaceholder:{type:String,required:!1,default:null},hideFooter:{type:Boolean,required:!1,default:!1}},data:function(){return{loading:!0,options:{},items:[],itemsTotal:0}},computed:p(p({},(0,o.Se)({cacheGet:"cache/get"})),{},{cacheKey:function(){return"datatable."+this.$route.name},footerProps:function(){return{"items-per-page-options":[5,10,25,50],"items-per-page-text":this.$t("Rows per page")}},sortDirection:function(){return this.options.sortDesc?"desc":"asc"},slotHeaderMap:function(){return this.headers.reduce((function(e,t){return e["item."+t.value]=t,e}),{})}}),created:function(){this.options=this.cacheGet(this.cacheKey)||{page:1,itemsPerPage:10,sortBy:this.sortBy,sortDesc:this.sortDesc,search:null,filters:{}}},methods:p(p({},(0,o.nv)({cachePut:"cache/put"})),{},{get:function(){return l.U2.apply(void 0,arguments)},format:function(e,t){return"function"==typeof u[e]?u[e](t):t},updateDataTableOptions:function(e){var t=e.page,r=e.itemsPerPage,a=e.sortBy,n=e.sortDesc;this.options=p(p({},this.options),{},{page:t,itemsPerPage:r,sortBy:a[0],sortDesc:n[0]}),this.cacheOptions(),this.fetchData()},cacheOptions:function(){var e=p(p({},this.options),{},{filters:{},search:null});this.cachePut({key:this.cacheKey,value:e})},filterData:function(e){this.options.filters=e,this.fetchData()},searchData:function(e){this.options.search=e,this.fetchData()},fetchData:function(){var e,t=this;return(e=n().mark((function e(){var r,a;return n().wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return t.loading=!0,e.next=3,i().get(t.api,{params:p({page:t.options.page,items_per_page:t.options.itemsPerPage,sort_by:t.options.sortBy,sort_direction:t.sortDirection,search:t.options.search},t.options.filters)});case 3:r=e.sent,a=r.data,t.items=a.items,t.itemsTotal=a.count,t.loading=!1;case 8:case"end":return e.stop()}}),e)})),function(){var t=this,r=arguments;return new Promise((function(a,n){var s=e.apply(t,r);function i(e){d(s,a,n,i,o,"next",e)}function o(e){d(s,a,n,i,o,"throw",e)}i(void 0)}))})()}})}},13860:(e,t,r)=>{"use strict";r.d(t,{Z:()=>a});const a={props:{disabled:{type:Boolean,required:!1,default:!1}},data:function(){return{value:null,options:[{text:this.$t("All"),value:null},{text:this.$t("Pending"),value:"pending"},{text:this.$t("Approved"),value:"approved"},{text:this.$t("Rejected"),value:"rejected"}]}},methods:{change:function(){this.$emit("change",{status:this.value})}}}},41140:(e,t,r)=>{"use strict";r.d(t,{Z:()=>a});const a={props:{disabled:{type:Boolean,required:!1,default:!1}},data:function(){return{value:null,options:[{text:this.$t("All"),value:null},{text:this.$t("Sign up"),value:"sign_up"},{text:this.$t("Game loss"),value:"game_loss"},{text:this.$t("Game win"),value:"game_win"},{text:this.$t("Deposit"),value:"deposit"}]}},methods:{change:function(){this.$emit("change",{type:this.value})}}}},23817:(e,t,r)=>{"use strict";r.d(t,{Z:()=>a});const a={props:{disabled:{type:Boolean,required:!1,default:!1}},data:function(){return{value:"month",options:[{text:this.$t("Week"),value:"week"},{text:this.$t("Month"),value:"month"},{text:this.$t("Year"),value:"year"}]}},methods:{change:function(){this.$emit("change",{period:this.value})}}}},92270:(e,t,r)=>{"use strict";r.d(t,{Z:()=>a});const a={props:{disabled:{type:Boolean,required:!1,default:!1}},data:function(){return{value:null,options:[{text:this.$t("All"),value:null},{text:this.$t("Created"),value:"created"},{text:this.$t("Pending"),value:"pending"},{text:this.$t("Completed"),value:"completed"},{text:this.$t("Cancelled"),value:"cancelled"}]}},methods:{change:function(){this.$emit("change",{status:this.value})}}}},53182:(e,t,r)=>{"use strict";r.d(t,{Z:()=>h});var a=r(43895),n=r(38411),s=r(1435),i=r(62703),o=r(48225),l=r(99795),u=r(8490),c=r(39713),d=r(84954);function v(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var a=Object.getOwnPropertySymbols(e);t&&(a=a.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,a)}return r}function p(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{};t%2?v(Object(r),!0).forEach((function(t){f(e,t,r[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):v(Object(r)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))}))}return e}function f(e,t,r){return t in e?Object.defineProperty(e,t,{value:r,enumerable:!0,configurable:!0,writable:!0}):e[t]=r,e}const h={components:{PeriodFilter:a.Z,ComparisonPeriodFilter:n.Z,UserRoleFilter:s.Z,UserStatusFilter:i.Z,GameFilter:o.Z,GameResultFilter:l.Z,DepositWithdrawalStatusFilter:u.Z,AffiliateCommissionTypeFilter:c.Z,AffiliateCommissionStatusFilter:d.Z},props:{filters:{type:Array,required:!0},disabled:{type:Boolean,required:!1,default:!1}},data:function(){return{menu:!1,values:{},key:1}},methods:{change:function(e){this.values=p(p({},this.values),e)},reset:function(){this.key++,this.values={},this.apply()},apply:function(){this.menu=!1,this.$emit("apply",this.values)}}}},61862:(e,t,r)=>{"use strict";r.d(t,{Z:()=>u});var a=r(20629);function n(e){return function(e){if(Array.isArray(e))return s(e)}(e)||function(e){if("undefined"!=typeof Symbol&&Symbol.iterator in Object(e))return Array.from(e)}(e)||function(e,t){if(!e)return;if("string"==typeof e)return s(e,t);var r=Object.prototype.toString.call(e).slice(8,-1);"Object"===r&&e.constructor&&(r=e.constructor.name);if("Map"===r||"Set"===r)return Array.from(e);if("Arguments"===r||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r))return s(e,t)}(e)||function(){throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}function s(e,t){(null==t||t>e.length)&&(t=e.length);for(var r=0,a=new Array(t);r<t;r++)a[r]=e[r];return a}function i(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var a=Object.getOwnPropertySymbols(e);t&&(a=a.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,a)}return r}function o(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{};t%2?i(Object(r),!0).forEach((function(t){l(e,t,r[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):i(Object(r)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))}))}return e}function l(e,t,r){return t in e?Object.defineProperty(e,t,{value:r,enumerable:!0,configurable:!0,writable:!0}):e[t]=r,e}const u={props:{disabled:{type:Boolean,required:!1,default:!1}},data:function(){return{value:null}},computed:o(o({},(0,a.Se)({games:"package-manager/games"})),{},{options:function(){var e=this;return[{text:this.$t("All"),value:null}].concat(n(Object.keys(this.games).map((function(t){return{text:e.games[t].name,value:t}}))))}}),methods:{change:function(){this.$emit("change",{game:this.value})}}}},33205:(e,t,r)=>{"use strict";r.d(t,{Z:()=>a});const a={props:{disabled:{type:Boolean,required:!1,default:!1}},data:function(){return{value:null,options:[{text:this.$t("All"),value:null},{text:this.$t("Profitable"),value:"profitable"},{text:this.$t("Unprofitable"),value:"unprofitable"}]}},methods:{change:function(){this.$emit("change",{result:this.value})}}}},29839:(e,t,r)=>{"use strict";r.d(t,{Z:()=>a});const a={props:{disabled:{type:Boolean,required:!1,default:!1}},data:function(){return{value:null,options:[{text:this.$t("All time"),value:null},{text:this.$t("This week"),value:"week"},{text:this.$t("Previous week"),value:"prev_week"},{text:this.$t("This month"),value:"month"},{text:this.$t("Previous month"),value:"prev_month"},{text:this.$t("This year"),value:"year"},{text:this.$t("Previous year"),value:"prev_year"}]}},methods:{change:function(){this.$emit("change",{period:this.value})}}}},48044:(e,t,r)=>{"use strict";r.d(t,{Z:()=>a});const a={props:{disabled:{type:Boolean,required:!1,default:!1}},data:function(){return{value:["user","bot","admin"],options:[{text:this.$t("User"),value:"user"},{text:this.$t("Bot"),value:"bot"},{text:this.$t("Admin"),value:"admin"}]}},methods:{change:function(){this.$emit("change",{roles:this.value})}}}},58593:(e,t,r)=>{"use strict";r.d(t,{Z:()=>a});const a={props:{disabled:{type:Boolean,required:!1,default:!1}},data:function(){return{value:null,options:[{text:this.$t("All"),value:null},{text:this.$t("Active"),value:"active"},{text:this.$t("Blocked"),value:"blocked"}]}},methods:{change:function(){this.$emit("change",{status:this.value})}}}},95392:(e,t,r)=>{"use strict";r.d(t,{Z:()=>a});const a={props:{placeholder:{type:String,required:!1,default:null},disabled:{type:Boolean,required:!1,default:!1}},data:function(){return{menu:!1,value:null}},methods:{reset:function(){this.value=null,this.search()},search:function(){this.menu=!1,this.$emit("search",this.value)}}}},69516:(e,t,r)=>{"use strict";r.d(t,{s:()=>a,x:()=>n});var a=function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("v-menu",{attrs:{"offset-y":!0,transition:"scroll-y-transition",bottom:"",left:""},scopedSlots:e._u([{key:"activator",fn:function(t){var a=t.on;return[r("v-btn",e._g({attrs:{icon:""}},a),[r("v-icon",{attrs:{small:e.small}},[e._v("mdi-dots-vertical")])],1)]}}])},[e._v(" "),r("v-list",[r("v-list-item",{attrs:{to:{name:"admin.games.show",params:{id:e.id}},exact:""}},[r("v-list-item-icon",[r("v-icon",{attrs:{small:e.small}},[e._v("mdi-eye")])],1),e._v(" "),r("v-list-item-content",[r("v-list-item-title",[e._v(e._s(e.$t("View")))])],1)],1)],1)],1)},n=[]},68008:(e,t,r)=>{"use strict";r.d(t,{s:()=>a,x:()=>n});var a=function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("span",[r("user-avatar",{attrs:{user:e.user}}),e._v(" "),r("router-link",{staticClass:"mr-1",attrs:{to:{name:"admin.users.show",params:{id:e.user.id}}}},[e._v(e._s(e.user.name))]),e._v(" "),e.user.is_active?e._e():r("v-chip",{attrs:{color:"error",small:"",label:""}},[e._v(e._s(e.$t("Blocked")))]),e._v(" "),e.user.is_admin?r("v-chip",{attrs:{color:"primary",small:"",outlined:"",label:""}},[e._v(e._s(e.$t("Admin")))]):e.user.is_bot?r("v-chip",{attrs:{color:"warning",small:"",outlined:"",label:""}},[e._v(e._s(e.$t("Bot")))]):e._e(),e._v(" "),e._l(e.user.profiles,(function(t){return r("v-tooltip",{key:t.id,attrs:{bottom:""},scopedSlots:e._u([{key:"activator",fn:function(a){var n=a.on;return[r("v-icon",e._g({attrs:{small:""}},n),[e._v("mdi-"+e._s(e.providers[t.provider_name].mdi||t.provider_name))])]}}],null,!0)},[e._v(" "),r("span",[e._v(e._s(t.provider_name)+" "+e._s(e.$t("profile ID"))+" "+e._s(t.provider_user_id))])])})),e._v(" "),e.user.referrer?r("v-tooltip",{attrs:{bottom:""},scopedSlots:e._u([{key:"activator",fn:function(t){var a=t.on;return[r("v-icon",e._g({attrs:{small:""}},a),[e._v("mdi-account-arrow-left")])]}}],null,!1,2018221753)},[e._v(" "),r("span",[e._v(e._s(e.$t("Referred by {0}",[e.user.referrer.name])))])]):e._e(),e._v(" "),e.user.two_factor_auth_enabled?r("v-tooltip",{attrs:{bottom:""},scopedSlots:e._u([{key:"activator",fn:function(t){var a=t.on;return[r("v-icon",e._g({attrs:{small:""}},a),[e._v("mdi-two-factor-authentication")])]}}],null,!1,2737254619)},[e._v(" "),r("span",[e._v(e._s(e.$t("Two-factor authentication enabled")))])]):e._e()],2)},n=[]},8230:(e,t,r)=>{"use strict";r.d(t,{s:()=>a,x:()=>n});var a=function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("v-card",[r("v-toolbar",[e._t("toolbar-prepend"),e._v(" "),r("v-toolbar-title",[e._v("\n      "+e._s(e.title)+"\n    ")]),e._v(" "),r("v-spacer"),e._v(" "),r("v-btn",{attrs:{icon:"",loading:e.loading,disabled:e.loading},on:{click:e.fetchData}},[r("v-icon",[e._v("mdi-cached")])],1),e._v(" "),e.filters.length?r("filter-menu",{attrs:{filters:e.filters,disabled:e.loading},on:{apply:function(t){return e.filterData(t)}}}):e._e(),e._v(" "),e.search?r("search-menu",{attrs:{placeholder:e.searchPlaceholder,disabled:e.loading},on:{search:function(t){return e.searchData(t)}}}):e._e(),e._v(" "),e._t("toolbar-append")],2),e._v(" "),r("v-card-text",[e._t("table-prepend"),e._v(" "),r("v-data-table",{staticClass:"elevation-1",attrs:{headers:e.headers,items:e.items,page:e.options.page,"sort-by":e.options.sortBy,"sort-desc":e.options.sortDesc,"items-per-page":e.options.itemsPerPage,"footer-props":e.footerProps,"server-items-length":e.itemsTotal,loading:e.loading,"must-sort":!0,"hide-default-footer":e.hideFooter,"no-data-text":e.$t("No data found"),"no-results-text":e.$t("No data found")},on:{"update:options":e.updateDataTableOptions},scopedSlots:e._u([{key:"loading",fn:function(){return e._l(Array(e.options.itemsPerPage).fill(0),(function(e,t){return r("div",{key:t,staticClass:"align-center my-7"},[r("v-skeleton-loader",{attrs:{type:"text"}})],1)}))},proxy:!0},e._l(e.slotHeaderMap,(function(t,r){return{key:r,fn:function(a){var n=a.item;return[e.$scopedSlots[r]?e._t(r,null,{item:n}):[e._v("\n          "+e._s(t.format?e.format(t.format,e.get(n,t.value)):e.get(n,t.value))+"\n        ")]]}}}))],null,!0)}),e._v(" "),e._t("table-append")],2)],1)},n=[]},33132:(e,t,r)=>{"use strict";r.d(t,{s:()=>a,x:()=>n});var a=function(){var e=this,t=e.$createElement;return(e._self._c||t)("v-select",{attrs:{items:e.options,label:e.$t("Status"),disabled:e.disabled,outlined:""},on:{change:e.change},model:{value:e.value,callback:function(t){e.value=t},expression:"value"}})},n=[]},29375:(e,t,r)=>{"use strict";r.d(t,{s:()=>a,x:()=>n});var a=function(){var e=this,t=e.$createElement;return(e._self._c||t)("v-select",{attrs:{items:e.options,label:e.$t("Type"),disabled:e.disabled,outlined:""},on:{change:e.change},model:{value:e.value,callback:function(t){e.value=t},expression:"value"}})},n=[]},89355:(e,t,r)=>{"use strict";r.d(t,{s:()=>a,x:()=>n});var a=function(){var e=this,t=e.$createElement;return(e._self._c||t)("v-select",{attrs:{items:e.options,label:e.$t("Period"),disabled:e.disabled,outlined:""},on:{change:e.change},model:{value:e.value,callback:function(t){e.value=t},expression:"value"}})},n=[]},24504:(e,t,r)=>{"use strict";r.d(t,{s:()=>a,x:()=>n});var a=function(){var e=this,t=e.$createElement;return(e._self._c||t)("v-select",{attrs:{items:e.options,label:e.$t("Status"),disabled:e.disabled,outlined:""},on:{change:e.change},model:{value:e.value,callback:function(t){e.value=t},expression:"value"}})},n=[]},80930:(e,t,r)=>{"use strict";r.d(t,{s:()=>a,x:()=>n});var a=function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("v-menu",{attrs:{"close-on-content-click":!1,left:"","offset-x":"","nudge-width":300},scopedSlots:e._u([{key:"activator",fn:function(t){var a=t.on;return[r("v-btn",e._g({attrs:{icon:"",disabled:e.disabled}},a),[r("v-icon",{attrs:{color:Object.keys(e.values).length?"primary":void 0}},[e._v("\n        mdi-filter-outline\n      ")])],1)]}}]),model:{value:e.menu,callback:function(t){e.menu=t},expression:"menu"}},[e._v(" "),r("v-card",{attrs:{outlined:""}},[r("v-card-title",[e._v("\n      "+e._s(e.$t("Filter"))+"\n    ")]),e._v(" "),r("v-divider"),e._v(" "),r("v-card-text",{staticClass:"pt-5"},e._l(e.filters,(function(t){return r(t+"-filter",{key:t+"-"+e.key,tag:"component",on:{change:function(t){return e.change(t)}}})})),1),e._v(" "),r("v-card-actions",[r("v-spacer"),e._v(" "),r("v-btn",{attrs:{color:"primary",text:"",disabled:e.disabled},on:{click:e.reset}},[e._v("\n        "+e._s(e.$t("Reset"))+"\n      ")]),e._v(" "),r("v-btn",{attrs:{color:"primary",text:"",disabled:e.disabled},on:{click:e.apply}},[e._v("\n        "+e._s(e.$t("Apply"))+"\n      ")])],1)],1)],1)},n=[]},78976:(e,t,r)=>{"use strict";r.d(t,{s:()=>a,x:()=>n});var a=function(){var e=this,t=e.$createElement;return(e._self._c||t)("v-select",{attrs:{items:e.options,label:e.$t("Game"),disabled:e.disabled,outlined:""},on:{change:e.change},model:{value:e.value,callback:function(t){e.value=t},expression:"value"}})},n=[]},89439:(e,t,r)=>{"use strict";r.d(t,{s:()=>a,x:()=>n});var a=function(){var e=this,t=e.$createElement;return(e._self._c||t)("v-select",{attrs:{items:e.options,label:e.$t("Result"),disabled:e.disabled,outlined:""},on:{change:e.change},model:{value:e.value,callback:function(t){e.value=t},expression:"value"}})},n=[]},60045:(e,t,r)=>{"use strict";r.d(t,{s:()=>a,x:()=>n});var a=function(){var e=this,t=e.$createElement;return(e._self._c||t)("v-select",{attrs:{items:e.options,label:e.$t("Period"),disabled:e.disabled,outlined:""},on:{change:e.change},model:{value:e.value,callback:function(t){e.value=t},expression:"value"}})},n=[]},67763:(e,t,r)=>{"use strict";r.d(t,{s:()=>a,x:()=>n});var a=function(){var e=this,t=e.$createElement;return(e._self._c||t)("v-select",{attrs:{items:e.options,label:e.$t("Role"),disabled:e.disabled,outlined:"",multiple:""},on:{change:e.change},model:{value:e.value,callback:function(t){e.value=t},expression:"value"}})},n=[]},60878:(e,t,r)=>{"use strict";r.d(t,{s:()=>a,x:()=>n});var a=function(){var e=this,t=e.$createElement;return(e._self._c||t)("v-select",{attrs:{items:e.options,label:e.$t("Status"),disabled:e.disabled,outlined:""},on:{change:e.change},model:{value:e.value,callback:function(t){e.value=t},expression:"value"}})},n=[]},25179:(e,t,r)=>{"use strict";r.d(t,{s:()=>a,x:()=>n});var a=function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("v-menu",{attrs:{"close-on-content-click":!1,left:"","offset-x":"","nudge-width":300},scopedSlots:e._u([{key:"activator",fn:function(t){var a=t.on;return[r("v-btn",e._g({attrs:{icon:"",disabled:e.disabled}},a),[r("v-icon",{attrs:{color:e.value?"primary":void 0}},[e._v("\n        mdi-magnify\n      ")])],1)]}}]),model:{value:e.menu,callback:function(t){e.menu=t},expression:"menu"}},[e._v(" "),r("v-card",{attrs:{outlined:""}},[r("v-card-title",[e._v("\n      "+e._s(e.$t("Search"))+"\n    ")]),e._v(" "),r("v-divider"),e._v(" "),r("v-card-text",{staticClass:"pt-5"},[r("v-form",{on:{submit:function(t){return t.preventDefault(),e.search(t)}}},[r("v-text-field",{attrs:{label:e.$t("Search"),placeholder:e.placeholder||e.$t("Search..."),outlined:"",autofocus:""},model:{value:e.value,callback:function(t){e.value=t},expression:"value"}})],1)],1),e._v(" "),r("v-card-actions",[r("v-spacer"),e._v(" "),r("v-btn",{attrs:{color:"primary",text:"",disabled:e.disabled},on:{click:e.reset}},[e._v("\n        "+e._s(e.$t("Reset"))+"\n      ")]),e._v(" "),r("v-btn",{attrs:{color:"primary",text:"",disabled:e.disabled},on:{click:e.search}},[e._v("\n        "+e._s(e.$t("Search"))+"\n      ")])],1)],1)],1)},n=[]},95236:(e,t,r)=>{"use strict";r.r(t),r.d(t,{default:()=>v});var a=r(74987),n=r(92287),s=r(73946);const i={components:{DataTable:a.Z,UserLink:n.Z,GameMenu:s.Z},middleware:["auth","verified","2fa_passed","admin"],metaInfo:function(){return{title:this.$t("Games")}},computed:{headers:function(){return[{text:this.$t("ID"),value:"id"},{text:this.$t("Name"),value:"name",sortable:!1},{text:this.$t("Game"),value:"title"},{text:this.$t("Bet"),value:"bet",align:"right",format:"decimal"},{text:this.$t("Win"),value:"win",align:"right",format:"decimal"},{text:this.$t("Profit"),value:"profit",align:"right",format:"decimal"},{text:this.$t("Played"),value:"created_ago",align:"right"},{value:"actions",sortable:!1,align:"right"}]}}};var o=r(51900),l=r(43453),u=r.n(l),c=r(66530),d=(0,o.Z)(i,(function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("v-container",[r("data-table",{attrs:{api:"/api/admin/games",title:e.$t("Games"),headers:e.headers,filters:["period","game","user-role","game-result"],search:!0,"search-placeholder":e.$t("Game ID, user name or email")},scopedSlots:e._u([{key:"item.name",fn:function(e){var t=e.item;return[r("user-link",{attrs:{user:t.account.user}})]}},{key:"item.actions",fn:function(e){var t=e.item;return[r("game-menu",{attrs:{id:t.id,small:""}})]}}])})],1)}),[],!1,null,null,null);const v=d.exports;u()(d,{VContainer:c.Z})}}]);
//# sourceMappingURL=admin-games.js.map