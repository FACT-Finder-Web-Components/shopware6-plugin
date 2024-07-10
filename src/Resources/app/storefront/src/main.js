import AsnPlugin from './plugin/asn-plugin';

const PluginManager = window.PluginManager;
PluginManager.register('AsnPlugin', AsnPlugin);
PluginManager.register('TrackingPlugin', () => import('./plugin/tracking.plugin'), '[data-add-to-cart]');
PluginManager.override('OffCanvasFilter', () => import('./plugin/offcanvas-filter.plugin'), '[data-off-canvas-filter]');
