<html>
<head>
</head>
<body>
<style>
#msg{
  color: #4b4c4c;
    margin: 20px;
    background: #e2dddd;
    padding: 20;
    border-radius: 5px;
    font-size: 1rem;
    font-family: sans-serif;
    visibility: hidden;
    position: absolute;
}
}
iframe .payForm{
  width:100% !important;
}
</style>
<!-- Other HTML Here -->

<!-- Add this script tag prior to embedding the iFrame -->
<div id="msg">

</div>

<script>


// .payForm
window.addEventListener("message", receiveMessage, false);
// document.getElementsByClassName('payForm')[0].style.width= "100%"
console.log(`%c document.getElementsByClassName('payForm') 🤞`, 'color:orange; font-size:12px; padding:2px 4px; background: #333; border-radius:4px;',document.getElementsByClassName('payForm'))
function receiveMessage(event) {
  // Make sure the value for allowed matches the domain of the iFrame you are embedding.
  // var allowed = "https://api.sandbox.domain.com";
  // Verify sender's identity
  // if (event.origin !== allowed) return;

  // Add logic here for doing something in response to the message
  console.log(event); // for example only, log event object
  console.log(JSON.parse(event.data)); // for example only, log JSON data
  var data = JSON.parse(event.data)
  var body = {
    status : data.reason_code_id,
    id:data.transaction_api_id
  }
  var path = location.protocol +"//"+ window.location.host

  fetch(`${path}/api/v1/public/payments/status`,{
    method:'POST',
    body:JSON.stringify(body),
    headers:{
    'Content-Type': 'application/json'
  }
  }  ).then(data => {
    document.getElementById('msg').innerHTML = data.data == 'ok' ? "Payment success" : "Payment Declined"
    document.getElementById('msg').style.visibility = "visible"
    console.log(`%c status ${data.data}`, 'color:orange; font-size:12px; padding:2px 4px; background: #333; border-radius:4px;');
  }).catch(e => {console.log(e)
    document.getElementById('msg').innerHTML =  "Payment Declined"
    document.getElementById('msg').style.visibility = "visible"
   })
}

</script>

<!-- include the iframe after the script tag for the event listener -->
 <iframe style="border:none" width="100%" height="100%" src="<?=$content?>"></iframe>
</body>
</html>