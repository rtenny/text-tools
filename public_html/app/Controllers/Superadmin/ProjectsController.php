<?php

namespace App\Controllers\Superadmin;

use App\Controllers\BaseController;
use App\Models\ProjectModel;
use App\Libraries\EncryptionService;

class ProjectsController extends BaseController
{
    protected $projectModel;
    protected $encryptionService;

    public function __construct()
    {
        $this->projectModel = new ProjectModel();
        $this->encryptionService = new EncryptionService();
    }

    public function index()
    {
        $projects = $this->projectModel->orderBy('created_at', 'DESC')->findAll();

        $data = [
            'title' => 'Projects',
            'projects' => $projects,
        ];

        return view('superadmin/projects/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Create Project',
        ];

        return view('superadmin/projects/create', $data);
    }

    public function store()
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
            'default_ai_provider' => 'required|in_list[claude,openai]',
            'api_key' => 'required|min_length[10]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name = $this->request->getPost('name');
        $slug = url_title($name, '-', true);

        // Check if slug already exists
        if ($this->projectModel->where('slug', $slug)->first()) {
            return redirect()->back()->withInput()->with('error', 'A project with this name already exists.');
        }

        // Encrypt the API key
        $apiKey = $this->request->getPost('api_key');
        $encryptedApiKey = $this->encryptionService->encrypt($apiKey);

        $data = [
            'name' => $name,
            'slug' => $slug,
            'languages' => json_encode(['en', 'de', 'es']), // Fixed languages as per plan
            'default_ai_provider' => $this->request->getPost('default_ai_provider'),
            'api_key' => $encryptedApiKey,
            'is_active' => 1,
        ];

        if ($this->projectModel->insert($data)) {
            return redirect()->to('superadmin/projects')->with('success', 'Project created successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create project. Please try again.');
        }
    }

    public function edit($id)
    {
        $project = $this->projectModel->find($id);

        if (!$project) {
            return redirect()->to('superadmin/projects')->with('error', 'Project not found.');
        }

        // Get towns data
        $townService = new \App\Services\TownService();
        $allTowns = $townService->getAllAvailableTowns();
        $assignedTownIds = $townService->getTownIdsForProject($id);

        $data = [
            'title' => 'Edit Project',
            'project' => $project,
            'allTowns' => $allTowns,
            'assignedTownIds' => $assignedTownIds,
        ];

        return view('superadmin/projects/edit', $data);
    }

    public function update($id)
    {
        $project = $this->projectModel->find($id);

        if (!$project) {
            return redirect()->to('superadmin/projects')->with('error', 'Project not found.');
        }

        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
            'default_ai_provider' => 'required|in_list[claude,openai]',
            'is_active' => 'required|in_list[0,1]',
        ];

        // Only validate API key if it's provided (user wants to update it)
        $apiKeyProvided = !empty($this->request->getPost('api_key'));
        if ($apiKeyProvided) {
            $rules['api_key'] = 'required|min_length[10]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name = $this->request->getPost('name');
        $slug = url_title($name, '-', true);

        // Check if slug already exists (excluding current project)
        $existingProject = $this->projectModel->where('slug', $slug)->where('id !=', $id)->first();
        if ($existingProject) {
            return redirect()->back()->withInput()->with('error', 'A project with this name already exists.');
        }

        $data = [
            'name' => $name,
            'slug' => $slug,
            'default_ai_provider' => $this->request->getPost('default_ai_provider'),
            'is_active' => $this->request->getPost('is_active'),
        ];

        // Update API key if provided
        if ($apiKeyProvided) {
            $apiKey = $this->request->getPost('api_key');
            $data['api_key'] = $this->encryptionService->encrypt($apiKey);
        }

        if ($this->projectModel->update($id, $data)) {
            return redirect()->to('superadmin/projects')->with('success', 'Project updated successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update project. Please try again.');
        }
    }

    public function delete($id)
    {
        if (!$this->request->isAJAX() && $this->request->getMethod() !== 'post') {
            return redirect()->to('superadmin/projects')->with('error', 'Invalid request.');
        }

        $project = $this->projectModel->find($id);

        if (!$project) {
            return redirect()->to('superadmin/projects')->with('error', 'Project not found.');
        }

        // Delete will cascade to users due to foreign key
        if ($this->projectModel->delete($id)) {
            return redirect()->to('superadmin/projects')->with('success', 'Project deleted successfully.');
        } else {
            return redirect()->to('superadmin/projects')->with('error', 'Failed to delete project. Please try again.');
        }
    }

    /**
     * Assign towns to a project
     */
    public function assignTowns($id)
    {
        if ($this->request->getMethod() !== 'post') {
            return redirect()->to('superadmin/projects/edit/' . $id)->with('error', 'Invalid request.');
        }

        $project = $this->projectModel->find($id);

        if (!$project) {
            return redirect()->to('superadmin/projects')->with('error', 'Project not found.');
        }

        $townIds = $this->request->getPost('town_ids');

        // Ensure $townIds is an array (it will be null if nothing is selected)
        if (!is_array($townIds)) {
            $townIds = [];
        }

        $townService = new \App\Services\TownService();
        $success = $townService->assignTownsToProject($id, $townIds);

        if ($success) {
            $count = count($townIds);
            $message = $count > 0
                ? "Successfully assigned {$count} town(s) to this project."
                : "Successfully removed all towns from this project.";
            return redirect()->to('superadmin/projects/edit/' . $id)->with('success', $message);
        } else {
            return redirect()->to('superadmin/projects/edit/' . $id)->with('error', 'Failed to assign towns. Please try again.');
        }
    }
}
