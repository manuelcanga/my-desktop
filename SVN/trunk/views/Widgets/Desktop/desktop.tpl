<!DOCTYPE html>
<html lang="{{lang}}">
<head>
	<meta name="robots" content="noindex, nofollow" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta charset="UTF-8" />
	<meta name="author" content="Manuel Canga" />
	<base href="{{ site_url }}">

	<title>{{ site_name }} - My Desktop</title>

	   <script type="text/javascript">
		   var ajaxurl = '{{ajax_url}}';
		   var my_desktop_token = '{{my_desktop_token}}';
	   </script>
	<!-- <link rel="shortcut icon" href="../imgs/favicon.ico" > -->
    {% foreach js_assets as js_file %}
      <script src="{{ js_file }}"></script>
    {% endforeach %}

   {% foreach css_assets as css_file %}
      <link rel="stylesheet" type="text/css" href="{{ css_file }}">
   {% endforeach %}
</head>
<body class="{{ body_class }}" >
<aside id="panel">
	<!--  foreach top_panel_widget_list as widget  -->

	<div id="welcome">
		<strong>{'Welcome to'} <a href="{{ site_url }}">{{ site_name }}</a></strong>
	</div>

	<span id="clock" data-weekdays='{{weekdays}}' data-months='{{months}}'>&nbsp;</span>

	<div id="commands">
		<div id="user">
			<span>{{ current_user_name }}</span>
			<ul class="menu">
				<li><a href="{{my_desktop_url}}/switch">{'Switch to WP Admin'}</a></li>
				<li><a href="{{my_desktop_url}}/restore">{'Restore current desktop'}</a></li>
				<li><a href="{{my_desktop_url}}/logout">{'Logout'}</a></li>
			</ul>
		</div>

		<ul>
			<li id="fullScreen">{'Full Screen: OFF'}</li>
			<li id="editDesktop">{'Mode edition: OFF'}</li>
		</ul>
	</div>
</aside>

<div id="webtop">
       {% foreach webtop_widget_list as widget %}
       <div data-refresh="{{widget.refresh}}" data-widget_id="{{widget.id}}" data-number="{{index}}"
            data-height="{{widget.height}}"
            data-width="{{widget.width}}"
            data-posx="{{widget.posx}}"
            data-posy="{{widget.posy}}"
            data-title="{{widget.title}}" title="{{widget.title}}"
            data-url="{{widget.url | base_url | tokenize_url}}"
            data-image="{{widget.image | image_url}}"
            data-type="{{widget.type}}"
            class="widget widget_{{widget.type}}"
            id="widget_{{widget.id}}">

           {$ widget }
           {$ widget/{{widget.type}} }
       </div>
       {% endforeach %}
</div>

<aside id="taskbar">
	<!--  foreach taskbar_panel_widget_list as widget  -->
</aside>

</body>
</html>
