<div class="col col-md order-2 order-md-2 ui search all-search">
    <div class=" input-group flex-nowrap px-xl-4 search-box">
        <div class="ui left icon input">
            <i class="bx bx-search icon"></i>
            <input type="text" class="form-control w-100 prompt" value="@if(request()->q) {{request()->q}} @endif" placeholder="Укажите автора или название пластинки" autocomplete="off">
            <select class="search-style form-select flex-shrink-0" aria-label="Default select example" >
                <option value="{{route("vinyls.styles")}}" @if (!request()->route()->parameter('style')) selected @endif>Все стили</option>
                @foreach ($styles as $style)
                    <option value="{{route('vinyls.style', $style->slug)}}" data-id="{{$style->id}}" @if (request()->route('style') && request()->route('style')->slug == $style->slug) selected @endif>{{$style->name}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
