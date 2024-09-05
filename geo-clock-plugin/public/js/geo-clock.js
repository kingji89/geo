jQuery(document).ready(function($) {
    var $clockButton = $('#clock-button');
    var $workTimer = $('#work-timer');
    var $dailyTotal = $('#daily-total');
    var startTime;
    var timerInterval;

    function updateTimer() {
        var now = new Date();
        var difference = now - startTime;
        var hours = Math.floor(difference / 3600000);
        var minutes = Math.floor((difference % 3600000) / 60000);
        var seconds = Math.floor((difference % 60000) / 1000);
        $workTimer.text(
            (hours < 10 ? '0' : '') + hours + ':' +
            (minutes < 10 ? '0' : '') + minutes + ':' +
            (seconds < 10 ? '0' : '') + seconds
        );
    }

    function startTimer() {
        startTime = new Date();
        timerInterval = setInterval(updateTimer, 1000);
    }

    function stopTimer() {
        clearInterval(timerInterval);
        $workTimer.text('00:00:00');
    }

    $clockButton.on('click', function() {
        var status = $(this).data('status');
        if (status === 'out') {
            startTimer();
            $(this).data('status', 'in').text('End');
        } else {
            stopTimer();
            $(this).data('status', 'out').text('Start');
        }

        // Existing AJAX call for clock in/out
        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var lat = position.coords.latitude;
                var lng = position.coords.longitude;

                $.ajax({
                    url: geo_clock_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'clock_in_out',
                        nonce: geo_clock_ajax.nonce,
                        lat: lat,
                        lng: lng
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update UI based on clock in/out success
                            console.log(response.data);
                        } else {
                            console.error('Error:', response.data);
                        }
                    },
                    error: function() {
                        console.error('An error occurred. Please try again.');
                    }
                });
            }, function() {
                console.error('Unable to retrieve your location. Please enable location services.');
            });
        } else {
            console.error('Geolocation is not supported by your browser.');
        }
    });
});