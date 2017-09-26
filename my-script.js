function openTab(tab) {
    var x = document.getElementsByClassName("w3-button");
    for (i = 0; i < x.length; i++) {
      if(x[i].innerHTML ==tab){
        x[i].style.background = "#000";

      }else{
        x[i].style.background = "#a81c41";
      }
    }

    var x = document.getElementsByClassName("tab");
    for (i = 0; i < x.length; i++) {
        x[i].style.display = "none";
    }
    document.getElementById(tab).style.display = "block";

}
