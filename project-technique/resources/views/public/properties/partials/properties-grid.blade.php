<div class="row g-4">
    @foreach($properties as $property)
        <div class="col-md-4 col-lg-3">
            <div class="card property-card h-100 shadow-sm border-0">
                @if($property->image_path)
                    <div class="position-relative">
                        <img src="{{ asset('storage/' . $property->image_path) }}"
                             class="card-img-top"
                             alt="{{ $property->title }}"
                             style="height: 200px; object-fit: cover;">
                        <div class="price-tag">${{ number_format($property->price) }}</div>
                    </div>
                @endif
                <div class="card-body">
                    <h5 class="card-title fw-bold">{{ Str::limit($property->title, 50) }}</h5>
                    <p class="card-text text-muted small">{{ Str::limit($property->description, 100) }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-primary">{{ $property->city }}</span>
                        <span class="badge bg-secondary">{{ $property->category->name }}</span>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0">
                    <a href="{{ route('public.properties.show', $property) }}"
                       class="btn btn-outline-primary btn-sm w-100">
                        View Details
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>

@if($properties->hasMorePages())
    <div class="row mt-4">
        <div class="col text-center">
            <button class="btn btn-outline-primary" onclick="loadMoreProperties()">
                Load More Properties
            </button>
        </div>
    </div>
@endif
