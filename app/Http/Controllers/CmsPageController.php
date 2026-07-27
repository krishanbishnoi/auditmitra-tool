<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\CmsPage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class CmsPageController extends Controller
{
    // Show list of CMS pages
    public function index()
    {
        $pages = CmsPage::orderBy('created_at', 'desc')->paginate(10);
        return view('cms.index', compact('pages'));
    }

    // Show create form
    public function create()
    {
        return view('cms.create');
    }

    // Store new page
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $request->description,
            'status' => 1,
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/cms_files'), $filename);
            $data['file_path'] = 'uploads/cms_files/' . $filename;
        }

        CmsPage::create($data);

        return redirect()->route('cms.index')->with('success', 'CMS page created successfully.');
    }

    // Show edit form
    public function edit($id)
    {
        $page = CmsPage::findOrFail($id);
        return view('cms.edit', compact('page'));
    }

    // Update existing page
    public function update(Request $request, $id)
    {
        $page = CmsPage::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $page->name = $request->name;
        $page->title = $request->title;
        $page->slug = Str::slug($request->title);
        $page->content = $request->description;

        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($page->file_path && File::exists(public_path($page->file_path))) {
                File::delete(public_path($page->file_path));
            }

            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/cms_files'), $filename);
            $page->file_path = 'uploads/cms_files/' . $filename;
        }

        $page->save();

        return redirect()->route('cms.index')->with('success', 'CMS page updated successfully.');
    }

    // Delete page
    public function destroy($id)
    {
        $page = CmsPage::findOrFail($id);

        // Delete file if exists
        if ($page->file_path && File::exists(public_path($page->file_path))) {
            File::delete(public_path($page->file_path));
        }

        $page->delete();

        return redirect()->route('cms.index')->with('success', 'CMS page deleted successfully.');
    }
}
