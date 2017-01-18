<!DOCTYPE html>
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>La Sucrerie: Découvrez la douceur d'un lieu unique</title>
    <meta name="description"
          content="Bien plus qu’un ensemble de logements, c’est véritablement un nouveau lieu de vie, ouvert à tous, qui verra bientôt le jour. La Sucrerie vous offre 1,86 ha bucolique au bord de l’eau. Un habitat diversifié et un quartier propice à la convivialité.">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->

    <!-- Basic favicons -->
    <link rel="shortcut icon" sizes="16x16 32x32 48x48 64x64" href="/favicon.ico">
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico">
    <!--[if IE]>
    <link rel="shortcut icon" href="/favicon.ico"><![endif]-->
    <!-- For Opera Speed Dial -->
    <link rel="icon" type="image/png" sizes="195x195" href="/favicon-195.png">
    <!-- For iPad with high-resolution Retina Display running iOS ≥ 7 -->
    <link rel="apple-touch-icon" sizes="152x152" href="/favicon-152.png">
    <!-- For iPad with high-resolution Retina Display running iOS ≤ 6 -->
    <link rel="apple-touch-icon" sizes="144x144" href="/favicon-144.png">
    <!-- For iPhone with high-resolution Retina Display running iOS ≥ 7 -->
    <link rel="apple-touch-icon" sizes="120x120" href="/favicon-120.png">
    <!-- For iPhone with high-resolution Retina Display running iOS ≤ 6 -->
    <link rel="apple-touch-icon" sizes="114x114" href="/favicon-114.png">
    <!-- For Google TV devices -->
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96.png">
    <!-- For iPad Mini -->
    <link rel="apple-touch-icon" sizes="76x76" href="/favicon-76.png">
    <!-- For first- and second-generation iPad -->
    <link rel="apple-touch-icon" sizes="72x72" href="/favicon-72.png">
    <!-- For non-Retina iPhone, iPod Touch and Android 2.1+ devices -->
    <link rel="apple-touch-icon" href="favicon-57.png">
    <!-- Windows 8 Tiles -->
    <meta name="msapplication-TileColor" content="#FFFFFF">
    <meta name="msapplication-TileImage" content="/favicon-144.png">

    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/main.css?v=3">
    <link rel="stylesheet" href="css/style.css?v=5">
    <!-- <link rel="stylesheet" href="css/jquery.bxslider.css"> -->
    <link rel="stylesheet" href="js/vendor/css/jquery.fancybox.css">
    <script src="js/vendor/modernizr-2.6.2.min.js"></script>

    <!--[if gte IE 9]>
    <style type="text/css">
        .gradient {
            filter: none;
        }
    </style>
    <![endif]-->

</head>
<body>



<form action="./test.php" method="post" data-parsley-validate >
        <div class="input-container">
            <input name="name" type="text" placeholder="Nom" required data-parsley-error-message="Veuillez remplir votre nom" />
        </div>
        <div class="input-container rcol">
            <input name="prenom" type="text" placeholder="Prénom"/>
        </div>
        <input name="address" type="text" placeholder="Adresse" class="full"/>

        <div class="input-container">
            <input name="tel" type="text" placeholder="Téléphone"/>
        </div>
        <div class="input-container rcol">
            <input name="email" type="email" placeholder="E-mail" required data-parsley-required data-parsley-error-message="Veuillez remplir une adresse email valide" />
        </div>
        <div class="textarea-container">
            <textarea name="message" rows="10" placeholder="Message"></textarea>
        </div>
        <input type="submit" id="1" value="Envoyer"/>
    </form>
	</body>
</html>
