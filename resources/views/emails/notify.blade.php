<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>matarebeli.ge</title>
    <style>
        body {background-color: #ebf5f9;}
        * {margin: 0; padding: 0; box-sizing: border-box;}
        .mail-wrapper {width: 600px; margin: 30px auto;}
        .mail-wrapper .logo-wrapper {width: 100%; padding-bottom: 20px; text-align: center;}
        .mail-wrapper .logo-wrapper img {display: inline-block;}
        .mail-wrapper .header {height: 60px; width: 100%; background-color: #ffb953; padding: 20px 40px;}
        .mail-wrapper .header h1 {color: #ffffff; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-weight: bold; line-height: 20px;}
        .mail-wrapper .content {width: 100%; height: auto; padding: 50px 100px; background-color: #ffffff;}
        .mail-wrapper .content h2 {color: #000000; font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: normal; line-height: 20px; margin-bottom: 10px;}
        .mail-wrapper .content .code {width: 100%; height: 80px; text-align: center; padding: 15px; background-color: #f2f2f2; margin-bottom: 20px;}
        .mail-wrapper .content .code h3 {color: #000000; font-family: Arial, Helvetica, sans-serif; font-size: 48px; font-weight: normal; line-height: 50px;}
        .mail-wrapper .content .qr-wrapper {width: 100%; padding: 15px 0 35px; text-align: center;}
        .mail-wrapper .content .qr-wrapper img {display: inline-block;}
        .mail-wrapper .content .desc {width: 100%; padding-bottom: 30px}
        .mail-wrapper .content .desc p {color: #000000; font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: normal; line-height: 18px;margin-bottom: 10px;}
        .mail-wrapper .content .btn {display: block; width: 100%; height: 80px;color: #000000; font-family: Arial, Helvetica, sans-serif; font-size: 18px; font-weight: bold; text-align: center; text-decoration: none; padding: 30px 0; background-color: #ffb953;}
        .mail-wrapper .content .btn:hover {background-color: #ffc065;}
    </style>
</head>
<body>
<div class="mail-wrapper">
    <div class="logo-wrapper">
        <img src="https://matarebeli.ge/assets/images/matarebeli-logo.png" alt="matarebeli.ge">
    </div>
    <div class="header">
        <h1>THANK YOU FOR USING OUR SERVICE</h1>
    </div>
    <div class="content">

        @foreach( $transaction->tickets as $key => $ticket )
            @if( $key == 0 )
                <h2>Departure ticket purchase code</h2>
                <div class="code">
                    <h3>{{$ticket->request_id}}</h3>
                </div>
            @else
                <h2>Departure ticket purchase code</h2>
                <div class="code">
                    <h3>{{$ticket->request_id}}</h3>
                </div>
            @endif
        @endforeach

        <div class="qr-wrapper">
            <img src="{{$ticket->getQR()}}" alt="matarebeli.ge">
        </div>
        <div class="desc">
            <p>Please see the .PDF version of your ticket in attached file.</p>
            <p>It is important to show ticket holder Passport ID during the check in.</p>
        </div>

        <div class="desc">
            <p>მიმაგრებულ ფაილში იხილეთ ბილეთის საბეჭდი ვერსია.</p>
            <p>ელექტრონული სამგზავრო დოკუმენტით მგზავრობისათვის ვაგონის გამცილებელთან საჭიროა წარადგინოთ მგზავრების პიროვნების დამადასტურებელი მოწმობა.</p>
        </div>

        <a href="" class="btn">VIEW PRINTABLE TICKETS</a>
    </div>
</div>
</body>
</html>