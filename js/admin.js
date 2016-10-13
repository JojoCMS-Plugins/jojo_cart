/* Add AJAX behaviour to save quantities automatically */
$(document).ready(function(){
    if ( $('#transaction-report').length>0 ) {
        $('#showtransaction').on('show.bs.modal', function (event) {
            var link = $(event.relatedTarget) // Button that triggered the modal
            var modal = $(this);
            modal.find('#transactiondata').load(link.data('link')  + " #adminTransactionList");
            modal.find('#printlink').attr('href',link.data('link'));
        })
        $('#transactionupdate').on('show.bs.modal', function (event) {
            var link = $(event.relatedTarget) // Button that triggered the modal
            var action = link.data('action');
            var cell = link.closest('td');
            var modal = $(this);
            modal.find('#transactionupdatedata').load(link.data('link')  + " .transactionlist_ajax");
            link.hide();
            if (action=='abandoned') {
                cell.find('.complete').show();
                cell.find('.payment_pending').show();
                cell.find('.status').html('abandoned');
            } else if (action=='payment_pending') {
                cell.find('.complete').show();
                cell.find('.abandoned').show();
                cell.find('.status').html('payment pending');
             } else if (action=='complete') {
                cell.find('.payment_pending').show();
                cell.find('.abandoned').show();
                cell.find('.status').html('complete');
             }
        })
    }
});