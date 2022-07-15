@extends('layout')

@section('content')
    <div class="row">
        <div class="col">
            <h1> Test send </h1>
            <form action="{{ route('notifications.send') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label  class="form-label">Title</label>
                    <input type="text" class="form-control" name="title">
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Content</label>
                    <textarea class="form-control" rows="3" name="content"></textarea>
                  </div>
                  <button type="submit" class="btn btn-primary"> Send </button>
            </form>
        </div>
    </div>
@endsection