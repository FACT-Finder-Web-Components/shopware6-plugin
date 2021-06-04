import './module/sw-cms/blocks/commerce/listing/listing';
import './module/sw-cms/blocks/commerce/campaigns/campaigns';
import './module/sw-cms/elements/record-list';
import './module/sw-cms/elements/asn';
import './module/sw-cms/elements/sortbox';
import './module/sw-cms/elements/paging';
import './module/sw-cms/elements/campaigns';
import './module/sw-cms/elements/shared/shared.scss'

import deDE from './module/sw-cms/snippet/de-DE.json';
import enGB from './module/sw-cms/snippet/en-GB.json';

Shopware.Locale.extend('de-DE', deDE);
Shopware.Locale.extend('en-GB', enGB);
