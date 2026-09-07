import Plugin from 'src/plugin-system/plugin.class';

export default class AsnPlugin extends Plugin
{
    init() {
        this.registerEvents();
    }

    registerEvents() {
        document.addEventListener('click', this._handleToggleFilter.bind(this), true);
    }

    _handleToggleFilter(event) {
        const path = (event.composedPath && event.composedPath()) || this._eventPath(event);

        const clickedGroup = path.find(p =>
            p && p.tagName && (p.tagName === 'FF-ASN-GROUP' || p.tagName === 'FF-ASN-GROUP-SLIDER')
        );

        const allGroups = document.querySelectorAll('ff-asn-group, ff-asn-group-slider');

        if (!clickedGroup) {
            allGroups.forEach(g => {
                if (g.opened) {
                    g.toggle(true);
                }
            });
        } else {
            allGroups.forEach(g => {
                if (g !== clickedGroup && g.opened) {
                    g.toggle(true);
                }
            });
        }
    }

    _eventPath(evt) {
        var path = (evt.composedPath && evt.composedPath()) || evt.path,
            target = evt.target;

        if (path != null) {
            return (path.indexOf(window) < 0) ? path.concat(window) : path;
        }

        if (target === window) {
            return [window];
        }

        function getParents(node, memo) {
            memo = memo || [];
            var parentNode = node.parentNode;

            if (!parentNode) {
                return memo;
            }
            else {
                return getParents(parentNode, memo.concat(parentNode));
            }
        }

        return [target].concat(getParents(target), window);
    }
}
