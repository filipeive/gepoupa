<!-- Sidebar Menu -->
<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Dashboard -->
        <li class="nav-item">
            <a href="{{ route('member.dashboard') }}" class="nav-link">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
            </a>
        </li>

        <!-- Poupanças -->
        <li class="nav-item">
            <a href="{{ route('member.savings') }}" class="nav-link">
                <i class="nav-icon fas fa-piggy-bank"></i>
                <p>Poupanças</p>
            </a>
        </li>

        <!-- Empréstimos -->
        <li class="nav-item">
            <a href="{{ route('member.loans') }}" class="nav-link">
                <i class="nav-icon fas fa-hand-holding-usd"></i>
                <p>Empréstimos</p>
            </a>
        </li>

        <!-- Fundos Sociais -->
        <li class="nav-item">
            <a href="{{ route('member.social-funds') }}" class="nav-link">
                <i class="nav-icon fas fa-hand-holding-heart"></i>
                <p>Fundos Sociais</p>
            </a>
        </li>

        <!-- Perfil -->
        <li class="nav-item">
            <a href="{{ route('member.profile') }}" class="nav-link">
                <i class="nav-icon fas fa-user"></i>
                <p>Perfil</p>
            </a>
        </li>
    </ul>
</nav>