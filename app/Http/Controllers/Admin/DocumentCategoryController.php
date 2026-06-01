<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentCategory;
use Illuminate\Http\Request;

class DocumentCategoryController extends Controller
{
    public function index()
    {
        $this->requirePermission('categories.view');

        $categories = DocumentCategory::orderBy('name')->paginate(15);

        return response()
            ->view('admin.document-categories.index', compact('categories'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    public function create()
    {
        $this->requirePermission('categories.create');

        return view('admin.document-categories.create');
    }

    public function store(Request $request)
    {
        $this->requirePermission('categories.create');

        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:document_categories,name',
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
        ]);

        DocumentCategory::create([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.document-categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(DocumentCategory $documentCategory)
    {
        $this->requirePermission('categories.edit');

        return view('admin.document-categories.edit', compact('documentCategory'));
    }

    public function update(Request $request, DocumentCategory $documentCategory)
    {
        $this->requirePermission('categories.edit');

        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:document_categories,name,' . $documentCategory->getKey(),
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
        ]);

        $documentCategory->update([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.document-categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(DocumentCategory $documentCategory)
    {
        $this->requirePermission('categories.delete');

        // ✅ Exclude soft-deleted documents from the count
        if ($documentCategory->documents()->withoutTrashed()->exists()) {
            $count = $documentCategory->documents()->withoutTrashed()->count();
            $categoryName = $documentCategory->getAttribute('name');
            return redirect()->route('admin.document-categories.index')
                ->with('error', "Cannot delete category '{$categoryName}' because it is used by {$count} active document(s). Please reassign or delete those documents first.");
        }

        $documentCategory->delete();

        return redirect()->route('admin.document-categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}