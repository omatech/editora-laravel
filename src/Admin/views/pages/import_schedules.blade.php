@extends('editora::base')

@section('body')
    <main id="main">
        <div id="toolbar" class="container-fluid"></div>
        <section class="table-view-header">
            <div class="container">
				<span class="data">
					<h3 class="tit">{{getMessage('import_schedules')}}</h3>
				</span>
            </div>
        </section>

        <section>
            <div class="container">
                <form class="form" method="post" ENCTYPE="multipart/form-data" action="{{route('editora.action', 'import_schedules_action/')}}">
                    {{csrf_field()}}
                    <div class="form-group">
                        <label for="file_schedules">CSV</label>
                        <input class="form-control mt-3" id="file_schedules" type="file" name="file_schedules" required accept=".csv">
                    </div>
                    <input type="submit" value="Enviar" style="font-size:14px" class="btn clr-secondary">
                </form>
            </div>
        </section>

        @if(isset($message))
        <section>
            <div class="container">
                <br><br>
                <p>{!! $message !!}</p>
                <br>
                <p class="alert alert-success">{{getMessage('inserted_elements')}}: {!! $count_rows !!}</p>
                <br><br>
            </div>
        </section>
        @endif

    </main>
@endsection
