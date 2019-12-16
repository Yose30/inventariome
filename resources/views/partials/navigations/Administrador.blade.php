<li class="nav-item dropdown">
	<a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
		Remisiones <span class="caret"></span>
	</a>
	<div class="dropdown-menu dropdown-menu-left" aria-labelledby="navbarDropdown">
		<a class="dropdown-item" href="{{ route('administrador.remisiones') }}">
			{{ __('Listado de remisiones') }}
		</a>
		<a class="dropdown-item" href="{{ route('administrador.adeudos') }}">
			{{ __('Adeudos') }}
		</a>
	</div>
</li>
<li class="nav-item dropdown">
	<a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
		Libros <span class="caret"></span>
	</a>
	<div class="dropdown-menu dropdown-menu-left" aria-labelledby="navbarDropdown">
		<a class="dropdown-item" href="{{ route('administrador.libros') }}">
			{{ __('Listado de libros') }}
		</a>
		<a class="dropdown-item" href="{{ route('administrador.movimientos') }}">
			{{ __('Movimientos') }}
		</a>
		<a class="dropdown-item" href="{{ route('administrador.vendidos') }}">
			{{ __('Vendidos') }}
		</a>
	</div>
</li>
<li>
	<a class="nav-link" href="{{ route('administrador.entradas') }}">{{ __("Entradas") }}</a>
</li>
<li>
	<a class="nav-link" href="{{ route('administrador.notas') }}">{{ __("Notas") }}</a>
</li>
<li>
	<a class="nav-link" href="{{ route('administrador.pedidos') }}">{{ __("Pedidos") }}</a>
</li>
<li>
	<a class="nav-link" href="{{ route('administrador.promociones') }}">{{ __("Promociones") }}</a>
</li>
<li>
	<a class="nav-link" href="{{ route('administrador.donaciones') }}">{{ __("Donaciones") }}</a>
</li>
<li>
	<a class="nav-link" href="{{ route('administrador.clientes') }}">{{ __("Clientes") }}</a>
</li>
@include('partials.navigations.logged')