<?php
$__DIR_CUR__ = dirname(__FILE__);

$_TMPL_js_include = array(
    'static/js/jquery-1.8.3.min.js',
);
$_TMPL_title = "ShareImg - The Image Sharing Service";

include_once("$__DIR_CUR__/common_header.php");
?>
<script type="text/javascript">
// http://stackoverflow.com/questions/7909161/jquery-iframe-file-upload
$(document).ready(function () {
    $("#upload_submit").click(function () {
        if($('#upload_file').val()){
            $("body").append('<iframe name="upload_iframe" id="upload_iframe" style="display: none" />');

            $("#upload_iframe").load(function () {
                $('#upload_form').each(function(){
                    this.reset();
                });
                iframe = $("#upload_iframe")[0].contentWindow.document;
                contents = iframe.body.innerHTML;
                $("#upload_iframe").remove();

                message("file posted");

                var file_list = eval(contents);
                if(typeof file_list == 'object' && file_list.constructor == Array){
                    file = file_list.pop();
                    console.log("server api ok");
                    if(typeof file != 'undefined'){
                        message("file uploaded, still need confirmation");
                        console.log(file);
                        id = file['id'];
                        title = 'title_4_file_' + id;
                        desc = 'desc_4_file_' + id;
                        message("uploading metadata and confirm with server");
                        $.ajax({
                            type: "POST",
                            url: "api/image",
                            data: '{"id":"' + file['id'] + '", "title":"' + title + '", "desc":"' + desc + '"}',
                            success: function (result) {
                                message("<font color='green' size='4'><b>Confirmed, upload succeeded!</b></font>");
                                reload_gallery('new_first');
                                message("gallery list refreshed");
                            }
                        });
                    }
                }else{
                    message("<font color='red'><b>unrecognized response from server</b></font>");
                    console.log(contents);
                }
            });

            $('#upload_form').attr("target", "upload_iframe").submit();
        }else{
            message("<font color='red' size='4'><b>Please select a image file to upload.</b></font>");
            return false;
        }

    });

});

function message(html){
    var today = new Date();
    var h = today.getHours();
    var m = today.getMinutes();
    var s = today.getSeconds();
    var time = h+":"+m+":"+s;
    html = "<p>[" + time + "] "+ html + "</p>";
    $('#message').prepend(html);
}

</script>

    <div id="page-Wrapper">
        <div id="page">
            <div id="header">
                <h1 id="logo"><a href="index.php"><img src="static/images/logo.png" alt="shareimg - logo" title="ShareImg - The Image Sharing Service" /></a></h1>
                <ul id="nav-Main">
                    <li><a href="index.php">Home</a></li>
                    <li class="ACT"><a href="upload.php">Photo Upload</a></li>
                    <li><a href="gallery.php">Gallery</a></li>
                </ul>
            </div>
            <div id="featured"><img src="static/images/header-img.jpg" alt="" title="" /></div>
            <div id="content">
                <div id="content-Main">
                    <h2>Upload your images</h2>
                    <p>Gaza provisio conscientia dux effrenus Promus sui secundus rutila. Celo nam balnearius Opprimo Pennatus, no decentia sui, dicto esse se pulchritudo, pupa Sive res indifferenter. Captivo pala pro de</p>
                    <form method='post' id='upload_form' name='upload_form' enctype='multipart/form-data' action='api/upload'>
                        <ol>
                            <li><input type="file" name="upload_file" id="upload_file"/></li>
                            <li><input type="submit" name="upload_submit" id="upload_submit" value="Upload photos"/></li>
                        </ol>                        
                    </form>
                    <div id="message">
                    </div>
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
