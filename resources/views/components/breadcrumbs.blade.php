{{-- Create breadcrumbs --}}
<div class="breadcrumbs pb-4">
    <div class="d-flex flex-row bread-container">
        <div class="d-flex justify-content-start align-items-center">
            <h2 class="breadcrumbs-title" id="breadcrumb_title">{{ $title }}</h2>
            @if (isset($crumbs))
                <div class="d-flex flex-row ms-4">
                    @foreach ($crumbs as $key => $crumb)
                        <a id="breadcrumb_redirect" {{$crumb['link']?"href=".$crumb['link']."":''}}
                            class="{{$crumb['link']!=null?'crumbs':''}} me-2 text-muted">{{ $crumb['label'] }}</a>
                        @if (count($crumbs) != $key + 1)
                            <span class="text-muted me-2">-</span>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    @if (isset($separator) && $separator)
        <hr>
    @endif
</div>

<style>
    .breadcrumbs-title {
        color: rgb(46, 46, 46);
        font-weight: 700;
    }

    .crumbs {
        transition: all 0.2s;
    }

    .crumbs:hover {
        color: rgb(0, 0, 0) !important;
        text-decoration: underline;
    }

    .bread-container{
        background-color: white;
        width: fit-content;
        padding: 10px 15px;
        border-radius: 10px;
    }
</style>
