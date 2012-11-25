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
                    <li class="ACT"><a href="index.php">Home</a></li>
                    <li><a href="upload.php">Photo Upload</a></li>
                    <li><a href="gallery.php">Gallery</a></li>
                </ul>
            </div>
            <div id="featured"><img src="static/images/header-img.jpg" alt="" title="" /></div>
            <div id="content">
                <div id="content-Main">
                    <h2>Welcome to ShareImg</h2>
                    <p>Nsul claritas Quorum ira ago ruo Moestitia, subnego en proletarius os nos, vivo his ferox Seputus lex Triduum tam in quinquagesimus nec usquequaque vomer requietum soleo potens nam Contemno ac qui pe.</p>
                    <p>Nsul claritas Quorum ira ago ruo Moestitia, subnego en proletarius os nos, vivo his ferox Seputus lex Triduum tam in quinquagesimus nec usquequaque vomer requietum soleo potens nam Contemno ac qui pe.</p>
                    <p>Nsul claritas Quorum ira ago ruo Moestitia, subnego en proletarius os nos, vivo his ferox Seputus lex Triduum tam in quinquagesimus nec usquequaque vomer requietum soleo potens nam Contemno ac qui pe.</p>
                    <p><a href="upload.php">Upload your photos now</a></p>
                </div>
                <div id="aside">
                    <ul id="image_list">
                    </ul>
                </div>
            </div>

        </div>        
    </div>
<?php
include_once("$__DIR_CUR__/common_footer.php");
?>
