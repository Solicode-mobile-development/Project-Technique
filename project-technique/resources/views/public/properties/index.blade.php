<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real Estate Listings</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .property-card {
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .property-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .price-tag {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-blue-600 shadow">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('public.properties.index') }}">RealEstatePro</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('public.properties.index') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/properties">Admin Panel</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white py-12">
        <div class="container">
            <h1 class="display-4 fw-bold mb-4">Find Your Dream Property</h1>
            <p class="lead mb-4">Browse through our curated selection of premium real estate listings</p>

            <!-- Search Form -->
            <div class="row g-3 bg-white p-4 rounded shadow">
                <div class="col-md-4">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search properties...">
                </div>
                <div class="col-md-3">
                    <select id="cityFilter" class="form-select">
                        <option value="">All Cities</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}">{{ $city }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="categoryFilter" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button id="searchBtn" class="btn btn-primary w-100">Search</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Properties Grid -->
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col">
                <h2 class="fw-bold">Featured Properties</h2>
                <p class="text-muted">{{ $properties->total() }} properties found</p>
            </div>
        </div>

        <div id="propertiesContainer">
            @include('public.properties.partials.properties-grid', ['properties' => $properties])
        </div>

        <!-- Loading Spinner -->
        <div id="loadingSpinner" class="text-center d-none my-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        $(document).ready(function() {
            let currentPage = 1;
            let isLoading = false;

            // Search functionality
            $('#searchBtn').click(function() {
                performSearch();
            });

            $('#searchInput, #cityFilter, #categoryFilter').keypress(function(e) {
                if (e.which == 13) {
                    performSearch();
                }
            });

            // Infinite scroll
            $(window).scroll(function() {
                if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
                    loadMoreProperties();
                }
            });

            function performSearch() {
                const filters = {
                    search: $('#searchInput').val(),
                    city: $('#cityFilter').val(),
                    category_id: $('#categoryFilter').val(),
                    min_price: $('#minPrice').val(),
                    max_price: $('#maxPrice').val()
                };

                $.ajax({
                    url: "{{ route('public.properties.search') }}",
                    method: 'POST',
                    data: filters,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        $('#loadingSpinner').removeClass('d-none');
                    },
                    success: function(response) {
                        $('#propertiesContainer').html(response.html);
                        currentPage = 1;
                    },
                    complete: function() {
                        $('#loadingSpinner').addClass('d-none');
                    }
                });
            }

            function loadMoreProperties() {
                if (isLoading) return;

                isLoading = true;
                currentPage++;

                const filters = {
                    search: $('#searchInput').val(),
                    city: $('#cityFilter').val(),
                    category_id: $('#categoryFilter').val(),
                    page: currentPage
                };

                $.ajax({
                    url: "{{ route('public.properties.search') }}",
                    method: 'POST',
                    data: filters,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        $('#loadingSpinner').removeClass('d-none');
                    },
                    success: function(response) {
                        if (response.html.trim()) {
                            $('#propertiesContainer').append(response.html);
                        }
                    },
                    complete: function() {
                        $('#loadingSpinner').addClass('d-none');
                        isLoading = false;
                    }
                });
            }
        });
    </script>
</body>
</html>
