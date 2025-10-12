import{c as q,_ as p}from"./codePreview-D3lNCkU-.js";import{u as Z,a as H,m as h}from"./main-Ck4yvBPR.js";import{d as K,c,r as u,k as Q,l,m as t,C as i,P as m,x as f,u as o,f as k,q as b}from"./vendor-BuRU_kNN.js";import"./quill-De1S0mUZ.js";const _={class:"grid grid-cols-1 lg:grid-cols-2 gap-6"},ee={class:"panel"},te={class:"flex items-center justify-between mb-5"},re={class:"mb-5"},oe={class:"panel"},se={class:"flex items-center justify-between mb-5"},ae={class:"mb-5"},le={class:"panel"},ie={class:"flex items-center justify-between mb-5"},ne={class:"mb-5"},de={class:"panel"},pe={class:"flex items-center justify-between mb-5"},he={class:"mb-5"},ce={class:"panel"},ue={class:"flex items-center justify-between mb-5"},me={class:"mb-5"},fe={class:"panel"},ke={class:"flex items-center justify-between mb-5"},be={class:"mb-5"},ge={class:"panel"},we={class:"flex items-center justify-between mb-5"},xe={class:"mb-5"},Ce={class:"panel"},ve={class:"flex items-center justify-between mb-5"},ye={class:"mb-5"},Me={class:"panel"},De={class:"flex items-center justify-between mb-5"},Se={class:"mb-5"},Le={class:"panel"},Ae={class:"flex items-center justify-between mb-5"},Be={class:"mb-5"},Te={class:"panel"},ze={class:"flex items-center justify-between mb-5"},Re={class:"mb-5"},je={class:"panel"},Oe={class:"flex items-center justify-between mb-5"},Je={class:"mb-5"},Ee=K({__name:"charts",setup(Fe){Z({title:"Charts"});const s=H(),{codeArr:n,toggleCode:d}=q(),g=(r,e,a)=>{for(var w=0,x=[];w<e;){var U=Math.floor(Math.random()*750)+1,Y=Math.floor(Math.random()*(a.max-a.min+1))+a.min,I=Math.floor(Math.random()*61)+15;x.push([U,Y,I]),w++}return x},C=c(()=>{const r=!!(s.theme==="dark"||s.isDarkMode),e=s.rtlClass==="rtl";return{chart:{height:300,type:"line",toolbar:!1},colors:["#194C8D"],tooltip:{marker:!1,y:{formatter(a){return"$"+a}},theme:r?"dark":"light"},stroke:{width:2,curve:"smooth"},xaxis:{categories:["Jan","Feb","Mar","Apr","May","June"],axisBorder:{color:r?"#191e3a":"#e0e6ed"}},yaxis:{opposite:!!e,labels:{offsetX:e?-20:0}},grid:{borderColor:r?"#191e3a":"#e0e6ed"}}}),v=u([{name:"Sales",data:[45,55,75,25,45,110]}]),y=c(()=>{const r=!!(s.theme==="dark"||s.isDarkMode),e=s.rtlClass==="rtl";return{chart:{type:"area",height:300,zoom:{enabled:!1},toolbar:{show:!1}},colors:["#805dca"],dataLabels:{enabled:!1},stroke:{width:2,curve:"smooth"},xaxis:{axisBorder:{color:r?"#191e3a":"#e0e6ed"}},yaxis:{opposite:!!e,labels:{offsetX:e?-40:0}},labels:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],legend:{horizontalAlign:"left"},grid:{borderColor:r?"#191e3a":"#e0e6ed"},tooltip:{theme:r?"dark":"light"}}}),M=u([{name:"Income",data:[16800,16800,15500,17800,15500,17e3,19e3,16e3,15e3,17e3,14e3,17e3]}]),D=c(()=>{const r=!!(s.theme==="dark"||s.isDarkMode),e=s.rtlClass==="rtl";return{chart:{height:300,type:"bar",zoom:{enabled:!1},toolbar:{show:!1}},colors:["#805dca","#e7515a"],dataLabels:{enabled:!1},stroke:{show:!0,width:2,colors:["transparent"]},plotOptions:{bar:{horizontal:!1,columnWidth:"55%",endingShape:"rounded"}},grid:{borderColor:r?"#191e3a":"#e0e6ed"},xaxis:{categories:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct"],axisBorder:{color:r?"#191e3a":"#e0e6ed"}},yaxis:{opposite:!!e,labels:{offsetX:e?-10:0}},tooltip:{theme:r?"dark":"light",y:{formatter:function(a){return a}}}}}),S=u([{name:"Net Profit",data:[44,55,57,56,61,58,63,60,66]},{name:"Revenue",data:[76,85,101,98,87,105,91,114,94]}]),L=c(()=>{const r=!!(s.theme==="dark"||s.isDarkMode),e=s.rtlClass==="rtl";return{chart:{height:300,type:"bar",stacked:!0,zoom:{enabled:!1},toolbar:{show:!1}},colors:["#2196f3","#3b3f5c"],responsive:[{breakpoint:480,options:{legend:{position:"bottom",offsetX:-10,offsetY:5}}}],plotOptions:{bar:{horizontal:!1}},xaxis:{type:"datetime",categories:["01/01/2011 GMT","01/02/2011 GMT","01/03/2011 GMT","01/04/2011 GMT","01/05/2011 GMT","01/06/2011 GMT"],axisBorder:{color:r?"#191e3a":"#e0e6ed"}},yaxis:{opposite:!!e,labels:{offsetX:e?-20:0}},grid:{borderColor:r?"#191e3a":"#e0e6ed"},legend:{position:"right",offsetY:40},tooltip:{theme:r?"dark":"light"},fill:{opacity:.8}}}),A=u([{name:"PRODUCT A",data:[44,55,41,67,22,43]},{name:"PRODUCT B",data:[13,23,20,8,13,27]}]),B=c(()=>{const r=!!(s.theme==="dark"||s.isDarkMode),e=s.rtlClass==="rtl";return{chart:{height:300,type:"bar",zoom:{enabled:!1},toolbar:{show:!1}},dataLabels:{enabled:!1},stroke:{show:!0,width:1},colors:["#194C8D"],xaxis:{categories:["Sun","Mon","Tue","Wed","Thur","Fri","Sat"],axisBorder:{color:r?"#191e3a":"#e0e6ed"}},yaxis:{opposite:!!e,reversed:!!e},grid:{borderColor:r?"#191e3a":"#e0e6ed"},plotOptions:{bar:{horizontal:!0}},fill:{opacity:.8}}}),T=u([{name:"Sales",data:[44,55,41,67,22,43,21,70]}]),z=c(()=>{const r=!!(s.theme==="dark"||s.isDarkMode),e=s.rtlClass==="rtl";return{chart:{height:300,zoom:{enabled:!1},toolbar:{show:!1}},colors:["#2196f3","#00ab55","#194C8D"],stroke:{width:[0,2,2],curve:"smooth"},plotOptions:{bar:{columnWidth:"50%"}},fill:{opacity:[1,.25,1]},labels:["01/01/2022","02/01/2022","03/01/2022","04/01/2022","05/01/2022","06/01/2022","07/01/2022","08/01/2022","09/01/2022","10/01/2022","11/01/2022"],markers:{size:0},xaxis:{type:"datetime",axisBorder:{color:r?"#191e3a":"#e0e6ed"}},yaxis:{title:{text:"Points"},min:0,opposite:!!e,labels:{offsetX:e?-10:0}},grid:{borderColor:r?"#191e3a":"#e0e6ed"},tooltip:{shared:!0,intersect:!1,theme:r?"dark":"light",y:{formatter:a=>typeof a<"u"?a.toFixed(0)+" points":a}},legend:{itemMargin:{horizontal:4,vertical:8}}}}),R=u([{name:"TEAM A",type:"column",data:[23,11,22,27,13,22,37,21,44,22,30]},{name:"TEAM B",type:"area",data:[44,55,41,67,22,43,21,41,56,27,43]},{name:"TEAM C",type:"line",data:[30,25,36,30,45,35,64,52,59,36,39]}]),j=c(()=>{const r=!!(s.theme==="dark"||s.isDarkMode);return{chart:{height:300,type:"radar",zoom:{enabled:!1},toolbar:{show:!1}},colors:["#194C8D"],xaxis:{categories:["January","February","March","April","May","June"]},plotOptions:{radar:{polygons:{strokeColors:r?"#191e3a":"#e0e6ed",connectorColors:r?"#191e3a":"#e0e6ed"}}},tooltip:{theme:r?"dark":"light"}}}),O=u([{name:"Series 1",data:[80,50,30,40,100,20]}]),J=c(()=>({chart:{height:300,type:"pie",zoom:{enabled:!1},toolbar:{show:!1}},labels:["Team A","Team B","Team C","Team D","Team E"],colors:["#194C8D","#805dca","#00ab55","#e7515a","#e2a03f"],responsive:[{breakpoint:480,options:{chart:{width:200}}}],stroke:{show:!1},legend:{position:"bottom"}})),F=u([44,55,13,43,22]),G=c(()=>({chart:{height:300,type:"donut",zoom:{enabled:!1},toolbar:{show:!1}},stroke:{show:!1},labels:["Team A","Team B","Team C"],colors:["#194C8D","#805dca","#e2a03f"],responsive:[{breakpoint:480,options:{chart:{width:200}}}],legend:{position:"bottom"}})),$=u([44,55,13]),X=c(()=>{const r=!!(s.theme==="dark"||s.isDarkMode);return{chart:{height:300,type:"polarArea",zoom:{enabled:!1},toolbar:{show:!1}},colors:["#194C8D","#805dca","#00ab55","#e7515a","#e2a03f","#2196f3","#3b3f5c"],stroke:{show:!1},responsive:[{breakpoint:480,options:{chart:{width:200}}}],plotOptions:{polarArea:{rings:{strokeColor:r?"#191e3a":"#e0e6ed"},spokes:{connectorColors:r?"#191e3a":"#e0e6ed"}}},legend:{position:"bottom"},fill:{opacity:.85}}}),P=u([14,23,21,17,15,10,12,17,21]),E=c(()=>{const r=!!(s.theme==="dark"||s.isDarkMode);return{chart:{height:300,type:"radialBar",zoom:{enabled:!1},toolbar:{show:!1}},colors:["#194C8D","#805dca","#e2a03f"],grid:{borderColor:r?"#191e3a":"#e0e6ed"},plotOptions:{radialBar:{dataLabels:{name:{fontSize:"22px"},value:{fontSize:"16px"},total:{show:!0,label:"Total",formatter:function(e){return 249}}}}},labels:["Apples","Oranges","Bananas"],fill:{opacity:.85}}}),N=u([44,55,41]),V=c(()=>{const r=!!(s.theme==="dark"||s.isDarkMode),e=s.rtlClass==="rtl";return{chart:{height:300,type:"bubble",zoom:{enabled:!1},toolbar:{show:!1}},colors:["#194C8D","#00ab55"],dataLabels:{enabled:!1},xaxis:{tickAmount:12,type:"category",axisBorder:{color:r?"#191e3a":"#e0e6ed"}},yaxis:{max:70,opposite:!!e,labels:{offsetX:e?-10:0}},grid:{borderColor:r?"#191e3a":"#e0e6ed"},tooltip:{theme:r?"dark":"light"},stroke:{colors:r?["#191e3a"]:["#e0e6ed"]},fill:{opacity:.85}}}),W=u([{name:"Bubble1",data:g(new Date("11 Feb 2017 GMT").getTime(),20,{min:10,max:60})},{name:"Bubble2",data:g(new Date("11 Feb 2017 GMT").getTime(),20,{min:10,max:60})}]);return(r,e)=>(l(),Q("div",null,[e[49]||(e[49]=t("ul",{class:"flex space-x-2 rtl:space-x-reverse mb-6"},[t("li",null,[t("a",{href:"javascript:;",class:"text-primary hover:underline"},"Dashboard")]),t("li",{class:"before:content-['/'] ltr:before:mr-2 rtl:before:ml-2"},[t("span",null,"Charts")])],-1)),t("div",_,[e[48]||(e[48]=i('<div class="panel lg:col-span-2 p-3 flex items-center text-primary overflow-x-auto whitespace-nowrap"><div class="ring-2 ring-primary/30 rounded-full bg-primary text-white p-1.5 ltr:mr-3 rtl:ml-3"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5"><path d="M19.0001 9.7041V9C19.0001 5.13401 15.8661 2 12.0001 2C8.13407 2 5.00006 5.13401 5.00006 9V9.7041C5.00006 10.5491 4.74995 11.3752 4.28123 12.0783L3.13263 13.8012C2.08349 15.3749 2.88442 17.5139 4.70913 18.0116C9.48258 19.3134 14.5175 19.3134 19.291 18.0116C21.1157 17.5139 21.9166 15.3749 20.8675 13.8012L19.7189 12.0783C19.2502 11.3752 19.0001 10.5491 19.0001 9.7041Z" stroke="currentColor" stroke-width="1.5"></path><path opacity="0.5" d="M7.5 19C8.15503 20.7478 9.92246 22 12 22C14.0775 22 15.845 20.7478 16.5 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg></div><span class="ltr:mr-3 rtl:ml-3">Documentation: </span><a href="https://www.npmjs.com/package/vue3-apexcharts" target="_blank" class="block hover:underline">https://www.npmjs.com/package/vue3-apexcharts</a></div>',1)),t("div",ee,[t("div",te,[e[13]||(e[13]=t("h5",{class:"font-semibold text-lg dark:text-white-light"},"Simple Line",-1)),t("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:e[0]||(e[0]=a=>o(d)("code1"))},[...e[12]||(e[12]=[i('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),t("div",re,[k(o(h),{height:"300",options:C.value,series:v.value,class:"bg-white dark:bg-black rounded-lg overflow-hidden"},null,8,["options","series"])]),o(n).includes("code1")?(l(),m(p,{key:0},{default:b(()=>[...e[14]||(e[14]=[t("pre",null,`<!-- simple line -->
<apexchart height="300" :options="lineChart" :series="lineChartSeries" class="bg-white dark:bg-black rounded-lg overflow-hidden"></apexchart>

<!-- script -->
<script lang="ts" setup>
import { ref, computed } from 'vue';
import apexchart from 'vue3-apexcharts';
import { useAppStore } from '@/stores/index';
const store = useAppStore();

const lineChart = computed(() => {
  const isDark = store.theme === 'dark' || store.isDarkMode ? true : false;
  const isRtl = store.rtlClass === 'rtl' ? true : false;
  return {
    chart: {
      height: 300,
      type: 'line',
      toolbar: false,
    },
    colors: ['#194C8D'],
    tooltip: {
      marker: false,
      y: {
        formatter(number) {
          return '$' + number;
        },
      },
      theme: isDark ? 'dark' : 'light',
    },
    stroke: {
      width: 2,
      curve: 'smooth',
    },
    xaxis: {
      categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'June'],
      axisBorder: {
        color: isDark ? '#191e3a' : '#e0e6ed',
      },
    },
    yaxis: {
      opposite: isRtl ? true : false,
      labels: {
        offsetX: isRtl ? -20 : 0,
      },
    },
    grid: {
      borderColor: isDark ? '#191e3a' : '#e0e6ed',
    },
  };
});

const lineChartSeries = ref([
  {
    name: 'Sales',
    data: [45, 55, 75, 25, 45, 110],
  },
]);
<\/script>
`,-1)])]),_:1})):f("",!0)]),t("div",oe,[t("div",se,[e[16]||(e[16]=t("h5",{class:"font-semibold text-lg dark:text-white"},"Simple Area",-1)),t("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:e[1]||(e[1]=a=>o(d)("code2"))},[...e[15]||(e[15]=[i('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),t("div",ae,[k(o(h),{height:"300",options:y.value,series:M.value,class:"bg-white dark:bg-black rounded-lg overflow-hidden"},null,8,["options","series"])]),o(n).includes("code2")?(l(),m(p,{key:0},{default:b(()=>[...e[17]||(e[17]=[t("pre",null,`<!-- simple area -->
<apexchart height="300" :options="areaChart" :series="areaChartSeries" class="bg-white dark:bg-black rounded-lg overflow-hidden"></apexchart>

<!-- script -->
<script lang="ts" setup>
import { ref, computed } from 'vue';
import apexchart from 'vue3-apexcharts';
import { useAppStore } from '@/stores/index';
const store = useAppStore();

const areaChart = computed(() => {
  const isDark = store.theme === 'dark' || store.isDarkMode ? true : false;
  const isRtl = store.rtlClass === 'rtl' ? true : false;
  return {
    chart: {
      type: 'area',
      height: 300,
      zoom: {
        enabled: false,
      },
      toolbar: {
        show: false,
      },
    },
    colors: ['#805dca'],
    dataLabels: {
      enabled: false,
    },
    stroke: {
      width: 2,
      curve: 'smooth',
    },
    xaxis: {
      axisBorder: {
        color: isDark ? '#191e3a' : '#e0e6ed',
      },
    },
    yaxis: {
      opposite: isRtl ? true : false,
      labels: {
        offsetX: isRtl ? -40 : 0,
      },
    },
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    legend: {
      horizontalAlign: 'left',
    },
    grid: {
      borderColor: isDark ? '#191e3a' : '#e0e6ed',
    },
    tooltip: {
      theme: isDark ? 'dark' : 'light',
    },
  };
});

const areaChartSeries = ref([
  {
    name: 'Income',
    data: [16800, 16800, 15500, 17800, 15500, 17000, 19000, 16000, 15000, 17000, 14000, 17000],
  },
]);
<\/script>
`,-1)])]),_:1})):f("",!0)]),t("div",le,[t("div",ie,[e[19]||(e[19]=t("h5",{class:"font-semibold text-lg dark:text-white"},"Simple Column",-1)),t("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:e[2]||(e[2]=a=>o(d)("code3"))},[...e[18]||(e[18]=[i('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),t("div",ne,[k(o(h),{height:"300",options:D.value,series:S.value,class:"bg-white dark:bg-black rounded-lg overflow-hidden"},null,8,["options","series"])]),o(n).includes("code3")?(l(),m(p,{key:0},{default:b(()=>[...e[20]||(e[20]=[t("pre",null,`<!-- simple column -->
<apexchart height="300" :options="columnChart" :series="columnChartSeries" class="bg-white dark:bg-black rounded-lg overflow-hidden"></apexchart>

<!-- script -->
<script lang="ts" setup>
import { ref, computed } from 'vue';
import apexchart from 'vue3-apexcharts';
import { useAppStore } from '@/stores/index';
const store = useAppStore();

const columnChart = computed(() => {
  const isDark = store.theme === 'dark' || store.isDarkMode ? true : false;
  const isRtl = store.rtlClass === 'rtl' ? true : false;
  return {
    chart: {
      height: 300,
      type: 'bar',
      zoom: {
        enabled: false,
      },
      toolbar: {
        show: false,
      },
    },
    colors: ['#805dca', '#e7515a'],
    dataLabels: {
      enabled: false,
    },
    stroke: {
      show: true,
      width: 2,
      colors: ['transparent'],
    },
    plotOptions: {
      bar: {
        horizontal: false,
        columnWidth: '55%',
        endingShape: 'rounded',
      },
    },
    grid: {
      borderColor: isDark ? '#191e3a' : '#e0e6ed',
    },
    xaxis: {
      categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
      axisBorder: {
        color: isDark ? '#191e3a' : '#e0e6ed',
      },
    },
    yaxis: {
      opposite: isRtl ? true : false,
      labels: {
        offsetX: isRtl ? -10 : 0,
      },
    },
    tooltip: {
      theme: isDark ? 'dark' : 'light',
      y: {
        formatter: function (val) {
          return val;
        },
      },
    },
  };
});

const columnChartSeries = ref([
  {
    name: 'Net Profit',
    data: [44, 55, 57, 56, 61, 58, 63, 60, 66],
  },
  {
    name: 'Revenue',
    data: [76, 85, 101, 98, 87, 105, 91, 114, 94],
  },
]);
<\/script>
`,-1)])]),_:1})):f("",!0)]),t("div",de,[t("div",pe,[e[22]||(e[22]=t("h5",{class:"font-semibold text-lg dark:text-white"},"Simple Column Stacked",-1)),t("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:e[3]||(e[3]=a=>o(d)("code4"))},[...e[21]||(e[21]=[i('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),t("div",he,[k(o(h),{height:"300",options:L.value,series:A.value,class:"bg-white dark:bg-black rounded-lg overflow-hidden"},null,8,["options","series"])]),o(n).includes("code4")?(l(),m(p,{key:0},{default:b(()=>[...e[23]||(e[23]=[t("pre",null,`<!-- simple column stacked -->
<apexchart height="300" :options="simpleColumnStacked" :series="simpleColumnStackedSeries" class="bg-white dark:bg-black rounded-lg overflow-hidden"></apexchart>

<!-- script -->
<script lang="ts" setup>
import { ref, computed } from 'vue';
import apexchart from 'vue3-apexcharts';
import { useAppStore } from '@/stores/index';
const store = useAppStore();

const simpleColumnStacked = computed(() => {
  const isDark = store.theme === 'dark' || store.isDarkMode ? true : false;
  const isRtl = store.rtlClass === 'rtl' ? true : false;
  return {
    chart: {
      height: 300,
      type: 'bar',
      stacked: true,
      zoom: {
        enabled: false,
      },
      toolbar: {
        show: false,
      },
    },
    colors: ['#2196f3', '#3b3f5c'],
    responsive: [
      {
        breakpoint: 480,
        options: {
          legend: {
            position: 'bottom',
            offsetX: -10,
            offsetY: 5,
          },
        },
      },
    ],
    plotOptions: {
      bar: {
        horizontal: false,
      },
    },
    xaxis: {
      type: 'datetime',
      categories: ['01/01/2011 GMT', '01/02/2011 GMT', '01/03/2011 GMT', '01/04/2011 GMT', '01/05/2011 GMT', '01/06/2011 GMT'],
      axisBorder: {
        color: isDark ? '#191e3a' : '#e0e6ed',
      },
    },
    yaxis: {
      opposite: isRtl ? true : false,
      labels: {
        offsetX: isRtl ? -20 : 0,
      },
    },
    grid: {
      borderColor: isDark ? '#191e3a' : '#e0e6ed',
    },
    legend: {
      position: 'right',
      offsetY: 40,
    },
    tooltip: {
      theme: isDark ? 'dark' : 'light',
    },
    fill: {
      opacity: 0.8,
    },
  };
});

const simpleColumnStackedSeries = ref([
  {
    name: 'PRODUCT A',
    data: [44, 55, 41, 67, 22, 43],
  },
  {
    name: 'PRODUCT B',
    data: [13, 23, 20, 8, 13, 27],
  },
]);
<\/script>
`,-1)])]),_:1})):f("",!0)]),t("div",ce,[t("div",ue,[e[25]||(e[25]=t("h5",{class:"font-semibold text-lg dark:text-white"},"Simple Bar",-1)),t("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:e[4]||(e[4]=a=>o(d)("code5"))},[...e[24]||(e[24]=[i('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),t("div",me,[k(o(h),{height:"300",options:B.value,series:T.value,class:"bg-white dark:bg-black rounded-lg overflow-hidden"},null,8,["options","series"])]),o(n).includes("code5")?(l(),m(p,{key:0},{default:b(()=>[...e[26]||(e[26]=[t("pre",null,`<!-- simple bar -->
<apexchart height="300" :options="barChart" :series="barChartSeries" class="bg-white dark:bg-black rounded-lg overflow-hidden"></apexchart>

<!-- script -->
<script lang="ts" setup>
import { ref, computed } from 'vue';
import apexchart from 'vue3-apexcharts';
import { useAppStore } from '@/stores/index';
const store = useAppStore();

const barChart = computed(() => {
  const isDark = store.theme === 'dark' || store.isDarkMode ? true : false;
  const isRtl = store.rtlClass === 'rtl' ? true : false;
  return {
    chart: {
      height: 300,
      type: 'bar',
      zoom: {
        enabled: false,
      },
      toolbar: {
        show: false,
      },
    },
    dataLabels: {
      enabled: false,
    },
    stroke: {
      show: true,
      width: 1,
    },
    colors: ['#194C8D'],
    xaxis: {
      categories: ['Sun', 'Mon', 'Tue', 'Wed', 'Thur', 'Fri', 'Sat'],
      axisBorder: {
        color: isDark ? '#191e3a' : '#e0e6ed',
      },
    },
    yaxis: {
      opposite: isRtl ? true : false,
      reversed: isRtl ? true : false,
    },
    grid: {
      borderColor: isDark ? '#191e3a' : '#e0e6ed',
    },
    plotOptions: {
      bar: {
        horizontal: true,
      },
    },
    fill: {
      opacity: 0.8,
    },
  };
});

const barChartSeries = ref([
  {
    name: 'Sales',
    data: [44, 55, 41, 67, 22, 43, 21, 70],
  },
]);
<\/script>
`,-1)])]),_:1})):f("",!0)]),t("div",fe,[t("div",ke,[e[28]||(e[28]=t("h5",{class:"font-semibold text-lg dark:text-white"},"Mixed",-1)),t("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:e[5]||(e[5]=a=>o(d)("code6"))},[...e[27]||(e[27]=[i('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),t("div",be,[k(o(h),{height:"300",options:z.value,series:R.value,class:"bg-white dark:bg-black rounded-lg overflow-hidden"},null,8,["options","series"])]),o(n).includes("code6")?(l(),m(p,{key:0},{default:b(()=>[...e[29]||(e[29]=[t("pre",null,`<!-- mixed -->
<apexchart height="300" :options="mixedChart" :series="mixedChartSeries" class="bg-white dark:bg-black rounded-lg overflow-hidden"></apexchart>

<!-- script -->
<script lang="ts" setup>
import { ref, computed } from 'vue';
import apexchart from 'vue3-apexcharts';
import { useAppStore } from '@/stores/index';
const store = useAppStore();

const mixedChart = computed(() => {
  const isDark = store.theme === 'dark' || store.isDarkMode ? true : false;
  const isRtl = store.rtlClass === 'rtl' ? true : false;
  return {
    chart: {
      height: 300,
      // stacked: false,
      zoom: {
        enabled: false,
      },
      toolbar: {
        show: false,
      },
    },
    colors: ['#2196f3', '#00ab55', '#194C8D'],
    stroke: {
      width: [0, 2, 2],
      curve: 'smooth',
    },
    plotOptions: {
      bar: {
        columnWidth: '50%',
      },
    },
    fill: {
      opacity: [1, 0.25, 1],
    },

    labels: ['01/01/2022', '02/01/2022', '03/01/2022', '04/01/2022', '05/01/2022', '06/01/2022', '07/01/2022', '08/01/2022', '09/01/2022', '10/01/2022', '11/01/2022'],
    markers: {
      size: 0,
    },
    xaxis: {
      type: 'datetime',
      axisBorder: {
        color: isDark ? '#191e3a' : '#e0e6ed',
      },
    },
    yaxis: {
      title: {
        text: 'Points',
      },
      min: 0,
      opposite: isRtl ? true : false,
      labels: {
        offsetX: isRtl ? -10 : 0,
      },
    },
    grid: {
      borderColor: isDark ? '#191e3a' : '#e0e6ed',
    },
    tooltip: {
      shared: true,
      intersect: false,
      theme: isDark ? 'dark' : 'light',
      y: {
        formatter: (y) => {
          if (typeof y !== 'undefined') {
            return y.toFixed(0) + ' points';
          }
          return y;
        },
      },
    },
    legend: {
      itemMargin: {
        horizontal: 4,
        vertical: 8,
      },
    },
  };
});

const mixedChartSeries = ref([
  {
    name: 'TEAM A',
    type: 'column',
    data: [23, 11, 22, 27, 13, 22, 37, 21, 44, 22, 30],
  },
  {
    name: 'TEAM B',
    type: 'area',
    data: [44, 55, 41, 67, 22, 43, 21, 41, 56, 27, 43],
  },
  {
    name: 'TEAM C',
    type: 'line',
    data: [30, 25, 36, 30, 45, 35, 64, 52, 59, 36, 39],
  },
]);
<\/script>
`,-1)])]),_:1})):f("",!0)]),t("div",ge,[t("div",we,[e[31]||(e[31]=t("h5",{class:"font-semibold text-lg dark:text-white"},"Basic Radar",-1)),t("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:e[6]||(e[6]=a=>o(d)("code7"))},[...e[30]||(e[30]=[i('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),t("div",xe,[k(o(h),{height:"300",options:j.value,series:O.value,class:"bg-white dark:bg-black rounded-lg overflow-hidden"},null,8,["options","series"])]),o(n).includes("code7")?(l(),m(p,{key:0},{default:b(()=>[...e[32]||(e[32]=[t("pre",null,`<!-- basic radar -->
<apexchart height="300" :options="radarChart" :series="radarChartSeries" class="bg-white dark:bg-black rounded-lg overflow-hidden"></apexchart>

<!-- script -->
<script lang="ts" setup>
import { ref, computed } from 'vue';
import apexchart from 'vue3-apexcharts';
import { useAppStore } from '@/stores/index';
const store = useAppStore();

const radarChart = computed(() => {
  const isDark = store.theme === 'dark' || store.isDarkMode ? true : false;
  return {
    chart: {
      height: 300,
      type: 'radar',
      zoom: {
        enabled: false,
      },
      toolbar: {
        show: false,
      },
    },
    colors: ['#194C8D'],
    xaxis: {
      categories: ['January', 'February', 'March', 'April', 'May', 'June'],
    },
    plotOptions: {
      radar: {
        polygons: {
          strokeColors: isDark ? '#191e3a' : '#e0e6ed',
          connectorColors: isDark ? '#191e3a' : '#e0e6ed',
        },
      },
    },
    tooltip: {
      theme: isDark ? 'dark' : 'light',
    },
  };
});

const radarChartSeries = ref([
  {
    name: 'Series 1',
    data: [80, 50, 30, 40, 100, 20],
  },
]);
<\/script>
`,-1)])]),_:1})):f("",!0)]),t("div",Ce,[t("div",ve,[e[34]||(e[34]=t("h5",{class:"font-semibold text-lg dark:text-white"},"Basic Pie",-1)),t("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:e[7]||(e[7]=a=>o(d)("code8"))},[...e[33]||(e[33]=[i('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),t("div",ye,[k(o(h),{height:"300",options:J.value,series:F.value,class:"bg-white dark:bg-black rounded-lg overflow-hidden"},null,8,["options","series"])]),o(n).includes("code8")?(l(),m(p,{key:0},{default:b(()=>[...e[35]||(e[35]=[t("pre",null,`<!-- basic pie -->
<apexchart height="300" :options="pieChart" :series="pieChartSeries" class="bg-white dark:bg-black rounded-lg overflow-hidden"></apexchart>

<!-- script -->
<script lang="ts" setup>
import { ref, computed } from 'vue';
import apexchart from 'vue3-apexcharts';
import { useAppStore } from '@/stores/index';
const store = useAppStore();

const pieChart = computed(() => {
  return {
    chart: {
      height: 300,
      type: 'pie',
      zoom: {
        enabled: false,
      },
      toolbar: {
        show: false,
      },
    },
    labels: ['Team A', 'Team B', 'Team C', 'Team D', 'Team E'],
    colors: ['#194C8D', '#805dca', '#00ab55', '#e7515a', '#e2a03f'],
    responsive: [
      {
        breakpoint: 480,
        options: {
          chart: {
            width: 200,
          },
        },
      },
    ],
    stroke: {
      show: false,
    },
    legend: {
      position: 'bottom',
    },
  };
});

const pieChartSeries = ref([44, 55, 13, 43, 22]);
<\/script>
`,-1)])]),_:1})):f("",!0)]),t("div",Me,[t("div",De,[e[37]||(e[37]=t("h5",{class:"font-semibold text-lg dark:text-white"},"Basic Donut",-1)),t("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:e[8]||(e[8]=a=>o(d)("code9"))},[...e[36]||(e[36]=[i('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),t("div",Se,[k(o(h),{height:"300",options:G.value,series:$.value,class:"bg-white dark:bg-black rounded-lg overflow-hidden"},null,8,["options","series"])]),o(n).includes("code9")?(l(),m(p,{key:0},{default:b(()=>[...e[38]||(e[38]=[t("pre",null,`<!-- basic donut -->
<apexchart height="300" :options="donutChart" :series="donutChartSeries" class="bg-white dark:bg-black rounded-lg overflow-hidden"></apexchart>

<!-- script -->
<script lang="ts" setup>
import { ref, computed } from 'vue';
import apexchart from 'vue3-apexcharts';
import { useAppStore } from '@/stores/index';
const store = useAppStore();

const donutChart = computed(() => {
  return {
    chart: {
      height: 300,
      type: 'donut',
      zoom: {
        enabled: false,
      },
      toolbar: {
        show: false,
      },
    },
    stroke: {
      show: false,
    },
    labels: ['Team A', 'Team B', 'Team C'],
    colors: ['#194C8D', '#805dca', '#e2a03f'],
    responsive: [
      {
        breakpoint: 480,
        options: {
          chart: {
            width: 200,
          },
        },
      },
    ],
    legend: {
      position: 'bottom',
    },
  };
});

const donutChartSeries = ref([44, 55, 13]);
<\/script>
`,-1)])]),_:1})):f("",!0)]),t("div",Le,[t("div",Ae,[e[40]||(e[40]=t("h5",{class:"font-semibold text-lg dark:text-white"},"Basic Polar Area",-1)),t("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:e[9]||(e[9]=a=>o(d)("code10"))},[...e[39]||(e[39]=[i('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),t("div",Be,[k(o(h),{height:"300",options:X.value,series:P.value,class:"bg-white dark:bg-black rounded-lg overflow-hidden"},null,8,["options","series"])]),o(n).includes("code10")?(l(),m(p,{key:0},{default:b(()=>[...e[41]||(e[41]=[t("pre",null,`<!-- basic polar area -->
<apexchart height="300" :options="polarAreaChart" :series="polarAreaChartSeries" class="bg-white dark:bg-black rounded-lg overflow-hidden"></apexchart>

<!-- script -->
<script lang="ts" setup>
import { ref, computed } from 'vue';
import apexchart from 'vue3-apexcharts';
import { useAppStore } from '@/stores/index';
const store = useAppStore();

const polarAreaChart = computed(() => {
  const isDark = store.theme === 'dark' || store.isDarkMode ? true : false;
  return {
    chart: {
      height: 300,
      type: 'polarArea',
      zoom: {
        enabled: false,
      },
      toolbar: {
        show: false,
      },
    },
    colors: ['#194C8D', '#805dca', '#00ab55', '#e7515a', '#e2a03f', '#2196f3', '#3b3f5c'],
    stroke: {
      show: false,
    },
    responsive: [
      {
        breakpoint: 480,
        options: {
          chart: {
            width: 200,
          },
        },
      },
    ],
    plotOptions: {
      polarArea: {
        rings: {
          strokeColor: isDark ? '#191e3a' : '#e0e6ed',
        },
        spokes: {
          connectorColors: isDark ? '#191e3a' : '#e0e6ed',
        },
      },
    },
    legend: {
      position: 'bottom',
    },
    fill: {
      opacity: 0.85,
    },
  };
});

const polarAreaChartSeries = ref([14, 23, 21, 17, 15, 10, 12, 17, 21]);
<\/script>
`,-1)])]),_:1})):f("",!0)]),t("div",Te,[t("div",ze,[e[43]||(e[43]=t("h5",{class:"font-semibold text-lg dark:text-white"},"Radial Bar",-1)),t("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:e[10]||(e[10]=a=>o(d)("code11"))},[...e[42]||(e[42]=[i('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),t("div",Re,[k(o(h),{height:"300",options:E.value,series:N.value,class:"bg-white dark:bg-black rounded-lg overflow-hidden"},null,8,["options","series"])]),o(n).includes("code11")?(l(),m(p,{key:0},{default:b(()=>[...e[44]||(e[44]=[t("pre",null,`<!-- radial bar -->
<apexchart height="300" :options="radialBarChart" :series="radialBarChartSeries" class="bg-white dark:bg-black rounded-lg overflow-hidden"></apexchart>

<!-- script -->
<script lang="ts" setup>
import { ref, computed } from 'vue';
import apexchart from 'vue3-apexcharts';
import { useAppStore } from '@/stores/index';
const store = useAppStore();

const radialBarChart = computed(() => {
  const isDark = store.theme === 'dark' || store.isDarkMode ? true : false;
  return {
    chart: {
      height: 300,
      type: 'radialBar',
      zoom: {
        enabled: false,
      },
      toolbar: {
        show: false,
      },
    },
    colors: ['#194C8D', '#805dca', '#e2a03f'],
    grid: {
      borderColor: isDark ? '#191e3a' : '#e0e6ed',
    },
    plotOptions: {
      radialBar: {
        dataLabels: {
          name: {
            fontSize: '22px',
          },
          value: {
            fontSize: '16px',
          },
          total: {
            show: true,
            label: 'Total',
            formatter: function (w) {
              // By default this function returns the average of all series. The below is just an example to show the use of custom formatter function
              return 249;
            },
          },
        },
      },
    },
    labels: ['Apples', 'Oranges', 'Bananas'],
    fill: {
      opacity: 0.85,
    },
  };
});

const radialBarChartSeries = ref([44, 55, 41]);
<\/script>
`,-1)])]),_:1})):f("",!0)]),t("div",je,[t("div",Oe,[e[46]||(e[46]=t("h5",{class:"font-semibold text-lg dark:text-white"},"Bubble Chart",-1)),t("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:e[11]||(e[11]=a=>o(d)("code12"))},[...e[45]||(e[45]=[i('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),t("div",Je,[k(o(h),{height:"300",options:V.value,series:W.value,class:"bg-white dark:bg-black rounded-lg overflow-hidden"},null,8,["options","series"])]),o(n).includes("code12")?(l(),m(p,{key:0},{default:b(()=>[...e[47]||(e[47]=[t("pre",null,`<!-- bubble chart -->
<apexchart height="300" :options="bubbleChart" :series="bubbleChartSeries" class="bg-white dark:bg-black rounded-lg overflow-hidden"></apexchart>

<!-- script -->
<script lang="ts" setup>
import { ref, computed } from 'vue';
import apexchart from 'vue3-apexcharts';
import { useAppStore } from '@/stores/index';
const store = useAppStore();

const generateData = (baseval, count, yrange) => {
  var i = 0;
  var series: any = [];
  while (i < count) {
    var x = Math.floor(Math.random() * (750 - 1 + 1)) + 1;
    var y = Math.floor(Math.random() * (yrange.max - yrange.min + 1)) + yrange.min;
    var z = Math.floor(Math.random() * (75 - 15 + 1)) + 15;

    series.push([x, y, z]);
    baseval += 86400000;
    i++;
  }
  return series;
};

const bubbleChart = computed(() => {
  const isDark = store.theme === 'dark' || store.isDarkMode ? true : false;
  const isRtl = store.rtlClass === 'rtl' ? true : false;
  return {
    chart: {
      height: 300,
      type: 'bubble',
      zoom: {
        enabled: false,
      },
      toolbar: {
        show: false,
      },
    },
    colors: ['#194C8D', '#00ab55'],
    dataLabels: {
      enabled: false,
    },
    xaxis: {
      tickAmount: 12,
      type: 'category',
      axisBorder: {
        color: isDark ? '#191e3a' : '#e0e6ed',
      },
    },
    yaxis: {
      max: 70,
      opposite: isRtl ? true : false,
      labels: {
        offsetX: isRtl ? -10 : 0,
      },
    },
    grid: {
      borderColor: isDark ? '#191e3a' : '#e0e6ed',
    },
    tooltip: {
      theme: isDark ? 'dark' : 'light',
    },
    stroke: {
      colors: isDark ? ['#191e3a'] : ['#e0e6ed'],
    },
    fill: {
      opacity: 0.85,
    },
  };
});

const bubbleChartSeries = ref([
  {
    name: 'Bubble1',
    data: generateData(new Date('11 Feb 2017 GMT').getTime(), 20, {
      min: 10,
      max: 60,
    }),
  },
  {
    name: 'Bubble2',
    data: generateData(new Date('11 Feb 2017 GMT').getTime(), 20, {
      min: 10,
      max: 60,
    }),
  },
]);
<\/script>
`,-1)])]),_:1})):f("",!0)])])]))}});export{Ee as default};
