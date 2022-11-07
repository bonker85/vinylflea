@foreach($releases as $release)
    <dl class="row mt-3 releases-block">
        <dt class="col-sm-4 img-release">
            @if ($release->cover_image)
                @if (isset($release->uri))
                    <a href="https://discogs.com{{$release->uri}}" target="_blank">
                @endif
                @if (!strpos($release->cover_image, 'spacer.gif'))
                    <img src="{{$release->cover_image}}" alt="{{$release->title}}"/>
                @else
                    <img src="{{asset('assets/images/release/no-release.png')}}" alt="{{$release->title}}" width="100" />
                @endif
                @if (isset($release->uri))
                    </a>
                @endif
            @else
                <img src="{{asset('assets/images/release/no-release.png')}}" alt="{{$release->title}}" width="100" />
            @endif
        </dt>
        <dd class="col-sm-8">
            <dl class="row">
                @if (isset($relation_release))
                    <dt class="col-sm-3">
                        Связать
                    </dt>
                    <dd class="col-sm-9">
                        <input type="radio" value="{{$release->id}}" name="relation_release" />
                    </dd>
                @endif
                <dt class="col-sm-3">
                    Название
                </dt>
                <dd class="col-sm-9">
                    @if ($release->title){{$release->title}} @else - @endif
                </dd>
                <dt class="col-sm-3">
                    Жанр
                </dt>
                <dd class="col-sm-9">
                    @if (is_string($release->genre))
                        @if ($release->genre){{implode(', ', json_decode($release->genre))}} @else - @endif
                    @else
                        @if ($release->genre){{implode(', ', $release->genre)}} @else - @endif
                    @endif
                </dd>
                <dt class="col-sm-3">
                    Стиль
                </dt>
                <dd class="col-sm-9">
                    @if (is_string($release->style))
                        @if ($release->style){{implode(', ', json_decode($release->style))}} @else - @endif
                    @else
                        @if ($release->style){{implode(', ', $release->style)}} @else - @endif
                    @endif
                </dd>
                <dt class="col-sm-3">
                    Год
                </dt>
                <dd class="col-sm-9">
                    @if (isset($release->year)){{$release->year}} @else - @endif
                </dd>
                <dt class="col-sm-3">
                    Страна
                </dt>
                <dd class="col-sm-9">
                    @if ($release->country){{$release->country}} @else - @endif
                </dd>
                @if (is_string($release->label))
                <dt class="col-sm-3">
                    Labels
                </dt>
                <dd class="col-sm-9">
                   @if ($release->label){{implode(', ', json_decode($release->label))}} @else - @endif
                </dd>
                @endif
            </dl>
        </dd>
        @if (!$loop->last)
            <hr/>
        @endif
    </dl>
@endforeach
