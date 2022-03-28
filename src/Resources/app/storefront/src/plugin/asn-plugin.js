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
            const clickedGroup = e.target.closest('ff-asn-group');

            return [...document.querySelectorAll('ff-asn-group')].filter(g => g !== clickedGroup)
        }

        const isAsnGroup = e => {
             return e.path.find(p => p.tagName === 'ff-asn-group'.toUpperCase());
        }

        if (!isAsnGroup(event)) {
            document.querySelectorAll('ff-asn-group').forEach(g => {
                if (g.opened) g.toggle(true);
            })
        }

        if (isAsnGroup(event)) {
            getAllGroupsExceptClicked(event).forEach(g => {
                if (g.opened) g.toggle(true);
            })
        }
    }
}

