<script type="text/javascript">
//<![CDATA[
var quote=new Array();
  quote[0]='"After all, happiness is the ultimate success in life!" -<i>Dr. Elia Gourgouris</i>';    /* add as many quotes as you like!*/

var speed=0;    /*this is the time in milliseconds adjust to suit,*/
var q=0;

function showQuote() {

     document.getElementById("quotes").innerHTML=quote[q];
     q++;
if(q==quote.length) {
     q=0;
  }
}
setInterval('showQuote()',speed);
   
 //]]>
</script>