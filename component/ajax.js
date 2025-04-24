var _f = document.currentScript.src.replace("/ajax.js", "") ;
var vaPath = _f.split("/") ;
var __COMPONENT_FOLDER__ = "../" + vaPath [vaPath.length-1] ;
function compFolder(){return __COMPONENT_FOLDER__ ;}
document.write("<script id='mainScript' type='text/javascript' src='" + compFolder() + "/ajax.php'></script>") ;