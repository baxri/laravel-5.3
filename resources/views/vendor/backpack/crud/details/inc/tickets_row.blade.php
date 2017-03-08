<tr>
    <td>
                <span class="fa fa fa-subway" aria-hidden="true">
                    @if( empty( $ticket->parent_id ) )
                        <span class="fa fa-long-arrow-right" aria-hidden="true"></span>
                    @else
                        <span class="fa fa-long-arrow-left" aria-hidden="true"></span>
    @endif
    <td width="20%">
        <p><h2>Ticket Request ID:</h2> {{ $ticket->request_id }}</p>
    </td>
    <td width="20%">
        <p><b>Train Name:</b> {{ $ticket->train_name }}</p>

        <p><b>Source Station:</b> {{ $ticket->source_station }}</p>
        <p><b>Dest Station:</b> {{ $ticket->destination_station }}</p>

        <p><b>Vagon Class:</b> {{ $ticket->vagon_class }}</p>
        <p><b>Vagon Type:</b> {{ $ticket->vagon_type }} ({{ $ticket->vagon_rank }})</p>

        <p>
            <b>Train:</b> {{ $ticket->train }} <b>Vagon:</b> {{ $ticket->vagon }}
        </p>

    </td>
    <td>
        <p><b>Leave: </b>{{ \date('l d F', \strtotime( $ticket->leave_datetime )) }}</p>
        <p><b>Live Time: </b>{{ \date('H:i', \strtotime( $ticket->leave_datetime )) }}</p>

        <p><b>Enter:</b>{{ \date('l d F', \strtotime( $ticket->enter_datetime )) }}</p>
        <p><b>Enter Time: </b>{{ \date('H:i', \strtotime( $ticket->enter_datetime )) }}</p>
        <p><b>Status: </b>
            <a
                    class="sync-info-{{$ticket->id}} btn"
                    data-value="{{$ticket->id}}"
                    target="_blank"
                    href="{{ url(config('backpack.base.route_prefix', 'admin').'/transaction/ticket/'
                            .$ticket->id) }}/sync">
                {{ $ticket->get_transaction_status ? 'Check Status' : 'Check Status' }}
            </a>
        </p>

    </td>
    <td style="font-size: 13pt; color: green;">{{ number_format( $ticket->amount_from_api/100, 2 ) }}</td>
    <td>{{ $ticket->reason }}</td>
    <td>
        <a
                target="_blank"
                href="{{ url(config('backpack.base.route_prefix', 'admin').'/log?ticket_id='.$ticket->id) }}">
            <span class="fa fa-files-o" aria-hidden="true"></span>
        </a>
        &nbsp;&nbsp;
        <a
                target="_blank"
                href="{{ url(config('backpack.base.route_prefix', 'admin').'/transaction/ticket/'
                            .$ticket->id) }}/pdf">
            <span class="fa fa-file-pdf-o" aria-hidden="true"></span>
        </a>
    </td>

    <td>
        <p><b>IP: </b>{{$ticket->transaction->ipinfo->ip_key}}</p>
        <p><b>AS: </b>{{$ticket->transaction->ipinfo->as}}</p>
        <p><b>City: </b>{{$ticket->transaction->ipinfo->city}}</p>
        <p><b>Country: </b>{{$ticket->transaction->ipinfo->country}}</p>
        <p><b>Code: </b>{{$ticket->transaction->ipinfo->countryCode}}</p>
        <p><b>Ord: </b>{{$ticket->transaction->ipinfo->org}}</p>
    </td>

</tr>
@foreach ( $ticket->persons as $person )
    @include('vendor.backpack.crud.details.inc.persons_row')
@endforeach


<script>

    $(document).ready(function () {
        $('.sync-info-{{$ticket->id}}').click(function(){

            var button =  $(this);
            button.button('loading');

            $.ajax({
                url: 'transaction/ticket/' + button.data('value') + "/sync",
                type: 'POST',
                success: function(result) {

                    button.button('reset');

                    if( result.data.railway_status == {{\App\Ticket::$railway_active_status}} ){
                        new PNotify({
                            title: "მიმდინარე სტატუსი",
                            text: "ბილეთის სტატუსი აქტიურია",
                            type: "success"
                        });
                    }else{
                        new PNotify({
                            title: "მიმდინარე სტატუსი",
                            text: "ბილეთის სტატუსი არ არის აქტიური",
                            type: "error"
                        });
                    }

                    console.log(result);
                },
                error: function(result) {

                    console.log(result);

                    button.button('reset');

                    new PNotify({
                        title: "მიმდინარე სტატუსი",
                        text: "სინქრონიზაცია ვერ განხორიელდა",
                        type: "warning"
                    });

                    console.log(result);
                }
            });

            return false;
        });
    });

</script>
