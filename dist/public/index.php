<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
          integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="css/plugins.css">
    <link rel="stylesheet" href="css/style.css">

    <style>
        .bg-image{

            z-index: 4;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center center;
        }
        .bg-pattern{
            background-image: url("image/pattern15.png");
        }
    </style>
</head>
<body>
<div class="body-inner">
    <?php
        require_once "content.html";
    ?>
</div>
<div id="editor" style="margin-top: 100px">
    <div>

        <p>Here goes the initial content of the editor.</p>
    </div>
</div>
<div id="ckfinder1"></div>
<script src="js/jquery.js"></script>
<script src="js/plugins.js"></script>
<!--<script src="js/functions.js"></script>-->
<!--    <script src="/assets/plugin/ckfinder/ckfinder.min.js"></script>-->
<script src="/assets/plugin/ckfinder/ckfinder.min.js"></script>
<!--    <script src="js/ckfinder.bundle.js"></script>-->
<script src="js/script.bundle.js"></script>
</body>
</html>
