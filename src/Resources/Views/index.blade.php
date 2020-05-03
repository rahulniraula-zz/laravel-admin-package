@extends(config('admin.base_admin_layout'))
@section('admin_content')
@if(count($items)>0)
<table class="table table-hover">
    <tr>
        @foreach($cols as $col)
        <th>{{$col}}</th>
        @endforeach
        @foreach($a_cols as $a_col)
        <th>{{$a_col}}</th>
        @endforeach
    </tr>
    @foreach($items as $item)
    <tr>
        @foreach($cols as $col)
        <td>{{$item->getValue($col,'colsToInclude')}}</td>
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
