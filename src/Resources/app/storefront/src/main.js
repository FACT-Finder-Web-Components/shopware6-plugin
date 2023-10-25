import TrackingPlugin from './plugin/tracking.plugin';
import AsnPlugin from './plugin/asn-plugin';
import OffCanvasFilter from './plugin/offcanvas-filter.plugin';
import './reacting-cookie/reacting-cookie';

const PluginManager = window.PluginManager;
PluginManager.register('TrackingPlugin', TrackingPlugin);
PluginManager.register('AsnPlugin', AsnPlugin);

PluginManager.override('OffCanvasFilter', OffCanvasFilter, '[data-offcanvas-filter]');
