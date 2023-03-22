@include('layouts.includes')
@extends('layouts.professional.sidebar', ['file' => 'stats'])


@section('content')
    <link rel="stylesheet" href="{{ asset('css/stats.css') }}">

    <span>
        <button class="btn tab-selected" id="weekly" onclick="changeTab('weekly')">Semana</button>
        <button class="btn" id="monthly" onclick="changeTab('monthly')">Mês</button>
        <button class="btn" id="yearly" onclick="changeTab('yearly')">Ano</button>
    </span>
    <hr>
    <div id="weekly_section" class="section">
        <div class="container">
            <div class="title">
                <h2><i class="fa-regular fa-sack-dollar"></i> Lucro por Dia</h2>
            </div>
            <div class="content-container">
                <div>
                    <div class="profit-info mt-3">
                        <div class="row">
                            <div class="col-lg-2 col-md-4 col-sm-12 mt-1">
                                <div style="display: inline-flex">
                                    <div class="square-info"></div>
                                    <span class="silbl">Esta Semana</span>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-12 mt-1">
                                <div style="display: inline-flex">
                                    <div class="square-info" style="background-color: #5a76be;"></div>
                                    <span class="silbl">Ultima Semana</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="profit mt-2">
                        <div class="toCenter">
                            <canvas id="profit_p_day" style="width:100%;max-width:700px"></canvas>
                        </div>
                        {{-- Save data from the controller --}}
                        <input type="hidden" value="{{ json_encode($thisPerDay) }}" id="barChartThis">
                        <input type="hidden" value="{{ json_encode($lastPerDay) }}" id="barChartLast">
                    </div>
                </div>
            </div>
            <div class="title mt-5">
                <h2><i class="fa-solid fa-star-sharp-half-stroke"></i> Reviews</h2>
            </div>
            <div>
                <div class="reviews-stats mt-3">
                    @php
                        $color_map = ['#FD1919', '#FF450B', '#FFD600', '#9CCC37', '#38B945'];
                    @endphp
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <h2 class="rv-head">Ultima Semana</h2>
                            <div style="width: 85%">
                                @for ($i = 4; $i >= 0; $i--)
                                    <label class="{{ $i != 4 ? 'mt-3' : '' }}">{{ $i + 1 }} estrelas</label>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" aria-label="Basic example"
                                            style="width: {{ isset($lastRev['stars'][$i + 1]) ? $lastRev['stars'][$i + 1] : 0 }}%; background-color: {{ $color_map[$i] }}"
                                            aria-valuenow="{{ isset($lastRev['stars'][$i + 1]) ? $lastRev['stars'][$i + 1] : 0 }}"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                @endfor
                            </div>
                            <h5 class="mt-3" style="color:#1C46B2">Média:</h5>
                            <span style="float: left; font-size: 25px" class="mt-1">
                                @for ($i = 0; $i < $lastRev['avg']; $i++)
                                    <i class="fa-solid fa-star rw-st"></i>
                                @endfor
                            </span>
                        </div>
                        <hr class="separation-sm">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <h2 class="rv-head">Esta Semana</h2>
                            <div style="width: 85%">
                                @for ($i = 4; $i >= 0; $i--)
                                    <label class="{{ $i != 4 ? 'mt-3' : '' }}">{{ $i + 1 }} estrelas</label>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" aria-label="Basic example"
                                            style="width: {{ isset($thisRev['stars'][$i + 1]) ? $thisRev['stars'][$i + 1] : 0 }}%; background-color: {{ $color_map[$i] }}"
                                            aria-valuenow="{{ isset($thisRev['stars'][$i + 1]) ? $thisRev['stars'][$i + 1] : 0 }}"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                @endfor
                            </div>
                            <h5 class="mt-3" style="color:#1C46B2">Média:</h5>
                            <span style="float: left; font-size: 25px" class="mt-1">
                                @for ($i = 0; $i < $thisRev['avg']; $i++)
                                    <i class="fa-solid fa-star rw-st"></i>
                                @endfor
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="title mt-5">
                <h2><i class="fa-solid fa-cash-register"></i> Lucro Semanal</h2>
            </div>
            <div>
                <div class="gain mt-3">
                    <div class="row" style="font-size: 24px">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <h2 style="color:#1C46B2">Ultima Semana:</h2>
                            <span>Média de Dinheiro / dia: <span
                                    style="color:#1C46B2">{{ $lastGain['avg_per_day'] ? $lastGain['avg_per_day'] : 0 }}€</span></span><br />
                            <span class="mt-3">Média de custo / dia: <span
                                    style="color:#1C46B2">{{ $lastGain['avg_cost_per_day'] ? $lastGain['avg_cost_per_day'] : 0 }}€</span></span>
                            <div class="title mt-3">
                                <h3 style="font-weight: 600">Lucro Total:</h3>
                            </div>
                            <div class="title">
                                <h2 style="color:#1C46B2">{{ $lastGain['total_gain'] ? $lastGain['total_gain'] : 0 }}€</h2>
                            </div>
                        </div>
                        <hr class="separation-sm">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <h2 style="color:#1C46B2">Esta Semana:</h2>
                            <span>Média de Dinheiro / dia: <span
                                    style="color:#1C46B2">{{ $thisGain['avg_per_day'] ? $thisGain['avg_per_day'] : 0 }}€</span></span><br />
                            <span class="mt-3">Média de custo / dia: <span
                                    style="color:#1C46B2">{{ $thisGain['avg_cost_per_day'] ? $thisGain['avg_cost_per_day'] : 0 }}€</span></span>
                            <div class="title mt-3">
                                <h3 style="font-weight: 600">Lucro Total:</h3><br>
                            </div>
                            <div class="title">
                                <h2 style="color:#1C46B2">{{ $thisGain['total_gain'] ? $thisGain['total_gain'] : 0 }}€
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
                <br><br>
            </div>
        </div>
    </div>
    <div id="monthly_section" class="visually-hidden section">
        <div class="container">
            <div class="title">
                <h2><i class="fa-regular fa-sack-dollar"></i> Lucro por Semana</h2>
            </div>
            <div class="content-container">
                <div>
                    <div class="profit-info mt-3">
                        <div class="row">
                            <div class="col-lg-2 col-md-4 col-sm-12 mt-1">
                                <div style="display: inline-flex">
                                    <div class="square-info"></div>
                                    <span class="silbl">Este Mês</span>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-12 mt-1">
                                <div style="display: inline-flex">
                                    <div class="square-info" style="background-color: #5a76be;"></div>
                                    <span class="silbl">Ultimo Mês</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="profit mt-2">
                        <div class="toCenter">
                            <canvas id="profit_p_week" style="width:100%;max-width:700px"></canvas>
                        </div>
                        {{-- Save data from the controller --}}
                        <input type="hidden" value="{{ json_encode($thisPerMonth) }}" id="barChartThisWEEK">
                        <input type="hidden" value="{{ json_encode($lastPerMonth) }}" id="barChartLastWEEK">
                    </div>
                </div>
            </div>
            <div class="title mt-5">
                <h2><i class="fa-solid fa-star-sharp-half-stroke"></i> Reviews</h2>
            </div>
            <div>
                <div class="reviews-stats mt-3">
                    @php
                        $color_map = ['#FD1919', '#FF450B', '#FFD600', '#9CCC37', '#38B945'];
                    @endphp
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <h2 class="rv-head">Ultimo Mês</h2>
                            <div style="width: 85%">
                                @for ($i = 4; $i >= 0; $i--)
                                    <label class="{{ $i != 4 ? 'mt-3' : '' }}">{{ $i + 1 }} estrelas</label>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" aria-label="Basic example"
                                            style="width: {{ isset($lastRevMonth['stars'][$i + 1]) ? $lastRevMonth['stars'][$i + 1] : 0 }}%; background-color: {{ $color_map[$i] }}"
                                            aria-valuenow="{{ isset($lastRevMonth['stars'][$i + 1]) ? $lastRevMonth['stars'][$i + 1] : 0 }}"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                @endfor
                            </div>
                            <h5 class="mt-3" style="color:#1C46B2">Média:</h5>
                            <span style="float: left; font-size: 25px" class="mt-1">
                                @for ($i = 0; $i < $lastRevMonth['avg']; $i++)
                                    <i class="fa-solid fa-star rw-st"></i>
                                @endfor
                            </span>
                        </div>
                        <hr class="separation-sm">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <h2 class="rv-head">Este Mês</h2>
                            <div style="width: 85%">
                                @for ($i = 4; $i >= 0; $i--)
                                    <label class="{{ $i != 4 ? 'mt-3' : '' }}">{{ $i + 1 }} estrelas</label>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" aria-label="Basic example"
                                            style="width: {{ isset($thisRevMonth['stars'][$i + 1]) ? $thisRevMonth['stars'][$i + 1] : 0 }}%; background-color: {{ $color_map[$i] }}"
                                            aria-valuenow="{{ isset($thisRevMonth['stars'][$i + 1]) ? $thisRevMonth['stars'][$i + 1] : 0 }}"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                @endfor
                            </div>
                            <h5 class="mt-3" style="color:#1C46B2">Média:</h5>
                            <span style="float: left; font-size: 25px" class="mt-1">
                                @for ($i = 0; $i < $thisRevMonth['avg']; $i++)
                                    <i class="fa-solid fa-star rw-st"></i>
                                @endfor
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="title mt-5">
                <h2><i class="fa-solid fa-cash-register"></i> Lucro Mensal</h2>
            </div>
            <div>
                <div class="gain mt-3">
                    <div class="row" style="font-size: 24px">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <h2 style="color:#1C46B2">Ultimo Mês:</h2>
                            <span>Média de Dinheiro / dia: <span
                                    style="color:#1C46B2">{{ $lastGainMonth['avg_per_day'] ? $lastGainMonth['avg_per_day'] : 0 }}€</span></span><br />
                            <span class="mt-3">Média de custo / dia: <span
                                    style="color:#1C46B2">{{ $lastGainMonth['avg_cost_per_day'] ? $lastGainMonth['avg_cost_per_day'] : 0 }}€</span></span>
                            <div class="title mt-3">
                                <h3 style="font-weight: 600">Lucro Total:</h3>
                            </div>
                            <div class="title">
                                <h2 style="color:#1C46B2">
                                    {{ $lastGainMonth['total_gain'] ? $lastGainMonth['total_gain'] : 0 }}€</h2>
                            </div>
                        </div>
                        <hr class="separation-sm">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <h2 style="color:#1C46B2">Este Mês:</h2>
                            <span>Média de Dinheiro / dia: <span
                                    style="color:#1C46B2">{{ $thisGainMonth['avg_per_day'] ? $thisGainMonth['avg_per_day'] : 0 }}€</span></span><br />
                            <span class="mt-3">Média de custo / dia: <span
                                    style="color:#1C46B2">{{ $thisGainMonth['avg_cost_per_day'] ? $thisGainMonth['avg_cost_per_day'] : 0 }}€</span></span>
                            <div class="title mt-3">
                                <h3 style="font-weight: 600">Lucro Total:</h3><br>
                            </div>
                            <div class="title">
                                <h2 style="color:#1C46B2">
                                    {{ $thisGainMonth['total_gain'] ? $thisGainMonth['total_gain'] : 0 }}€
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
                <br><br>
            </div>
        </div>
    </div>

    <div id="yearly_section" class="visually-hidden section">
        <div class="container">
            <div class="title">
                <h2><i class="fa-regular fa-sack-dollar"></i> Lucro por Mês</h2>
            </div>
            <div class="content-container">
                <div>
                    <div class="profit-info mt-3">
                        <div class="row">
                            <div class="col-lg-2 col-md-4 col-sm-12 mt-1">
                                <div style="display: inline-flex">
                                    <div class="square-info"></div>
                                    <span class="silbl">Este Ano</span>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-4 col-sm-12 mt-1">
                                <div style="display: inline-flex">
                                    <div class="square-info" style="background-color: #5a76be;"></div>
                                    <span class="silbl">Ultimo Ano</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="profit mt-2">
                        <div class="toCenter">
                            <canvas id="profit_p_year" style="width:100%;max-width:700px"></canvas>
                        </div>
                        {{-- Save data from the controller --}}
                        <input type="hidden" value="{{ json_encode($thisPerYear) }}" id="barChartThisYEAR">
                        <input type="hidden" value="{{ json_encode($lastPerYear) }}" id="barChartLastYEAR">
                    </div>
                </div>
            </div>
            <div class="title mt-5">
                <h2><i class="fa-solid fa-star-sharp-half-stroke"></i> Reviews</h2>
            </div>
            <div>
                <div class="reviews-stats mt-3">
                    @php
                        $color_map = ['#FD1919', '#FF450B', '#FFD600', '#9CCC37', '#38B945'];
                    @endphp
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <h2 class="rv-head">Ultimo Ano</h2>
                            <div style="width: 85%">
                                @for ($i = 4; $i >= 0; $i--)
                                    <label class="{{ $i != 4 ? 'mt-3' : '' }}">{{ $i + 1 }} estrelas</label>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" aria-label="Basic example"
                                            style="width: {{ isset($lastRevYear['stars'][$i + 1]) ? $lastRevYear['stars'][$i + 1] : 0 }}%; background-color: {{ $color_map[$i] }}"
                                            aria-valuenow="{{ isset($lastRevYear['stars'][$i + 1]) ? $lastRevYear['stars'][$i + 1] : 0 }}"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                @endfor
                            </div>
                            <h5 class="mt-3" style="color:#1C46B2">Média:</h5>
                            <span style="float: left; font-size: 25px" class="mt-1">
                                @for ($i = 0; $i < $lastRevYear['avg']; $i++)
                                    <i class="fa-solid fa-star rw-st"></i>
                                @endfor
                            </span>
                        </div>
                        <hr class="separation-sm">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <h2 class="rv-head">Este Ano</h2>
                            <div style="width: 85%">
                                @for ($i = 4; $i >= 0; $i--)
                                    <label class="{{ $i != 4 ? 'mt-3' : '' }}">{{ $i + 1 }} estrelas</label>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" aria-label="Basic example"
                                            style="width: {{ isset($thisRevYear['stars'][$i + 1]) ? $thisRevYear['stars'][$i + 1] : 0 }}%; background-color: {{ $color_map[$i] }}"
                                            aria-valuenow="{{ isset($thisRevYear['stars'][$i + 1]) ? $thisRevYear['stars'][$i + 1] : 0 }}"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                @endfor
                            </div>
                            <h5 class="mt-3" style="color:#1C46B2">Média:</h5>
                            <span style="float: left; font-size: 25px" class="mt-1">
                                @for ($i = 0; $i < $thisRevYear['avg']; $i++)
                                    <i class="fa-solid fa-star rw-st"></i>
                                @endfor
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="title mt-5">
                <h2><i class="fa-solid fa-cash-register"></i> Lucro Anual</h2>
            </div>
            <div>
                <div class="gain mt-3">
                    <div class="row" style="font-size: 24px">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <h2 style="color:#1C46B2">Ultimo Ano:</h2>
                            <span>Média de Dinheiro / dia: <span
                                    style="color:#1C46B2">{{ $lastGainYear['avg_per_day'] ? $lastGainYear['avg_per_day'] : 0 }}€</span></span><br />
                            <span class="mt-3">Média de custo / dia: <span
                                    style="color:#1C46B2">{{ $lastGainYear['avg_cost_per_day'] ? $lastGainYear['avg_cost_per_day'] : 0 }}€</span></span>
                            <div class="title mt-3">
                                <h3 style="font-weight: 600">Lucro Total:</h3>
                            </div>
                            <div class="title">
                                <h2 style="color:#1C46B2">
                                    {{ $lastGainYear['total_gain'] ? $lastGainYear['total_gain'] : 0 }}€</h2>
                            </div>
                        </div>
                        <hr class="separation-sm">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <h2 style="color:#1C46B2">Este Ano:</h2>
                            <span>Média de Dinheiro / dia: <span
                                    style="color:#1C46B2">{{ $thisGainYear['avg_per_day'] ? $thisGainYear['avg_per_day'] : 0 }}€</span></span><br />
                            <span class="mt-3">Média de custo / dia: <span
                                    style="color:#1C46B2">{{ $thisGainYear['avg_cost_per_day'] ? $thisGainYear['avg_cost_per_day'] : 0 }}€</span></span>
                            <div class="title mt-3">
                                <h3 style="font-weight: 600">Lucro Total:</h3><br>
                            </div>
                            <div class="title">
                                <h2 style="color:#1C46B2">
                                    {{ $thisGainYear['total_gain'] ? $thisGainYear['total_gain'] : 0 }}€
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br><br>
        </div>
    </div>


    <script>
        function changeTab(tab) {
            const map = ["weekly", "monthly", "yearly"];
            $.each(map, (key, val) => {
                if (tab == val) {
                    if (!$("#" + val).hasClass("tab-selected")) {
                        $("#" + val).addClass("tab-selected");
                        $("#" + val + "_section").removeClass("visually-hidden");
                    }
                } else {
                    if ($("#" + val).hasClass("tab-selected")) {
                        $("#" + val).removeClass("tab-selected");
                        $("#" + val + "_section").addClass("visually-hidden");
                    }
                }
            });
        }


        // Format the array with the data for the barchart and format it
        function formatForBarChart(data) {
            var formatted = [];
            $.each(data, (key, val) => {
                formatted[formatted.length] = val.total_price;
            });
            return formatted;
        }

        createBarChart("profit_p_day");
        createBarChart("profit_p_week");
        createBarChart("profit_p_year");

        function createBarChart(chartID) {

            var lbls = [];
            if (chartID == "profit_p_day") {
                chartDataTHIS = formatForBarChart(JSON.parse($("#barChartThis").val()));
                chartDataLAST = formatForBarChart(JSON.parse($("#barChartLast").val()));
                var lbls = [
                    "Segunda-Feira",
                    "Terça-Feira",
                    "Quarta-Feira",
                    "Quinta-Feira",
                    "Sexta-Feira",
                    "Sábado",
                    "Domingo",
                ]
            } else if (chartID == "profit_p_week") {
                chartDataTHIS = formatForBarChart(JSON.parse($("#barChartThisWEEK").val()));
                chartDataLAST = formatForBarChart(JSON.parse($("#barChartLastWEEK").val()));
                var maxlength =
                    {{ count($lastPerMonth) > count($thisPerMonth) ? count($lastPerMonth) : count($thisPerMonth) }};
                for (var i = 0; i < maxlength; i++) {
                    lbls[i] = "Semana " + (i + 1);
                }
            } else {
                chartDataTHIS = formatForBarChart(JSON.parse($("#barChartThisYEAR").val()));
                chartDataLAST = formatForBarChart(JSON.parse($("#barChartLastYEAR").val()));
                var lbls = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro",
                    "Outubro", "Novembro", "Dezembro"
                ];

            }

            var data = {
                labels: lbls,
                datasets: [{
                        label: chartID == "profit_p_day" ? "Esta Semana" : (chartID == "profit_p_week" ?
                            "Este Mês" : "Este Ano"),
                        backgroundColor: "#1C46B2",
                        data: chartDataTHIS
                    },
                    {
                        label: chartID == "profit_p_day" ? "Ultima Semana" : (chartID == "profit_p_week" ?
                            "Ultimo Mês" : "Ultimo Ano"),
                        backgroundColor: "#5a76be",
                        data: chartDataLAST
                    }
                ]
            };
            var myBarChart = new Chart(chartID, {
                type: 'bar',
                data: data,
                options: {
                    legend: {
                        display: false
                    },
                    barValueSpacing: 20,
                    scales: {
                        yAxes: [{
                            ticks: {
                                min: 0,
                                callback: function(value, index, values) {
                                    return value.toLocaleString("en-US", {
                                        style: "currency",
                                        currency: "EUR"
                                    });
                                }
                            }
                        }]
                    }
                }
            });
        }
    </script>
@stop
