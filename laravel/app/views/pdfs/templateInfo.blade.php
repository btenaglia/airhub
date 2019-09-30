<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Invoice</title>
    <style>
        body{
            font-family: sans-serif;
            border:20px solid #274e13;

        }
        body{
          
        }
        body .head{
            text-align:center;
            border-bottom: 10px solid;
            padding:20px 0;
        }
        .head .name h2{
            font-size: 1em;
           }
        body .ticket{
            text-align: center;
            border-bottom: 10px solid;
        }
        .ticket strong{
            line-height: 4;
        }
        .ticket h2{
            background: #b9b9b9;
            margin: 0;
            padding: 10px;
            text-decoration: underline;
            margin-bottom: 10px;
        }
        .ticket table{
            border-spacing: 0;
            width: 90%;
            margin: 0 auto;
        }
        table tr{
            height: 40px;
        }
        .color{
            background: #b9b9b9;
        }
        footer{
            text-align:center;
            margin:20px 0;
        }
    </style>
</head>
<body>
    <div class="head">
        <img src="<?php echo $_SERVER["DOCUMENT_ROOT"].'/images/logo.png'?>" width="100" alt="">
        <h2 style="color:#274e13">Allier Air</h2>
        
        <div class="name">
            <h2 class="color">Name:</h2>
            {{$data['complete_name']}}
        </div>
    </div>
        <div class="ticket">
            <h2>TRIP DETAILS</h2>
            <strong>August 23,2019</strong>
            <table cellpacing="0" >
            <tr class="color"><td>Departing Airport</td><td>Arriving Airport</td></tr>
            <tr><td>Departing Airport</td><td>Arriving Airport</td></tr>
            <tr class="color"><td>Departing Airport</td><td>Arriving Airport</td></tr>
            <tr><td>Departing Airport</td><td>Arriving Airport</td></tr>
            </table>
        </div>
        <footer>
             <h1 style="color:#274e13;">Allier Air</h1>
             (508) 231-5800 <br>
             info@alliesair.com
             www.alliesair.com <br>
             550 barnstable Road | Hyannis,MA | 02601 <br>
        </footer>
</body>

</html>
