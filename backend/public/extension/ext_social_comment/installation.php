<?php
// include common
require __DIR__ . "/../common.php";

$plugin_identifier = "ext_social_comment"; // identifier name (routes,module class_name)
// route base
$base_route = "";
$api_base_route = "extension/social/comment"; // without leading-slash
// other
$plugin_name = "Social Comment";
$plugin_description = "Lorem ipsum dolar sit amit demo text";
// version
$version = "1.0";
// array vars
$features = $webservices = array();
// string vars
$install_sql = $uninstall_sql = $upgrade_sql = $install_files = $uninstall_files = $update_note = "";

//features
$features = array();
// webservices ( uri => name )
$webservices = array();


// configurations
$config = array(
    "identifier" => $plugin_identifier,
    "base_route" => $base_route,
    "api_base_route" => $api_base_route,
    "name" => $plugin_name,
    "description" => $plugin_description,
    "version" => $version,
    "features" => $features,
    "webservices" => $webservices
);

//exit(json_encode($config));
?>

<?php // Install SQL : Base ?>
<?php //$install_sql .= trim(file_get_contents("sql/install.sql",true)); ?>
<?php $install_sql .= getFileContents(__DIR__ . "/sql/install", ".sql"); ?>
<?php $install_sql .= getFileContents(__DIR__ . "/sql/install/api", ".sql"); ?>

<?php // un-installation SQL : Base ?>
<?php //$uninstall_sql .= trim(file_get_contents("sql/uninstall.sql",true)); ?>
<?php $uninstall_sql .= getFileContents(__DIR__ . "/sql/uninstall", ".sql"); ?>

<?php // installation Files ?>
<?php ob_start(); ?>
/resources/lang/en/ext_social_comment.php
/app/Http/Routes/Ext_SocialComment.php
/app/Http/Models/Extension/Social/ExtSocialComment.php
/app/Http/Hooks/ExtSocialComment.php
/app/Http/Hooks/ExtSocialCommentModel.php
/app/Http/Controllers/Api/Extension/Social/Comment/IndexController.php
<?php $install_files = $uninstall_files = ob_get_clean(); ?>


<?php
// Wildcards dictionary starts
/*
{base_route}
{api_base_route}
{wildcard_identifier}
{wildcard_title}
{wildcard_plural_title}
{wildcard_ucword}
{wildcard_pk}
{wildcard_datetime}
{plugin_identifier}
{wildcard_ucword}
{wildcard_identifier}
{file_identifier}
{file_ucword}
{base_entity_id}
{target_identifier}
{target_ucword}
{target_pk}
{target_entity_id}
{file_target_identifier}
{file_target_ucword}
*/
// Wildcards dictionary ends


return array(
    "config" => $config,
    "update_note" => trim($update_note),
    "install_sql" => trim($install_sql),
    "uninstall_sql" => trim($uninstall_sql),
    "install_files" => trim($install_files),
    "uninstall_files" => trim($uninstall_files)
);
?>
