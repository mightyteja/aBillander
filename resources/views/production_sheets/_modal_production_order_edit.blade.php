@section('modals')

@parent

<div class="modal fade" id="ProductionOrderEdit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="ProductionOrderEditLabel">{{ l('Edit Production Order') }}: <span id="production_order_id_edit"></span></h4>
            </div>
                {!! Form::open(array('id' => 'ProductionOrderEdit_action')) !!}
                {{-- !! Form::hidden('_method', 'DELETE') !! --}}
            <div class="modal-body">

        <div class="row">

                {!! Form::hidden('current_production_sheet_id', $sheet->id) !!}
                <input type="hidden" name="due_date"            id="due_date"            value="{{ $sheet->due_date }}">

                  <div class="form-group col-lg-3 col-md-3 col-sm-3 {{ $errors->has('planned_quantity') ? 'has-error' : '' }}">
                     {{ l('Quantity') }}
                     {!! Form::text('planned_quantity', null, array('class' => 'form-control', 'id' => 'planned_quantity_edit')) !!}
                     {!! $errors->first('planned_quantity', '<span class="help-block">:message</span>') !!}
                  </div>

                 <div class="form-group col-lg-5 col-md-5 col-sm-5 {{ $errors->has('work_center_id') ? 'has-error' : '' }}">
                    {{ l('Work Center') }}
                    {!! Form::select('work_center_id', ['0' => l('-- Please, select --', [], 'layouts')] + $work_centerList, null, array('class' => 'form-control', 'id' => 'work_center_id_edit')) !!}
                    {!! $errors->first('work_center_id', '<span class="help-block">:message</span>') !!}
                 </div>
        </div>

        <div class="row">
                  <div class="form-group col-lg-12 col-md-12 col-sm-12 {{ $errors->has('notes') ? 'has-error' : '' }}">
                     {{ l('Notes', [], 'layouts') }}
                     {!! Form::textarea('notes', null, array('class' => 'form-control', 'id' => 'notes_edit', 'rows' => '3')) !!}
                     {!! $errors->first('notes', '<span class="help-block">:message</span>') !!}
                  </div>
        </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-dismiss="modal">{{l('Cancel', [], 'layouts')}}</button>
                {!! Form::submit(l('Confirm', [], 'layouts'), array('class' => 'btn btn-success')) !!}
            </div>
                {!! Form::close() !!}
        </div>
    </div>
</div>

@stop
@section('scripts') @parent 

<script type="text/javascript">
    $(document).ready(function () {
        $('body').on('click', '.edit-production-order', function(evnt) { 
 //       $('.delete-item').click(function (evnt) {
              var id = $(this).attr('data-oid');
              var reference = $(this).attr('data-oreference');
              var name = $(this).attr('data-oname');
            var href = $(this).attr('href');
            var quantity = $(this).attr('data-oquantity');
            var workcenter = $(this).attr('data-oworkcenter');
            var notes = $(this).attr('data-onotes');

            var label = '';

            label = id+' :: ['+reference+'] '+name;

              $('#production_order_id_edit').text(label);

              $('#planned_quantity_edit').val(quantity);
              $('#work_center_id_edit').val(workcenter);
              $('#notes_edit').val(notes);

            $('#ProductionOrderEdit_action').attr('action', href);
            $('#ProductionOrderEdit').modal({show: true});
            return false;
        });
    });
</script>

@stop