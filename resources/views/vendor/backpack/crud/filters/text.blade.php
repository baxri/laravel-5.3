
<li filter-name="{{ $filter->name }}"
    filter-type="{{ $filter->type }}"   style="margin-left: 5px;">

    <input type="text" name="{{ $filter->name }}"
           class="datepicker {{ $filter->name }}-class form-control input-sm"
           placeholder="{{ $filter->label }}"
           value="{{Request::get($filter->name)}}"
           data-provide="datepicker"
           style="margin: 2px;">
</li>

@push('crud_list_scripts')
<script>
    jQuery(document).ready(function($) {

       /* $('.datepicker').datepicker({
            'format': 'y-m-d'
        });*/

        $(".{{ $filter->name }}-class").on( 'blur keyup', function(e) {
            e.preventDefault();

            var code = e.which;

            console.log(code);

            if(code==13){

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


                @endif

            }


        });

        $("li[filter-name={{ $filter->name }}]").on('filter:clear', function(e) {
            $(".{{ $filter->name }}-class").val("");
        });
    });
</script>
@endpush
