/**
 * 
 */

var $post=function(url,parameters,loadingMessage,functionName){
    var request=new XMLHttpRequest();
    var method="POST";
    if(parameters==""){method="GET";parameters=null;}
    request.open(method,url,true);
    request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    request.onreadystatechange=function(){
	 if(request.readyState==4){

	     if(request.status==200){
		    if(functionName){
		       try{  
			      var json = eval("("+ request.responseText+")");
			      eval(functionName+"(json)");
                }catch(e){}
		    }
	     }
	 }
    };
    request.send(parameters);
};