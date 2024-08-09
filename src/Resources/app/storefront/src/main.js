import AsnPlugin from './plugin/asn-plugin';

const PluginManager = window.PluginManager;
PluginManager.register('AsnPlugin', AsnPlugin);
PluginManager.register('TrackingPlugin', () => import('./plugin/tracking.plugin'), '[data-add-to-cart]');
PluginManager.register('FFOffCanvasFilter', () => import('./plugin/offcanvas-filter.plugin'), '[data-ff-off-canvas-filter]');
