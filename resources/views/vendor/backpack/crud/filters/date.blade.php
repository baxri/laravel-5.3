
<li filter-name="{{ $filter->name }}"
    filter-type="{{ $filter->type }}"   style="margin-left: 5px;">

    <input type="text" name="{{ $filter->name }}"
           class="datepicker {{ $filter->name }}-class form-control input-sm"
           placeholder="YYYY-MM-DD"
           value="{{Request::get($filter->name) ? Request::get($filter->name) : $filter->default_value}}"
           data-provide="datepicker"
           style="margin: 2px;">
</li>

@push('crud_list_scripts')
<script>
    jQuery(document).ready(function($) {

        $(".{{ $filter->name }}-class").change(function(e) {
            e.preventDefault();
            var parameter = $(this).attr('name');
            var value = $(this).val();

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

                @if ( !empty( $crud->getTotals() ) )
                    if (new_url.indexOf("?") >= 0){
                        new_url = new_url + "&request_type=total";
                    }else{
                        new_url = new_url + "?request_type=total";
                    }

                    make_request_to_get_total_info(
                        new_url
                    );
                @endif
            @endif
        });

        $("li[filter-name={{ $filter->name }}]").on('filter:clear', function(e) {
            $(".{{ $filter->name }}-class").val("{{$filter->default_value}}");
            $(".{{ $filter->name }}-class").trigger("change");
        });
    });
</script>
@endpush
