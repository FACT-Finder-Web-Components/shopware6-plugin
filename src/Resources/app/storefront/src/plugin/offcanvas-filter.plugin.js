import OffCanvasFilterPlugin from 'src/plugin/offcanvas-filter/offcanvas-filter.plugin';
import OffCanvas from 'src/plugin/offcanvas/offcanvas.plugin';

export default class OffCanvasFilter extends OffCanvasFilterPlugin
{
    ASNMobileClass = 'ffw-asn-vertical';
    ASNGroupMobileClass = 'ffw-asn-group-vertical';
    ASNGroupElementMobileClass = 'ffw-asn-group-element-vertical';

    init() {
        this._registerEventListeners();
        this._setupFilterVisibility();
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
            const filterContent = event.detail.offCanvasContent && event.detail.offCanvasContent[0].children[0];
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

            const asnGroupSlider = filterContent.querySelector('ff-asn-group-slider');

            if (asnGroupSlider && typeof asnGroupSlider._asnGroupChanged === 'function') {
                asnGroupSlider._asnGroupChanged();
            }
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

    // Fix ff-asn-remove-all-filters issue on mobile view (in the future this issue should be fixed in the webcomponents library)
    _setupFilterVisibility() {
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.addedNodes.length) {
                    const filterContainer = document.querySelector('.filter-panel-active-container');
                    if (filterContainer) {
                        this._initializeFilterVisibility();
                    }
                }
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });

        const filterContainer = document.querySelector('.filter-panel-active-container');
        if (filterContainer) {
            this._initializeFilterVisibility();
        }

        document.addEventListener('ffCoreReady', () => {
            this._updateFilterVisibility();
        });
    }

    _initializeFilterVisibility() {
        const filterContainer = document.querySelector('.filter-panel-active-container');
        const filterCloud = filterContainer?.querySelector('ff-filter-cloud');
        const resetButton = filterContainer?.querySelector('ff-asn-remove-all-filters');

        if (!filterContainer || !filterCloud || !resetButton) {
            return;
        }

        this.filterCloud = filterCloud;
        this.resetButton = resetButton;

        this._updateFilterVisibility();

        if (this.filterObserver) {
            this.filterObserver.disconnect();
        }
        this.filterObserver = new MutationObserver(() => {
            this._updateFilterVisibility();
        });
        this.filterObserver.observe(filterCloud, {
            childList: true,
            subtree: true
        });
    }

    _updateFilterVisibility() {
        const filterContainer = document.querySelector('.filter-panel-active-container');
        if (!filterContainer || !document.body.contains(this.filterCloud) || !document.body.contains(this.resetButton)) {
            return;
        }

        const activeFilters = this.filterCloud.querySelectorAll('span.filter-active[data-template="filter"]');
        if (activeFilters.length > 0) {
            this.resetButton.classList.remove('ffw-hidden');
            this.resetButton.style.display = 'inline-block';
        } else {
            this.resetButton.classList.add('ffw-hidden');
            this.resetButton.style.display = 'none';
        }
    }
}
