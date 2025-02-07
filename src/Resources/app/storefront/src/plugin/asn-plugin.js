import Plugin from 'src/plugin-system/plugin.class';

export default class AsnPlugin extends Plugin
{
    init() {
        this.registerEvents();
    }

    registerEvents() {
        document.addEventListener('click', this._handleToggleFilter.bind(this));
    }

    _handleToggleFilter(event) {
        const getAllGroupsExceptClicked = e => {
            const clickedGroup = e.target.closest('ff-asn-group, ff-asn-group-slider');

            return [...document.querySelectorAll('ff-asn-group, ff-asn-group-slider')].filter(g => g !== clickedGroup)
        }

        const isAsnGroup = e => {
            return this._eventPath(e).find(p => p.tagName === 'ff-asn-group'.toUpperCase()
                || p.tagName === 'ff-asn-group-slider'.toUpperCase());
        }

        if (!isAsnGroup(event)) {
            document.querySelectorAll('ff-asn-group, ff-asn-group-slider').forEach(g => {
                if (g.opened) g.toggle(true);
            })
        }

        if (isAsnGroup(event)) {
            getAllGroupsExceptClicked(event).forEach(g => {
                if (g.opened) g.toggle(true);
            })
        }
    }

    _eventPath(evt) {
        var path = (evt.composedPath && evt.composedPath()) || evt.path,
            target = evt.target;

        if (path != null) {
            // Safari doesn't include Window, but it should.
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

