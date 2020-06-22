@extends(config('admin.base_admin_layout'))
@section('admin_content')
@php Illuminate\Support\Facades\Event::dispatch('before.render',["list",$class_name]) @endphp
{{app()->make('ViewRecorder')->getContent("list")}}
@if(count($items)>0)
<table class="table table-hover">
    <tr>
        @foreach($cols as $col)
        <th>{{$class_name::getLabel($col)}}</th>
        @endforeach
        @foreach($a_cols as $a_col)
        <th>{{$class_name::getLabel($a_col)}}</th>
        @endforeach
    </tr>
    @foreach($items as $item)
    <tr>
        @foreach($cols as $col)
        <td>
            @if($item->hasHtmlEnabled($col))
            {!!$item->getValue($col,'colsToInclude')!!}
            @else
            {{$item->getValue($col,'colsToInclude')}}
            @endif
        </td>
        @endforeach
        @foreach($a_cols as $a_col)
        <td>{!!$item->getValue($a_col,'additionalColumns')!!}</td>
        @endforeach
    </tr>
    @endforeach
</table>
{!!$items->links()!!}
@else
<div class="alert alert-warning">No Data Available</div>
@endif
@endsection
