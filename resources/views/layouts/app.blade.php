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
    <style>
        .select2-container--default .select2-selection--single {
            height: 34px;
            border: 1px solid #e5e6e7;
            border-radius: 1px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 32px;
            padding-left: 12px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 32px;
        }

        .modal .select2-container {
            width: 100% !important;
        }

        .select2-container--open,
        .select2-dropdown,
        .select2-container--default.select2-container--open {
            z-index: 20600 !important;
        }

        body > .modal {
            z-index: 20550 !important;
        }

        body > .modal .modal-dialog {
            z-index: 20560 !important;
        }

        .modal-backdrop {
            z-index: 20540 !important;
        }

        .selowa-modal-open {
            overflow: hidden;
        }

        .btn {
            border-radius: 4px;
            font-weight: 600;
        }

        .btn-white {
            color: #2f4050;
            background: #ffffff;
            border-color: #cbd5e1;
            box-shadow: 0 1px 2px rgba(15, 23, 42, .08);
        }

        .btn-white:hover,
        .btn-white:focus {
            color: #1f2937;
            background: #f8fafc;
            border-color: #94a3b8;
        }

        .btn-primary {
            background: #1677b9;
            border-color: #12649d;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background: #12649d;
            border-color: #0f5484;
        }

        .btn-info {
            background: #0f9aa8;
            border-color: #0d8390;
        }

        .btn-success {
            background: #1f9d66;
            border-color: #188452;
        }

        .btn-warning {
            color: #ffffff;
            background: #d98b13;
            border-color: #b9750f;
        }

        .btn-warning:hover,
        .btn-warning:focus {
            color: #ffffff;
            background: #b9750f;
            border-color: #965f0c;
        }

        .btn-danger {
            background: #d64545;
            border-color: #bb3535;
        }

        .ibox-title .btn-xs,
        .ibox-tools .btn-xs {
            padding: 4px 9px;
            line-height: 1.4;
        }

        @media (max-width: 768px) {
            body > .select2-container--open {
                left: 12px !important;
                right: 12px !important;
                width: calc(100vw - 24px) !important;
            }

            body > .select2-container--open .select2-dropdown {
                width: 100% !important;
            }

            body > .modal .modal-dialog {
                margin: 10px;
            }
        }
    </style>

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

            $(document).on('show.bs.modal', '.modal', function () {
                const $modal = $(this);

                if (! $modal.parent().is('body')) {
                    $modal.appendTo(document.body);
                }
            });

            $(document).on('hidden.bs.modal', '.modal', function () {
                if ($('.modal.in').length === 0) {
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');
                }
            });

            window.SelowaModal = {
                show(target) {
                    const modal = typeof target === 'string' ? document.querySelector(target) : target;

                    if (!modal) {
                        return;
                    }

                    if (modal.parentNode !== document.body) {
                        document.body.appendChild(modal);
                    }

                    document.querySelectorAll('.modal-backdrop').forEach((backdrop) => backdrop.remove());

                    const backdrop = document.createElement('div');
                    backdrop.className = 'modal-backdrop fade in selowa-backdrop';
                    document.body.appendChild(backdrop);

                    document.body.classList.add('modal-open', 'selowa-modal-open');
                    modal.style.display = 'block';
                    modal.removeAttribute('aria-hidden');
                    modal.setAttribute('aria-modal', 'true');
                    modal.scrollTop = 0;
                    modal.classList.add('in');
                    modal.dataset.selowaOpen = '1';

                    if ($.fn.select2) {
                        $(modal).find('select.form-control, select.select2').each(function () {
                            const $select = $(this);

                            if ($select.data('select2')) {
                                $select.select2('destroy');
                            }

                            $select.select2({
                                width: '100%',
                                dropdownParent: $(document.body),
                                placeholder: function () {
                                    return $(this).find('option:first').text();
                                }
                            });
                        });
                    }

                    modal.querySelectorAll('.select2-container').forEach((container) => {
                        container.style.width = '100%';
                    });
                },
                hide(target) {
                    const modal = typeof target === 'string' ? document.querySelector(target) : target;

                    if (!modal) {
                        return;
                    }

                    modal.classList.remove('in');
                    modal.style.display = 'none';
                    modal.setAttribute('aria-hidden', 'true');
                    modal.removeAttribute('aria-modal');
                    delete modal.dataset.selowaOpen;

                    document.querySelectorAll('.modal-backdrop').forEach((backdrop) => backdrop.remove());

                    if (document.querySelectorAll('.modal.in').length === 0) {
                        document.body.classList.remove('modal-open', 'selowa-modal-open');
                    }
                }
            };

            $(document).on('click', '[data-selowa-modal]', function (event) {
                event.preventDefault();
                window.SelowaModal.show(this.getAttribute('data-selowa-modal'));
            });

            $(document).on('click', '.modal[data-selowa-open="1"] [data-dismiss="modal"]', function (event) {
                event.preventDefault();
                window.SelowaModal.hide($(this).closest('.modal')[0]);
            });

            $(document).on('click', '.modal[data-selowa-open="1"]', function (event) {
                if (event.target === this) {
                    window.SelowaModal.hide(this);
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    const openModal = document.querySelector('.modal[data-selowa-open="1"]');

                    if (openModal) {
                        window.SelowaModal.hide(openModal);
                    }
                }
            });

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
                $('select.form-control, select.select2').each(function () {
                    const $select = $(this);
                    const $modal = $select.closest('.modal');

                    $select.select2({
                        width: '100%',
                        dropdownParent: $modal.length ? $modal : $(document.body),
                        placeholder: function () {
                            return $(this).find('option:first').text();
                        }
                    });
                });
            }

            window.SelowaMoney = {
                digits(value) {
                    return String(value || '').replace(/\D+/g, '');
                },
                format(value) {
                    const digits = this.digits(value);

                    return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                },
                bind(root) {
                    const scope = root || document;

                    scope.querySelectorAll('.money-input').forEach((input) => {
                        if (input.dataset.moneyBound === '1') {
                            return;
                        }

                        input.dataset.moneyBound = '1';
                        input.value = this.format(input.value);
                        input.addEventListener('input', () => {
                            input.value = this.format(input.value);
                        });
                    });

                    scope.querySelectorAll('form').forEach((form) => {
                        if (form.dataset.moneySubmitBound === '1') {
                            return;
                        }

                        form.dataset.moneySubmitBound = '1';
                        form.addEventListener('submit', () => {
                            form.querySelectorAll('.money-input').forEach((input) => {
                                input.value = this.digits(input.value);
                            });
                        });
                    });
                }
            };

            window.SelowaMoney.bind(document);

            if ($.fn.DataTable) {
                $('table.table').not('.no-datatable').each(function () {
                    if ($.fn.DataTable.isDataTable(this)) {
                        return;
                    }

                    $(this).DataTable({
                        pageLength: 25,
                        responsive: true,
                        order: [],
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
