<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title></title>
    <style>
        /* Reset Begin */
        html, body, div, span, applet, object, iframe,
        h1, h2, h3, h4, h5, h6, p, blockquote, pre,
        a, abbr, acronym, address, big, cite, code,
        del, dfn, em, img, ins, kbd, q, s, samp,
        small, strike, strong, sub, sup, tt, var,
        b, u, i, center,
        dl, dt, dd, ol, ul, li,
        fieldset, form, label, legend,
        table, caption, tbody, tfoot, thead, tr, th, td,
        article, aside, canvas, details, embed,
        figure, figcaption, footer, header, hgroup,
        menu, nav, output, ruby, section, summary,
        time, mark, audio, video {
            margin: 0;
            padding: 0;
            border: 0;
            font-size: 100%;
            font: inherit;
            vertical-align: baseline; }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box; }

        body {
            font-size: 62.5%;
            line-height: 1; }

        .pdf-wrapper {
            width: 860px;
            margin: 20px auto; }
        .pdf-wrapper .header {
            width: 100%;
            height: 88px;
            padding: 14px 22px;
            background-color: #f2f2f2; }
        .pdf-wrapper .header .logo-wrapper {
            float: left;
            width: 60px;
            height: 60px; }
        .pdf-wrapper .header .logo-wrapper img {
            display: block;
            width: 60px;
            height: 60px; }
        .pdf-wrapper .header .col-1 {
            float: left;
            width: 90px;
            height: 40px;
            padding: 5px 10px;
            margin: 10px 0 0;
            border-right: 1px solid #b2b2b2; }
        .pdf-wrapper .header .col-1 p {
            display: block;
            color: #7f7f7f;
            font-size: 12px;
            text-align: right;
            line-height: 15px; }
        .pdf-wrapper .header .col-2 {
            float: left;
            width: 90px;
            height: 40px;
            border-right: 1px solid #b2b2b2; }
        .pdf-wrapper .header .col-2 h2 {
            display: block;
            color: #000000;
            padding: 5px 10px;
            text-align: right; }
        .pdf-wrapper .header .col-2 h2 strong {
            display: inline-block;
            font-size: 18px;
            font-weight: bold;
            line-height: 30px;
            vertical-align: top; }
        .pdf-wrapper .header .col-2 h2 span {
            display: inline-block;
            float: right;
            color: #7f7f7f;
            font-size: 10px;
            text-align: left;
            line-height: 15px;
            padding-left: 4px;
            vertical-align: top; }
        .pdf-wrapper .header .col-3 {
            float: left;
            width: 80px;
            height: 40px;
            border-right: 1px solid #b2b2b2; }
        .pdf-wrapper .header .col-3 h2 {
            display: block;
            color: #000000;
            padding: 5px 10px;
            text-align: right; }
        .pdf-wrapper .header .col-3 h2 strong {
            display: inline-block;
            font-size: 18px;
            font-weight: bold;
            line-height: 30px;
            vertical-align: top; }
        .pdf-wrapper .header .col-3 h2 span {
            display: inline-block;
            float: right;
            color: #7f7f7f;
            font-size: 10px;
            text-align: left;
            line-height: 15px;
            padding-left: 4px;
            vertical-align: top; }
        .pdf-wrapper .header .col-4 {
            float: left;
            width: 90px;
            height: 40px; }
        .pdf-wrapper .header .col-4 h2 {
            display: block;
            color: #000000;
            padding: 5px 10px;
            text-align: right; }
        .pdf-wrapper .header .col-4 h2 strong {
            display: inline-block;
            font-size: 18px;
            font-weight: bold;
            line-height: 30px;
            vertical-align: top; }
        .pdf-wrapper .header .col-4 h2 span {
            display: inline-block;
            float: right;
            color: #7f7f7f;
            font-size: 10px;
            text-align: left;
            line-height: 15px;
            padding-left: 4px;
            vertical-align: top; }
        .pdf-wrapper .header .col-5 {
            float: right;
            width: 160px;
            height: 40px; }
        .pdf-wrapper .header .col-5 h2 {
            display: block;
            color: #000000;
            padding: 5px 10px;
            text-align: right; }
        .pdf-wrapper .header .col-5 h2 strong {
            display: inline-block;
            font-size: 14px;
            font-weight: bold;
            line-height: 30px;
            vertical-align: top; }
        .pdf-wrapper .header .col-5 h2 span {
            display: inline-block;
            color: #7f7f7f;
            font-size: 10px;
            text-align: right;
            line-height: 15px;
            padding-right: 10px;
            vertical-align: top; }
        .pdf-wrapper .content {
            width: 100%;
            height: 290px;
            border-left: 2px solid #f2f2f2;
            border-right: 2px solid #f2f2f2;
            background-color: #ffffff; }
        .pdf-wrapper .content .col-1 {
            float: left;
            width: 100px;
            height: 290px;
            text-align: center; }
        .pdf-wrapper .content .col-1 img {
            display: inline-block;
            width: 34px;
            height: 205px;
            margin: 40px auto 0; }
        .pdf-wrapper .content .col-2 {
            float: left;
            width: 756px;
            height: 290px;
            padding: 30px 0 20px 20px; }
        .pdf-wrapper .content .col-2 .row-1 {
            width: 736px;
            height: 90px;
            padding: 20px 0 0;
            border-bottom: 1px solid #f2f2f2; }
        .pdf-wrapper .content .col-2 .row-1 h3 {
            display: block;
            float: left;
            font-size: 2.4em;
            line-height: 1em; }
        .pdf-wrapper .content .col-2 .row-1 h3 strong {
            display: block;
            color: #000000;
            font-weight: bold; }
        .pdf-wrapper .content .col-2 .row-1 h3 span {
            display: block;
            color: #7f7f7f; }
        .pdf-wrapper .content .col-2 .row-1 .arrow-wrapper {
            display: block;
            float: left;
            padding: 10px 15px; }
        .pdf-wrapper .content .col-2 .row-2 {
            width: 736px;
            height: 80px;
            padding: 20px 0 0;
            border-bottom: 1px solid #f2f2f2; }
        .pdf-wrapper .content .col-2 .row-2 .row2-col-1 {
            float: left;
            width: 48%; }
        .pdf-wrapper .content .col-2 .row-2 .row2-col-1 h4 {
            display: block;
            float: left;
            width: 110px;
            height: 40px;
            color: #7f7f7f;
            font-size: 1.4em;
            text-align: right;
            border-right: 1px solid #f2f2f2;
            padding-right: 20px; }
        .pdf-wrapper .content .col-2 .row-2 .row2-col-1 h4 span {
            display: block;
            line-height: 20px; }
        .pdf-wrapper .content .col-2 .row-2 .row2-col-1 p {
            display: block;
            float: left;
            color: #000000;
            font-size: 1.8em;
            line-height: 40px;
            font-weight: bold;
            padding-left: 20px; }
        .pdf-wrapper .content .col-2 .row-2 .row2-col-2 {
            float: left;
            width: 48%; }
        .pdf-wrapper .content .col-2 .row-2 .row2-col-2 h4 {
            display: block;
            float: right;
            width: 150px;
            height: 40px;
            color: #7f7f7f;
            font-size: 1.4em;
            text-align: right;
            border-right: 1px solid #f2f2f2;
            padding-right: 20px; }
        .pdf-wrapper .content .col-2 .row-2 .row2-col-2 h4 span {
            display: block;
            line-height: 20px; }
        .pdf-wrapper .content .col-2 .row-2 .row2-col-2 p {
            display: block;
            float: right;
            color: #000000;
            font-size: 1.8em;
            line-height: 40px;
            font-weight: bold;
            padding-left: 20px; }
        .pdf-wrapper .content .col-2 .row-3 {
            width: 736px;
            height: 80px;
            padding: 20px 0 0; }
        .pdf-wrapper .content .col-2 .row-3 .row3-col {
            float: left; }
        .pdf-wrapper .content .col-2 .row-3 .row3-col.small {
            width: 22%; }
        .pdf-wrapper .content .col-2 .row-3 .row3-col.big {
            width: 28%; }
        .pdf-wrapper .content .col-2 .row-3 .row3-col h4 {
            display: block;
            float: left;
            width: 110px;
            height: 40px;
            color: #7f7f7f;
            font-size: 1.4em;
            text-align: right;
            border-right: 1px solid #f2f2f2;
            padding-right: 20px; }
        .pdf-wrapper .content .col-2 .row-3 .row3-col h4 span {
            display: block;
            line-height: 20px; }
        .pdf-wrapper .content .col-2 .row-3 .row3-col p {
            display: block;
            float: left;
            color: #000000;
            font-size: 1.8em;
            line-height: 40px;
            font-weight: bold;
            padding-left: 20px; }
        .pdf-wrapper .footer {
            width: 100%;
            height: 28px;
            background-color: #f2f2f2; }
        .pdf-wrapper .footer .col-1 {
            float: left;
            display: block;
            width: 60%;
            text-align: center;
            padding: 5px 20px; }
        .pdf-wrapper .footer .col-2 {
            float: left;
            display: block;
            width: 40%;
            padding: 5px 20px;
            text-align: right; }
        .pdf-wrapper .footer p {
            color: #7f7f7f;
            font-size: 1em;
            line-height: 20px; }

        /*# sourceMappingURL=style.css.map */

    </style>
</head>
<body>

@foreach( $ticket->persons as $key => $person )
<div class="pdf-wrapper">
    <div class="header">
        <div class="logo-wrapper"><img src="http://new.matarebeli.ge/assets/images/pdf-logo.png" alt=""></div>
        <div class="col-1">
            <p>გამგზავრება</p>
            <p>Departure</p>
        </div>
        <div class="col-2">
            <h2>
                <strong>
                    {{ \date('H:i', strtotime( $ticket->leave_datetime )) }}
                </strong>
                <span>
                    სთ
                    <br>
                    Tm
                </span>
            </h2>
        </div>
        <div class="col-3">
            <h2>
                <strong>
                    {{ \date('m', strtotime( $ticket->leave_datetime )) }}
                </strong>
                <span>
                    {{\App\helpers\Railway::translateDate($ticket->leave_datetime, "", 'en',  true)}}
                    <br>
                    {{\App\helpers\Railway::translateDate($ticket->leave_datetime, "", 'ka',  true)}}
                </span>
            </h2>
        </div>
        <div class="col-4">
            <h2>
                <strong>
                    {{ \date('Y', strtotime( $ticket->leave_datetime )) }}
                </strong>
                <span>
                    წელი
                    <br>
                    Year
                </span>
            </h2>
        </div>
        <div class="col-5">
            <h2>
                <span>
                    შესყიდვის კოდი
                    <br>
                    Purchase code
                </span>
                <strong>
                    {{$ticket->request_id}}
                </strong>
            </h2>
        </div>
    </div>
    <div class="content">
        <div class="col-1"><img src="http://new.matarebeli.ge/assets/images/pdf-logo-2.png" alt=""></div>
        <div class="col-2">
            <div class="row-1">
                <h3>
                    <strong>{{ \App\helpers\Railway::translateStation($ticket->source_station, 'ka') }}</strong>
                    <span>{{ \App\helpers\Railway::translateStation($ticket->source_station, 'en') }}</span>
                </h3>
                <div class="arrow-wrapper">
                    <img src="http://new.matarebeli.ge/assets/images/pdf-arrow.png" alt="">
                </div>
                <h3>
                    <strong>{{ \App\helpers\Railway::translateStation($ticket->destination_station, 'ka') }}</strong>
                    <span>{{ \App\helpers\Railway::translateStation($ticket->destination_station, 'en') }}</span>
                </h3>
            </div>
            <div class="row-2">
                <div class="row2-col-1">
                    <h4>
                        <span>სახელი</span>
                        <span>Name</span>
                    </h4>
                    <p>{{$person->name}}  {{$person->surname}}</p>
                </div>
                <div class="row2-col-2">
                    <p>{{$person->idnumber}}</p>
                    <h4>
                        <span>პირადი ნომერი</span>
                        <span>ID Number</span>
                    </h4>
                </div>
            </div>
            <div class="row-3">
                <div class="row3-col big">
                    <h4>
                        <span>მატარებელი</span>
                        <span>Train</span>
                    </h4>
                    <p>{{ $ticket->train }}</p>
                </div>
                <div class="row3-col small">
                    <h4>
                        <span>ვაგონი</span>
                        <span>Carriage</span>
                    </h4>
                    <p>{{ $ticket->vagon }}</p>
                </div>
                <div class="row3-col small">
                    <h4>
                        <span>ადგილი</span>
                        <span>Seat</span>
                    </h4>
                    <p>{{$person->place_number}}</p>
                </div>
                <div class="row3-col big">
                    <h4>
                        <span>კლასი</span>
                        <span>Class</span>
                    </h4>
                    <p>
                        {{ \App\helpers\Railway::translate($ticket->vagon_class, 'ka') }} /
                        {{ \App\helpers\Railway::translate($ticket->vagon_class, 'en') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="footer">
        <div class="col-1"><p>ქოლცენტრი / Call center: (995 32) 2 193 195</p></div>
        <div class="col-2"><p>ფასი / Price: {{number_format($ticket->amount_from_api/100,2)}} ლარი / GEL</p></div>
    </div>
</div>
@endforeach


</body>
</html>