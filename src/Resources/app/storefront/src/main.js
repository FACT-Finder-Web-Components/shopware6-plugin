import TrackingPlugin from './plugin/tracking.plugin';
import AsnPlugin from './plugin/asn-plugin';
import OffCanvasFilter from './plugin/offcanvas-filter.plugin';

const PluginManager = window.PluginManager;
PluginManager.register('TrackingPlugin', TrackingPlugin);
PluginManager.register('AsnPlugin', AsnPlugin);
// PluginManager.override('OffCanvasFilter', OffCanvasFilter, '[data-off-canvas-filter]');
PluginManager.override('OffCanvasFilter', () => import('./plugin/offcanvas-filter.plugin'), '[data-off-canvas-filter]');

