<tr style="color: gray;">
    <td>&nbsp;&nbsp;&nbsp;&nbsp;<span class="fa fa-angle-double-right" aria-hidden="true"></span> &nbsp;&nbsp {{ $person->name }}</td>
    <td>{{ $person->surname }}</td>
    <td><b>Purchase ID: </b>{{ $person->purchase }}</td>
    <td>{{ $person->idnumber }}</td>
    <td align="right">
        <span id="status-{{$person->id}}"
              data-class="label-{{  $person->getStatusClass() }}"
              class="label label-{{  $person->getStatusClass() }}">{{$person->getStatusName()}}</span>
    </td>
    <td><span class="fa fa-ticket" aria-hidden="true"></span> Place - {{ $person->place_number }}</td>
    <td>
        <span class="fa fa-money" aria-hidden="true"></span> &nbsp;&nbsp
        Discount - {{ number_format( $person->discount_amount/100, 2 ) }}</td>

    <td colspan="14">
        @if( $person->ischild )
            Child
        @else
            Adult
        @endif
    </td>
    <td>
        <span class="fa fa-money" aria-hidden="true"></span> &nbsp;
        &nbsp Return - {{ number_format( $person->returned_amount/100, 2 ) }}</td>
    <td>
        <span class="fa fa-money" aria-hidden="true"></span>
        {{ !empty($person->payout->payout_hash_id) ? $person->payout->payout_hash_id : '' }}
    </td>
    <td >
        @if( $person->status == \App\Person::$success )
            <a data-value="{{$person->id}}" class="return-ticket-{{$transaction->id}}"
               onclick="returnTicket($(this))"
               style="cursor: pointer;" data-toggle="tooltip">
                <span class="fa fa-reply" aria-hidden="true"></span>
            </a>
        @endif
    </td>
</tr>

<script>
   // jQuery(document).ready(function($) {


        function returnTicket( button ){

            var comment = prompt("შეიყვანეთ კომენტარი", "ბილეთის დაბრუნება");

            if( comment.length == 0 ){

                button.button('reset');
                new PNotify({
                    title: "ბილეთის დაბრუნება",
                    text: "კომენტარი არ არის შეყვანილი",
                    type: "warning"
                });

                button.button('reset');
                return false;
            }

            button.button('loading');

            $.ajax({
                url: 'person/return/' + button.data('value'),
                type: 'POST',
                data: {
                    comment : comment
                },
                success: function(result) {
                    button.button('reset');
                    new PNotify({
                        title: "ბილეთის დაბრუნება",
                        text: "ბილეთი დაბრუნება წამატებით განხორციელდა",
                        type: "success"
                    });
                    console.log(result);
                },
                error: function(result) {
                    button.button('reset');
                    new PNotify({
                        title: "ბილეთის დაბრუნება",
                        text: "ბილეთის დაბრუნება ვერ განხორიელდა",
                        type: "warning"
                    });
                    console.log(result);
                }
            });
            return false;

        }


   // });
</script>
