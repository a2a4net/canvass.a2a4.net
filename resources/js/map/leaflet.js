import L from 'leaflet';
import 'leaflet-polylinedecorator';
import 'leaflet.markercluster';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';

window.L = L;

let map = null;
let clusterGroup = null;
let decorator = null;
let routeLayer = null;

const CONFIG = {
    tileLayer: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
    lineStyle: {color: '#87b9f6', weight: 3, opacity: 0.8},
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

    clusterGroup = L.markerClusterGroup({
        maxClusterRadius: 50,
        spiderfyOnMaxZoom: true,
        showCoverageOnHover: false,
        iconCreateFunction: (cluster) => {
            const count = cluster.getChildCount();
            const size = count < 10 ? 35 : (count < 100 ? 45 : 55);

            return L.divIcon({
                html: `<div style="
                    width: ${size}px;
                    height: ${size}px;
                    line-height: ${size}px;
                    background: #3b82f6;
                    color: white;
                    border-radius: 50%;
                    text-align: center;
                    font-weight: bold;
                    border: 2px solid #fff;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
                ">${count}</div>`,
                className: 'custom-cluster-icon',
                iconSize: L.point(size, size)
            });
        }
    });

    map.addLayer(clusterGroup);
}

export function renderGeoJson(data) {
    if (!clusterGroup || !data) return;

    clusterGroup.clearLayers();

    if (decorator) map.removeLayer(decorator);

    if (routeLayer) map.removeLayer(routeLayer);

    const geoJsonLayer = L.geoJson(data, {
        pointToLayer: (f, latlng) => L.circleMarker(latlng, {
            radius: 10,
            fillColor: f.properties.planned ? '#059669' : '#3b82f6',
            color: '#fff',
            weight: 2,
            opacity: 1,
            fillOpacity: 0.9
        }),
        onEachFeature: (f, l) => {
            if (f.properties) {
                const {id, address, checked_at} = f.properties;

                l.bindPopup(`
                    <div class="p-1">
                        <strong>ID:</strong> ${id}<br>
                        <strong>Адреса:</strong> ${address}<br>
                        <strong>Перевірено:</strong> ${checked_at || 'Ні'}
                    </div>
                `);
            }
        }
    });

    clusterGroup.addLayer(geoJsonLayer);

    const routeData = data.features?.find(f => f.geometry.type === 'LineString');

    if (routeData) {
        routeLayer = L.geoJson(routeData, {style: CONFIG.lineStyle}).addTo(map);

        const coords = routeData.geometry.coordinates;

        if (coords.length > 1) {
            decorator = L.polylineDecorator(
                coords.map(([lng, lat]) => [lat, lng]),
                {patterns: [CONFIG.arrowOptions]}
            ).addTo(map);
        }
    }

    const bounds = clusterGroup.getBounds();

    if (bounds.isValid()) {
        map.fitBounds(bounds, CONFIG.fitBoundsOptions);
    }
}

export const getMapInstance = () => map;
