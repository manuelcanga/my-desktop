(function () {
    var konqueror_url = '<?= $konqueror_url ?>';
    var active_menu = '<?= $active_menu ?>';

    redirect_main_links_to_konqueror = function () {
        var konqueror_menu = document.getElementById('konqueror-menu');
        konqueror_menu.classList.add('depth_1');

        /** Create konqueror from adminemnu */
        konqueror_menu.innerHTML = document.getElementById('adminmenu').innerHTML;
        /** Remove old menu */
        document.getElementById('adminmenuwrap').innerHTML = '';

        /** Add dashboard submanu as main menu elements */
        konqueror_menu.innerHTML = get_dashboard_links() + konqueror_menu.innerHTML;

        var link_list = document.querySelectorAll('#konqueror-menu > li > a');
        var link_count = link_list.length;

        for (var link_i = 0; link_i < link_count; link_i++) {
            var parent = link_list[link_i].parentElement;
            var parent_id = parent.getAttribute('id');
            var has_submenu = parent.getElementsByTagName('ul').length > 0

            if (has_submenu) {
                link_list[link_i].href = konqueror_url + '&active=' + parent_id;
            }

            /* Add default icon as pitfall */
            var current_image = parent.getElementsByClassName('wp-menu-image');

            if (current_image.length > 0 && need_default_image(current_image[0])) {
                current_image[0].className = "dashicons-admin-generic " + current_image[0].className;
            }
        }

        konqueror_menu.classList.add('active');
    };

    get_dashboard_links = function () {
        var dashboard_links = document.querySelector('#menu-dashboard ul.wp-submenu');

        if (null === dashboard_links) {
            return '';
        }

        var submenu_list = document.querySelectorAll('#menu-dashboard ul.wp-submenu li:not(.wp-first-item)');

        var submenu_html = '';
        for (var link_i = 0, total_items = submenu_list.length; link_i < total_items; link_i++) {
            var submenu = submenu_list[link_i];

            /* remove dashboard label */
            if (0 === link_i) {
                continue;
            }

            var link = submenu.children[0];

            link.innerHTML = create_main_link_element(link.innerHTML, href2icon(link.href));

            submenu_html += submenu.outerHTML;
        }

        return submenu_html;
    }

    create_main_link_element = function (title, image_class = '') {
        return '<div class="wp-menu-image dashicons-before ' + image_class + '" aria-hidden="true">' +
            '<br>' +
            '</div>' +
            '<div class="wp-menu-name">' + title + '</div>';
    }

    need_default_image = function (current_image) {
        return !current_image.style.backgroundImage && -1 === current_image.className.indexOf('dashicons-admin-');
    }

    /**
     * Use script name of href as image class. When ref is a page then concat page param to script name.
     * @param href
     * @returns {string}
     */
    href2icon = function (href) {
        var link = href.split('?')[0];
        /* extract script name in order to can use it as a class */
        var class_name = link.substr(link.lastIndexOf('/') + 1);
        var icon_class = 'custom-dashicons-' + class_name.replace('.php', '').replace('.', '-');

        return icon_class + hrf2icon_extra(href, icon_class);
    };

    /**
     * Retrieve more specific icon for href link using as base base_class string.
     *
     * @param href
     * @param base_class
     *
     * @returns {string}
     */
    hrf2icon_extra = function (href, base_class) {
        var allowed_params = ['page', 'taxonomy', 'post_type'];

        for (var i = 0; i < allowed_params.length; i++) {
            var param = '?' + allowed_params[[i]] + "=";
            var index_of_param = href.indexOf(param);

            if (-1 === index_of_param) {
                continue;
            }

            var max_index = (-1 !== href.indexOf('&')) ? href.indexOf('&') : href.length;

            var extra_class = " " + base_class + "-" + href.substr(index_of_param + param.length, max_index);

            return extra_class.replace('_', '-').replace('#', '-').replace('/', '');
        }

        return '';
    }

    generate_breadcrumb = function (current_page_title = '') {
        var breadcrumb = '<div class="konqueror-breadcrumb">';
        breadcrumb += '<a href="' + konqueror_url + '">My CMS</a>';

        if ('' !== current_page_title) {
            breadcrumb += '&nbsp;&gt;&nbsp;';
            breadcrumb += '<span>' + current_page_title + '</span>';
        }
        breadcrumb += '</div>';

        return breadcrumb;
    };

    show_submenu_active = function () {
        var current_element = document.querySelector('#' + active_menu + " ul");
        var current_page_info = current_element.getElementsByClassName('wp-submenu-head')[0];

        /** remove notifications. E.g: comment count */
        if (current_page_info.getElementsByTagName('span').length > 0) {
            current_page_info.getElementsByTagName('span')[0].remove();
        }

        var page_title = current_page_info.textContent;

        /** Add useful class */
        var konqueror_menu = document.getElementById('konqueror-menu');
        konqueror_menu.classList.add('depth_2');
        konqueror_menu.classList.add('depth_2_' + active_menu);
        konqueror_menu.innerHTML = generate_breadcrumb(page_title) + current_element.innerHTML;

        /** Add links */
        var link_list = document.querySelectorAll('#konqueror-menu > li > a');
        var link_count = link_list.length;

        for (var link_i = 0; link_i < link_count; link_i++) {
            var parent = link_list[link_i].parentElement;
            var parent_id = parent.getAttribute('id');
            var classes = "wp-menu-image dashicons-before dashicons-admin-generic " + href2icon(link_list[link_i].href);
            var menu_name = '<div class="wp-menu-name">' + link_list[link_i].innerHTML + '</div>'

            link_list[link_i].innerHTML = '<div class="' + classes + '" aria-hidden="true"><br></div>' + menu_name;
        }


        konqueror_menu.classList.add('active');
    }

    document.addEventListener('DOMContentLoaded', function () {
        if ('' === active_menu) {
            return redirect_main_links_to_konqueror();
        }

        return show_submenu_active();
    });
})();