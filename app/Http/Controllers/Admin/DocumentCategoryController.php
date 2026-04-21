<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentCategory;
use Illuminate\Http\Request;

class DocumentCategoryController extends Controller
{
    public function index()
    {
    
    $categories = DocumentCategory::orderBy('name')->paginate(15);
    
    return response()
        ->view('admin.document-categories.index', compact('categories'))
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->header('Pragma', 'no-cache');

    }

    public function create()
    {
        return view('admin.document-categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:document_categories,name',
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
        ]);

        DocumentCategory::create([
            'name'        => $request->name,
            'description' => $request->description,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.document-categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function show(DocumentCategory $documentCategory)
    {
        return view('admin.document-categories.show', compact('documentCategory'));
    }

    public function edit(DocumentCategory $documentCategory)
    {
        return view('admin.document-categories.edit', compact('documentCategory'));
    }

    public function update(Request $request, DocumentCategory $documentCategory)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:document_categories,name,' . $documentCategory->id,
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
        ]);

        $documentCategory->update([
            'name'        => $request->name,
            'description' => $request->description,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.document-categories.index')
            ->with('success', 'Category updated successfully.');
    }
    public function destroy(DocumentCategory $documentCategory)
    {
    
        // Check if any documents are using this category
        if ($documentCategory->documents()->exists()) {
            $count = $documentCategory->documents()->count();
            return redirect()->route('admin.document-categories.index')
                ->with('error', "Cannot delete category '{$documentCategory->name}' because it is used by {$count} document(s). Please reassign or delete those documents first.");
        }

        $documentCategory->delete();

        return redirect()->route('admin.document-categories.index')
            ->with('success', 'Category deleted successfully.');
    }

}