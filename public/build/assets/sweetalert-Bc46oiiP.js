import{c as b,_ as c}from"./codePreview-B8fh2aNm.js";import{S as r}from"./sweetalert2.esm.all-5zhdP7Ax.js";import{u as k}from"./main-JT-dYQ3z.js";import{d as C,k as v,l as i,m as e,C as l,I as p,x as m,u as o,q as w}from"./vendor-CjqaLWPm.js";import"./quill-De1S0mUZ.js";const x={class:"pt-5 space-y-8 sweet-alerts"},y={class:"grid grid-cols-1 lg:grid-cols-2 gap-6"},L={class:"panel"},B={class:"flex items-center justify-between mb-5"},S={class:"mb-5"},M={class:"flex items-center justify-center"},A={class:"panel"},j={class:"flex items-center justify-between mb-5"},T={class:"mb-5"},$={class:"flex items-center justify-center"},I={class:"panel"},Y={class:"flex items-center justify-between mb-5"},q={class:"mb-5"},D={class:"flex items-center justify-center"},P={class:"panel"},W={class:"flex items-center justify-between mb-5"},O={class:"mb-5"},_={class:"flex items-center justify-center"},H={class:"panel"},N={class:"flex items-center justify-between mb-5"},R={class:"mb-5"},V={class:"flex items-center justify-center"},U={class:"panel"},Q={class:"flex items-center justify-between mb-5"},G={class:"mb-5"},J={class:"flex items-center justify-center"},F={class:"panel"},E={class:"flex items-center justify-between mb-5"},X={class:"mb-5"},z={class:"flex items-center justify-center"},Z={class:"panel"},K={class:"flex items-center justify-between mb-5"},tt={class:"mb-5"},et={class:"flex items-center justify-center"},st={class:"panel"},ot={class:"flex items-center justify-between mb-5"},rt={class:"mb-5"},nt={class:"flex items-center justify-center"},it={class:"panel"},lt={class:"flex items-center justify-between mb-5"},at={class:"mb-5"},dt={class:"flex items-center justify-center"},ut={class:"panel"},ct={class:"flex items-center justify-between mb-5"},pt={class:"mb-5"},mt={class:"flex items-center justify-center"},wt={class:"panel"},ht={class:"flex items-center justify-between mb-5"},ft={class:"mb-5"},gt={class:"flex items-center justify-center"},bt={class:"panel"},kt={class:"flex items-center justify-between mb-5"},Ct={class:"mb-5"},vt={class:"flex items-center justify-center"},xt={class:"panel"},yt={class:"flex items-center justify-between mb-5"},Lt={class:"mb-5"},Bt={class:"flex items-center justify-center",id:"rtl-container"},St={class:"panel"},Mt={class:"flex items-center justify-between mb-5"},At={class:"mb-5"},jt={class:"flex items-center justify-center"},Pt=C({__name:"sweetalert",setup(Tt){k({title:"Sweetalert"});const{codeArr:a,toggleCode:d}=b(),u=async n=>{if(n===1)r.fire({title:"Saved succesfully",padding:"2em",customClass:"sweet-alerts"});else if(n===2)r.fire({icon:"success",title:"Good job!",text:"You clicked the!",padding:"2em",customClass:"sweet-alerts"});else if(n===3){const t="https://api.ipify.org?format=json";r.fire({title:"Your public IP",confirmButtonText:"Show my public IP",text:"Your public IP will be received via AJAX request",showLoaderOnConfirm:!0,customClass:"sweet-alerts",preConfirm:()=>fetch(t).then(s=>s.json()).then(s=>{r.fire({title:s.ip,customClass:"sweet-alerts"})}).catch(()=>{r.fire({icon:"error",title:"Unable to get your public IP",customClass:"sweet-alerts"})})})}else if(n===4)r.fire({icon:"question",title:"The Internet?",text:"That thing is still around?",padding:"2em",customClass:"sweet-alerts"});else if(n===5){const t=["1","2","3"],s=r.mixin({confirmButtonText:"Next →",showCancelButton:!0,progressSteps:t,input:"text",inputAttributes:{required:"true"},validationMessage:"This field is required",padding:"2em",customClass:"sweet-alerts"}),f=[];let h;for(h=0;h<t.length;){const g=await s.fire({title:`Question ${t[h]}`,text:h==0?"Chaining swal modals is easy.":"",inputValue:f[h]||"",showCancelButton:h>0,currentProgressStep:h,customClass:"sweet-alerts"});if(g.value)f[h]=g.value,h++;else if(g.dismiss===r.DismissReason.cancel)h--;else break}h===t.length&&r.fire({title:"All done!",padding:"2em",html:"Your answers: <pre>"+JSON.stringify(f)+"</pre>",confirmButtonText:"Lovely!",customClass:"sweet-alerts"})}else if(n===6)r.fire({title:"Custom animation with Animate.css",showClass:{popup:"animate__animated animate__flip"},hideClass:{popup:"animate__animated animate__fadeOutUp"},padding:"2em",customClass:"sweet-alerts"});else if(n===7){let t;r.fire({title:"Auto close alert!",html:"I will close in <b></b> milliseconds.",timer:2e3,timerProgressBar:!0,customClass:"sweet-alerts",didOpen:()=>{var f;r.showLoading();const s=(f=r.getHtmlContainer())==null?void 0:f.querySelector("b");t=setInterval(()=>{s.textContent=r.getTimerLeft()},100)},willClose:()=>{clearInterval(t)}}).then(s=>{s.dismiss===r.DismissReason.timer&&console.log("I was closed by the timer")})}else if(n===8)r.fire({title:"Sweet!",text:"Modal with a custom image.",imageUrl:"/assets/images/custom-swal.svg",imageWidth:224,imageHeight:"auto",imageAlt:"Custom image",padding:"2em",customClass:"sweet-alerts"});else if(n===9)r.fire({icon:"info",title:"<i>HTML</i> <u>example</u>",html:'You can use <b>bold text</b>, <a href="//github.com">links</a> and other HTML tags',showCloseButton:!0,showCancelButton:!0,focusConfirm:!1,confirmButtonText:'<i class="flaticon-checked-1"></i> Great!',confirmButtonAriaLabel:"Thumbs up, great!",cancelButtonText:'<i class="flaticon-cancel-circle"></i> Cancel',cancelButtonAriaLabel:"Thumbs down",padding:"2em",customClass:"sweet-alerts"});else if(n===10)r.fire({icon:"warning",title:"Are you sure?",text:"You won't be able to revert this!",showCancelButton:!0,confirmButtonText:"Delete",padding:"2em",customClass:"sweet-alerts"}).then(t=>{t.value&&r.fire({title:"Deleted!",text:"Your file has been deleted.",icon:"success",customClass:"sweet-alerts"})});else if(n===11){const t=r.mixin({customClass:{popup:"sweet-alerts",confirmButton:"btn btn-secondary",cancelButton:"btn btn-dark ltr:mr-3 rtl:ml-3"},buttonsStyling:!1});t.fire({title:"Are you sure?",text:"You won't be able to revert this!",icon:"warning",showCancelButton:!0,confirmButtonText:"Yes, delete it!",cancelButtonText:"No, cancel!",reverseButtons:!0,padding:"2em"}).then(s=>{s.value?t.fire("Deleted!","Your file has been deleted.","success"):s.dismiss===r.DismissReason.cancel&&t.fire("Cancelled","Your imaginary file is safe :)","error")})}else n===12?r.fire({title:"Custom width, padding, background.",width:600,padding:"7em",customClass:"background-modal sweet-alerts",background:"#fff url(/assets/images/sweet-bg.jpg) no-repeat 100% 100%"}):n===13?r.fire({icon:"error",title:"Oops...",text:"Something went wrong!",footer:'<a href="javascript:;">Why do I have this issue?</a>',padding:"2em",customClass:"sweet-alerts"}):n===14?r.fire({title:"هل تريد الاستمرار؟",confirmButtonText:"نعم",cancelButtonText:"لا",showCancelButton:!0,showCloseButton:!0,padding:"2em",customClass:"sweet-alerts"}):n===15&&r.mixin({toast:!0,position:"top-end",showConfirmButton:!1}).fire({icon:"success",title:"Signed in successfully",padding:"10px 20px"})};return(n,t)=>(i(),v("div",null,[t[77]||(t[77]=e("ul",{class:"flex space-x-2 rtl:space-x-reverse"},[e("li",null,[e("a",{href:"javascript:;",class:"text-primary hover:underline"},"Components")]),e("li",{class:"before:content-['/'] ltr:before:mr-2 rtl:before:ml-2"},[e("span",null,"Sweet Alerts")])],-1)),e("div",x,[t[76]||(t[76]=l('<div class="panel p-3 flex items-center text-primary overflow-x-auto whitespace-nowrap"><div class="ring-2 ring-primary/30 rounded-full bg-primary text-white p-1.5 ltr:mr-3 rtl:ml-3"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5"><path d="M19.0001 9.7041V9C19.0001 5.13401 15.8661 2 12.0001 2C8.13407 2 5.00006 5.13401 5.00006 9V9.7041C5.00006 10.5491 4.74995 11.3752 4.28123 12.0783L3.13263 13.8012C2.08349 15.3749 2.88442 17.5139 4.70913 18.0116C9.48258 19.3134 14.5175 19.3134 19.291 18.0116C21.1157 17.5139 21.9166 15.3749 20.8675 13.8012L19.7189 12.0783C19.2502 11.3752 19.0001 10.5491 19.0001 9.7041Z" stroke="currentColor" stroke-width="1.5"></path><path opacity="0.5" d="M7.5 19C8.15503 20.7478 9.92246 22 12 22C14.0775 22 15.845 20.7478 16.5 19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg></div><span class="ltr:mr-3 rtl:ml-3">Documentation: </span><a href="https://www.npmjs.com/package/sweetalert2" target="_blank" class="block hover:underline">https://www.npmjs.com/package/sweetalert2</a></div>',1)),e("div",y,[e("div",L,[e("div",B,[t[31]||(t[31]=e("h5",{class:"font-semibold text-lg dark:text-white-light"},"Basic message",-1)),e("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:t[0]||(t[0]=s=>o(d)("code1"))},[...t[30]||(t[30]=[l('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),e("div",S,[e("div",M,[e("button",{type:"button",class:"btn btn-primary",onClick:t[1]||(t[1]=s=>u(1))},"Basic message")])]),o(a).includes("code1")?(i(),p(c,{key:0},{default:w(()=>[...t[32]||(t[32]=[e("pre",null,`<!-- basic message -->
<button type="button" class="btn btn-primary" @click="showAlert()">Basic message</button>

<!-- script -->
<script>
    const showAlert = async (type: number) => {
        Swal.fire({
            title: 'Saved succesfully',
            padding: '2em',
            customClass: 'sweet-alerts',
        });
    }
<\/script>
`,-1)])]),_:1})):m("",!0)]),e("div",A,[e("div",j,[t[34]||(t[34]=e("h5",{class:"font-semibold text-lg dark:text-white-light"},"Success message",-1)),e("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:t[2]||(t[2]=s=>o(d)("code2"))},[...t[33]||(t[33]=[l('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),e("div",T,[e("div",$,[e("button",{type:"button",class:"btn btn-secondary",onClick:t[3]||(t[3]=s=>u(2))},"Success message!")])]),o(a).includes("code2")?(i(),p(c,{key:0},{default:w(()=>[...t[35]||(t[35]=[e("pre",null,`<!-- success message -->
<button type="button" class="btn btn-secondary" @click="showAlert()">Success message!</button>

<!-- script -->
<script>
    const showAlert = async (type: number) => {
        Swal.fire({
            icon: 'success',
            title: 'Good job!',
            text: 'You clicked the!',
            padding: '2em',
            customClass: 'sweet-alerts',
        });
    }
<\/script>
`,-1)])]),_:1})):m("",!0)]),e("div",I,[e("div",Y,[t[37]||(t[37]=e("h5",{class:"font-semibold text-lg dark:text-white-light"},"Dynamic queue",-1)),e("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:t[4]||(t[4]=s=>o(d)("code3"))},[...t[36]||(t[36]=[l('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),e("div",q,[e("div",D,[e("button",{type:"button",class:"btn btn-success",onClick:t[5]||(t[5]=s=>u(3))},"Dynamic queue")])]),o(a).includes("code3")?(i(),p(c,{key:0},{default:w(()=>[...t[38]||(t[38]=[e("pre",null,`<!-- dynamic queue -->
<button type="button" class="btn btn-success" @click="showAlert()">Dynamic queue</button>

<!-- script -->
<script>
    const showAlert = async (type: number) => {
        const ipAPI = 'https://api.ipify.org?format=json';
        Swal.fire({
            title: 'Your public IP',
            confirmButtonText: 'Show my public IP',
            text: 'Your public IP will be received ' + 'via AJAX request',
            showLoaderOnConfirm: true,
            customClass: 'sweet-alerts',
            preConfirm: () => {
                return fetch(ipAPI)
                    .then((response) => {
                        return response.json();
                    })
                    .then((data) => {
                        Swal.fire({
                            title: data.ip,
                            customClass: 'sweet-alerts',
                        });
                    })
                    .catch(() => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Unable to get your public IP',
                            customClass: 'sweet-alerts',
                        });
                    });
            },
        });
    }
<\/script>
`,-1)])]),_:1})):m("",!0)]),e("div",P,[e("div",W,[t[40]||(t[40]=e("h5",{class:"font-semibold text-lg dark:text-white-light"},"A title with a text under",-1)),e("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:t[6]||(t[6]=s=>o(d)("code5"))},[...t[39]||(t[39]=[l('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),e("div",O,[e("div",_,[e("button",{type:"button",class:"btn btn-danger",onClick:t[7]||(t[7]=s=>u(4))},"Title & text")])]),o(a).includes("code5")?(i(),p(c,{key:0},{default:w(()=>[...t[41]||(t[41]=[e("pre",null,`<!-- success message -->
<button type="button" class="btn btn-danger" @click="showAlert()">Title & text</button>

<!-- script -->
<script>
    const showAlert = async (type: number) => {
        Swal.fire({
            icon: 'question',
            title: 'The Internet?',
            text: 'That thing is still around?',
            padding: '2em',
            customClass: 'sweet-alerts',
        });
    }
<\/script>
`,-1)])]),_:1})):m("",!0)]),e("div",H,[e("div",N,[t[43]||(t[43]=e("h5",{class:"font-semibold text-lg dark:text-white-light"},"Chaining modals (queue)",-1)),e("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:t[8]||(t[8]=s=>o(d)("code6"))},[...t[42]||(t[42]=[l('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),e("div",R,[e("div",V,[e("button",{type:"button",class:"btn btn-warning",onClick:t[9]||(t[9]=s=>u(5))},"Chaining modals (queue)")])]),o(a).includes("code6")?(i(),p(c,{key:0},{default:w(()=>[...t[44]||(t[44]=[e("pre",null,`<!-- success message -->
<button type="button" class="btn btn-warning" @click="showAlert()">Chaining modals (queue)</button>

<!-- script -->
<script>
    const showAlert = async (type: number) => {
        const steps = ['1', '2', '3'];
        const swalQueueStep = Swal.mixin({
            confirmButtonText: 'Next →',
            showCancelButton: true,
            progressSteps: steps,
            input: 'text',
            inputAttributes: {
                required: true,
            },
            validationMessage: 'This field is required',
            padding: '2em',
            customClass: 'sweet-alerts',
        });

        const values = [];
        let currentStep;

        for (currentStep = 0; currentStep < steps.length;) {
            const result = await swalQueueStep.fire({
                title: \`Question \${steps[currentStep]}\`,
                text: currentStep == 0 ? 'Chaining swal modals is easy.' : '',
                inputValue: values[currentStep],
                showCancelButton: currentStep > 0,
                currentProgressStep: currentStep,
                customClass: 'sweet-alerts',
            });
            if (result.value) {
                values[currentStep] = result.value;
                currentStep++;
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                currentStep--;
            } else {
                break;
            }
        }

        if (currentStep === steps.length) {
            Swal.fire({
                title: 'All done!',
                padding: '2em',
                html: 'Your answers: <pre>' + JSON.stringify(values) + '</pre>',
                confirmButtonText: 'Lovely!',
                customClass: 'sweet-alerts',
            });
        }
    }
<\/script>
`,-1)])]),_:1})):m("",!0)]),e("div",U,[e("div",Q,[t[46]||(t[46]=e("h5",{class:"font-semibold text-lg dark:text-white-light"},"Custom animation",-1)),e("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:t[10]||(t[10]=s=>o(d)("code7"))},[...t[45]||(t[45]=[l('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),e("div",G,[e("div",J,[e("button",{type:"button",class:"btn btn-info",onClick:t[11]||(t[11]=s=>u(6))},"Custom animation")]),t[47]||(t[47]=e("div",{class:"fixed inset-0 bg-[black]/60 z-[999] transition-all duration-300 px-4 hidden"},[e("div",{class:"panel absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-11/12 sm:w-[480px] text-center"},[e("h3",{class:"text-[#3b3f5c] dark:text-white-light text-2xl font-bold mb-5"},"Custom animation with Animate.css"),e("button",{type:"button",class:"btn btn-info"},"Ok")])],-1))]),o(a).includes("code7")?(i(),p(c,{key:0},{default:w(()=>[...t[48]||(t[48]=[e("pre",null,`<!-- success message -->
<button type="button" class="btn btn-info" @click="showAlert()">Custom animation</button>

<!-- script -->
<script>
    const showAlert = async (type: number) => {
        Swal.fire({
            title: 'Custom animation with Animate.css',
            animation: false,
            showClass: {
                popup: 'animate__animated animate__flip'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp'
            },
            padding: '2em',
            customClass: 'sweet-alerts',
        });
    }
<\/script>
`,-1)])]),_:1})):m("",!0)]),e("div",F,[e("div",E,[t[50]||(t[50]=e("h5",{class:"font-semibold text-lg dark:text-white-light"},"Message with auto close timer",-1)),e("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:t[12]||(t[12]=s=>o(d)("code8"))},[...t[49]||(t[49]=[l('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),e("div",X,[e("div",z,[e("button",{type:"button",class:"btn btn-primary",onClick:t[13]||(t[13]=s=>u(7))},"Message timer")])]),o(a).includes("code8")?(i(),p(c,{key:0},{default:w(()=>[...t[51]||(t[51]=[e("pre",null,`<!-- success message -->
<button type="button" class="btn btn-primary" @click="showAlert()">Message timer</button>

<!-- script -->
<script>
    const showAlert = async (type: number) => {
        let timerInterval;

        Swal.fire({
            title: 'Auto close alert!',
            html: 'I will close in <b></b> milliseconds.',
            timer: 2000,
            timerProgressBar: true,
            customClass: 'sweet-alerts',
            didOpen: () => {
                Swal.showLoading();
                const b = Swal.getHtmlContainer().querySelector('b');
                timerInterval = setInterval(() => {
                    b.textContent = Swal.getTimerLeft();
                }, 100);
            },
            willClose: () => {
                clearInterval(timerInterval);
            },
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.timer) {
                console.log('I was closed by the timer');
            }
        });
    }
<\/script>
`,-1)])]),_:1})):m("",!0)]),e("div",Z,[e("div",K,[t[53]||(t[53]=e("h5",{class:"font-semibold text-lg dark:text-white-light"},"Message with custom image",-1)),e("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:t[14]||(t[14]=s=>o(d)("code9"))},[...t[52]||(t[52]=[l('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),e("div",tt,[e("div",et,[e("button",{type:"button",class:"btn btn-secondary",onClick:t[15]||(t[15]=s=>u(8))},"Message with custom image")])]),o(a).includes("code9")?(i(),p(c,{key:0},{default:w(()=>[...t[54]||(t[54]=[e("pre",null,`<!-- success message -->
<button type="button" class="btn btn-secondary" @click="showAlert()">Message with custom image</button>

<!-- script -->
<script>
    const showAlert = async (type: number) => {
        Swal.fire({
            title: 'Sweet!',
            text: 'Modal with a custom image.',
            imageUrl: ('/assets/images/thumbs-up.jpg'),
            imageWidth: 224,
            imageHeight: 200,
            imageAlt: 'Custom image',
            animation: false,
            padding: '2em',
            customClass: 'sweet-alerts',
        });
    }
<\/script>
`,-1)])]),_:1})):m("",!0)]),e("div",st,[e("div",ot,[t[56]||(t[56]=e("h5",{class:"font-semibold text-lg dark:text-white-light"},"Custom HTML description and buttons",-1)),e("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:t[16]||(t[16]=s=>o(d)("code10"))},[...t[55]||(t[55]=[l('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),e("div",rt,[e("div",nt,[e("button",{type:"button",class:"btn btn-danger",onClick:t[17]||(t[17]=s=>u(9))},"Custom Description & buttons")])]),o(a).includes("code10")?(i(),p(c,{key:0},{default:w(()=>[...t[57]||(t[57]=[e("pre",null,`<!-- success message -->
<button type="button" class="btn btn-danger" @click="showAlert()">Custom Description & buttons</button>

<!-- script -->
<script>
    const showAlert = async (type: number) => {
        Swal.fire({
            icon: 'info',
            title: '<i>HTML</i> <u>example</u>',
            html: 'You can use <b>bold text</b>, ' + '<a href="//github.com">links</a> ' + 'and other HTML tags',
            showCloseButton: true,
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: '<i class="flaticon-checked-1"></i> Great!',
            confirmButtonAriaLabel: 'Thumbs up, great!',
            cancelButtonText: '<i class="flaticon-cancel-circle"></i> Cancel',
            cancelButtonAriaLabel: 'Thumbs down',
            padding: '2em',
            customClass: 'sweet-alerts',
        });
    }
<\/script>
`,-1)])]),_:1})):m("",!0)]),e("div",it,[e("div",lt,[t[59]||(t[59]=e("h5",{class:"font-semibold text-lg dark:text-white-light"},'Warning message, with "Confirm" button',-1)),e("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:t[18]||(t[18]=s=>o(d)("code11"))},[...t[58]||(t[58]=[l('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),e("div",at,[e("div",dt,[e("button",{type:"button",class:"btn btn-success",onClick:t[19]||(t[19]=s=>u(10))},"Confirm")])]),o(a).includes("code11")?(i(),p(c,{key:0},{default:w(()=>[...t[60]||(t[60]=[e("pre",null,`<!-- success message -->
<button type="button" class="btn btn-success" @click="showAlert()">Confirm</button>

<!-- script -->
<script>
    const showAlert = async (type: number) => {
        Swal.fire({
            icon: 'warning',
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            showCancelButton: true,
            confirmButtonText: 'Delete',
            padding: '2em',
            customClass: 'sweet-alerts',
        }).then((result) => {
            if (result.value) {
                Swal.fire({ title: 'Deleted!', text: 'Your file has been deleted.', icon: 'success', customClass: 'sweet-alerts' });
            }
        });
    }
<\/script>
`,-1)])]),_:1})):m("",!0)]),e("div",ut,[e("div",ct,[t[62]||(t[62]=e("h5",{class:"font-semibold text-lg dark:text-white-light"},'Execute something else for "Cancel".',-1)),e("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:t[20]||(t[20]=s=>o(d)("code12"))},[...t[61]||(t[61]=[l('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),e("div",pt,[e("div",mt,[e("button",{type:"button",class:"btn btn-warning",onClick:t[21]||(t[21]=s=>u(11))},'Addition else for "Cancel".')])]),o(a).includes("code12")?(i(),p(c,{key:0},{default:w(()=>[...t[63]||(t[63]=[e("pre",null,`<!-- success message -->
<button type="button" class="btn btn-warning" @click="showAlert()">Addition else for "Cancel".</button>

<!-- script -->
<script>
    const showAlert = async (type: number) => {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                popup: 'sweet-alerts',
                confirmButton: 'btn btn-secondary',
                cancelButton: 'btn btn-dark ltr:mr-3 rtl:ml-3',
            },
            buttonsStyling: false,
        });
        swalWithBootstrapButtons
        .fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true,
            padding: '2em',
        })
        .then((result) => {
            if (result.value) {
                swalWithBootstrapButtons.fire('Deleted!', 'Your file has been deleted.', 'success');
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                swalWithBootstrapButtons.fire('Cancelled', 'Your imaginary file is safe :)', 'error');
            }
        });
    }
<\/script>
`,-1)])]),_:1})):m("",!0)]),e("div",wt,[e("div",ht,[t[65]||(t[65]=e("h5",{class:"font-semibold text-lg dark:text-white-light"},"A message with custom width, padding and background",-1)),e("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:t[22]||(t[22]=s=>o(d)("code13"))},[...t[64]||(t[64]=[l('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),e("div",ft,[e("div",gt,[e("button",{type:"button",class:"btn btn-info",onClick:t[23]||(t[23]=s=>u(12))},"Custom Message")])]),o(a).includes("code13")?(i(),p(c,{key:0},{default:w(()=>[...t[66]||(t[66]=[e("pre",null,`<!-- success message -->
<button type="button" class="btn btn-info" @click="showAlert()">Custom Message</button>

<!-- script -->
<script>
    const showAlert = async (type: number) => {
        Swal.fire({
            title: 'Custom width, padding, background.',
            width: 600,
            padding: '7em',
            customClass: 'background-modal sweet-alerts',
            background: '#fff url(' + ('/assets/images/sweet-bg.jpg') + ') no-repeat 100% 100%',
        });
    }
<\/script>
`,-1)])]),_:1})):m("",!0)]),e("div",bt,[e("div",kt,[t[68]||(t[68]=e("h5",{class:"font-semibold text-lg dark:text-white-light"},"With Footer",-1)),e("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:t[24]||(t[24]=s=>o(d)("code14"))},[...t[67]||(t[67]=[l('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),e("div",Ct,[e("div",vt,[e("button",{type:"button",class:"btn btn-dark",onClick:t[25]||(t[25]=s=>u(13))},"With Footer")])]),o(a).includes("code14")?(i(),p(c,{key:0},{default:w(()=>[...t[69]||(t[69]=[e("pre",null,`<!-- success message -->
<button type="button" class="btn btn-dark" @click="showAlert()">With Footer</button>

<!-- script -->
<script>
    const showAlert = async (type: number) => {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Something went wrong!',
            footer: '<a href="javascript:;">Why do I have this issue?</a>',
            padding: '2em',
            customClass: 'sweet-alerts',
        });
    }
<\/script>
`,-1)])]),_:1})):m("",!0)]),e("div",xt,[e("div",yt,[t[71]||(t[71]=e("h5",{class:"font-semibold text-lg dark:text-white-light"},"RTL support",-1)),e("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:t[26]||(t[26]=s=>o(d)("code15"))},[...t[70]||(t[70]=[l('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),e("div",Lt,[e("div",Bt,[e("button",{type:"button",class:"btn btn-primary",onClick:t[27]||(t[27]=s=>u(14))},"RTL")])]),o(a).includes("code15")?(i(),p(c,{key:0},{default:w(()=>[...t[72]||(t[72]=[e("pre",null,`<!-- success message -->
<button type="button" class="btn btn-primary" @click="showAlert()">RTL</button>

<!-- script -->
<script>
    const showAlert = async (type: number) => {
        Swal.fire({
            title: 'هل تريد الاستمرار؟',
            confirmButtonText: 'نعم',
            cancelButtonText: 'لا',
            showCancelButton: true,
            showCloseButton: true,
            padding: '2em',
            customClass: 'sweet-alerts',
        });
    }
<\/script>
`,-1)])]),_:1})):m("",!0)]),e("div",St,[e("div",Mt,[t[74]||(t[74]=e("h5",{class:"font-semibold text-lg dark:text-white-light"},"Mixin",-1)),e("a",{class:"font-semibold hover:text-gray-400 dark:text-gray-400 dark:hover:text-gray-600",href:"javascript:;",onClick:t[28]||(t[28]=s=>o(d)("code16"))},[...t[73]||(t[73]=[l('<span class="flex items-center"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 ltr:mr-2 rtl:ml-2"><path d="M17 7.82959L18.6965 9.35641C20.239 10.7447 21.0103 11.4389 21.0103 12.3296C21.0103 13.2203 20.239 13.9145 18.6965 15.3028L17 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path opacity="0.5" d="M13.9868 5L10.0132 19.8297" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path><path d="M7.00005 7.82959L5.30358 9.35641C3.76102 10.7447 2.98975 11.4389 2.98975 12.3296C2.98975 13.2203 3.76102 13.9145 5.30358 15.3028L7.00005 16.8296" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path></svg> Code </span>',1)])])]),e("div",At,[e("div",jt,[e("button",{type:"button",class:"btn btn-secondary",onClick:t[29]||(t[29]=s=>u(15))},"Mixin")])]),o(a).includes("code16")?(i(),p(c,{key:0},{default:w(()=>[...t[75]||(t[75]=[e("pre",null,`<!-- success message -->
<button type="button" class="btn btn-secondary" @click="showAlert()">Mixin</button>

<!-- script -->
<script>
    const showAlert = async (type: number) => {
        const toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            padding: '2em',
            customClass: 'sweet-alerts',
        });
        toast.fire({
            icon: 'success',
            title: 'Signed in successfully',
            padding: '2em',
            customClass: 'sweet-alerts',
        });
    }
<\/script>
`,-1)])]),_:1})):m("",!0)])])])]))}});export{Pt as default};
