@extends('layouts.app')
@section('title', 'Template Page')

@section('content')
<h2>Template Create</h2>

@if(Session::has('success'))
<p class="alert alert-info" style="color:green;">{{ Session::get('success') }}</p>
@endif

@if(Session::has('error'))
<p class="alert alert-info" style="color:red;">{{ Session::get('error') }}</p>
@endif

<form action="{{route('templates.store')}}" method="POST" enctype='multipart/form-data'>
   @csrf
  <div class="form-group">
    <label for="inputTitle">Template Title</label>
    <input type="text" class="form-control" id="inputTitle" placeholder="enter template title" name="title">
  </div>
  <div class="form-group">
    <label for="inputTitle">Template Image</label>
    <input type="file" class="form-control" id="inputImage" name="image">
  </div>
  <div class="form-group">
    <label for="exampleFormControlTextarea1">Tags</label>
    <input type="text" value="" data-role="tagsinput" name="tags"/>
  </div>
  <input type="submit" value="submit" name="submit"/>
</form>


@endsection

