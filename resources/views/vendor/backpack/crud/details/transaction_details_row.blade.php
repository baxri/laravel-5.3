<div class="panel panel-default" style="margin: 5px;">
    <table class="table">
        @foreach ( $transaction->tickets as $ticket )
            @include('vendor.backpack.crud.details.inc.tickets_row')
        @endforeach
    </table>
</div>