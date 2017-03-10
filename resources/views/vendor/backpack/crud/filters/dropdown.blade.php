{{-- Dropdown Backpack CRUD filter --}}

<li filter-name="{{ $filter->name }}"
	filter-type="{{ $filter->type }}"
	class="dropdown {{ Request::get($filter->name)?'active':'' }}">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ $filter->label }} <span class="caret"></span></a>
    <ul class="dropdown-menu">
		<li><a parameter="{{ $filter->name }}" key="" href="">-</a></li>
		<li role="separator" class="divider"></li>
		@if (is_array($filter->values) && count($filter->values))
			@foreach($filter->values as $key => $value)
				@if ($key == 'dropdown-separator')
					<li role="separator" class="divider"></li>
				@else
					<li class="{{ ($filter->isActive() && $filter->currentValue == $key)?'active':'' }}">
						<a  parameter="{{ $filter->name }}"
							href=""
							key="{{ $key }}"
							>{{ $value }}</a>
					</li>
				@endif
			@endforeach
		@endif
    </ul>
  </li>


{{-- ########################################### --}}
{{-- Extra CSS and JS for this particular filter --}}

{{-- FILTERS EXTRA CSS  --}}
{{-- push things in the after_styles section --}}

    {{-- @push('crud_list_styles')
        <!-- no css -->
    @endpush --}}


{{-- FILTERS EXTRA JS --}}
{{-- push things in the after_scripts section --}}

@push('crud_list_scripts')
    <script>
		jQuery(document).ready(function($) {
			$("li.dropdown[filter-name={{ $filter->name }}] .dropdown-menu li a").click(function(e) {
				e.preventDefault();

				var value = $(this).attr('key');
				var parameter = $(this).attr('parameter');

				@if (!$crud->ajaxTable())
					// behaviour for normal table
					var current_url = normalizeAmpersand('{{ Request::fullUrl() }}');
					var new_url = addOrUpdateUriParameter(current_url, parameter, value);

					// refresh the page to the new_url
					new_url = normalizeAmpersand(new_url.toString());
			    	window.location.href = new_url;
			    @else
			    	// behaviour for ajax table
					var ajax_table = $("#crudTable").DataTable();
					var current_url = ajax_table.ajax.url();
					var new_url = addOrUpdateUriParameter(current_url, parameter, value);

					// replace the datatables ajax url with new_url and reload it
					new_url = normalizeAmpersand(new_url.toString());
					ajax_table.ajax.url(new_url).load();

					@if ( method_exists($crud, "getTotals") && !empty( $crud->getTotals() ) )
						if (new_url.indexOf("?") >= 0){
							new_url = new_url + "&request_type=total";
						}else{
							new_url = new_url + "?request_type=total";
						}

						make_request_to_get_total_info(
							new_url
						);
					@endif

					if ( URI(new_url).hasQuery('{{ $filter->name }}', true )) {
						$("li[filter-name={{ $filter->name }}]").removeClass('active').addClass('active');
						$("li[filter-name={{ $filter->name }}] .dropdown-menu li").removeClass('active');
						$(this).parent().addClass('active');
					}
					else
					{
						$("li[filter-name={{ $filter->name }}]").trigger("filter:clear");
					}
			    @endif
			});

			$("li[filter-name={{ $filter->name }}]").on('filter:clear', function(e) {
				$("li[filter-name={{ $filter->name }}]").removeClass('active');
				$("li[filter-name={{ $filter->name }}] .dropdown-menu li").removeClass('active');
			});
		});
	</script>
@endpush
