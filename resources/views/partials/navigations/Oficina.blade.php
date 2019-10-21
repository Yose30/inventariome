<li>
	<a class="nav-link" href="{{ route('oficina.remisiones') }}">{{ __("Remisiones") }}</a>
</li>
<li>
	<a class="nav-link" href="{{ route('oficina.remision') }}">{{ __("Crear remision") }}</a>
</li>
<li>
	<a class="nav-link" href="{{ route('oficina.pagos') }}">{{ __("Pagos") }}</a>
</li>
<li>
	<a class="nav-link" href="{{ route('oficina.clientes') }}">{{ __("Clientes") }}</a>
</li>
<li>
	<a class="nav-link" href="{{ route('oficina.libros') }}">{{ __("Libros") }}</a>
</li>
<li>
	<a class="nav-link" href="{{ route('oficina.adeudos') }}">{{ __("Adeudos") }}</a>
</li>
<li>
	<a class="nav-link" href="{{ route('oficina.entradas') }}">{{ __("Entradas") }}</a>
</li>
@include('partials.navigations.logged')