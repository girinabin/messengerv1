@extends('layouts.app')

@section('content')
<div id="app" class="container">
    <messages :user="{{ Auth::user() }}"></messages>
</div>
@endsection
