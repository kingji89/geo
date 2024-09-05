(function($) {
    'use strict';

    $(function() {
        console.log('Geo Clock Admin JS loaded');

        var $locationsTable = $('#geo-clock-locations');
        var $addLocationButton = $('#add-location');
        var locationRowTemplate = $('#location-row-template').html();

        $addLocationButton.on('click', function() {
            var newIndex = $locationsTable.find('tbody tr').length;
            var newRow = locationRowTemplate.replace(/{index}/g, newIndex);
            $locationsTable.find('tbody').append(newRow);
        });

        $locationsTable.on('click', '.remove-location', function() {
            $(this).closest('tr').remove();
        });

        $('.update-log').on('click', function() {
            var $row = $(this).closest('tr');
            var logId = $row.data('log-id');
            var clockIn = $row.find('.clock-in').val();
            var clockOut = $row.find('.clock-out').val();

            console.log('Updating log:', logId, clockIn, clockOut);

            $.ajax({
                url: geo_clock_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'update_employee_log',
                    nonce: geo_clock_admin.nonce,
                    log_id: logId,
                    clock_in: clockIn,
                    clock_out: clockOut
                },
                success: function(response) {
                    console.log('Update response:', response);
                    if (response.success) {
                        alert('Log updated successfully');
                    } else {
                        alert('Failed to update log: ' + response.data);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX error:', textStatus, errorThrown);
                    alert('An error occurred while updating the log: ' + textStatus);
                }
            });
        });

        $('.delete-log').on('click', function() {
            if (confirm('Are you sure you want to delete this log?')) {
                var $row = $(this).closest('tr');
                var logId = $row.data('log-id');

                console.log('Deleting log:', logId);

                $.ajax({
                    url: geo_clock_admin.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'delete_employee_log',
                        nonce: geo_clock_admin.nonce,
                        log_id: logId
                    },
                    success: function(response) {
                        console.log('Delete response:', response);
                        if (response.success) {
                            $row.remove();
                            alert('Log deleted successfully');
                        } else {
                            alert('Failed to delete log: ' + response.data);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('AJAX error:', textStatus, errorThrown);
                        alert('An error occurred while deleting the log: ' + textStatus);
                    }
                });
            }
        });
    });

})(jQuery);