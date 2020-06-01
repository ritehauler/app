<html>
<body>
<h1 align="center">Please wait...</h1>
<!-- <a target="_top" href="{{ $login_url }}">Click here to proceed</a> -->
<script type="text/javascript">
    var url = "{{ $login_url }}";
    url = url.replace(/&amp;/g, "&");
    //console.log("url",url);
    window.top.location = url;
</script>
</body>
</html>