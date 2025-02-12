import AsnPlugin from './plugin/asn-plugin';
import TrackingPlugin from './plugin/tracking.plugin';

const PluginManager = window.PluginManager;
PluginManager.register('AsnPlugin', AsnPlugin);
PluginManager.register('TrackingPlugin', TrackingPlugin);
PluginManager.register('FFOffCanvasFilter', () => import('./plugin/offcanvas-filter.plugin'), '[data-ff-off-canvas-filter]');
