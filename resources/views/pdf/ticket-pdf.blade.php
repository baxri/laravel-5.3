<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title></title>
<style>
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
figure, figcaption, footer, header,
menu, nav, output, ruby, section, summary,
time, mark, audio, video {
    margin: 0;
    padding: 0; }
table {
    border-collapse: separate;
    border-spacing: 0;
}
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box; }
body {
    font-size: 62.5%;
    line-height: 1;
}
.pdf-wrapper {
    width: 860px;
    margin: 20px auto;
}
table.main-table {
    width: 100%;
}
table.header-table {
    width: 100%;
}
table.header-table tbody {
    height: 88px;
}
table.header-table tbody tr .main-td {
    text-align: center;
    padding: 14px;
    vertical-align: middle;
}
table.header-table tbody tr .main-td table {
    width: 100%;
}
table.inner-table {
    height: 40px;
    margin: 10px 0;
    padding-right: 15px;
    border-right: 1px solid #b2b2b2;
}
table.content-table {
    width: 100%;
    border-left: 2px solid #f2f2f2;
    border-right: 2px solid #f2f2f2;
}
table.content-table .content-logo {
    width: 100px;
    height: 222px;
    text-align: center;
}
table.content-table .content-logo img {
    display: inline-block;
    width: 28px;
}
table.content-table .content-details {
    width: auto;
    height: 222px;
    text-align: center;
}
table.content-table .content-details table {
    width: 100%;
}
table.content-table .content-details .bottom-border {
    border-bottom: 1px solid #f2f2f2;
}
table.footer-table {
    width: 100%;
}
table.footer-table td {
    color: #7f7f7f;
    font-size: 12px;
    line-height: 20px;
    padding: 10px 20px;
}
table.footer-table td p {
    color: #7f7f7f;
    font-size: 10px;
    line-height: 20px;
}
.aline-right {
    text-align: right;
}
.aline-center {
    text-align: center;
}
.aline-left {
    text-align: left;
}
.cls1 {
    color: #7f7f7f;
    font-size: 14px;
    line-height: 20px;
}
.cls3 {
    width: 200px;
}
.span1 {
    color: #000;
    font-size: 22px;
    font-weight: bold;
    line-height: 30px;
    padding-right: 6px;
}
.span2 {
    color: #7f7f7f;
    font-size: 10px;
    line-height: 15px;
}
.span3 {
    color: #000;
    font-size: 16px;
    font-weight: bold;
    line-height: 30px;
    padding-left: 6px;
}
.span4 {
    color: #7f7f7f;
    font-size: 14px;
    line-height: 20px;
    padding-right: 12px;
}
.span5 {
    color: #000;
    font-size: 18px;
    font-weight: bold;
    line-height: 40px;
}
.padding20 {
    padding-top: 10px;
    padding-bottom: 10px;
}
.station {
    width: 30%;
    font-size: 20px;
    line-height: 40px;
}
.arrow {
    width: 100px;
}
.station p.black {
    color: #000000;
    font-weight: bold;
}
.station p.gray {
    color: #7f7f7f;
}
.gray {
    color: #7f7f7f !important;
}
.middle-details td,
.third-details td {
    padding-left: 10px;
    padding-right: 10px;
}
.third-details td.aline-right {
    color: #7f7f7f;
    font-size: 12px;
    line-height: 20px;
    border-right: 1px solid #f2f2f2;
}
.third-details td.aline-left {
    font-size: 20px;
    line-height: 40px;
    font-weight: bold;
}
.third-details td.last-td {
    text-align: left;
    font-size: 16px;
    line-height: 20px;
}
.middle-details td.aline-right {
    border-right: 1px solid #f2f2f2;
}
</style>
</head>
<body>
@foreach( $ticket->persons as $key => $person )
<div class="pdf-wrapper">
    <table class="main-table">
        <tbody>
            <tr>
                <td>
                    <table bgcolor="#f2f2f2" class="header-table">
                        <tbody>
                            <tr>
                                <td class="main-td"><img src="http://www.matarebeli.ge/assets/images/pdf-logo.png" alt=""></td>
                                <td class="main-td">
                                    <table class="inner-table cls1">
                                        <tbody>
                                        <tr>
                                            <td class="aline-right">
                                                <p>გამგზავრება</p>
                                                <p>Departure</p>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td class="main-td">
                                    <table class="inner-table cls2">
                                        <tbody>
                                            <tr>
                                                <td class="span1 aline-right">
                                                    {{ \date('H:i', strtotime( $ticket->leave_datetime )) }}
                                                </td>
                                                <td class="span2 aline-left">
                                                    სთ<br>Tm
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td class="main-td">
                                    <table class="inner-table cls2">
                                        <tbody>
                                        <tr>
                                            <td class="span1 aline-right">
                                                {{ \date('d', strtotime( $ticket->leave_datetime )) }}
                                            </td>
                                            <td class="span2 aline-left">
                                                {{\App\helpers\Railway::translateDate($ticket->leave_datetime, "", 'en',  true)}}<br>{{\App\helpers\Railway::translateDate($ticket->leave_datetime, "", 'ka',  true)}}
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td class="main-td">
                                    <table class="inner-table cls2">
                                        <tbody>
                                        <tr>
                                            <td class="span1 aline-right">
                                                {{ \date('Y', strtotime( $ticket->leave_datetime )) }}
                                            </td>
                                            <td class="span2 aline-left">
                                                წელი<br>Year
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td class="main-td cls3">
                                    <table>
                                        <tbody>
                                        <tr>
                                            <td class="span2 aline-right">
                                                შესყიდვის კოდი<br>Purchase code
                                            </td>
                                            <td class="span3 aline-right">
                                                {{$ticket->request_id}}
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="content-table">
                        <tbody>
                        <tr>
                            <td class="content-logo"><img src="http://www.matarebeli.ge/assets/images/pdf-logo-2.png" alt=""></td>
                            <td class="content-details">
                                <table>
                                    <tbody>
                                    <tr>
                                        <td class="padding20 bottom-border">
                                            <table>
                                                <tbody>
                                                    <tr>
                                                        <td class="station aline-center">
                                                            <p class="black">{{ \App\helpers\Railway::translateStation($ticket->source_station, 'ka') }}</p>
                                                            <p class="gray">{{ \App\helpers\Railway::translateStation($ticket->source_station, 'en') }}</p>
                                                        </td>
                                                        <td class="arrow aline-center">
                                                            <img src="http://www.matarebeli.ge/assets/images/pdf-arrow.png" alt="">
                                                        </td>
                                                        <td class="station aline-center">
                                                            <p class="black">{{ \App\helpers\Railway::translateStation($ticket->destination_station, 'ka') }}</p>
                                                            <p class="gray">{{ \App\helpers\Railway::translateStation($ticket->destination_station, 'en') }}</p>
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="padding20 bottom-border">
                                            <table>
                                                <tbody>
                                                    <tr class="middle-details">
                                                        <td class="span4 aline-right" colspan="2">
                                                            სახელი<br>Name
                                                        </td>
                                                        <td class="span5 aline-left" colspan="2">
                                                            <p>{{$person->name}} {{$person->surname}}</p>
                                                        </td>
                                                        <td class="span4 aline-right" colspan="2">
                                                            პირადი ნომერი<br>ID Number
                                                        </td>
                                                        <td class="span5 aline-left" colspan="2">
                                                            <p>{{$person->idnumber}}</p>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="padding20">
                                            <table>
                                                <tbody>
                                                <tr class="third-details">
                                                    <td class="aline-right">
                                                        <p>მატარებელი</p>
                                                        <p>Train</p>
                                                    </td>
                                                    <td class="aline-left">
                                                        {{ $ticket->train }}
                                                    </td>
                                                    <td class="aline-right">
                                                        ვაგონი<br>Carriage
                                                    </td>
                                                    <td class="aline-left">
                                                        {{ $ticket->vagon }}
                                                    </td>
                                                    <td class="aline-right">
                                                        ადგილი<br>Seat
                                                    </td>
                                                    <td class="aline-left">
                                                        {{$person->place_number}}
                                                    </td>
                                                    <td class="aline-right">
                                                        კლასი<br>Class
                                                    </td>
                                                    <td class="last-td">
                                                        {{ \App\helpers\Railway::translate($ticket->vagon_class, 'ka') }}<br>
                                                        <span class="gray">{{ \App\helpers\Railway::translate($ticket->vagon_class, 'en') }}</span>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table bgcolor="#f2f2f2" class="footer-table">
                        <tbody>
                        <tr>
                            <td class="aline-center">ქოლცენტრი / Call center: (995 32) 2 193 195</td>
                            <td class="aline-right">ფასი / Price: {{number_format($ticket->amount_from_api/100,2)}} ლარი / GEL</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endforeach
</body>
</html>