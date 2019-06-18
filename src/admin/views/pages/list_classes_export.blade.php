@extends('editora::base')

@section('body')
    <main id="main">
        <div id="toolbar" class="container-fluid">
            {{--<span class="toolbar-left">
            </span>
			<span class="toolbar-left">
				<div class="toolbar-right">
                    <div class="save-block">
                        <a href="/admin/list_class_import" class="btn clr-secondary"><span class="btn-text">Importar Excel</span></a>
                    </div>
                </div>
			</span>--}}
        </div>
        <section class="table-view-header">
            <div class="container">
				<span class="data">
					<h3 class="tit">Listado clases para exportar</h3>
				</span>

                <form action="" class="form">
                    <div class="form-group">
						<span class="input-group">
							<input id="search_instances" type="text" class="form-control" placeholder="Buscar">
							<span class="input-addon">
								<i class="icon-magnify"></i>
							</span>
						</span>
                    </div>
                </form>
            </div>
        </section>

        <section class="">
            <div class="container">
                <form class="form" method="post" ENCTYPE="multipart/form-data" action="/admin/list_class_import">
                    {{csrf_field()}}
                    <input type="file" name="file_class">
                    <input type="submit" value="Enviar">
                </form>
            </div>
        </section>

        @if(isset($message))
        <section class="">
            <div class="container">
                <br><br>
                <p>{!! $message !!}</p>
                <br>
                <p>{!! $count_rows !!} elements insertats</p>
                <br><br>
            </div>
        </section>
        @endif

        @if(isset($classes))
        <section class="table-view">
            <div class="container">
                <table class="table" id="classes-table">
                    <thead>
                    <tr>
                        <th class="tit"><span>ID</span></th>
                        <th class="tit"><span>Nom</span></th>
                        <th class="favorite">
                            {{--<span class="hidden">--}}
                                Exportar
                            {{--</span>--}}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($classes as $item)
                            <tr class="published favorited">
                                <td class="tit">{{$item['class_id']}}</td>
                                <td class="tit">{{$item['name']}}</td>
                                <td class="favorite">
                        			<span>
                        			    <a href="/admin/list_class_export?id={{$item['class_id'].'&name='.$item['name']}}" class="btn-square clr-default">
                                            <i class="icon-pencil"></i>
                                        </a>
                        			</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @section('scripts')
                    @parent
                    <script type="text/javascript">
                        $(document).ready(function() {
                            oTable = $('#classes-table').DataTable({
                                ordering: false,
                                paging: false,
                                searching:true,
                                sDom:"ltipr"
                            });

                            $('#search_instances').keyup(function(){
                                oTable.search($(this).val()).draw() ;
                            })
                        } );
                    </script>
                @endsection

            </div>
        </section>
        @endif


    </main>
@endsection
