(function () {
  'use strict';

  function initMapPicker(containerId, latInputId, lngInputId, options) {
    var container = document.getElementById(containerId);
    var latInput = document.getElementById(latInputId);
    var lngInput = document.getElementById(lngInputId);
    if (!container || !latInput || !lngInput) return;

    options = options || {};
    var defaultLat = parseFloat(latInput.value) || options.defaultLat || -25.8603;
    var defaultLng = parseFloat(lngInput.value) || options.defaultLng || 28.1894;

    var map = L.map(container, {
      center: [defaultLat, defaultLng],
      zoom: options.zoom || 13,
      zoomControl: true,
      attributionControl: false,
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
    }).addTo(map);

    var marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

    function updateCoords(lat, lng) {
      latInput.value = lat.toFixed(7);
      lngInput.value = lng.toFixed(7);
    }

    marker.on('dragend', function () {
      var pos = marker.getLatLng();
      updateCoords(pos.lat, pos.lng);
    });

    map.on('click', function (e) {
      marker.setLatLng(e.latlng);
      updateCoords(e.latlng.lat, e.latlng.lng);
    });

    latInput.addEventListener('change', function () {
      var lat = parseFloat(this.value);
      var lng = parseFloat(lngInput.value);
      if (!isNaN(lat) && !isNaN(lng)) {
        marker.setLatLng([lat, lng]);
        map.setView([lat, lng]);
      }
    });

    lngInput.addEventListener('change', function () {
      var lat = parseFloat(latInput.value);
      var lng = parseFloat(this.value);
      if (!isNaN(lat) && !isNaN(lng)) {
        marker.setLatLng([lat, lng]);
        map.setView([lat, lng]);
      }
    });

    if (options.showCircle) {
      var radiusInput = document.getElementById(options.radiusInputId);
      if (radiusInput) {
        var circle = L.circle([defaultLat, defaultLng], {
          radius: parseInt(radiusInput.value) || 500,
          color: '#10b981',
          weight: 2,
          fillOpacity: 0.1,
        }).addTo(map);

        marker.on('dragend', function () {
          circle.setLatLng(marker.getLatLng());
        });
        map.on('click', function (e) {
          circle.setLatLng(e.latlng);
        });
        radiusInput.addEventListener('input', function () {
          circle.setRadius(parseInt(this.value) || 500);
        });
      }
    }

    setTimeout(function () {
      map.invalidateSize();
    }, 100);

    return map;
  }

  window.initMapPicker = initMapPicker;
})();
