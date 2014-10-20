<!DOCTYPE html>
<html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">
    <meta content="yes" name="apple-mobile-web-app-capable"/>
    <meta content="yes" name="apple-touch-fullscreen"/>
    <meta content="telephone=no" name="format-detection"/>
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <title>私属小游戏</title>
    <link rel="stylesheet" type="text/css" href="assets/css/index.css">
    <style type="text/css">
          .drag-box{
              background-image: url("assets/images/upload.png");
              z-index: 500;
              margin-bottom:0px ;
          }
          .play-container{
              margin-bottom:0px ;
          }
          .count-down{
            display:none;
           }
           .masker, .load{
            display:none;
           }
           .overcast {
            width: 312px;
            height: 312px;
            margin: 0 auto;
           margin-top:0;
            position: relative;
            background-color: #000;
            z-index: 500;
             opacity: 1;
           display:none;

          }
          .progress{
                position: absolute;
                top: 50px;
                left: 50px;
                font-size: 20px;
                color:
          }
         .webuploader-pick{
             height:312px;
          }
      </style>
      <script src="assets/js/zepto.min.js"></script>
      <link rel="stylesheet" type="text/css" href="http://diygame.qiniudn.com//js/webuploader/webuploader.css">
      <script type="text/javascript" src="http://diygame.qiniudn.com//js/webuploader/webuploader.custom.js"></script>
    </head>
    <body>
    <script>
        var res_path = "http://diygame.qiniudn.com//spellfriend/";
        var _host = "http://" + window.location.host;
        var people_img = '';
    </script>
    <h1 class="game-title"></h1>
    <div class="drag-content">
        <div class="play-container" id="who_picker" >
            <div class="drag-box" id = "upload"></div>
        </div>
        <br>
        <div class="timer">
            <div class="timer-con">
                <div class="step-con">
                    <img class="step-con" src="assets/images/step.png" width="20" height="20">
                </div>
                <span id="counter" class="t counter">0&nbsp;步</span>
            </div>
        </div>
        <div class="overcast"></div>
    </div>
    <div class="progress"  id="progress"></div>
    <div style="display:none"></div>
    <script>
    var game_data = {
        who:"",
        where :"",
        show: function()
        {
            alert("who:" + game_data.who);
            alert("show:" + game_data.where);
        }
    }
    var _wxid = "";
    var _model={
        _post:function(){
          //_title=$id("game_title").value.trim();
          _title ="";
          _image = game_data.where;

          if((game_data.where == 'undefined') )
          {
              show_toast("照片正在上传失败，请重新上传...");return;
          }
          var post_data = "wxid="+_wxid + "&title="+_title + "&image=" + _image;

          $post("/wxapi.php/spellfriend/submit_new_model",post_data,"请稍候","_model._ok");
       },

       _ok:function(json){
           _mid = json.mid;
            _localUrl = "/wxapi.php/spellfriend/play/mid/" + _mid;
            location.href=_localUrl;
         }
    };

    var who_uploader = WebUploader.create({
        auto:true,
        server: "upload.php",
        pick: '#who_picker',
        sendAsBinary : true,
        thumb : {
                width  : 120,
                height : 120,
                crop: true,
            },
        compress : {
                width  : 300,
                height : 300,
                crop: true,
                preserveHeaders: true,
                quality: 80
        },
        accept: {
            title: 'Images',
            extensions: 'gif,jpg,jpeg,bmp,png',
            mimeTypes: 'image/*'
        }
    });
    who_uploader.on('fileQueued', function(file){
    });
    who_uploader.on( 'uploadSuccess', function( file, ret ) {
            game_data.where = ret.path;
            if(game_data.where == 'undefined')
            {
                alert("照片正在上传失败，请重新上传...");return;
            }
            document.getElementById('progress').innerHTML = "上传成功，正在生成游戏....";
            _model._post();
            console.log(game_data.where);
    });


    who_uploader.on( 'uploadError', function( file, ret ) {
         alert(ret)
    });
    who_uploader.onUploadProgress = function( file, percentage ) {
        document.getElementById('progress').innerHTML = "图片正在上传中，请稍后....";
        console.log( file.statusText );
    };

    </script>
  </body>
</html>
