var map = L.map('map').setView([39.464741, -0.375517],15);

var Esri_WorldGrayCanvas = L.tileLayer('http://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
	attribution: 'Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ',
	maxZoom: 25
}).addTo(map);

var marker = L.marker([39.464741, -0.375517]).addTo(map);