(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-2d0b6182"],{"1c60":function(t,a,e){"use strict";e.r(a);var s=function(){var t=this,a=t.$createElement,e=t._self._c||a;return e("div",{staticClass:"wbs-content"},[e("div",{directives:[{name:"loading",rawName:"v-loading",value:!t.isLoaded,expression:"!isLoaded"}],staticClass:"wbs-content-inner",class:{"wb-page-loaded":t.isLoaded}},[e("div",{staticClass:"wbs-main"},[t.isLoaded?e("div",{staticClass:"setting-box"},[e("table",{staticClass:"wbs-form-table"},[e("tr",[e("th",{staticClass:"w8em"},[t._v("功能开关")]),e("td",[e("el-switch",{attrs:{"active-value":"1","inactive-value":"0"},model:{value:t.opt.switch,callback:function(a){t.$set(t.opt,"switch",a)},expression:"opt.switch"}}),e("span",{staticClass:"ml"},[t._v("功能已"+t._s(1==t.opt.switch?"开启":"关闭"))]),e("div",{staticClass:"description mt"},[t._v("利用第三方翻译API对文章进行翻译，快速生产大量原创文章。")])],1)]),e("tr",[e("th",[t._v("选择API")]),e("td",{staticClass:"info"},[e("el-radio-group",{model:{value:t.opt.api,callback:function(a){t.$set(t.opt,"api",a)},expression:"opt.api"}},[e("el-radio",{attrs:{label:"google"}},[t._v("谷歌翻译（官方）")]),e("el-radio",{attrs:{label:"google2"}},[t._v("谷歌翻译（第三方）")]),e("el-radio",{attrs:{label:"baidu"}},[t._v("百度翻译（官方）")])],1),"google"==t.opt.api?e("div",{staticClass:"with-sub-form-table mt"},[e("table",{staticClass:"wbs-form-table-sub"},[e("tbody",[e("tr",[e("th",{staticClass:"row w6em"},[t._v("API Key")]),e("td",[e("el-input",{attrs:{size:"mini",placeholder:""},model:{value:t.opt.google.key,callback:function(a){t.$set(t.opt.google,"key",a)},expression:"opt.google.key"}})],1)]),t._m(0)])])]):t._e(),"google2"==t.opt.api?e("div",{staticClass:"with-sub-form-table mt"},[e("table",{staticClass:"wbs-form-table-sub"},[e("tbody",[e("tr",[e("th",{staticClass:"row w6em"},[t._v("选择代理")]),e("td",{staticClass:"info"},[e("el-radio",{attrs:{label:"none"},model:{value:t.opt.google2.proxy,callback:function(a){t.$set(t.opt.google2,"proxy",a)},expression:"opt.google2.proxy"}},[t._v("否")]),e("el-radio",{attrs:{label:"wbolt"},model:{value:t.opt.google2.proxy,callback:function(a){t.$set(t.opt.google2,"proxy",a)},expression:"opt.google2.proxy"}},[t._v("闪电博")])],1)]),t._m(1)])])]):t._e(),"baidu"==t.opt.api?e("div",{staticClass:"with-sub-form-table mt"},[e("table",{staticClass:"wbs-form-table-sub"},[e("tbody",[e("tr",[e("th",{staticClass:"row w6em"},[t._v("API Key")]),e("td",[e("el-input",{attrs:{size:"mini",placeholder:""},model:{value:t.opt.baidu.key,callback:function(a){t.$set(t.opt.baidu,"key",a)},expression:"opt.baidu.key"}})],1)]),e("tr",[e("th",{staticClass:"row w6em"},[t._v("Secret Key")]),e("td",[e("el-input",{attrs:{size:"mini",placeholder:""},model:{value:t.opt.baidu.secret,callback:function(a){t.$set(t.opt.baidu,"secret",a)},expression:"opt.baidu.secret"}})],1)]),t._m(2)])])]):t._e()],1)]),e("tr",[e("th",[t._v("翻译内容")]),e("td",{staticClass:"info"},[e("el-checkbox-group",{model:{value:t.opt.trans,callback:function(a){t.$set(t.opt,"trans",a)},expression:"opt.trans"}},[e("el-checkbox",{attrs:{label:"post_title"}},[t._v("标题")]),e("el-checkbox",{attrs:{label:"post_content"}},[t._v("正文")])],1)],1)]),e("tr",[e("th",[t._v("自动翻译")]),e("td",{staticClass:"info"},[e("el-switch",{attrs:{"active-value":"1","inactive-value":"0"},model:{value:t.opt.auto,callback:function(a){t.$set(t.opt,"auto",a)},expression:"opt.auto"}}),e("span",{staticClass:"ml"},[t._v(t._s(1==t.opt.auto?"已启用，自动翻译仅对草稿进行扫描翻译，不对其他文章状态执行任务。":"未启用"))])],1)]),e("tr",[e("th",[t._v("翻译语言")]),e("td",{staticClass:"info"},[e("el-radio-group",{model:{value:t.opt.lan,callback:function(a){t.$set(t.opt,"lan",a)},expression:"opt.lan"}},[e("el-radio",{attrs:{label:"en-zh"}},[t._v("英译中（默认）")]),e("el-radio",{attrs:{label:"zh-en"}},[t._v("中译英")])],1)],1)]),e("tr",[e("th",[t._v("错误日志")]),e("td",{staticClass:"info"},[t.errorList&&t.errorList.length?e("ul",{staticStyle:{"max-height":"400px","overflow-y":"auto"}},t._l(t.errorList,(function(a,s){return e("li",{key:"err-li-"+s},[t._v(" "+t._s(a)+" ")])})),0):e("p",{staticClass:"wk"},[t._v("(无)")])])]),e("tr",[e("th",[t._v("test")]),e("td",[e("button",{attrs:{type:"button"},on:{click:t.test}},[t._v("test")])])])]),t.isPro?t._e():e("div",{staticClass:"getpro-mask"},[e("div",{staticClass:"mask-inner"},[e("el-button",{attrs:{type:"primary",size:"small"},on:{click:function(a){return t.doActivePro()}}},[t._v("获取PRO版本")]),e("p",{staticClass:"tips"},[t._v("* 激活PRO版本即可使用")])],1)])]):t._e(),e("wb-prompt",{directives:[{name:"show",rawName:"v-show",value:t.isLoaded,expression:"isLoaded"}],staticClass:"mt"})],1),t.$cnf.is_pro?t._e():e("wbs-more-sources"),e("wbs-ctrl-bar",{on:{submit:t.updateData}})],1)])},o=[function(){var t=this,a=t.$createElement,e=t._self._c||a;return e("tr",[e("th"),e("td",[e("div",{staticClass:"description"},[t._v(" 温馨提示：您可以通过注册登录"),e("a",{staticClass:"link",attrs:{target:"_blank",href:"https://cloud.google.com/"}},[t._v("Google Cloud")]),t._v("获取，"),e("a",{staticClass:"link",attrs:{target:"_blank",href:" https://www.wbolt.com/cloud-translation-api.html"}},[t._v("查看谷歌翻译API申请及配置教程")]),t._v("。 ")])])])},function(){var t=this,a=t.$createElement,e=t._self._c||a;return e("tr",[e("th"),e("td",[e("div",{staticClass:"description"},[t._v(" 温馨提示：如果网站服务器为中国大陆境内服务器，请勿选择该选项；代理服务器可能会有限制，如果翻译失败，请稍后再试。 ")])])])},function(){var t=this,a=t.$createElement,e=t._self._c||a;return e("tr",[e("th"),e("td",[e("div",{staticClass:"description"},[t._v(" 温馨提示："),e("a",{staticClass:"link",attrs:{target:"_blank",href:"https://ai.baidu.com/tech/mt/doc_trans"}},[t._v("百度文档翻译")]),t._v("，非实時翻译返回翻译结果，提交需要翻译的文档，一段时间后再获取翻译的文档。 ")])])])}],i={name:"MagicPostTranslate",components:{},data(){const t=this;return{wb_cnf:t.$cnf,formChanged:0,isLoaded:!1,isPro:t.$cnf.is_pro,opt:{},activeName:"google",errorList:[]}},created(){const t=this;t.$cnf.is_pro&&t.$verify((a,e)=>{console.log([a,e]),a?(t.$cnf.is_pro=a,t.isPro=a):(t.isPro=0,t.$cnf.is_pro=0)}),t.loadData()},methods:{test(){console.log([this.isPro])},loadData(){const t=this;t.$api.getData({_ajax_nonce:t.$cnf._ajax_nonce,action:t.$cnf.action.act,op:"translate_setting"}).then(a=>{t.opt=a["opt"],a["data"]&&(t.errorList=a["data"]),t.$nextTick(()=>{t.formChanged=1}),t.isLoaded=!0})},updateData(t){const a=this,e={_ajax_nonce:a.$cnf._ajax_nonce,action:a.$cnf.action.act,op:"translate_update",opt:a.opt};a.$api.saveData(e).then(e=>{a.$wbui.toast("设置保存成功"),a.formChanged=1,t&&t()})}},watch:{opt:{handler(){this.formChanged++},deep:!0}},beforeRouteLeave(t,a,e){const s=this;s.formChanged>1?s.$wbui.open({content:"您修改的设置尚未保存，确定离开此页面吗？",btn:["保存并离开","放弃修改"],yes(){return e(!1),s.updateData((function(){e()})),!1},no(){return e(),!1}}):e()}},l=i,n=e("2877"),r=Object(n["a"])(l,s,o,!1,null,null,null);a["default"]=r.exports}}]);