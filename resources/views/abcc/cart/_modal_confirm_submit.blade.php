@section('modals')

@parent

<div class="modal fade" id="modal-confirm-submit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modal-confirm-label"></h4>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <input id="form_id" name="form_id" type="hidden" value="">
                <input id="customer_id" name="customer_id" type="hidden" value="1">
                <input id="customer_id" name="customer_id" type="hidden" value="1">
                <input id="customer_id" name="customer_id" type="hidden" value="1">

                <button type="button" class="btn btn-link" data-dismiss="modal">{{l('Cancel', [], 'layouts')}}</button>
                <button type="button" id="submit-" class="btn btn-success success"><i class="fa fa-thumbs-up"></i> &nbsp; {{ l('Confirm', [], 'layouts' )}}<//button>

                <button type="button" id="submit-ko" class="btn btn-default" data-dismiss="modal">{{l('Continue', [], 'layouts')}}</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts') @parent 

<script type="text/javascript">
    $(document).ready(function () {
        $('body').on('click', '.confirm-', function(evnt) {
            var form_id = $(this).parents('form').attr('id');
            var message_ok = "{{l('You are going to Confirm your Order. Are you sure?')}}";    // $(this).attr('data-content');
            var message_ko = "{{l('You can not Confirm your Order at this moment.')}}";
            var title = $(this).attr('data-title');

            $('#form_id').val(form_id);

            $('#modal-confirm-label').text(title);

            if ( $('#is_billable').val() > 0 )
            {
                    $('#modal-confirm-submit .modal-body').text(message_ok);

                    $('#submit-').show();
                    $('#submit-ko').hide();

            } else {

                    $('#modal-confirm-submit .modal-body').html(message_ko+' '+$("#can_min_order")[0].outerHTML);

                    $('#submit-').hide();
                    $('#submit-ko').show();

            }

            $('#modal-confirm-submit').modal({show: true});
            return false;
        });

        $('#submit-').click(function(){
             /* when the submit button in the modal is clicked, submit the form */
            // alert($('#form_id').val());
            $(this).prop('disabled', true);
            $('#'+$('#form_id').val()).submit();
        });

    });
</script>

{{-- See: https://www.jqueryscript.net/other/Lightweight-jQuery-Confirmation-Modal-For-Bootstrap.html --}}

@endsection
