(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-2d0d0f97"],{"69ec":function(t,e,a){"use strict";a.r(e);var s=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{directives:[{name:"loading",rawName:"v-loading",value:!t.isLoaded,expression:"!isLoaded"}],staticClass:"wbs-content-inner",class:{"wb-page-loaded":t.isLoaded}},[a("div",{staticClass:"wbs-main log-box"},[t.isLoaded?a("table",{staticClass:"wbs-form-table"},[a("tr",[a("th",{staticClass:"w8em"},[t._v("功能开关")]),a("td",{staticClass:"info"},[a("el-switch",{attrs:{"active-value":"1","inactive-value":"0"},model:{value:t.opt.switch,callback:function(e){t.$set(t.opt,"switch",e)},expression:"opt.switch"}}),a("span",{staticClass:"ml"},[t._v("功能已"+t._s(1==t.opt.switch?"开启":"关闭"))])],1)]),a("tr",[a("th",[t._v("文章类型")]),a("td",{staticClass:"info"},[a("el-checkbox-group",{model:{value:t.opt.post_type,callback:function(e){t.$set(t.opt,"post_type",e)},expression:"opt.post_type"}},t._l(t.cnf.post_type,(function(e,s){return a("el-checkbox",{key:s,attrs:{label:s}},[t._v(t._s(e))])})),1)],1)]),a("tr",[a("th",[t._v("文章状态")]),a("td",{staticClass:"info"},[a("el-checkbox-group",{model:{value:t.opt.post_status,callback:function(e){t.$set(t.opt,"post_status",e)},expression:"opt.post_status"}},t._l(t.cnf.post_status,(function(e,s){return a("el-checkbox",{key:s,attrs:{label:s}},[t._v(t._s(e))])})),1)],1)]),a("tr",[a("th",[t._v("添加方式")]),a("td",[a("el-radio-group",{model:{value:t.opt.source,callback:function(e){t.$set(t.opt,"source",e)},expression:"opt.source"}},t._l(t.cnf.source,(function(e,s){return a("el-radio",{key:s,attrs:{label:s}},[t._v(t._s(e))])})),1),a("div",{staticClass:"description mt"},[t._v("* 手动添加至定时发布，需进入对应的草稿或待审将文章批量添加至定时计划列表。")])],1)]),a("tr",[a("th",[t._v("定时规则")]),a("td",[a("div",{staticClass:"with-sub-form-table"},[a("table",{staticClass:"wbs-form-table-sub"},[a("tr",[a("th",{staticClass:"w8em"},[t._v("周期处理上限")]),a("td",[a("el-input-number",{attrs:{size:"mini",placeholder:"每定时周期处理文章上限"},model:{value:t.opt.post_max,callback:function(e){t.$set(t.opt,"post_max",e)},expression:"opt.post_max"}})],1)]),a("tr",[a("th",[t._v("每天发布文章数")]),a("td",[a("el-input-number",{attrs:{size:"mini",placeholder:"每天发布文章数"},model:{value:t.opt.post_num,callback:function(e){t.$set(t.opt,"post_num",e)},expression:"opt.post_num"}})],1)]),a("tr",[a("th",[t._v("发布时间间隔")]),a("td",{staticClass:"info"},[a("el-radio-group",{model:{value:t.opt.delay,callback:function(e){t.$set(t.opt,"delay",e)},expression:"opt.delay"}},[a("el-radio",{attrs:{label:"0"}},[t._v("随机（推荐）")]),a("el-radio",{attrs:{label:"1"}},[t._v("固定")])],1),1==t.opt.delay?a("span",{staticClass:"ml"},[a("el-input-number",{attrs:{size:"mini",placeholder:""},model:{value:t.opt.delay_minute,callback:function(e){t.$set(t.opt,"delay_minute",e)},expression:"opt.delay_minute"}}),a("span",[t._v("分钟")])],1):t._e()],1)]),a("tr",[a("th",[t._v("每日发布时间区间")]),a("td",{staticClass:"info"},[a("el-radio-group",{on:{change:t.updateRange},model:{value:t.opt.range,callback:function(e){t.$set(t.opt,"range",e)},expression:"opt.range"}},[a("el-radio",{attrs:{label:"1"}},[t._v("每日统一")]),a("el-radio",{attrs:{label:"2"}},[t._v("每日各异")])],1),2==t.opt.range?t._l(t.tabList,(function(e,s){return a("div",{key:"w-t-"+s,staticClass:"wb-flex-box v-center wb-el-slider"},[a("div",{staticClass:"w4em"},[t._v(t._s(e.w))]),a("el-slider",{staticClass:"primary",attrs:{range:"","show-stops":"","format-tooltip":t.formatTooltip,max:24,size:"mini"},model:{value:t.opt.week_time[e.v],callback:function(a){t.$set(t.opt.week_time,e.v,a)},expression:"opt.week_time[r.v]"}}),a("div",{staticClass:"ml ft"},[a("span",{staticClass:"wk"},[t._v("周期: ")]),t._v(" "+t._s(t.opt.week_time[e.v][0])+":00 ~ "+t._s(t.opt.week_time[e.v][1])+":00 ")])],1)})):a("div",{staticClass:"wb-flex-box v-center wb-el-slider"},[a("el-slider",{staticClass:"primary",attrs:{range:"","show-stops":"","format-tooltip":t.formatTooltip,max:24,size:"mini"},model:{value:t.opt.time_range,callback:function(e){t.$set(t.opt,"time_range",e)},expression:"opt.time_range"}}),a("div",{staticClass:"ml"},[a("span",{staticClass:"wk"},[t._v("周期: ")]),t._v(" "+t._s(t.opt.time_range[0])+":00 ~ "+t._s(t.opt.time_range[1])+":00 ")])],1)],2)])])])])]),a("tr",[a("th",[t._v("失败任务")]),a("td",{staticClass:"info"},[a("el-radio",{attrs:{label:"1"},model:{value:t.opt.fail,callback:function(e){t.$set(t.opt,"fail",e)},expression:"opt.fail"}},[t._v("重新加入任务")]),a("el-radio",{attrs:{label:"2"},model:{value:t.opt.fail,callback:function(e){t.$set(t.opt,"fail",e)},expression:"opt.fail"}},[t._v("检测直接发布")])],1)]),a("tr",[a("th",[t._v("定时周期")]),a("td",{staticClass:"info"},[a("el-radio",{attrs:{label:"hourly"},model:{value:t.opt.cron,callback:function(e){t.$set(t.opt,"cron",e)},expression:"opt.cron"}},[t._v("每小时检测")]),a("el-radio",{attrs:{label:"daily"},model:{value:t.opt.cron,callback:function(e){t.$set(t.opt,"cron",e)},expression:"opt.cron"}},[t._v("每天检测")])],1)])]):t._e(),t.isPro?t._e():a("div",{staticClass:"getpro-mask"},[a("div",{staticClass:"mask-inner"},[a("el-button",{attrs:{type:"primary",size:"small"},on:{click:function(e){return t.doActivePro()}}},[t._v("获取PRO版本")]),a("p",{staticClass:"tips"},[t._v("* 激活PRO版本即可使用")])],1)]),a("wb-prompt",{directives:[{name:"show",rawName:"v-show",value:t.isLoaded,expression:"isLoaded"}],staticClass:"mt"})],1),t.$cnf.is_pro?t._e():a("wbs-more-sources"),t.isPro?a("wbs-ctrl-bar",{on:{submit:t.updateData}}):t._e()],1)},o=[],i={name:"MagicPostScheduleConfig",components:{},data(){return{tabWeek:"1",formChanged:0,isLoaded:!1,isPro:0,opt:{},cnf:{}}},created(){const t=this;t.$cnf.is_pro&&t.$verify((e,a)=>{console.log([e,a]),e?t.isPro=e:(t.isPro=0,t.$cnf.is_pro=0)}),t.loadData(),t.$cnf.is_pro>0&&t.$isPrdActive(t.$WB)},methods:{loadData(){const t=this;t.$api.getData({_ajax_nonce:t.$cnf._ajax_nonce,action:t.$cnf.action.act,op:"schedule_setting"}).then(e=>{t.opt=e.opt,t.cnf=e.cnf,t.isLoaded=!0})},updateData(t){const e=this,a={_ajax_nonce:e.$cnf._ajax_nonce,action:e.$cnf.action.act,op:"schedule_update",opt:e.opt};e.isPro&&e.$api.saveData(a).then(a=>{e.$wbui.toast("设置保存成功"),e.formChanged=1,t&&t()})},updateRange(){const t=this;t.tabWeek="1"},formatTooltip(t){return t+":00"}},computed:{tabList(){const t=this;return 2==t.opt.range?t.cnf.week:t.cnf.month}}},l=i,n=a("2877"),c=Object(n["a"])(l,s,o,!1,null,null,null);e["default"]=c.exports}}]);