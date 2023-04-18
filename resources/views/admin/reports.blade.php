@include('layouts.includes')

<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@extends('layouts.admin.sidebar', ['file' => 'reports'])

@section('content')

    @component('components.modal_builder', [
        'modal_id' => 'reportMsg',
        'hasHeader' => true,
        'rawHeader' =>
            '<h5 class="modal-title" id="reportMsgLabel"><i class="fa-solid fa-triangle-exclamation" style="color: #e13737;"></i> Denúncia</h5>',
        'hasBody' => true,
        'rawBody' => '<div class="report-container"><span class="report-msg" id="report_msg"></span></div>
                                      <input type="hidden" id="review_id">
                                      <input type="hidden" id="report_id">',
        'hasFooter' => true,
        'buttons' => [
            ['label' => 'Apagar esta review', 'id' => 'removeRev', 'class' => 'btn btn-danger'],
            ['label' => 'Ignorar', 'id' => 'ignore', 'class' => 'btn btn-dark'],
        ],
    ])
    @endcomponent

    <div class="container mt-3">
        <div class="reports-container">
            <table class="table" id="reports" style="width: 100%">
                <thead style="background-color: rgb(20, 20, 20); color:white">
                    <th class="t-point1">User</th>
                    <th>Title</th>
                    <th>Review</th>
                    <th>Star</th>
                    <th>Written At</th>
                    <th class="t-point2"></th>
                </thead>
            </table>
        </div>
    </div>

    <script>
        function open_modal(id, desc, r_id) {
            $("#review_id").val(id);
            $("#report_id").val(r_id);
            $("#report_msg").text(desc);
            $("#reportMsg").modal("toggle");
        }

        $("#ignore").on('click', () => {
            ignoreORremove("/admin/denuncias/ignore");
        })

        $("#removeRev").on('click', () => {
            ignoreORremove("/admin/denuncias/remove");
        })

        function ignoreORremove(ajaxUrl) {
            $.ajax({
                method: "post",
                url: ajaxUrl,
                data: {
                    "_token": "{{ csrf_token() }}",
                    'report_id': $("#report_id").val(),
                    'review_id': $("#review_id").val()
                }
            }).done((res) => {
                successToast(res.title, res.message);
                $("#reportMsg").modal("toggle");
                $("#reports").DataTable().ajax.reload(null, false);
            }).fail((err) => {
                errorToast(err.responseJSON.title, err.responseJSON.message);
            })
        }

        $("#reports").dataTable({
            "ordering": false,

            "language": {
                "paginate": {
                    "next": '<i class="fa-solid fa-caret-right"></i>',
                    "previous": '<i class="fa-solid fa-caret-left"></i>'
                }
            },

            ajax: {
                method: "post",
                url: "/admin/denuncias/get",
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                dataSrc: ''
            },
            columns: [{
                    data: "user",
                    width: "20%"
                },
                {
                    data: "title",
                    width: "20%"
                },
                {
                    data: "review",
                    width: "25%"
                },
                {
                    data: "stars",
                    width: "10%"
                },
                {
                    data: "written_at",
                    width: "15%",
                    render: function(data, type, row, meta) {
                        var format = new Date(data);
                        format = format.getDate() + '/' + (format.getMonth() + 1) + '/' + format
                            .getFullYear();
                        return format;
                    }
                },
                {
                    data: null,
                    width: "10%",
                    render: function(data, type, row, meta) {
                        return "<i class=\"fa-solid fa-message-lines reports-ico\" onclick=\"open_modal(" +
                            row.id + ", '" + row.description + "', " + row.reportid + ")\"></i>";
                    }
                }
            ]
        });
    </script>
@stop
