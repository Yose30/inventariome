<li>
	<a class="nav-link" href="{{ route('oficina.remisiones') }}">{{ __("Remisiones") }}</a>
</li>
<li>
	<a class="nav-link" href="{{ route('oficina.pagos') }}">{{ __("Pagos") }}</a>
</li>
<li>
	<a class="nav-link" href="{{ route('oficina.adeudos') }}">{{ __("Adeudos") }}</a>
</li>
<li>
	<a class="nav-link" href="{{ route('oficina.pedidos') }}">{{ __("Pedidos") }}</a>
</li>
<li>
	<a class="nav-link" href="{{ route('oficina.donaciones') }}">{{ __("Donaciones") }}</a>
</li>
<li>
	<a class="nav-link" href="{{ route('oficina.entradas') }}">{{ __("Entradas") }}</a>
</li>
<li>
	<a class="nav-link" href="{{ route('oficina.libros') }}">{{ __("Libros") }}</a>
</li>
<li>
	<a class="nav-link" href="{{ route('oficina.clientes') }}">{{ __("Clientes") }}</a>
</li>
@include('partials.navigations.logged')