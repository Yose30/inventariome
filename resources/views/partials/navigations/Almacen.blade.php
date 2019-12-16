<li class="nav-item dropdown">
	<a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
		Remisiones <span class="caret"></span>
	</a>
	<div class="dropdown-menu dropdown-menu-left" aria-labelledby="navbarDropdown">
		<a class="dropdown-item" href="{{ route('almacen.entregas') }}">
			{{ __('Marcar entrega') }}
		</a>
		<a class="dropdown-item" href="{{ route('almacen.pagos') }}">
			{{ __('Pago / Devolución') }}
		</a>
		<a class="dropdown-item" href="{{ route('almacen.remisiones') }}">
			{{ __('Listado de remisiones') }}
		</a>
		<a class="dropdown-item" href="{{ route('almacen.adeudos') }}">
			{{ __("Adeudos") }}
		</a>
	</div>
</li>
<li>
	<a class="nav-link" href="{{ route('almacen.notas') }}">{{ __("Notas") }}</a>
</li>
<li>
	<a class="nav-link" href="{{ route('almacen.pedidos') }}">{{ __("Pedidos") }}</a>
</li>
<li>
	<a class="nav-link" href="{{ route('almacen.promociones') }}">{{ __("Promociones") }}</a>
</li>
<li>
	<a class="nav-link" href="{{ route('almacen.donaciones') }}">{{ __("Donaciones") }}</a>
</li>
<li>
	<a class="nav-link" href="{{ route('almacen.entradas') }}">{{ __("Entradas") }}</a>
</li>
<li>
	<a class="nav-link" href="{{ route('almacen.libros') }}">{{ __("Libros") }}</a>
</li>
@include('partials.navigations.logged')