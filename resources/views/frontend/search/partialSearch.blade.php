<link rel="stylesheet" href="{{ asset('css/search.css') }}">

<div class="container" id="container">
    <div class="row">
        <div class="col-7">
            <div class="restaurants">
                @if (isset($restaurants))
                    @foreach ($restaurants as $res)
                        <div class="row mb-4 restaurant" id="{{ $res['id'] }}">
                            <div class="col-4">
                                <img src="{{ $res['logo_name'] ? asset('img/logos/' . $res['logo_name']) : $res['logo_url'] }}"
                                    class="restaurant-img">
                            </div>
                            <div class="col-7">
                                <h3 class="restaurant-title">{{ $res['name'] }}</h3>
                                <span class="text-muted">{{ $res['description'] }}</span>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
        @php
            $alltags = '';
            if (isset($filters['tags'])) {
                // treat a string for tags
                foreach ($filters['tags'] as $key => $value) {
                    $alltags .= $value . (isset($filters['tags'][$key + 1]) ? ', ' : '');
                }
            }
        @endphp
        <div class="col-5">
            <div class="filters">
                <h4>Filtros</h4>
                <hr>
                <label>Tags:</label>
                <input type="text" class="form-control mt-1" name="tags" id="filter0"
                    placeholder="Fast Food, Veggie, Healthy etc" value="{{ $alltags }}">
                <label class="mt-3">Rating:</label><br>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="filter1">
                    <label class="form-check-label" for="filter1">
                        1
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="filter2">
                    <label class="form-check-label" for="filter2">
                        2
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="filter3">
                    <label class="form-check-label" for="filter3">
                        3
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="filter4">
                    <label class="form-check-label" for="filter4">
                        4
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="filter5">
                    <label class="form-check-label" for="filter5">
                        5
                    </label>
                </div>
                <button class="btn btn-light mt-3 form-control" id="filter_conf">Filtrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(() => {
        $(".restaurant").on('click', function() {
            window.location.href = "/restaurante/" + this.id;
        })

        // The DOM element you wish to replace with Tagify
        var input = document.querySelector('input[name=tags]');

        // initialize Tagify on the above input node reference
        new Tagify(input)

        $("#filter_conf").on('click', () => {
            // get all filters
            var filterData = [];
            var map = ["tags", "c1", "c2", "c3", "c4", "c5", ];
            for (var i = 0; i < 6; i++) {
                filterData[map[i]] = i == 0 ? $("#filter" + i).val() : ($("#filter" + i).is(
                    ":checked") ? 1 : 0);
            }

            $.ajax({
                method: 'post',
                url: '/search_no_reload',
                data: {
                    "_token": "{{ csrf_token() }}",
                    hasFilters: 1,
                    tags: filterData.tags,
                    reviews: {
                        1: filterData.c1,
                        2: filterData.c2,
                        3: filterData.c3,
                        4: filterData.c4,
                        5: filterData.c5
                    },
                    query: $("#searchBar").val()
                }
            }).done((res) => {
                $("#container").html(res);
            })
        })
    });
</script>
