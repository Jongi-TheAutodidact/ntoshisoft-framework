(function () {
  'use strict';

  var mapData = window.intelMapData;
  if (!mapData) return;

  var map = L.map('intel-map-container', {
    center: mapData.center || [-25.8603, 28.1894],
    zoom: mapData.zoom || 12,
    zoomControl: true,
    attributionControl: true,
  });

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution:
      '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    maxZoom: 19,
  }).addTo(map);

  var incidentLayer = L.layerGroup().addTo(map);
  var geofenceLayer = L.layerGroup().addTo(map);
  var locationLayer = L.layerGroup().addTo(map);

  function severityColor(severity) {
    switch ((severity || '').toLowerCase()) {
      case 'critical':
        return '#ef4444';
      case 'high':
        return '#f97316';
      case 'medium':
        return '#eab308';
      case 'low':
        return '#22c55e';
      default:
        return '#6b7280';
    }
  }

  function riskColor(level) {
    switch ((level || '').toUpperCase()) {
      case 'CRITICAL':
        return '#ef4444';
      case 'HIGH':
        return '#f97316';
      case 'MEDIUM':
        return '#eab308';
      case 'LOW':
        return '#22c55e';
      default:
        return '#6b7280';
    }
  }

  function createIncidentIcon(severity) {
    var color = severityColor(severity);
    return L.divIcon({
      html:
        '<div style="width:28px;height:28px;border-radius:50%;background:' +
        color +
        ';border:3px solid #fff;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,0.4);font-size:13px;">' +
        '<i class="fa-solid fa-bolt" style="color:#fff;"></i></div>',
      className: '',
      iconSize: [28, 28],
      iconAnchor: [14, 14],
      popupAnchor: [0, -14],
    });
  }

  function createLocationIcon() {
    return L.divIcon({
      html:
        '<div style="width:24px;height:24px;border-radius:50%;background:#3b82f6;border:3px solid #fff;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 6px rgba(0,0,0,0.3);font-size:11px;">' +
        '<i class="fa-solid fa-location-dot" style="color:#fff;"></i></div>',
      className: '',
      iconSize: [24, 24],
      iconAnchor: [12, 12],
      popupAnchor: [0, -12],
    });
  }

  function renderIncidents(incidents) {
    incidentLayer.clearLayers();
    if (!incidents || !incidents.length) return;

    incidents.forEach(function (inc) {
      var color = severityColor(inc.severity);
      var icon = createIncidentIcon(inc.severity);
      var marker = L.marker([inc.lat, inc.lng], { icon: icon }).bindPopup(
        '<div style="font-family:Inter,sans-serif;min-width:200px;">' +
          '<div style="font-weight:600;font-size:0.95rem;margin-bottom:4px;">' +
          sentinel.escapeHtml(inc.title) +
          '</div>' +
          '<div style="font-size:0.8rem;color:#6b7280;margin-bottom:6px;">#' +
          sentinel.escapeHtml(inc.ref) +
          '</div>' +
          '<div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:6px;">' +
          '<span style="padding:2px 8px;border-radius:4px;font-size:0.7rem;font-weight:600;background:' +
          color +
          '20;color:' +
          color +
          ';border:1px solid ' +
          color +
          '40;">' +
          (inc.severity || 'unknown').toUpperCase() +
          '</span>' +
          '<span style="padding:2px 8px;border-radius:4px;font-size:0.7rem;background:#1e293b;color:#94a3b8;">' +
          sentinel.escapeHtml(inc.type) +
          '</span>' +
          '</div>' +
          '<div style="font-size:0.75rem;color:#64748b;">' +
          '<i class="fa-regular fa-clock"></i> ' +
          sentinel.escapeHtml(inc.status) +
          '</div>' +
          '<div style="margin-top:8px;">' +
          '<a href="' +
          window.intelMapData.baseUrl +
          '/admin/sentinel/incidents/detail/' +
          inc.id +
          '" style="font-size:0.75rem;color:#3b82f6;text-decoration:none;">View Details &rarr;</a>' +
          '</div>' +
          '</div>',
        { maxWidth: 280, className: 'sentinel-map-popup' }
      );
      incidentLayer.addLayer(marker);
    });
  }

  function renderGeofences(geofences) {
    geofenceLayer.clearLayers();
    if (!geofences || !geofences.length) return;

    geofences.forEach(function (gf) {
      var color = gf.color || riskColor(gf.risk_level);
      var opacity = 0.25;
      var fillOpacity = 0.1;

      var layer;
      if (gf.type === 'polygon' && gf.boundary_points && gf.boundary_points.length >= 3) {
        var latlngs = gf.boundary_points.map(function (pt) {
          return [pt[0], pt[1]];
        });
        layer = L.polygon(latlngs, {
          color: color,
          weight: 2,
          opacity: 0.6,
          fillColor: color,
          fillOpacity: fillOpacity,
        });
      } else {
        layer = L.circle([gf.lat, gf.lng], {
          radius: gf.radius_meters || 500,
          color: color,
          weight: 2,
          opacity: 0.6,
          fillColor: color,
          fillOpacity: fillOpacity,
        });
      }

      layer.bindPopup(
        '<div style="font-family:Inter,sans-serif;min-width:200px;">' +
          '<div style="font-weight:600;font-size:0.95rem;margin-bottom:4px;">' +
          sentinel.escapeHtml(gf.name) +
          '</div>' +
          '<div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:6px;">' +
          '<span style="padding:2px 8px;border-radius:4px;font-size:0.7rem;font-weight:600;background:' +
          color +
          '20;color:' +
          color +
          ';border:1px solid ' +
          color +
          '40;">' +
          (gf.type || 'circle').toUpperCase() +
          '</span>' +
          '<span style="padding:2px 8px;border-radius:4px;font-size:0.7rem;background:#1e293b;color:#94a3b8;">' +
          (gf.risk_level || 'LOW') +
          '</span>' +
          '</div>' +
          (gf.type === 'circle'
            ? '<div style="font-size:0.75rem;color:#64748b;">Radius: ' +
              (gf.radius_meters || 500) +
              'm</div>'
            : '') +
          '<div style="margin-top:8px;">' +
          '<a href="' +
          window.intelMapData.baseUrl +
          '/admin/sentinel/geofences/edit/' +
          gf.id +
          '" style="font-size:0.75rem;color:#3b82f6;text-decoration:none;">Edit Geofence &rarr;</a>' +
          '</div>' +
          '</div>',
        { maxWidth: 280, className: 'sentinel-map-popup' }
      );

      geofenceLayer.addLayer(layer);
    });
  }

  function renderLocations(locations) {
    locationLayer.clearLayers();
    if (!locations || !locations.length) return;

    locations.forEach(function (loc) {
      var icon = createLocationIcon();
      var marker = L.marker([loc.lat, loc.lng], { icon: icon }).bindPopup(
        '<div style="font-family:Inter,sans-serif;min-width:200px;">' +
          '<div style="font-weight:600;font-size:0.95rem;margin-bottom:4px;">' +
          sentinel.escapeHtml(loc.name) +
          '</div>' +
          (loc.description
            ? '<div style="font-size:0.8rem;color:#64748b;margin-bottom:6px;">' +
              sentinel.escapeHtml(loc.description) +
              '</div>'
            : '') +
          '<div style="display:flex;gap:6px;flex-wrap:wrap;">' +
          '<span style="padding:2px 8px;border-radius:4px;font-size:0.7rem;background:#1e293b;color:#94a3b8;">' +
          sentinel.escapeHtml(loc.type) +
          '</span>' +
          '<span style="padding:2px 8px;border-radius:4px;font-size:0.7rem;background:#1e293b;color:#94a3b8;">' +
          (loc.risk_level || 'LOW') +
          '</span>' +
          '</div>' +
          '<div style="margin-top:8px;">' +
          '<a href="' +
          window.intelMapData.baseUrl +
          '/admin/sentinel/locations/edit/' +
          loc.id +
          '" style="font-size:0.75rem;color:#3b82f6;text-decoration:none;">Edit Location &rarr;</a>' +
          '</div>' +
          '</div>',
        { maxWidth: 280, className: 'sentinel-map-popup' }
      );
      locationLayer.addLayer(marker);
    });
  }

  function fitMapBounds() {
    var allBounds = [];
    if (mapData.incidents && mapData.incidents.length) {
      mapData.incidents.forEach(function (i) {
        allBounds.push([i.lat, i.lng]);
      });
    }
    if (mapData.locations && mapData.locations.length) {
      mapData.locations.forEach(function (l) {
        allBounds.push([l.lat, l.lng]);
      });
    }
    if (mapData.geofences && mapData.geofences.length) {
      mapData.geofences.forEach(function (g) {
        if (g.type === 'polygon' && g.boundary_points && g.boundary_points.length) {
          g.boundary_points.forEach(function (pt) {
            allBounds.push([pt[0], pt[1]]);
          });
        } else {
          allBounds.push([g.lat, g.lng]);
        }
      });
    }
    if (allBounds.length) {
      var bounds = L.latLngBounds(allBounds);
      if (bounds.isValid()) {
        map.fitBounds(bounds, { padding: [50, 50], maxZoom: 14 });
      }
    }
  }

  var pollInterval;

  function startPolling() {
    var apiUrl =
      window.intelMapData.baseUrl + '/admin/sentinel/api/map-data';
    pollInterval = setInterval(function () {
      fetch(apiUrl)
        .then(function (res) { return res.json(); })
        .then(function (data) {
          if (data.incidents) {
            renderIncidents(data.incidents);
            mapData.incidents = data.incidents;
            updateCounts();
          }
          if (data.geofences) {
            renderGeofences(data.geofences);
            mapData.geofences = data.geofences;
          }
          if (data.locations) {
            renderLocations(data.locations);
            mapData.locations = data.locations;
          }
          sentinel.addTerminalLine(
            'Map data refreshed: ' +
              (data.incidents ? data.incidents.length : 0) +
              ' incidents, ' +
              (data.geofences ? data.geofences.length : 0) +
              ' geofences',
            'info'
          );
        })
        .catch(function () {});
    }, 30000);
  }

  function updateCounts() {
    var countEl = document.getElementById('map-incident-count');
    if (countEl && mapData.incidents) {
      countEl.textContent = mapData.incidents.length;
    }
  }

  function init() {
    if (!mapData.baseUrl) {
      mapData.baseUrl = '';
    }

    renderIncidents(mapData.incidents);
    renderGeofences(mapData.geofences);
    renderLocations(mapData.locations);

    fitMapBounds();

    L.control
      .layers(
        null,
        {
          Incidents: incidentLayer,
          Geofences: geofenceLayer,
          Locations: locationLayer,
        },
        { collapsed: false }
      )
      .addTo(map);

    startPolling();

    updateCounts();

    sentinel.addTerminalLine(
      'Intel Map initialized: ' +
        (mapData.incidents ? mapData.incidents.length : 0) +
        ' incidents, ' +
        (mapData.geofences ? mapData.geofences.length : 0) +
        ' geofences',
      'success'
    );
  }

  document.addEventListener('DOMContentLoaded', init);

  window.sentinelIntelMap = {
    refresh: function () {
      renderIncidents(mapData.incidents);
      renderGeofences(mapData.geofences);
      renderLocations(mapData.locations);
    },
  };
})();
