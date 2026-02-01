import{c as L,_ as u}from"./codePreview-B8fh2aNm.js";import{M as p}from"./vue3-multiselect-DU6vhVa9.js";import{u as M}from"./main-B02PUh7k.js";import{d as V,r as o,k as O,l as r,m as t,C as a,I as d,x as c,u as l,f as m,q as v}from"./vendor-CjqaLWPm.js";import"./quill-De1S0mUZ.js";const B={class:"pt-5 space-y-8"},j={class:"grid grid-cols-1 lg:grid-cols-2 gap-6"},P={class:"panel"},S={class:"flex items-center justify-between mb-5"},W={class:"mb-5"},$={class:"panel"},G={class:"flex items-center justify-between mb-5"},U={class:"mb-5"},N={class:"panel"},D={class:"flex items-center justify-between mb-5"},q={class:"mb-5"},A={class:"panel lg:row-start-3"},R={class:"flex items-center justify-between mb-5"},Y={class:"mb-5"},_={class:"panel"},E={class:"flex items-center justify-between mb-5"},F={class:"mb-5"},I={class:"panel"},Z={class:"flex items-center justify-between mb-5"},z={class:"mb-5"},te=V({__name:"select2",setup(H){M({title:"Vue Multiselect"});const{codeArr:i,toggleCode:n}=L(),h=o(["Orange","White","Purple"]),f=o("Orange"),x=o([{group_name:"Group 1",list:[{name:"Orange"},{name:"White"},{name:"Purple"}]},{group_name:"Group 2",list:[{name:"Yellow"},{name:"Red"},{name:"Green"}]},{group_name:"Group 3",list:[{name:"Aqua"},{name:"Black"},{name:"Blue"}]}]),b=o({name:"Orange"}),y=o([{name:"Orange"},{name:"White",$isDisabled:!0},{name:"Purple"}]),g=o({name:"Orange"}),k=o(""),w=o(""),C=o([]);return(J,e)=>(r(),O("div",null,[e[31]||(e[31]=t("ul",{class:"flex space-x-2 rtl:space-x-reverse"},[t("li",null,[t("a",{href:"javascript:;",class:"text-primary hover:underline"},"Forms")]),t("li",{class:"before:content-['/'] ltr:before:mr-2 rtl:before:ml-2"},[t("span",null,"Select2")])],-1)),t("div",B,[e[30]||(e[30]=a('<div class="panel p-3 flex items-center text-primary overflow-x-auto whitespace-nowrap"><div class="ring-2 ring-primary/30 rounded-full bg-primary text-white p-1.5 ltr:mr-3 rtl:ml-3"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5"><path d="M19.0001 9.7041V9C19.0001 5.13401 15.8661 2 12.0001 2C8.13407 2 5.00006 5.13401 5.00006 9V9.7041C5.00006 10.5491 4.74995 11.3752 4.28123 12.0783L3.13263 13.8012C2.08349 15.3749 2.88442 17.5139 4.70913 18.0116C9.48258 19.3134 14.5175 19.3134 19.291 18.0116C21.1157 17.5139 21.9166 15.3749 20.8675 13.8012L19.7189 12.0783C19.2502 11.3752 19.0001 10.5491 19.0001 9.7041Z" stroke="currentColor" stroke-width="1.5"></path><path opacity="0.5" d="M7.5 19C8.15503 20.7478 9.92246 22 12 22C14.0775 22 15.845 20.7478 16.5 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg></div><span class="ltr:mr-3 rtl:ml-3">Documentation: </span><a href="https://www.npmjs.com/package/@suadelabs/vue3-multiselect" target="_blank" class="block hover:underline">https://www.npmjs.com/package/@suadelabs/vue3-multiselect</a></div>',1)),t("div",j,[t("div",P,[t("div",S,[e[13]||(e[13]=t("h5",{class:"font-semibold text-lg dark:text-white-light"},"Basic",-1)),t("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:e[0]||(e[0]=s=>l(n)("code1"))},[...e[12]||(e[12]=[a('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),t("div",W,[m(l(p),{modelValue:f.value,"onUpdate:modelValue":e[1]||(e[1]=s=>f.value=s),options:h.value,class:"custom-multiselect",searchable:!1,"preselect-first":!0,"allow-empty":!1,"selected-label":"","select-label":"","deselect-label":""},null,8,["modelValue","options"])]),l(i).includes("code1")?(r(),d(u,{key:0},{default:v(()=>[...e[14]||(e[14]=[t("pre",null,`<!-- basic -->
<multiselect
  v-model="input1"
  :options="options"
  class="custom-multiselect"
  :searchable="false"
  :preselect-first="true"
  :allow-empty="false"
  selected-label=""
  select-label=""
  deselect-label=""
></multiselect>

<!-- script -->
<script lang="ts" setup>
  import { ref } from 'vue';
  import Multiselect from '@suadelabs/vue3-multiselect';
  import '@suadelabs/vue3-multiselect/dist/vue3-multiselect.css';

  const options = ref(['Orange', 'White', 'Purple']);
  const input1 = ref('Orange');
<\/script>
`,-1)])]),_:1})):c("",!0)]),t("div",$,[t("div",G,[e[16]||(e[16]=t("h5",{class:"font-semibold text-lg dark:text-white-light"},"Nested",-1)),t("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:e[2]||(e[2]=s=>l(n)("code2"))},[...e[15]||(e[15]=[a('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),t("div",U,[m(l(p),{modelValue:b.value,"onUpdate:modelValue":e[3]||(e[3]=s=>b.value=s),options:x.value,class:"custom-multiselect",searchable:!1,"allow-empty":!1,"group-values":"list","group-label":"group_name",placeholder:"Select Option","track-by":"name",label:"name","preselect-first":!0,"selected-label":"","select-label":"","deselect-label":""},null,8,["modelValue","options"])]),l(i).includes("code2")?(r(),d(u,{key:0},{default:v(()=>[...e[17]||(e[17]=[t("pre",null,`<!-- nested -->
<multiselect
  v-model="input2"
  :options="options2"
  class="custom-multiselect"
  :searchable="false"
  :allow-empty="false"
  group-values="list"
  group-label="group_name"
  placeholder="Select Option"
  track-by="name"
  label="name"
  :preselect-first="true"
  selected-label=""
  select-label=""
  deselect-label=""
></multiselect>

<!-- script -->
<script lang="ts" setup>
  import { ref } from 'vue';
  import Multiselect from '@suadelabs/vue3-multiselect';
  import '@suadelabs/vue3-multiselect/dist/vue3-multiselect.css';

  const options2 = ref([
    {
      group_name: 'Group 1',
      list: [{ name: 'Orange' }, { name: 'White' }, { name: 'Purple' }],
    },
    {
      group_name: 'Group 2',
      list: [{ name: 'Yellow' }, { name: 'Red' }, { name: 'Green' }],
    },
    {
      group_name: 'Group 3',
      list: [{ name: 'Aqua' }, { name: 'Black' }, { name: 'Blue' }],
    },
  ]);
  const input2 = ref({ name: 'Orange' });
<\/script>
`,-1)])]),_:1})):c("",!0)]),t("div",N,[t("div",D,[e[19]||(e[19]=t("h5",{class:"font-semibold text-lg dark:text-white-light"},"Disabling options",-1)),t("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:e[4]||(e[4]=s=>l(n)("code3"))},[...e[18]||(e[18]=[a('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),t("div",q,[m(l(p),{modelValue:g.value,"onUpdate:modelValue":e[5]||(e[5]=s=>g.value=s),options:y.value,class:"custom-multiselect",searchable:!1,"allow-empty":!1,"track-by":"name",label:"name","preselect-first":!0,"selected-label":"","select-label":"","deselect-label":""},null,8,["modelValue","options"])]),l(i).includes("code3")?(r(),d(u,{key:0},{default:v(()=>[...e[20]||(e[20]=[t("pre",null,`<!-- disabling options -->
<multiselect
  v-model="input3"
  :options="options3"
  class="custom-multiselect"
  :searchable="false"
  :allow-empty="false"
  track-by="name"
  label="name"
  :preselect-first="true"
  selected-label=""
  select-label=""
  deselect-label=""
></multiselect>

<!-- script -->
<script lang="ts" setup>
  import { ref } from 'vue';
  import Multiselect from '@suadelabs/vue3-multiselect';
  import '@suadelabs/vue3-multiselect/dist/vue3-multiselect.css';

  const options3 = ref([{ name: 'Orange' }, { name: 'White', $isDisabled: true }, { name: 'Purple' }]);
  const input3 = ref({ name: 'Orange' });
<\/script>
`,-1)])]),_:1})):c("",!0)]),t("div",A,[t("div",R,[e[22]||(e[22]=t("h5",{class:"font-semibold text-lg dark:text-white-light"},"Placeholder",-1)),t("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:e[6]||(e[6]=s=>l(n)("code4"))},[...e[21]||(e[21]=[a('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),t("div",Y,[m(l(p),{modelValue:k.value,"onUpdate:modelValue":e[7]||(e[7]=s=>k.value=s),options:h.value,class:"custom-multiselect",searchable:!1,placeholder:"Choose...","selected-label":"","select-label":"","deselect-label":""},null,8,["modelValue","options"])]),l(i).includes("code4")?(r(),d(u,{key:0},{default:v(()=>[...e[23]||(e[23]=[t("pre",null,`<!-- placeholder -->
<multiselect v-model="input4" :options="options" class="custom-multiselect" :searchable="false" placeholder="Choose..." selected-label="" select-label="" deselect-label=""></multiselect>

<!-- script -->
<script lang="ts" setup>
  import { ref } from 'vue';
  import Multiselect from '@suadelabs/vue3-multiselect';
  import '@suadelabs/vue3-multiselect/dist/vue3-multiselect.css';

  const options = ref(['Orange', 'White', 'Purple']);
  const input4 = ref('');
<\/script>
`,-1)])]),_:1})):c("",!0)]),t("div",_,[t("div",E,[e[25]||(e[25]=t("h5",{class:"font-semibold text-lg dark:text-white-light"},"Searchable",-1)),t("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:e[8]||(e[8]=s=>l(n)("code5"))},[...e[24]||(e[24]=[a('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),t("div",F,[m(l(p),{modelValue:w.value,"onUpdate:modelValue":e[9]||(e[9]=s=>w.value=s),options:h.value,class:"custom-multiselect",searchable:!0,placeholder:"Select an option","selected-label":"","select-label":"","deselect-label":""},null,8,["modelValue","options"])]),l(i).includes("code5")?(r(),d(u,{key:0},{default:v(()=>[...e[26]||(e[26]=[t("pre",null,`<!-- searchable -->
<multiselect
  v-model="input5"
  :options="options"
  class="custom-multiselect"
  :searchable="true"
  placeholder="Select an option"
  selected-label=""
  select-label=""
  deselect-label=""
></multiselect>

<!-- script -->
<script lang="ts" setup>
  import { ref } from 'vue';
  import Multiselect from '@suadelabs/vue3-multiselect';
  import '@suadelabs/vue3-multiselect/dist/vue3-multiselect.css';

  const options = ref(['Orange', 'White', 'Purple']);
  const input5 = ref('');
<\/script>
`,-1)])]),_:1})):c("",!0)]),t("div",I,[t("div",Z,[e[28]||(e[28]=t("h5",{class:"font-semibold text-lg dark:text-white-light"},"Multiple select",-1)),t("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:e[10]||(e[10]=s=>l(n)("code6"))},[...e[27]||(e[27]=[a('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),t("div",z,[m(l(p),{modelValue:C.value,"onUpdate:modelValue":e[11]||(e[11]=s=>C.value=s),options:h.value,class:"custom-multiselect",multiple:!0,searchable:!1,placeholder:"Select an option","selected-label":"","select-label":"","deselect-label":""},null,8,["modelValue","options"])]),l(i).includes("code6")?(r(),d(u,{key:0},{default:v(()=>[...e[29]||(e[29]=[t("pre",null,`<!-- multiple -->
<multiselect
  v-model="input6"
  :options="options"
  class="custom-multiselect"
  :multiple="true"
  :searchable="false"
  placeholder="Select an option"
  selected-label=""
  select-label=""
  deselect-label=""
>
</multiselect>

<!-- script -->
<script lang="ts" setup>
  import { ref } from 'vue';
  import Multiselect from '@suadelabs/vue3-multiselect';
  import '@suadelabs/vue3-multiselect/dist/vue3-multiselect.css';

  const options = ref(['Orange', 'White', 'Purple']);
  const input6 = ref([]);
<\/script>
`,-1)])]),_:1})):c("",!0)])])])]))}});export{te as default};
