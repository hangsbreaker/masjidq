<?php
include "jimat.php";

if (isset($_POST['rad'])) {
    // print_r($_POST);

    $rd = array();
    $rad = $_POST['rad'];
    foreach ($rad as $k => $r) {
        $rd[$k] = preg_replace("/[^0-9\.-]/", "", $r);
    }
    $data = array();
    $sql = mysqli_query($c, "SELECT * FROM masjid WHERE (lat <= " . $rd[0] . " AND lat >= " . $rd[1] . ") AND (lng <= " . $rd[2] . " AND lng >= " . $rd[3] . ")");
    while ($d = mysqli_fetch_object($sql)) {
        array_push($data, $d);
    }
    echo json_encode($data);
    exit;
} else if (isset($_POST['kegiatan'])) {
    $data = array();
    $id = mysqli_real_escape_string($c, $_POST['kegiatan']);
    $sql = mysqli_query($c, "SELECT * FROM jadwal WHERE id_masjid=" . $id . " order by tanggal desc, jam desc limit 500");
    while ($d = mysqli_fetch_object($sql)) {
        array_push($data, $d);
    }
    echo json_encode($data);
    exit;
} else if (isset($_POST['searchTerm'])) {
    $data = array();
    $term = mysqli_real_escape_string($c, strtolower($_POST['searchTerm']));
    $sql = mysqli_query($c, "SELECT * FROM masjid WHERE concat(nama,' ',alamat) like '%" . $term . "%' limit 20");
    while ($d = mysqli_fetch_object($sql)) {
        array_push($data, array("id" => $d->id, "text" => $d->nama, "data" => $d));
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
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="initial-scale=1.0, user-scalable=yes, width=device-width" />
    <meta name="Description" content="MasjidQ">
    <link rel="shortcut icon" type="image/png" href="markmosq.png" />

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto+Mono">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.5.1/leaflet.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet-sidebar-v2@3.1.1/css/leaflet-sidebar.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
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
            z-index: 1;
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
            top: 12px;
            font-size: 20px !important;
            background: #fff;
            color: #aaa;
        }

        #detail {
            position: fixed;
            display: none;
            top: 0px;
            left: 0px;
            height: 100%;
            max-width: 410px;
            background: #ffffff;
            box-shadow: 0px 0px 10px 0px #888;
            overflow-y: auto;
            z-index: 1111;
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

        .cols {
            position: relative;
            width: 100%;
            min-height: 1px;
            padding-right: 15px;
            padding-left: 15px;
        }

        #identify .icons {
            flex: 0 0 16.666667%;
            max-width: 16.666667%;
        }

        #identify .labels {
            flex: 0 0 83.333333%;
            max-width: 83.333333%;
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

        /* select 2 */
        #caridata {
            width: 390px;
        }

        .select2-results__message,
        .select2-selection__arrow {
            display: none;
        }

        .select2-selection,
        .select2-search__field {
            outline: none;
        }

        .select2-container--default {
            font-size: 14px;
            border-radius: 10px;
            outline: none;
            border: none;
            background: #ffffff;
            box-shadow: 0px 0px 10px 0px #aaa;
            padding: 8px 50px 8px 15px;
        }

        .select2-container--default .select2-selection--single {
            border: none;
        }

        .select2-dropdown--below {
            top: -42px;
            z-index: 11111;
            padding: 0px;
            margin: 0px;
            background: transparent;
            border: none;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: none;
            max-width: 335px;
            padding: 6px 50px 6px 15px;
        }

        .select2-results__options {
            position: relative;
            background: #ffffff;
            top: -5px;
            border-bottom-left-radius: 5px;
            border-bottom-right-radius: 5px;
            box-shadow: 0px 5px 8px 0px #aaa;
        }

        .select2-selection__clear {
            position: absolute;
            outline: none;
            border: none;
            padding: 0px;
            right: -9px;
            top: 0px;
            font-size: 28px !important;
            background-color: #ffffff !important;
            color: #aaa;
            z-index: 1;
            height: 44px !important;
            width: 25px;
        }

        .optres {
            width: 100%;
            padding: 5px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
</head>

<body translate="no">

    <div id="wcari">
        <select id="caridata" name="caridata"></select>
        <i class="fas fa-search" id="clrbtn"></i>
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
                <label for="alamat" class="cols icons"><i class="fas fa-map-marker-alt"></i></label>
                <div class="cols labels" id="alamat">
                </div>
            </div>
            <div class="form-group row" id="wtelepon">
                <label for="telepon" class="cols icons"><i class="fas fa-phone"></i></label>
                <div class="cols labels" id="telepon">
                </div>
            </div>
            <div class="form-group row" id="wwebsite">
                <label for="website" class="cols icons"><i class="fas fa-globe-asia"></i></label>
                <div class="cols labels" id="website">
                </div>
            </div>
            <hr>
            <div class="form-group row" id="wketerangan">
                <div class="col-sm-12">
                    <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="kegiatan-tab" data-toggle="tab" href="#kegiatan" role="tab" aria-controls="kegiatan" aria-selected="true">Kegiatan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="keterangan-tab" data-toggle="tab" href="#keterangan" role="tab" aria-controls="keterangan" aria-selected="false">Profil</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="kegiatan" role="tabpanel" aria-labelledby="kegiatan-tab"></div>
                        <div class="tab-pane fade" id="keterangan" role="tabpanel" aria-labelledby="keterangan-tab"></div>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script id="rendered-js">
        $(document).ready(function() {

            $("#caridata").select2({
                allowClear: true,
                placeholder: "Cari",
                minimumInputLength: 1,
                ajax: {
                    url: window.location.pathname,
                    type: "post",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            searchTerm: params.term // search term
                        };
                    },
                    processResults: function(response) {
                        // console.log(response);
                        return {
                            // results: response
                            results: $.map(response, function(item) {
                                return {
                                    text: item.text,
                                    id: item.id,
                                    alamat: item.data.alamat,
                                    data: item.data
                                }
                            })
                        };
                    },
                    cache: true
                },
                templateResult: format
                // templateSelection: format
            });
            $('#caridata').on('select2:select', function(e) {
                var data = e.params.data;
                // console.log(data);
                map.panTo(new L.LatLng(data.data.lat, data.data.lng));
                markClick(data.data);
            });

            $('#caridata').on('select2:open', function(e) {
                $('#caridata').val(null).trigger("change");
            });

            $('#clrbtn').on("click", function() {
                if (window.innerWidth < 400) {
                    $('#detail').css("display", "none");
                }
            });

            if (window.innerWidth < 400 || window.matchMedia('(max-device-width: 400px)').matches) {
                // alert(screen.width + ' ' + window.innerWidth);
                $(".select2").css("width", (window.innerWidth - 18) + "px");
            }
        });

        function format(state) {
            if (state.loading) {
                return state.text;
            }
            var dt = state.data;
            // console.log(dt.id);
            var $container = $('<div class="optres">&nbsp;<i class="fas fa-map-marker-alt"></i>&nbsp;&nbsp;&nbsp;&nbsp;' + state.text + ', <span>' + state.alamat + '</span></div>');
            return $container;
        }

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
            $('#keterangan').html(id['keterangan'].replaceAll("\n", "<br>"));
            $('#myTab a[href="#kegiatan"]').tab('show');

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
                    // console.log(d);
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
    </script>
</body>

</html>