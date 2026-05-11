<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentVersion;

class DocumentVersionController extends Controller
{
    public function download(Document $document, DocumentVersion $version)
    {
        // Ensure version belongs to document
        if ($version->document_id !== $document->id) {
            abort(404);
        }

        // Authorize using DocumentPolicy
        $this->authorize('view', $document);

        // ✅ Check if Cloudinary URL exists
        if (! $version->file_path) {
            return redirect()->route('documents.show', $document)
                ->with('error', 'The file is missing. Please contact the administrator.');
        }

        // ✅ Redirect to public Cloudinary URL
        return redirect($version->file_path);
    }
}