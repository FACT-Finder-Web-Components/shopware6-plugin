import './module/sw-cms/blocks/commerce/listing/listing';
import './module/sw-cms/blocks/commerce/campaigns/campaigns';
import './module/sw-cms/blocks/commerce/filters';
import './module/sw-cms/elements/record-list';
import './module/sw-cms/elements/asn';
import './module/sw-cms/elements/sortbox';
import './module/sw-cms/elements/paging';
import './module/sw-cms/elements/campaigns';
import './module/sw-cms/elements/shared/shared.scss';
import './module/sw-cms/blocks/shared/shared.scss';
import './module/ui-feed-export';
import './module/configuration';
import './init/field-roles-service.init';

import deDECms from './module/sw-cms/snippet/de-DE.json';
import enGBCms from './module/sw-cms/snippet/en-GB.json';

import deDEConfig from './module/configuration/snippet/de-DE.json';
import enGBConfig from './module/configuration/snippet/en-GB.json';

Shopware.Locale.extend('de-DE', deDEConfig);
Shopware.Locale.extend('de-DE', deDECms);
Shopware.Locale.extend('en-GB', enGBConfig);
Shopware.Locale.extend('en-GB', enGBCms);
