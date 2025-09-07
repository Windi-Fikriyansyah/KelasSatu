<ul class="menu-inner py-1">
    <!-- Dashboard -->
    <li class="menu-item active">
        <a href="{{ route('owner.dashboard') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-home-circle"></i>
            <div data-i18n="Analytics">Dashboard</div>
        </a>
    </li>

    <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Data Master</span>
    </li>
    <li class="menu-item">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-collection"></i>
            <div data-i18n="Account Settings">Data Master</div>
        </a>
        <ul class="menu-sub">
            <li class="menu-item">
                <a href="{{ route('admin.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-id-card"></i>
                    <div data-i18n="Notifications">Data Admin</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('pengguna.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-user"></i>
                    <div data-i18n="Notifications">Data User</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('kategori.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-category"></i>
                    <div data-i18n="Notifications">Data Kategori</div>
                </a>
            </li>
        </ul>
    </li>

    <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Manajemen Kursus</span>
    </li>
    <li class="menu-item">
        <a href="{{ route('kursus.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-book"></i>
            <div data-i18n="Account Settings">Data Kursus</div>
        </a>
    </li>

    <li class="menu-item">
        <a href="{{ route('materi.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-book-open"></i>
            <div data-i18n="Account Settings">Data Materi</div>
        </a>
    </li>

    <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Manajemen Quiz</span>
    </li>
    <li class="menu-item">
        <a href="{{ route('latihan.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-edit-alt"></i>
            <div data-i18n="Account Settings">Data Latihan</div>
        </a>
    </li>

    <li class="menu-item">
        <a href="{{ route('tryout.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-task"></i>
            <div data-i18n="Account Settings">Data Tryout</div>
        </a>
    </li>


    <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Laporan</span>
    </li>
    <li class="menu-item">
        <a href="{{ route('withdraw.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-book"></i>
            <div data-i18n="Account Settings">Laporan Withdrawl</div>
        </a>
    </li>


</ul>
