<?php

namespace App\Http\Controllers\Admin;

use App\Models\Package;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::orderBy('amount', 'asc')->get();
        return view('admin.pages.packages.index', compact('packages'));
    }

    public function edit(Package $package)
    {
        return view('admin.pages.packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|integer|min:0',
            'coins' => 'required|integer|min:0',
            'bonus_coins' => 'required|integer|min:0',
            'expiry' => 'required|integer|min:1',
        ]);

        $package->update($request->all());

        return redirect()->route('admin.packages.index')
            ->with('success', 'Gói xu đã được cập nhật thành công.');
    }
}
