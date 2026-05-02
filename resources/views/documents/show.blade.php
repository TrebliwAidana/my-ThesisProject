@extends('layouts.app')

@section('title', $document->title . ' — VSULHS SSLG')
@section('page-title', 'Document Details')

@push('styles')
<style>
/* ════════════════════════════════════════════════
   DOCUMENT DETAILS — Emerald & Gold Luxury Theme
   Matching all management views
════════════════════════════════════════════════ */

/* ── Hero Section ── */
.doc-hero {
    position: relative;
    overflow: hidden;
    border-radius: 1.25rem;
    padding: 1.75rem 2rem;
    isolation: isolate;
    background: linear-gradient(135deg, #064E3B 0%, #065F46 35%, #047857 60%, #0A3A28 100%);
}
.doc-hero::before {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,1) 1px, transparent 0);
    background-size: 28px 28px;
    opacity: 0.04; z-index: 0;
}
.doc-hero::after {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 280px; height: 280px;
    background: radial-gradient(circle, rgba(212,175,55,0.35), transparent 65%);
    filter: blur(48px); z-index: 0;
}
.doc-hero-content { position: relative; z-index: 1; }

.doc-hero-title {
    font-family: 'DM Serif Display', serif;
    font-size: clamp(1.5rem, 3.5vw, 2.2rem);
    color: #fff;
    letter-spacing: -0.02em;
    line-height: 1.1;
}
.doc-hero-title span {
    background: linear-gradient(90deg, #F0CC55, #D4AF37);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.doc-hero-pill {
    display: inline-flex; align-items: center; gap: 0.4rem;
    padding: 0.3rem 0.75rem;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(212,175,55,0.28);
    border-radius: 999px;
    font-size: 0.72rem; font-weight: 600;
    color: rgba(255,255,255,0.88);
    font-family: 'DM Mono', monospace;
}

/* ── Document Card ── */
.doc-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
html.dark .doc-card {
    box-shadow: 0 4px 20px rgba(0,0,0,0.22);
}
.doc-card-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--border);
    background: var(--surface-2);
}
.doc-card-header h1 {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text);
    font-family: 'DM Serif Display', serif;
    margin-bottom: 0.25rem;
}
.doc-card-header p {
    font-size: 0.7rem;
    color: var(--text-3);
    font-family: 'DM Mono', monospace;
}
.doc-card-body {
    padding: 1.5rem;
}

/* ── Info Grid ── */
.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin: 1rem 0;
}
.info-item {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
    flex-wrap: wrap;
}
.info-label {
    font-size: 0.7rem;
    font-weight: 700;
    color: var(--text-3);
    font-family: 'DM Mono', monospace;
    background: var(--surface-2);
    padding: 0.2rem 0.6rem;
    border-radius: 0.5rem;
}
.info-value {
    font-size: 0.8rem;
    font-weight: 500;
    color: var(--text);
}

/* ── Description Box ── */
.description-box {
    background: var(--surface-2);
    border-radius: 0.75rem;
    padding: 1rem;
    margin-bottom: 1rem;
}
.description-label {
    font-size: 0.7rem;
    font-weight: 700;
    color: var(--text-3);
    margin-bottom: 0.5rem;
    font-family: 'DM Mono', monospace;
}
.description-text {
    font-size: 0.85rem;
    color: var(--text-2);
    line-height: 1.6;
}

/* ── Action Buttons ── */
.action-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border);
}
.btn-preview {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    font-size: 0.75rem;
    font-weight: 700;
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: #fff;
    border: none;
    border-radius: 0.65rem;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
}
.btn-preview:hover {
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: #0f172a;
    transform: translateY(-1px);
}
.btn-download {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    font-size: 0.75rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--emerald), var(--emerald-dark));
    color: #fff;
    border: none;
    border-radius: 0.65rem;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
}
.btn-download:hover {
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: #0f172a;
    transform: translateY(-1px);
}
.btn-edit {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    font-size: 0.75rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: #0f172a;
    border: none;
    border-radius: 0.65rem;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
}
.btn-edit:hover {
    background: linear-gradient(135deg, var(--emerald), var(--emerald-dark));
    color: #fff;
    transform: translateY(-1px);
}
.btn-delete {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    font-size: 0.75rem;
    font-weight: 700;
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: #fff;
    border: none;
    border-radius: 0.65rem;
    cursor: pointer;
    transition: all 0.2s ease;
}
.btn-delete:hover {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    transform: translateY(-1px);
}
.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    font-size: 0.75rem;
    font-weight: 600;
    background: transparent;
    color: var(--text-2);
    border: 1.5px solid var(--border);
    border-radius: 0.65rem;
    cursor: pointer;
    transition: all 0.18s ease;
    text-decoration: none;
}
.btn-back:hover {
    border-color: rgba(212,175,55,0.4);
    color: var(--gold-dark);
    background: rgba(212,175,55,0.06);
}

/* ── Version History Card ── */
.version-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
.version-card-header {
    padding: 0.875rem 1.25rem;
    border-bottom: 1px solid var(--border);
    background: linear-gradient(135deg, #064E3B 0%, #047857 60%, #065F46 100%);
}
.version-card-header h2 {
    font-size: 0.85rem;
    font-weight: 700;
    color: #fff;
    font-family: 'DM Mono', monospace;
    text-transform: uppercase;
    letter-spacing: 0.1em;
}

.version-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.8rem;
}
.version-table th {
    padding: 0.7rem 1rem;
    text-align: left;
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--text-3);
    font-family: 'DM Mono', monospace;
    background: var(--surface-2);
    border-bottom: 1px solid var(--border);
}
.version-table td {
    padding: 0.75rem 1rem;
    color: var(--text-2);
    vertical-align: middle;
    border-bottom: 1px solid var(--border);
}
.version-table tr:last-child td {
    border-bottom: none;
}
.version-table tr:hover {
    background: rgba(212,175,55,0.025);
}
.current-version-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.2rem 0.6rem;
    border-radius: 9999px;
    font-size: 0.6rem;
    font-weight: 700;
    background: rgba(5,150,105,0.1);
    color: #047857;
    border: 1px solid rgba(5,150,105,0.2);
}
html.dark .current-version-badge {
    background: rgba(16,185,129,0.15);
    color: #6ee7b7;
}
.version-download {
    color: var(--emerald);
    text-decoration: none;
    font-size: 0.7rem;
    font-weight: 600;
    transition: color 0.15s ease;
}
.version-download:hover {
    color: var(--gold-dark);
}

/* ── Animations ── */
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}
.anim-1 { animation: fadeUp 0.38s ease 0.04s both; }
.anim-2 { animation: fadeUp 0.38s ease 0.10s both; }
.anim-3 { animation: fadeUp 0.38s ease 0.16s both; }
</style>
@endpush

@section('content')

<div class="space-y-5">
    
    {{-- Hero Section --}}
    <div class="doc-hero anim-1">
        <div class="doc-hero-content">
            <p class="text-emerald-300/70 text-[10px] font-bold tracking-[0.2em] uppercase mb-2"
               style="font-family:'DM Mono',monospace;">
                {{ now()->format('F Y') }} · Document Management
            </p>
            <h1 class="doc-hero-title mb-3">Document<br><span>Details</span></h1>
            <div class="flex flex-wrap gap-2">
                <span class="doc-hero-pill">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    {{ $document->title }}
                </span>
            </div>
        </div>
    </div>

    {{-- Document Details Card --}}
    <div class="doc-card anim-2">
        <div class="doc-card-header">
            <h1>{{ $document->title }}</h1>
            <p>
                Uploaded by {{ $document->owner->full_name ?? 'Unknown' }} 
                on {{ $document->created_at->format('F d, Y') }}
            </p>
        </div>

        <div class="doc-card-body">
            {{-- Description --}}
            <div class="description-box">
                <div class="description-label">📄 Description</div>
                <div class="description-text">{{ $document->description ?: 'No description provided.' }}</div>
            </div>

            {{-- Info Grid --}}
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Category</span>
                    <span class="info-value">{{ $document->category->name ?? '—' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">File Size</span>
                    <span class="info-value">{{ $document->formatted_size }}</span>
                </div>
                @if($document->currentVersion)
                <div class="info-item">
                    <span class="info-label">MIME Type</span>
                    <span class="info-value">{{ $document->currentVersion->mime_type }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Version</span>
                    <span class="info-value">v{{ $document->currentVersion->version_number }}</span>
                </div>
                @endif
            </div>

            {{-- Action Buttons --}}
            <div class="action-buttons">
                @if($document->currentVersion)
                    @php
                        $ext = strtolower(pathinfo($document->currentVersion->file_name, PATHINFO_EXTENSION));
                    @endphp
                    @if(in_array($ext, ['pdf','jpg','jpeg','png','gif','webp']))
                        <a href="{{ route('documents.preview', $document) }}" 
                           target="_blank"
                           class="btn-preview">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Preview
                        </a>
                    @endif
                    <a href="{{ route('documents.download', $document) }}" 
                       class="btn-download">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download (v{{ $document->currentVersion->version_number }})
                    </a>
                @else
                    <span class="text-text-3 italic text-sm">No file attached to this document.</span>
                @endif

                @can('update', $document)
                    <a href="{{ route('documents.edit', $document) }}" class="btn-edit">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                @endcan

                @can('delete', $document)
                    <form method="POST" action="{{ route('documents.destroy', $document) }}" 
                          onsubmit="return confirm('⚠️ Are you sure you want to delete this document?\n\nThis will move it to the trash and it can be restored later.')" 
                          class="inline">
                        @csrf 
                        @method('DELETE')
                        <button type="submit" class="btn-delete">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete
                        </button>
                    </form>
                @endcan

                <a href="{{ route('documents.index') }}" class="btn-back">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Documents
                </a>
            </div>
        </div>
    </div>

    {{-- Version History Card --}}
    @if($document->versions->count() > 0)
    <div class="version-card anim-3">
        <div class="version-card-header">
            <h2>📜 Version History</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="version-table">
                <thead>
                    <tr>
                        <th>Version</th>
                        <th>File Name</th>
                        <th>Size</th>
                        <th>Uploaded By</th>
                        <th>Date</th>
                        <th>Change Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($document->versions as $version)
                    <tr>
                        <td class="font-medium">
                            v{{ $version->version_number }}
                            @if($document->currentVersion && $version->id === $document->currentVersion->id)
                                <span class="current-version-badge ml-1">Current</span>
                            @endif
                        </td>
                        <td class="max-w-[200px] truncate">{{ $version->file_name }}</td>
                        <td class="text-xs font-mono text-text-3">
                            @php
                                $bytes = $version->file_size;
                                $units = ['B', 'KB', 'MB', 'GB'];
                                $i = 0;
                                while ($bytes >= 1024 && $i < count($units) - 1) {
                                    $bytes /= 1024;
                                    $i++;
                                }
                            @endphp
                            {{ round($bytes, 2) . ' ' . $units[$i] }}
                        </td>
                        <td>{{ $version->uploader->full_name ?? 'Unknown' }}</td>
                        <td class="text-xs font-mono text-text-3 whitespace-nowrap">{{ $version->created_at->format('M d, Y H:i') }}</td>
                        <td class="max-w-[200px] truncate">{{ $version->change_notes ?: '—' }}</td>
                        <td>
                            <a href="{{ route('documents.version.download', [$document->id, $version->id]) }}" 
                               class="version-download">
                                Download
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

@endsection