<li>
	<a class="nav-link" href="{{ route('almacen.entregas') }}">{{ __("Entregas") }}</a>
</li>
<li>
	<a class="nav-link" href="{{ route('almacen.pagos') }}">{{ __("Pagos") }}</a>
</li>
<li>
	<a class="nav-link" href="{{ route('almacen.remisiones') }}">{{ __("Remisiones") }}</a>
</li>
<li>
	<a class="nav-link" href="{{ route('almacen.notas') }}">{{ __("Notas") }}</a>
</li>
<li>
	<a class="nav-link" href="{{ route('almacen.promociones') }}">{{ __("Promociones") }}</a>
</li>
<li>
	<a class="nav-link" href="{{ route('almacen.adeudos') }}">{{ __("Adeudos") }}</a>
</li>
<li>
	<a class="nav-link" href="{{ route('almacen.entradas') }}">{{ __("Entradas") }}</a>
</li>
<li>
	<a class="nav-link" href="{{ route('almacen.libros') }}">{{ __("Libros") }}</a>
</li>
@include('partials.navigations.logged')