(this.webpackJsonp=this.webpackJsonp||[]).push([["omikron-fact-finder"],{"13wT":function(e,n){e.exports='{% block sw_cms_element_record_list %}\n  <div class="flex-container">\n    <div>FF-RECORD-LIST</div>\n  </div>\n{% endblock %}\n\n'},"14ua":function(e,n){e.exports='{% block cms_block_commerce_factfinder_listing_preview %}\n  <div class="preview-container">\n    FACT-Finder Search Result\n  </div>\n{% endblock %}\n\n'},"4fLr":function(e,n){e.exports='{% block sw_cms_element_record_list_preview %}\n  <div class="preview-container">\n    FF-RECORD-LIST\n  </div>\n{% endblock %}\n'},"7GBO":function(e,n,t){},"8Qy9":function(e,n){e.exports='{% block sw_cms_preview_element_asn %}\n  <div class="preview-container">\n    FF-ASN\n  </div>\n{% endblock %}\n\n'},"9uj7":function(e,n){e.exports='{% block sw_cms_element_campaigns_config %}\n  <div>\n\n    <sw-switch-field label="Advisor Campaign" v-model="element.config.enableAdvisorCampaign.value">\n    </sw-switch-field>\n\n    <sw-container v-if="element.config.enableAdvisorCampaign.value">\n      <sw-text-field\n        label="Advisor Campaign Name"\n        placeholder="Advisor Campaign Name"\n        v-tooltip="$tc(\'sw-cms.elements.campaigns.config.leaveFreeIfNotSpecified\')"\n        v-model="element.config.advisorCampaignName.value">\n      </sw-text-field>\n    </sw-container>\n\n    <sw-switch-field label="Feedback Campaign" v-model="element.config.enableFeedbackCampaign.value">\n    </sw-switch-field>\n\n    <sw-container v-if="element.config.enableFeedbackCampaign.value">\n      <sw-text-field\n        label="Feedback Campaign Label"\n        placeholder="Feedback Campaign Label"\n        v-tooltip="$tc(\'sw-cms.elements.campaigns.config.leaveFreeIfNotSpecified\')"\n        v-model="element.config.feedbackCampaignLabel.value">\n      </sw-text-field>\n    </sw-container>\n\n    <sw-switch-field label="Redirect Campaign" v-model="element.config.enableRedirectCampaign.value">\n    </sw-switch-field>\n  </div>\n{% endblock %}\n'},Bobi:function(e,n){e.exports='{% block sw_cms_element_campaigns %}\n  <div class="flex-container">\n    <div>CAMPAIGNS</div>\n  </div>\n{% endblock %}\n\n'},DOth:function(e,n){e.exports='{% block sw_cms_element_paging_preview %}\n  <div class="preview-container">\n    FF-PAGING\n  </div>\n{% endblock %}\n\n'},F72v:function(e,n,t){var l=t("7GBO");"string"==typeof l&&(l=[[e.i,l,""]]),l.locals&&(e.exports=l.locals);(0,t("SZ7m").default)("2127bda2",l,!0,{})},FDvW:function(e,n,t){"use strict";t.r(n);var l=t("ToaI"),a=t.n(l);const{Component:o}=Shopware;o.register("sw-cms-block-listing",{template:a.a});var i=t("14ua"),s=t.n(i);const{Component:c}=Shopware;c.register("sw-cms-block-listing-preview",{template:s.a}),Shopware.Service("cmsService").registerCmsBlock({name:"listing",label:"sw-cms.blocks.commerce.factfinderWebComponentsListing.label",category:"commerce",component:"sw-cms-block-listing",previewComponent:"sw-cms-block-listing-preview",slots:{toolbarFilters:"asn",toolbarPaging:"paging",toolbarSorting:"sortbox",records:"record-list"}});var r=t("tY33"),m=t.n(r);const{Component:d}=Shopware;d.register("sw-cms-block-campaigns",{template:m.a});var p=t("VoLy"),f=t.n(p);const{Component:g}=Shopware;g.register("sw-cms-block-campaigns-preview",{template:f.a}),Shopware.Service("cmsService").registerCmsBlock({name:"campaigns",label:"sw-cms.blocks.commerce.factfinderWebComponentsCampaigns.label",category:"commerce",component:"sw-cms-block-campaigns",previewComponent:"sw-cms-block-campaigns-preview",slots:{campaigns:"campaigns"}});var b=t("xGP2"),u=t.n(b);const{Component:w}=Shopware;w.register("sw-cms-block-filters",{template:u.a});var v=t("Itec"),h=t.n(v);const{Component:C}=Shopware;C.register("sw-cms-block-filters-preview",{template:h.a}),Shopware.Service("cmsService").registerCmsBlock({name:"filters",label:"sw-cms.blocks.commerce.factfinderWebComponentsFilters.label",category:"commerce",component:"sw-cms-block-filters",previewComponent:"sw-cms-block-filters-preview",defaultConfig:{cssClass:"cms-block-sidebar-filter"},slots:{filters:"asn"}});var x=t("13wT"),k=t.n(x);Shopware.Component.register("sw-cms-el-record-list",{template:k.a,mixins:["cms-element"],created(){this.createdComponent()},methods:{createdComponent(){this.initElementConfig("record-list")}}});var _=t("LxB6"),S=t.n(_);Shopware.Component.register("sw-cms-el-config-record-list",{template:S.a,mixins:["cms-element"],created(){this.createdComponent()},methods:{createdComponent(){this.initElementConfig("record-list")}}});var F=t("4fLr"),y=t.n(F);Shopware.Component.register("sw-cms-el-preview-record-list",{template:y.a}),Shopware.Service("cmsService").registerCmsElement({name:"record-list",label:"sw-cms.elements.recordList.label",component:"sw-cms-el-record-list",configComponent:"sw-cms-el-config-record-list",previewComponent:"sw-cms-el-preview-record-list",defaultConfig:{subscribe:{value:!0,source:"static"},infiniteScrolling:{value:!1,source:"static"},restoreScrollPosition:{value:!1,source:"static"},infiniteDebounceDelay:{value:"32",source:"static"},infiniteScrollMargin:{value:0,source:"static"},callbackArg:{value:"records",source:"static"},callback:{value:"",source:"static"},id:{value:"",source:"static"},domUpdated:{value:"",source:"static"}}});var A=t("jj/O"),L=t.n(A);Shopware.Component.register("sw-cms-el-asn",{template:L.a,mixins:["cms-element"],created(){this.createdComponent()},methods:{createdComponent(){this.initElementConfig("asn")}}});var I=t("XtAn"),N=t.n(I);t("mUJL");Shopware.Component.register("sw-cms-el-config-asn",{template:N.a,mixins:["cms-element"],created(){this.createdComponent()},methods:{createdComponent(){this.initElementConfig("asn")}}});var O=t("8Qy9"),T=t.n(O);Shopware.Component.register("sw-cms-el-preview-asn",{template:T.a}),Shopware.Service("cmsService").registerCmsElement({name:"asn",label:"sw-cms.elements.asn.label",component:"sw-cms-el-asn",configComponent:"sw-cms-el-config-asn",previewComponent:"sw-cms-el-preview-asn",defaultConfig:{subscribe:{value:!0,source:"static"},vertical:{value:!1,source:"static"},topic:{value:"asn",source:"static"},callbackArg:{value:"groups",source:"static"},callback:{value:"",source:"static"},id:{value:"",source:"static"},domUpdated:{value:"",source:"static"},filterCloud:{value:!0,source:"static"},filterCloudBlacklist:{value:"",source:"static"},filterCloudWhitelist:{value:"",source:"static"},filterCloudOrder:{value:"fact-finder",source:"static"}}});var W=t("Xq3Z"),D=t.n(W);Shopware.Component.register("sw-cms-el-sortbox",{template:D.a,mixins:["cms-element"],created(){this.createdComponent()},methods:{createdComponent(){this.initElementConfig("sortbox")}}});var E=t("mIpn"),G=t.n(E);Shopware.Component.register("sw-cms-el-config-sortbox",{template:G.a,mixins:["cms-element"],created(){this.createdComponent()},methods:{createdComponent(){this.initElementConfig("sortbox")}}});var V=t("xOnl"),B=t.n(V);Shopware.Component.register("sw-cms-el-preview-sortbox",{template:B.a}),Shopware.Service("cmsService").registerCmsElement({name:"sortbox",label:"sw-cms.elements.sortbox.label",component:"sw-cms-el-sortbox",configComponent:"sw-cms-el-config-sortbox",previewComponent:"sw-cms-el-preview-sortbox",defaultConfig:{subscribe:{value:!0,source:"static"},opened:{value:!0,source:"static"},showSelected:{value:!1,source:"static"},showSelectedFirst:{value:!1,source:"static"},collapseOnblur:{value:!1,source:"static"}}});var $=t("oINR"),U=t.n($);Shopware.Component.register("sw-cms-el-paging",{template:U.a,mixins:["cms-element"],created(){this.createdComponent()},methods:{createdComponent(){this.initElementConfig("paging")}}});var R=t("tlS5"),M=t.n(R);Shopware.Component.register("sw-cms-el-config-paging",{template:M.a,mixins:["cms-element"],created(){this.createdComponent()},methods:{createdComponent(){this.initElementConfig("paging")}}});var P=t("DOth"),z=t.n(P);Shopware.Component.register("sw-cms-el-preview-paging",{template:z.a}),Shopware.Service("cmsService").registerCmsElement({name:"paging",label:"sw-cms.elements.paging.label",component:"sw-cms-el-paging",configComponent:"sw-cms-el-config-paging",previewComponent:"sw-cms-el-preview-paging",defaultConfig:{subscribe:{value:!0,source:"static"},showOnly:{value:"true",source:"static"}}});var J=t("Bobi"),j=t.n(J);Shopware.Component.register("sw-cms-el-campaigns",{template:j.a,mixins:["cms-element"],created(){this.createdComponent()},methods:{createdComponent(){this.initElementConfig("campaigns")}}});var X=t("9uj7"),Z=t.n(X);Shopware.Component.register("sw-cms-el-config-campaigns",{template:Z.a,mixins:["cms-element"],created(){this.createdComponent()},methods:{createdComponent(){this.initElementConfig("campaigns")}}});var q=t("clpl"),Y=t.n(q);Shopware.Component.register("sw-cms-el-preview-campaigns",{template:Y.a}),Shopware.Service("cmsService").registerCmsElement({name:"campaigns",label:"sw-cms.elements.campaigns.label",component:"sw-cms-el-campaigns",configComponent:"sw-cms-el-config-campaigns",previewComponent:"sw-cms-el-preview-campaigns",defaultConfig:{advisorCampaignName:{value:"",source:"static"},feedbackCampaignLabel:{value:"",source:"static"},enableFeedbackCampaign:{value:"",source:"static"},enableAdvisorCampaign:{value:"",source:"static"},enableRedirectCampaign:{value:"",source:"static"}}});t("F72v"),t("Gh/0");var K=t("l6e+"),Q=t.n(K);const{Component:H,Mixin:ee}=Shopware;H.register("ui-feed-export-form",{template:Q.a,data:()=>({salesChannelValue:null,salesChannelLanguageValue:null,exportTypeValue:null}),mixins:[ee.getByName("notification")],methods:{successFeedGenerationWindow(){this.createNotificationSuccess({message:this.$tc("ui-feed-export.component.export_form.alert_success.text")})},errorFeedGenerationWindow(){this.createNotificationError({message:this.$tc("ui-feed-export.component.export_form.alert_error.text")})},getFeedExportFile(e){const n=Shopware.Service("syncService").httpClient,t={Authorization:`Bearer ${Shopware.Context.api.authToken.access}`,"Content-Type":"application/json"},l={salesChannelValue:this.salesChannelValue,salesChannelLanguageValue:this.salesChannelLanguageValue,exportTypeValue:this.exportTypeValue};n.get(e,{headers:t,params:l}).then((e=>{200===e.status?this.successFeedGenerationWindow():this.errorFeedGenerationWindow()}))}}});var ne=t("yzdu"),te=t.n(ne);const{Component:le}=Shopware;le.register("ui-feed-export-index",{template:te.a,metaInfo(){return{title:this.$createTitle()}}});var ae=t("t1Na"),oe=t("Gw9K");const{Module:ie}=Shopware;ie.register("ui-feed-export",{color:"#ff3d58",icon:"default-shopping-paper-bag-product",title:"ui-feed-export.title",description:"",snippets:{"de-DE":ae,"en-GB":oe},routes:{index:{component:"ui-feed-export-index",path:"index"}},navigation:[{label:"ui-feed-export.title",path:"ui.feed.export.index",position:100,parent:"sw-extension"}]});var se=t("RGSx"),ce=t("eVqi");Shopware.Locale.extend("de-DE",se),Shopware.Locale.extend("en-GB",ce)},"Gh/0":function(e,n,t){var l=t("lU4u");"string"==typeof l&&(l=[[e.i,l,""]]),l.locals&&(e.exports=l.locals);(0,t("SZ7m").default)("6ed01b73",l,!0,{})},Gw9K:function(e){e.exports=JSON.parse('{"ui-feed-export":{"title":"FACT-Finder® Feed Export","component":{"export_form":{"sales_channel":{"label":"Sales Channel"},"sales_channel_language":{"label":"Language"},"button":{"title":"Run Integration"},"alert_success":{"text":"Integration process has been started"},"alert_error":{"text":"An error occurred during integration process"},"export_type":{"label":"Select export type"}}}}}')},Itec:function(e,n){e.exports='{% block cms_block_commerce_factfinder_filters_preview %}\n  <div class="preview-container">\n    FACT-Finder Filters\n  </div>\n{% endblock %}\n\n'},LFlY:function(e,n,t){},LxB6:function(e,n){e.exports='{% block sw_cms_element_record_list_config %}\n  <div>\n    <sw-switch-field label="Subscribe" v-model="element.config.subscribe.value"></sw-switch-field>\n\n    <sw-switch-field label="Infinite Scrolling" v-model="element.config.infiniteScrolling.value"></sw-switch-field>\n\n    <sw-text-field\n      label="Debounce Delay"\n      placeholder="Debounce Delay"\n      v-model="element.config.infiniteDebounceDelay.value">\n    </sw-text-field>\n\n    <sw-text-field label="ID"\n                   placeholder="ID"\n                   v-tooltip="{ message:$tc(\'sw-cms.elements.recordList.config.id\')}"\n                   v-model="element.config.id.value">\n    </sw-text-field>\n\n    <sw-text-field label="Callback Argument"\n                   placeholder="Callback Argument"\n                   v-tooltip="{ message:$tc(\'sw-cms.elements.recordList.config.callbackArg\')}"\n                   v-model="element.config.callbackArg.value">\n    </sw-text-field>\n\n    <sw-textarea-field label="Add Callback"\n                       placeholder="Callback"\n                       size="medium"\n                       v-model="element.config.callback.value"\n                       v-tooltip="{ message:$tc(\'sw-cms.elements.recordList.config.callback\')}">\n    </sw-textarea-field>\n\n    <sw-textarea-field label="Dom Updated"\n                       placeholder="Dom Updated"\n                       size="medium"\n                       v-model="element.config.domUpdated.value"\n                       v-tooltip="{ message:$tc(\'sw-cms.elements.recordList.config.domUpdated\')}">\n    </sw-textarea-field>\n  </div>\n{% endblock %}\n'},RGSx:function(e){e.exports=JSON.parse('{"sw-cms":{"blocks":{"commerce":{"factfinderWebComponentsListing":{"label":"FACTFinder Web Components Search Result"},"factfinderWebComponentsCampaigns":{"label":"FACTFinder Web Components Campaigns"},"factfinderWebComponentsFilters":{"label":"FACTFinder Web Components Filters"}}},"elements":{"recordList":{"label":"ff-record-list","config":{"callbackArg":"Name of argument which will be available inside the callback scope","callback":"callback to the subscribed topic. It is recommended to have only one callback per topic, per page.","domUpdated":"listener to dom-update event of that element.","id":"Value will be passed as `id` attribute to element. If not specified, the default CMS element id will be used"}},"asn":{"label":"ff-asn","asn":{"label":"ff-asn","config":{"callbackArg":"Name of argument which will be available inside the callback scope","callback":"callback to the subscribed topic. It is recommended to have only one callback per topic, per page.","domUpdated":"listener to dom-update event of that element.","id":"Value will be passed as `id` attribute to element. If not specified, the default CMS element id will be used","topic":"Leaving this field empty causes element subscribe to its default topic (asn)","vertical":"Setting to true will add additional CSS class `btn-block` to the `ff-asn-group` and `<div slot=\\"groupCaption\\"` and ffw-asn-vertical, ffw-asn-group-vertical and ffw-asn-group-element-vertical to corresponding elements"}},"sortbox":{"label":"ff-sortbox"},"paging":{"label":"ff-paging"},"campaigns":{"label":"campaigns","config":{"leaveFreeIfNotSpecified":"Leave this value free if you want page to accept all campaigns matching the criteria"}}}}}}')},ToaI:function(e,n){e.exports='{% block cms_block_commerce_factfinder_listing %}\n  <div>\n    <slot name="toolbarFilters"></slot>\n    <div class="flex-container">\n      <slot name="toolbarPaging"></slot>\n      <slot name="toolbarSorting"></slot>\n    </div>\n    <slot name="records"></slot>\n  </div>\n{% endblock %}\n'},VoLy:function(e,n){e.exports='{% block cms_block_commerce_factfinder_campaigns_preview %}\n  <div class="preview-container">\n    FACT-Finder Campaigns\n  </div>\n{% endblock %}\n\n'},Xq3Z:function(e,n){e.exports='{% block sw_cms_element_sortbox %}\n  <div class="flex-container">\n    <div>FF-SORTBOX</div>\n  </div>\n{% endblock %}\n\n'},XtAn:function(e,n){e.exports='{% block sw_cms_element_asn_config %}\n  <div>\n    <sw-switch-field label="Subscribe" v-model="element.config.subscribe.value"></sw-switch-field>\n\n    <sw-switch-field label="Vertical"\n                     v-tooltip="{ message:$tc(\'sw-cms.elements.asn.config.veritcal\')}"\n                     v-model="element.config.vertical.value">\n    </sw-switch-field>\n\n    <sw-text-field label="ID"\n                   placeholder="ID"\n                   v-tooltip="{ message:$tc(\'sw-cms.elements.asn.config.id\')}"\n                   v-model="element.config.id.value">\n    </sw-text-field>\n\n    <sw-text-field label="Topic"\n                   placeholder="Topic"\n                   v-tooltip="{ message:$tc(\'sw-cms.elements.asn.config.topic\')}"\n                   v-model="element.config.topic.value">\n    </sw-text-field>\n\n    <sw-text-field label="Callback Argument"\n                   placeholder="Callback Argument"\n                   v-tooltip="{ message:$tc(\'sw-cms.elements.asn.config.callbackArg\')}"\n                   v-model="element.config.callbackArg.value">\n    </sw-text-field>\n    <sw-textarea-field label="Callback"\n                       placeholder="Callback"\n                       size="medium"\n                       v-model="element.config.callback.value"\n                       v-tooltip="{ message:$tc(\'sw-cms.elements.asn.config.callback\')}">\n    </sw-textarea-field>\n    <sw-textarea-field label="Dom Updated"\n                       placeholder="Dom Updated"\n                       size="medium"\n                       v-model="element.config.domUpdated.value"\n                       v-tooltip="{ message:$tc(\'sw-cms.elements.asn.config.domUpdated\')}">\n    </sw-textarea-field>\n\n    <wrapper class="secondary">\n      <h3 class="filter-cloud">Filter Cloud</h3>\n      <sw-switch-field label="Use Filter Cloud" v-model="element.config.filterCloud.value"></sw-switch-field>\n\n      <sw-container v-if="element.config.filterCloud.value">\n        <sw-text-field label="Blacklist"\n                       placeholder="Blacklist"\n                       v-model="element.config.filterCloudBlacklist.value"></sw-text-field>\n\n        <sw-text-field label="Whitelist"\n                       placeholder="Whitelist"\n                       v-model="element.config.filterCloudWhitelist.value"></sw-text-field>\n\n        <sw-select-field label="Order" placeholder="Order" v-model="element.config.filterCloudOrder.value">\n          <option value="fact-finder">factfinder</option>\n          <option value="alphabetical">alphabetical</option>\n          <option value="user-selection">userSelection</option>\n        </sw-select-field>\n      </sw-container>\n    </wrapper>\n  </div>\n{% endblock %}\n'},clpl:function(e,n){e.exports='{% block sw_cms_element_campaigns_preview %}\n  <div class="preview-container">\n    CAMPAIGNS\n  </div>\n{% endblock %}\n\n'},eVqi:function(e){e.exports=JSON.parse('{"sw-cms":{"blocks":{"commerce":{"factfinderWebComponentsListing":{"label":"FACTFinder Web Components Search Result"},"factfinderWebComponentsCampaigns":{"label":"FACTFinder Web Components Campaigns"},"factfinderWebComponentsFilters":{"label":"FACTFinder Web Components Filters"}}},"elements":{"recordList":{"label":"ff-record-list","config":{"callbackArg":"Name of argument which will be available inside the callback scope","callback":"callback to the subscribed topic. It is recommended to have only one callback per topic, per page.","domUpdated":"listener to dom-update event of that element.","id":"Value will be passed as `id` attribute to element. If not specified, the default CMS element id will be used"}},"asn":{"label":"ff-asn","config":{"callbackArg":"Name of argument which will be available inside the callback scope","callback":"callback to the subscribed topic. It is recommended to have only one callback per topic, per page.","domUpdated":"listener to dom-update event of that element.","id":"Value will be passed as `id` attribute to element. If not specified, the default CMS element id will be used","topic":"Leaving this field empty causes element subscribe to its default topic (asn)","vertical":"Setting to true will add additional CSS class `btn-block` to the `ff-asn-group` and `<div slot=\\"groupCaption\\"` and ffw-asn-vertical, ffw-asn-group-vertical and ffw-asn-group-element-vertical to corresponding elements"}},"sortbox":{"label":"ff-sortbox"},"paging":{"label":"ff-paging"},"campaigns":{"label":"campaigns","config":{"leaveFreeIfNotSpecified":"Leave this value free if you want page to accept all campaigns matching the criteria"}}}}}')},"jj/O":function(e,n){e.exports='{% block sw_cms_element_asn %}\n  <div class="flex-container">\n    <div>FF-ASN</div>\n  </div>\n{% endblock %}\n\n'},"l6e+":function(e,n){e.exports='<sw-card-view>\n\n  <sw-card>\n    <sw-container columns="repeat(auto-fit, minmax(250px, 1fr)" gap="0px 30px">\n      <sw-entity-single-select\n        v-model="salesChannelValue"\n        entity="sales_channel"\n        id="sales_channel"\n        :label="$tc(\'ui-feed-export.component.export_form.sales_channel.label\')">\n      </sw-entity-single-select>\n\n      <sw-entity-single-select\n        v-model="salesChannelLanguageValue"\n        entity="language"\n        id="sales_channel_language"\n        :label="$tc(\'ui-feed-export.component.export_form.sales_channel_language.label\')">\n      </sw-entity-single-select>\n\n      <sw-select-field placeholder="placeholder goes here..."\n                       id="export_type"\n                       v-model="exportTypeValue"\n                       :label="$tc(\'ui-feed-export.component.export_form.export_type.label\')">\n        <option value="products">Products</option>\n        <option value="cms">CMS</option>\n        <option value="brands">Brands</option>\n      </sw-select-field>\n\n    </sw-container>\n    <sw-button @click="getFeedExportFile(\'_action/fact-finder/generate-feed\')"\n               variant="success"\n               block="true"\n               size="large">\n\n      {{ $tc(\'ui-feed-export.component.export_form.button.title\') }}\n    </sw-button>\n  </sw-card>\n</sw-card-view>\n'},lU4u:function(e,n,t){},mIpn:function(e,n){e.exports='{% block sw_cms_element_sortbox_config %}\n  <div>\n    <sw-switch-field label="subscribe" v-model="element.config.subscribe.value"></sw-switch-field>\n    <sw-switch-field label="opened" v-model="element.config.showSelected.value"></sw-switch-field>\n    <sw-switch-field label="show selected" v-model="element.config.showSelectedFirst.value"></sw-switch-field>\n    <sw-switch-field label="show selected first" v-if="element.config.showSelected.value" v-model="element.config.showSelectedFirst.value"></sw-switch-field>\n    <sw-switch-field label="collapse onblur" v-model="element.config.collapseOnblur.value"></sw-switch-field>\n  </div>\n{% endblock %}\n'},mUJL:function(e,n,t){var l=t("LFlY");"string"==typeof l&&(l=[[e.i,l,""]]),l.locals&&(e.exports=l.locals);(0,t("SZ7m").default)("cfe5862a",l,!0,{})},oINR:function(e,n){e.exports='{% block sw_cms_element_paging %}\n  <div class="flex-container">\n    <div>FF-PAGING</div>\n  </div>\n{% endblock %}\n\n'},t1Na:function(e){e.exports=JSON.parse('{"ui-feed-export":{"title":"FACT-Finder® Feed Export","component":{"export_form":{"sales_channel":{"label":"Sales Channel"},"sales_channel_language":{"label":"Language"},"button":{"title":"Run Integration"},"alert_success":{"text":"Integration process has been started"},"alert_error":{"text":"An error occurred during integration process"}}}}}')},tY33:function(e,n){e.exports='{% block cms_block_commerce_factfinder_campaigns %}\n      <div class="flex-container">\n        <slot name="campaigns"></slot>\n      </div>\n{% endblock %}\n'},tlS5:function(e,n){e.exports='{% block sw_cms_element_paging_config %}\n  <div>\n    <sw-text-field\n      label="Show Only"\n      placeholder="Show Only"\n      v-model="element.config.showOnly.value">\n    </sw-text-field>\n    <sw-switch-field label="Subscribe" v-model="element.config.subscribe.value"></sw-switch-field>\n  </div>\n{% endblock %}\n'},xGP2:function(e,n){e.exports='{% block cms_block_commerce_factfinder_filters %}\n      <div class="flex-container">\n        <slot name="filters"></slot>\n      </div>\n{% endblock %}\n'},xOnl:function(e,n){e.exports='{% block sw_cms_element_sortbox_preview %}\n  <div class="preview-container">\n    FF-SORTBOX\n  </div>\n{% endblock %}\n'},yzdu:function(e,n){e.exports='{% block factfinder_ui_feed_export_index %}\n  <sw-page class="ui-feed-export-index">\n    <template slot="content">\n      <ui-feed-export-form></ui-feed-export-form>\n    </template>\n  </sw-page>\n{% endblock %}\n'}},[["FDvW","runtime","vendors-node"]]]);