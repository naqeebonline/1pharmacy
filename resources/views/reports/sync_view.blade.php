<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
</head>
<body>
<div class="row">
    <div class="col-12">


        {{-- LISTIN PATIENTS --}}
        <div class="card my-5">
            <h3>Syncing Data.........</h3>
        </div>
        <!-- /traffic sources -->
    </div>
</div>
<script>
     i = 0;

    setInterval(function () {
        i++;
        $('#screen-blocker').show();
        $('#loading-icon').show();
        if(i== 5){
            window.location.reload();
        }
        load_sync();

    }, 120000);//300000

    load_sync();

    function load_sync() {
        $.ajax({
            type: 'POST',
            url: "{{ route('pos.syncDataLive') }}",
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(res) {
                $('#screen-blocker').hide();
                $('#loading-icon').hide();
            },
            error: function(xhr, status, error) {
                $('#screen-blocker').hide();
                $('#loading-icon').hide();
                console.error("Error:", error);
            },
            complete: function() {
                // Runs on both success and error
                $('#screen-blocker').hide();
                $('#loading-icon').hide();
            }
        });
    }
</script>
</body>
</html>








