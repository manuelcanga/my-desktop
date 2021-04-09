<!DOCTYPE html>
<html lang="{{lang}}">
<head>
    <title>{'Note'}</title>
    <meta name="robots" content="noindex, nofollow"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta charset="UTF-8"/>
    <base href="{{site_url}}">
    <style>
        body, html { width: 100% }

        body, html, textarea {
            background-color: {{bgcolor}};
            margin: 0;
            padding: 0;
            height: 100%;
            color: #060606;
            font-size: 14px;
            border: 0
        }

        textarea {
            resize: none;
            padding: 12px;
            width: 100%;
            box-sizing: border-box
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var note = document.getElementById("note");
            var interval = null;

            function send() {
                var data = new FormData();
                data.append('text', note.value);
                data.append('my_desktop_widget', "{{my_desktop_widget}}");
                data.append('my_desktop_token', "{{my_desktop_token}}");
                data.append('url', '/widget/note-save');
                data.append('action', "{{plugin_name}}");

                var xhr = new XMLHttpRequest();
                xhr.open('POST', "{{ajax_url}}");
                xhr.send(data);
            }

            function waiting() {
                send();
                interval = clearInterval(interval);
            }

            note.onfocus = function (e) {
                if (interval) return;

                interval = setInterval(send, {{saving_time}});
                send();
            }

            note.onclick = function (e) {
                if (interval) return;

                interval = setInterval(send, {{saving_time}} );
                send();
            }


            /* El textarea perdio el foco */
            note.onblur = function (e) {
                if (!interval) return;
                send();
                interval = clearInterval(interval);
            }


            document.onmouseout = function (e) {
                send();

                /* Salió del widget, toca guardar */
                if (e.toElement == null && e.relatedTarget == null && interval) {
                    interval = setTimeout(function(){ waiting(); },{{saving_time}} / 2 );
                }
            }
        });
    </script>
</head>
<body>
<textarea id="note">{{text}}</textarea>
</body>
</html>
