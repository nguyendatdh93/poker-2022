if(!self.define){const e=e=>{"require"!==e&&(e+=".js");let s=Promise.resolve();return a[e]||(s=new Promise((async s=>{if("document"in self){const a=document.createElement("script");a.src=e,document.head.appendChild(a),a.onload=s}else importScripts(e),s()}))),s.then((()=>{if(!a[e])throw new Error(`Module ${e} didn’t register its module`);return a[e]}))},s=(s,a)=>{Promise.all(s.map(e)).then((e=>a(1===e.length?e[0]:e)))},a={require:Promise.resolve(s)};self.define=(s,i,r)=>{a[s]||(a[s]=Promise.resolve().then((()=>{let a={};const c={uri:location.origin+s.slice(1)};return Promise.all(i.map((s=>{switch(s){case"exports":return a;case"module":return c;default:return e(s)}}))).then((e=>{const s=r(...e);return a.default||(a.default=s),a}))})))}}define("./service-worker.js",["./workbox-842c0e69"],(function(e){"use strict";e.setCacheNameDetails({prefix:"stake-pwa"}),self.skipWaiting(),e.clientsClaim(),e.precacheAndRoute([{url:"//js/app.js.LICENSE.txt",revision:"72139f74ac7847a3f571b7b9fd112b4c"},{url:"/fonts/vendor/@mdi/materialdesignicons-webfont.eot?53f53f50a1b2033979f58d724a02a7e5",revision:"64d4cf64afd77a4ad2713f648eb920e6"},{url:"/fonts/vendor/@mdi/materialdesignicons-webfont.ttf?0e4e0b3da55fa7faa77b4c2d978008dd",revision:"174c02fc4609e8fc4389f5d21f16a296"},{url:"/fonts/vendor/@mdi/materialdesignicons-webfont.woff2?e9db4005489e24809b62e61177c543a8",revision:"7a44ea195f395e1d086010e44555a5c4"},{url:"/fonts/vendor/@mdi/materialdesignicons-webfont.woff?d8e8e0f7931afa097409dbb0ea7815d8",revision:"147e3378b44bc9570418b1eece10dd7c"},{url:"/js/2fa.js",revision:"cb4eb072548a92a849ed19c05d1871f4"},{url:"/js/2fa.js.map",revision:"f3decd0e565f2ecb1262be57837f256a"},{url:"/js/404.js",revision:"a78ddb95f5120e0c4e84d2b2ea8bf863"},{url:"/js/404.js.map",revision:"c6eb6badb744de404dc555b417a1b5e7"},{url:"/js/503.js",revision:"3d16a843d414a714ba227fa22053b82f"},{url:"/js/503.js.map",revision:"3e447791b4333d9a52f73c122b74e632"},{url:"/js/admin-accounts-credit.js",revision:"09e8f5a6b3c21570c9692485297b7781"},{url:"/js/admin-accounts-credit.js.map",revision:"36846aa4bdef7c738bd3b59f9edf4faa"},{url:"/js/admin-accounts-debit.js",revision:"5c203593cbca396c0626ea54d0c0cd16"},{url:"/js/admin-accounts-debit.js.map",revision:"27e86c76be2c1acf76ab53e134546eec"},{url:"/js/admin-accounts-show.js",revision:"eae828a4073cd600f35fb4021622e55e"},{url:"/js/admin-accounts-show.js.map",revision:"5d16e277229dd59beae452c15f1b3086"},{url:"/js/admin-accounts-transactions.js",revision:"ba5069e1c6ccaeda9a0bc25bebc3115b"},{url:"/js/admin-accounts-transactions.js.map",revision:"371e8f139c0d98886ced484f285d9649"},{url:"/js/admin-accounts.js",revision:"9a226e924b50b7da25375b584f244217"},{url:"/js/admin-accounts.js.map",revision:"54b067f75e3c74fdbb1ed1e1d7bc5abe"},{url:"/js/admin-add-ons-changelog.js",revision:"aee40ed58725e9eba1a74c67ebbbf97f"},{url:"/js/admin-add-ons-changelog.js.map",revision:"445d7e81b35d80d942a2fa042e09d5bc"},{url:"/js/admin-add-ons-install.js",revision:"7efe75b7642f1362ce3383187a9dee03"},{url:"/js/admin-add-ons-install.js.map",revision:"3528240d09713717a75ffe085e6b33af"},{url:"/js/admin-add-ons.js",revision:"81f4d5e2212c1fe0da133e366617cf4b"},{url:"/js/admin-add-ons.js.map",revision:"4cb0845de12a03bd2da4e327785c719d"},{url:"/js/admin-affiliate-commissions-approve.js",revision:"27145e6b5c13d0f710a0ff3d592c0182"},{url:"/js/admin-affiliate-commissions-approve.js.map",revision:"f5ac08b1666b17004b91b234e3ca8dd9"},{url:"/js/admin-affiliate-commissions-reject.js",revision:"441cfb8f3b041aaf2330c14a0759682e"},{url:"/js/admin-affiliate-commissions-reject.js.map",revision:"545fdfb49dac8202c3b2fdd6f2e693d7"},{url:"/js/admin-affiliate-commissions-show.js",revision:"cf34e669ffde2e1ec356334180459fa6"},{url:"/js/admin-affiliate-commissions-show.js.map",revision:"106e88764509937ade026f7a86d40060"},{url:"/js/admin-affiliate-commissions.js",revision:"4da11829807f7f17b368c2c645a38123"},{url:"/js/admin-affiliate-commissions.js.map",revision:"41ff978fae2b5d4324d4448fa6a4958b"},{url:"/js/admin-affiliate-tree.js",revision:"8e407cff93d8a0b5a8e38a390078ab7b"},{url:"/js/admin-affiliate-tree.js.map",revision:"ebad35c39f0ba84f4f73b798b1b4b539"},{url:"/js/admin-affiliate.js",revision:"c25a746c64bad94929d20d1cd7f609c0"},{url:"/js/admin-affiliate.js.map",revision:"54d5cea42a664340ab47f874b21f2096"},{url:"/js/admin-chat-messages-delete.js",revision:"4809a20acae2362aa13aad610b643615"},{url:"/js/admin-chat-messages-delete.js.map",revision:"f5edb9ac9eb1d1dc849681f3a242abc2"},{url:"/js/admin-chat-messages.js",revision:"cd1fc5a6bb88f99be3ecf78f21fc5646"},{url:"/js/admin-chat-messages.js.map",revision:"5d772452d9acc95670320e87df6d386d"},{url:"/js/admin-chat-rooms-create.js",revision:"5ef91af3e3d3b1866c6685f0cac64a70"},{url:"/js/admin-chat-rooms-create.js.map",revision:"80e0f98493784ca35db607a016f58eea"},{url:"/js/admin-chat-rooms-delete.js",revision:"d27faaa767f3d676e672a8f1016e8b79"},{url:"/js/admin-chat-rooms-delete.js.map",revision:"67ebdfec4d5a4a38e58d44f767ee2156"},{url:"/js/admin-chat-rooms-edit.js",revision:"f355274bb9fceaa34f3db9a1ecfba830"},{url:"/js/admin-chat-rooms-edit.js.map",revision:"b1d09158df379edc18a6dc0a609dae90"},{url:"/js/admin-chat-rooms.js",revision:"896a5cbf0ccf5ebbbd186d51a5bcd442"},{url:"/js/admin-chat-rooms.js.map",revision:"3e6cb1dd846e125512ba47e3e8a6fcf2"},{url:"/js/admin-chat.js",revision:"8eb61daf788f80c96b5bbd8a20f9a86c"},{url:"/js/admin-chat.js.map",revision:"c38453f4be4cbbe2533d1ab127978852"},{url:"/js/admin-dashboard-affiliates.js",revision:"a3b3288c2133b050b1613fd52fd6fbd3"},{url:"/js/admin-dashboard-affiliates.js.map",revision:"1f64affe96cc535444f90ea611304732"},{url:"/js/admin-dashboard-games.js",revision:"da6f5ca3841d598bdd293a8886dba9f5"},{url:"/js/admin-dashboard-games.js.map",revision:"7c9822fe18fc6b7f9320d21a85dbb305"},{url:"/js/admin-dashboard-users.js",revision:"e3cca79f1d05d3be31c2c5574639beb7"},{url:"/js/admin-dashboard-users.js.map",revision:"d604afeb662c1c5de785ad7d44ecf837"},{url:"/js/admin-dashboard.js",revision:"e630d556d08d14913672c476246dd455"},{url:"/js/admin-dashboard.js.map",revision:"c39e50f16e46ee2f43ff6ff57c0d5e42"},{url:"/js/admin-games-show.js",revision:"1f40718d099199140b971032323cd818"},{url:"/js/admin-games-show.js.map",revision:"79f644c9175b3142f50ad0e70dcf6c94"},{url:"/js/admin-games.js",revision:"6e6424a8cbe2d690db2f10fdc7ee29a5"},{url:"/js/admin-games.js.map",revision:"4e952e1a5a1bd19d5b1417faecbf9501"},{url:"/js/admin-license.js",revision:"cc9d4e43967ffc55c94e201e8a69a4fc"},{url:"/js/admin-license.js.map",revision:"414b7d3a3e310a03ca4d95bc2974d063"},{url:"/js/admin-maintenance.js",revision:"5aca47c6740afcba35ad41d3a9b75ed8"},{url:"/js/admin-maintenance.js.map",revision:"0a8591d6bf276b90548362d27836d766"},{url:"/js/admin-settings.js",revision:"5a6eeeee626c692548f4a78c0992b2a1"},{url:"/js/admin-settings.js.map",revision:"bc1bc0cff842c3d44ca18156541879f3"},{url:"/js/admin-users-delete.js",revision:"97750b965e1fc72a0a9cdecacb1bf02e"},{url:"/js/admin-users-delete.js.map",revision:"c331ac8d210c7094efeeaf3cbd4a04ba"},{url:"/js/admin-users-edit.js",revision:"63b32abf89f259dcf2eb0147d91b2f79"},{url:"/js/admin-users-edit.js.map",revision:"032af5bf5e78968349d540ba6ec2d716"},{url:"/js/admin-users-mail.js",revision:"f5ace159cd9ce2e1dceea28967436ed7"},{url:"/js/admin-users-mail.js.map",revision:"041a3dd65660de859e3215adee8ab41d"},{url:"/js/admin-users-show.js",revision:"023be4062b9b0decdc0e517cb2af5f24"},{url:"/js/admin-users-show.js.map",revision:"2decb996a9638c57693bf4ebf3bf5b6d"},{url:"/js/admin-users.js",revision:"f8263db61afad60b2155c6ff95023ab8"},{url:"/js/admin-users.js.map",revision:"b78886a62c945c7b4027bbfce099c03b"},{url:"/js/components.js",revision:"dd139cdf8724d32dc6be1bec9310ff9c"},{url:"/js/components.js.map",revision:"b5e486345dcfa18dda4a0f9a16fa2b9a"},{url:"/js/email-verify.js",revision:"397ed83fb23ac037d2bca24d3ecd161c"},{url:"/js/email-verify.js.map",revision:"656d174593575d54c9dbe874b4b7d086"},{url:"/js/email.js",revision:"7e1e881682a4f8fb3f27f455edd7498b"},{url:"/js/email.js.map",revision:"ae7fa30885ecfb8b7c2565981c48d101"},{url:"/js/games.js",revision:"c83ee2c45e7ec5aaf6900af473ad36ce"},{url:"/js/games.js.map",revision:"bdc817f1fdc6fdf34c1ab0c070ab31f7"},{url:"/js/history-games-show.js",revision:"c6a5d2853a8d247f83d3b31887cd159c"},{url:"/js/history-games-show.js.map",revision:"b0a59605b6370624d340c60d49ad0e0c"},{url:"/js/history-games-verify.js",revision:"e2f68788e39654d0c25d564d82eba00e"},{url:"/js/history-games-verify.js.map",revision:"d38ce7434aaab3fadc5753cc057ed908"},{url:"/js/history-losses.js",revision:"3f86eab6c86484b95170c728f4b300a5"},{url:"/js/history-losses.js.map",revision:"488d9a8b008ce76d2d2d669ff714a118"},{url:"/js/history-recent.js",revision:"2de1773c79beca401d12d465a3b8b21b"},{url:"/js/history-recent.js.map",revision:"9a3de476f62ba016764fbc442181734a"},{url:"/js/history-user.js",revision:"eae557a05eb4ab21088f7de65ff87b7f"},{url:"/js/history-user.js.map",revision:"8b32918b3f3f3fbd11ab0458855c89a9"},{url:"/js/history-wins.js",revision:"339d7ce077e2527bff5d621cf99bfbe9"},{url:"/js/history-wins.js.map",revision:"d203ec3aa69b6588ba969b7b8e70e1fe"},{url:"/js/history.js",revision:"62116493a3278d22663a4cf213a9d28d"},{url:"/js/history.js.map",revision:"7c79959057ece307dca29d1e04818422"},{url:"/js/leaderboard.js",revision:"16b80a762f0f005bb344c0e15ef07822"},{url:"/js/leaderboard.js.map",revision:"006a8274e8120c13259a09e0f8f9b551"},{url:"/js/login.js",revision:"9ba1b20be86498619db29b525447f667"},{url:"/js/login.js.map",revision:"827fe2dc9a6ef3065fdd523039e09812"},{url:"/js/mixins.js",revision:"7f8d9804a0dfbbe5e609a39762c07eba"},{url:"/js/mixins.js.map",revision:"057b63e22926c75acb1188ae1e214c75"},{url:"/js/offline.js",revision:"345e631ddb4ce1556212dd77400a2b6c"},{url:"/js/offline.js.map",revision:"a35ac4500f5a1163535c4fd480bca2ff"},{url:"/js/packages/baccarat-resources-js-pages-admin-settings.js",revision:"58833edc8e3ad79958607653cf69f4a3"},{url:"/js/packages/baccarat-resources-js-pages-admin-settings.js.map",revision:"92421980eb11041e383835cc2e032ebb"},{url:"/js/packages/baccarat-resources-js-pages-game.js",revision:"2a94d5f64bbf0635864c7d86e21d616f"},{url:"/js/packages/baccarat-resources-js-pages-game.js.map",revision:"66110724df83fefef56920f4ec7c55cd"},{url:"/js/packages/baccarat-resources-js-pages-info.js",revision:"15098bab7a1af5834296809a8098b8e4"},{url:"/js/packages/baccarat-resources-js-pages-info.js.map",revision:"673fc6edd905fb87bd7ebde73ac44a76"},{url:"/js/packages/baccarat-resources-js-pages-show.js",revision:"4df3d07378197ac4f88d885061b9b3e8"},{url:"/js/packages/baccarat-resources-js-pages-show.js.map",revision:"ce71770e04c72e135f7ea47b174adc2d"},{url:"/js/packages/casino-holdem-resources-js-pages-admin-settings.js",revision:"fd6016ee100d317d790019fa5396d14e"},{url:"/js/packages/casino-holdem-resources-js-pages-admin-settings.js.map",revision:"4f164b807496e0eb472ca03c143f29f6"},{url:"/js/packages/casino-holdem-resources-js-pages-game.js",revision:"3234787c8358c48022443498a9eba1d5"},{url:"/js/packages/casino-holdem-resources-js-pages-game.js.map",revision:"96856e1d8f347b796a9de7eb1e57e65f"},{url:"/js/packages/casino-holdem-resources-js-pages-info.js",revision:"7250a110189d572889a22d468a17ff6c"},{url:"/js/packages/casino-holdem-resources-js-pages-info.js.map",revision:"f246709744d9dd1e936c00da9396b43a"},{url:"/js/packages/casino-holdem-resources-js-pages-show.js",revision:"c0cce44fc49bb8cae27f4a91134d7cd3"},{url:"/js/packages/casino-holdem-resources-js-pages-show.js.map",revision:"4267269872650b218bcdb0c93e083784"},{url:"/js/packages/multiplayer-blackjack-resources-js-pages-admin-settings.js",revision:"aeaadb516b3d7ae625058ee560842889"},{url:"/js/packages/multiplayer-blackjack-resources-js-pages-admin-settings.js.map",revision:"273e97ebb132d08a12db7c1930d5dd54"},{url:"/js/packages/multiplayer-blackjack-resources-js-pages-game.js",revision:"8bc35dfdbe40815577bef8845378398d"},{url:"/js/packages/multiplayer-blackjack-resources-js-pages-game.js.map",revision:"9abae91ec9c99409fface2912df85778"},{url:"/js/packages/multiplayer-blackjack-resources-js-pages-info.js",revision:"e7c25de62720b9d8540de8118250dbf0"},{url:"/js/packages/multiplayer-blackjack-resources-js-pages-info.js.map",revision:"d25552a77adb23d61c2c70b70c1f0f35"},{url:"/js/packages/multiplayer-blackjack-resources-js-pages-show.js",revision:"b903d23100bb501a840fcdf500346702"},{url:"/js/packages/multiplayer-blackjack-resources-js-pages-show.js.map",revision:"bc8eaab9e7c8b1c432f37a77e43add75"},{url:"/js/pages.js",revision:"92705a1409b3cebfc867ef226ab18ec3"},{url:"/js/pages.js.map",revision:"09d8b5c874c960d3bc9deff3fa2ce6ce"},{url:"/js/password-email.js",revision:"ccff4538e8eccee2ec70c2e5a8346017"},{url:"/js/password-email.js.map",revision:"c4bff749fbf3d1df8c873f6ccda740e2"},{url:"/js/password-reset.js",revision:"09990363c7aa555765d16b498a1e59be"},{url:"/js/password-reset.js.map",revision:"d9596086acadb516f8ce1b17c41f88b6"},{url:"/js/register.js",revision:"a3cc28ad81c2d5c079a6028279b793ad"},{url:"/js/register.js.map",revision:"bd68b8d19b280807a7a28b0481a226ed"},{url:"/js/user-account-faucet.js",revision:"d740465e63c4a92d71974884d4eefb0f"},{url:"/js/user-account-faucet.js.map",revision:"23fab4bc151a4ca956bc9c63ed1e9e7f"},{url:"/js/user-account-transactions.js",revision:"79d682557625b3432ff1f5ea120663ee"},{url:"/js/user-account-transactions.js.map",revision:"e38a550630e2d5f57cb57b53873aed69"},{url:"/js/user-affiliate-commissions.js",revision:"ce6629850fea4ac24f5b98893474e299"},{url:"/js/user-affiliate-commissions.js.map",revision:"d64fa3a0a16ed0b3d9bee5cdc3228aaf"},{url:"/js/user-affiliate-info.js",revision:"f8f0503bd8bfe13e1422517a85f265fd"},{url:"/js/user-affiliate-info.js.map",revision:"e4dc3745a48ba4ad2efc74a35236afa0"},{url:"/js/user-affiliate-stats.js",revision:"ee448288e8fb42115d5b52f6316f1245"},{url:"/js/user-affiliate-stats.js.map",revision:"390703223e58749d279342dbe9b95f03"},{url:"/js/user-affiliate-tree.js",revision:"ae9c895d9e769f873132f9ebbb14c75d"},{url:"/js/user-affiliate-tree.js.map",revision:"943057d11cef87f1cc661306bafc50ae"},{url:"/js/user-affiliate.js",revision:"c4141f8159703ac2d5fa45ecf6b8e480"},{url:"/js/user-affiliate.js.map",revision:"3f435bffc8faf45ea1f73f088cc0ef25"},{url:"/js/user-edit.js",revision:"ea4d0aad0670006ba09ea2d9c20f5d9d"},{url:"/js/user-edit.js.map",revision:"784aa662b0b38c936db831026f4f8f5a"},{url:"/js/user-security-2fa.js",revision:"bd41fb4095c63fbc480bb92b29a745fc"},{url:"/js/user-security-2fa.js.map",revision:"d7bea577b9bea1120f01d7964a10f16a"},{url:"/js/user-security-password.js",revision:"5ade7abe80f5ec14c1527a4d233eb5e2"},{url:"/js/user-security-password.js.map",revision:"64ea37768c99d1cadead030896d31b57"},{url:"/js/user-security.js",revision:"cacb56b88dbcea5acb893ae4cc2167fc"},{url:"/js/user-security.js.map",revision:"8e1fa50ab57197ed41f3e25420dafd4a"},{url:"/js/users-show.js",revision:"1e83bdbcec9c11a9819d4a5911e4fafc"},{url:"/js/users-show.js.map",revision:"bfa919d7f2a7b79b3fb37ae43e9ca4f2"},{url:"/js/vendor/vue-recaptcha.js",revision:"e71c6b620f737b65d7f759466ab653cc"},{url:"/js/vendor/vue-recaptcha.js.map",revision:"96dcecc5a2cf8891681da36bad22e406"},{url:"/js/vendor/vuetify.js",revision:"0145d432aedd11e6c0b511b50c2800bb"},{url:"/js/vendor/vuetify.js.map",revision:"f586db3544c82c8d2043e1128c571a4e"}],{}),e.registerRoute(/^https:\/\/fonts\.(?:googleapis|gstatic)\.com\//,new e.NetworkFirst,"GET"),e.registerRoute(/\.(?:png|jpg|jpeg|svg|wav|mp3|webm|eot|ttf|woff|woff2)$/,new e.CacheFirst({cacheName:"assets",plugins:[new e.ExpirationPlugin({maxEntries:50,purgeOnQuotaError:!0})]}),"GET")}));
