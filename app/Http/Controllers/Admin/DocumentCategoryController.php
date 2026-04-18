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
        return view('admin.document-categories.index', compact('categories'));
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
        $documentCategory->delete();

        return redirect()->route('admin.document-categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}