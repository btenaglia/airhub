
<style>
    body,html{
        padding:0;
        margin:0;
        position:relative;
    }
.container{

    align-items: center;

    height: 100%;
    font-family: sans-serif;
    background-repeat: repeat;
    background-color: #b8fdf6;
    background-size: cover;
    background-image: url(/images/flight.png);
    position: absolute;
    display: flex;
    flex-direction: column;
 
    justify-content: space-around;

}
.container .info{
    display:flex;
    align-items: center;
    flex-wrap: wrap;
    justify-content: space-around;

}
.container .info .text{

        color: white;
        text-shadow: 1px 1px 10px #000000e0;
        text-align: center;
}
.container footer{
    color:white;
    text-align:center;
    text-shadow: 1px 1px 10px #000000e0;
}
@media screen (max-width:240px){
    .container .info{
        display:block;
        text-align: center;

    }
    .container .info img{
        width: 350px;
    }
}
</style>

<div class="container">
            <div class="info">
                <img src="/images/Airplane.png" alt="">
                <div class="text">
                Thank you !
                Contact us to report the payment.
                </div>
            </div>

<footer>Allies Air Co.</footer>
</div>
