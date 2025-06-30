<div class="table-responsive mt-3">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Category</th>
                <th>Code</th>
                <th>Type</th>
                <th>Parent</th>
                <th>Sort Order</th>
                <th>Usage Count</th>
                <th>Total Amount</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            @if($category->parent_id)
                                <i class="bi bi-arrow-return-right text-muted me-2"></i>
                            @else
                                @if($category->type === 'income')
                                    <i class="bi bi-arrow-down-circle text-success me-2"></i>
                                @else
                                    <i class="bi bi-arrow-up-circle text-danger me-2"></i>
                                @endif
                            @endif
                            <div>
                                <strong>{{ $category->name }}</strong>
                                @if($category->description)
                                    <br><small class="text-muted">{{ Str::limit($category->description, 40) }}</small>
                                @endif
                                @if($category->children->count() > 0)
                                    <br><small class="text-info">{{ $category->children->count() }} subcategories</small>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <code class="bg-light p-1 rounded">{{ $category->code }}</code>
                    </td>
                    <td>
                        @if($category->type === 'income')
                            <span class="badge bg-success">
                                <i class="bi bi-arrow-down-circle"></i> Income
                            </span>
                        @else
                            <span class="badge bg-danger">
                                <i class="bi bi-arrow-up-circle"></i> Expense
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($category->parent)
                            <span class="badge bg-secondary">{{ $category->parent->name }}</span>
                        @else
                            <span class="text-muted">Root Category</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-light text-dark">{{ $category->sort_order }}</span>
                    </td>
                    <td>
                        <span class="badge bg-info">{{ $analytics['usage_counts'][$category->id] ?? 0 }} uses</span>
                    </td>
                    <td>
                        @if(($analytics['total_amounts'][$category->id] ?? 0) > 0)
                            <strong class="text-{{ $category->type === 'income' ? 'success' : 'danger' }}">
                                Rs. {{ number_format($analytics['total_amounts'][$category->id], 2) }}
                            </strong>
                        @else
                            <span class="text-muted">Rs. 0.00</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <a href="{{ route('payment-categories.show', $category) }}" 
                               class="btn btn-sm btn-outline-primary" title="View Details">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('payment-categories.edit', $category) }}" 
                               class="btn btn-sm btn-outline-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            
                            @if(($analytics['usage_counts'][$category->id] ?? 0) === 0 && $category->children->count() === 0)
                                <form action="{{ route('payment-categories.destroy', $category) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                            onclick="return confirm('Are you sure you want to delete this category?')" 
                                            title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endif

                            @if(!$category->parent_id)
                                <a href="{{ route('payment-categories.create', ['parent_id' => $category->id]) }}" 
                                   class="btn btn-sm btn-outline-success" title="Add Subcategory">
                                    <i class="bi bi-plus"></i>
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>

                <!-- Display subcategories if any -->
                @if($category->children->count() > 0)
                    @foreach($category->children as $child)
                        <tr class="bg-light">
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-arrow-return-right text-muted me-2 ms-3"></i>
                                    <div>
                                        <strong>{{ $child->name }}</strong>
                                        @if($child->description)
                                            <br><small class="text-muted">{{ Str::limit($child->description, 40) }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <code class="bg-white p-1 rounded">{{ $child->code }}</code>
                            </td>
                            <td>
                                @if($child->type === 'income')
                                    <span class="badge bg-success-subtle text-success">Income</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">Expense</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $child->parent->name }}</span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $child->sort_order }}</span>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $analytics['usage_counts'][$child->id] ?? 0 }} uses</span>
                            </td>
                            <td>
                                @if(($analytics['total_amounts'][$child->id] ?? 0) > 0)
                                    <strong class="text-{{ $child->type === 'income' ? 'success' : 'danger' }}">
                                        Rs. {{ number_format($analytics['total_amounts'][$child->id], 2) }}
                                    </strong>
                                @else
                                    <span class="text-muted">Rs. 0.00</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('payment-categories.show', $child) }}" 
                                       class="btn btn-sm btn-outline-primary" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('payment-categories.edit', $child) }}" 
                                       class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    
                                    @if(($analytics['usage_counts'][$child->id] ?? 0) === 0)
                                        <form action="{{ route('payment-categories.destroy', $child) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this subcategory?')" 
                                                    title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @endif
            @empty
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <div class="text-muted">
                            <i class="bi bi-tags fs-1 d-block mb-2"></i>
                            <h5>No categories found</h5>
                            <p>Add your first payment category to organize transactions.</p>
                            <a href="{{ route('payment-categories.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Add Category
                            </a>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($categories->count() > 0)
    <div class="mt-3">
        <small class="text-muted">
            <i class="bi bi-info-circle"></i> 
            Categories with active transactions cannot be deleted. Subcategories are shown indented under their parent categories.
        </small>
    </div>
@endif 