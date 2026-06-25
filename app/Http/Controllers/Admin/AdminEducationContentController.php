<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EducationContent;
use App\Http\Requests\StoreEducationContentRequest;
use App\Http\Requests\UpdateEducationContentRequest;
use App\Services\EducationContentService;
use App\Exceptions\RecyclinkException;
use Illuminate\Routing\Controllers\HasMiddleware;

class AdminEducationContentController extends Controller implements HasMiddleware
{
    protected EducationContentService $educationService;

    public function __construct(EducationContentService $educationService)
    {
        $this->educationService = $educationService;
    }

    public static function middleware(): array
    {
        return [
            'auth',
            'verified',
            'role:admin',
        ];
    }

    // ponytail: list education posts
    public function index()
    {
        $contents = EducationContent::latest()->paginate(15);
        return view('admin.education.index', compact('contents'));
    }

    // ponytail: display creation form
    public function create()
    {
        return view('admin.education.create');
    }

    public function store(StoreEducationContentRequest $request)
    {
        $this->authorize('create', EducationContent::class);
        try {
            $this->educationService->store($request->validated());
            return redirect()->route('admin.education.index')->with('success', 'Article created as draft.');
        } catch (RecyclinkException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // ponytail: display edit form
    public function edit(EducationContent $educationContent)
    {
        return view('admin.education.edit', ['content' => $educationContent]);
    }

    public function update(UpdateEducationContentRequest $request, EducationContent $educationContent)
    {
        $this->authorize('update', $educationContent);
        try {
            $this->educationService->update($educationContent, $request->validated());
            return redirect()->route('admin.education.index')->with('success', 'Article updated successfully.');
        } catch (RecyclinkException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(EducationContent $educationContent)
    {
        $this->authorize('delete', $educationContent);
        try {
            $this->educationService->destroy($educationContent);
            return redirect()->route('admin.education.index')->with('success', 'Article deleted successfully.');
        } catch (RecyclinkException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function publish(EducationContent $educationContent)
    {
        $this->authorize('publish', $educationContent);
        try {
            $this->educationService->publish($educationContent);
            return redirect()->back()->with('success', 'Article published successfully.');
        } catch (RecyclinkException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function archive(EducationContent $educationContent)
    {
        $this->authorize('archive', $educationContent);
        try {
            $this->educationService->archive($educationContent);
            return redirect()->back()->with('success', 'Article archived successfully.');
        } catch (RecyclinkException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
