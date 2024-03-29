@include('layouts.includes')
@extends('layouts.clients.nav')

@section('content')
    @include('components.frontend.restaurantstop', ['selected' => 'reviews'])
    <link rel="stylesheet" href="{{ asset('css/restaurants/reviews.css') }}">

    <div class="container">
        <div class="overview">
            <div class="ovHeader">
                <div class="row">
                    <div class="col-6">
                        <h2 style="float: left">Reviews</h2>
                    </div>
                    <div class="col-6">
                        <span style="float: right">
                            @for ($i = 0; $i < $stats['avg']; $i++)
                                <i class="fa-solid fa-star rw-st"></i>
                            @endfor
                        </span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        @php
                            $color_map = ['#FD1919', '#FF450B', '#FFD600', '#9CCC37', '#38B945'];
                        @endphp
                        @for ($i = 4; $i >= 0; $i--)
                            <label class="{{ $i != 4 ? 'mt-3' : '' }}">{{ $i + 1 }} estrelas</label>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" aria-label="Basic example"
                                    style="width: {{ isset($stats['stars'][$i + 1]) ? $stats['stars'][$i + 1] : 0 }}%; background-color: {{ $color_map[$i] }}"
                                    aria-valuenow="{{ isset($stats['stars'][$i + 1]) ? $stats['stars'][$i + 1] : 0 }}"
                                    aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        @endfor
                    </div>
                </div>

                <h4 class="mt-3">{{ $label }}</h4>
            </div>
        </div>
        <br /><br />
        <div class="rv-container" id="rv_container">
            @if ($canReview)
                <div class="review_form">
                    <h4 id="dur">Deixar uma Review</h4>
                    {{-- Will be either "new" or "edit" to know if user is adding a new review or editing and existing one --}}
                    <input type="hidden" value="new" id="whichAction">
                    <input type="text" class="form-control mt-3" placeholder="Titulo..." id="review_title">
                    <textarea type="text" class="form-control mt-2" rows="5" placeholder="Escreva aqui a sua critica..."
                        id="review_body"></textarea>
                    <div style="display: flex; justify-content: center">
                        <div class="f-stars mt-3">
                            <i class="fa-solid fa-star s1"></i>
                            <i class="fa-solid fa-star s2"></i>
                            <i class="fa-solid fa-star s3"></i>
                            <i class="fa-solid fa-star s4"></i>
                            <i class="fa-solid fa-star s5"></i>
                        </div>
                    </div>
                    <button class="mt-3 btn btn-dark form-control" id="send_review">Enviar Review</button>
                    <button class="mt-3 btn btn-danger form-control visually-hidden" id="cancel_editing">Parar de
                        Editar</button>
                </div>
            @endif
        </div>
        <br>
        <div class="container">
            @if (count($myreviews) > 0)
                <hr>
                <br>
                <h4 style="margin-right: 25px">Minhas Reviews</h4>
                <div class="arc">
                    <div>
                        @foreach ($myreviews as $mr)
                            <div class="r-item" id="mr_{{ $mr['id'] }}">
                                <img src="{{ asset('img/pfp/' . $mr['pfp']) }}"
                                    onerror="this.src = '{{ asset('img/pfp/defaultpfp.png') }}';" class="r-pfp"
                                    id="userIco">
                                <span style="font-size: 20px">{{ $mr['first_name'] . ' ' . $mr['last_name'] }}</span>
                                <br />
                                <div class="allr-stars mt-3">
                                    @for ($i = 0; $i < $mr['stars']; $i++)
                                        <i class="fa-solid fa-star rw-st"></i>
                                    @endfor
                                    <span style="float: right">{{ date('d/m/Y', strtotime($mr['written_at'])) }}</span>
                                </div>
                                <hr>
                                <div class="r-title">
                                    <h3>{{ $mr['title'] }}</h3>
                                </div>
                                <div class="r-b">
                                    <p style="width: 66vw">{{ $mr['review'] }}</p>
                                </div>
                                <div class="r-actions">
                                    <i class="fa-solid fa-pen-to-square"
                                        onclick="onEdit({{ $mr['id'] }}, `{{ $mr['title'] }}`, `{{ $mr['review'] }}`, {{ $mr['stars'] }})"></i>
                                    <i onclick="deleteReview({{ $mr['id'] }})" class="fa-solid fa-trash-can"></i>
                                </div>
                            </div><br />
                        @endforeach
                    </div>
                </div>
                <br>
            @endif
            <hr>
            <div class="arc">
                <div class="all_reviews mt-5">
                    @foreach ($reviews as $r)
                        @if ($r['written_by'] != session()->get('user.id'))
                            <div class="r-item">
                                <img src="{{ asset('img/pfp/' . $r['pfp']) }}"
                                    onerror="this.src = '{{ asset('img/pfp/defaultpfp.png') }}';" class="r-pfp"
                                    id="userIco">
                                <span style="font-size: 20px">{{ $r['first_name'] . ' ' . $r['last_name'] }}</span>
                                <br />
                                <div class="allr-stars mt-3">
                                    @for ($i = 0; $i < $r['stars']; $i++)
                                        <i class="fa-solid fa-star rw-st"></i>
                                    @endfor
                                    <span style="float: right">{{ date('d/m/Y', strtotime($r['written_at'])) }}</span>
                                </div>
                                <hr>
                                <div class="r-title">
                                    <h3>{{ $r['title'] }}</h3>
                                </div>
                                <div class="r-b">
                                    <p style="width: 66vw">{{ $r['review'] }}</p>
                                </div>
                            </div><br />
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="id_of_editing" value="none...">

    <script defer>
        let total_stars = 0;

        const stars = $(".f-stars i")

        // Switches the review form from adding to editing and vice versa
        function switchActions(edit = 0, info = {}) {
            $(".fa-star").removeClass("star-active");

            $("#review_title").val(edit ? info.title : '');
            $("#review_body").val(edit ? info.review : '');
            total_stars = edit ? info.stars : 0;
            if (edit) {
                for (var i = 0; i < info.stars; i++) {
                    $(".s" + (i + 1)).addClass("star-active");
                }
                $("#cancel_editing").removeClass("visually-hidden");
            } else {
                $("#cancel_editing").addClass("visually-hidden");
            }

            $("#whichAction").val(edit ? "edit" : "new");
            $("#send_review").text(edit ? "Editar Review" : "Enviar Review");
        }

        // To edit reviews
        function onEdit(id, title, review, stars) {
            $("#id_of_editing").val(id);
            switchActions(1, {
                title: title,
                review: review,
                stars: stars
            });
            $('html, body').animate({
                scrollTop: $("#rv_container").offset().top - 500
            }, 0);
        }

        $("#cancel_editing").on('click', () => {
            switchActions();
        })

        function deleteReview(id) {
            $.ajax({
                method: "post",
                url: "/review/remove",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": id
                }
            }).done(res => {
                successToast(res.title, res.message);
                $("#mr_" + id).remove();
            }).fail(err => {
                errorToast(err.responseJSON.title, err.responseJSON.message);
            });
        }

        $.each(stars, (index1, star) => {
            $(star).on('click', () => {
                $.each(stars, (index2, star) => {
                    total_stars = index1 + 1;
                    index1 >= index2 ? $(star).addClass("star-active") : $(star).removeClass(
                        "star-active")
                })
            })
        })

        $("#send_review").on('click', () => {
            var hasEmpty = animateErr(["review_title", "review_body"]);
            if (total_stars === 0) {
                $(".f-stars").addClass("animate__animated animate__headShake");
                $(".f-stars").addClass("stars-empty");
                setTimeout(() => {
                    $(".f-stars").removeClass("animate__animated animate__headShake");
                    $(".f-stars").removeClass("stars-empty");
                }, 800);
            }

            if (hasEmpty || total_stars <= 0) return;

            const texto = '{"text":"' + $("#review_title").val() + ' ' + $("#review_body").val() +
                '", "maskCharacter": "x", "language": "pt"}';
            console.log(texto)
            const settings = {
                async: true,
                crossDomain: true,
                url: 'https://profanity-cleaner-bad-word-filter.p.rapidapi.com/profanity',
                method: 'POST',
                headers: {
                    'content-type': 'application/json',
                    'X-RapidAPI-Key': 'fd4376aebcmshee63295b5031b37p1eb22djsnccb958dc8818',
                    'X-RapidAPI-Host': 'profanity-cleaner-bad-word-filter.p.rapidapi.com'
                },
                processData: false,
                data: texto
            };

            $.ajax(settings).done(function(res) {
                if (res.profanities.length > 0) {
                    return errorToast("Aviso!", "Críticas não devem conter profanidade");
                }
                var ajax_data = {
                    "_token": "{{ csrf_token() }}",
                    "restaurant_id": $("#restaurant_id").val(),
                    "title": $("#review_title").val(),
                    "body": $("#review_body").val(),
                    "stars": total_stars,
                    "edit": $("#whichAction").val() == "new" ? 0 : 1,
                    "id": $("#whichAction").val() == "new" ? "no to be used..." : $("#id_of_editing")
                        .val()
                };

                $.ajax({
                    method: 'post',
                    url: '/review/add',
                    data: ajax_data
                }).done((res) => {
                    successToast(res.title, res.message);
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }).fail((err) => {
                    errorToast(err.responseJSON.title, err.responseJSON.message);
                })
            });
        });
    </script>
@stop
