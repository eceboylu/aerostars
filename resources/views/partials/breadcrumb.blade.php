@if (! empty($breadcrumbs ?? []))
<nav class="crumbs" aria-label="Breadcrumb">
    <ol>
        <li>
            <a href="{{ route('home') }}">
                Home
            </a>
        </li>

        @foreach ($breadcrumbs as $breadcrumb)
            <li>
                @if ($loop->last || ! $breadcrumb->url)
                    <span aria-current="page">
                        {{ $breadcrumb->title }}
                    </span>
                @else
                    <a href="{{ $breadcrumb->url }}">
                        {{ $breadcrumb->title }}
                    </a>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
@endif