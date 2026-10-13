@php
    $categoriesRootPage = \App\Models\Page::where('slug', 'categories')->first();
    $familyPages = $categoriesRootPage
        ? $categoriesRootPage->children()
            ->where('status', true)
            ->withCount(['children' => fn ($query) => $query->where('status', true)])
            ->orderBy('title')
            ->get()
        : collect();
    $totalCategoryCount = $familyPages->sum('children_count');
@endphp
<section class="section"><div class="wrap">
  <div class="section__head"><h2>{{ $familyPages->count() }} award families, {{ $totalCategoryCount }} categories</h2>
  <p>Each family covers one part of the airport services market. Every category has its own page with criteria and suggested evidence.</p></div>
  <ul class="rows">
    @foreach ($familyPages as $familyPage)
        <li class="row"><a class="row__link" href="{{ $familyPage->publicUrl() }}"><div class="row__body"><span class="row__name">{{ $familyPage->title }}</span><span class="row__text">{{ \App\Http\Controllers\SiteController::shortDescription($familyPage) }}</span></div><span class="row__go">{{ $familyPage->children_count }} {{ Str::plural('category', $familyPage->children_count) }}</span></a></li>
    @endforeach
  </ul>
</div></section>
