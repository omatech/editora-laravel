@extends('editora::base')

@section('body')
<main id="main">
	<div id="toolbar" class="container-fluid"></div>
	<div class="status-bar">
		<div class="item-parameters">
			<div class="container">
				<span class="item-tit">
					<span class="section-name">ERROR:</span>
					<h1 class="tit">{{$title}}</h1>
				</span>
			</div>
		</div>
	</div>
</main>
@endsection
