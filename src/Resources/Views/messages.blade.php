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
