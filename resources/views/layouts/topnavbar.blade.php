<div class="row border-bottom">
    <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary" href="#">
                <i class="fa fa-bars"></i>
            </a>
        </div>

        <ul class="nav navbar-top-links navbar-right">
            <li style="padding: 16px 20px 0 0; color: #676a6c;">
                <i class="fa fa-user-circle"></i> {{ auth()->user()?->name ?? 'User' }}
            </li>
            <li>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-link" style="padding: 15px 20px; color: #676a6c;">
                        <i class="fa fa-sign-out"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </nav>
</div>
