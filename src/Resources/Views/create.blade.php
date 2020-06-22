@extends(config('admin.base_admin_layout'))
@section('admin_content')
@php Illuminate\Support\Facades\Event::dispatch('before.render',["create",$class_name]) @endphp
{{app()->make('ViewRecorder')->getContent("create")}}
{!! Form::open(['url' =>route(config('admin.prefix') . '.store',['model'=>$modelUrlSegment]),'files'=>true ]) !!}
<div class="row">
    @foreach($fields as $field_name=>$metadata)
    @php
    $lang=[];
    if(property_exists($class_name,'do_not_translate')){
    $lang=in_array($field_name,
    $class_name::$do_not_translate)?[config('admin.default_language')]:config('admin.languages');
    }else{
    $lang=config('admin.languages');
    }

    @endphp
    @foreach($lang as $language)
    @if(true)
    <div class="{{isset($metadata['wrapper_css_class'])?$metadata['wrapper_css_class']:'col-md-12'}}">
        <div class="form-group">
            {!!
            Form::label($field_name,$class_name::getLabel($field_name))!!} <span
                class="label_language">{{$language['code']}}</span>
            {!! Geeklearners\Util\Util::buildForm($field_name.'__'.$language['code'],$metadata) !!}
        </div>
    </div>
    @endif
    @endforeach

    @endforeach
</div>
<div class="row">
    <div class="col-md-12">
        @if(property_exists($class_name,'form_submit_button'))
        {!!
        Form::submit(isset($class_name::$form_submit_button['value'])?$class_name::$form_submit_button['value']:'Submit',$class_name::$form_submit_button)
        !!}
        @else
        {!! Form::submit('Submit',['class'=>'btn btn-primary float-right'])!!}
        @endif
    </div>
</div>
{!! Form::close() !!}
@endsection
