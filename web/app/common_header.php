<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>  
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<?php
foreach($_TMPL_js_include as $s){
    echo "<script type=\"text/javascript\" src=\"$s\"></script>\n";
}
?>
<link rel="stylesheet" type="text/css" href="static/css/styles.css" />
<title><?php echo $_TMPL_title;?></title>
<script type="text/javascript">
$(document).ready(reload_gallery());
function reload_gallery(order, count){
    if(typeof count === 'undefined'){
        count = 100;
    }
    if(typeof order === 'undefined'){
        order = 'new_first';
    }
    $.ajax({
        type: "GET",
        url: "api/image?start=0&count=" + count + "&order=" + order,
        success: function (result) {
            image_list = eval(result);
            $("#image_list").empty();
            for(var i in image_list){
                if(image_list[i].hasOwnProperty('thumb_url') && image_list[i].thumb_url.length>0)
                    img_url = image_list[i]['thumb_url'];
                else
                    img_url = "static/images/processing.jpg";
                popup_url = "popup.html?id=" + image_list[i]['id'];
                img_title = image_list[i]['title'];
                img_change_time = image_list[i]['change_time'];
                $("#image_list").append("<li><a href='#' onClick='popup(\"" + popup_url + "\", \"" + img_title + "\");'><img src='" + img_url + "' alt title='" + img_title + "(" + img_change_time + ") '></a></li>");
            }
            if(!$("#image_list").html()){
                $("#image_list").append("<h2>There are no pictures, upload <a href='upload.php'>yours</a></h2>");
            }
        }
    });
}


function change_sort_menu(){

    var opt = document.getElementById("sort_menu");
    if( 1 == opt.selectedIndex){
        reload_gallery('old_first');
    }else{
        reload_gallery('new_first');
    }
}

function popup(url, name) {
    newwindow=window.open(url, name, 'height=450,width=600');
    if (window.focus) {newwindow.focus()}
        return false;
}

</script>
</head>
<body>
