@if(Session::has('flash_warning'))
<div class="alert alert-warning">
    {{Session::get('flash_warning')}}
</div>
@endif
@if(Session::has('flash_success'))
<div class="alert alert-success">
    {{Session::get('flash_success')}}
</div>
@endif
@if($errors->any())
<div class="alert alert-danger">
    @foreach($errors->all() as $err)
    {{$err}}
    @endforeach
</div>
@endif
