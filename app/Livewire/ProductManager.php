<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Kelola Produk & Kategori - Kafe POS')]
class ProductManager extends Component
{
    use WithPagination;

    // Filters
    public string $search = '';
    public string $selectedCategory = 'all';

    // Form fields
    public bool $isFormOpen = false;
    public ?int $productId = null;
    public string $name = '';
    public string $sku = '';
    public ?int $category_id = null;
    public string $price = '';
    public string $cost_price = '';
    public string $image = '';
    public bool $is_available = true;

    // Category Modal
    public bool $isCategoryModalOpen = false;
    public string $newCategoryName = '';

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:150',
            'sku' => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9\-_]+$/', 'unique:products,sku,' . $this->productId],
            'category_id' => 'required|integer|exists:categories,id',
            'price' => 'required|numeric|min:0|max:999999999',
            'cost_price' => 'nullable|numeric|min:0|max:999999999',
            'image' => 'nullable|string|max:500|url:http,https',
            'is_available' => 'boolean',
        ];
    }

    protected function authorizeAdmin(): void
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Aksi ini hanya diperbolehkan untuk Administrator.');
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedCategory(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->authorizeAdmin();
        $this->resetValidation();
        $this->productId = null;
        $this->name = '';
        $this->category_id = Category::first()?->id;
        $this->sku = 'PRD-' . strtoupper(Str::random(5));
        $this->price = '';
        $this->cost_price = '';
        $this->image = '';
        $this->is_available = true;
        $this->isFormOpen = true;
    }

    public function editProduct(int $id): void
    {
        $this->authorizeAdmin();
        $this->resetValidation();
        $product = Product::findOrFail($id);
        $this->productId = $product->id;
        $this->name = $product->name;
        $this->sku = $product->sku;
        $this->category_id = $product->category_id;
        $this->price = (string) $product->price;
        $this->cost_price = (string) $product->cost_price;
        $this->image = $product->image ?? '';
        $this->is_available = (bool) $product->is_available;
        $this->isFormOpen = true;
    }

    protected function sanitizeInput(?string $value): string
    {
        if ($value === null) {
            return '';
        }
        $clean = preg_replace('/<(script|style)\b[^>]*>(.*?)<\/\1>/is', '', $value);
        return trim(strip_tags($clean));
    }

    public function saveProduct(): void
    {
        $this->authorizeAdmin();
        $this->name = $this->sanitizeInput($this->name);
        $this->sku = $this->sanitizeInput($this->sku);
        if ($this->image) {
            $this->image = trim(strip_tags($this->image));
        }

        $validated = $this->validate();

        if ($this->productId) {
            $product = Product::findOrFail($this->productId);
            $product->update($validated);
            session()->flash('message', 'Produk "' . htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') . '" berhasil diperbarui.');
        } else {
            Product::create($validated);
            session()->flash('message', 'Produk baru berhasil ditambahkan.');
        }

        $this->isFormOpen = false;
    }

    public function toggleAvailability(int $id): void
    {
        $this->authorizeAdmin();
        $product = Product::findOrFail($id);
        $product->is_available = !$product->is_available;
        $product->save();
    }

    public function deleteProduct(int $id): void
    {
        $this->authorizeAdmin();
        $product = Product::findOrFail($id);
        $name = $product->name;
        $product->delete();
        session()->flash('message', 'Produk "' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '" berhasil dihapus.');
    }

    public function openCategoryModal(): void
    {
        $this->authorizeAdmin();
        $this->newCategoryName = '';
        $this->isCategoryModalOpen = true;
    }

    public function addCategory(): void
    {
        $this->authorizeAdmin();
        $this->newCategoryName = $this->sanitizeInput($this->newCategoryName);

        $this->validate([
            'newCategoryName' => 'required|string|max:50|unique:categories,name',
        ]);

        Category::create([
            'name' => $this->newCategoryName,
            'slug' => Str::slug($this->newCategoryName),
        ]);

        $this->newCategoryName = '';
        session()->flash('message', 'Kategori baru berhasil ditambahkan.');
    }

    public function deleteCategory(int $id): void
    {
        $this->authorizeAdmin();
        $category = Category::findOrFail($id);
        $category->delete();
        session()->flash('message', 'Kategori berhasil dihapus.');
    }

    public function render()
    {
        $categories = Category::withCount('products')->get();

        $query = Product::with('category');

        if ($this->selectedCategory !== 'all') {
            $query->whereHas('category', function ($q) {
                $q->where('slug', $this->selectedCategory);
            });
        }

        if (!empty($this->search)) {
            $escapedSearch = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], trim($this->search));
            $query->where(function ($q) use ($escapedSearch) {
                $q->where('name', 'like', '%' . $escapedSearch . '%')
                  ->orWhere('sku', 'like', '%' . $escapedSearch . '%');
            });
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(8);

        return view('livewire.product-manager', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
