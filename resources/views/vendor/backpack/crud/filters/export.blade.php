<li
    class="datepicker {{ $filter->name }}-class btn btn-default"
    style="background-color: seagreen; color: white;  margin-top: 3px; margin-left: 7px; height: 28px; line-height: 15px;"
    name="{{ $filter->name }}"
>Exel Export</li>

@push('crud_list_scripts')
<script>
    jQuery(document).ready(function($) {

        $(".{{ $filter->name }}-class").click(function(e) {
            e.preventDefault();
            makeOP($(this));
        });

        function makeOP(elem){
            var parameter = elem.attr('name');
            var value = elem.val();

            @if (!$crud->ajaxTable())
                var current_url = normalizeAmpersand("{{ Request::fullUrl() }}");
                var new_url = addOrUpdateUriParameter(current_url, parameter, value);

                new_url = normalizeAmpersand(new_url.toString());
                window.location.href = new_url.toString();
            @else

                var ajax_table = $("#crudTable").DataTable();
                var current_url = ajax_table.ajax.url();
                var new_url = addOrUpdateUriParameter(current_url, parameter, value);

                new_url = normalizeAmpersand(new_url.toString());
                ajax_table.ajax.url(new_url).load();

                if (new_url.indexOf("?") >= 0){
                    new_url = new_url + "&request_type=excel";
                }else{
                    new_url = new_url + "?request_type=excel";
                }

                make_server_side_ajax_export(
                    new_url
                );


            @endif
        }

        $("li[filter-name={{ $filter->name }}]").on('filter:clear', function(e) {
            $(".{{ $filter->name }}-class").val("");
        });
    });
</script>
@endpush
