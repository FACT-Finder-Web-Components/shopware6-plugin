import OffCanvasFilterPlugin from 'src/plugin/offcanvas-filter/offcanvas-filter.plugin';
import OffCanvas from 'src/plugin/offcanvas/offcanvas.plugin';

export default class OffCanvasFilter extends OffCanvasFilterPlugin
{
    ASNMobileClass = 'ffw-asn-vertical';
    ASNGroupMobileClass = 'ffw-asn-group-vertical';
    ASNGroupElementMobileClass = 'ffw-asn-group-element-vertical';

    init() {
        this._registerEventListeners();
    }

    /**
     * Register events to handle opening the Detail Filter OffCanvas
     * by clicking a defined trigger selector
     * @private
     */
    _registerEventListeners() {
        this.el.addEventListener('click', this._onClickOffCanvasFilter.bind(this));
    }

    _onCloseOffCanvas(event) {
        setTimeout(() => {
            const filterContent = event.detail.offCanvasContent && event.detail.offCanvasContent[0];
            if (!filterContent) {
                throw Error('There was nothing passed as `event.detail.offCanvasContent` in the `onCloseOffcanvas` event');
            }
            this._toggleASNMobileMode(filterContent.querySelector('ff-asn'));
            const originPosition = document.querySelector('#filtersOrigin');
            originPosition.appendChild(filterContent);
        });

        document.$emitter.unsubscribe('onCloseOffcanvas', this._onCloseOffCanvas.bind(this));
    }

    _onClickOffCanvasFilter(event) {
        event.preventDefault();

        const filterContent = document.querySelector('[data-off-canvas-filter-content="true"]');
        this._toggleASNMobileMode(filterContent.querySelector('ff-asn'));
        if (!filterContent) {
            throw Error('There was no DOM element with the data attribute "data-off-canvas-filter-content".');
        }

        //open canvas but don't pass asn or filter cloud html as it will cause the new element to create and initiate with no data
        OffCanvas.open('', () => {}, 'bottom', true, OffCanvas.REMOVE_OFF_CANVAS_DELAY(), true, 'offcanvas-filter');

        setTimeout(() => {
            const offCanvas = document.querySelector('.offcanvas');
            offCanvas.appendChild(filterContent);
        });

        document.$emitter.subscribe('onCloseOffcanvas', this._onCloseOffCanvas.bind(this));

        this.$emitter.publish('onClickOffCanvasFilter');
    }

    _toggleASNMobileMode(asnInstance,) {

        const modifyClasses =  operation => instance => classes => instance.classList[operation](...classes);
        const addClasses = modifyClasses('add');
        const removeClasses = modifyClasses('remove');

        asnInstance.querySelectorAll('ff-asn-group').forEach(group => {
            const caption = group.querySelector('[slot="groupCaption"]');
            const elements = group.querySelectorAll('ff-asn-group-element');

            const groupClassList = [this.ASNGroupMobileClass, 'btn-block'];
            group.classList.contains(this.ASNGroupMobileClass) ? modifyClasses(group)(groupClassList) : addClasses(group)(groupClassList);
            caption.classList.contains('btn-block') ? removeClasses(caption)(['btn-block']) : addClasses(caption)(['btn-block']);


            elements.forEach(element => {
                element.classList.contains(this.ASNGroupElementMobileClass)
                ? removeClasses(element)([this.ASNGroupElementMobileClass])
                : addClasses(element)([this.ASNGroupElementMobileClass]);
            });
        });
    }
}
