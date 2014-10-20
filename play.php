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
        <style>
            .sbg {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                text-align: center;
                color: #fff;
                font-size: 22px;
                line-height: 1.7em;
                background: rgba(0,0,0,0.85);
                z-index: 200;
            }
            .sbg .arron {
                position: absolute;
                top: 8px;
                right: 8px;
                width: 100px;
                height: 100px;
                background: url(assets/images/arron.gif) no-repeat;
                background-size: 100px 100px;
            }
            .sbg p {
                padding-top: 78px;
            }
            .moregame, .replay{
                width: 60px;
                height: 30px;
                background-color: #35ff5e;
                text-shadow: 1px;
            }
            .makegame{
                width: 198px;
                height: 42px;
                margin: 10px auto;
                background-image:url(assets/images/make.png);
                text-align: center;
                overflow: hidden;
            }
        </style>
        <script src="assets/js/main.js"></script>
    </head>
    <body>
        <!--页面集合-->
        <div id="pageWrapper">
            <div id="pages">
                <div id="page_default" class="pagemodel">
                    <div class="initloading" ><span class="normal-loading"></span></div>
                </div>
            </div>
        </div>
        <script>
            var res_path = "http://diygame.qiniudn.com//spellfriend/";
            var _host = "http://" + window.location.host;
            var people_img = _host + '/uploads/01.jpg';
        </script>
        <script type="text/template" style="display:none;" id="pageTemplate">
            <div id="<%= id%>" class="pagemodel">
                <div class="initloading" >
                    <span class="normal-loading"></span>
                </div>
            </div>
        </script>
        <script type="text/template" style="display:none;" id="jigsawTemplate">
            <div class="virturl-body"></div>
            <h1 id="game-title" class="game-title">111</h1>
            <div class="drag-content" id="drag-content">
                <div class="play-container">
                    <div class="drag-box"></div>
                    <div class="masker">
                        <div class="load">
                            <div class="first-layer"></div>
                            <div class="second-layer"></div>
                            <div class="third-layer"></div>
                            <div class="count-down" >
                                <div class="play-button play-button-ready playbtn" ></div>
                                <ul>
                                    <li>3</li>
                                    <li>2</li>
                                    <li>1</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <span class="done">done</span>
                    <div class="menu-box" id="menuBox">
                        <div class="overcast" id="overcast">
                            <div class="menu">
                                <div class="menu-body" id="menuBody">
                                    <div class="h1" id="h1">哇，你好厉害啊</div>
                                    <div class="h2" id="h2">你只用了8步就完成了！</div>
                                    <a href="javascript:" id="shareBtn" class="overBtn share">炫耀一下</a>
                                    <a href="javascript:" id="madeBtn" class="overBtn madeGame">我也要定制</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="timer">
                    <div class="timer-con">
                        <div class="step-con">
                            <img class="step-con" src="assets/images/step.png" width="20" height="20">
                        </div>
                        <span id="counter" class="t counter">0&nbsp;步</span>
                    </div>
                    <div class="makegame" id="makegame"></div>
                </div>
            </div>
        </script>

        <div id="sbg" class="sbg" onClick="document.getElementById('sbg').style.display = 'none';">
            <div class="arron"></div>
            <p id="msg">请点击右上角按钮<br />再点击【分享到朋友圈】<br />  喊他们来挑战吧！</p>
        </div>
        <script type="text/template" style="display:none;" id="jigsawLayoutTemplate">
            <% for(var i = 0 ; i < list.length;i++){%>
                <div class="item" sort="<%=list[i].sort%>" dragitem='1' style="width:<%=list[i].w%>px;height:<%=list[i].h%>px;background:url(<%=img%>) no-repeat;background-position:<%=list[i].x%>px <%=list[i].y%>px;background-size:<%=width%>px <%=height%>px;"></div>
            <%}%>
        </script>
        <script src="assets/js/common.js"></script>
        <script src="assets/js/index.min.js"></script>

        <script src="assets/js/wxtools.js"></script>

        <div style="display:none">
            <script type="text/javascript">
                var gameTitle = "";
                var dataForWeixin = {
                        appId : "",
                        MsgImg : people_img,
                        TLImg:people_img,
                        shareurl :"http://l2857.yiyijob.cn/index.php?r=" + _host + "/wxapi.php/spellfriend/play/mid/29832",
                        title :  '我定制了“' + gameTitle + '”看你用几步能得到完整的TA',
                        gametitle: "",
                        desc :   '  定制好友拼图\n-来自《私属小游戏》',

                        before_share : function()
                        {
                            //dataForWeixin.shareurl = "http://l2857.yiyijob.cn/index.php?r=" + _host + "/wxapi.php/tinygame/iphone6/from/tl";
                            //var title = document.getElementById('game-title').innerHTML;
                        },

                        callback : function() {
                            $post("/wxapi.php/spellfriend/inc_share ", "mid=29832");
                        }
                };

                function inputName(){
                   gameTitle = prompt('           亲，给游戏起个贴切的名字吧！',"")
                   // gameTitle = gameTitle.trim();
                    while(true){
                        if (gameTitle!= null && gameTitle!="")
                        {
                            if(gameTitle.length > 20){
                                alert("游戏名称请在10字以内,情重新输入");
                                gameTitle = prompt('       游戏名不让为空嘛，你还是给俺取个名字吧！',"");
                                gameTitle = gameTitle.trim();
                            }
                            else break;
                        }
                        else
                        {
                            gameTitle = prompt('       游戏名不让为空嘛，你还是给俺取个名字吧！',"");
                            //gameTitle = gameTitle.trim();
                        }
                    }
                   document.getElementById('game-title').innerHTML = gameTitle;

                      // var _wxid = "";
                   var mid = "29832";
                   var post_data = "id=" + mid + "&title=" + gameTitle;
                   $post("/wxapi.php/spellfriend/submit_new_titile",post_data,"请稍候","_model._ok");
                }

                function chackName(){
                    gameTitle = '111';
                    if(gameTitle==''|| gameTitle==null || gameTitle==undefined )
                        inputName();
                    dataForWeixin.title = '我定制了“' + gameTitle + '”看你用几步能得到完整的TA';
                }
                window.onload=chackName();


                // 微信分享的数据
                function inc_view()
                {
                    $post("/wxapi.php/spellfriend/inc_view","mid=29832");
                }
                setTimeout(inc_view,5000);

                 function dp_share(){
                       setTimeout(function(){
                           document.getElementById('sbg').style.display = 'block';
                       },500);
                 }


                document.getElementById("shareBtn").onclick = function(){
                    dp_share();
                }
                function attention_wx()
                {
                    alert("关注“私属小游戏”，您可以定制只属于你自己的好友拼图，点击确认立即关注");
                    location.href='http://mp.weixin.qq.com/s?__biz=MzA3NDYxODQwNA==&mid=200461755&idx=1&sn=7ed6fd3a1cdfbcc2b6e3bd8332e42585#rd';
                }

                document.getElementById("madeBtn").onclick = function(){
                    attention_wx();
                 }

                function gameover(){
                    var over1 = "", over2 = "";
                    if(step < 10){
                        over1 = "哇，你好厉害啊";
                        over2 = "你只用了" + step + "步就完成了";

                    }
                    else{
                        over1 = "哎，你弱爆了";
                        over2 = "你用了" + step + "步才完成拼图";
                    }

                    document.getElementById("h1").innerHTML = over1;
                    document.getElementById("h2").innerHTML = over2;
                    document.getElementById("menuBox").style.display ='block' ;
                }

                document.getElementById("makegame").onclick = function(){
                    //_localUrl = "/wxapi.php/spellfriend/creat_game";
                    //location.href=_localUrl;
                    location.href="http://game.zhengguzhiba.com/wxapi.php/index/tip_add/type/6";
                 }
            </script>
        </div>
    </body>
</html>
