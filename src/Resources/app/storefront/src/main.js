import TrackingPlugin from './plugin/tracking.plugin';

const PluginManager = window.PluginManager;
PluginManager.register('TrackingPlugin', TrackingPlugin)
