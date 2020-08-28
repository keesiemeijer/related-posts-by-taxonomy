(this["webpackJsonpgithub-pages-code-reference"]=this["webpackJsonpgithub-pages-code-reference"]||[]).push([[0],{1:function(e){e.exports=JSON.parse('{"page_title":"Search The Code Reference!","home_desc":"Want to know what\'s going on inside this %1$s.","home_desc-2":"Search the Code Reference for more information about functions, classes, methods, and hooks.","home":"Home","functions":"Functions","function":"Function","classes":"Classes","class":"Class","hooks":"Hooks","hook":"Hook","methods":"Methods","method":"Method","search_results":"Search Results","results_for":"Search Results for: %1$s","functions_search":"Search functions","classes_search":"Search classes","hooks_search":"Search hooks","methods_search":"Search methods","source":"Source","source_file":"Source: %1$s","view_source":"View source","view_source_file":"View source file","submit":"Search","repo":"GitHub Repository","docs":"Documentation","related":"Related","used_by":"Used By","uses":"Uses","changelog":"Changelog","version":"Version","version_label":"Version: %1$s","description":"Description","code_base":"code base","loading":"Loading...","retry":"Retry","timeout":"Taking a long time...","error":"Error!","namespace":"Namespace: %1$s","filter_by_version":"Since version:","filter_by_type":"Type:","none":"none","modified":"modified","introduced":"introduced","deprecated":"deprecated","deprecated_in":"deprecated in version: %1$s","undocumented":"undocumented","undocumented_version":"undocumented version","found":"%1$d %2$s found","filter_version_found":"%1$d %2$s found with version %3$s","filter_type_found":"%1$d %2$s %3$s found","filter_all_found":"%1$d %2$s %3$s found with version %4$s","not_found":"No %1$s found","filter_version_not_found":"No %1$s found with version %2$s","filter_type_not_found":"No %1$s %2$s found","filter_all_not_found":"No %1$s %2$s found with version %3$s"}')},112:function(e,t,n){var a={"./classes.json":[193,20],"./functions.json":[194,21],"./hooks.json":[195,22],"./methods.json":[196,23],"./version.json":[197,24]};function r(e){if(!n.o(a,e))return Promise.resolve().then((function(){var t=new Error("Cannot find module '"+e+"'");throw t.code="MODULE_NOT_FOUND",t}));var t=a[e],r=t[0];return n.e(t[1]).then((function(){return n.t(r,3)}))}r.keys=function(){return Object.keys(a)},r.id=112,e.exports=r},189:function(e,t,n){var a={"./includes-back-compat-deprecated.json":[198,3],"./includes-class-cache.json":[199,4],"./includes-class-debug.json":[200,5],"./includes-class-defaults.json":[201,6],"./includes-class-lazy-loading.json":[202,7],"./includes-class-plugin.json":[203,8],"./includes-class-rest-api.json":[204,9],"./includes-class-widget.json":[205,10],"./includes-functions.json":[206,11],"./includes-gallery.json":[207,12],"./includes-query.json":[208,13],"./includes-settings.json":[209,14],"./includes-shortcode.json":[210,15],"./includes-taxonomy.json":[211,16],"./includes-template-loader.json":[212,17],"./includes-template-tags.json":[213,18],"./related-posts-by-taxonomy.json":[214,19]};function r(e){if(!n.o(a,e))return Promise.resolve().then((function(){var t=new Error("Cannot find module '"+e+"'");throw t.code="MODULE_NOT_FOUND",t}));var t=a[e],r=t[0];return n.e(t[1]).then((function(){return n.t(r,3)}))}r.keys=function(){return Object.keys(a)},r.id=189,e.exports=r},190:function(e,t,n){},191:function(e,t,n){},192:function(e,t,n){"use strict";n.r(t);var a=n(0),r=n.n(a),s=n(73),o=n.n(s),l=n(4),i=n(3),c=n(12),u=n.n(c),p=n(8),h=n(9),m=n(11),d=n(10),f=n(2),g=n.n(f),v=r.a.createContext(),y=function(e){Object(m.a)(a,e);var t=Object(d.a)(a);function a(e){var r;return Object(p.a)(this,a),(r=t.call(this,e)).fetchData=function(e){if(r.setState({status:"searching",postType:e}),g()(r.state[e]))try{n(112)("./"+e+".json").then((function(t){r.setState({[e]:t,status:"done"})}))}catch(t){r.setState({status:"error"})}else r.setState({status:"done"})},r.state={functions:{},classes:{},hooks:{},methods:{}},r}return Object(h.a)(a,[{key:"render",value:function(){return r.a.createElement(v.Provider,{value:{postType:this.state.postType,postTypeData:this.state,fetchData:this.fetchData}},this.props.children)}}]),a}(r.a.Component);function E(e){return function(t){Object(m.a)(a,t);var n=Object(d.a)(a);function a(){return Object(p.a)(this,a),n.apply(this,arguments)}return Object(h.a)(a,[{key:"render",value:function(){var t=this;return r.a.createElement(v.Consumer,null,(function(n){n.postType;var a=n.postTypeData,s=n.fetchData;return r.a.createElement(e,Object.assign({},t.props,{postType:t.props.postType,postTypeData:a,fetchData:s}))}))}}]),a}(a.Component)}var _=n(15),b=n(74),j=n.n(b),O=n(32),S=n.n(O),T=n(16);function k(e){return u()(e,"/").split("/").filter((function(e){return""!==e}))}function w(e){return e?(/^[?#]/.test(e)?e.slice(1):e).split("&").reduce((function(e,t){var n=t.split("="),a=Object(T.a)(n,2),r=a[0],s=a[1];return e[r]=s?decodeURIComponent(s.replace(/\+/g," ")):"",e}),{}):{}}function C(e,t){var n=w(e);return n.hasOwnProperty(t)&&n[t].length?n[t]:""}function $(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"";return t.length?"/"+(e=u()(e," /"))+(""===(t=u()(t," /"))?"":"/")+t:e}function x(e){if(!e.length||!function(e){return 1===["functions","hooks","classes","methods"].filter((function(t){return e===t})).length}(e))return"";var t=e.substring(0,e.length-1);return"classe"===t?"class":t}function N(e,t){var n="",a=k(e);return t<=a.length&&(n=a[t]),n}function D(e,t){return t===k(e).length}function R(e,t){return e=e.title,t=t.title,e.length!==t.length?e.length-t.length:e<t?-1:1}var L=function(e,t){return RegExp(q(t.trim()),"i").test(e)},q=function(e){return e.replace(/[-\\^$*+?.()|[\]{}]/g,"\\$&")};function P(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"";return(t=t.trim().toLowerCase()).length?e.length?e.filter((function(e){return U(e.title.toLowerCase(),t)})).sort(R):[]:e}function U(e,t){if(L(e,t))return!0;var n=q(e.trim().toLowerCase().replace(/[_\-\s]+/g," ")),a=q(t.trim().toLowerCase().replace(/[_\-\s]+/g," "));if(L(n,a))return!0;var r=(a=a.split(" ")).length;if(1>=r)return!1;for(var s=0,o=0;o<r;o++)-1!==n.indexOf(a[o].trim())&&s++;return s===r}var F=function(e){Object(m.a)(n,e);var t=Object(d.a)(n);function n(e){var a;return Object(p.a)(this,n),(a=t.call(this,e)).onChange=function(e,t){var n=t.newValue;a.setState({value:n})},a.onSuggestionsFetchRequested=function(e){var t=e.value;a.loadSuggestions(t)},a.onSuggestionsClearRequested=function(){a.setState({suggestions:[]})},a.handleSubmit=a.handleSubmit.bind(Object(_.a)(a)),a.state={value:"",suggestions:[],isLoading:!1},a.lastRequestId=null,a}return Object(h.a)(n,[{key:"handleSubmit",value:function(e){var t=this;e.preventDefault();var n=$(this.props.home,this.props.postType),a=this.props.postTypeData[this.props.postType],r=S()(a.content,(function(e){return e.title===t.state.value}));n=-1!==r?n+"/"+a.content[r].slug:n+"/?search="+this.state.value.trim().replace(/\s+/g,"+"),this.props.history.push(n)}},{key:"loadSuggestions",value:function(e){var t=this.props.postType;if(this.setState({isLoading:!0}),this.props.fetchData(t),g()(this.props.postTypeData[t]));else{var n=this.props.postTypeData[t].content;this.setState({isLoading:!1,suggestions:P(n,e)})}}},{key:"renderSuggestion",value:function(e){return r.a.createElement("div",null,e.title)}},{key:"render",value:function(){var e=this.state,t=e.value,n=e.suggestions,a=this.props.postType,s={placeholder:this.props.strings[a+"_search"],value:t,onChange:this.onChange};return r.a.createElement("form",{onSubmit:this.handleSubmit},r.a.createElement(j.a,{suggestions:n,onSuggestionsFetchRequested:this.onSuggestionsFetchRequested,onSuggestionsClearRequested:this.onSuggestionsClearRequested,getSuggestionValue:function(e){return e.title},renderSuggestion:this.renderSuggestion,inputProps:s}),r.a.createElement("input",{type:"submit",value:this.props.strings.submit,id:"submit"}))}}]),n}(a.Component),M=Object(i.g)(F),H=n(22),I=function(e){var t=e.referenceData,n=t.parsed_name,a=t.parsed_version,s=t.app_description,o=e.page,i=e.postType,c=e.home,u="methods"===i?"classes":i,p=e.strings.page_title;n.length&&(p=n,p+=a.length?" "+a:"","home"!==o&&(p=r.a.createElement(l.b,{to:c},p)));var h="";return s.length&&(h=r.a.createElement("p",{className:"site-description"},s)),r.a.createElement("header",{className:"site-header"},r.a.createElement("h1",{className:"site-title"},p),h,-1!==H.indexOf(u)&&r.a.createElement(M,Object.assign({},e,{postType:u})),r.a.createElement("nav",null,r.a.createElement(l.c,{to:c,exact:!0,activeClassName:"active"},e.strings.home),H.map((function(t,n){var a=$(c,t);return"methods"!==t&&r.a.createElement(l.c,{to:a,key:n,activeClassName:"active"},e.strings[t])}))))},V=n(1),Q=function(e){var t=e.page,n=e.children,a="hfeed site devhub-wrap",s=t?a+" "+t:a;return window.scrollTo(0,0),r.a.createElement("div",{id:"page",className:s},r.a.createElement(I,Object.assign({},e,{strings:V})),r.a.createElement("div",{id:"content",className:"site-content"},r.a.createElement("div",{id:"content-area",className:"code-reference"},r.a.createElement("main",{id:"main",className:"site-main",role:"main"},n))))},W=function(e){var t=e.referenceData,n=t.parsed_name,a=t.repo_url,s=t.app_url,o=t.parsed_type,l="";n.length&&(l=V.page_title);var i=V.repo?V.repo:"GitHub",c=o.length?o:V.code_base,u="";c.length&&(u=V.home_desc.replace("%1$s",c));var p="";s&&n&&(p=r.a.createElement("li",null,r.a.createElement("a",{href:s},n)));var h="";return a&&i.length&&(h=r.a.createElement("li",null,r.a.createElement("a",{href:a},i))),r.a.createElement("div",null,l&&r.a.createElement("h2",null,l),r.a.createElement("p",null,u," ",V["home_desc-2"]),(p||h)&&r.a.createElement("ul",null,p,h))},J=E((function(e){return r.a.createElement(Q,Object.assign({},e,{page:"home"}),r.a.createElement(W,e))})),B=n(78),G=n(5),z=n.n(G);function A(e,t,n,a){var r=n.length,s="undocumented"===e,o=x(t),l=!a||1<a?t:o;t=V[l].toLowerCase();var i=a?"%2$s":"%1$s",c=a?"%3$s":"%2$s",u=a?"%2$s":"%1$s",p=a?"":"not_",h=e.length?"filter_version_".concat(p,"found"):"".concat(p,"found"),m=V[h];return(r||s)&&(n=s?V.undocumented:n,h="filter_type_".concat(p,"found"),e.length&&!s&&(h="filter_all_".concat(p,"found"),c=a?"%4$s":"%3$s"),i=a?"%3$s":"%2$s",m=V[h].replace(u,n)),a&&(m=m.replace("%1$d",a)),m.replace(i,l).replace(c,e)}function K(e){var t=e.terms,n=e.version,s=e.postType,o=e.filter,l=e.postCount,i="undocumented"===n;if(g()(t))return null;var c=t.map((function(e,t){return r.a.createElement("option",{key:t,value:e},"undocumented"===e?V.undocumented_version:e)})),u=A(n,s,o,l);return r.a.createElement(a.Fragment,null,r.a.createElement("form",{onSubmit:e.handleSubmit},r.a.createElement("label",null,V.filter_by_version,r.a.createElement("select",{value:n,onChange:function(t){e.onChangeVersion(t.target.value)}},r.a.createElement("option",{key:"none",value:""},V.none),c)),!i&&r.a.createElement("label",null,V.filter_by_type,r.a.createElement("select",{value:o,onChange:function(t){e.onChangeType(t.target.value)}},r.a.createElement("option",{value:"none"},V.none),r.a.createElement("option",{value:"introduced"},V.introduced),r.a.createElement("option",{value:"modified"},V.modified),r.a.createElement("option",{value:"deprecated"},V.deprecated)))),l?"":r.a.createElement("hr",null),r.a.createElement("p",null,u)," ",l?r.a.createElement("hr",null):"")}function X(e){return 1===["introduced","modified","deprecated"].filter((function(t){return e===t})).length}function Y(e){var t=Object(a.useState)(!1),n=Object(T.a)(t,2),s=n[0],o=n[1];if(Object(a.useEffect)((function(){var e=setTimeout((function(){o(!0)}),500);return function(){return clearTimeout(e)}}),[s]),!s)return null;var l=e.hasOwnProperty("message")?e.message:"";return l.length?r.a.createElement("div",null,l):r.a.createElement("div",{className:"loader"},V.loading)}var Z=function(e){return function(t){Object(m.a)(a,t);var n=Object(d.a)(a);function a(){return Object(p.a)(this,a),n.apply(this,arguments)}return Object(h.a)(a,[{key:"componentDidUpdate",value:function(e,t){e.postType!==this.props.postType&&g()(this.props.postTypeData[this.props.postType])&&this.props.fetchData(this.props.postType)}},{key:"componentDidMount",value:function(){g()(this.props.postTypeData[this.props.postType])&&this.props.fetchData(this.props.postType)}},{key:"render",value:function(){var t=z()(this.props,"postTypeData."+this.props.postType+".content",[]);return g()(t)?r.a.createElement(Y,null):(t=t.sort((function(e,t){var n=e.title.toUpperCase(),a=t.title.toUpperCase();return n<a?-1:n>a?1:0})),r.a.createElement(e,Object.assign({},this.props,{content:t})))}}]),a}(r.a.Component)},ee=function(e){Object(m.a)(a,e);var t=Object(d.a)(a);function a(e){var n;return Object(p.a)(this,a),(n=t.call(this,e)).state={type:"",version:"",terms:{},failedRequest:!1},n.handleChangeType=n.handleChangeType.bind(Object(_.a)(n)),n.handleChangeVersion=n.handleChangeVersion.bind(Object(_.a)(n)),n}return Object(h.a)(a,[{key:"setStateFromQuery",value:function(){var e=this.getStateFromQuery();this.setState(e)}},{key:"getStateFromQuery",value:function(){var e=this.props.location.search,t=C(e,"type"),n=this.isSearch();return{type:!n&&X(t)?t:"",version:n?"":C(e,"since")}}},{key:"isSearch",value:function(){var e,t=this.props.location.search;return e="search",!!w(t).hasOwnProperty(e)}},{key:"getSearch",value:function(){return C(this.props.location.search,"search").replace(/\++/g," ").toLowerCase()}},{key:"get_versions",value:function(){var e=this;if(g()(this.state.terms))try{n.e(25).then(n.t.bind(null,215,3)).then((function(t){e.setState({terms:t})}))}catch(t){this.setState({failedRequest:!0})}}},{key:"update_query_string",value:function(){var e=this.props.home+"/"+this.props.postType,t=X(this.state.type)?this.state.type:"",n=this.props.location.search,a=this.state.version.length?"since="+this.state.version+"&":"";(a+=t.length?"type="+t+"&":"",this.isSearch())&&(a="search="+C(n,"search"));((a=a.replace(/\s+/g,"+")).length||n)&&(a="?"+u()(a," &")).length&&n!==a&&(this.props.history.push(e+a),this.props.history.replace({pathname:this.props.location.pathname,search:a,state:this.state}))}},{key:"componentDidUpdate",value:function(e,t){var n=t.version!==this.state.version,a=t.type!==this.state.type;if(n||a)this.update_query_string();else{var r=this.getStateFromQuery(),s=r.version!==this.state.version,o=r.type!==this.state.type;(s||o)&&this.setState(r)}}},{key:"componentDidMount",value:function(){this.setStateFromQuery(),this.get_versions()}},{key:"handleChangeType",value:function(e){(e=X(e)?e:"")!==this.state.type&&this.setState({type:e})}},{key:"handleChangeVersion",value:function(e){e!==this.state.version&&this.setState({version:e})}},{key:"render",value:function(){var e=this,t=this.props,n=t.postType,a=t.home,s=t.content,o=z()(this.state.terms,n,{}),i=this.isSearch(),c=this.getSearch(),u=V.results_for.replace("%1$s",c),p=V[n],h=[],m="",d="";return g()(o)||g()(this.state.version)||(m=-1===o.indexOf(this.state.version)?"":this.state.version),i?(p=V.search_results,h=P(s,c),d=A("",n,"",h.length)):h=function(e,t){var n=arguments.length>2&&void 0!==arguments[2]?arguments[2]:"",a=!t.length;return a&&!n.length?e:e=e.filter((function(e){var r=-1!==e.terms.indexOf(t),s=-1!==e.terms.indexOf(e.introduced),o=-1!==e.terms.indexOf(e.deprecated),l=-1!==e.terms.indexOf("undocumented");switch(n){case"deprecated":return a?e.deprecated:e.deprecated===t;case"introduced":return a?e.introduced:e.introduced===t;case"modified":if(a){var i=e.terms.length;return!(!i||l)&&(i=s?--i:i,0<(i=o?--i:i))}var c=s&&e.introduced===t,u=o&&e.deprecated===t;return r&&!(c||u)}return!!a||r}))}(s,m,this.state.type),r.a.createElement("div",null,r.a.createElement("h2",null,p),i?r.a.createElement("p",null,u):"",i&&!h.length?r.a.createElement("hr",null):"",i?r.a.createElement("p",null,d):"",i&&h.length?r.a.createElement("hr",null):"",!this.isSearch()&&!this.state.failedRequest&&!g()(o)&&r.a.createElement(K,{onChangeType:function(t){return e.handleChangeType(t)},onChangeVersion:function(t){return e.handleChangeVersion(t)},postType:n,terms:o,version:m,filter:this.state.type,postCount:h.length}),h.map((function(t,s){var o="",i=$(a,n+"/"+t.slug);if(t.deprecated){var c=V.deprecated_in.replace("%1$s",t.deprecated);o=r.a.createElement("span",null," \u2014 ",r.a.createElement("span",{className:"deprecated-item"},c))}return r.a.createElement("article",{key:s,className:e.props.postClass},r.a.createElement("h1",null,r.a.createElement(l.b,{to:i},t.title),o),r.a.createElement("div",{className:"description",dangerouslySetInnerHTML:{__html:t.summary}}),r.a.createElement("div",{className:"sourcefile"},r.a.createElement("p",null,V.source_file.replace("%1$s",t.source_file))))})))}}]),a}(r.a.Component),te=Object(B.a)(Z,i.g)(ee),ne=function(e){return e.data.hasOwnProperty("signature")&&e.data.signature.length?r.a.createElement("h1",{dangerouslySetInnerHTML:{__html:e.data.signature}}):null},ae=function(e){return e.element.hasOwnProperty("summary")&&e.element.summary.length?r.a.createElement("section",{className:"summary",dangerouslySetInnerHTML:{__html:e.element.summary}}):null},re=function(e){return e.data.hasOwnProperty("html")&&e.data.html.length?r.a.createElement("div",{dangerouslySetInnerHTML:{__html:e.data.html}}):null},se=function(e){var t=e.element,n=t.line_num,a=t.source_file,s=t.parent,o=t.namespace,i=e.referenceData.repo_release_url;if(!a.length)return null;var c=V.source_file.replace("%1$s",a),p="",h="",m=!1,d="",f="",g="";if(i.length&&(h=V.view_source_file,p=u()(i,"/")+"/"+a,n.length&&(h=V.view_source,p+="#L"+n)),s&&"methods"===e.postType){f=s;var v=e.slug.split("::");2===v.length&&(f=r.a.createElement(l.b,{to:$(e.home,"/classes/"+v[0])},s)),d=r.a.createElement("li",null,V.class,": ",f)}return o.length&&"global"!==o.toLowerCase()&&(g=r.a.createElement("li",null,V.namespace.replace("%1$s",o))),p.length&&h?(m=r.a.createElement("a",{href:p,target:"_blank",rel:"noopener noreferrer"},h),r.a.createElement("ul",{className:"source-info"},d,g,r.a.createElement("li",null,c," \u2014 ",m))):r.a.createElement("ul",{className:"source-info"},d,g,r.a.createElement("li",null,c))},oe=function(e){var t=z()(e,"data.related.uses",{}),n=z()(e,"data.related.used_by",{});if(g()(t)&&g()(n))return null;var a="";g()(t)||(a=r.a.createElement("article",{className:"uses"},r.a.createElement("h3",null,V.uses),r.a.createElement("ul",null,t.map((function(t,n){return r.a.createElement("li",{key:n},r.a.createElement("span",null,t.source)," ",r.a.createElement(l.b,{to:e.home+t.url},t.text))})))));var s="";return g()(n)||(s=r.a.createElement("div",null,r.a.createElement("hr",null),r.a.createElement("article",{className:"used-by"},r.a.createElement("h3",null,V.used_by),r.a.createElement("ul",null,n.map((function(t,n){return r.a.createElement("li",{key:n},r.a.createElement("span",null,t.source)," ",r.a.createElement(l.b,{to:e.home+t.url},t.text))})))))),r.a.createElement("div",null,r.a.createElement("hr",null),r.a.createElement("section",{className:"related"},r.a.createElement("h2",null,V.related),a,s))},le=function(e){var t=e.data.changelog,n=e.archiveUrl;return g()(e.data.changelog)?null:r.a.createElement("div",null,r.a.createElement("hr",null),r.a.createElement("section",{className:"changelog"},r.a.createElement("h3",null,V.changelog),r.a.createElement("table",null,r.a.createElement("caption",{className:"screen-reader-text"},V.changelog),r.a.createElement("thead",null,r.a.createElement("tr",null,r.a.createElement("th",{className:"changelog-version"},V.version),r.a.createElement("th",{className:"changelog-desc"},V.description))),r.a.createElement("tbody",null,t.map((function(e,t){var a=e.version;return g()(n)||(a=r.a.createElement(l.b,{to:n+"?since="+a},a)),r.a.createElement("tr",{key:t},r.a.createElement("td",null,a),r.a.createElement("td",{dangerouslySetInnerHTML:{__html:e.description}}))}))))))},ie=function(e){return e.data.hasOwnProperty("methods")?r.a.createElement("div",null,r.a.createElement("hr",null),r.a.createElement("section",{className:"class-methods"},r.a.createElement("h2",null,V.methods),r.a.createElement("ul",null,e.data.methods.map((function(t,n){return r.a.createElement("li",{key:n},r.a.createElement(l.b,{to:$(e.home,t.url)},t.title)," \u2014 ",r.a.createElement("div",{className:"class-methods-excerpt",dangerouslySetInnerHTML:{__html:t.excerpt}}))}))))):null},ce=function(e){return e.data.hasOwnProperty("notice")&&e.data.notice.length?r.a.createElement("div",{dangerouslySetInnerHTML:{__html:e.data.notice}}):null};var ue=Z((function(e){var t,s=e.postType,o=e.home,l=e.slug,c=e.content,u=S()(c,(function(e){return e.slug===l})),p=-1!==u?c[u]:{};if(t=function(e){var t=Object(a.useState)(e),r=Object(T.a)(t,2),s=r[0],o=r[1],l=Object(a.useState)(!1),i=Object(T.a)(l,2),c=i[0],u=i[1];return Object(a.useEffect)((function(){try{g()(e)?(o(null),u(!0)):n(189)("./"+e+".json").then((function(e){o(e),u(!1)}))}catch(t){o(null),u(!0)}}),[e]),{data:s,failedRequest:c}}(z()(p,"json_file","")),g()(t.data)||g()(p))return t.failedRequest?r.a.createElement(i.a,{to:o}):r.a.createElement(Y,null);var h=z()(p,"slug",""),m=z()(p,"line_num","");if(!h.length||!m.length)return null;var d=z()(t.data,h+"-"+m,{});if(g()(d))return null;var f="";"classes"===s&&(f=r.a.createElement(ie,{element:p,data:d,home:o}));var v=$(o,s);return"methods"===s&&(v=""),r.a.createElement("article",{className:e.postClass},r.a.createElement(ce,{element:p,data:d}),r.a.createElement(ne,{element:p,data:d}),r.a.createElement(ae,{element:p,data:d}),r.a.createElement(se,Object.assign({element:p},e)),r.a.createElement(re,{element:p,data:d}),r.a.createElement(le,{element:p,data:d,archiveUrl:v}),f,r.a.createElement(oe,{element:p,data:d,home:o}))}));var pe=E((function(e){var t=e.location.pathname,n=e.routeIndex,a=e.route.postType,s=$(e.home,e.route.path),o=e.match.isExact?1:2,l=D(t,n+o),c=N(t,o);if(l||"classes"!==a||(l=D(t,n+ ++o),a=l?"methods":a),!l)return r.a.createElement(i.a,{to:e.home});"methods"===a&&(c+="::"+N(t,o));var u=function(e){var t=x(e);return t.length?"wp-parser-"+t:""}(a),p=e.match.isExact?"archive":"single";return r.a.createElement(Q,Object.assign({},e,{postType:a,page:p}),r.a.createElement(i.d,null,r.a.createElement(i.b,{path:s,exact:!0,render:function(t){return r.a.createElement(te,Object.assign({},e,{postType:a,postClass:u}))}}),r.a.createElement(i.b,{path:s+"/:slug",render:function(t){return r.a.createElement(ue,Object.assign({},e,{postType:a,postClass:u,slug:c}))}})))})),he=[{path:"/",postType:"functions",component:J,exact:!0},{path:"/hooks",postType:"hooks",component:pe,exact:!1},{path:"/functions",postType:"functions",component:pe,exact:!1},{path:"/classes",postType:"classes",component:pe,exact:!1}],me=n(48),de=(n(190),n(191),function(e){var t=me.app_basename,n=u()("/"+t),a={appName:t,referenceData:me,home:n,routeIndex:"/"===n?0:1};return r.a.createElement(l.a,null,r.a.createElement(i.d,null,he.map((function(e){return function(e,t){var n=$(t.home,e.path);if(t.home!==n&&-1===H.indexOf(e.postType))return null;var a=e.component;return r.a.createElement(i.b,{path:n,key:n,exact:e.exact,render:function(n){return r.a.createElement(a,Object.assign({},n,t,{postType:e.postType,route:e}))}})}(e,a)})),r.a.createElement(i.a,{to:a.home})))}),fe=Boolean("localhost"===window.location.hostname||"[::1]"===window.location.hostname||window.location.hostname.match(/^127(?:\.(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)){3}$/));function ge(e){navigator.serviceWorker.register(e).then((function(e){e.onupdatefound=function(){var t=e.installing;t.onstatechange=function(){"installed"===t.state&&(navigator.serviceWorker.controller?console.log("New content is available; please refresh."):console.log("Content is cached for offline use."))}}})).catch((function(e){console.error("Error during service worker registration:",e)}))}o.a.render(r.a.createElement(y,null,r.a.createElement(v.Consumer,null,(function(e){var t=e.fetchData;return r.a.createElement(de,{fetchData:t})}))),document.getElementById("root")),function(){if("serviceWorker"in navigator){if(new URL("/related-posts-by-taxonomy",window.location).origin!==window.location.origin)return;window.addEventListener("load",(function(){var e="".concat("/related-posts-by-taxonomy","/service-worker.js");fe?(!function(e){fetch(e).then((function(t){404===t.status||-1===t.headers.get("content-type").indexOf("javascript")?navigator.serviceWorker.ready.then((function(e){e.unregister().then((function(){window.location.reload()}))})):ge(e)})).catch((function(){console.log("No internet connection found. App is running in offline mode.")}))}(e),navigator.serviceWorker.ready.then((function(){console.log("This web app is being served cache-first by a service worker. To learn more, visit https://goo.gl/SC7cgQ")}))):ge(e)}))}}()},22:function(e){e.exports=JSON.parse('["functions","hooks","classes","methods"]')},48:function(e){e.exports=JSON.parse('{"homepage":"https://keesiemeijer.github.io/related-posts-by-taxonomy","app_basename":"related-posts-by-taxonomy","app_description":"Plugin Code Reference","app_url":"https://wordpress.org/plugins/related-posts-by-taxonomy","app_docs_url":"","repo_url":"https://github.com/keesiemeijer/related-posts-by-taxonomy","repo_release_url":"https://github.com/keesiemeijer/related-posts-by-taxonomy/tree/2.7.4","repo_gh_pages":"https://github.com/keesiemeijer/related-posts-by-taxonomy.git","parsed_name":"Related Posts by Taxonomy","parsed_version":"v2.7.4","parsed_type":"plugin"}')},79:function(e,t,n){e.exports=n(192)}},[[79,1,2]]]);
//# sourceMappingURL=main.9be4d1d8.chunk.js.map