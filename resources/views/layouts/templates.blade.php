<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/YOUR_KIT_CODE.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background: #ffffff;
            color: rgb(124, 124, 124);
            padding: 20px;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
        }

        .sidebar h4 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .nav-link {
            color: rgb(124, 124, 124);
            font-weight: bold;
            padding: 12px;
            border-radius: 8px;
            transition: background 0.3s, color 0.3s;
        }

        .nav-link:hover,
        .nav-link.active {
            background: #007bff;
            color: #ffffff !important;
        }

        .nav-link i {
            margin-right: 10px;
        }

        .content {
            margin-left: 270px;
            padding: 20px;
        }

        .navbar {
            background: #ffffff;
            padding: 10px 20px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .profile-dropdown {
            position: relative;
            display: inline-block;
        }

        .profile-dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #ffffff;
            min-width: 160px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            z-index: 1000;
        }

        .profile-dropdown:hover .profile-dropdown-content {
            display: block;
        }

        .profile-dropdown-content a {
            color: black;
            padding: 10px;
            text-decoration: none;
            display: block;
            border-radius: 5px;
        }

        .profile-dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .profile-icon {
            background-color: orange;
            color: white;
            border-radius: 50%;
            padding: 10px;
            cursor: pointer;
        }

        .search-trigger {
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Modal Search */
        .search-modal {
            position: fixed;
            top: -100px;
            left: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            padding: 15px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1050;
            transition: top 0.3s ease-in-out;
        }

        .search-modal input {
            width: 80%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
        }

        .search-modal .close-search {
            cursor: pointer;
            font-size: 18px;
            color: red;
            padding: 10px;
            border: none;
            background: none;
        }

        .dashboard-box {
            border: 2px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            margin: 20px;
            background-color: #f9f9f9;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <h4>FlexyLite</h4>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-box"></i> Produk
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('orders.index') }}" class="nav-link {{ request()->routeIs('orders.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-cart-shopping"></i> Pembelian
                </a>
            </li>  
            @if(Auth::user()->role === 'admin')         
            <li class="nav-item">
                <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-user"></i> User
                </a>
            </li>
            @endif
        </ul>
    </div>
    

    <div class="content">
        <nav class="navbar d-flex justify-content-between">
            <div class="search-trigger" onclick="toggleSearch()">
                <i class="fa fa-search"></i> <span>Search</span>
            </div>
            <div class="profile-dropdown">
                <div class="profile-icon">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="profile-dropdown-content">
                    <a href="#"><i class="fa fa-user"></i> Administrator</a>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fa fa-sign-out-alt"></i> Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </nav>

        @yield('content')
    </div>

    <!-- Modal Search -->
    <div id="searchModal" class="search-modal">
        <button class="close-search" onclick="toggleSearch()">X</button>
        <input type="text" id="searchInput" placeholder="Cari...">
    </div>

</body>

<script>
    function toggleSearch() {
        let searchModal = document.getElementById("searchModal");
        let searchInput = document.getElementById("searchInput");

        if (searchModal.style.top === "0px") {
            searchModal.style.top = "-100px";
        } else {
            searchModal.style.top = "0px";
            searchInput.focus();
        }
    }

    document.addEventListener("keydown", function(event) {
        if (event.key === "Escape") {
            toggleSearch();
        }
    });
</script>

</html>
