import L from 'leaflet';
import 'leaflet-polylinedecorator';

window.L = L;

let map = null;
let layer = null;
let decorator = null;

const CONFIG = {
    tileLayer: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
    lineStyle: {color: '#87b9f6', weight: 1, opacity: 0.6},
    fitBoundsOptions: {padding: [50, 50], maxZoom: 16},
    arrowOptions: {
        offset: '5%',
        repeat: 100,
        symbol: L.Symbol.arrowHead({
            pixelSize: 10,
            pathOptions: {fillOpacity: 1, color: '#3388ff', weight: 0}
        })
    }
};

export function initMap(el, options = {}) {
    if (map) return;

    map = L.map(el, {zoomControl: true, attributionControl: true})
        .setView(options.center, options.zoom);

    L.tileLayer(CONFIG.tileLayer, {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    layer = L.geoJson(null, {
        style: (f) => f.geometry.type === 'LineString' ? CONFIG.lineStyle : {},
        pointToLayer: (f, latlng) => L.circleMarker(latlng, {
            radius: 10,
            fillColor: f.properties.planned ? '#059669' : '#3b82f6',
            weight: 1,
            opacity: 1,
            fillOpacity: 0.5
        }),
        onEachFeature: (f, l) => {
            if (f.properties) {
                const {id, address, checked_at} = f.properties;
                l.bindPopup(`
                    <div class="p-2">
                        <strong>ID:</strong> ${id}<br>
                        <strong>Адреса:</strong> ${address}<br>
                        <strong>Перевірено:</strong> ${checked_at || 'Не перевірено'}
                    </div>
                `);
            }
        }
    }).addTo(map);
}

export function renderGeoJson(data) {
    if (!layer || !data) return;

    layer.clearLayers();

    if (decorator) map.removeLayer(decorator);

    layer.addData(data);

    const bounds = layer.getBounds();

    if (bounds.isValid()) map.fitBounds(bounds, CONFIG.fitBoundsOptions);

    const route = data.features?.find(f => f.geometry.type === 'LineString');

    if (route?.geometry.coordinates.length > 1) {
        decorator = L.polylineDecorator(route.geometry.coordinates.map(([lng, lat]) => [lat, lng]), {patterns: [CONFIG.arrowOptions]}).addTo(map);
    }
}

export const getMapInstance = () => map;
