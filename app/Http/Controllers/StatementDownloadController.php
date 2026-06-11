<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StatementDownloadController extends Controller
{
    public function __invoke(Request $request, Media $media): BinaryFileResponse
    {
        $partner = $request->user()?->partner;

        // IDOR guard: the document must belong to MY partner record.
        abort_unless(
            $partner
            && $media->collection_name === 'statements'
            && $media->model_type === Partner::class
            && (int) $media->model_id === $partner->id,
            403,
        );

        return response()->download($media->getPath(), $media->file_name);
    }
}
