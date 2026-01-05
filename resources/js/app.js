import 'flatpickr/dist/flatpickr.css';
import flatpickr from 'flatpickr';
import {Ukrainian} from 'flatpickr/dist/l10n/uk.js';
import * as bootstrap from 'bootstrap';
import {initMap, renderGeoJson, getMapInstance} from './map/leaflet';

window.LeafletMap = {
    init: initMap,
    render: renderGeoJson,
};

window.initMap = initMap;
window.renderGeoJson = renderGeoJson;
window.bootstrap = bootstrap;

document.addEventListener('livewire:navigated', () => {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
});

window.setupDashboardMap = (el, geoJson) => ({
    initialized: false,

    initMap() {
        if (!this.initialized) {
            window.LeafletMap.init(el, {
                center: [49.2320995, 28.4684847],
                zoom: 16
            });

            window.LeafletMap.render(geoJson);

            this.initialized = true;
        }

        this.resize();
    },

    resize() {
        setTimeout(() => {
            const map = getMapInstance();

            if (map) map.invalidateSize();
        }, 300);
    }
});

window.initDateRangePicker = (el) => {
    const {from, to} = el.dataset;

    flatpickr(el, {
        mode: 'range',
        dateFormat: 'Y-m-d',
        minDate: '2025-01-01',
        maxDate: 'today',
        defaultDate: (from && to) ? [from, to] : null,
        locale: {...Ukrainian, rangeSeparator: ' — '},
        onChange: (dates) => {
            if (dates.length === 2) {
                el.dispatchEvent(new CustomEvent('date-range-selected', {
                    detail: {
                        from: flatpickr.formatDate(dates[0], 'Y-m-d'),
                        to: flatpickr.formatDate(dates[1], 'Y-m-d'),
                    },
                    bubbles: true,
                }));
            }
        }
    });
};

window.addEventListener('mapDataUpdated', (e) => {
    window.LeafletMap.render(Array.isArray(e.detail) ? e.detail[0] : e.detail);
});
