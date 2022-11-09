<table id="advert_exports">
    <thead>
    <tr>
        @foreach ($title as $class => $name)
            <th class="{{$class}}">{{$name}}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach ($table as $row)
        <tr>
            @foreach ($row as $item)
                @if (is_array($item))
                    <td>
                        @foreach ($item as $link)
                            <a href="{{$link['link']}}">{{$link['name']}}</a>
                        @endforeach
                    </td>
                @else
                    <td>{{$item}}</td>
                @endif
             @endforeach
        </tr>
        @endforeach
    </tbody>
</table>

