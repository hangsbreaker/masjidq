<?php
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'masjidq';

$con = mysqli_connect($dbhost, $dbuser, $dbpass);
mysqli_select_db($con, $dbname);
// let me know if the connection fails
if (!$con) {
    print("Connection Failed.");
    exit;
}

if (isset($_POST['rad'])) {
    // print_r($_POST);

    $rd = array();
    $rad = $_POST['rad'];
    foreach ($rad as $k => $r) {
        $rd[$k] = preg_replace("/[^0-9\.-]/", "", $r);
    }
    $data = array();
    $sql = mysqli_query($con, "SELECT * FROM masjid WHERE (lat <= " . $rd[0] . " AND lat >= " . $rd[1] . ") AND (lng <= " . $rd[2] . " AND lng >= " . $rd[3] . ")");
    while ($d = mysqli_fetch_object($sql)) {
        array_push($data, $d);
    }
    echo json_encode($data);
    exit;
} else if (isset($_POST['kegiatan'])) {
    $data = array();
    $id = $_POST['kegiatan'];
    $sql = mysqli_query($con, "SELECT * FROM jadwal WHERE id_masjid=" . $id . " order by tanggal desc, jam desc limit 500");
    while ($d = mysqli_fetch_object($sql)) {
        array_push($data, $d);
    }
    echo json_encode($data);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>MasjidQ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto+Mono">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.5.1/leaflet.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet-sidebar-v2@3.1.1/css/leaflet-sidebar.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            padding: 0;
            margin: 0;
        }

        html,
        body,
        #map {
            height: 100%;
            font-family: "Roboto", sans-serif;
        }

        #sidebar {
            height: 90px;
        }

        /* Customise Zoom Controls */

        .leaflet-control-zoom {
            position: fixed;
            bottom: 10px;
            right: 10px;
        }

        /*.leaflet-control-zoom a {
            color: white !important;
            background: rgba(0, 0, 0, 0.5) !important;
            transition: all 0.5s ease;
        }

        .leaflet-control-zoom a:hover {
            color: white !important;
            background: rgba(0, 0, 0, 0.9) !important;
        }*/

        #wcari {
            position: fixed;
            top: 10px;
            left: 9px;
            z-index: 11111;
        }

        #wcari:before {
            content: "";
            position: absolute;
            top: 10px;
            right: 40px;
            font-size: 20px;
            border-left: 1px solid #ddd;
            height: 25px;
        }

        #wcari input {
            width: 390px;
            font-size: 14px;
            border-radius: 10px;
            outline: none;
            border: none;
            box-shadow: 0px 0px 10px 0px #aaa;
            padding: 12px 50px 12px 15px
        }

        #clrbtn {
            position: absolute;
            outline: none;
            border: none;
            padding: 0px;
            right: 12px;
            top: 0px;
            font-size: 30px;
            background: #fff;
            color: #aaa;
        }

        #detail {
            position: fixed;
            top: 0px;
            left: 0px;
            height: 100%;
            width: 410px;
            background: #ffffff;
            box-shadow: 0px 0px 10px 0px #888;
            z-index: 1111;
            display: none;
        }

        #title {
            width: 100%;
            text-align: center;
            margin-top: 100px;
            margin-bottom: 20px;
        }

        #actbutton {
            text-align: center;
        }

        #actbutton a {
            color: #333;
            text-decoration: none;
        }

        #actbutton .fas {
            font-size: 20px;
            background: #1a73e8;
            padding: 12px;
            border-radius: 50%;
            color: #ffffff;
        }

        #identify {
            padding: 18px;
        }

        #identify .fas {
            font-size: 25px;
            color: #1a73e8;
        }

        .linkd {
            color: #333;
        }

        .tab-content>.tab-pane {
            padding-top: 15px;
        }

        .keg label {
            margin-bottom: 0px;
        }

        .keg small {
            position: relative;
            top: -5px;
            color: #888;
        }
    </style>
</head>

<body translate="no">
    <div id="wcari">
        <input type="text" id="cari" placeholder="Cari Masjid" />
        <button type="button" id="clrbtn" onclick="clrcari()">&times;</button>
    </div>
    <div id="map"></div>
    <div id="detail">
        <div id="foto"></div>
        <h2 id="title"></h2>
        <hr>
        <div id="actbutton">
            <a href="" id="linkmap" target="_blank"><i class="fas fa-directions"></i></a>
            Rute
        </div>
        <hr>
        <div id="identify">
            <div class="form-group row" id="walamat">
                <label for="alamat" class="col-sm-2 col-form-label"><i class="fas fa-map-marker-alt"></i></label>
                <div class="col-sm-10" id="alamat">
                </div>
            </div>
            <div class="form-group row" id="wtelepon">
                <label for="telepon" class="col-sm-2 col-form-label"><i class="fas fa-phone"></i></label>
                <div class="col-sm-10" id="telepon">
                </div>
            </div>
            <div class="form-group row" id="wwebsite">
                <label for="website" class="col-sm-2 col-form-label"><i class="fas fa-globe-asia"></i></label>
                <div class="col-sm-10" id="website">
                </div>
            </div>
            <hr>
            <div class="form-group row" id="wketerangan">
                <div class="col-sm-12">

                    <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="keterangan-tab" data-toggle="tab" href="#keterangan" role="tab" aria-controls="keterangan" aria-selected="true">Profil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="kegiatan-tab" data-toggle="tab" href="#kegiatan" role="tab" aria-controls="kegiatan" aria-selected="false">Kegiatan</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="keterangan" role="tabpanel" aria-labelledby="keterangan-tab"></div>
                        <div class="tab-pane fade" id="kegiatan" role="tabpanel" aria-labelledby="kegiatan-tab"></div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.5.1/leaflet.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/leaflet-sidebar-v2@3.1.1/js/leaflet-sidebar.min.js'></script>
    <script id="rendered-js">
        // Move to Coor
        // map.panTo(new L.LatLng(-7.453751892045342, 112.70092964172363));
        // Make Circle
        // const circle = new L.Circle([map.getCenter().lat, map.getCenter().lng], 1000).addTo(map);
        // console.log(circle.getLatLng());

        //Init BaseMaps
        var basemaps = {
            "OpenStreetMaps": L.tileLayer(
                "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                    minZoom: 2,
                    maxZoom: 19,
                    id: "osm.streets"
                }),


            "GoogleMap": L.tileLayer(
                "https://mt1.google.com/vt/lyrs=r&x={x}&y={y}&z={z}", {
                    minZoom: 2,
                    maxZoom: 19,
                    id: "google.street"
                }),


            "GoogleSatellite": L.tileLayer(
                "https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}", {
                    minZoom: 2,
                    maxZoom: 19,
                    id: "google.satellite"
                }),


            "GoogleHybrid": L.tileLayer(
                "https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}", {
                    minZoom: 2,
                    maxZoom: 19,
                    id: "google.hybrid"
                })
        };

        //Map Options
        var mapOptions = {
            zoomControl: false,
            attributionControl: false,
            center: [-7.4668932, 112.7170543],
            zoom: 15,
            layers: [basemaps.GoogleMap]
        };

        //Render Main Map
        var map = L.map("map", mapOptions);

        //Handle Map click to Display Lat/Lng
        map.on('click', function(e) {
            // console.log(e.latlng.lat + ", " + e.latlng.lng);
            $('#detail').css("display", "none");
        });

        map.on("moveend", function() {
            // console.log(map.getCenter().toString());
            // console.log(map.getCenter());
            // const circle = new L.Circle([map.getCenter().lat, map.getCenter().lng], 1000).addTo(map);
            // console.log(circle.getLatLng());
            checkMosq(map.getCenter().lat, map.getCenter().lng);
        });

        //Render Zoom Control
        L.control.
        zoom({
            position: "topleft"
        }).addTo(map);

        var myIcon = L.icon({
            iconUrl: 'markmosq.png',
            iconSize: [30, 42], // size of the icon
        });

        function markClick(id) {
            console.log(id);
            $('#detail').css("display", "block");
            $('#linkmap').attr('href', 'https://maps.google.com/?q=' + id['lat'] + ',' + id['lng']);
            $('#title').html(id['nama']);
            $('#alamat').html(id['alamat']);

            $('#wtelepon').css("display", "none");
            if (id['telepon'] != "") {
                var tel = "";
                var tl = id['telepon'].split(";");
                for (t in tl) {
                    tel = tel + tl[t] + '<br>';
                }
                $('#telepon').html(tel);
                $('#wtelepon').css("display", "flex");
            }

            $('#wwebsite').css("display", "none");
            if (id['website'] != "") {
                var web = "";
                var wb = id['website'].split(";");
                for (w in wb) {
                    web = web + '<a href="' + wb[w] + '" target="_blank" class="linkd">' + wb[w].replace('http://', '').replace('https://', '').replace('www.', '') + '</a><br>';
                }
                $('#website').html(web);
                $('#wwebsite').css("display", "flex");
            }
            $('#keterangan').html(id['keterangan']);
            $('#myTab a[href="#keterangan"]').tab('show');

            getKegiatan(id['id']);
        }

        function checkMosq(lat, lng) {
            var rad = [
                lat + 0.02706280428958685,
                lat - 0.02706280428958685,
                lng + 0.02706280428958685,
                lng - 0.02706280428958685
            ];

            $.ajax({
                type: 'POST',
                url: window.location.pathname,
                data: {
                    rad: rad
                },
                success: function(data) {
                    // console.log(data);
                    var d = JSON.parse(data);
                    console.log(d);
                    placeMark(d);
                }
            });
        }
        checkMosq(mapOptions['center'][0], mapOptions['center'][1]);

        function placeMark(loc = "") {
            var marker = [];
            for (var i = 0; i < loc.length; i++) {
                marker[i] = new L.marker(
                    [loc[i]['lat'], loc[i]['lng']], {
                        icon: myIcon,
                        title: loc[i][0]
                    }
                ).on('click',
                    L.bind(markClick, null, loc[i])
                ).addTo(map);
            }
        }

        function getKegiatan(id) {
            $('#kegiatan').html('');
            $.ajax({
                type: 'POST',
                url: window.location.pathname,
                data: {
                    kegiatan: id
                },
                success: function(data) {
                    // console.log(data);
                    var d = JSON.parse(data);
                    for (i in d) {
                        $('#kegiatan').append('<div class="form-group keg"><label>' + d[i]['kegiatan'] + '</label><br><small>' + ftgl(d[i]['tanggal']) + ' ' + d[i]['jam'] + '</small></div><hr>');
                    }
                }
            });
        }

        function ftgl(t) {
            var p = t.split('-');
            return p[2] + '-' + p[1] + '-' + p[0];
        }

        // ================================================================================
        function clrcari() {
            $('#cari').val("");
        }
    </script>
</body>

</html>