<?php

namespace App\Http\Controllers;

use App\FileUpload;
use Illuminate\Http\Request;

class FileController extends Controller
{
    protected $uploadPath = '/files/';

    public function Index(Request $request)
    {
        $files = FileUpload::all();
        return view('file.index', compact('files'));
    }

    public function Save(Request $request)
    {
        $this->validate($request, [
            'file' => 'max:50000|mimes:pdf,doc,docx,xlsx,pptx,xls,xlsx',
        ]);

        $file = $request->file('file');
        $fileExt = strtolower($file->getClientOriginalExtension());
        $imgOriginalName = $file->getClientOriginalName();
        $img_filename = md5(uniqid('upload_file', true)) . '_uploaded.' . $fileExt;
        $location = public_path($this->uploadPath);
        $file->move($location, $img_filename);

        $fileUpload = new FileUpload();
        $fileUpload->name = $imgOriginalName;
        $fileUpload->url = $img_filename;
        $fileUpload->save();

        return redirect('/file')->with('success', 'Saved successfully!');
    }

    public function Edit(Request $request, $id)
    {
        $file = FileUpload::findOrFail($id);
        return view('file.edit', compact('file'));

    }

    public function Update(Request $request, $id)
    {
        $this->validate($request, [
            'file' => 'max:50000|mimes:pdf,doc,docx,xlsx,pptx,xls,xlsx',
        ]);
        $fileModel = FileUpload::findOrFail($id);
        if (!isset($fileModel)) {
            return back();
        }
        $file = $request->file('file');
        $path = public_path($this->uploadPath);
        $fileExt = strtolower($file->getClientOriginalExtension());
        $imgOriginalName = $file->getClientOriginalName();
        $file_name = md5(uniqid('upload_file', true)) . '_uploaded.' . $fileExt;
        try {
            unlink(public_path($this->uploadPath) . $fileModel->url);
        } catch (\Exception $e) {
        }
        $file->move($path, $file_name);
        $fileModel->name = $imgOriginalName;
        $fileModel->url = $file_name;
        $fileModel->save();
        return redirect('/file')->with('success', 'Saved successfully!');
    }

    public function Delete(Request $request, $id)
    {
        $fileModel = FileUpload::findOrFail($id);
        try {
            unlink(public_path($this->uploadPath) . $fileModel->url);

        } catch (\Exception $e) {
        }
        $fileModel->delete();
        return back();
    }
}
