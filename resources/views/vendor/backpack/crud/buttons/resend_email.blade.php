<a
        href="{{ url($crud->route.'/resend/'.$entry->getKey()) }}-email"
        data-value="{{$entry->getKey()}}"
        class="resend-button-{{$entry->getKey()}} btn btn-xs btn-default" data-button-type="{{$entry->getKey()}}_return_ticket">
    Email
</a>


<script>
    jQuery(document).ready(function($) {

        $(".resend-button-{{$entry->getKey()}}-email").click(function(){

            var button =  $(this);
            button.button('loading');

            $.ajax({
                url: 'transaction/resend-email/' + $(this).data('value'),
                type: 'POST',
                success: function(result) {

                    button.button('reset');

                    new PNotify({
                        title: "ინფორმაციის თავიდან გაგზავნა",
                        text: "ინფორმაციის გაგზავნა წამატებით განხორციელდა",
                        type: "success"
                    });

                    console.log(result);
                },
                error: function(result) {

                    button.button('reset');

                    new PNotify({
                        title: "ინფორმაციის თავიდან გაგზავნა",
                        text: "ინფორმაციის გაგზავნა ვერ განხორიელდა",
                        type: "warning"
                    });

                    console.log(result);
                }
            });

            return false;
        });

    });
</script>

