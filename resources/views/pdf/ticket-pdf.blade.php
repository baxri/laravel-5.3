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
                            <td colspan="2" class="left-side">{{ \date('l d F', \strtotime( $ticket->leave_datetime )) }}</td>
                            <td colspan="2" class="right-side">{{$ticket->request_id}}</td>
                        </tr>
                        <tr class="row-1">
                            <td colspan="4">{{ $ticket->train_name }}</td>
                        </tr>
                        <tr class="row-3">
                            <td colspan="4">{{ \date('H:i', \strtotime( $ticket->leave_datetime )) }}</td>
                        </tr>
                        <tr class="row-6">
                            <td colspan="3" class="left-side">Help: (995 32) 2 193 195</td>
                            <td class="right-side">Price: {{number_format($ticket->amount_from_api/100,2)}} GEL</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>