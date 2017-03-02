<li
    class="datepicker {{ $filter->name }}-class fa fa-refresh btn btn-default"
    style="margin-top: 3px;"
    name="{{ $filter->name }}"
></li>

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


            @endif
        }

        $("li[filter-name={{ $filter->name }}]").on('filter:clear', function(e) {
            $(".{{ $filter->name }}-class").val("");
        });
    });
</script>
@endpush
