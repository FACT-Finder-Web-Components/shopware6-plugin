import TrackingPlugin from './plugin/tracking.plugin';
import AsnPlugin from './plugin/asn-plugin';

const PluginManager = window.PluginManager;
PluginManager.register('TrackingPlugin', TrackingPlugin);
PluginManager.register('AsnPlugin', AsnPlugin);
PluginManager.override('OffCanvasFilter', () => import('./plugin/offcanvas-filter.plugin'), '[data-off-canvas-filter]');
