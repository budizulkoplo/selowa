<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selowa - @yield('title')</title>

    <link rel="icon" href="{{ asset('selowa.webp') }}">

    <link rel="stylesheet" href="{!! asset('css/vendor.css') !!}" />
    <link rel="stylesheet" href="{!! asset('css/app.css') !!}" />
    <link rel="stylesheet" href="{{ asset('css/plugins/dataTables/datatables.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/plugins/select2/select2.min.css') }}" />

</head>
<body>

  <!-- Wrapper-->
    <div id="wrapper">

        <!-- Navigation -->
        @include('layouts.navigation')

        <!-- Page wraper -->
        <div id="page-wrapper" class="gray-bg">

            <!-- Page wrapper -->
            @include('layouts.topnavbar')

            <!-- Main view  -->
            @yield('content')

            <!-- Footer -->
            @include('layouts.footer')

        </div>
        <!-- End page wrapper-->

    </div>
    <!-- End wrapper-->

<script src="{!! asset('js/app.js') !!}" type="text/javascript"></script>
<script src="{{ asset('js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
<script src="{{ asset('js/plugins/dataTables/datatables.min.js') }}"></script>
<script src="{{ asset('js/plugins/dataTables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('js/plugins/select2/select2.full.min.js') }}"></script>
<script>
    (function () {
        if (!window.jQuery) {
            return;
        }

        window.jQuery(function ($) {
            if ($.fn && $.fn.metisMenu) {
                $('#side-menu').metisMenu();
            }

            document.addEventListener('click', function (event) {
                if (window.innerWidth > 768 || !document.body.classList.contains('mini-navbar')) {
                    return;
                }

                const sidebar = document.querySelector('.navbar-static-side');
                const toggle = document.querySelector('.navbar-minimalize');

                if (sidebar && !sidebar.contains(event.target) && toggle && !toggle.contains(event.target)) {
                    document.body.classList.remove('mini-navbar');
                }
            });

            if ($.fn.select2) {
                $('select.form-control, select.select2').select2({
                    width: '100%',
                    placeholder: function () {
                        return $(this).find('option:first').text();
                    }
                });
            }

            if ($.fn.DataTable) {
                $('table.table').not('.no-datatable').each(function () {
                    if ($.fn.DataTable.isDataTable(this)) {
                        return;
                    }

                    $(this).DataTable({
                        pageLength: 25,
                        responsive: true,
                        language: {
                            search: 'Cari:',
                            lengthMenu: 'Tampilkan _MENU_ data',
                            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                            infoEmpty: 'Tidak ada data',
                            zeroRecords: 'Data tidak ditemukan',
                            paginate: {
                                first: 'Pertama',
                                last: 'Terakhir',
                                next: 'Berikutnya',
                                previous: 'Sebelumnya'
                            }
                        }
                    });
                });
            }

            const searchInput = document.getElementById('menuSearch');
            const searchResultCount = document.getElementById('searchResultCount');

            if (searchInput) {
                let searchTimeout;
                const menuItems = document.querySelectorAll('#side-menu > li:not(.nav-header):not(.search-bar)');

                const resetMenuSearch = () => {
                    menuItems.forEach((item) => {
                        item.style.display = '';

                        const submenu = item.querySelector('.nav-second-level');
                        const parentLink = item.querySelector('a');

                        if (submenu && submenu.dataset.searchOpened === '1') {
                            submenu.style.display = '';
                            submenu.classList.remove('in');
                            delete submenu.dataset.searchOpened;
                        }

                        if (submenu) {
                            submenu.querySelectorAll('li').forEach((child) => {
                                child.style.display = '';
                            });
                        }

                        if (item.dataset.searchActivated === '1') {
                            item.classList.remove('active');
                            delete item.dataset.searchActivated;
                        }

                        if (parentLink && parentLink.dataset.searchExpanded === '1') {
                            parentLink.setAttribute('aria-expanded', 'false');
                            delete parentLink.dataset.searchExpanded;
                        }
                    });
                };

                searchInput.addEventListener('keyup', function () {
                    clearTimeout(searchTimeout);

                    searchTimeout = setTimeout(() => {
                        const keyword = this.value.toLowerCase().trim();
                        let visibleCount = 0;

                        resetMenuSearch();

                        if (keyword === '') {
                            if (searchResultCount) {
                                searchResultCount.innerText = '';
                            }

                            return;
                        }

                        menuItems.forEach((item) => {
                            const parentLink = item.querySelector('a');
                            const parentText = (parentLink ? parentLink.textContent : item.textContent).toLowerCase();
                            const submenu = item.querySelector('.nav-second-level');
                            const childItems = submenu ? Array.from(submenu.querySelectorAll('li')) : [];
                            const parentMatches = parentText.includes(keyword);
                            let childMatches = false;

                            childItems.forEach((child) => {
                                const matches = child.textContent.toLowerCase().includes(keyword);
                                childMatches = childMatches || matches;
                                child.style.display = parentMatches || matches ? '' : 'none';
                            });

                            if (parentMatches || childMatches) {
                                item.style.display = '';
                                visibleCount++;

                                if (submenu && childItems.length > 0) {
                                    submenu.style.display = 'block';
                                    submenu.classList.add('in');
                                    submenu.dataset.searchOpened = '1';
                                }

                                if (parentLink && submenu && childItems.length > 0) {
                                    item.classList.add('active');
                                    item.dataset.searchActivated = '1';
                                    parentLink.setAttribute('aria-expanded', 'true');
                                    parentLink.dataset.searchExpanded = '1';
                                }
                            } else {
                                item.style.display = 'none';
                            }
                        });

                        if (searchResultCount) {
                            searchResultCount.innerText = visibleCount > 0 ? visibleCount + ' menu ditemukan' : 'Tidak ada';
                        }
                    }, 100);
                });
            }

            const footerClock = document.getElementById('footerClock');

            if (footerClock) {
                const formatter = new Intl.DateTimeFormat('id-ID', {
                    weekday: 'long',
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                });

                const tick = () => {
                    footerClock.innerText = formatter.format(new Date()).replace('pukul ', '');
                };

                tick();
                setInterval(tick, 1000);
            }
        });
    }());
</script>

@section('scripts')
@show

</body>
</html>
