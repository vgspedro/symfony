mySidenav = document.getElementById("mySidenav")
overlayBg = document.getElementById("myOverlay")
loader = document.getElementById("loader-cont")

function my_Map() {
    
  var myCenter = new google.maps.LatLng(37.0872609,-8.4263386)
  var mapCanvas = document.getElementById("map")
  var mapOptions = {
    center: myCenter,
    scrollwheel: false,
    draggable: false,    
    zoom: 13,
    panControl: true,
    zoomControl: true,
    mapTypeControl: false,
    scaleControl: false,
    streetViewControl: false,
    overviewMapControl: false,
    rotateControl: false,
  }

  var map = new google.maps.Map(mapCanvas, mapOptions)

  var marker = new google.maps.Marker({position:myCenter})

  marker.setMap(map)

google.maps.event.addListener(marker,"click",function() {
  map.setZoom(17)
  map.setCenter(marker.getPosition())
  })
  var infowindow = new google.maps.InfoWindow({
    content: "Taruga Tours"
  })
  infowindow.open(map,marker)
}
