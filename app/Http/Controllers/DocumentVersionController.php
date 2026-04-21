<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentVersion;
use Illuminate\Support\Facades\Storage;

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

        // Check if the file actually exists on disk
        if (!Storage::disk('private')->exists($version->file_path)) {
            return redirect()->route('documents.show', $document)
                ->with('error', 'The file is missing from the server. Please contact the administrator.');
        }

        return Storage::disk('private')->download($version->file_path, $version->file_name);
    }
}