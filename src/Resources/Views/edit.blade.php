@extends(config('admin.base_admin_layout'))
@section('admin_content')
@php Illuminate\Support\Facades\Event::dispatch('before.render',["create",$class_name]) @endphp
{{app()->make('ViewRecorder')->getContent("edit")}}
{!! Form::model($item,['files'=>true,'url' =>route(config('admin.prefix') .
'.update',['model'=>$modelUrlSegment,'id'=>$item->id])
])
!!}
<input type="hidden" name="_method" value="put">
<div class="row">
    @foreach($fields as $field_name=>$metadata)
    <div class="{{isset($metadata['wrapper_css_class'])?$metadata['wrapper_css_class']:'col-md-12'}}">
        <div class="form-group">
            {!! Form::label($field_name,ucwords($field_name))!!}
            {!! Geeklearners\Util\Util::buildForm($field_name,$metadata,$item) !!}
        </div>
    </div>
    @endforeach
</div>
<div class="row">
    <div class="col-md-12">
        @if(property_exists($class_name,'form_update_button'))
        {!!
        Form::submit(isset($class_name::$form_update_button['value'])?$class_name::$form_update_button['value']:'Update',$class_name::$form_update_button)
        !!}
        @else
        {!! Form::submit('Update',['class'=>'btn btn-primary float-right'])!!}
        @endif
    </div>
</div>
{!! Form::close() !!}
@endsection
