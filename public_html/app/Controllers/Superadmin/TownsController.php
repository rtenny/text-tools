<?php

namespace App\Controllers\Superadmin;

use App\Controllers\BaseController;
use App\Services\TownService;

class TownsController extends BaseController
{
    protected $townService;

    public function __construct()
    {
        $this->townService = new TownService();
    }

    /**
     * Display list of all towns
     */
    public function index()
    {
        $towns = $this->townService->getAllAvailableTowns();

        // Get project count for each town
        foreach ($towns as &$town) {
            $town['project_count'] = $this->townService->getProjectCountForTown($town['id']);
        }

        $data = [
            'title' => 'Manage Towns',
            'towns' => $towns,
        ];

        return view('superadmin/towns/index', $data);
    }

    /**
     * Display create town form
     */
    public function create()
    {
        $data = [
            'title' => 'Create Town',
        ];

        return view('superadmin/towns/create', $data);
    }

    /**
     * Store new town
     */
    public function store()
    {
        $rules = [
            'name' => 'required|min_length[2]|max_length[255]|is_unique[towns.name]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $townName = $this->request->getPost('name');
        $townId = $this->townService->createTown($townName);

        if ($townId) {
            return redirect()->to('/superadmin/towns')->with('success', 'Town created successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create town. Please try again.');
        }
    }

    /**
     * Display edit town form
     */
    public function edit($townId)
    {
        $townModel = new \App\Models\TownModel();
        $town = $townModel->find($townId);

        if (!$town) {
            return redirect()->to('/superadmin/towns')->with('error', 'Town not found.');
        }

        $projectCount = $this->townService->getProjectCountForTown($townId);

        $data = [
            'title' => 'Edit Town',
            'town' => $town,
            'project_count' => $projectCount,
        ];

        return view('superadmin/towns/edit', $data);
    }

    /**
     * Update town
     */
    public function update($townId)
    {
        $rules = [
            'name' => "required|min_length[2]|max_length[255]|is_unique[towns.name,id,{$townId}]",
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $townName = $this->request->getPost('name');
        $success = $this->townService->updateTown($townId, $townName);

        if ($success) {
            return redirect()->to('/superadmin/towns')->with('success', 'Town updated successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update town. Please try again.');
        }
    }

    /**
     * Delete town
     */
    public function delete($townId)
    {
        // Check if town is in use
        $projectCount = $this->townService->getProjectCountForTown($townId);

        if ($projectCount > 0) {
            return redirect()->to('/superadmin/towns')->with('error', "Cannot delete town. It is assigned to {$projectCount} project(s).");
        }

        $success = $this->townService->deleteTown($townId);

        if ($success) {
            return redirect()->to('/superadmin/towns')->with('success', 'Town deleted successfully.');
        } else {
            return redirect()->to('/superadmin/towns')->with('error', 'Failed to delete town. Please try again.');
        }
    }
}
