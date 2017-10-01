@extends layouts/default.blade.php

@section('body_content')
  <section class="login">
    {!! $login_form !!}

    <table>
      <tbody>
    @foreach( $guards AS $guard)
      <tr>
	<td>@if( !empty($guard->getLogo()) )
	  <img src="/{{ $guard->getLogo() }}" alt="{{ $guard->getName() }}"/>
	@endif</td>
	<td><a href="/login/{{ $guard->getName() }}">{{ $guard->getName() }}</a></td>
      </tr>
    @endforeach
      </tbody>
    </table>
  </section>
@endsection