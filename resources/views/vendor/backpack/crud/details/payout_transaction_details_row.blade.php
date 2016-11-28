<div class="panel panel-default" style="margin: 5px;">
    <table class="table">
        @foreach ( $payout->persons as $person )
            @include('vendor.backpack.crud.details.inc.persons_row')
        @endforeach
    </table>
</div>