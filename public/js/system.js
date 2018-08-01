mySidenav = document.getElementById("mySidenav")
overlayBg = document.getElementById("myOverlay")
loader = document.getElementById("loader-cont")
/*
count = 0

path = "https://www.google.pt/maps/@37.087802,-8.4264864,3a,75y,185.03h,76.94t/data=!3m6!1e1!3m4!1s9suOi359S8Fqb6ihifQaAQ!2e0!7i13312!8i6656"

function w3_open() {
    if (mySidenav.style.display === "block") {
        mySidenav.style.display = "none"
         overlayBg.style.display = "none"
         loader.style.display = "none"
    } else {
        mySidenav.style.display = "block"
         overlayBg.style.display = "block"
         loader.style.display = "none"
    }
}

function w3_close() {
    mySidenav.style.display = "none"
    overlayBg.style.display = "none"
}


window.onscroll = function() {myFunction()}
function myFunction() {
    var topo = document.getElementById("bt-top")
    document.body.scrollTop > 4000 || document.documentElement.scrollTop > 4000 ?
    topo.className = "top w3-animate-zoom w3-hide-small" : 
    topo.className = topo.className.replace("top w3-animate-zoom w3-hide-small", "top w3-hide w3-hide-small")
}


function orderDateByTimestamp(ar){
  mindate=''
  flag=0

  var arr_tm=[]

  var t=[]

  var arr_or=[]


  timenow = (new Date().getTime()/1000)+86400
  

  for(i=0;i<ar.length;i++)
    arr_tm.push(new Date(ar[i]).getTime())

    t = arr_tm.sort()

  for(r=0;r<t.length;r++){

  if( timenow <= t[r]-86400){
    mindate = moment.unix(timenow).format('DD/MM/YYYY')
    if(timenow >= t[r]-86400 && t[r]+86400 < t[r+1] && flag == 0){
      flag=1
      mindate = moment.unix((t[r])/1000-86400).format('DD/MM/YYYY')
    }
}




arr_or.push(moment.unix(t[r]/1000).format('DD/MM/YYYY'))

if(!mindate) mindate = moment.unix(timenow).format('DD/MM/YYYY')

//console.log(mindate)

$('.datetimepicker').data("DateTimePicker").minDate(mindate)
$('.datetimepicker').data("DateTimePicker").maxDate('01/01/2970')
}


return arr_or
}


function onClick(element) {
  document.getElementById("img01").src = element.src
  document.getElementById("modal01").style.display = "block"
  var captionText = document.getElementById("caption")
  captionText.innerHTML = element.alt
}


function navigation(pos){
  w3_close()
  vl=45
  $(".nav-space").val() ? vl = vl + parseInt($(".nav-space").val()) : vl
  $("html, body").animate({
    scrollTop: $(pos).offset().top -vl
  }, 1000)
}

function weather(){
  $.simpleWeather({
    location: "Benagil, Faro",
    unit: "c",
    success: function(weather) {
      html = '<p style="margin:-3px"><i class="icon-'+weather.code+'"></i> '+weather.temp+'&deg'+weather.units.temp+'</br><span class="fa-ta fa-ta-wind" style="font-size: 22px"></span> '+weather.wind.speed+' '+weather.units.speed+'</p><li style="margin-left:20px"><a class="lang-action pt active-lang" href="javascript:void(0)" title="pt_PT" onclick = "languages(this.title,1)">PT</a></li><li><a class="lang-action en"  href="javascript:void(0)"  title="en_EN" onclick = "languages(this.title,2)">EN</a></li>'
      html_mob = '<p><i class="icon-'+weather.code+'"></i> '+weather.temp+'&deg'+weather.units.temp+'</br><span class="fa-ta fa-ta-wind" style="font-size: 22px"></span> '+weather.wind.speed+' '+weather.units.speed+'</p>'
      $(".weather").html(html)
      $(".weather-mob").html(html_mob)
      wind = weather.wind.speed
    },
    error: function(error) {
      count++
      html = '<p class="lang-only"></p><li style="margin-left:20px"><a class="lang-action pt active-lang" href="javascript:void(0)" title="pt_PT" onclick = "languages(this.title,1)">PT</a></li><li><a class="lang-action en"  href="javascript:void(0)"  title="en_EN" onclick = "languages(this.title,2)">EN</a></li>'

      $(".weather").html(html)
      count <= 3 ? weather() : false
    }
  })
}

weather()

var csrf_token = $("#csrf").val()

$(':input[type=number]').on('mousewheel',function(e){this.blur()})
$(':input[type=number]').on('keydown', function(e) {
    if ( e.which == 38 || e.which == 40 )
        e.preventDefault()
    }) 

$("#booking").on("submit", function(e) {
  e.preventDefault()

  $("#myOverlay, #loader-cont").show()

  if ($(".DATA").val().match(/-/g) ){
    dt = $(".DATA").val().split("-")
    dtf = dt[2]+"/"+dt[1]+"/"+dt[0]
    datav=$("#booking").serialize()+"&date="+dtf
  }

  else {
    dtf = $(".DATA").val()
    datav=$("#booking").serialize()
  }

  $(".my-btn").prop("disabled",true)
  $(".user-info-1,.user-info-2,.user-info-3").addClass("w3-hide")

$("body").bind("ajaxSend", function(elm, xhr){
      xhr.setRequestHeader('X-CSRF-Token', csrf_token)
})


  $.ajax({
    type: "POST",
    url: "form/inforequest.php",
    data: datav,
    cache: false,
    success: function(data) {
      $("#myOverlay, #loader-cont").hide()
        navigation("#ncriancas")
        if (data == 00){
          $(".user-info-00").removeClass("w3-hide")
          $(".user-info-txt").html(data)
          setTimeout(function(){
            $(".my-btn").prop("disabled",false)}, 2500)
         }

        else if (data == 01){
          $(".user-info-01").removeClass("w3-hide")
          setTimeout(function(){
            $(".my-btn").prop("disabled",false)
            grecaptcha.reset()
          }, 2500)
        }

        else if (data == 10){
          $(".user-info-10").removeClass("w3-hide")
          setTimeout(function(){
            $(".my-btn").prop("disabled",false)
            grecaptcha.reset()
          }, 2500)
        }

        else if (data == 11 || data == 111){
            $(".user-info-11").removeClass("w3-hide")
            $("input.NOME,input.EMAIL, input.TELEFONE, select.TOURTYPE, input.NADULTOS,input.NCRIANCAS,input.MENSAGEM, input.ADDRESS, input.NBEBES, textarea#message").val("")
            setDates()
setTimeout(function(){
                $(".my-btn").prop("disabled",false)
                grecaptcha.reset()
            }, 2500)
         }

        else{
          $(".user-info-2").removeClass("w3-hide")
          $(".user-info-txt").html(data)
          setTimeout(function(){ $(".my-btn").prop("disabled",false)}, 2500)
         }
    },

    error: function(data) {
      navigation("#ncriancas")
      $("#myOverlay, #loader-cont").hide()
      $(".user-info-3").removeClass("w3-hide")
      setTimeout(function(){
        $(".my-btn").prop("disabled",false)
      }, 1500)
    }

  })
})
*/



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
