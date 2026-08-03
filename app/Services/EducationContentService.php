<?php

namespace App\Services;

use App\Models\EducationContent;
use Illuminate\Support\Str;

class EducationContentService
{
    // ponytail: create content
    public function store(array $data): EducationContent
    {
        $data['slug'] = Str::slug($data['title']);
        $data['admin_id'] = auth()->id();
        $data['status'] = $data['status'] ?? 'published';

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        if (request()->hasFile('thumbnail')) {
            $data['thumbnail_url'] = request()->file('thumbnail')->store('education', 'public');
        }

        return EducationContent::create($data);
    }

    // ponytail: update content
    public function update(EducationContent $content, array $data): EducationContent
    {
        if (isset($data['title'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        if (isset($data['status']) && $data['status'] === 'published' && empty($content->published_at) && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        if (request()->hasFile('thumbnail')) {
            $data['thumbnail_url'] = request()->file('thumbnail')->store('education', 'public');
        }

        $content->update($data);
        return $content;
    }

    // ponytail: delete content
    public function destroy(EducationContent $content): bool
    {
        return $content->delete();
    }

    // ponytail: publish content
    public function publish(EducationContent $content): EducationContent
    {
        $content->update([
            'status' => 'published',
            'published_at' => now(),
        ]);
        return $content;
    }

    // ponytail: archive content
    public function archive(EducationContent $content): EducationContent
    {
        $content->update([
            'status' => 'archived',
        ]);
        return $content;
    }
}
