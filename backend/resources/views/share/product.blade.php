<!DOCTYPE html>
<html>
<head>
    <meta property="og:site_name" content="<?php echo $app_name; ?>"/>
    <meta property="og:title" content="{{ $data['title'] }}" />
    <meta property="og:image" content="{{ isset($data['gallery'][0]->file) ? $data['gallery'][0]->file : "" }}" />
    <meta property="og:description" content="{{ $data['meta_description'] }}" />
    <meta property="og:url" content="{{ \Request::fullUrl() }}">
    <meta property="description" content ="{{ $data['meta_description'] }}">
    <meta property="keywords" content ="{{ $data['meta_keywords'] }}">
    <!-- Deeplinking Android -->
    <meta property="al:android:url" content="<?php echo $schema; ?>" />
    <meta property="al:android:package" content="<?php echo $playstore_keystore; ?>" />
    <meta property="al:android:app_name" content="<?php echo $app_name; ?>" />
    <!-- Deeplinking iOS -->
    <meta property="al:ios:url" content="<?php echo $schema; ?>" />
    <meta property="al:ios:app_store_id" content="<?php echo $appstore_id; ?>" />
    <meta property="al:ios:app_name" content="<?php echo $app_name; ?>" />
    <meta property="al:web:should_fallback" content="false" />
    <meta property="og:type" content="website" />
</head>
<body>
<script type="text/javascript">

     window.alert = null; // disable alerts
    alert = null;

    <?php if($source == 'ios') : ?>

            window.location = '<?php echo $schema;?>';
    setTimeout(function() {
        window.location = '<?php echo $appstore_url2;?>';
    },100);

        <?php elseif($source == 'android') : ?>

            window.location = '<?php echo $schema;?>';
    setTimeout(function() {
        window.location = '<?php echo $playstore_url2;?>';
    },500);
    <?php else: ?>
            window.location = "{{ url('/') }}"

    <?php endif; ?>

</script>
</body>
</html>
