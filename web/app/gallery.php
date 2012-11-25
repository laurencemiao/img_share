<?php
$__DIR_CUR__ = dirname(__FILE__);

$_TMPL_js_include = array(
    'static/js/jquery-1.8.3.min.js',
);

$_TMPL_title = "ShareImg - The Image Sharing Service";

include_once("$__DIR_CUR__/common_header.php");
?>
    <div id="page-Wrapper">
        <div id="page">
            <div id="header">
                <h1 id="logo"><a href="index.php"><img src="static/images/logo.png" alt="shareimg - logo" title="ShareImg - The Image Sharing Service" /></a></h1>
                <ul id="nav-Main">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="upload.php">Photo Upload</a></li>
                    <li class="ACT"><a href="gallery.php">Gallery</a></li>
                </ul>
            </div>            
            <div id="content">
                <div id="gallery">
                    <select id='sort_menu' onchange='change_sort_menu();'>
                        <option value="">Sort your photos</option>
                        <option value="1">Oldest First</option>
                        <option value="2">Newest First</option>
                    </select>
                    <ul id="image_list">
                    </ul>
                </div>
            </div>

        </div>        
    </div>
<?php
include_once("$__DIR_CUR__/common_footer.php");
?>
