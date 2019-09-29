<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Invoice</title>
    <style>
        body{
            padding:10px;
            border:10px solid green;
                        
        }
        body .head{
            background:red;
            text-align:center;
        }
    </style>
</head>
<body>
    <div class="head">
        <img src="/images/logo.png" alt=""> 
        
        <h2>Allier Air</h2>
        <h1>Trip Details</h2>
        <div class="name">
        name: <?php echo $pepe;?>
        </div>
    </div>
</body>
</html>