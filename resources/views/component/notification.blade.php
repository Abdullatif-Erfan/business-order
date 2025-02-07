@if(Session::has('message'))
    @php
        $message = Session::get('message');
    @endphp
    <script>
        var placementFrom = 'top'; // top, bottom
        var placementAlign = 'left'; // right, center
        var state = '{{ $message['type'] }}'; // success, warning, danger
        var style = 'withicon'; // plain, withicon
        var content = {};

        content.message = '<span style="font-size:16px;">{{ $message['msg'] }}</span>';
        content.title = '&nbsp;&nbsp;&nbsp;' + ' <span style="font-size:16px;"> پیام </span>';

        if (style == "withicon") {
            content.icon = 'fa fa-bell';
        } else {
            content.icon = 'none';
        }

        content.url = '#';
        content.target = '_blank';

        $.notify(content, {
            type: state,
            placement: {
                from: placementFrom,
                align: placementAlign
            },
            time: 500,
        });
    </script>
@endif
