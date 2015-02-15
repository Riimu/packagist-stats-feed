<!DOCTYPE html>
<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title><?=$pageTitle?></title>
  <style type="text/css">
   body {
    background-color: #E8E8F0;
   }

   div {
    font-family: Verdana;
    background-color: #F8F8F8;
    margin: 20px auto;
    padding: 20px;
    width: 850px;
   }
   h1 {
    padding: 0px;
    margin: 0px 0px 1em 0px;
   }
   #copyright {
    margin-top: 3em;
    font-size: 65%;
    padding-top: 1em;
    border-top: 1px solid #400000;
   }
  </style>
 </head>
 <body>
  <div>
<?php $content->render(); ?>

   <p id="copyright">Copyright &copy; 2015 to <a href="http://riimu.net">Riikka Kalliomäki</a>.
    Source at <a href="https://github.com/Riimu/packagist-stats-feed">Github</a></p>
  </div>

<!-- analyze -->
<script type="text/javascript">
  var _paq = _paq || [];
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u="//stats.riimu.net/";
    _paq.push(['setTrackerUrl', u+'analyze.php']);
    _paq.push(['setSiteId', 5]);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'analyze.js'; s.parentNode.insertBefore(g,s);
  })();
</script>
<noscript><p><img src="//stats.riimu.net/analyze.php?idsite=5" style="border:0;" alt="" /></p></noscript>
<!-- End analyze Code -->

 </body>
</html>
