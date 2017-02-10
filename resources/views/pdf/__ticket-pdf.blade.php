<div class="container">
    <div class="ticket_cnt">
        <table class="main-table">
            <tbody>
            <tr>
                <td class="logo-area">
                    <img width="30" src="http://www.matarebeli.ge/mails_pdf/styles/image/ticket-logo.png" alt="">
                </td>
                <td class="info-area">
                    <table>
                        <tbody>
                        <tr class="row-2">
                            <td colspan="2" class="left-side">

                                {{ \App\helpers\Railway::trans(strtoupper(\date('l', \strtotime( $ticket->leave_datetime ))), $ticket->lang ) }}
                                {{ \date('d', \strtotime( $ticket->leave_datetime )) }}
                                {{ \App\helpers\Railway::trans(strtoupper(\date('F', \strtotime( $ticket->leave_datetime ))), $ticket->lang )}}
                            </td>
                        </tr>
                        <tr class="row-2">
                            <td colspan="2" class="right-side">Reuqest ID: {{$ticket->request_id}}</td>
                        </tr>
                        <tr class="row-1">
                            <td colspan="4">{{\App\helpers\Railway::trans('SOURCE_STATION', $ticket->lang)}}: {{ \App\helpers\Railway::translateStation($ticket->source_station, $ticket->lang) }} </td>
                        </tr>
                        <tr class="row-1">
                            <td colspan="4">{{\App\helpers\Railway::trans('DESTINATION_STATION', $ticket->lang)}}: {{ \App\helpers\Railway::translateStation($ticket->destination_station, $ticket->lang) }} </td>
                        </tr>
                        <tr class="row-1">
                            <td colspan="4">{{\App\helpers\Railway::trans('LEAVE_TIME', $ticket->lang)}}: {{ \date('H:i', \strtotime( $ticket->leave_datetime )) }}</td>
                        </tr>
                        <tr class="row-1">
                            <td>
                                <br/> <br/>
                                @foreach( $ticket->persons as $key => $person )
                                    <div>
                                        {{++$key}}) {{$person->name}} {{$person->surname}} -  Personal number: {{$person->idnumber}} Place: #{{$person->place_number}}
                                    </div>
                                @endforeach
                                <br/> <br/>
                            </td>
                        </tr>
                        <tr class="row-1">
                            <td colspan="4">
                                <p>
                                    {{\App\helpers\Railway::trans('TRAIN', $ticket->lang)}}: #{{ $ticket->train }}
                                    {{\App\helpers\Railway::trans('VAGON', $ticket->lang)}}: #{{ $ticket->vagon }}
                                    {{ \App\helpers\Railway::translate($ticket->vagon_class, $ticket->lang) }}
                                    ( {{ \App\helpers\Railway::translate($ticket->vagon_type, $ticket->lang) }} ) </p>
                            </td>
                        </tr>

                        <tr class="row-6">
                            <br/> <br/>
                            <td colspan="3" class="left-side">{{\App\helpers\Railway::trans('HELP', $ticket->lang)}}: (995 32) 2 193 195</td>
                            <td class="right-side">{{\App\helpers\Railway::trans('PRICE', $ticket->lang)}}: {{number_format($ticket->amount_from_api/100,2)}} GEL</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>