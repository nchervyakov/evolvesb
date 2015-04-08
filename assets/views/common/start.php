<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html lang="en">
    <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="cleartype" content="on">
        <meta name="robots" content="index,follow">

        <title><?php echo isset($pageTitle) && $pageTitle ? $pageTitle : $titleBase; ?></title>

        <meta name="description" content="<?php echo $pageDescription; ?>" />
        <meta name="keywords" content="<?php echo $pageKeywords; ?>"/>

        <meta name="author" content="Evolve Skateboards">
        <meta property="og:url" content="http://evolveskateboards.ru/">
        <meta property="og:site_name" content="Evolve Skateboards">

        <meta property="og:type" content="website">
        <meta property="og:title" content="Electric Skateboards | Electric Longboards | Evolve Skateboards ">

        <meta property="og:image" content="/images/logo.png">
        <meta property="og:image:secure_url" content="/images/logo.png">
        <meta property="og:description" content="Evolve Electric Skateboards are custom designed and hand crafted for optimal performance and ride. Our Electric Skateboards are assembled in the USA!">

        <meta name="twitter:site" content="@evolvesk8boards">

        <!-- Mobile Specific Metas -->
        <meta name="HandheldFriendly" content="True">
        <meta name="MobileOptimized" content="320">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

        <!-- Stylesheets -->
        <link href="/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <link href="/css/bootstrap.css" rel="stylesheet" type="text/css" media="all" />
        <link rel="stylesheet" href="/css/ladda-themeless.min.css">
        <link href="/css/ladda-themeless.min.css" rel="stylesheet">
        <link href="/css/styles.css" rel="stylesheet" type="text/css" media="all" />
        <!--[if lte IE 9]>
              <link href="/css/ie.css" rel="stylesheet" type="text/css"  media="all"  />
            <![endif]-->
        <!--[if lte IE 7]>
              /css/lte-ie7.js
            <![endif]-->
        <?php if (isset($headCSS)) { echo $headCSS; } ?>
        <?php $protocol = $_SERVER['HTTPS'] == 'on' ? 'https' : 'http'; ?>

        <!-- Icons -->
        <link rel="shortcut icon" type="image/x-icon" href="/images/favicon.ico">
        <link rel="canonical" href="http://evolveskateboards.ru/" />

        <!-- Custom Fonts -->
        <link href='<?php echo $protocol; ?>://fonts.googleapis.com/css?family=.|Lato:light,normal,bold|Lato:light,normal,bold|PT+Sans+Narrow:light,normal,bold|Merriweather:light,normal,bold' rel='stylesheet' type='text/css'>

        <!-- jQuery and jQuery fallback -->
        <script src="<?php echo $protocol; ?>://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script>window.jQuery || document.write("<script src='/js/jquery.min.js'>\x3C/script>")</script>

        <script src="/js/jquery-migrate-1.2.1.js"></script>
        <script src="/js/bootstrap.js" type="text/javascript"></script>
        <script src="/js/spin.min.js"></script>
        <script src="/js/ladda.min.js"></script>
        <script src="/js/ladda.jquery.min.js"></script>
        <script src="/js/bootstrap.file-input.js"></script>
        <script src="/js/jquery.inputmask.js"></script>
        <script src="/js/cloudzoom.js" type="text/javascript"></script>
        <script src="/js/option_selection.js" type="text/javascript"></script>
        <script type="text/javascript" src="/js/ga_urchin_forms.js"></script>

        <script src="/js/bootstrapValidator.min.js"></script>
        <script src="/js/bootstrapvalidator/lang/ru_RU.js"></script>
        <script src="/js/tools.js"></script>
        <script src="/js/app.js" type="text/javascript"></script>
        <?php if (isset($headScripts)) { echo $headScripts; } ?>
    </head>
    <body class="<?=(isset($bodyClass) ? $bodyClass : "") ?>">
    <!--script>
        window.fbAsyncInit = function() {
            FB.init({
                appId      : '1524599041117229',
                xfbml      : true,
                version    : 'v2.1'
            });
        };

        (function(d, s, id){
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {return;}
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    </script-->
	<!-- Google Tag Manager -->
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-TZ7MZ8"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-TZ7MZ8');</script>
<!-- End Google Tag Manager --> 