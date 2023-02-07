<link rel="stylesheet" href="{{ asset('css/search.css') }}">

<div class="container">
    <div class="row">
        <div class="col-7">
            <div class="restaurants">
                <div class="row mb-4">
                    <div class="col-4">
                        <img src="https://toohotel.com/wp-content/uploads/2022/09/TOO_restaurant_Panoramique_vue_Paris_nuit_v2-scaled.jpg"
                            class="restaurant-img">
                    </div>
                    <div class="col-7">
                        <h3 class="restaurant-title">Nome Restaurante</h3>
                        <span class="text-muted">Lorem ipsum dolor sit amet, consectetur adipiscing
                            elit, sed do eiusmod tempor incididunt ut labore et
                            dolore magna aliqua.</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-5">
            <div class="filters">
                <h4>Filtros</h4>
                <hr>
                <label>Tags:</label>
                {{-- TODO: Use tagify later --}}
                <input type="text" class="form-control mt-1" placeholder="Fast Food, Veggie, Healthy etc">
                <label class="mt-3">Rating:</label><br>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="rating1">
                    <label class="form-check-label" for="rating1">
                        1
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="rating2">
                    <label class="form-check-label" for="rating2">
                        2
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="rating3">
                    <label class="form-check-label" for="rating3">
                        3
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="rating4">
                    <label class="form-check-label" for="rating4">
                        4
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="rating5">
                    <label class="form-check-label" for="rating5">
                        5
                    </label>
                </div>
                <button class="btn btn-light mt-3 form-control">Confirmar</button>
            </div>
        </div>
    </div>
</div>
