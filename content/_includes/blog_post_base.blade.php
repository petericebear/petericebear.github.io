@extends('_includes.base')

@section('body')

    <a href="/">Back to home</a>

    <h1 class="blog-post-title">@yield('post::title')</h1>

    @yield('post_body')

    <span class="post-footer">—————————————&nbsp;&nbsp; Please share if you liked it &nbsp;&nbsp;—————————————</span>

    <div class="text-center">
        <a href="https://twitter.com/share"
           class="twitter-share-button"
           data-text="{{str_limit($__env->yieldContent('post::title'),95)}}"
           data-via="petericebear">Tweet</a>
        &nbsp&nbsp&nbsp
        <a href="https://twitter.com/intent/tweet?screen_name=petericebear"
           class="twitter-mention-button"
           data-related="themsaid">Tweet to @petericebear</a>
    </div>
@stop