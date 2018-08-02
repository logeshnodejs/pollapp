<?php
include("check_user.php");
define("IN_SCRIPT","1");
session_start();
include("../include/SiteManager.class.php");
$website = new SiteManager();
$website->is_admin=true;
$website->data_path="../";
$website->InitData();
$website->LoadSettings();

$website->LoadTemplate();

if(isset($_REQUEST["page"]))
{
	$website->check_word($_REQUEST["page"]);
	$website->SetPage($_REQUEST["page"]);
}
else
{
	$website->SetPage("home");
}

$website->Render();
?>