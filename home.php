<?php
session_start();
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

if (!isset($_SESSION['username'])) {
    // login first
    header('location:login.php');
    exit;
}
// admin page
$username = $_SESSION['username'];
$bio = mysqli_fetch_object(mysqli_query($con, "select * from user where username='" . $username . "'"));

if (isset($_POST['NamaMasjid'])) {
    $nama = mysqli_real_escape_string($con, $_POST['NamaMasjid']);
    $alamat = mysqli_real_escape_string($con, $_POST['Alamat']);
    $lat = mysqli_real_escape_string($con, $_POST['Latitude']);
    $lng = mysqli_real_escape_string($con, $_POST['Longitude']);
    $telepon = mysqli_real_escape_string($con, $_POST['Telepon']);
    $website = mysqli_real_escape_string($con, $_POST['Website']);
    $keterangan = mysqli_real_escape_string($con, $_POST['Profil']);
    mysqli_query($con, "update masjid set nama='" . $nama . "', alamat='" . $alamat . "', lat='" . $lat . "', lng='" . $lng . "', telepon='" . $telepon . "', website='" . $website . "', keterangan='" . $keterangan . "' where id='" . $bio->id_masjid . "'");

    header('location:home.php');
    exit;
}

include "atable.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Home</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="initial-scale=1.0, user-scalable=yes, width=device-width" />
    <meta name="Description" content="MasjidQ">
    <link rel="shortcut icon" type="image/png" href="markmosq.png" />
    <link href="http://hangsbreaker.github.io/formbuilder/lib/css/bootstrap.min.css" rel="stylesheet" />
    <link href="http://hangsbreaker.github.io/formbuilder/lib/css/select2.css" rel="stylesheet" />
    <link href="http://hangsbreaker.github.io/formbuilder/lib/css/datetimepicker.min.css" rel="stylesheet" />
    <link href="http://hangsbreaker.github.io/formbuilder/lib/css/formbuilder.css" rel="stylesheet" />
    <script src='http://hangsbreaker.github.io/formbuilder/lib/js/jquery.1.12.4.min.js'></script>
    <?php atable_init(); ?>
</head>
<style>
    #profilmasjid {
        margin-top: 50px;
        margin-bottom: 50px;
    }
</style>

<body>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">MasjidQ</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo $bio->nama; ?> <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="#profil"><span class="glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp;&nbsp;&nbsp;Profil</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="logout.php"><span class="glyphicon glyphicon-off" aria-hidden="true"></span>&nbsp;&nbsp;&nbsp;Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
    <div class="container">
        <div>

            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Jadwal Kegiatan</a></li>
                <li role="presentation"><a href="#masjid" aria-controls="masjid" role="tab" data-toggle="tab">Profil Masjid</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="home">
                    <?php
                    $t = new Atable();
                    $t->limit = 20;
                    $t->caption = "Jadwal Kegiatan";
                    $t->query = "SELECT no, id_masjid, tanggal, jam, kegiatan, keterangan, pemateri, kebutuhan, terkumpul FROM jadwal";
                    $t->where = "id_masjid='" . $bio->id_masjid . "'";
                    $t->orderby = "tanggal desc, jam desc";
                    $t->col = '["id_masjid", "tanggal", "jam", "kegiatan", "keterangan", "pemateri", "kebutuhan", "terkumpul"]';
                    $t->colv = '["id_masjid", "Tanggal", "Jam", "Kegiatan", "Keterangan", "Pemateri", "Kebutuhan", "Terkumpul"]';
                    $t->collist = TRUE;
                    $t->xls = TRUE;
                    $t->colnumber = FALSE;
                    $t->colsize = '["","95px"]';
                    // $t->colalign = '[""]';
                    // $t->showsql = TRUE;
                    // $t->database = 'pgsql';
                    // $atbb->dbcon = $connection;
                    // $t->param = '';
                    // $t->style = 'table table-hover table-striped table-bordered';
                    // $atbb->showall=TRUE;
                    // $atbb->loadmore=FALSE;
                    // $t->reload=TRUE;
                    // $t->datainfo=FALSE;
                    // $t->paging=FALSE;
                    // $t->debug=FALSE;

                    $t->add = TRUE;
                    $t->edit = TRUE;
                    $t->delete = TRUE;

                    echo $t->load();
                    ?>
                </div>
                <div role="tabpanel" class="tab-pane" id="masjid">
                    <form id="profilmasjid" method="post" action=""></form>
                </div>
            </div>

        </div>
    </div>
    <style>
        .atble tr:first-child td {
            display: none;
        }

        #dtblatable0 th:nth-child(1),
        #dtblatable0 td:nth-child(1) {
            display: none;
        }
    </style>
    <script>
        $(document).ready(function() {
            const masjid = '{"title":"Data Masjid","description":"","form":[{"type":"text","question":"Nama Masjid","description":"","required":true,"data":[]},{"type":"textarea","question":"Alamat","description":"","required":true,"data":[]},{"type":"text","question":"Latitude","description":"","required":true,"data":[]},{"type":"text","question":"Longitude","description":"","required":true,"data":[]},{"type":"text","question":"Telepon","description":"","required":false,"data":[]},{"type":"text","question":"Website","description":"","required":false,"data":[]},{"type":"textarea","question":"Profil","description":"","required":false,"data":[]}]}';
            buildform("profilmasjid", masjid, "ID");

            <?php
            $mj = mysqli_fetch_object(mysqli_query($con, "select * from masjid where id='" . $bio->id_masjid . "'"));
            echo '$("#NamaMasjid").val("' . $mj->nama . '");';
            echo '$("#Alamat").val("' . $mj->alamat . '");';
            echo '$("#Latitude").val("' . $mj->lat . '");';
            echo '$("#Longitude").val("' . $mj->lng . '");';
            echo '$("#Telepon").val("' . $mj->telepon . '");';
            echo '$("#Website").val("' . $mj->website . '");';
            echo '$("#Profil").val(' . json_encode($mj->keterangan) . ');';
            ?>
        });

        $(document).ajaxStop(function() {
            $('#dtadd0').on("click", function() {
                $('#id_masjid-0').val(<?php echo $bio->id_masjid; ?>);
                $('#kebutuhan-0').val(0);
                $('#terkumpul-0').val(0);
            });
        });
    </script>
    <script src="http://hangsbreaker.github.io/formbuilder/lib/js/formbuilder.js"></script>
    <script src="http://hangsbreaker.github.io/formbuilder/lib/js/bootstrap.min.js"></script>
    <script src="http://hangsbreaker.github.io/formbuilder/lib/js/select2.min.js"></script>
    <script src="http://hangsbreaker.github.io/formbuilder/lib/js/datetimepicker.js"></script>
</body>

</html>