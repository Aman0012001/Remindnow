<!doctype html>
<html class="no-js" lang="en">
  <head>
    @include('frontend.elements.seo')
    @include('frontend.elements.commoncss')
  </head>
  <body data-mobile-nav-style="full-screen-menu" data-mobile-nav-bg-color="#252840">
    @include('frontend.elements.header')
    @yield('content')
    @include('frontend.elements.footer')
    @include('frontend.elements.commonjs')
  </body>
</html>