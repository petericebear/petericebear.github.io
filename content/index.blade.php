@extends('_includes.base')

@section('pageTitle', 'WebDeveloper - Laravel Enthousiast -')

@section('body')

    @foreach($paginatedBlogPosts as $post)
        <div class="blog-post">
            <h3><a href="{{$post->path}}">{{$post->title}}</a></h3>
            <p>{{$post->brief}}</p>
        </div>
    @endforeach

    @include('_includes.blog_paginator')
@stop