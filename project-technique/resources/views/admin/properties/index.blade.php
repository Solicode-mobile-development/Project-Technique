<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Property Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .stat-card {
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-3px);
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar min-vh-100">
                <div class="position-sticky pt-3">
                    <h4 class="text-white px-3 mb-4">RealEstate Admin</h4>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-white active" href="#">
                                <i class="fas fa-home me-2"></i> Properties
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#" data-bs-toggle="modal" data-bs-target="#importModal">
                                <i class="fas fa-file-import me-2"></i> Import CSV
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h2 fw-bold">Property Management</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#propertyModal">
                        <i class="fas fa-plus me-2"></i> Add New Property
                    </button>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card stat-card border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title text-muted">Total Properties</h5>
                                <h2 class="fw-bold text-primary">{{ $stats['total'] }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title text-muted">Avg Price</h5>
                                <h2 class="fw-bold text-success">${{ number_format($stats['average_price'], 2) }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title text-muted">Top Cities</h5>
                                @foreach($stats['cities'] as $city)
                                    <span class="badge bg-info me-2 mb-2">
                                        {{ $city->city }} ({{ $city->count }})
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Properties Table -->
                <div class="card border-0 shadow">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Image</th>
                                        <th>Title</th>
                                        <th>Price</th>
                                        <th>City</th>
                                        <th>Category</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="propertiesTable">
                                    @foreach($properties as $property)
                                        <tr id="property-{{ $property->id }}">
                                            <td>{{ $property->id }}</td>
                                            <td>
                                                @if($property->image_path)
                                                    <img src="{{ asset('storage/' . $property->image_path) }}"
                                                         alt="{{ $property->title }}"
                                                         width="50" height="50"
                                                         style="object-fit: cover; border-radius: 5px;">
                                                @else
                                                    <div class="bg-secondary text-white rounded d-flex align-items-center justify-content-center"
                                                         style="width: 50px; height: 50px;">
                                                        <i class="fas fa-home"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ Str::limit($property->title, 40) }}</td>
                                            <td class="fw-bold text-success">${{ number_format($property->price) }}</td>
                                            <td>{{ $property->city }}</td>
                                            <td>
                                                <span class="badge bg-primary">{{ $property->category->name }}</span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary me-2"
                                                        onclick="editProperty({{ $property->id }})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger"
                                                        onclick="deleteProperty({{ $property->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $properties->links() }}
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Property Modal -->
    <x-modal id="propertyModal" title="Property Details" size="lg">
        <form id="propertyForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="propertyId" name="id">

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="title" class="form-label">Property Title *</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="price" class="form-label">Price ($) *</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="city" class="form-label">City *</label>
                        <input type="text" class="form-control" id="city" name="city" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category *</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description *</label>
                <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Property Image</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                <small class="text-muted">Max file size: 2MB. Allowed formats: JPEG, PNG, JPG, GIF</small>
                <div id="imagePreview" class="mt-2"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="saveBtn">
                    <span id="saveBtnText">Save Property</span>
                    <span id="saveBtnSpinner" class="spinner-border spinner-border-sm d-none"></span>
                </button>
            </div>
        </form>
    </x-modal>

    <!-- Import CSV Modal -->
    <x-modal id="importModal" title="Import Properties from CSV">
        <form id="importForm" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="csv_file" class="form-label">CSV File *</label>
                <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required>
                <small class="text-muted">
                    Required columns: title, description, price, city, category<br>
                    <a href="#" onclick="downloadSampleCsv()">Download sample CSV</a>
                </small>
            </div>

            <div class="alert alert-info">
                <h6>CSV Format Requirements:</h6>
                <ul class="mb-0">
                    <li>First row must contain headers</li>
                    <li>Price should be numeric (without currency symbols)</li>
                    <li>Category will be created if it doesn't exist</li>
                </ul>
            </div>

            <div id="importResults" class="d-none">
                <div class="alert alert-success" id="successAlert">
                    <strong>Success:</strong> <span id="successCount"></span> properties imported successfully!
                </div>
                <div class="alert alert-danger" id="errorAlert">
                    <strong>Errors:</strong> <span id="errorCount"></span> properties failed to import.
                    <ul id="errorList"></ul>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="importBtn">
                    Import CSV
                    <span id="importBtnSpinner" class="spinner-border spinner-border-sm d-none"></span>
                </button>
            </div>
        </form>
    </x-modal>

    <!-- JavaScript -->
    <script>
        let currentPropertyId = null;

        // Handle property form submission
        $('#propertyForm').submit(function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const url = currentPropertyId
                ? `/admin/properties/${currentPropertyId}`
                : '/admin/properties';
            const method = currentPropertyId ? 'PUT' : 'POST';

            $('#saveBtn').prop('disabled', true);
            $('#saveBtnText').text(currentPropertyId ? 'Updating...' : 'Saving...');
            $('#saveBtnSpinner').removeClass('d-none');

            $.ajax({
                url: url,
                method: method,
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        // Close modal
                        $('#propertyModal').modal('hide');

                        // Show success message
                        showAlert('success', response.message);

                        // Refresh table or update specific row
                        if (currentPropertyId) {
                            updatePropertyRow(response.property);
                        } else {
                            addPropertyRow(response.property);
                        }

                        // Reset form
                        resetPropertyForm();
                    }
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON.errors;
                    let errorMessage = 'An error occurred. Please try again.';

                    if (errors) {
                        errorMessage = Object.values(errors).flat().join('<br>');
                    }

                    showAlert('danger', errorMessage);
                },
                complete: function() {
                    $('#saveBtn').prop('disabled', false);
                    $('#saveBtnText').text(currentPropertyId ? 'Update Property' : 'Save Property');
                    $('#saveBtnSpinner').addClass('d-none');
                }
            });
        });

        // Edit property
        function editProperty(id) {
            currentPropertyId = id;

            $.ajax({
                url: `/admin/properties/${id}`,
                method: 'GET',
                success: function(response) {
                    const property = response.property;

                    $('#propertyId').val(property.id);
                    $('#title').val(property.title);
                    $('#description').val(property.description);
                    $('#price').val(property.price);
                    $('#city').val(property.city);
                    $('#category_id').val(property.category_id);

                    // Show image preview if exists
                    if (property.image_path) {
                        $('#imagePreview').html(`
                            <img src="/storage/${property.image_path}"
                                 alt="${property.title}"
                                 class="img-thumbnail"
                                 style="max-height: 150px;">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="removeImage" name="remove_image">
                                <label class="form-check-label" for="removeImage">
                                    Remove current image
                                </label>
                            </div>
                        `);
                    } else {
                        $('#imagePreview').html('');
                    }

                    $('#propertyModal').modal('show');
                }
            });
        }

        // Delete property
        function deleteProperty(id) {
            if (confirm('Are you sure you want to delete this property?')) {
                $.ajax({
                    url: `/admin/properties/${id}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            $(`#property-${id}`).remove();
                            showAlert('success', response.message);
                        }
                    },
                    error: function() {
                        showAlert('danger', 'Failed to delete property. Please try again.');
                    }
                });
            }
        }

        // Handle CSV import
        $('#importForm').submit(function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            $('#importBtn').prop('disabled', true);
            $('#importBtnSpinner').removeClass('d-none');
            $('#importResults').addClass('d-none');

            $.ajax({
                url: '/admin/properties/import-csv',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        const results = response.results;

                        $('#successCount').text(results.success);
                        $('#errorCount').text(results.failed);

                        const errorList = $('#errorList');
                        errorList.empty();

                        if (results.errors.length > 0) {
                            results.errors.forEach(error => {
                                errorList.append(`<li>${error}</li>`);
                            });
                            $('#errorAlert').removeClass('d-none');
                        } else {
                            $('#errorAlert').addClass('d-none');
                        }

                        $('#importResults').removeClass('d-none');

                        if (results.success > 0) {
                            // Reload page to show new properties
                            setTimeout(() => location.reload(), 2000);
                        }
                    }
                },
                error: function(xhr) {
                    const error = xhr.responseJSON.message || 'Import failed. Please try again.';
                    showAlert('danger', error);
                },
                complete: function() {
                    $('#importBtn').prop('disabled', false);
                    $('#importBtnSpinner').addClass('d-none');
                }
            });
        });

        // Utility functions
        function addPropertyRow(property) {
            const row = `
                <tr id="property-${property.id}">
                    <td>${property.id}</td>
                    <td>
                        ${property.image_path ?
                            `<img src="/storage/${property.image_path}" alt="${property.title}" width="50" height="50" style="object-fit: cover; border-radius: 5px;">` :
                            `<div class="bg-secondary text-white rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-home"></i>
                            </div>`
                        }
                    </td>
                    <td>${property.title.substring(0, 40)}${property.title.length > 40 ? '...' : ''}</td>
                    <td class="fw-bold text-success">$${Number(property.price).toLocaleString()}</td>
                    <td>${property.city}</td>
                    <td><span class="badge bg-primary">${property.category.name}</span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary me-2" onclick="editProperty(${property.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteProperty(${property.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;

            $('#propertiesTable').prepend(row);
        }

        function updatePropertyRow(property) {
            const row = $(`#property-${property.id}`);
            row.find('td:eq(2)').text(property.title.substring(0, 40) + (property.title.length > 40 ? '...' : ''));
            row.find('td:eq(3)').html(`<span class="fw-bold text-success">$${Number(property.price).toLocaleString()}</span>`);
            row.find('td:eq(4)').text(property.city);
            row.find('td:eq(5)').html(`<span class="badge bg-primary">${property.category.name}</span>`);
        }

        function resetPropertyForm() {
            currentPropertyId = null;
            $('#propertyForm')[0].reset();
            $('#propertyId').val('');
            $('#imagePreview').html('');
        }

        function showAlert(type, message) {
            const alert = `
                <div class="alert alert-${type} alert-dismissible fade show position-fixed top-0 end-0 m-3"
                     role="alert" style="z-index: 9999; min-width: 300px;">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;

            $('body').append(alert);

            setTimeout(() => {
                $('.alert').alert('close');
            }, 5000);
        }

        function downloadSampleCsv() {
            const csvContent = "title,description,price,city,category\n" +
                              "Luxury Villa,Beautiful 5-bedroom villa with pool,1500000.00,Los Angeles,Luxury Homes\n" +
                              "Downtown Apartment,Modern 2-bedroom apartment in city center,450000.00,New York,Apartments\n" +
                              "Beach House,Coastal property with ocean view,850000.00,Miami,Vacation Homes";

            const blob = new Blob([csvContent], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'sample_properties.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        }

        // Clear form when modal is hidden
        $('#propertyModal').on('hidden.bs.modal', function() {
            resetPropertyForm();
        });

        // Preview image before upload
        $('#image').change(function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').html(`
                        <img src="${e.target.result}"
                             alt="Preview"
                             class="img-thumbnail"
                             style="max-height: 150px;">
                    `);
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
